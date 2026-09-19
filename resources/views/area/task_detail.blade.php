@extends('area._base')
@push('head')
@endpush
@section('content')
    <div class="detail-layout">
        <div class="detail-left">
            <div class="detail-info-card">
                <div class="detail-info-header">
                    <i data-feather="clipboard"></i>
                    <h4>Data Tasking</h4>
                    <button class="btn btn-primary d-lg-none ml-auto" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                        <i data-feather="file-text" style="width: 16px; height: 16px;"></i> View Detail
                    </button>
                </div>
                <div class="detail-info-body">
                    <div class="collapse d-lg-block" id="collapseExample">
                        <div class="info-list">
                            <div class="info-row">
                                <span class="info-label">Kode</span>
                                <span class="info-value">{{ $task->kode_task }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Deskripsi</span>
                                <span class="info-value">{{ $task->task }}</span>
                            </div>
                            @if (Auth::user()->role == 'Admin')
                                <div class="info-row">
                                    <span class="info-label">Client</span>
                                    <span class="info-value">{{ $task->customer }}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Worker</span>
                                    <span class="info-value">{{ $task->worker }}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Tanggal Order</span>
                                    <span class="info-value">{{ hariTglIndo($task->order) }}</span>
                                </div>
                            @endif
                            <div class="info-row">
                                <span class="info-label">Deadline</span>
                                <span class="info-value">{{ hariTglIndo($task->deadline) }}</span>
                            </div>
                            @if (Auth::user()->role == 'Admin')
                                <div class="info-row">
                                    <span class="info-label">Price Order</span>
                                    <span class="info-value">{{ rupiah($task->price_order, true) }}</span>
                                </div>
                            @endif
                            <div class="info-row">
                                <span class="info-label">Pay to Worker</span>
                                <span class="info-value">{{ rupiah($task->pay_worker, true) }}</span>
                            </div>
                            @if (Auth::user()->role == 'Admin')
                                <div class="info-row">
                                    <span class="info-label">Margin</span>
                                    <span class="info-value">{{ rupiah($task->margin, true) }}</span>
                                </div>
                            @endif
                            <div class="info-row">
                                <span class="info-label">Task Status</span>
                                <span class="info-value">{{ $task->task_status }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Pay Status</span>
                                <span class="info-value">{{ $task->pay_status }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="detail-right">
            <div class="detail-info-card">
                <div class="detail-info-header">
                    <i data-feather="folder"></i>
                    <h4>File</h4>
                    @if (Auth::user()->role == 'Worker')
                        <button class="btn btn-primary ml-auto" data-toggle="modal" data-target="#modal_add">
                            <i data-feather="plus" style="width: 16px; height: 16px;"></i> Tambah
                        </button>
                    @endif
                </div>
                <div class="detail-info-body">
                    <div class="detail-search-row">
                        <input type="text" id="search_filter" class="form-control" placeholder="Search..." />
                    </div>
                    <div class="detail-table-wrapper">
                        <table class="detail-table" id="table" style="width: 100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Deskripsi</th>
                                    <th>File</th>
                                    <th>Waktu Upload</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 1; @endphp
                                @foreach ($detail as $row)
                                    <tr>
                                        <td>{{ $no++ }}</td>
                                        <td>{{ $row->deskripsi }}</td>
                                        <td><a href="{{ asset($row->file) }}" target="_blank" class="detail-file-link">{{ str_replace('storage/task/', '', $row->file) }}</a></td>
                                        <td>{{ tglIndo($row->created_at) }}</td>
                                        <td class="text-nowrap">
                                            <a href="javascript:;" onclick="editData('{{ $row }}')" class="detail-btn-icon detail-btn-warning" title="Edit"><i data-feather="edit-2"></i></a>
                                            <a href="javascript:;" onclick="deleteData('{{ $row->id }}')" class="detail-btn-icon detail-btn-danger" title="Hapus"><i data-feather="trash-2"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('modal')
    <div class="modal fade" id="modal_add" role="dialog" aria-labelledby="formModal" aria-hidden="true">
        <div class="modal-dialog" role="document"><div class="modal-content">
            <div class="modal-header">
                    <h5 class="modal-title">Tambah File</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i data-feather="x" style="width: 20px; height: 20px;"></i>
                    </button>
                </div>
            <div class="modal-body">
                <form id="form_add" enctype="multipart/form-data">
                    <input type="hidden" name="kode_task" value="{{ $task->kode_task }}">
                    <div class="form-group"><label>Deskripsi</label><textarea name="deskripsi" id="deskripsi_add" class="form-control"></textarea><div id="error_deskripsi_add" class="invalid-feedback"></div></div>
                    <div class="form-group"><label>File</label><input type="file" name="file" id="file_add" class="form-control"><div id="error_file_add" class="invalid-feedback"></div></div>
                    <button type="submit" id="btn_add" class="btn btn-primary">Save</button>
                </form>
            </div>
        </div></div>
    </div>
    <div class="modal fade" id="modal_edit" role="dialog" aria-labelledby="formModal" aria-hidden="true">
        <div class="modal-dialog" role="document"><div class="modal-content">
            <div class="modal-header">
                    <h5 class="modal-title">Edit File</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i data-feather="x" style="width: 20px; height: 20px;"></i>
                    </button>
                </div>
            <div class="modal-body">
                <form id="form_edit" enctype="multipart/form-data">
                    <input type="hidden" name="id" id="id_edit">
                    <div class="form-group"><label>Deskripsi</label><textarea name="deskripsi" id="deskripsi_edit" class="form-control"></textarea><div id="error_deskripsi_edit" class="invalid-feedback"></div></div>
                    <div class="form-group"><label>File</label><input type="file" name="file" id="file_edit" class="form-control"></div>
                    <button type="submit" id="btn_edit" class="btn btn-primary">Save</button>
                </form>
            </div>
        </div></div>
    </div>
@endsection
@push('js')
    <script>
        var datatable = $("#table").DataTable({"dom": "<'dt--top-section'>" + "<''tr>" + "<'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--length-info d-flex justify-content-center align-middle 'l<'dt--pages-count ml-1'i>><'dt--pagination mt-sm-0 mt-3'p>>", "oLanguage": {"sLengthMenu": "_MENU_"}});
        $("#search_filter").on("keyup", function() { datatable.search($(this).val()).draw(); });

        $("#form_add").submit(function(e) {
            $("#btn_add").prop("disabled", true);
            var formdata = new FormData(this);
            $.ajax({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, cache: false, contentType: false, processData: false, method: 'post', url: "{{ url('task/add-file') }}", data: formdata, dataType: 'json',
                success: function(response) {
                    $("#btn_add").prop("disabled", false);
                    if (response.status == "1") { swal.fire({icon: "success", title: 'Berhasil', text: response.msg, showConfirmButton: true, timer: 900}).then((result) => { if (result.isConfirmed) { location.reload(); } }); setTimeout(function() { location.reload(); }, 900); }
                    else { swal.fire({icon: "error", title: 'Gagal !', text: response.msg, showConfirmButton: true, timer: 900}); }
                },
                error: function(response) { $("#btn_add").prop("disabled", false); errorAjaxResponse(response); }
            });
            e.preventDefault();
        });

        function editData(data) {
            var rowData = JSON.parse(decodeURIComponent(data));
            $("#id_edit").val(rowData.id);
            $("#deskripsi_edit").val(rowData.deskripsi);
            $("#modal_edit").modal("show");
        }

        $("#form_edit").submit(function(e) {
            $("#btn_edit").prop("disabled", true);
            var formdata = new FormData(this);
            $.ajax({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, cache: false, contentType: false, processData: false, method: 'post', url: "{{ url('task/edit-file') }}", data: formdata, dataType: 'json',
                success: function(response) {
                    $("#btn_edit").prop("disabled", false);
                    if (response.status == "1") { swal.fire({icon: "success", title: 'Berhasil', text: response.msg, showConfirmButton: true, timer: 900}).then((result) => { if (result.isConfirmed) { location.reload(); } }); setTimeout(function() { location.reload(); }, 900); }
                    else { swal.fire({icon: "error", title: 'Gagal !', text: response.msg, showConfirmButton: true, timer: 900}); }
                },
                error: function(response) { $("#btn_edit").prop("disabled", false); errorAjaxResponse(response); }
            });
            e.preventDefault();
        });

        function deleteData(id) {
            swal.fire({title: 'Apakah anda yakin??', text: "Anda tidak dapat mengembalikan ini !!", icon: "warning", showCancelButton: true, confirmButtonText: 'Hapus!', cancelButtonText: 'Batal', confirmButtonClass: 'btn btn-danger mr-3', cancelButtonClass: 'btn btn-secondary', buttonsStyling: false}).then(function(result) {
                if (result.value) {
                    $.ajax({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, type: "POST", url: "{{ url('task/delete-file') }}", data: 'id=' + id,
                        success: function(response) { if (response.status == "1") { swal.fire({icon: "success", title: 'Berhasil', text: response.msg, showConfirmButton: true, timer: 900}).then((result) => { if (result.isConfirmed) { location.reload(); } }); setTimeout(function() { location.reload(); }, 900); } else { swal.fire("Error!", response.msg, "error"); } },
                        error: function(response) { errorAjaxResponse(response); }
                    });
                }
            });
        }
    </script>
@endpush
