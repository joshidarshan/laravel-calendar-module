<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Builder</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- jQuery + Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>

    <style>
        body {
            background: #f3f4f6;
            font-family: Inter, system-ui;
        }

        .topbar {
            height: 60px;
            background: linear-gradient(135deg, #4f46e5, #f9fffe);
            color: #fff;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1050;
            display: flex;
            align-items: center;
            padding: 0 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .1);
        }

        .toggle-btn {
            font-size: 22px;
            cursor: pointer;
            margin-right: 15px;
        }

        .sidebar {
            position: fixed;
            top: 60px;
            left: 0;
            bottom: 0;
            width: 240px;
            background: #fff;
            border-right: 1px solid #e5e7eb;
            padding: 15px 10px;
            transition: all .3s ease;
            overflow-y: auto;
        }

        .sidebar.collapsed {
            width: 70px;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            margin-bottom: 6px;
            color: #374151;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 500;
            transition: .2s;
            white-space: nowrap;
        }

        .sidebar a i {
            font-size: 18px;
            min-width: 22px;
            text-align: center;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #eef2ff;
            color: #4f46e5;
        }

        .sidebar.collapsed span {
            display: none;
        }

        .content {
            margin-left: 240px;
            padding: 90px 30px 30px;
            transition: all .3s ease;
        }

        .content.collapsed {
            margin-left: 70px;
        }

        .card {
            border-radius: 14px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, .06);
            border: none;
        }

        @media(max-width:768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .content {
                margin-left: 0;
            }
        }

        #formsArrow {
            transition: transform .3s ease;
        }

        #formsArrow.rotate {
            transform: rotate(180deg);
        }
    </style>

    @stack('styles')
</head>

<body>

    <!-- TOPBAR -->
    <!-- TOPBAR -->
<div class="topbar d-flex justify-content-between align-items-center">
    <!-- LEFT SIDE -->
    <div class="d-flex align-items-center gap-3">
        <i class="bi bi-list toggle-btn" id="toggleSidebar"></i>
        <h5 class="mb-0 fw-semibold">
            <i class="bi bi-ui-checks-grid me-2"></i>
            @yield('topbar-title', 'Dashboard')
        </h5>
    </div>

    <!-- RIGHT SIDE -->
    <div class="d-flex gap-2">
        @yield('topbar-buttons')
    </div>
</div>


    <!-- SIDEBAR -->
    <div class="sidebar" id="sidebar">
        <a href="{{ route('forms.index') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-house"></i>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('calendar.index') }}">
            <i class="bi bi-calendar-event"></i>
            <span>Calendar</span>
        </a>
        <a href="{{ route('report.day.table') }}" class="{{ request()->routeIs('report.index') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-text"></i>
            <span>Report</span>
        </a>


        <hr>

        <!-- Search -->
        <div class="px-3 mb-2">
            <input type="text" id="formSearch" class="form-control form-control-sm" placeholder="Search forms...">
        </div>

        <!-- Forms Dropdown -->
        <a href="javascript:void(0)" class="d-flex justify-content-between align-items-center" id="toggleFormsList">
            <span><i class="bi bi-file-earmark-text"></i> Forms</span>
            <i class="bi bi-chevron-down" id="formsArrow"></i>
        </a>

        <div id="formsList" class="ms-3 mt-1">
            <!-- Recent 10 forms -->
            @php $recentForms = \App\Models\Form::latest()->take(5)->get(); @endphp
            @foreach ($recentForms as $form)
                <a href="{{ route('forms.entries', $form->id) }}"
                    class="{{ request()->is('forms/' . $form->id . '*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-text"></i>
                    <span>{{ $form->name }}</span>
                </a>
            @endforeach
        </div>
    </div>

    <!-- CONTENT -->
    <div class="content" id="content">
        @yield('content')
    </div>

    <script>
        // Sidebar toggle
        $('#toggleSidebar').click(function() {
            $('#sidebar').toggleClass('collapsed show');
            $('#content').toggleClass('collapsed');
        });

        // Forms list toggle
        $('#toggleFormsList').click(function() {
            $('#formsList').slideToggle(200);
            $('#formsArrow').toggleClass('rotate');
        });

        // AJAX server-side search
        $('#formSearch').on('keyup', function() {
            let keyword = $(this).val();
            $.ajax({
                url: "{{ route('forms.ajax.search') }}",
                type: "GET",
                data: {
                    search: keyword
                },
                success: function(res) {
                    let html = '';
                    if (res.data.length > 0) {
                        res.data.forEach(f => {
                            html += `<a href="/forms/${f.id}/entries">
                                    <i class="bi bi-file-earmark-text"></i>
                                    <span>${f.name}</span>
                                </a>`;
                        });
                    } else {
                        html = `<div class="text-muted px-2">No forms found</div>`;
                    }
                    $('#formsList').html(html);
                }
            });
        });
    </script>

    @stack('scripts')
</body>

</html>
