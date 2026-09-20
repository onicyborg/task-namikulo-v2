@extends('area._base')

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2 class="page-title">Kategori Task</h2>
            <p class="page-subtitle">Kelola kategori tambahan untuk task general</p>
        </div>
        <div class="page-header-right">
            <button class="btn btn-primary" data-toggle="modal" data-target="#modal_category"><i data-feather="plus" style="width:16px;height:16px;"></i> Tambah Kategori</button>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="table-toolbar">
                <div class="table-toolbar-left">
                    <div class="search-box">
                        <i data-feather="search" style="width:16px;height:16px;"></i>
                        <input type="text" id="search_filter" class="form-control" placeholder="Cari kategori..." />
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table" id="table-1" style="width:100%">
                    <thead><tr><th>#</th><th>Nama</th><th>Tipe</th><th>Status</th><th>Action</th></tr></thead>
                    <tbody>
                        @foreach ($categories as $index => $category)
                            <tr><td>{{ $index + 1 }}</td><td class="font-weight-bold">{{ $category->nama }}</td><td><span class="badge badge-light">{{ $category->tipe }}</span></td><td>{{ $category->is_system ? 'Bawaan Sistem' : 'Custom' }}</td><td class="text-nowrap"><button class="btn btn-icon btn-sm btn-warning mr-1" aria-label="Edit {{ $category->nama }}" onclick="editCategory({{ $category->id }}, @js($category->nama))"><i data-feather="edit-2" style="width:16px;height:16px;"></i></button>@if (!$category->is_system)<button class="btn btn-icon btn-sm btn-danger" aria-label="Hapus {{ $category->nama }}" onclick="deleteCategory({{ $category->id }})"><i data-feather="trash-2" style="width:16px;height:16px;"></i></button>@endif</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('modal')
    <div class="modal fade" id="modal_category"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="category_title">Tambah Kategori</h5><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span class="modal-close-mark" aria-hidden="true">&times;</span></button></div><div class="modal-body"><form id="category_form"><input type="hidden" id="category_id" name="id"><div class="form-group"><label>Nama Kategori</label><input class="form-control" name="nama" id="category_name" required></div></form></div><div class="modal-footer"><button class="btn btn-secondary" data-dismiss="modal">Batal</button><button class="btn btn-primary" id="category_save">Simpan</button></div></div></div></div>
@endsection

@push('js')
<script>
    var categoryTable = $('#table-1').DataTable({
        "dom": "<'dt--top-section'>" + "<''tr>" + "<'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--length-info d-flex justify-content-center align-middle 'l<'dt--pages-count ml-1'i>><'dt--pagination mt-sm-0 mt-3'p>>",
        "oLanguage": {"sLengthMenu": "_MENU_"},
        processing: true,
        order: [],
        columnDefs: [{ targets: [0, 4], orderable: false, searchable: false }],
        drawCallback: function () { feather.replace(); }
    });
    $("#search_filter").on("keyup", function() { categoryTable.search($(this).val()).draw(); });
    var categoryEdit = false;
    function editCategory(id, name) { categoryEdit = true; $('#category_title').text('Edit Kategori'); $('#category_id').val(id); $('#category_name').val(name); $('#modal_category').modal('show'); }
    $('#category_save').on('click', function () { var data = $('#category_form').serialize(); $.post('{{ url('task-category') }}/' + (categoryEdit ? 'edit' : 'add'), data + '&_token={{ csrf_token() }}').done(function (r) { if (r.status == 1) location.reload(); }).fail(errorAjaxResponse); });
    function deleteCategory(id) { Swal.fire({title:'Hapus kategori?', icon:'warning', showCancelButton:true}).then(function (r) { if (r.isConfirmed) $.post('{{ url('task-category/delete') }}', {_token:'{{ csrf_token() }}', id:id}).done(function (r) { if (r.status == 1) location.reload(); else Swal.fire('Gagal', r.msg, 'error'); }).fail(errorAjaxResponse); }); }
</script>
@endpush
