@extends('area._base')
@push('head')
    <style>
        .calendar-loading-shell {
            position: relative;
        }

        .calendar-loading {
            position: absolute;
            inset: 0;
            z-index: 10;
            display: none;
            min-height: 320px;
            background: color-mix(in srgb, var(--nm-surface, #ffffff) 72%, transparent);
        }

        .calendar-loading .loader {
            display: block !important;
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            background-color: transparent;
            opacity: 1;
        }

        @supports not (background: color-mix(in srgb, white 50%, transparent)) {
            .calendar-loading {
                background: rgba(255, 255, 255, 0.72);
            }
        }

        body.dark .calendar-loading {
            background: rgba(15, 23, 42, 0.72);
        }

        .fc-more-popover {
            z-index: 1060;
            width: min(360px, calc(100vw - 24px));
            max-height: min(480px, calc(100vh - 120px));
            overflow: hidden;
            border: 1px solid var(--nm-border, #e5e7eb);
            border-radius: 12px;
            background: var(--nm-surface, #ffffff);
            box-shadow: 0 16px 34px rgba(15, 23, 42, 0.18);
        }

        .fc-more-popover .fc-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex: 0 0 auto;
            min-height: 42px;
            padding: 10px 12px;
            border-bottom: 1px solid var(--nm-border, #e5e7eb);
            background: var(--nm-surface-muted, #f8fafc);
            color: var(--nm-ink, #1f2937);
            font-size: 13px;
            font-weight: 800;
        }

        .fc-more-popover .fc-header .fc-close {
            order: 2;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 24px;
            width: 24px;
            height: 24px;
            float: none;
            margin: 0 0 0 12px;
            color: var(--nm-muted, #64748b);
            font-size: 0;
            line-height: 1;
        }

        .fc-more-popover .fc-header .fc-close::after {
            content: "\00d7";
            position: static;
            top: auto;
            font-family: Arial, sans-serif;
            font-size: 22px;
            font-weight: 400;
            line-height: 20px;
        }

        .fc-more-popover .fc-header .fc-title {
            order: 1;
            flex: 1 1 auto;
            float: none;
            margin: 0;
            text-align: left;
        }

        .fc-more-popover .fc-body {
            max-height: calc(min(480px, 100vh - 120px) - 42px);
            overflow-x: hidden;
            overflow-y: auto;
            padding: 8px;
            scrollbar-width: thin;
        }

        .fc-more-popover .fc-event-container {
            padding: 0;
        }

        .fc-more-popover .fc-event {
            margin: 4px 0;
            border-radius: 6px;
        }

        body.dark .fc-more-popover {
            border-color: #334155;
            background: #1e293b;
            box-shadow: 0 16px 34px rgba(0, 0, 0, 0.34);
        }

        body.dark .fc-more-popover .fc-header {
            border-bottom-color: #334155;
            background: #0f172a;
            color: #e2e8f0;
        }

        body.dark .fc-more-popover .fc-header .fc-close {
            color: #94a3b8;
        }
    </style>
@endpush
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
            <div class="calendar-loading-shell">
                <div id="calendar" class="calendar-container"></div>
                <div id="calendar-loading" class="calendar-loading" role="status" aria-live="polite" aria-label="Memuat kalender">
                    <div class="loader" aria-hidden="true"></div>
                </div>
            </div>
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
                        <span class="modal-close-mark" aria-hidden="true">&times;</span>
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
            $('#modal_detail_calendar').on('mousedown.calendarPopover', function(event) {
                event.stopPropagation();
            });
            $(document).on('mousedown.calendarPopover', '.modal-backdrop', function(event) {
                event.stopImmediatePropagation();
            });
        });

        var calendar;
        var date_start, date_end;
        var calendarRequest;

        function setCalendarLoading(isLoading) {
            $('#calendar-loading').toggle(isLoading);
        }

        calendar = $('#calendar').fullCalendar({
            height: 'auto',
            defaultView: 'month',
            editable: false,
            selectable: false,
            eventLimit: true,
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,listMonth'
            },
            viewRender: function(view) {
                date_start = view.start.format('YYYY-MM-DD');
                date_end = view.end.format('YYYY-MM-DD');
                loadCalendar();
            },
            eventClick: function(event) {
                detailCalendar(event.id);
            },
            eventRender: function(event, element) {
                element.css({
                    backgroundColor: event.backgroundColor,
                    borderColor: event.borderColor,
                    color: event.textColor,
                    fontWeight: '700'
                });
                element.find('.fc-title').css({color: event.textColor, fontWeight: '700'});
            }
        });

        function loadCalendar() {
            var worker_id = $("#worker_id").val();
            if (calendarRequest) {
                calendarRequest.abort();
            }
            setCalendarLoading(true);
            calendarRequest = $.ajax({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, type: "GET", url: "{{ url('task/get-by-date') }}", data: {start: date_start, end: date_end, worker_id: worker_id}, dataType: 'json',
                success: function(response) {
                    if (response.status == 1) {
                        var data = response.task;
                        calendar.fullCalendar('removeEvents');
                        data.forEach(function(task) {
                            var workerColor = /^#[0-9A-F]{6}$/i.test(task.hex || '') ? task.hex : '#64748B';
                            var colorValue = workerColor.substring(1);
                            var red = parseInt(colorValue.substring(0, 2), 16);
                            var green = parseInt(colorValue.substring(2, 4), 16);
                            var blue = parseInt(colorValue.substring(4, 6), 16);
                            var textColor = (red * 299 + green * 587 + blue * 114) / 1000 >= 150 ? '#1F2937' : '#FFFFFF';
                            calendar.fullCalendar('renderEvent', {
                                id: task.id,
                                title: task.fullname + ' - ' + task.task,
                                start: task.deadline,
                                end: task.deadline,
                                description: task.task,
                                className: 'calendar-event',
                                backgroundColor: workerColor,
                                borderColor: workerColor,
                                textColor: textColor,
                                allDay: true
                            }, true);
                        });
                    } else { Swal.fire("Oops!", response.msg, "error"); }
                },
                error: function(response) {
                    if (response.statusText !== 'abort') {
                        errorAjaxResponse(response);
                    }
                },
                complete: function() {
                    setCalendarLoading(false);
                    calendarRequest = null;
                }
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
