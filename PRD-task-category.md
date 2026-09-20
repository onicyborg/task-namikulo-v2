# PRD — Task Category, Metopen & Artikel Ilmiah (Namikulo)

| | |
|---|---|
| **Versi** | 1.0 (Draft siap implementasi) |
| **Tanggal** | 20 September 2026 |
| **Aplikasi** | Namikulo — Task Management System (Laravel) |
| **Tujuan dokumen** | Pedoman agent/developer untuk mengimplementasikan update dari database sampai view |

---

## 1. Ringkasan

Saat ini semua task diperlakukan sama (satu form, satu halaman list, satu halaman detail). Update ini menambahkan **kategori task** sebagai master data baru. Kategori awalnya ada 3: **Parafrase**, **Artikel Ilmiah**, dan **Metopen (Metodologi Penelitian)**.

Dampaknya:

1. Ada master data baru **Kategori Task**.
2. Form add/update task punya field baru **Kategori**. Untuk Artikel Ilmiah dan Metopen, muncul field tambahan (Prodi, Judul, Keterangan, checklist Tugas 1–4, dan khusus Artikel Ilmiah ada toggle "Lanjutan dari Metopen").
3. Menu **Task** di sidebar menjadi expandable: **General**, **Metopen**, **Artikel Ilmiah**.
4. Halaman detail task menampilkan card tambahan untuk kategori Metopen/Artikel Ilmiah, termasuk card checklist yang diisi oleh worker.

## 2. Tujuan & Non-Goals

**Tujuan**
- Task bisa dikategorikan dan dipisah tampilannya per kategori.
- Data pendukung Metopen/Artikel Ilmiah tersimpan terstruktur.
- Flow untuk kategori **Parafrase tidak berubah sama sekali** (kecuali tambahan field Kategori).
- Seluruh UI baru mengikuti style existing.

**Non-Goals (tidak dikerjakan sekarang)**
- Perubahan flow pembayaran, margin, atau perhitungan harga.
- Mengisi `task.user_id` yang saat ini belum terisi pada flow add task.
- Notifikasi, riwayat perubahan checklist, atau upload file khusus per tugas.
- Export Excel untuk halaman Metopen/Artikel Ilmiah (opsional, lihat bagian 11).

## 3. Panduan untuk Agent (baca dulu)

1. **Pelajari code existing sebelum menulis apa pun**: `routes/web.php`, `TaskController`, controller/view **Client** (referensi master data), model `Task`, view `task/*`, dan partial layout sidebar. Tiru pola penamaan, struktur folder, cara validasi, cara render tabel, dan cara notifikasi yang sudah ada.
2. **Jangan menambah library baru** kecuali terpaksa. Gunakan komponen yang sudah dipakai (kemungkinan Select2, tabel dengan pagination/search, modal, ikon yang sama).
3. **Logika kategori berdasarkan kolom `tipe`, bukan `id` atau `nama`**, supaya nama kategori boleh diubah tanpa merusak fitur.
4. **Cek tipe kolom sebelum membuat foreign key.** Di ERD, `task.id` bertipe `int` (bukan bigint). Jika migration pakai `increments()`, gunakan `unsignedInteger('task_id')` (bukan `foreignId()` yang menghasilkan bigint) agar FK tidak error.
5. Semua tabel baru mengikuti konvensi existing: nama tabel singular, `timestamps`, dan `softDeletes` (`deleted_at`).
6. **Data existing harus aman.** Semua task lama otomatis menjadi kategori Parafrase (lihat 5.4). Jangan ada migration yang menghapus atau mengubah data lama.
7. Jika ada hal yang tidak cocok dengan asumsi di dokumen ini, ikuti kondisi code aktual dan catat penyesuaiannya.

## 4. Aturan Bisnis Kategori

| Kategori | `tipe` | Field tambahan | Toggle lanjutan |
|---|---|---|---|
| Parafrase | `general` | — | — |
| Metopen | `metopen` | Prodi, Judul, Keterangan, Tugas 1–4 | — |
| Artikel Ilmiah | `artikel_ilmiah` | Prodi, Judul, Keterangan, Tugas 1–4 | Lanjutan dari Metopen (ya/tidak) |

Aturan:

