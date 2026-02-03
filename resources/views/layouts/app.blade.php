<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Form Builder')</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- JS -->
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
            font-family: Inter, system-ui, sans-serif;
            background: #f3f4f6;
        }

        /* PREVENT FLICKER */
        .sidebar,
        .content {
            visibility: hidden;
        }

        /* TOPBAR */
        .topbar {
            height: 60px;
            position: fixed;
            inset: 0 0 auto 0;
            z-index: 1050;
            background: linear-gradient(135deg, #4f46e5, #f9fffe);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, .15);
        }

        .toggle-btn {
            font-size: 22px;
            cursor: pointer;
        }

        /* SIDEBAR */
        .sidebar {
            position: fixed;
            top: 60px;
            left: 0;
            bottom: 0;
            width: 240px;
            background: #ffffff;
            border-right: 1px solid #e5e7eb;
            padding: 14px 12px;
            transition: all .3s ease;
            display: flex;
            flex-direction: column;
            z-index: 1040;
        }

        .sidebar.collapsed {
            width: 72px;
        }

        .sidebar-content {
            flex: 1;
            overflow-y: auto;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 10px 14px;
            margin-bottom: 6px;
            border-radius: 12px;
            color: #374151;
            font-weight: 500;
            text-decoration: none;
            transition: all .2s ease;
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

        /* CONTENT */
        .content {
            margin-left: 240px;
            padding: 90px 30px 30px;
            transition: margin-left .3s ease;
        }

        .content.collapsed {
            margin-left: 72px;
        }

        /* USER ICON */
        #userArrow {
            transition: transform .2s ease;
        }

        /* MOBILE */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .content,
            .content.collapsed {
                margin-left: 0;
            }
        }
    </style>

    @stack('styles')
</head>

<body>

    <!-- TOPBAR -->
    <div class="topbar">
        <div class="d-flex align-items-center gap-3">
            <i class="bi bi-list toggle-btn" id="toggleSidebar"></i>
            <h5 class="mb-0 fw-semibold">
                <i class="bi bi-ui-checks-grid me-2"></i>
                @yield('topbar-title', 'Dashboard')
            </h5>
        </div>

        <div class="d-flex gap-2">
            @yield('topbar-buttons')
        </div>
    </div>

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">

        <div class="sidebar-content">
            <a href="{{ route('forms.index') }}" data-route="forms.index">
                <i class="bi bi-house"></i><span>Dashboard</span>
            </a>

            <a href="{{ route('calendar.index') }}" data-route="calendar.index">
                <i class="bi bi-calendar-event"></i><span>Calendar</span>
            </a>

            <a href="{{ route('assignments.dashboard') }}" data-route="assignments.dashboard">
                <i class="bi bi-graph-up"></i><span>Task</span>
            </a>

            <a href="{{ route('report.report') }}" data-route="report.report">
                <i class="bi bi-file-earmark-text"></i><span>Report</span>
            </a>
        </div>

        <!-- USER -->
        <div class="border-top pt-3 px-2">
            <div id="userDropdown"
                class="d-flex justify-content-between align-items-center p-2 rounded bg-light"
                style="cursor:pointer;">
                <span>{{ Str::limit(auth()->user()->name, 14) }}</span>
                <i class="bi bi-chevron-down" id="userArrow"></i>
            </div>

            <div id="userMenu" class="mt-2 d-none">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-sm btn-danger w-100">
                        <i class="bi bi-box-arrow-right me-1"></i> Logout
                    </button>
                </form>
            </div>
        </div>

    </aside>

    <!-- CONTENT -->
    <main class="content" id="content">
        @yield('content')
    </main>

    <!-- SCRIPT -->
    <script>
        $(function () {

            const sidebar = $('#sidebar');
            const content = $('#content');

            /* RESTORE SIDEBAR STATE */
            if (localStorage.getItem('sidebar') === 'collapsed') {
                sidebar.addClass('collapsed');
                content.addClass('collapsed');
            }

            /* ACTIVE ROUTE */
            const currentRoute = "{{ Route::currentRouteName() }}";
            $('.sidebar a').each(function () {
                if ($(this).data('route') === currentRoute) {
                    $(this).addClass('active');
                }
            });

            /* SIDEBAR TOGGLE */
            $('#toggleSidebar').on('click', function () {

                if (window.innerWidth <= 768) {
                    sidebar.toggleClass('show');
                    return;
                }

                sidebar.toggleClass('collapsed');
                content.toggleClass('collapsed');

                localStorage.setItem(
                    'sidebar',
                    sidebar.hasClass('collapsed') ? 'collapsed' : 'open'
                );
            });

            /* USER MENU */
            $('#userDropdown').on('click', function () {
                const menu = $('#userMenu');
                const icon = $('#userArrow');

                menu.toggleClass('d-none');

                if (menu.hasClass('d-none')) {
                    icon.removeClass('bi-chevron-up').addClass('bi-chevron-down');
                } else {
                    icon.removeClass('bi-chevron-down').addClass('bi-chevron-up');
                }
            });

            /* SHOW AFTER JS */
            $('.sidebar, .content').css('visibility', 'visible');

        });
    </script>

    @stack('scripts')
</body>

</html>
