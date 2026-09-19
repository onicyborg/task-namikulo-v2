@extends('area._base')
@push('head')
@endpush
@section('content')
    <!-- Stat Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3">
            <div class="stat-card" style="--accent-rgb: 59, 130, 246;">
                <div style="display: flex; align-items: flex-start; justify-content: space-between;">
                    <div>
                        <div class="stat-label">Total Task</div>
                        <div class="stat-value">{{ $statistik['waiting'] + $statistik['progress'] + $statistik['done'] }}</div>
                    </div>
                    <div class="stat-icon" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                        <i data-feather="layers"></i>
                    </div>
                </div>
                <div style="margin-top: 0.75rem; display: flex; gap: 0.5rem;">
                    <span class="badge badge-warning" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">{{ $statistik['waiting'] }} Waiting</span>
                    <span class="badge badge-primary" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">{{ $statistik['progress'] }} Progress</span>
                    <span class="badge badge-success" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">{{ $statistik['done'] }} Done</span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3">
            <div class="stat-card" style="--accent-rgb: 245, 158, 11;">
                <div style="display: flex; align-items: flex-start; justify-content: space-between;">
                    <div>
                        <div class="stat-label">Waiting</div>
                        <div class="stat-value">{{ $statistik['waiting'] }}</div>
                    </div>
                    <div class="stat-icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                        <i data-feather="clock"></i>
                    </div>
                </div>
                <div style="margin-top: 0.75rem;">
                    <span style="font-size: 0.75rem; color: var(--text-secondary);">Task yang belum dikerjakan</span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3">
            <div class="stat-card" style="--accent-rgb: 16, 185, 129;">
                <div style="display: flex; align-items: flex-start; justify-content: space-between;">
                    <div>
                        <div class="stat-label">Done</div>
                        <div class="stat-value">{{ $statistik['done'] }}</div>
                    </div>
                    <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                        <i data-feather="check-circle"></i>
                    </div>
                </div>
                <div style="margin-top: 0.75rem;">
                    <span style="font-size: 0.75rem; color: var(--text-secondary);">Task yang sudah selesai</span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3">
            <div class="stat-card" style="--accent-rgb: 99, 102, 241;">
                <div style="display: flex; align-items: flex-start; justify-content: space-between;">
                    <div>
                        <div class="stat-label">{{ Auth::user()->role == 'Admin' ? 'Total Margin' : 'Total Revenue' }}</div>
                        <div class="stat-value" style="font-size: 1.5rem;">{{ Auth::user()->role == 'Admin' ? rupiah($statistik['margin'], true) : rupiah($statistik['pay_worker'], true) }}</div>
                    </div>
                    <div class="stat-icon" style="background: rgba(99, 102, 241, 0.1); color: #6366f1;">
                        <i data-feather="trending-up"></i>
                    </div>
                </div>
                <div style="margin-top: 0.75rem;">
                    <span style="font-size: 0.75rem; color: var(--text-secondary);">{{ Auth::user()->role == 'Admin' ? 'Keuntungan bersih' : 'Total pembayaran diterima' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart & Stats Row -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card" style="border-radius: var(--radius-xl);">
                <div class="card-header">
                    <h4>Statistik Task</h4>
                    <div class="card-header-action">
                        <span style="font-size: 0.8125rem; color: var(--text-secondary);">6 Bulan Terakhir</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-9 col-md-8 col-sm-12 dashboard-chart-column">
                            <div id="chart1" style="min-height: 300px;"></div>
                        </div>
                        <div class="col-lg-3 col-md-4 col-sm-12 mt-4 mt-md-0 dashboard-summary-column">
                            <div class="dashboard-summary" style="background: var(--bg-primary); border-radius: var(--radius-lg); padding: 1.25rem; border: 1px solid var(--border-color);">
                                <h6 style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--text-secondary); margin-bottom: 1rem;">Ringkasan</h6>
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.625rem 0; border-bottom: 1px solid var(--border-light);">
                                    <span style="font-size: 0.8125rem; color: var(--text-secondary); display: flex; align-items: center; gap: 0.5rem;">
                                        <span style="width: 8px; height: 8px; background: #f59e0b; border-radius: 50%; display: inline-block;"></span>
                                        Waiting
                                    </span>
                                    <span style="font-size: 0.9375rem; font-weight: 700;">{{ $statistik['waiting'] }}</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.625rem 0; border-bottom: 1px solid var(--border-light);">
                                    <span style="font-size: 0.8125rem; color: var(--text-secondary); display: flex; align-items: center; gap: 0.5rem;">
                                        <span style="width: 8px; height: 8px; background: #6366f1; border-radius: 50%; display: inline-block;"></span>
                                        Progress
                                    </span>
                                    <span style="font-size: 0.9375rem; font-weight: 700;">{{ $statistik['progress'] }}</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.625rem 0; border-bottom: 1px solid var(--border-light);">
                                    <span style="font-size: 0.8125rem; color: var(--text-secondary); display: flex; align-items: center; gap: 0.5rem;">
                                        <span style="width: 8px; height: 8px; background: #10b981; border-radius: 50%; display: inline-block;"></span>
                                        Done
                                    </span>
                                    <span style="font-size: 0.9375rem; font-weight: 700;">{{ $statistik['done'] }}</span>
                                </div>
                                @if (Auth::user()->role == 'Admin')
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.625rem 0; border-bottom: 1px solid var(--border-light);">
                                    <span style="font-size: 0.8125rem; color: var(--text-secondary);">Price Order</span>
                                    <span style="font-size: 0.875rem; font-weight: 600;">{{ rupiah($statistik['price'], true) }}</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.625rem 0; border-bottom: 1px solid var(--border-light);">
                                    <span style="font-size: 0.8125rem; color: var(--text-secondary);">Pay Worker</span>
                                    <span style="font-size: 0.875rem; font-weight: 600;">{{ rupiah($statistik['pay_worker'], true) }}</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.625rem 0;">
                                    <span style="font-size: 0.8125rem; color: var(--text-secondary); font-weight: 600;">Margin</span>
                                    <span style="font-size: 0.875rem; font-weight: 700; color: var(--status-success);">{{ rupiah($statistik['margin'], true) }}</span>
                                </div>
                                @else
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.625rem 0;">
                                    <span style="font-size: 0.8125rem; color: var(--text-secondary); font-weight: 600;">Total Revenue</span>
                                    <span style="font-size: 0.875rem; font-weight: 700; color: var(--status-success);">{{ rupiah($statistik['pay_worker'], true) }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Timeline -->
    <section class="card timeline-card" aria-labelledby="timeline-heading">
        <div class="card-header timeline-card-header">
            <div>
                <p class="timeline-eyebrow">Agenda kerja</p>
                <h4 id="timeline-heading">Deadline Mendatang</h4>
            </div>
            <span class="timeline-count">{{ count($timeline) }} task</span>
        </div>
        <div class="card-body timeline-body">
            @if (count($timeline) == 0)
                <div class="timeline-empty">
                    <i data-feather="calendar"></i>
                    <p>Belum ada deadline mendatang.</p>
                </div>
            @else
                @php $prevMonth = null; @endphp
                @foreach ($timeline as $item)
                    @php
                        $deadline = strtotime($item->deadline);
                        $currentMonth = bulanIndo(date('m', $deadline)) . ' ' . date('Y', $deadline);
                        $monthShort = substr(bulanIndo(date('m', $deadline)), 0, 3);
                        $isNewMonth = $currentMonth !== $prevMonth;
                        $statusColor = $item->task_status === 'Waiting' ? 'warning' : ($item->task_status === 'Progress' ? 'primary' : 'success');
                    @endphp

                    @if ($isNewMonth)
                        @if (! $loop->first)
                            </div>
                        </section>
                        @endif
                        <section class="timeline-month" aria-label="{{ $currentMonth }}">
                            <h5>{{ $currentMonth }}</h5>
                            <div class="timeline-list">
                    @endif

                    <a href="{{ url('task/detail/' . $item->id) }}" class="timeline-item" aria-label="Buka detail task {{ $item->task }}">
                        <time class="timeline-date" datetime="{{ $item->deadline }}">
                            <strong>{{ date('d', $deadline) }}</strong>
                            <span>{{ $monthShort }}</span>
                        </time>
                        <span class="timeline-rail" aria-hidden="true"><span></span></span>
                        <span class="timeline-content">
                            <span class="timeline-meta">
                                <span class="timeline-status timeline-status--{{ $statusColor }}">{{ $item->task_status }}</span>
                                <span class="timeline-deadline">Deadline {{ tglIndo($item->deadline) }}</span>
                            </span>
                            <strong class="timeline-task">{{ $item->task }}</strong>
                            <span class="timeline-people">
                                <span><i data-feather="user" aria-hidden="true"></i>{{ $item->fullname }}</span>
                                <span><i data-feather="briefcase" aria-hidden="true"></i>{{ $item->customer }}</span>
                            </span>
                        </span>
                        <i class="timeline-arrow" data-feather="chevron-right" aria-hidden="true"></i>
                    </a>

                    @if ($loop->last)
                            </div>
                        </section>
                    @endif
                    @php $prevMonth = $currentMonth; @endphp
                @endforeach
            @endif
        </div>
    </section>
@endsection

@section('modal')
@endsection

@push('js')
    <script src="{{ asset('assets/bundles/apexcharts/apexcharts.min.js') }}"></script>
    <script>
        $(function() {
            chart1();
            feather.replace();
        });

        function chart1() {
            const grafik = @json($grafik);
            const bulan = [];
            const waiting = [];
            const progress = [];
            const done = [];

            grafik.forEach((item) => {
                bulan.push(item.bulan);
                waiting.push(parseInt(item.waiting));
                progress.push(parseInt(item.progress));
                done.push(parseInt(item.done));
            });
            const maxDataValue = Math.max(...waiting, ...progress, ...done, 1);

            var options = {
                chart: {
                    height: 300,
                    type: "area",
                    toolbar: { show: false },
                    sparkline: { enabled: false },
                    dropShadow: { enabled: true, blur: 8, opacity: 0.06 }
                },
                colors: ["#f59e0b", "#6366f1", "#10b981"],
                dataLabels: { enabled: false },
                stroke: {
                    curve: "smooth",
                    width: 3
                },
                series: [
                    { name: "Waiting", data: waiting },
                    { name: "Progress", data: progress },
                    { name: "Done", data: done }
                ],
                grid: {
                    borderColor: "var(--border-color)",
                    strokeDashArray: 4,
                    xaxis: { lines: { show: false } },
                    yaxis: { lines: { show: true } }
                },
                markers: {
                    size: 0,
                    hover: { size: 6 }
                },
                xaxis: {
                    categories: bulan,
                    tickPlacement: 'between',
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                    labels: {
                        style: { colors: "var(--text-muted)", fontSize: "12px" }
                    }
                },
                yaxis: {
                    title: { text: "Total", style: { color: "var(--text-muted)", fontSize: "12px" } },
                    labels: {
                        style: { colors: "var(--text-muted)", fontSize: "12px" }
                    },
                    max: maxDataValue + 1
                },
                legend: {
                    show: true,
                    position: 'top',
                    horizontalAlign: 'right',
                    fontSize: '12px',
                    labels: { colors: "var(--text-secondary)" },
                    markers: { width: 10, height: 10, radius: 3 }
                },
                fill: {
                    type: "gradient",
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.15,
                        opacityTo: 0.02,
                        stops: [0, 90, 100]
                    }
                },
                tooltip: {
                    theme: document.body.classList.contains('dark') ? 'dark' : 'light',
                    cssClass: 'dashboard-chart-tooltip',
                    style: { fontSize: '12px', fontFamily: 'Plus Jakarta Sans, sans-serif' }
                }
            };

            var chart = new ApexCharts(document.querySelector("#chart1"), options);
            chart.render();
        }
    </script>
@endpush
