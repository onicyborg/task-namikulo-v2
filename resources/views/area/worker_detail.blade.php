@extends('area._base')
@push('head')
@endpush
@section('content')
    <div class="client-detail-page">
    <div class="stats-grid">
        <div class="stat-card" id="card1">
            <div class="stat-card__icon">
                <i data-feather="user"></i>
            </div>
            <div class="stat-card__body">
                <p class="stat-card__name">{{ $worker->fullname }}</p>
                <p class="stat-card__meta">
                    <i data-feather="map-pin"></i>
                    <span>{{ empty($worker->asal) ? '-' : $worker->asal }}</span>
                </p>
                <p class="stat-card__meta">
                    <i data-feather="phone"></i>
                    <span>{{ empty($worker->handphone) ? '-' : $worker->handphone }}</span>
                </p>
            </div>
        </div>
        <div class="stat-card stat-card--highlight">
            <div class="stat-card__icon">
                <i data-feather="clipboard"></i>
            </div>
            <div class="stat-card__body text-center">
                <h2 class="stat-card__value">{{ rupiah($worker->total) }}</h2>
                <p class="stat-card__label">Total Order</p>
            </div>
        </div>
        <div class="stat-card stat-card--highlight">
            <div class="stat-card__icon">
                <i data-feather="dollar-sign"></i>
            </div>
            <div class="stat-card__body text-center">
                <h2 class="stat-card__value">{{ rupiah($worker->pay, true) }}</h2>
                <p class="stat-card__label">Total Pay Worker</p>
            </div>
        </div>
        <div class="stat-card stat-card--highlight">
            <div class="stat-card__icon">
                <i data-feather="trending-up"></i>
            </div>
            <div class="stat-card__body text-center">
                <h2 class="stat-card__value">{{ rupiah($worker->margin, true) }}</h2>
                <p class="stat-card__label">Total Margin</p>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="detail-card-header">
            <div class="detail-card-header-left">
                <i data-feather="list"></i>
                <h4>Data Task Worker</h4>
            </div>
            <button class="btn btn-primary d-lg-none" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                <i data-feather="filter" style="width: 16px; height: 16px;"></i> Filter
            </button>
        </div>
        <div class="detail-card-body">
            <div class="collapse d-lg-block" id="collapseExample">
                <div class="filter-bar">
                    <div class="form-group" style="flex: 1; min-width: 180px;">
                        <label>Cari</label>
                        <input type="text" id="search_filter" class="form-control" placeholder="Search..." />
                    </div>
                    <div class="form-group" style="min-width: 160px;">
                        <label>Tanggal Mulai</label>
                        <input type="date" id="tanggal_mulai_filter" class="form-control" onchange="reloadDataTable()" />
                    </div>
                    <div class="form-group" style="min-width: 160px;">
                        <label>Tanggal Akhir</label>
                        <input type="date" id="tanggal_akhir_filter" class="form-control" onchange="reloadDataTable()" />
                    </div>
                    <div class="form-group" style="min-width: 160px;">
                        <label>Task Status</label>
                        <select id="task_status_filter" class="form-control" onchange="reloadDataTable()">
                            <option value="">Seluruh Data</option>
                            <option value="Waiting">Waiting</option>
                            <option value="Progress">Progress</option>
                            <option value="Done">Done</option>
                        </select>
                    </div>
                    <div class="form-group" style="min-width: 160px;">
                        <label>Pay Status</label>
                        <select id="pay_status_filter" class="form-control" onchange="reloadDataTable()">
                            <option value="">Seluruh Data</option>
                            <option value="Paid">Paid</option>
                            <option value="Hold">Hold</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="detail-table-wrapper">
                <table class="detail-table" id="table-1" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Kode</th>
                            <th>Customer</th>
                            <th>Worker</th>
                            <th>Deskripsi</th>
                            <th>Tanggal Order</th>
                            <th>Deadline</th>
                            <th>Price Order</th>
                            <th>Pay to Worker</th>
                            <th>Margin</th>
                            <th>Task Status</th>
                            <th>Pay Status</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    </div>
@endsection
@section('modal')
@endsection
@push('js')
    <script>
        $(document).ready(function() { feather.replace(); $('#pay_status_filter').select2({width: '100%'}); $('#task_status_filter').select2({width: '100%'}); });
        var datatable = $("#table-1").DataTable({
            "dom": "<'dt--top-section'>" + "<''tr>" + "<'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--length-info d-flex justify-content-center align-middle 'l<'dt--pages-count ml-1'i>><'dt--pagination mt-sm-0 mt-3'p>>",
            "oLanguage": {"sLengthMenu": "_MENU_"},
            processing: true, serverSide: true,
            ajax: {url: '{{ url('task/list') }}' + '?tanggal_mulai_filter=' + $("#tanggal_mulai_filter").val() + '&tanggal_akhir_filter=' + $("#tanggal_akhir_filter").val() + '&task_status_filter=' + $("#task_status_filter").val() + '&pay_status_filter=' + $("#pay_status_filter").val() + '&worker_id_filter={{ $worker->id }}'},
            columns: [
                {"data": "DT_RowIndex", "name": "DT_RowIndex", "orderable": false, "searchable": false},
                {data: 'kode_task', name: 'kode_task'},
                {data: 'customer', name: 'client.customer'},
                {data: 'fullname', name: 'users.fullname'},
                {data: 'task', name: 'task'},
                {"searchable": false, data: 'order', name: 'order'},
                {"searchable": false, data: 'deadline', name: 'deadline'},
                {data: 'price_order', name: 'price_order', render: function(d) { return idrFormat(d, true); }},
                {data: 'pay_worker', name: 'pay_worker', render: function(d) { return idrFormat(d, true); }},
                {data: 'margin', name: 'margin', render: function(d) { return idrFormat(d, true); }},
                {data: 'task_status', name: 'task_status', render: function(d) { if (d == 'Waiting') return '<span class="badge badge-warning">' + d + '</span>'; else if (d == 'Progress') return '<span class="badge badge-primary">' + d + '</span>'; else return '<span class="badge badge-success">' + d + '</span>'; }},
                {data: 'pay_status', name: 'pay_status', render: function(d) { return d == 'Paid' ? '<span class="badge badge-success">' + d + '</span>' : '<span class="badge badge-danger">' + d + '</span>'; }}
            ],
            order: []
        });
        $("#search_filter").on("keyup", function() { datatable.search($(this).val()).draw(); });
        function reloadDataTable() { var newUrl = '{{ url('task/list') }}' + '?tanggal_mulai_filter=' + $("#tanggal_mulai_filter").val() + '&tanggal_akhir_filter=' + $("#tanggal_akhir_filter").val() + '&task_status_filter=' + $("#task_status_filter").val() + '&pay_status_filter=' + $("#pay_status_filter").val() + '&worker_id_filter={{ $worker->id }}'; datatable.ajax.url(newUrl).load(); }
    </script>
@endpush
