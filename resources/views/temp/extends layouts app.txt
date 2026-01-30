@extends('layouts.app')

@section('topbar-title', 'Employee Report')

@section('topbar-buttons')
    @include('report._filterbar')
@endsection

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <button id="prevBtn" class="btn btn-outline-secondary">⬅ Prev</button>
        <div class="fw-bold fs-5" id="reportLabel"></div>
        <button id="nextBtn" class="btn btn-outline-secondary">Next ➡</button>
    </div>

    <div id="tableView">
        <table id="reportTable" class="table table-bordered text-center w-100">
            <thead>
                <tr>
                    <th class="text-start">Employee</th>
                    <th>Total</th>
                    <th>Score</th>
                    <th>Pending</th>
                    <th>Progress</th>
                    <th>Completed</th>
                    <th>Overdue</th>
                    {{-- <th>Delayed</th> --}}
                </tr>
            </thead>
        </table>
    </div>

    <div id="chartView" class="d-none">

        {{-- TOP DONUT CHARTS --}}
        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <div class="card p-3 card-chart">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card p-3 card-chart">
                    <canvas id="completionChart"></canvas>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card p-3 card-chart">
                    <canvas id="timingChart"></canvas>
                </div>
            </div>
        </div>

        {{-- SUMMARY LABELS BELOW DONUTS --}}
        <div class="row mb-3">
            <div class="col-12">
                <div id="summaryLabels" class="d-flex flex-wrap gap-3 align-items-center">
                    <!-- filled by JS: small swatch + label + value -->
                </div>
            </div>
        </div>

        {{-- EMPLOYEE BAR CHART (FULL WIDTH) --}}
        {{-- <div class="row g-3">
            <div class="col-12">
                <div class="card p-3 card-chart">
                    <h6 class="mb-2">Employee Wise</h6>
                    <canvas id="employeeChart"></canvas>
                </div>
            </div>
        </div> --}}
        <div class="row g-3">
            <div class="col-md-6">
                <div class="card p-3 card-chart">
                    <h6 class="mb-2">Employee Wise</h6>
                    <canvas id="employeeChart"></canvas>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card p-3 card-chart">
                    <h6 class="mb-2">Category Wise</h6>
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>


    </div>

@endsection