- Kategori **wajib** dipilih saat add/update task.
- **Prodi** dan **Judul** wajib untuk `metopen` dan `artikel_ilmiah`. **Keterangan** opsional. Tugas 1–4 default belum tercentang.
- Label field Judul dinamis: "Judul Metopen" atau "Judul Artikel Ilmiah".
- Toggle "Lanjutan dari Metopen" hanya muncul dan tersimpan untuk `artikel_ilmiah`. Untuk `metopen` nilainya selalu `false`.
- **Saat kategori task diedit:**
  - ke `general` (Parafrase): data di `task_academic` di-soft-delete (disembunyikan, tidak dihapus permanen).
  - ke `metopen`/`artikel_ilmiah`: data `task_academic` dibuat/di-restore (`updateOrCreate`). Jika menjadi `metopen`, `is_lanjutan_metopen` di-reset ke `false`.
- **Saat task di-soft-delete**, data `task_academic` miliknya ikut di-soft-delete.
- Kategori seed (`is_system = true`) **tidak bisa dihapus** dan `tipe`-nya tidak bisa diubah; namanya boleh diedit. Kategori yang sedang dipakai task juga tidak bisa dihapus.
- Kategori baru yang dibuat lewat master data otomatis bertipe `general` (perilaku sama seperti Parafrase).

## 5. Perubahan Database

### 5.1 Tabel baru `task_category`

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | int, PK, increment | |
| nama | varchar(255) | Contoh: Parafrase |
| tipe | varchar(50), default `general` | `general` / `metopen` / `artikel_ilmiah` |
| is_system | boolean, default false | `true` untuk 3 kategori seed |
| created_at, updated_at | datetime | |
| deleted_at | timestamp, nullable | |

Seed awal (idempotent, gunakan `updateOrCreate` berdasarkan `tipe` untuk `is_system = true`):

| nama | tipe | is_system |
|---|---|---|
| Parafrase | general | true |
| Artikel Ilmiah | artikel_ilmiah | true |
| Metopen | metopen | true |

### 5.2 Ubah tabel `task`

| Kolom baru | Tipe | Keterangan |
|---|---|---|
| category_id | int (unsigned), nullable → not null setelah backfill | FK ke `task_category.id`, tambahkan index |

### 5.3 Tabel baru `task_academic` (relasi 1:1 dengan task)

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | int, PK, increment | |
| task_id | int (unsigned), **unique**, FK → `task.id` | Tipe harus sama dengan `task.id` |
| prodi | varchar(255) | Program studi |
| judul | text | Judul Metopen / Artikel Ilmiah |
| keterangan | text, nullable | |
| is_lanjutan_metopen | boolean, default false | Hanya bermakna untuk Artikel Ilmiah |
| tugas_1 | boolean, default false | Checklist |
| tugas_2 | boolean, default false | Checklist |
| tugas_3 | boolean, default false | Checklist |
| tugas_4 | boolean, default false | Checklist |
| created_at, updated_at | datetime | |
| deleted_at | timestamp, nullable | |

### 5.4 Urutan migration & data existing

1. Migration: buat `task_category`.
2. Seed 3 kategori (di migration atau seeder yang dijalankan saat deploy, harus idempotent).
3. Migration: tambah `task.category_id` **nullable** + index.
4. Backfill: `UPDATE task SET category_id = <id Parafrase> WHERE category_id IS NULL`.
5. Migration: ubah `category_id` menjadi NOT NULL dan pasang FK.
6. Migration: buat `task_academic`.

Semua migration harus punya `down()` yang benar.

### 5.5 Tambahan DBML (untuk diperbarui di ERD)

```dbml
Table task_category {
  id int [pk, increment]
  nama varchar(255)
  tipe varchar(50) [default: 'general', note: 'general | metopen | artikel_ilmiah']
  is_system boolean [default: false, note: 'true = kategori seed, tidak bisa dihapus']
  created_at datetime
  updated_at datetime
  deleted_at timestamp
}

Table task_academic {
  id int [pk, increment]
  task_id int [not null, unique]
  prodi varchar(255)
  judul text
  keterangan text
  is_lanjutan_metopen boolean [default: false, note: 'hanya untuk Artikel Ilmiah']
  tugas_1 boolean [default: false]
  tugas_2 boolean [default: false]
  tugas_3 boolean [default: false]
  tugas_4 boolean [default: false]
  created_at datetime
  updated_at datetime
  deleted_at timestamp
}

// Tambahkan juga pada Table task:
//   category_id int [note: 'Relasi ke task_category']

Ref: task_category.id < task.category_id
Ref: task.id - task_academic.task_id
```

