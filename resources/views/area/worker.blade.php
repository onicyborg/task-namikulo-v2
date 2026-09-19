@extends('area._base')
@push('head')
@endpush
@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2 class="page-title">Data Worker</h2>
            <p class="page-subtitle">Kelola data worker dan informasi akun</p>
        </div>
        <div class="page-header-right">
            <button href="#" class="btn btn-primary" data-toggle="modal" data-target="#modal_add">
                <i data-feather="plus" style="width: 16px; height: 16px;"></i> Tambah Worker
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-toolbar">
                <div class="table-toolbar-left">
                    <div class="search-box">
                        <i data-feather="search" style="width: 16px; height: 16px;"></i>
                        <input type="text" id="search_filter" class="form-control" placeholder="Cari worker..." />
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table" id="table-1" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Foto</th>
                            <th>Nama</th>
                            <th>Handphone</th>
                            <th>Jenis Kelamin</th>
                            <th>Asal</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Hex Color</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('modal')
    <div class="modal fade" id="modal_add" role="dialog" aria-labelledby="formModal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="formModal">
                        <i data-feather="user-plus" style="width: 18px; height: 18px; vertical-align: middle; margin-right: 8px;"></i>
                        Tambah Worker
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i data-feather="x" style="width: 18px; height: 18px;"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form_add">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-label">Fullname</label>
                                    <input type="text" class="form-control" name="fullname" id="fullname_add" placeholder="Nama lengkap">
                                    <div id="error_fullname_add" class="invalid-feedback"></div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Handphone</label>
                                    <input type="text" class="form-control" name="handphone" id="handphone_add" placeholder="Nomor telepon">
                                    <div id="error_handphone_add" class="invalid-feedback"></div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Jenis Kelamin</label>
                                    <select name="jk" id="jk_add" class="form-control">
                                        <option></option>
                                        <option value="Pria">Pria</option>
                                        <option value="Wanita">Wanita</option>
                                    </select>
                                    <div id="error_jk_add" class="invalid-feedback"></div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Asal</label>
                                    <input type="text" class="form-control" name="asal" id="asal_add" placeholder="Kota atau asal daerah">
                                    <div id="error_asal_add" class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-label">Username</label>
                                    <input type="text" class="form-control" name="username" id="username_add" placeholder="Username login">
                                    <div id="error_username_add" class="invalid-feedback"></div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Email</label>
                                    <input type="text" class="form-control" name="email" id="email_add" placeholder="Alamat email">
                                    <div id="error_email_add" class="invalid-feedback"></div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Password</label>
                                    <input type="text" class="form-control" name="password" id="password_add" placeholder="Password login">
                                    <div id="error_password_add" class="invalid-feedback"></div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Hex Color</label>
                                    <div class="color-picker-wrapper">
                                        <input type="color" class="form-control form-control-color" name="hex" id="hex_add" value="#3a7bd5">
                                    </div>
                                    <div id="error_hex_add" class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" id="btn_add" class="btn btn-primary">
                                <i data-feather="save" style="width: 16px; height: 16px; vertical-align: middle; margin-right: 4px;"></i>
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal_edit" role="dialog" aria-labelledby="formModal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="formModal">
                        <i data-feather="edit-2" style="width: 18px; height: 18px; vertical-align: middle; margin-right: 8px;"></i>
                        Edit Worker
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i data-feather="x" style="width: 18px; height: 18px;"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form_edit">
                        <input type="hidden" name="id" id="id_edit">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-label">Fullname</label>
                                    <input type="text" class="form-control" name="fullname" id="fullname_edit" placeholder="Nama lengkap">
                                    <div id="error_fullname_edit" class="invalid-feedback"></div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Handphone</label>
                                    <input type="text" class="form-control" name="handphone" id="handphone_edit" placeholder="Nomor telepon">
                                    <div id="error_handphone_edit" class="invalid-feedback"></div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Jenis Kelamin</label>
                                    <select name="jk" id="jk_edit" class="form-control">
                                        <option></option>
                                        <option value="Pria">Pria</option>
                                        <option value="Wanita">Wanita</option>
                                    </select>
                                    <div id="error_jk_edit" class="invalid-feedback"></div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Asal</label>
                                    <input type="text" class="form-control" name="asal" id="asal_edit" placeholder="Kota atau asal daerah">
                                    <div id="error_asal_edit" class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-label">Username</label>
                                    <input type="text" class="form-control" name="username" id="username_edit" placeholder="Username login">
                                    <div id="error_username_edit" class="invalid-feedback"></div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Email</label>
                                    <input type="text" class="form-control" name="email" id="email_edit" placeholder="Alamat email">
                                    <div id="error_email_edit" class="invalid-feedback"></div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Password</label>
                                    <input type="text" class="form-control" name="password" id="password_edit" placeholder="Kosongkan jika tidak ingin mengubah">
                                    <span class="form-hint">*kosongkan bila tidak ingin mengganti password</span>
                                    <div id="error_password_edit" class="invalid-feedback"></div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Hex Color</label>
                                    <div class="color-picker-wrapper">
                                        <input type="color" class="form-control form-control-color" name="hex" id="hex_edit" value="#3a7bd5">
                                    </div>
                                    <div id="error_hex_edit" class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submmit" id="btn_edit" class="btn btn-primary">
                                <i data-feather="save" style="width: 16px; height: 16px; vertical-align: middle; margin-right: 4px;"></i>
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>
        $(document).ready(function() { $('#jk_add').select2({width: '100%', placeholder: 'Pilih'}); $('#jk_edit').select2({width: '100%', placeholder: 'Pilih'}); });

        var datatable = $("#table-1").DataTable({
            "dom": "<'dt--top-section'>" + "<''tr>" + "<'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--length-info d-flex justify-content-center align-middle 'l<'dt--pages-count ml-1'i>><'dt--pagination mt-sm-0 mt-3'p>>",
            "oLanguage": {"sLengthMenu": "_MENU_"},
            processing: true, serverSide: true, ajax: {url: '{{ url('worker/list-active') }}'},
            columns: [
                {"data": "DT_RowIndex", "name": "DT_RowIndex", "orderable": false, "searchable": false},
                {"orderable": false, "searchable": false, "data": null, "render": function(data) { return '<img src="' + data.img + '" style="width:50px">'; }},
                {data: 'fullname', name: 'fullname'},
                {data: 'handphone', name: 'handphone'},
                {data: 'jk', name: 'jk'},
                {data: 'asal', name: 'asal'},
                {data: 'username', name: 'username'},
                {data: 'email', name: 'email'},
                {"orderable": false, "searchable": false, "data": null, "render": function(data) { return '<div style="width:100px;height:10px;background-color:' + data.hex + ';"></div>'; }},
                {"orderable": false, "searchable": false, "data": null, "render": function(data) {
                    var url_detail = '{{ asset('worker/detail') }}/' + data.id;
                    return '<div class="text-nowrap"><a href="' + url_detail + '" class="btn btn-icon btn-sm btn-primary"><i class="fas fa-file-alt" style="width: 16px"></i></a><a href="javascript:;" onclick="editData(\'' + encodeURIComponent(JSON.stringify(data)) + '\')" class="btn btn-icon btn-sm btn-warning mx-1"><i class="fas fa-edit" style="width: 16px"></i></a><a href="javascript:;" onclick="deleteData(\'' + data.id + '\')" class="btn btn-icon btn-sm btn-danger"><i class="fas fa-trash" style="width: 16px"></i></a></div>';
                }}
            ],
            order: []
        });

        $("#search_filter").on("keyup", function() { datatable.search($(this).val()).draw(); });

        function reloadDataTable() { datatable.ajax.reload(); }

        $("#form_add").submit(function(e) {
            $("#btn_add").prop("disabled", true);
            var formdata = new FormData(this);
            $.ajax({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, cache: false, contentType: false, processData: false, method: 'post', url: "{{ url('worker/add') }}", data: formdata, dataType: 'json',
                success: function(response) {
                    $("#btn_add").prop("disabled", false);
                    if (response.status == "1") { swal.fire({icon: "success", title: 'Berhasil', text: response.msg, showConfirmButton: true, timer: 900}).then((result) => { if (result.isConfirmed) { $('#form_add')[0].reset(); $('#jk_add').val('').change(); $('#modal_add').modal('hide'); reloadDataTable(); } }); setTimeout(function() { $('#form_add')[0].reset(); $('#jk_add').val('').change(); $('#modal_add').modal('hide'); reloadDataTable(); }, 900); }
                    else { swal.fire({icon: "error", title: 'Gagal !', text: response.msg, showConfirmButton: true, timer: 900}); }
                },
                error: function(response) { $("#btn_add").prop("disabled", false); errorAjaxResponse(response); }
            });
            e.preventDefault();
        });

        function editData(data) {
            var rowData = JSON.parse(decodeURIComponent(data));
            $("#id_edit").val(rowData.id); $("#fullname_edit").val(rowData.fullname); $("#handphone_edit").val(rowData.handphone); $("#asal_edit").val(rowData.asal); $("#jk_edit").val(rowData.jk).change(); $("#username_edit").val(rowData.username); $("#email_edit").val(rowData.email); $("#hex_edit").val(rowData.hex); $("#modal_edit").modal("show");
        }

        $("#form_edit").submit(function(e) {
            $("#btn_edit").prop("disabled", true);
            var formdata = new FormData(this);
            $.ajax({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, cache: false, contentType: false, processData: false, method: 'post', url: "{{ url('worker/edit') }}", data: formdata, dataType: 'json',
                success: function(response) {
                    $("#btn_edit").prop("disabled", false);
                    if (response.status == "1") { swal.fire({icon: "success", title: 'Berhasil', text: response.msg, showConfirmButton: true, timer: 900}).then((result) => { if (result.isConfirmed) { $('#form_edit')[0].reset(); $('#jk_edit').val('').change(); $('#modal_edit').modal('hide'); reloadDataTable(); } }); setTimeout(function() { $('#form_edit')[0].reset(); $('#jk_edit').val('').change(); $('#modal_edit').modal('hide'); reloadDataTable(); }, 900); }
                    else { swal.fire({icon: "error", title: 'Gagal !', text: response.msg, showConfirmButton: true, timer: 900}); }
                },
                error: function(response) { $("#btn_edit").prop("disabled", false); errorAjaxResponse(response); }
            });
            e.preventDefault();
        });

        function deleteData(id) {
            swal.fire({title: 'Apakah anda yakin??', text: "Anda tidak dapat mengembalikan ini !!", icon: "warning", showCancelButton: true, confirmButtonText: 'Hapus!', cancelButtonText: 'Batal', confirmButtonClass: 'btn btn-danger mr-3', cancelButtonClass: 'btn btn-secondary', buttonsStyling: false}).then(function(result) {
                if (result.value) { $.ajax({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, type: "POST", url: "{{ url('worker/delete') }}", data: 'id=' + id, success: function(response) { if (response.status == "1") { swal.fire({icon: "success", title: 'Berhasil', text: response.msg, showConfirmButton: true, timer: 900}).then((result) => { if (result.isConfirmed) { reloadDataTable(); } }); setTimeout(function() { reloadDataTable(); }, 900); } else { swal.fire("Error!", response.msg, "error"); } }, error: function(response) { errorAjaxResponse(response); } }); }
            });
        }
    </script>
@endpush
