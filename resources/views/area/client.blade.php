@extends('area._base')
@push('head')
@endpush
@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2 class="page-title">Data Client</h2>
            <p class="page-subtitle">Kelola data client dan informasi kontak</p>
        </div>
        <div class="page-header-right">
            <button href="#" class="btn btn-primary" data-toggle="modal" data-target="#modal_add">
                <i data-feather="plus" style="width: 16px; height: 16px;"></i> Tambah Client
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-toolbar">
                <div class="table-toolbar-left">
                    <div class="search-box">
                        <i data-feather="search" style="width: 16px; height: 16px;"></i>
                        <input type="text" id="search_filter" class="form-control" placeholder="Cari client..." />
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table" id="table-1" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Customer</th>
                            <th>Handphone</th>
                            <th>Jenis Kelamin</th>
                            <th>Asal</th>
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
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="formModal">
                        <i data-feather="user-plus" style="width: 18px; height: 18px; vertical-align: middle; margin-right: 8px;"></i>
                        Tambah Client
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span class="modal-close-mark" aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form_add">
                        <div class="form-group">
                            <label class="form-label">Customer</label>
                            <input type="text" class="form-control" name="customer" id="customer_add" placeholder="Nama customer">
                            <div id="error_customer_add" class="invalid-feedback"></div>
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
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="formModal">
                        <i data-feather="edit-2" style="width: 18px; height: 18px; vertical-align: middle; margin-right: 8px;"></i>
                        Edit Client
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span class="modal-close-mark" aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form_edit">
                        <input type="hidden" name="id" id="id_edit">
                        <div class="form-group">
                            <label class="form-label">Customer</label>
                            <input type="text" class="form-control" name="customer" id="customer_edit" placeholder="Nama customer">
                            <div id="error_customer_edit" class="invalid-feedback"></div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Handphone</label>
                            <input type="text" class="form-control" name="handphone" id="handphone_edit" placeholder="Nomor telepon">
                            <div id="error_handphone_edit" class="invalid-feedback"></div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="jk" id="jk_edit" class="form-control">
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
        $(document).ready(function() {
            $('#jk_add').select2({width: '100%', placeholder: 'Pilih', dropdownParent: $('#modal_add')});
            $('#jk_edit').select2({width: '100%', placeholder: 'Pilih', dropdownParent: $('#modal_edit')});
        });

        var datatable = $("#table-1").DataTable({
            "dom": "<'dt--top-section'>" + "<''tr>" + "<'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--length-info d-flex justify-content-center align-middle 'l<'dt--pages-count ml-1'i>><'dt--pagination mt-sm-0 mt-3'p>>",
            "oLanguage": {"sLengthMenu": "_MENU_"},
            processing: true,
            serverSide: true,
            ajax: {url: '{{ url('client/list') }}'},
            columns: [
                {"data": "DT_RowIndex", "name": "DT_RowIndex", "orderable": false, "searchable": false},
                {data: 'customer', name: 'customer'},
                {data: 'handphone', name: 'handphone'},
                {data: 'jk', name: 'jk'},
                {data: 'asal', name: 'asal'},
                {"orderable": false, "searchable": false, "data": null, "render": function(data, type, row, meta) {
                    var url_detail = '{{ asset('client/detail') }}/' + row.id;
                    var html = '<div class="text-nowrap">';
                    html += '<a href="' + url_detail + '" class="btn btn-icon btn-sm btn-primary"><i class="fas fa-file-alt" style="width: 16px"></i></a>';
                    html += '<a href="javascript:;" onclick="editData(\'' + encodeURIComponent(JSON.stringify(row)) + '\')" class="btn btn-icon btn-sm btn-warning mx-1"><i class="fas fa-edit" style="width: 16px"></i></a>';
                    html += '<a href="javascript:;" onclick="deleteData(\'' + row.id + '\')" class="btn btn-icon btn-sm btn-danger"><i class="fas fa-trash" style="width: 16px"></i></a>';
                    html += '</div>';
                    return html;
                }},
            ],
            order: []
        });

        $("#search_filter").on("keyup", function() { datatable.search($(this).val()).draw(); });

        function reloadDataTable() { datatable.ajax.reload(); }

        $("#form_add").submit(function(e) {
            $("#btn_add").prop("disabled", true);
            $("#customer_add").removeClass('is-invalid');
            $("#handphone_add").removeClass('is-invalid');
            $("#jk_add").removeClass('is-invalid');
            $("#asal_add").removeClass('is-invalid');
            var formdata = new FormData(this);
            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                cache: false, contentType: false, processData: false, method: 'post',
                url: "{{ url('client/add') }}", data: formdata, dataType: 'json',
                success: function(response) {
                    $("#btn_add").prop("disabled", false);
                    if (response.status == "1") {
                        swal.fire({icon: "success", title: 'Berhasil', text: response.msg, showConfirmButton: true, timer: 900}).then((result) => {
                            if (result.isConfirmed) {
                                $('#form_add')[0].reset(); $('#jk_add').val('').change(); $('#modal_add').modal('hide'); reloadDataTable();
                            }
                        });
                        setTimeout(function() { $('#form_add')[0].reset(); $('#jk_add').val('').change(); $('#modal_add').modal('hide'); reloadDataTable(); }, 900);
                    } else {
                        swal.fire({icon: "error", title: 'Gagal !', text: response.msg, showConfirmButton: true, timer: 900});
                    }
                },
                error: function(response) {
                    $("#btn_add").prop("disabled", false);
                    if (response.status == 422) {
                        let errorResponse = JSON.parse(response.responseText);
                        if (errorResponse.errors && errorResponse.errors.customer) { $("#customer_add").addClass('is-invalid'); $("#error_customer_add").html(errorResponse.errors.customer[0]); }
                        if (errorResponse.errors && errorResponse.errors.handphone) { $("#handphone_add").addClass('is-invalid'); $("#error_handphone_add").html(errorResponse.errors.handphone[0]); }
                        if (errorResponse.errors && errorResponse.errors.jk) { $("#jk_add").addClass('is-invalid'); $("#error_jk_add").html(errorResponse.errors.jk[0]); }
                        if (errorResponse.errors && errorResponse.errors.asal) { $("#asal_add").addClass('is-invalid'); $("#error_asal_add").html(errorResponse.errors.asal[0]); }
                    } else { errorAjaxResponse(response); }
                }
            });
            e.preventDefault();
        });

        function editData(data) {
            var rowData = JSON.parse(decodeURIComponent(data));
            $("#id_edit").val(rowData.id);
            $("#customer_edit").val(rowData.customer);
            $("#handphone_edit").val(rowData.handphone);
            $("#asal_edit").val(rowData.asal);
            $("#jk_edit").val(rowData.jk).change();
            $("#modal_edit").modal("show");
        }

        $("#form_edit").submit(function(e) {
            $("#btn_edit").prop("disabled", true);
            var formdata = new FormData(this);
            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                cache: false, contentType: false, processData: false, method: 'post',
                url: "{{ url('client/edit') }}", data: formdata, dataType: 'json',
                success: function(response) {
                    $("#btn_edit").prop("disabled", false);
                    if (response.status == "1") {
                        swal.fire({icon: "success", title: 'Berhasil', text: response.msg, showConfirmButton: true, timer: 900}).then((result) => {
                            if (result.isConfirmed) { $('#form_edit')[0].reset(); $('#jk_edit').val('').change(); $('#modal_edit').modal('hide'); reloadDataTable(); }
                        });
                        setTimeout(function() { $('#form_edit')[0].reset(); $('#jk_edit').val('').change(); $('#modal_edit').modal('hide'); reloadDataTable(); }, 900);
                    } else { swal.fire({icon: "error", title: 'Gagal !', text: response.msg, showConfirmButton: true, timer: 900}); }
                },
                error: function(response) {
                    $("#btn_edit").prop("disabled", false);
                    if (response.status == 422) { errorAjaxResponse(response); }
                    else { errorAjaxResponse(response); }
                }
            });
            e.preventDefault();
        });

        function deleteData(id) {
            swal.fire({
                title: 'Apakah anda yakin??', text: "Anda tidak dapat mengembalikan ini !!", icon: "warning",
                showCancelButton: true, confirmButtonText: 'Hapus!', cancelButtonText: 'Batal',
                customClass: { confirmButton: 'swal2-delete-confirm', cancelButton: 'swal2-delete-cancel' }, buttonsStyling: false
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        type: "POST", url: "{{ url('client/delete') }}", data: 'id=' + id,
                        success: function(response) {
                            if (response.status == "1") {
                                swal.fire({icon: "success", title: 'Berhasil', text: response.msg, showConfirmButton: true, timer: 900}).then((result) => {
                                    if (result.isConfirmed) { reloadDataTable(); }
                                });
                                setTimeout(function() { reloadDataTable(); }, 900);
                            } else { swal.fire("Error!", response.msg, "error"); }
                        },
                        error: function(response) { errorAjaxResponse(response); }
                    });
                }
            });
        }
    </script>
@endpush
