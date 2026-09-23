@extends('area._base')

@push('head')
@endpush

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2 class="page-title">Request Task</h2>
            <p class="page-subtitle">Tinjau request dari client, konfirmasi kebutuhan, dan assign ke worker.</p>
        </div>
        <div class="page-header-right">
            <a href="{{ url('request-task') }}" target="_blank" class="btn btn-primary">
                <i data-feather="external-link" style="width: 16px; height: 16px;"></i> Buka Form Client
            </a>
        </div>
    </div>

    <div class="card" style="border-radius: var(--radius-xl);">
        <div class="filter-bar" style="border-radius: var(--radius-xl) var(--radius-xl) 0 0;">
            <div class="form-group" style="min-width: 220px;">
                <label for="search_filter">Cari Request</label>
                <input type="text" id="search_filter" class="form-control" placeholder="Kode, client, atau WhatsApp...">
            </div>
            <div class="form-group" style="min-width: 190px;">
                <label for="status_filter">Status Request</label>
                <select id="status_filter" class="form-control">
                    <option value="pending">Menunggu diproses</option>
                    <option value="assigned">Sudah di-assign</option>
                </select>
            </div>
        </div>

        <div class="table-responsive" style="border-radius: 0 0 var(--radius-xl) var(--radius-xl);">
            <table class="table" id="task-request-table" style="width: 100%;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Kode Request</th>
                        <th>Client</th>
                        <th>WhatsApp</th>
                        <th>Kategori</th>
                        <th>Dibuat</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@section('modal')
    <div class="modal fade" id="request-detail-modal" role="dialog" aria-labelledby="request-detail-title" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="width: 40px; height: 40px; border-radius: var(--radius-md); background: rgba(var(--accent-rgb), 0.1); display: flex; align-items: center; justify-content: center; color: var(--accent-primary);">
                            <i data-feather="inbox" style="width: 20px; height: 20px;"></i>
                        </div>
                        <div>
                            <h5 class="modal-title" id="request-detail-title" style="margin: 0;">Detail Request</h5>
                            <p id="detail-category" style="margin: 0; font-size: 0.75rem; color: var(--text-secondary);">Memuat kategori...</p>
                        </div>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span class="modal-close-mark" aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="detail-content">
                        <div class="text-center p-4">Memuat detail...</div>
                    </div>

                    <div class="detail-info-card mt-3 mb-0">
                        <div class="detail-info-header">
                            <i data-feather="user-check"></i>
                            <h4 id="assignment-section-title">Assign ke Worker</h4>
                        </div>
                        <div class="detail-info-body">
                            <form id="assign-form">
                                <input type="hidden" id="assign-id">
                                <div class="row">
                                    <div class="col-md-4"><div class="form-group"><label for="worker_id">Worker <span class="text-danger">*</span></label><select id="worker_id" name="worker_id" class="form-control" required><option value="">Pilih worker</option>@foreach($workers as $worker)<option value="{{ $worker->id }}">{{ $worker->fullname }}</option>@endforeach</select></div></div>
                                    <div class="col-md-4"><div class="form-group"><label for="price_order">Price Order</label><input id="price_order" name="price_order" type="number" min="0" class="form-control" placeholder="Opsional"></div></div>
                                    <div class="col-md-4"><div class="form-group"><label for="pay_worker">Pay to Worker</label><input id="pay_worker" name="pay_worker" type="number" min="0" class="form-control" placeholder="Opsional"></div></div>
                                </div>
                                <div id="assign-title-wrap" class="form-group d-none"><label for="assign_judul">Judul penelitian <span class="text-danger">*</span></label><textarea id="assign_judul" name="judul" class="form-control" rows="2" placeholder="Isi judul setelah dikonfirmasi dengan client"></textarea><small class="form-text text-muted">Judul Metopen wajib dilengkapi sebelum request di-assign ke worker.</small></div>
                                <div id="assigned-task-meta" class="row d-none">
                                    <div class="col-md-4"><div class="form-group"><label for="assigned_task_code">Kode Task</label><input id="assigned_task_code" class="form-control" readonly></div></div>
                                    <div class="col-md-4"><div class="form-group"><label for="assigned_task_status">Task Status</label><input id="assigned_task_status" class="form-control" readonly></div></div>
                                    <div class="col-md-4"><div class="form-group"><label for="assigned_pay_status">Pay Status</label><input id="assigned_pay_status" class="form-control" readonly></div></div>
                                    <div class="col-md-12"><div class="form-group mb-0"><label for="assigned_at">Assigned At</label><input id="assigned_at" class="form-control" readonly></div></div>
                                </div>
                                <button type="submit" class="btn btn-primary" id="assign-button"><i data-feather="user-check" style="width: 16px; height: 16px;"></i> Assign ke Worker</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(function() {
            $('#status_filter').select2({width: '100%'});
            $('#worker_id').select2({width: '100%', dropdownParent: $('#request-detail-modal')});

            var requestTable = $('#task-request-table').DataTable({
                dom: "<'dt--top-section'>" + "<''tr>" + "<'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--length-info d-flex justify-content-center align-middle 'l<'dt--pages-count ml-1'i>><'dt--pagination mt-sm-0 mt-3'p>>",
                oLanguage: {sLengthMenu: '_MENU_'}, processing: true, serverSide: true,
                ajax: {url: '{{ url('task-request/list') }}', data: function(data) { data.status = $('#status_filter').val(); }},
                columns: [
                    {data: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'kode_request', name: 'kode_request'},
                    {data: 'customer', orderable: false, searchable: false},
                    {data: 'handphone', orderable: false, searchable: false},
                    {data: 'category_name', orderable: false, searchable: false},
                    {data: 'created_at', name: 'created_at'},
                    {data: null, orderable: false, searchable: false, render: function(data) { return '<div class="text-nowrap"><a href="javascript:;" class="btn btn-icon btn-sm btn-info js-detail" data-id="' + data.id + '" title="Detail"><i data-feather="file-text" style="width: 16px; height: 16px;"></i></a></div>'; }}
                ],
                order: [], drawCallback: function() { feather.replace(); }
            });

            $('#search_filter').on('keyup', function() { requestTable.search(this.value).draw(); });
            $('#status_filter').on('change', function() { requestTable.ajax.reload(); });

            $('#task-request-table').on('click', '.js-detail', function() {
                var id = $(this).data('id');
                $('#assign-id').val(id); $('#detail-category').text('Memuat kategori...'); $('#detail-content').html('<div class="text-center p-4">Memuat detail...</div>');
                $('#assign-form')[0].reset(); $('#worker_id').val('').prop('disabled', false).trigger('change'); $('#price_order, #pay_worker').prop('readonly', false); $('#assign-title-wrap').addClass('d-none'); $('#assign_judul').prop('required', false).prop('disabled', false); $('#assigned-task-meta').addClass('d-none'); $('#assign-button').show(); $('#assignment-section-title').text('Assign ke Worker'); $('#request-detail-modal').modal('show');

                $.get('{{ url('task-request/detail') }}/' + id, function(response) {
                    var draft = response.draft, client = draft.client || {}, category = draft.category || {}, whatsapp = (client.handphone || '').replace(/[^0-9]/g, '');
                    var needsMetopenTitle = category.tipe === 'metopen', assigned = draft.status === 'assigned', assignedTask = draft.task || {}, academic = assignedTask.academic || {};
                    $('#assign-title-wrap').toggleClass('d-none', !needsMetopenTitle); $('#assign_judul').val(academic.judul || draft.judul || '').prop('required', needsMetopenTitle).prop('disabled', assigned);
                    if (assigned) {
                        $('#assignment-section-title').text('Data Assignment'); $('#assign-button').hide(); $('#worker_id').val(assignedTask.worker_id || '').trigger('change').prop('disabled', true); $('#price_order').val(assignedTask.price_order ?? 0).prop('readonly', true); $('#pay_worker').val(assignedTask.pay_worker ?? 0).prop('readonly', true); $('#assigned_task_code').val(assignedTask.kode_task || '-'); $('#assigned_task_status').val(assignedTask.task_status || '-'); $('#assigned_pay_status').val(assignedTask.pay_status || '-'); $('#assigned_at').val(draft.assigned_at || '-'); $('#assigned-task-meta').removeClass('d-none');
                    }
                    $('#detail-category').text(category.nama || 'Kategori tidak tersedia');
                    $('#detail-content').html(
                        '<div class="detail-info-card mb-0"><div class="detail-info-header"><i data-feather="user"></i><h4>Data Client</h4></div><div class="detail-info-body"><div class="info-list">' +
                        '<div class="info-row"><span class="info-label">Nama</span><span class="info-value">' + escapeHtml(client.customer || '-') + '</span></div>' +
                        '<div class="info-row"><span class="info-label">WhatsApp</span><span class="info-value">' + escapeHtml(client.handphone || '-') + ' <a href="https://wa.me/' + whatsapp + '" target="_blank" class="btn btn-sm btn-success ml-2"><i data-feather="message-circle" style="width: 14px; height: 14px;"></i> Chat WhatsApp</a></span></div>' +
                        '<div class="info-row"><span class="info-label">Kode Request</span><span class="info-value">' + escapeHtml(draft.kode_request || '-') + '</span></div></div></div></div>' +
                        '<div class="detail-info-card mt-3 mb-0"><div class="detail-info-header"><i data-feather="clipboard"></i><h4>Informasi Request</h4></div><div class="detail-info-body"><div class="info-list">' +
                        '<div class="info-row"><span class="info-label">Kategori</span><span class="info-value">' + escapeHtml(category.nama || '-') + '</span></div>' +
                        '<div class="info-row"><span class="info-label">Tanggal Mulai</span><span class="info-value">' + escapeHtml(draft.order || '-') + '</span></div>' +
                        '<div class="info-row"><span class="info-label">Deadline</span><span class="info-value">' + escapeHtml(draft.deadline || '-') + '</span></div>' +
                        '<div class="info-row"><span class="info-label">Deskripsi</span><span class="info-value">' + escapeHtml(draft.task || '-') + '</span></div>' +
                        (draft.prodi ? '<div class="info-row"><span class="info-label">Program Studi</span><span class="info-value">' + escapeHtml(draft.prodi) + '</span></div>' : '') +
                        (draft.judul ? '<div class="info-row"><span class="info-label">Judul</span><span class="info-value">' + escapeHtml(draft.judul) + '</span></div>' : (category.tipe === 'metopen' ? '<div class="info-row"><span class="info-label">Judul</span><span class="info-value text-warning">Belum ditentukan — isi sebelum assign</span></div>' : '')) +
                        (draft.keterangan ? '<div class="info-row"><span class="info-label">Keterangan</span><span class="info-value">' + escapeHtml(draft.keterangan) + '</span></div>' : '') +
                        '</div></div></div>'
                    );
                    feather.replace();
                });
            });

            function escapeHtml(value) { return $('<div>').text(value || '').html(); }

            $('#assign-form').on('submit', function(event) {
                event.preventDefault();
                var button = $('#assign-button'); button.prop('disabled', true).html('<i data-feather="loader" style="width: 16px; height: 16px;"></i> Memproses...'); feather.replace();
                $.ajax({
                    url: '{{ url('task-request') }}/' + $('#assign-id').val() + '/assign', method: 'POST',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, data: $(this).serialize(),
                    success: function(response) { $('#request-detail-modal').modal('hide'); requestTable.ajax.reload(null, false); Swal.fire({icon: 'success', title: 'Berhasil', text: response.msg, timer: 1400, showConfirmButton: false}); },
                    error: function(xhr) { var message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Request gagal di-assign.'; Swal.fire({icon: 'error', title: 'Gagal', text: message}); },
                    complete: function() { button.prop('disabled', false).html('<i data-feather="user-check" style="width: 16px; height: 16px;"></i> Assign ke Worker'); feather.replace(); }
                });
            });
        });
    </script>
@endpush