## 6. Model & Relasi

- `TaskCategory` (baru): `hasMany(Task::class, 'category_id')`, `SoftDeletes`. Scope `tipe($tipe)`.
- `TaskAcademic` (baru): `belongsTo(Task::class)`, `SoftDeletes`, cast kolom boolean. Accessor `progress` = jumlah `tugas_1..4` yang `true` (0–4).
- `Task` (ubah): `belongsTo(TaskCategory::class, 'category_id')`, `hasOne(TaskAcademic::class)`. Tambahkan `category_id` ke `$fillable`. Hook `deleting` untuk ikut men-soft-delete `task_academic`.

## 7. Routes

Nama route dan gaya penulisan **disesuaikan dengan konvensi existing**. Rute statis (`/task/metopen`, `/task/artikel-ilmiah`) harus didaftarkan **sebelum** rute berparameter agar tidak tertabrak.

| Method | URI | Aksi | Keterangan |
|---|---|---|---|
| GET | `/task` | `TaskController@index` | **Ubah**: tambah kolom & filter Kategori |
| GET | `/task/metopen` | `TaskController@metopen` | **Baru**: list khusus Metopen |
| GET | `/task/artikel-ilmiah` | `TaskController@artikelIlmiah` | **Baru**: list khusus Artikel Ilmiah |
| POST | `/task` (store existing) | `TaskController@store` | **Ubah**: terima `category_id` + field akademik |
| PUT/POST | update existing | `TaskController@update` | **Ubah**: sama seperti store |
| GET | `/task/detail/{id}` | `TaskController@detail` | **Ubah**: card tambahan bila tipe akademik |
| PATCH | `/task/{id}/checklist` | `TaskController@updateChecklist` | **Baru**: simpan checklist Tugas 1–4 |
| GET | `/task-category` | `TaskCategoryController@index` | **Baru** (master data) |
| POST | `/task-category` | `TaskCategoryController@store` | **Baru** |
| PUT/POST | `/task-category/{id}` | `TaskCategoryController@update` | **Baru** |
| DELETE | `/task-category/{id}` | `TaskCategoryController@destroy` | **Baru** |

Jika list task existing memakai endpoint data terpisah (AJAX/datatable), **tambahkan parameter `tipe`** pada endpoint yang sama. Jangan menduplikasi query.

## 8. Controller & Validasi

### 8.1 `TaskCategoryController` (baru)
Tiru `ClientController`. Validasi `nama`: required, string, max 255, unique (abaikan soft-deleted). `destroy` ditolak dengan pesan jelas bila `is_system = true` atau kategori dipakai task. `tipe` dan `is_system` tidak boleh diubah dari request.

### 8.2 `TaskController` (ubah)
- `store`/`update`: jalankan dalam **DB transaction**. Simpan task seperti biasa (plus `category_id`), lalu tangani `task_academic` sesuai aturan di bagian 4.
- Validasi kondisional berdasarkan `tipe` dari kategori yang dipilih:

```php
'category_id' => ['required', 'exists:task_category,id'],
'prodi'       => [Rule::requiredIf($isAcademic), 'nullable', 'string', 'max:255'],
'judul'       => [Rule::requiredIf($isAcademic), 'nullable', 'string'],
'keterangan'  => ['nullable', 'string'],
'is_lanjutan_metopen' => ['nullable', 'boolean'],   // hanya diproses jika tipe = artikel_ilmiah
'tugas_1'     => ['nullable', 'boolean'],           // sama untuk tugas_2..tugas_4
```

  Tentukan `$isAcademic` dari tipe kategori di server. **Jangan percaya field tersembunyi dari client.**
- `index`: eager load `category`. Tambah filter `category_id`.
- `metopen()` dan `artikelIlmiah()`: keduanya memanggil satu method privat `listByTipe($tipe)` dan merender view yang sama. Eager load `client`, `worker`, `category`, `academic`.
- `detail`: eager load `category` dan `academic`, kirim ke view.
- `updateChecklist(Request $request, $id)`: menerima `tugas_1..tugas_4` (boolean), hanya untuk task bertipe akademik. Otorisasi: **Admin**, atau **Worker yang `id`-nya sama dengan `task.worker_id`**. Selain itu tolak (403). Mengembalikan JSON (`success`, `progress`).

## 9. UI / View

