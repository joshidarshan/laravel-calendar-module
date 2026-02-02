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

        /* SIDEBAR */
        .sidebar {
            position: fixed;
            top: 60px;
            left: 0;
            bottom: 0;
            width: 240px;
            background: #fff;
            border-right: 1px solid #e5e7eb;
            padding: 12px;
            transition: all .3s ease;
            display: flex;
            flex-direction: column;
        }

        .sidebar.collapsed {
            width: 70px;
        }

        .sidebar-content {
            flex: 1;
            overflow-y: auto;
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

        #formsArrow,
        #userArrow {
            transition: transform .3s ease;
        }

        .rotate {
            transform: rotate(180deg);
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
    </style>

    @stack('styles')
</head>

<body>

    <!-- TOPBAR -->
    <div class="topbar d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <i class="bi bi-list toggle-btn" id="toggleSidebar"></i>
            <h5 class="mb-0 fw-semibold">
                <i class="bi bi-ui-checks-grid me-2"></i>
                @yield('topbar-title', 'Dashboard')
            </h5>
        </div>
        <div>@yield('topbar-buttons')</div>
    </div>

    <!-- SIDEBAR -->
    <div class="sidebar" id="sidebar">

        <div class="sidebar-content">

            <a href="{{ route('forms.index') }}">
                <i class="bi bi-house"></i><span>Dashboard</span>
            </a>

            <a href="{{ route('calendar.index') }}">
                <i class="bi bi-calendar-event"></i><span>Calendar</span>
            </a>

            <a href="{{ route('assignments.dashboard') }}">
                <i class="bi bi-graph-up"></i><span>Task</span>
            </a>

            <a href="{{ route('report.report') }}">
                <i class="bi bi-file-earmark-text"></i><span>Report</span>
            </a>

            <hr>

            <div class="px-2 mb-2">
                <input type="text" id="formSearch" class="form-control form-control-sm" placeholder="Search forms...">
            </div>

            <a href="javascript:void(0)" id="toggleFormsList" class="d-flex justify-content-between align-items-center">
                <span><i class="bi bi-file-earmark-text"></i> Forms</span>
                <i class="bi bi-chevron-down" id="formsArrow"></i>
            </a>

            <div id="formsList" class="ms-3 mt-1">
                @foreach (\App\Models\Form::latest()->take(5)->get() as $form)
                    <a href="{{ route('forms.entries', $form->id) }}">
                        <i class="bi bi-file-earmark-text"></i>
                        <span>{{ $form->name }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- USER (FIXED) -->
        <div class="border-top pt-3 px-2">
            <div id="userDropdown" class="d-flex justify-content-between align-items-center p-2 rounded"
                style="cursor:pointer;background:#f3f4f6;">
                <span>{{ Str::limit(auth()->user()->name, 13) }}</span>
                <i class="bi bi-chevron-down" id="userArrow"></i>
            </div>

            <div id="userMenu" class="mt-2" style="display:none;">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-sm btn-danger w-100">
                        <i class="bi bi-box-arrow-right me-1"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- CONTENT -->
    <div class="content" id="content">
        @yield('content')
    </div>

    <!-- JS -->
    <script>
        $(document).ready(function () {

            /* RESTORE STATE */
            if (localStorage.getItem('sidebar') === 'collapsed') {
                $('#sidebar').addClass('collapsed');
                $('#content').addClass('collapsed');
            }

            if (localStorage.getItem('forms') === 'open') {
                $('#formsList').show();
                $('#formsArrow').addClass('rotate');
            }

            if (localStorage.getItem('user') === 'open') {
                $('#userMenu').show();
                $('#userArrow').addClass('rotate');
            }

            /* SIDEBAR */
            $('#toggleSidebar').click(function () {
                $('#sidebar').toggleClass('collapsed show');
                $('#content').toggleClass('collapsed');
                localStorage.setItem('sidebar',
                    $('#sidebar').hasClass('collapsed') ? 'collapsed' : 'open');
            });

            /* FORMS */
            $('#toggleFormsList').click(function () {
                $('#formsList').slideToggle(200);
                $('#formsArrow').toggleClass('rotate');
                localStorage.setItem('forms',
                    $('#formsList').is(':visible') ? 'open' : 'closed');
            });

            /* USER */
            $('#userDropdown').click(function () {
                $('#userMenu').slideToggle(150);
                $('#userArrow').toggleClass('rotate');
                localStorage.setItem('user',
                    $('#userMenu').is(':visible') ? 'open' : 'closed');
            });

        });
    </script>

    @stack('scripts')
</body>

</html>
