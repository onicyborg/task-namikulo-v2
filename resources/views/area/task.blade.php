@extends('area._base')
@push('head')
@endpush
@section('content')
    <!-- Page Header -->
    <div class="mb-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap" style="gap: 1rem;">
            <div>
                        <h2 style="font-size: 1.5rem; font-weight: 800; color: var(--text-primary); margin: 0; line-height: 1.2;">{{ $taskType === 'general' ? 'Data Tasking' : ($taskType === 'metopen' ? 'Data Task Metopen' : 'Data Artikel Ilmiah') }}</h2>
                        <p style="font-size: 0.875rem; color: var(--text-secondary); margin: 0.25rem 0 0;">{{ $taskType === 'general' ? 'Kelola semua task dan pekerja dengan mudah' : 'Kelola task dan informasi akademik dengan mudah' }}</p>
            </div>
            <div class="card-header-action">
                @if (Auth::user()->role == 'Admin' && $taskType === 'general')
                    <button class="btn btn-primary" data-toggle="modal" data-target="#modal_add">
                        <i data-feather="plus" style="width: 16px; height: 16px;"></i> Tambah
                    </button>
                @endif
                <button type="button" onclick="exportTask()" class="btn btn-success">
                    <i data-feather="download" style="width: 16px; height: 16px;"></i> Export Excel
                </button>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="card" style="border-radius: var(--radius-xl);">
        <div class="filter-bar" style="border-radius: var(--radius-xl) var(--radius-xl) 0 0;">
            <!-- Search -->
            <div class="form-group" style="flex: 1; min-width: 180px;">
                <label>Cari</label>
                <input type="text" id="search_filter" class="form-control" placeholder="Search task, customer, worker..." />
            </div>
            <!-- Tanggal Mulai -->
            <div class="form-group" style="min-width: 160px;">
                <label>Tanggal Mulai</label>
                <input type="date" id="tanggal_mulai_filter" class="form-control" onchange="reloadDataTable()" />
            </div>
            <!-- Tanggal Akhir -->
            <div class="form-group" style="min-width: 160px;">
                <label>Tanggal Akhir</label>
                <input type="date" id="tanggal_akhir_filter" class="form-control" onchange="reloadDataTable()" />
            </div>
            <!-- Task Status -->
            <div class="form-group" style="min-width: 160px;">
                <label>Task Status</label>
                <select id="task_status_filter" class="form-control" onchange="reloadDataTable()">
                    <option value="">Seluruh Data</option>
                    <option value="Waiting">Waiting</option>
                    <option value="Progress">Progress</option>
                    <option value="Done">Done</option>
                </select>
            </div>
            <!-- Pay Status -->
            <div class="form-group" style="min-width: 160px;">
                <label>Pay Status</label>
                <select id="pay_status_filter" class="form-control" onchange="reloadDataTable()">
                    <option value="">Seluruh Data</option>
                    <option value="Paid">Paid</option>
                    <option value="Hold">Hold</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <div class="table-responsive" style="border-radius: 0 0 var(--radius-xl) var(--radius-xl);">
            <table class="table" id="table-1" style="width: 100%;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Kode</th>
                        <th>Customer</th>
                        @if (Auth::user()->role == 'Admin')<th>Worker</th>@endif
                        @if ($taskType !== 'general')<th>Prodi</th><th>Judul</th><th>Progress</th>@endif
                        <th>Deskripsi</th>
                        <th>Tanggal Order</th>
                        <th>Deadline</th>
                        @if (Auth::user()->role == 'Admin')
                            <th>Price Order</th>
                            <th>Pay to Worker</th>
                            <th>Margin</th>
                        @else
                            <th>Pay to Worker</th>
                        @endif
                        <th>Task Status</th>
                        <th>Pay Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection
@section('modal')
    <!-- Modal Add -->
    <div class="modal fade" id="modal_add" role="dialog" aria-labelledby="formModalAdd" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="width: 40px; height: 40px; border-radius: var(--radius-md); background: rgba(var(--accent-rgb), 0.1); display: flex; align-items: center; justify-content: center; color: var(--accent-primary);">
                            <i data-feather="plus-circle" style="width: 20px; height: 20px;"></i>
                        </div>
                        <div>
                            <h5 class="modal-title" id="formModalAdd" style="margin: 0;">Tambah Task</h5>
                            <p style="margin: 0; font-size: 0.75rem; color: var(--text-secondary);">Isi form di bawah untuk menambah task baru</p>
                        </div>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span class="modal-close-mark" aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form_add">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Customer</label>
                                    <select name="client_id" id="client_id_add" class="form-control"><option></option></select>
                                    <div id="error_client_id_add" class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Kategori <span class="text-danger">*</span></label>
                                    <select name="category_id" id="category_id_add" class="form-control" required>
                                        <option value="">Pilih kategori</option>
                                        @foreach ($categories as $category)<option value="{{ $category->id }}" data-type="{{ $category->tipe }}">{{ $category->nama }}</option>@endforeach
                                    </select>
                                    <div id="error_category_id_add" class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Worker</label>
                                    <select name="worker_id" id="worker_id_add" class="form-control"><option></option></select>
                                    <div id="error_worker_id_add" class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Task Description</label>
                            <textarea name="task" id="task_add" class="form-control" rows="3"></textarea>
                            <div id="error_task_add" class="invalid-feedback"></div>
                        </div>
                        <div class="academic-fields academic-fields-add d-none">
                            <div class="row">
                                <div class="col-lg-6"><div class="form-group"><label>Program Studi <span class="text-danger">*</span></label><input name="prodi" id="prodi_add" class="form-control"><div id="error_prodi_add" class="invalid-feedback"></div></div></div>
                                <div class="col-lg-6 academic-continuation-add"><div class="form-group"><label class="d-block">Lanjutan dari Metopen</label><label class="custom-switch mt-2"><input type="checkbox" name="is_lanjutan_metopen" value="1" class="custom-switch-input"><span class="custom-switch-indicator"></span><span class="custom-switch-description">Ya</span></label></div></div>
                            </div>
                            <div class="form-group"><label>Judul <span class="text-danger">*</span></label><textarea name="judul" id="judul_add" class="form-control" rows="2"></textarea><div id="error_judul_add" class="invalid-feedback"></div></div>
                            <div class="form-group"><label>Keterangan</label><textarea name="keterangan" id="keterangan_add" class="form-control" rows="2"></textarea></div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Tanggal Order</label>
                                    <input type="date" name="order" id="order_add" class="form-control">
                                    <div id="error_order_add" class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Deadline</label>
                                    <input type="date" name="deadline" id="deadline_add" class="form-control">
                                    <div id="error_deadline_add" class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Price Order</label>
                                    <input type="number" name="price_order" id="price_order_add" class="form-control">
                                    <div id="error_price_order_add" class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Pay to Worker</label>
                                    <input type="number" name="pay_worker" id="pay_worker_add" class="form-control">
                                    <div id="error_pay_worker_add" class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Task Status</label>
                                    <select name="task_status" id="task_status_add" class="form-control">
                                        <option></option>
                                        <option value="Waiting">Waiting</option>
                                        <option value="Progress">Progress</option>
                                        <option value="Done">Done</option>
                                    </select>
                                    <div id="error_task_status_add" class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Pay Status</label>
                                    <select name="pay_status" id="pay_status_add" class="form-control">
                                        <option></option>
                                        <option value="Paid">Paid</option>
                                        <option value="Hold">Hold</option>
                                    </select>
                                    <div id="error_pay_status_add" class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" id="btn_add" class="btn btn-primary">
                        <i data-feather="save" style="width: 16px; height: 16px;"></i> Save
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal fade" id="modal_edit" role="dialog" aria-labelledby="formModalEdit" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="width: 40px; height: 40px; border-radius: var(--radius-md); background: rgba(245, 158, 11, 0.1); display: flex; align-items: center; justify-content: center; color: #f59e0b;">
                            <i data-feather="edit-2" style="width: 20px; height: 20px;"></i>
                        </div>
                        <div>
                            <h5 class="modal-title" id="formModalEdit" style="margin: 0;">Edit Task</h5>
                            <p style="margin: 0; font-size: 0.75rem; color: var(--text-secondary);">Perbarui informasi task di bawah</p>
                        </div>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span class="modal-close-mark" aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form_edit">
                        <input type="hidden" name="id" id="id_edit">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Customer</label>
                                    <select name="client_id" id="client_id_edit" class="form-control"></select>
                                    <div id="error_client_id_edit" class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Worker</label>
                                    <select name="worker_id" id="worker_id_edit" class="form-control"></select>
                                    <div id="error_worker_id_edit" class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Kategori <span class="text-danger">*</span></label>
                                    <select name="category_id" id="category_id_edit" class="form-control" required>
                                        <option value="">Pilih kategori</option>
                                        @foreach ($categories as $category)<option value="{{ $category->id }}" data-type="{{ $category->tipe }}">{{ $category->nama }}</option>@endforeach
                                    </select>
                                    <div id="error_category_id_edit" class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Task Description</label>
                            <textarea name="task" id="task_edit" class="form-control" rows="3"></textarea>
                            <div id="error_task_edit" class="invalid-feedback"></div>
                        </div>
                        <div class="academic-fields academic-fields-edit d-none">
                            <div class="row">
                                <div class="col-lg-6"><div class="form-group"><label>Program Studi <span class="text-danger">*</span></label><input name="prodi" id="prodi_edit" class="form-control"><div id="error_prodi_edit" class="invalid-feedback"></div></div></div>
                                <div class="col-lg-6 academic-continuation-edit"><div class="form-group"><label class="d-block">Lanjutan dari Metopen</label><label class="custom-switch mt-2"><input type="checkbox" name="is_lanjutan_metopen" value="1" class="custom-switch-input"><span class="custom-switch-indicator"></span><span class="custom-switch-description">Ya</span></label></div></div>
                            </div>
                            <div class="form-group"><label>Judul <span class="text-danger">*</span></label><textarea name="judul" id="judul_edit" class="form-control" rows="2"></textarea><div id="error_judul_edit" class="invalid-feedback"></div></div>
                            <div class="form-group"><label>Keterangan</label><textarea name="keterangan" id="keterangan_edit" class="form-control" rows="2"></textarea></div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Tanggal Order</label>
                                    <input type="date" name="order" id="order_edit" class="form-control">
                                    <div id="error_order_edit" class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Deadline</label>
                                    <input type="date" name="deadline" id="deadline_edit" class="form-control">
                                    <div id="error_deadline_edit" class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Price Order</label>
                                    <input type="number" name="price_order" id="price_order_edit" class="form-control">
                                    <div id="error_price_order_edit" class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Pay to Worker</label>
                                    <input type="number" name="pay_worker" id="pay_worker_edit" class="form-control">
                                    <div id="error_pay_worker_edit" class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Task Status</label>
                                    <select name="task_status" id="task_status_edit" class="form-control">
                                        <option value="Waiting">Waiting</option>
                                        <option value="Progress">Progress</option>
                                        <option value="Done">Done</option>
                                    </select>
                                    <div id="error_task_status_edit" class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Pay Status</label>
                                    <select name="pay_status" id="pay_status_edit" class="form-control">
                                        <option value="Paid">Paid</option>
                                        <option value="Hold">Hold</option>
                                    </select>
                                    <div id="error_pay_status_edit" class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" id="btn_edit" class="btn btn-primary">
                        <i data-feather="save" style="width: 16px; height: 16px;"></i> Save
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Pay -->
    <div class="modal fade" id="modal_edit_pay" role="dialog" aria-labelledby="formModalPay" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="width: 40px; height: 40px; border-radius: var(--radius-md); background: rgba(6, 182, 212, 0.1); display: flex; align-items: center; justify-content: center; color: #06b6d4;">
                            <i data-feather="dollar-sign" style="width: 20px; height: 20px;"></i>
                        </div>
                        <div>
                            <h5 class="modal-title" id="formModalPay" style="margin: 0;">Pay</h5>
                            <p style="margin: 0; font-size: 0.75rem; color: var(--text-secondary);">Ajukan pembayaran untuk worker</p>
                        </div>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span class="modal-close-mark" aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form_edit_pay">
                        <input type="hidden" name="id" id="id_edit_pay">
                        <input type="hidden" name="price_order" id="price_order_edit_pay">
                        <div class="form-group">
                            <label>Task Description</label>
                            <textarea readonly id="task_edit_pay" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Pay</label>
                            <input type="number" name="pay_worker" id="pay_worker_edit_pay" class="form-control">
                            <div id="error_pay_worker_edit_pay" class="invalid-feedback"></div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" id="btn_edit_pay" class="btn btn-primary">
                        <i data-feather="send" style="width: 16px; height: 16px;"></i> Submit Pengajuan
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Status -->
    <div class="modal fade" id="modal_edit_status" role="dialog" aria-labelledby="formModalStatus" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="width: 40px; height: 40px; border-radius: var(--radius-md); background: rgba(16, 185, 129, 0.1); display: flex; align-items: center; justify-content: center; color: #10b981;">
                            <i data-feather="check-square" style="width: 20px; height: 20px;"></i>
                        </div>
                        <div>
                            <h5 class="modal-title" id="formModalStatus" style="margin: 0;">Update Status</h5>
                            <p style="margin: 0; font-size: 0.75rem; color: var(--text-secondary);">Ubah status task</p>
                        </div>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span class="modal-close-mark" aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form_edit_status">
                        <input type="hidden" name="id" id="id_edit_status">
                        <div class="form-group">
                            <label>Task Status</label>
                            <select name="task_status" id="task_status_edit_status" class="form-control">
                                <option value="Progress">Progress</option>
                                <option value="Done">Done</option>
                            </select>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" id="btn_edit_status" class="btn btn-primary">
                        <i data-feather="save" style="width: 16px; height: 16px;"></i> Save
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>
        $(document).ready(function() {
            $('#pay_status_filter, #task_status_filter').select2({width: '100%'});
            $('#task_status_add, #pay_status_add').select2({width: '100%', placeholder: 'Pilih', dropdownParent: $('#modal_add')});
            $('#task_status_edit_status').select2({width: '100%', placeholder: 'Pilih', dropdownParent: $('#modal_edit_status')});
            $('#task_status_edit, #pay_status_edit').select2({width: '100%', placeholder: 'Pilih', dropdownParent: $('#modal_edit')});
            $('#category_id_add').select2({width: '100%', placeholder: 'Pilih kategori', dropdownParent: $('#modal_add')});
            $('#category_id_edit').select2({width: '100%', placeholder: 'Pilih kategori', dropdownParent: $('#modal_edit')});
            $('#client_id_add').select2({width: '100%', placeholder: 'Pilih', ajax: {url: '{{ url('client/search') }}', dataType: 'json', delay: 250, processResults: function(d) { return { results: d }; }, cache: true}, dropdownParent: $('#modal_add')});
            $('#worker_id_add').select2({width: '100%', placeholder: 'Pilih', ajax: {url: '{{ url('worker/search') }}', dataType: 'json', delay: 250, processResults: function(d) { return { results: d }; }, cache: true}, dropdownParent: $('#modal_add')});
            $('#client_id_edit').select2({width: '100%', ajax: {url: '{{ url('client/search') }}', dataType: 'json', delay: 250, processResults: function(d) { return { results: d }; }, cache: true}, dropdownParent: $('#modal_edit')});
            $('#worker_id_edit').select2({width: '100%', ajax: {url: '{{ url('worker/search') }}', dataType: 'json', delay: 250, processResults: function(d) { return { results: d }; }, cache: true}, dropdownParent: $('#modal_edit')});
            var orderAdd = document.getElementById("order_add"), deadlineAdd = document.getElementById("deadline_add");
            if(orderAdd && deadlineAdd){ deadlineAdd.min = orderAdd.value; orderAdd.addEventListener("change", function(){ deadlineAdd.min = orderAdd.value; }); }

            $('#category_id_add, #category_id_edit').on('change', function () { toggleAcademicFields(this.id.indexOf('_add') > -1 ? 'add' : 'edit'); });
            toggleAcademicFields('add');
            toggleAcademicFields('edit');

            // Initialize feather icons for modals
            feather.replace();
        });

        var taskType = @json($taskType);
        var url_datatable = '{{ url('task/list') }}?tipe=' + encodeURIComponent(taskType) + '&tanggal_mulai_filter=' + $("#tanggal_mulai_filter").val() + '&tanggal_akhir_filter=' + $("#tanggal_akhir_filter").val() + '&task_status_filter=' + $("#task_status_filter").val() + '&pay_status_filter=' + $("#pay_status_filter").val();
        @if (Auth::user()->role == 'Worker') url_datatable += '&worker_id_filter={{ Auth::user()->id }}'; @endif

        var datatable = $("#table-1").DataTable({
            "dom": "<'dt--top-section'>" + "<''tr>" + "<'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--length-info d-flex justify-content-center align-middle 'l<'dt--pages-count ml-1'i>><'dt--pagination mt-sm-0 mt-3'p>>",
            "oLanguage": {"sLengthMenu": "_MENU_"},
            processing: true, serverSide: true, ajax: {url: url_datatable}, scrollX: true,
            @if (Auth::user()->role == 'Admin') fixedColumns: {leftColumns: 4}, @endif
            columns: [
                {"data": "DT_RowIndex", "name": "DT_RowIndex", "orderable": false, "searchable": false},
                {data: 'kode_task', name: 'kode_task'},
                {data: 'customer', name: 'client.customer'},
                @if (Auth::user()->role == 'Admin') {data: 'fullname', name: 'users.fullname'}, @endif
                @if ($taskType !== 'general')
                    {data: 'prodi', name: 'task_academic.prodi', defaultContent: '-'},
                    {data: 'judul', name: 'task_academic.judul', defaultContent: '-', render: function(d) { return d ? $('<div>').text(d).html() : '-'; }},
                    {data: null, orderable: false, searchable: false, render: function(d) { var n = [d.tugas_1,d.tugas_2,d.tugas_3,d.tugas_4].filter(Boolean).length; return '<span class="badge badge-primary">' + n + '/4</span>'; }},
                @endif
                {data: 'task', name: 'task'},
                {"searchable": false, data: 'order', name: 'order'},
                {"searchable": false, data: 'deadline', name: 'deadline'},
                @if (Auth::user()->role == 'Admin')
                    {data: 'price_order', name: 'price_order', render: function(d) { return idrFormat(d, true); }},
                    {data: 'pay_worker', name: 'pay_worker', render: function(d) { return idrFormat(d, true); }},
                    {data: 'margin', name: 'margin', render: function(d) { return idrFormat(d, true); }},
                @else {data: 'pay_worker', name: 'pay_worker', render: function(d) { return idrFormat(d, true); }}, @endif
                {data: 'task_status', name: 'task_status', render: function(d) {
                    if (d == 'Waiting') return '<span class="badge badge-warning">' + d + '</span>';
                    else if (d == 'Progress') return '<span class="badge badge-primary">' + d + '</span>';
                    else return '<span class="badge badge-success">' + d + '</span>';
                }},
                {data: 'pay_status', name: 'pay_status', render: function(d) {
                    return d == 'Paid' ? '<span class="badge badge-success">' + d + '</span>' : '<span class="badge badge-danger">' + d + '</span>';
                }},
                {"orderable": false, "searchable": false, "data": null, "render": function(data, type, row) {
                    var url_detail = '{{ asset('task/detail') }}/' + row.id;
                    @if (Auth::user()->role == 'Admin')
                        var html = '<div class="text-nowrap">';
                        html += '<a href="' + url_detail + '" class="btn btn-icon btn-sm btn-info mr-1" title="Detail"><i data-feather="file-text" style="width: 16px; height: 16px;"></i></a>';
                        html += '<a href="javascript:;" onclick="editData(\'' + row.id + '\')" class="btn btn-icon btn-sm btn-warning mr-1" title="Edit"><i data-feather="edit" style="width: 16px; height: 16px;"></i></a>';
                        html += '<a href="javascript:;" onclick="deleteData(\'' + row.id + '\')" class="btn btn-icon btn-sm btn-danger" title="Delete"><i data-feather="trash-2" style="width: 16px; height: 16px;"></i></a>';
                        html += '</div>';
                    @else
                        var html = '<div class="text-nowrap">';
                        if (row.task_status == 'Waiting') html += '<a href="javascript:;" onclick="editPay(\'' + row.id + '\')" class="btn btn-icon btn-sm btn-info mr-1" title="Bayar"><i data-feather="credit-card" style="width: 16px; height: 16px;"></i></a>';
                        html += '<a href="' + url_detail + '" class="btn btn-icon btn-sm btn-warning mr-1" title="Detail"><i data-feather="file-text" style="width: 16px; height: 16px;"></i></a>';
                        html += '<a href="javascript:;" onclick="editStatus(\'' + row.id + '\',\'' + row.task_status + '\')" class="btn btn-icon btn-sm btn-success" title="Update Status"><i data-feather="check-circle" style="width: 16px; height: 16px;"></i></a>';
                        html += '</div>';
                    @endif
                    return html;
                }}
            ],
            order: [],
            "drawCallback": function(settings) {
                feather.replace();
            }
        });

        // FixedColumns renders the first columns in a cloned table. Keep the
        // row hover state synchronized between the clone and the main table.
        $(document).off('mouseenter.taskFixedRowHover mouseleave.taskFixedRowHover', '.dataTables_scrollBody table tbody tr, .DTFC_LeftBodyWrapper table tbody tr');
        $(document).on('mouseenter.taskFixedRowHover', '.dataTables_scrollBody table tbody tr, .DTFC_LeftBodyWrapper table tbody tr', function() {
            var rowIndex = $(this).index();
            var wrapper = $(this).closest('.dataTables_wrapper');
            wrapper.find('table tbody tr').removeClass('task-row-hover').each(function() {
                if ($(this).index() === rowIndex) $(this).addClass('task-row-hover');
            });
        });
        $(document).on('mouseleave.taskFixedRowHover', '.dataTables_scrollBody table tbody tr, .DTFC_LeftBodyWrapper table tbody tr', function() {
            $(this).closest('.dataTables_wrapper').find('table tbody tr').removeClass('task-row-hover');
        });

        $("#search_filter").on("keyup", function() { datatable.search($(this).val()).draw(); });

        function reloadDataTable() {
            var newUrl = '{{ url('task/list') }}?tipe=' + encodeURIComponent(taskType) + '&tanggal_mulai_filter=' + $("#tanggal_mulai_filter").val() + '&tanggal_akhir_filter=' + $("#tanggal_akhir_filter").val() + '&task_status_filter=' + $("#task_status_filter").val() + '&pay_status_filter=' + $("#pay_status_filter").val();
            @if (Auth::user()->role == 'Worker') newUrl += '&worker_id_filter={{ Auth::user()->id }}'; @endif
            datatable.ajax.url(newUrl).load();
        }

        function exportTask() {
            window.location.href = "{{ url('task/export') }}?search_filter=" + $("#search_filter").val() + '&tanggal_mulai_filter=' + $("#tanggal_mulai_filter").val() + '&tanggal_akhir_filter=' + $("#tanggal_akhir_filter").val() + '&task_status_filter=' + $("#task_status_filter").val() + '&pay_status_filter=' + $("#pay_status_filter").val();
        }

        function toggleAcademicFields(mode) {
            var value = $('#category_id_' + mode + ' option:selected').data('type');
            var academic = value === 'metopen' || value === 'artikel_ilmiah';
            $('.academic-fields-' + mode).toggleClass('d-none', !academic);
            $('.academic-continuation-' + mode).toggleClass('d-none', value !== 'artikel_ilmiah');
            $('#prodi_' + mode + ', #judul_' + mode).prop('required', academic);
        }

        $("#form_add").submit(function(e) {
            $("#btn_add").prop("disabled", true);
            var formdata = new FormData(this);
            $.ajax({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, cache: false, contentType: false, processData: false, method: 'post', url: "{{ url('task/add') }}", data: formdata, dataType: 'json',
                success: function(response) {
                    $("#btn_add").prop("disabled", false);
                    if (response.status == "1") {
                        swal.fire({icon: "success", title: 'Berhasil', text: response.msg, showConfirmButton: true, timer: 900}).then((result) => { if (result.isConfirmed) { $('#form_add')[0].reset(); $('#client_id_add').val('').change(); $('#worker_id_add').val('').change(); $('#task_status_add').val('').change(); $('#pay_status_add').val('').change(); $('#modal_add').modal('hide'); reloadDataTable(); } });
                        setTimeout(function() { $('#form_add')[0].reset(); $('#client_id_add').val('').change(); $('#worker_id_add').val('').change(); $('#task_status_add').val('').change(); $('#pay_status_add').val('').change(); $('#modal_add').modal('hide'); reloadDataTable(); }, 900);
                    } else { swal.fire({icon: "error", title: 'Gagal !', text: response.msg, showConfirmButton: true, timer: 900}); }
                },
                error: function(response) { $("#btn_add").prop("disabled", false); if (response.status == 422) { let err = JSON.parse(response.responseText); if (err.errors) { if (err.errors.client_id) { $("#client_id_add").addClass('is-invalid'); $("#error_client_id_add").html(err.errors.client_id[0]); } if (err.errors.worker_id) { $("#worker_id_add").addClass('is-invalid'); $("#error_worker_id_add").html(err.errors.worker_id[0]); } if (err.errors.task) { $("#task_add").addClass('is-invalid'); $("#error_task_add").html(err.errors.task[0]); } } } else { errorAjaxResponse(response); } }
            });
            e.preventDefault();
        });

        function editData(id) {
            $.ajax({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, type: "GET", url: "{{ url('task/detail') }}/" + id, dataType: 'json',
                success: function(response) {
                    if (response.status == 1) {
                        var data = response.task;
                        $("#id_edit").val(data.id);
                        $("#client_id_edit").select2("trigger", "select", {data: {id: data.client_id, text: data.customer}});
                        $("#worker_id_edit").select2("trigger", "select", {data: {id: data.worker_id, text: data.worker}});
                        $("#category_id_edit").val(data.category_id).trigger('change');
                        $("#task_edit").val(data.task);
                        if (data.academic) {
                            $("#prodi_edit").val(data.academic.prodi);
                            $("#judul_edit").val(data.academic.judul);
                            $("#keterangan_edit").val(data.academic.keterangan);
                            $("#modal_edit input[name='is_lanjutan_metopen']").prop('checked', !!data.academic.is_lanjutan_metopen);
                        }
                        $("#order_edit").val(data.order);
                        $("#deadline_edit").val(data.deadline);
                        $("#price_order_edit").val(data.price_order);
                        $("#pay_worker_edit").val(data.pay_worker);
                        $("#task_status_edit").val(data.task_status).change();
                        $("#pay_status_edit").val(data.pay_status).change();
                        $("#modal_edit").modal("show");
                        feather.replace();
                    } else { Swal.fire("Oops!", response.msg, "error"); }
                },
                error: function(response) { errorAjaxResponse(response); }
            });
        }

        $("#form_edit").submit(function(e) {
            $("#btn_edit").prop("disabled", true);
            var formdata = new FormData(this);
            $.ajax({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, cache: false, contentType: false, processData: false, method: 'post', url: "{{ url('task/edit') }}", data: formdata, dataType: 'json',
                success: function(response) {
                    $("#btn_edit").prop("disabled", false);
                    if (response.status == "1") {
                        swal.fire({icon: "success", title: 'Berhasil', text: response.msg, showConfirmButton: true, timer: 900}).then((result) => { if (result.isConfirmed) { $('#form_edit')[0].reset(); $('#client_id_edit').val('').change(); $('#worker_id_edit').val('').change(); $('#modal_edit').modal('hide'); reloadDataTable(); } });
                        setTimeout(function() { $('#form_edit')[0].reset(); $('#client_id_edit').val('').change(); $('#worker_id_edit').val('').change(); $('#modal_edit').modal('hide'); reloadDataTable(); }, 900);
                    } else { swal.fire({icon: "error", title: 'Gagal !', text: response.msg, showConfirmButton: true, timer: 900}); }
                },
                error: function(response) { $("#btn_edit").prop("disabled", false); errorAjaxResponse(response); }
            });
            e.preventDefault();
        });

        function deleteData(id) {
            swal.fire({title: 'Apakah anda yakin??', text: "Anda tidak dapat mengembalikan ini !!", icon: "warning", showCancelButton: true, confirmButtonText: 'Hapus!', cancelButtonText: 'Batal', customClass: { confirmButton: 'swal2-delete-confirm', cancelButton: 'swal2-delete-cancel' }, buttonsStyling: false}).then(function(result) {
                if (result.value) {
                    $.ajax({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, type: "POST", url: "{{ url('task/delete') }}", data: 'id=' + id,
                        success: function(response) { if (response.status == "1") { swal.fire({icon: "success", title: 'Berhasil', text: response.msg, showConfirmButton: true, timer: 900}).then((result) => { if (result.isConfirmed) { reloadDataTable(); } }); setTimeout(function() { reloadDataTable(); }, 900); } else { swal.fire("Error!", response.msg, "error"); } },
                        error: function(response) { errorAjaxResponse(response); }
                    });
                }
            });
        }

        function editPay(id) {
            $.ajax({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, type: "GET", url: "{{ url('task/detail') }}/" + id, dataType: 'json',
                success: function(response) {
                    if (response.status == 1) {
                        var data = response.task;
                        $("#id_edit_pay").val(data.id);
                        $("#task_edit_pay").val(data.task);
                        $("#pay_worker_edit_pay").val(data.pay_worker);
                        $("#price_order_edit_pay").val(data.price_order);
                        $("#modal_edit_pay").modal("show");
                        feather.replace();
                    } else { Swal.fire("Oops!", response.msg, "error"); }
                },
                error: function(response) { errorAjaxResponse(response); }
            });
        }

        $("#form_edit_pay").submit(function(e) {
            $("#btn_edit_pay").prop("disabled", true);
            var formdata = new FormData(this);
            $.ajax({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, cache: false, contentType: false, processData: false, method: 'post', url: "{{ url('task/edit-pay') }}", data: formdata, dataType: 'json',
                success: function(response) {
                    $("#btn_edit_pay").prop("disabled", false);
                    if (response.status == "1") {
                        swal.fire({icon: "success", title: 'Berhasil', text: response.msg, showConfirmButton: true, timer: 900}).then((result) => { if (result.isConfirmed) { $('#form_edit_pay')[0].reset(); $('#modal_edit_pay').modal('hide'); reloadDataTable(); } });
                        setTimeout(function() { $('#form_edit_pay')[0].reset(); $('#modal_edit_pay').modal('hide'); reloadDataTable(); }, 900);
                    } else { swal.fire({icon: "error", title: 'Gagal !', text: response.msg, showConfirmButton: true, timer: 900}); }
                },
                error: function(response) { $("#btn_edit_pay").prop("disabled", false); errorAjaxResponse(response); }
            });
            e.preventDefault();
        });

        function editStatus(id, status) {
            $("#id_edit_status").val(id);
            $("#task_status_edit_status").val(status).change();
            $("#modal_edit_status").modal("show");
            feather.replace();
        }

        $("#form_edit_status").submit(function(e) {
            $("#btn_edit_status").prop("disabled", true);
            var formdata = new FormData(this);
            $.ajax({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, cache: false, contentType: false, processData: false, method: 'post', url: "{{ url('task/edit-status') }}", data: formdata, dataType: 'json',
                success: function(response) {
                    $("#btn_edit_status").prop("disabled", false);
                    if (response.status == "1") {
                        swal.fire({icon: "success", title: 'Berhasil', text: response.msg, showConfirmButton: true, timer: 900}).then((result) => { if (result.isConfirmed) { $('#form_edit_status')[0].reset(); $('#modal_edit_status').modal('hide'); reloadDataTable(); } });
                        setTimeout(function() { $('#form_edit_status')[0].reset(); $('#modal_edit_status').modal('hide'); reloadDataTable(); }, 900);
                    } else { swal.fire({icon: "error", title: 'Gagal !', text: response.msg, showConfirmButton: true, timer: 900}); }
                },
                error: function(response) { $("#btn_edit_status").prop("disabled", false); errorAjaxResponse(response); }
            });
            e.preventDefault();
        });
    </script>
@endpush
