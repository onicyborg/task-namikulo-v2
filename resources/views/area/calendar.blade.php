@extends('area._base')
@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2 class="page-title">Calendar</h2>
            <p class="page-subtitle">Pantau jadwal dan deadline task worker</p>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            @if (Auth::user()->role == 'Admin')
                <div class="filter-bar mb-3">
                    <div class="form-group" style="min-width: 200px; margin-bottom: 0;">
                        <label>Worker</label>
                        <select name="worker_id" id="worker_id" class="form-control" onchange="loadCalendar()"></select>
                    </div>
                </div>
            @endif
            <div id="calendar" class="calendar-container"></div>
        </div>
    </div>
@endsection
@section('modal')
    <div class="modal fade" id="modal_detail_calendar" tabindex="-1" role="dialog" aria-labelledby="modalDetailCalendarLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDetailCalendarLabel">
                        <i data-feather="calendar" style="width: 18px; height: 18px; vertical-align: middle; margin-right: 8px;"></i>
                        Detail Task
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i data-feather="x" style="width: 18px; height: 18px;"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="info-list">
                        <div class="info-row">
                            <span class="info-label">Kode</span>
                            <span class="info-value" id="kode_detail"></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Deskripsi</span>
                            <span class="info-value" id="task_detail"></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Client</span>
                            <span class="info-value" id="client_detail"></span>
                        </div>
                        @if (Auth::user()->role == 'Admin')
                            <div class="info-row">
                                <span class="info-label">Worker</span>
                                <span class="info-value" id="worker_detail"></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Tanggal Order</span>
                                <span class="info-value" id="order_detail"></span>
                            </div>
                        @endif
                        <div class="info-row">
                            <span class="info-label">Deadline</span>
                            <span class="info-value" id="deadline_detail"></span>
                        </div>
                        @if (Auth::user()->role == 'Admin')
                            <div class="info-row">
                                <span class="info-label">Price Order</span>
                                <span class="info-value" id="price_order_detail"></span>
                            </div>
                        @endif
                        <div class="info-row">
                            <span class="info-label">Pay to Worker</span>
                            <span class="info-value" id="pay_worker_detail"></span>
                        </div>
                        @if (Auth::user()->role == 'Admin')
                            <div class="info-row">
                                <span class="info-label">Margin</span>
                                <span class="info-value" id="margin_detail"></span>
                            </div>
                        @endif
                        <div class="info-row">
                            <span class="info-label">Task Status</span>
                            <span class="info-value" id="task_status_detail"></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Pay Status</span>
                            <span class="info-value" id="pay_status_detail"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>
        $(document).ready(function() {
            $('#worker_id').select2({
                width: '100%',
                ajax: {url: '{{ url('worker/search') }}', data: {'empty_result': 'true'}, dataType: 'json', delay: 250, processResults: function(d) { return { results: d }; }, cache: true},
            });
            $("#worker_id").select2("trigger", "select", {data: {id: 'all', text: 'Seluruh Worker'}});
        });

        var calendar;
        var date_start, date_end;
        var todayDate = new Date();
        var YM = todayDate.toISOString().slice(0, 7);
        var TODAY = todayDate.getFullYear() + '-' + String(todayDate.getMonth() + 1).padStart(2, '0') + '-' + String(todayDate.getDate()).padStart(2, '0');

        var calendarEl = document.getElementById('calendar');
        calendar = new FullCalendar.Calendar(calendarEl, {
            headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,listMonth' },
            height: 'auto', contentHeight: 'auto', aspectRatio: 1.35, nowIndicator: true, now: TODAY + 'T09:25:00',
            views: { dayGridMonth: { buttonText: 'month' } },
            initialView: 'dayGridMonth', initialDate: TODAY, editable: false, dayMaxEvents: true, navLinks: true,
            datesSet: function(info) { date_start = info.startStr; date_end = info.endStr; loadCalendar(); },
            eventClick: function(info) { detailCalendar(info.event.id); },
            eventDidMount: function(info) { info.el.style.backgroundColor = info.event.backgroundColor; info.el.style.borderColor = info.event.borderColor; info.el.style.color = info.event.textColor; }
        });
        calendar.render();

        function loadCalendar() {
            var worker_id = $("#worker_id").val();
            $.ajax({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, type: "GET", url: "{{ url('task/get-by-date') }}", data: {start: date_start, end: date_end, worker_id: worker_id}, dataType: 'json',
                success: function(response) {
                    if (response.status == 1) {
                        var data = response.task;
                        calendar.getEvents().forEach(function(event) { event.remove(); });
                        data.forEach(function(task) { var eventPalette = { Waiting: { background: '#90CAF9', border: '#2196F3', text: '#0D47A1' }, Progress: { background: '#2196F3', border: '#0D47A1', text: '#FFFFFF' }, Done: { background: '#76ABAE', border: '#4F8588', text: '#222831' } }; var palette = eventPalette[task.task_status] || { background: task.hex || '#2196F3', border: task.hex || '#0D47A1', text: '#FFFFFF' }; calendar.addEvent({ id: task.id, title: task.fullname + ' - ' + task.task, start: task.deadline, end: task.deadline, description: task.task, className: 'calendar-event', backgroundColor: palette.background, borderColor: palette.border, textColor: palette.text, allDay: true }); });
                    } else { Swal.fire("Oops!", response.msg, "error"); }
                },
                error: function(response) { errorAjaxResponse(response); }
            });
        }

        function detailCalendar(id) {
            $.ajax({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, type: "GET", url: "{{ url('task/detail') }}/" + id, dataType: 'json',
                success: function(response) {
                    if (response.status == 1) {
                        var data = response.task;
                        $("#kode_detail").html(data.kode_task);
                        $("#task_detail").html(data.task);
                        $("#client_detail").html(data.customer);
                        @if (Auth::user()->role == 'Admin')
                            $("#order_detail").html(data.order_indo);
                            $("#worker_detail").html(data.worker);
                            $("#price_order_detail").html(idrFormat(data.price_order, true));
                            $("#margin_detail").html(idrFormat(data.margin, true));
                        @endif
                        $("#deadline_detail").html(data.deadline_indo);
                        $("#pay_worker_detail").html(idrFormat(data.pay_worker, true));
                        $("#task_status_detail").html(data.task_status);
                        $("#pay_status_detail").html(data.pay_status);
                        $("#modal_detail_calendar").modal("show");
                    } else { Swal.fire("Oops!", response.msg, "error"); }
                },
                error: function(response) { errorAjaxResponse(response); }
            });
        }
    </script>
@endpush