**Prinsip:** ikuti style existing (warna biru, card putih dengan header ikon, tombol biru/abu/merah, badge status, font, spacing). Halaman master data **Kategori Task** meniru halaman **Client** (judul + subjudul, tombol tambah di kanan atas, search, tabel dengan kolom aksi).

### 9.1 Sidebar
- Menu **Task** menjadi item expandable dengan 3 submenu: **General** (`/task`), **Metopen** (`/task/metopen`), **Artikel Ilmiah** (`/task/artikel-ilmiah`).
- Menu parent otomatis terbuka dan ter-highlight bila salah satu submenu aktif; submenu aktif punya highlight sendiri.
- Tambahkan item **Kategori Task** di grup **DATA MASTER** (di bawah Client dan Worker).
- Gunakan mekanisme dropdown/collapse yang sudah ada di template. Jika belum ada, buat konsisten dengan style sidebar sekarang, termasuk mode mini sidebar (`user_preferences.mini_sidebar`).

### 9.2 Master Kategori Task (baru)
- View index: tabel kolom **#, Nama Kategori, Tipe, Aksi**. Tambah/edit lewat modal (pola yang sama seperti Client). Tombol hapus disembunyikan untuk kategori `is_system`.
- Pada form tambah/edit, hanya field **Nama** yang bisa diisi.

### 9.3 Form Add / Update Task (modal, ubah)
- Tambahkan field **Kategori** (select, wajib). Letakkan di bagian atas form, sebelum Customer/Worker.
- Setiap `<option>` membawa `data-tipe`. JS menampilkan/menyembunyikan blok tambahan berdasarkan tipe:
  - `general`: blok tambahan tersembunyi, form sama seperti sekarang.
  - `metopen`/`artikel_ilmiah`: tampilkan blok berisi **Prodi**, **Judul** (label dinamis), **Keterangan** (textarea), dan checklist **Tugas 1–4**.
  - `artikel_ilmiah`: tambahkan toggle **Lanjutan dari Metopen**.
- Saat blok disembunyikan, field di dalamnya di-`disable` agar tidak terkirim dan tidak ikut divalidasi.
- Blok tambahan dibuat sebagai **partial** (misalnya `task/partials/academic-fields.blade.php`) dan dipakai oleh modal add maupun update.
- Modal update memuat kategori dan data `task_academic` yang sudah ada, lalu memicu tampil/sembunyi blok sesuai tipe.
- Di halaman Metopen/Artikel Ilmiah, tombol **Tambah** membuka modal yang sama dengan kategori **sudah terpilih dan dikunci** (supaya task yang baru dibuat langsung muncul di list tersebut).
- Tampilkan error validasi dengan cara yang sama seperti form existing.

### 9.4 List General `/task` (ubah)
- Tambahkan kolom **Kategori** (badge) dan filter **Kategori** di baris filter existing. Kolom dan fungsi lain tidak berubah.

### 9.5 List Metopen & Artikel Ilmiah (baru)
Satu view bersama (misalnya `task/academic-index.blade.php`) dengan variabel `$tipe`. Judul halaman dan subjudul menyesuaikan kategori. Tampilan lebih sederhana dari General:

| Kolom | Catatan |
|---|---|
| # | |
| Kode | `kode_task` |
| Customer | |
| Prodi | |
| Judul | dipotong (truncate) dengan tooltip |
| Worker | |
| Deadline | format tanggal sama seperti General |
| Progress | badge `x/4` dari checklist |
| Task Status | badge yang sama dengan General |
| Aksi | Detail, Edit, Hapus |

- Khusus Artikel Ilmiah, tambahkan penanda badge **Lanjutan Metopen** (pada kolom Judul atau kolom tersendiri).
- Kolom harga, pay to worker, margin, dan pay status **tidak ditampilkan** di sini (tetap ada di General dan di detail).
- Search mencakup kode, customer, worker, prodi, dan judul. Pagination dan pilihan jumlah entri mengikuti tabel existing.

### 9.6 Detail Task (ubah)
Tetap satu route dan satu view detail. Tambahan hanya muncul bila tipe akademik:

- Card **Data Tasking** (existing) menambah baris **Kategori**.
- Card baru **Detail Metopen** / **Detail Artikel Ilmiah** berisi Prodi, Judul, Keterangan, dan (khusus Artikel Ilmiah) status Lanjutan dari Metopen.
- Card baru **Checklist Tugas** berisi 4 checkbox Tugas 1–4 dan indikator progress `x/4`. Card ini adalah tempat **worker mengisi progres**.
  - Editable untuk Admin dan worker yang ditugaskan pada task tersebut. Selain itu tampil read-only.
  - Setiap centang disimpan lewat AJAX ke `PATCH /task/{id}/checklist`. Beri feedback (toast/notifikasi existing) dan kembalikan ke posisi semula bila gagal.