@push('styles')
    <style>
        .card-chart {
            min-height: 260px;
            display: flex;
            flex-direction: column;
        }

        .card-chart canvas {
            height: 100% !important;
            width: 100% !important;
            max-height: 420px !important;
        }

        .summary-item {
            display: flex;
            gap: 8px;
            align-items: center;
            padding: 6px 10px;
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
            font-size: 0.95rem;
        }

        .swatch {
            width: 12px;
            height: 12px;
            border-radius: 3px;
            display: inline-block;
        }

        .summary-label {
            color: #374151;
            margin-right: 6px;
        }

        .summary-value {
            font-weight: 700;
            color: #111827;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        (function() {
            if (window.__reportChartsInitialized) return;
            window.__reportChartsInitialized = true;

            let filter = 'day',
                param = null,
                view = 'table';

            const table = $('#reportTable').DataTable({
                serverSide: true,
                ajax: {
                    url: "{{ route('report.data') }}",
                    data: d => {
                        d.filter = filter;
                        if (param) {
                            if (filter === 'day') d.date = param;
                            if (filter === 'week') d.week = param;
                            if (filter === 'month') d.month = param;
                        }
                    }
                },
                columns: [{
                        data: 'employee',
                        className: 'text-start'
                    },
                    {
                        data: 'total'
                    }, {
                        data: 'score'
                    },
                    {
                        data: 'pending'
                    }, {
                        data: 'in_progress'
                    }, {
                        data: 'completed'
                    }, {
                        data: 'overdue'
                    }
                ]
            });

            table.on('xhr', () => {
                const j = table.ajax.json();
                if (j) {
                    $('#reportLabel').text(j.label);
                    updateNav(j.prev, j.next);
                }
            });

            // small data-label plugin (register once)
            const drawBarDataLabels = {
                id: 'drawBarDataLabels',
                afterDatasetsDraw(chart) {
                    const ctx = chart.ctx;
                    chart.data.datasets.forEach((dataset, i) => {
                        const meta = chart.getDatasetMeta(i);
                        meta.data.forEach((bar, index) => {
                            const val = dataset.data[index];
                            if (!val) return;
                            ctx.save();
                            ctx.fillStyle = '#000';
                            ctx.font = '12px Arial';
                            ctx.textAlign = 'center';
                            ctx.textBaseline = 'middle';
                            const x = bar.x;
                            const y = bar.y + (bar.base - bar.y) / 2;
                            ctx.fillText(val, x, y);
                            ctx.restore();
                        });
                    });
                }
            };
            Chart.register(drawBarDataLabels);

            const commonOptions = {
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        stacked: true
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true
                    }
                }
            };

            // Donut charts
            const statusChart = new Chart(document.getElementById('statusChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Overdue', 'Pending', 'In Progress'],
                    datasets: [{
                        data: [2, 4, 5],
                        backgroundColor: ['#dc3545', '#ffc107', '#0dcaf0']
                    }]
                },
                options: {
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                generateLabels: chart =>
                                    legendWithCountAndPercent(chart, false)
                            }
                        }
                    }
                }
            });

            const completionChart = new Chart(document.getElementById('completionChart'), {
    type: 'doughnut',
    data: {
        labels: ['Completed', 'Not Completed'],
        datasets: [{
            data: [3, 1],
            backgroundColor: ['#28a745', '#dc3545']
        }]
    },
    options: {
        plugins: {
            legend: {
                position: 'right',
                labels: {
                    generateLabels: chart =>
                        legendWithCountAndPercent(chart, false)
                }
            }
        }
    }
});

            const timingChart = new Chart(document.getElementById('timingChart'), {
                type: 'doughnut',
                data: {
                    labels: ['In-Time', 'Delayed'],
                    datasets: [{
                        data: [6, 2],
                        backgroundColor: ['#28a745', '#dc3545']
                    }]
                },
                options: {
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                generateLabels: chart =>
                                    legendWithCountAndPercent(chart, true)
                            }
                        }
                    }
                }
            });

            // Category wise bar chart
            const categoryChart = new Chart(
                document.getElementById('categoryChart'), {
                    type: 'bar',
                    data: {
                        labels: ['Pending', 'In Progress', 'Completed', 'Overdue'],
                        datasets: [{
                            label: 'Tasks',
                            data: [0, 0, 0, 0],
                            backgroundColor: [
                                '#ffc107',
                                '#0dcaf0',
                                '#28a745',
                                '#dc3545'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                    precision: 0
                                }
                            }
                        }
                    }
                }
            );

            // Employee stacked bar (full width)
            const employeeChart = new Chart(document.getElementById('employeeChart'), {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                            label: 'Pending',
                            data: [],
                            backgroundColor: '#ffc107'
                        },
                        {
                            label: 'In Progress',
                            data: [],
                            backgroundColor: '#0dcaf0'
                        },
                        {
                            label: 'Completed',
                            data: [],
                            backgroundColor: '#28a745'
                        },
                        {
                            label: 'Overdue',
                            data: [],
                            backgroundColor: '#dc3545'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    },
                    scales: {
                        x: {
                            stacked: false
                        },
                        y: {
                            stacked: false,
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                precision: 0
                            },
                            title: {
                                display: true,
                                text: 'Tasks Count'
                            }
                        }
                    }
                }
            });

            function legendWithCountAndPercent(chart, showPercent = false) {
                const data = chart.data;
                const values = data.datasets[0].data;
                const total = values.reduce((a, b) => a + b, 0);

                return data.labels.map((label, i) => {
                    const value = values[i] || 0;
                    let text = `${label} (${value})`;

                    if (showPercent && total > 0) {
                        const percent = Math.round((value / total) * 100);
                        text += ` - ${percent}%`;
                    }

                    return {
                        text,
                        fillStyle: data.datasets[0].backgroundColor[i],
                        strokeStyle: data.datasets[0].backgroundColor[i],
                        index: i
                    };
                });
            }

            function legendWithCount(chart) {
                const data = chart.data;
                return data.labels.map((label, i) => {
                    const value = data.datasets[0].data[i] || 0;
                    return {
                        text: `${label} (${value})`,
                        fillStyle: data.datasets[0].backgroundColor[i],
                        strokeStyle: data.datasets[0].backgroundColor[i],
                        index: i
                    };
                });
            }

            function renderSummaryLabels(summary) {
                const container = document.getElementById('summaryLabels');
                if (!container) return;
                const items = [{
                        label: 'Completed',
                        value: summary.completed || 0,
                        color: '#28a745'
                    },
                    {
                        label: 'Pending',
                        value: summary.pending || 0,
                        color: '#ffc107'
                    },
                    {
                        label: 'Overdue',
                        value: summary.overdue || 0,
                        color: '#dc3545'
                    },
                    {
                        label: 'In Progress',
                        value: summary.in_progress || 0,
                        color: '#0dcaf0'
                    },
                    {
                        label: 'Delayed',
                        value: summary.delayed || 0,
                        color: '#6f42c1'
                    },
                    {
                        label: 'Not Completed',
                        value: summary.not_completed || 0,
                        color: '#f97316'
                    },
                    {
                        label: 'In-Time',
                        value: summary.in_time || 0,
                        color: '#10b981'
                    }
                ];

                container.innerHTML = items.map(it => `
                    <div class="summary-item">
                        <span class="swatch" style="background:${it.color}"></span>
                        <span class="summary-label">${it.label}:</span>
                        <span class="summary-value">${it.value}</span>
                    </div>
                `).join('');
            }

            function loadAllCharts() {
                $.get("{{ route('report.chart.data') }}", {
                    filter,
                    date: filter === 'day' ? param : null,
                    week: filter === 'week' ? param : null,
                    month: filter === 'month' ? param : null
                }, function(r) {
                    console.log('chart.data', r);
                    if (!r) return;

                    // donuts
                    statusChart.data.datasets[0].data = [r.summary.overdue || 0, r.summary.pending || 0, r
                        .summary.in_progress || 0
                    ];
                    completionChart.data.datasets[0].data = [r.summary.completed || 0, r.summary
                        .not_completed || 0
                    ];
                    timingChart.data.datasets[0].data = [r.summary.in_time || 0, r.summary.delayed || 0];
                    statusChart.update();
                    completionChart.update();
                    timingChart.update();
                    categoryChart.data.datasets[0].data = [
                        r.summary.pending || 0,
                        r.summary.in_progress || 0,
                        r.summary.completed || 0,
                        r.summary.overdue || 0
                    ];
                    categoryChart.update();
                    // render textual summary labels under donuts
                    renderSummaryLabels(r.summary);

                    // employee stacked bars (full width)
                    employeeChart.data.labels = r.employee.labels || [];
                    employeeChart.data.datasets[0].data = r.employee.pending || [];
                    employeeChart.data.datasets[1].data = r.employee.in_progress || [];
                    employeeChart.data.datasets[2].data = r.employee.completed || [];
                    employeeChart.data.datasets[3].data = r.employee.overdue || [];

                    // 🔥 Y-axis total fix
                    let maxTotal = 0;
                    r.employee.labels.forEach((_, i) => {
                        const total =
                            (r.employee.pending[i] || 0) +
                            (r.employee.in_progress[i] || 0) +
                            (r.employee.completed[i] || 0) +
                            (r.employee.overdue[i] || 0);

                        if (total > maxTotal) maxTotal = total;
                    });

                    // 🔥 exact Y-axis max
                    employeeChart.options.scales.y.max = maxTotal;

                    employeeChart.update();



                    $('#reportLabel').text(r.label);
                    updateNav(r.prev, r.next);
                }).fail(() => console.warn('Failed to load chart data'));
            }

            function updateNav(prev, next) {
                $('#prevBtn').data('value', prev).prop('disabled', !prev || filter === 'all');
                $('#nextBtn').data('value', next).prop('disabled', !next || filter === 'all');
            }

            $('#prevBtn').click(() => {
                param = $('#prevBtn').data('value');
                view === 'table' ? table.ajax.reload() : loadAllCharts();
            });
            $('#nextBtn').click(() => {
                param = $('#nextBtn').data('value');
                view === 'table' ? table.ajax.reload() : loadAllCharts();
            });

            $('.filter-btn').click(function() {
                $('.filter-btn').removeClass('btn-primary active').addClass('btn-outline-primary');
                $(this).addClass('btn-primary active').removeClass('btn-outline-primary');
                filter = $(this).data('filter');
                param = null;
                view === 'table' ? table.ajax.reload() : loadAllCharts();
            });

            $('#btnTable').click(e => {
                e.preventDefault();
                view = 'table';
                $('#chartView').addClass('d-none');
                $('#tableView').removeClass('d-none');
                table.ajax.reload();
            });
            $('#btnChart').click(e => {
                e.preventDefault();
                view = 'chart';
                $('#tableView').addClass('d-none');
                $('#chartView').removeClass('d-none');
                loadAllCharts();
            });
            console.log(r.employee.delayed);

            // initial load
            table.ajax.reload();
        })();
    </script>
@endpush

