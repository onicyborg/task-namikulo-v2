<?php
function tglIndo($tanggal)
{
    $tanggal = date('Y-m-d', strtotime($tanggal));
    $bulan = array(
        1 =>   'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    );
    $pecahkan = explode('-', $tanggal);
    return $pecahkan[2] . ' ' . $bulan[(int)$pecahkan[1]] . ' ' . $pecahkan[0];
}

function hariTglIndo($tanggal)
{
    $tanggal = date('Y-m-d', strtotime($tanggal));
    // Array nama hari dalam Bahasa Indonesia
    $nama_hari = array(
        'Minggu',
        'Senin',
        'Selasa',
        'Rabu',
        'Kamis',
        'Jumat',
        'Sabtu'
    );

    // Array nama bulan dalam Bahasa Indonesia
    $nama_bulan = array(
        1 => 'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    );

    // Pecah tanggal menjadi tahun, bulan, dan hari
    $pecahkan = explode('-', $tanggal);
    $tahun = $pecahkan[0];
    $bulan = (int)$pecahkan[1];
    $hari = $pecahkan[2];

    // Dapatkan nama hari berdasarkan tanggal
    $nama_hari_tanggal = $nama_hari[date('w', strtotime($tanggal))];

    // Format tanggal
    $tanggal_formatted = $nama_hari_tanggal . ', ' . $hari . ' ' . $nama_bulan[$bulan] . ' ' . $tahun;

    return $tanggal_formatted;
}

function rupiah($angka, $status = false)
{
    $hasil_rupiah =  number_format($angka, '0', '.', '.');
    if ($status == true) {
        return 'Rp. ' . $hasil_rupiah;
    } else {
        return $hasil_rupiah;
    }
}

function bulanIndo($angka)
{
    $bulan = array(
        1 =>   'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    );

    return $bulan[(int)$angka];
}