- Saran layout: kolom kiri = Data Tasking lalu Detail Metopen/Artikel Ilmiah; kolom kanan = Checklist Tugas lalu File.
- Untuk kategori Parafrase, detail **tidak berubah** selain baris Kategori.

## 10. Acceptance Criteria

**Database & data**
- [ ] Migration berjalan mulus pada database yang sudah berisi data; semua task lama memiliki `category_id` Parafrase.
- [ ] Seed 3 kategori tidak menduplikasi data bila dijalankan berulang.

**Master Kategori**
- [ ] Bisa tambah, edit nama, dan hapus kategori non-sistem; kategori sistem/terpakai tidak bisa dihapus.

**Form task**
- [ ] Memilih Parafrase: form identik dengan sebelumnya; task tersimpan tanpa data `task_academic`.
- [ ] Memilih Metopen atau Artikel Ilmiah: blok tambahan muncul; Prodi dan Judul wajib; toggle Lanjutan hanya muncul di Artikel Ilmiah.
- [ ] Edit task mengubah kategori sesuai aturan bagian 4 tanpa error dan tanpa data yatim.
- [ ] Validasi tetap benar walau field diakali dari sisi client.

**Navigasi & list**
- [ ] Sidebar menampilkan submenu General/Metopen/Artikel Ilmiah dengan state aktif/terbuka yang benar, termasuk saat sidebar mini.
- [ ] General menampilkan semua task (semua kategori) dengan kolom dan filter Kategori.
- [ ] Metopen dan Artikel Ilmiah hanya menampilkan task kategorinya, dengan kolom sederhana dan progress `x/4`.

**Detail & checklist**
- [ ] Detail Metopen/Artikel Ilmiah menampilkan card tambahan; detail Parafrase tetap seperti sekarang.
- [ ] Worker yang ditugaskan dan Admin bisa mengubah checklist; worker lain tidak bisa (403).
- [ ] Progress di detail dan list konsisten setelah checklist diubah.

**Umum**
- [ ] Tampilan konsisten dengan style existing dan tidak rusak di layar kecil.
- [ ] Tidak ada regresi pada fitur task, client, dan worker yang sudah ada.

## 11. Asumsi & Pertanyaan Terbuka

Asumsi yang dipakai di dokumen ini (konfirmasi bila berbeda dari kebutuhan):

1. Worker bisa login dan melihat task miliknya, sehingga bisa mengisi checklist sendiri. Jika worker belum punya akses login, checklist untuk sementara diisi Admin dan aturan otorisasi di 8.2 disesuaikan.
2. Label "Tugas 1–4" bersifat tetap (tidak bisa dikonfigurasi per kategori). Jika nanti tiap tugas perlu nama khusus (misalnya "Bab 1"), tambahkan konfigurasi tersendiri.
3. Kolom `pay_status`, harga, dan margin sengaja tidak ditampilkan di list Metopen/Artikel Ilmiah, hanya di General dan detail. Ubah bila admin perlu melihatnya di sana.
4. Tombol **Export Excel** tetap hanya di General. Bila perlu, export per kategori bisa ditambahkan belakangan.
5. Riwayat kapan dan siapa yang mencentang tugas tidak dicatat pada versi ini.

## 12. Urutan Pengerjaan (disarankan)

1. **Database**: migration `task_category` → seed → `task.category_id` + backfill → `task_academic`.
2. **Model**: `TaskCategory`, `TaskAcademic`, relasi pada `Task`.
3. **Master Kategori Task**: controller, request, routes, view, menu sidebar.
4. **Form add/update task**: field Kategori, partial blok akademik, JS tampil/sembunyi, validasi, logika simpan.
5. **List**: kolom + filter Kategori di General, lalu halaman Metopen dan Artikel Ilmiah.
6. **Sidebar**: menu Task expandable dengan 3 submenu.
7. **Detail**: card tambahan, card checklist, endpoint `updateChecklist`.
8. **QA**: jalankan seluruh Acceptance Criteria, terutama regresi pada task Parafrase dan data lama.
