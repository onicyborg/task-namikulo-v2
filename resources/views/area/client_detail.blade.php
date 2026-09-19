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
                <p class="stat-card__name">{{ $client->customer }}</p>
                <p class="stat-card__meta">
                    <i data-feather="map-pin"></i>
                    <span>{{ empty($client->asal) ? '-' : $client->asal }}</span>
                </p>
                <p class="stat-card__meta">
                    <i data-feather="phone"></i>
                    <span>{{ empty($client->handphone) ? '-' : $client->handphone }}</span>
                </p>
            </div>
        </div>
        <div class="stat-card stat-card--highlight">
            <div class="stat-card__icon">
                <i data-feather="clipboard"></i>
            </div>
            <div class="stat-card__body text-center">
                <h2 class="stat-card__value">{{ rupiah($client->total) }}</h2>
                <p class="stat-card__label">Total Order</p>
            </div>
        </div>
        <div class="stat-card stat-card--highlight">
            <div class="stat-card__icon">
                <i data-feather="dollar-sign"></i>
            </div>
            <div class="stat-card__body text-center">
                <h2 class="stat-card__value">{{ rupiah($client->pay, true) }}</h2>
                <p class="stat-card__label">Total Harga</p>
            </div>
        </div>
        <div class="stat-card stat-card--highlight">
            <div class="stat-card__icon">
                <i data-feather="trending-up"></i>
            </div>
            <div class="stat-card__body text-center">
                <h2 class="stat-card__value">{{ rupiah($client->margin, true) }}</h2>
                <p class="stat-card__label">Total Margin</p>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="detail-card-header">
            <div class="detail-card-header-left">
                <i data-feather="list"></i>
                <h4>Data Order Client</h4>
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
        $(document).ready(function() {
            feather.replace();
            $('#pay_status_filter').select2({width: '100%'});
            $('#task_status_filter').select2({width: '100%'});
            var inputTanggalAwal = document.getElementById("tanggal_mulai_filter");
            var inputTanggalAkhir = document.getElementById("tanggal_akhir_filter");
            if (inputTanggalAwal && inputTanggalAkhir) {
                inputTanggalAkhir.min = inputTanggalAwal.value;
                inputTanggalAwal.addEventListener("change", function() {
                    var tanggalAwalBaru = new Date(inputTanggalAwal.value);
                    var tanggalAkhirLama = new Date(inputTanggalAkhir.value);
                    if (tanggalAwalBaru > tanggalAkhirLama) { inputTanggalAkhir.value = inputTanggalAwal.value; }
                    inputTanggalAkhir.min = inputTanggalAwal.value;
                });
            }
        });

        var tanggal_mulai = $("#tanggal_mulai_filter").val();
        var tanggal_akhir = $("#tanggal_akhir_filter").val();
        var task_status = $("#task_status_filter").val();
        var pay_status = $("#pay_status_filter").val();

        var datatable = $("#table-1").DataTable({
            "dom": "<'dt--top-section'>" + "<''tr>" + "<'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--length-info d-flex justify-content-center align-middle 'l<'dt--pages-count ml-1'i>><'dt--pagination mt-sm-0 mt-3'p>>",
            "oLanguage": {"sLengthMenu": "_MENU_"},
            processing: true, serverSide: true,
            ajax: {url: '{{ url('task/list') }}' + '?tanggal_mulai_filter=' + tanggal_mulai + '&tanggal_akhir_filter=' + tanggal_akhir + '&task_status_filter=' + task_status + '&pay_status_filter=' + pay_status + '&client_id_filter={{ $client->id }}'},
            columns: [
                {"data": "DT_RowIndex", "name": "DT_RowIndex", "orderable": false, "searchable": false},
                {data: 'kode_task', name: 'kode_task'},
                {data: 'customer', name: 'client.customer'},
                {data: 'fullname', name: 'users.fullname'},
                {data: 'task', name: 'task'},
                {"searchable": false, data: 'order', name: 'order'},
                {"searchable": false, data: 'deadline', name: 'deadline'},
                {data: 'price_order', name: 'price_order', render: function(data) { return idrFormat(data, true); }},
                {data: 'pay_worker', name: 'pay_worker', render: function(data) { return idrFormat(data, true); }},
                {data: 'margin', name: 'margin', render: function(data) { return idrFormat(data, true); }},
                {data: 'task_status', name: 'task_status', render: function(data) {
                    if (data == 'Waiting') return '<span class="badge badge-warning">' + data + '</span>';
                    else if (data == 'Progress') return '<span class="badge badge-primary">' + data + '</span>';
                    else return '<span class="badge badge-success">' + data + '</span>';
                }},
                {data: 'pay_status', name: 'pay_status', render: function(data) {
                    if (data == 'Paid') return '<span class="badge badge-success">' + data + '</span>';
                    else return '<span class="badge badge-danger">' + data + '</span>';
                }},
            ],
            order: []
        });

        $("#search_filter").on("keyup", function() { datatable.search($(this).val()).draw(); });

        function reloadDataTable() {
            var newUrl = '{{ url('task/list') }}' + '?tanggal_mulai_filter=' + $("#tanggal_mulai_filter").val() + '&tanggal_akhir_filter=' + $("#tanggal_akhir_filter").val() + '&task_status_filter=' + $("#task_status_filter").val() + '&pay_status_filter=' + $("#pay_status_filter").val() + '&client_id_filter={{ $client->id }}';
            datatable.ajax.url(newUrl).load();
        }
    </script>
@endpush
