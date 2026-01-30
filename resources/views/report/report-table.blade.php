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
                </tr>
            </thead>
        </table>
    </div>

    <div id="chartView" class="d-none">
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

        <div class="row mb-3">
            <div class="col-12">
                <div id="summaryLabels" class="d-flex flex-wrap gap-3 align-items-center"></div>
            </div>
        </div>

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

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (function() {
            const apiToken = "{{ $apiToken }}"; // token from controller
            let filter = 'day',
                param = null,
                view = 'table';

            // --- DataTable ---
            const table = $('#reportTable').DataTable({
                ajax: {
                    url: "/api/report/table",
                    type: "GET",
                    headers: {
                        Authorization: "Bearer " + apiToken
                    },
                    data: d => {
                        d.filter = filter;
                        if (filter === 'day') d.date = param;
                        if (filter === 'week') d.week = param;
                        if (filter === 'month') d.month = param;
                    },
                    dataSrc: "data"
                },
                columns: [{
                        data: 'employee',
                        className: 'text-start'
                    },
                    {
                        data: 'total'
                    },
                    {
                        data: 'score'
                    },
                    {
                        data: 'pending'
                    },
                    {
                        data: 'in_progress'
                    },
                    {
                        data: 'completed'
                    },
                    {
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

            // --- Charts ---
            const statusChart = new Chart(document.getElementById('statusChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Overdue', 'Pending', 'In Progress'],
                    datasets: [{
                        data: [0, 0, 0],
                        backgroundColor: ['#dc3545', '#ffc107', '#0dcaf0']
                    }]
                }
            });
            const completionChart = new Chart(document.getElementById('completionChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Completed', 'Not Completed'],
                    datasets: [{
                        data: [0, 0],
                        backgroundColor: ['#28a745', '#dc3545']
                    }]
                }
            });
            const timingChart = new Chart(document.getElementById('timingChart'), {
                type: 'doughnut',
                data: {
                    labels: ['In-Time', 'Delayed'],
                    datasets: [{
                        data: [0, 0],
                        backgroundColor: ['#28a745', '#dc3545']
                    }]
                }
            });
            const categoryChart = new Chart(document.getElementById('categoryChart'), {
                type: 'bar',
                data: {
                    labels: ['Pending', 'In Progress', 'Completed', 'Overdue'],
                    datasets: [{
                        label: 'Tasks',
                        data: [0, 0, 0, 0],
                        backgroundColor: ['#ffc107', '#0dcaf0', '#28a745', '#dc3545']
                    }]
                }
            });
            const employeeChart = new Chart(document.getElementById('employeeChart'), {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Pending',
                        data: [],
                        backgroundColor: '#ffc107'
                    }, {
                        label: 'In Progress',
                        data: [],
                        backgroundColor: '#0dcaf0'
                    }, {
                        label: 'Completed',
                        data: [],
                        backgroundColor: '#28a745'
                    }, {
                        label: 'Overdue',
                        data: [],
                        backgroundColor: '#dc3545'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            function loadAllCharts() {
                $.ajax({
                    url: "/api/report/chart",
                    type: "GET",
                    headers: {
                        Authorization: "Bearer " + apiToken
                    },
                    data: {
                        filter,
                        date: filter === 'day' ? param : null,
                        week: filter === 'week' ? param : null,
                        month: filter === 'month' ? param : null
                    },
                    success: function(r) {
                        if (!r) return;
                        statusChart.data.datasets[0].data = [r.summary.overdue || 0, r.summary.pending || 0,
                            r.summary.in_progress || 0
                        ];
                        completionChart.data.datasets[0].data = [r.summary.completed || 0, r.summary
                            .not_completed || 0
                        ];
                        timingChart.data.datasets[0].data = [r.summary.in_time || 0, r.summary.delayed ||
                        0];
                        categoryChart.data.datasets[0].data = [r.summary.pending || 0, r.summary
                            .in_progress || 0, r.summary.completed || 0, r.summary.overdue || 0
                        ];
                        employeeChart.data.labels = r.employee.labels || [];
                        employeeChart.data.datasets[0].data = r.employee.pending || [];
                        employeeChart.data.datasets[1].data = r.employee.in_progress || [];
                        employeeChart.data.datasets[2].data = r.employee.completed || [];
                        employeeChart.data.datasets[3].data = r.employee.overdue || [];

                        // exact y-axis max
                        let maxTotal = 0;
                        r.employee.labels.forEach((_, i) => {
                            const t = (r.employee.pending[i] || 0) + (r.employee.in_progress[i] ||
                                0) + (r.employee.completed[i] || 0) + (r.employee.overdue[i] ||
                                0);
                            if (t > maxTotal) maxTotal = t;
                        });
                        employeeChart.options.scales.y.max = maxTotal;

                        statusChart.update();
                        completionChart.update();
                        timingChart.update();
                        categoryChart.update();
                        employeeChart.update();

                        // summary labels
                        const container = document.getElementById('summaryLabels');
                        const items = [{
                            label: 'Completed',
                            value: r.summary.completed || 0,
                            color: '#28a745'
                        }, {
                            label: 'Pending',
                            value: r.summary.pending || 0,
                            color: '#ffc107'
                        }, {
                            label: 'Overdue',
                            value: r.summary.overdue || 0,
                            color: '#dc3545'
                        }, {
                            label: 'In Progress',
                            value: r.summary.in_progress || 0,
                            color: '#0dcaf0'
                        }, {
                            label: 'Delayed',
                            value: r.summary.delayed || 0,
                            color: '#6f42c1'
                        }, {
                            label: 'Not Completed',
                            value: r.summary.not_completed || 0,
                            color: '#f97316'
                        }, {
                            label: 'In-Time',
                            value: r.summary.in_time || 0,
                            color: '#10b981'
                        }];
                        container.innerHTML = items.map(it =>
                            `<div class="summary-item"><span class="swatch" style="background:${it.color}"></span><span class="summary-label">${it.label}:</span><span class="summary-value">${it.value}</span></div>`
                            ).join('');

                        $('#reportLabel').text(r.label);
                        updateNav(r.prev, r.next);
                    }
                });
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

            table.ajax.reload(); // initial load
        })();
    </script>
@endpush
