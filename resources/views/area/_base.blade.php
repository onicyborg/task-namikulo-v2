<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Namikulo' }}</title>

    @php
        $otikaAssets = 'https://otika.namikulo.com/assets';
        $otikaThemePalette = ['white', 'cyan', 'black', 'purple', 'orange', 'green', 'red'];
        $otikaTheme = in_array($themeColor ?? 'white', $otikaThemePalette, true) ? ($themeColor ?? 'white') : 'white';
        $otikaLayout = ($layout ?? 'light') === 'dark' ? 'dark' : 'light';
        $otikaSidebar = ($sidebarColor ?? 'dark-sidebar') === 'light-sidebar' ? 'light' : 'dark';
        $otikaMiniSidebar = (bool) ($miniSidebar ?? false);
        $otikaStickyHeader = (bool) ($stickyHeader ?? true);
    @endphp
    @php
        $isTaskContext = ($page ?? '') === 'task';
        $activeTaskType = null;
        if ($isTaskContext) {
            $activeTaskType = $taskType ?? request()->query('tipe', 'general');
            if (request()->is('task/metopen')) $activeTaskType = 'metopen';
            if (request()->is('task/artikel-ilmiah')) $activeTaskType = 'artikel_ilmiah';
            if (isset($task) && $task->category) $activeTaskType = $task->category->tipe;
        }
    @endphp
    {{-- Custom content helpers stay local; Otika framework assets use the deployed asset host. --}}
    <link rel="stylesheet" href="{{ asset('css/namikulo.css') }}?v={{ filemtime(public_path('css/namikulo.css')) }}">
    <link rel="stylesheet" href="{{ $otikaAssets }}/css/app.min.css">
    <link rel="stylesheet" href="{{ $otikaAssets }}/bundles/fullcalendar/fullcalendar.min.css">
    <link rel="stylesheet" href="{{ $otikaAssets }}/bundles/prism/prism.css">
    <link rel="stylesheet" href="{{ $otikaAssets }}/bundles/datatables/datatables.min.css">
    <link rel="stylesheet" href="{{ $otikaAssets }}/bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ $otikaAssets }}/bundles/select2/dist/css/select2.min.css">
    <link rel="stylesheet" href="{{ $otikaAssets }}/bundles/jquery-selectric/selectric.css">
    <link rel="stylesheet" href="{{ $otikaAssets }}/css/style.css">
    <link rel="stylesheet" href="{{ $otikaAssets }}/css/components.css">
    <link rel="stylesheet" href="{{ $otikaAssets }}/css/custom.css">
    {{-- Otika's FontAwesome files are not served with CORS headers; use a CORS-enabled equivalent. --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    {{-- Load Otika's Nunito family from a CORS-enabled provider. --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap">
    <style>
        /* The deployed Otika CSS references font files without CORS headers. Keep the Otika UI
           intact while preventing the browser from requesting those broken font sources. */
        body,
        body *:not(.fa):not(.fas):not(.far):not(.fab):not(.fal):not(.fad):not(.fc-icon) {
            font-family: "Nunito", "Segoe UI", Arial, sans-serif !important;
        }
        .sidebar-brand > a {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
    </style>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('files/img/namikulo.png') }}">
    @stack('head')
</head>

<body class="{{ $otikaLayout }} {{ $otikaSidebar === 'light' ? 'light-sidebar' : 'dark-sidebar' }} theme-{{ $otikaTheme }}{{ $otikaMiniSidebar ? ' sidebar-mini' : '' }}">
    <div class="loader"></div>
    <div id="app">
        <div class="main-wrapper main-wrapper-1">
            <div class="navbar-bg"></div>
            <nav class="navbar navbar-expand-lg main-navbar{{ $otikaStickyHeader ? ' sticky' : '' }}">
                <div class="form-inline mr-auto">
                    <ul class="navbar-nav mr-3">
                        <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg collapse-btn" aria-label="Buka navigasi"><i data-feather="align-justify"></i></a></li>
                    </ul>
                </div>
                <ul class="navbar-nav navbar-right">
                    <li class="dropdown">
                        <a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user" aria-label="Menu akun"><img alt="Foto {{ Auth::user()->fullname }}" src="{{ asset(Auth::user()->img) }}" class="user-img-radious-style"></a>
                        <div class="dropdown-menu dropdown-menu-right pullDown">
                            <div class="dropdown-title">Halo {{ Auth::user()->fullname }}</div>
                            <a href="{{ url('profil') }}" class="dropdown-item has-icon"><i class="far fa-user"></i> Profil</a>
                            <div class="dropdown-divider"></div>
                            <a href="{{ url('logout') }}" class="dropdown-item has-icon text-danger"><i class="fas fa-sign-out-alt"></i> Keluar</a>
                        </div>
                    </li>
                </ul>
            </nav>

            <div class="main-sidebar sidebar-style-2">
                <aside id="sidebar-wrapper" aria-label="Navigasi utama">
                    <div class="sidebar-brand"><a href="{{ url('dashboard') }}"><img alt="Logo Namikulo" src="{{ asset('files/img/namikulo.png') }}" class="header-logo"><span class="logo-name">NAMIKULO</span></a></div>
                    <ul class="sidebar-menu">
                        <li class="menu-header">Main</li>
                        <li class="{{ ($page ?? '') === 'dashboard' ? 'active' : '' }}"><a href="{{ url('dashboard') }}" class="nav-link"><i data-feather="monitor"></i><span>Dashboard</span></a></li>
                        <li class="{{ ($page ?? '') === 'calendar' ? 'active' : '' }}"><a href="{{ url('calendar') }}" class="nav-link"><i data-feather="calendar"></i><span>Kalender</span></a></li>
                        @if (Auth::user()->role === 'Admin')
                            <li class="{{ ($page ?? '') === 'task-request' ? 'active' : '' }}"><a href="{{ url('task-request') }}" class="nav-link"><i data-feather="inbox"></i><span>Request Task From Client</span></a></li>
                        @endif
                        <li class="dropdown {{ ($page ?? '') === 'task' ? 'active' : '' }}">
                            <a href="#" class="menu-toggle nav-link has-dropdown"><i data-feather="briefcase"></i><span>Task</span></a>
                            <ul class="dropdown-menu">
                                <li class="{{ $activeTaskType === 'general' ? 'active' : '' }}"><a class="nav-link" href="{{ url('task') }}?tipe=general" @if ($activeTaskType === 'general') aria-current="page" @endif>General</a></li>
                                <li class="{{ $activeTaskType === 'metopen' ? 'active' : '' }}"><a class="nav-link" href="{{ url('task/metopen') }}" @if ($activeTaskType === 'metopen') aria-current="page" @endif>Metopen</a></li>
                                <li class="{{ $activeTaskType === 'artikel_ilmiah' ? 'active' : '' }}"><a class="nav-link" href="{{ url('task/artikel-ilmiah') }}" @if ($activeTaskType === 'artikel_ilmiah') aria-current="page" @endif>Artikel Ilmiah</a></li>
                            </ul>
                        </li>
                        @if (Auth::user()->role === 'Admin')
                            <li class="menu-header">Master</li>
                            <li class="{{ ($page ?? '') === 'client' ? 'active' : '' }}"><a href="{{ url('client') }}" class="nav-link"><i data-feather="command"></i><span>Client</span></a></li>
                            <li class="{{ ($page ?? '') === 'worker' ? 'active' : '' }}"><a href="{{ url('worker') }}" class="nav-link"><i data-feather="user"></i><span>Worker</span></a></li>
                            <li class="{{ ($page ?? '') === 'task-category' ? 'active' : '' }}"><a href="{{ url('task-category') }}" class="nav-link"><i data-feather="tag"></i><span>Kategori Task</span></a></li>
                        @endif
                    </ul>
                </aside>
            </div>

            <div class="main-content">
                <section class="section"><div class="section-body">@yield('content')</div></section>
                @yield('modal')
                <div class="settingSidebar" aria-label="Pengaturan tampilan">
                    <a href="javascript:void(0)" class="settingPanelToggle" aria-label="Buka pengaturan tampilan"><i class="fa fa-spin fa-cog"></i></a>
                    <div class="settingSidebar-body ps-container ps-theme-default"><div class="fade show active">
                        <div class="setting-panel-header">Pengaturan tampilan</div>
                        <div class="p-15 border-bottom"><h6 class="font-medium m-b-10">Layout</h6><div class="selectgroup layout-color w-50">
                            <label class="selectgroup-item"><input type="radio" name="layout" value="1" class="selectgroup-input-radio select-layout"><span class="selectgroup-button">Light</span></label>
                            <label class="selectgroup-item"><input type="radio" name="layout" value="2" class="selectgroup-input-radio select-layout"><span class="selectgroup-button">Dark</span></label>
                        </div></div>
                        <div class="p-15 border-bottom"><h6 class="font-medium m-b-10">Sidebar Color</h6><div class="selectgroup selectgroup-pills sidebar-color">
                            <label class="selectgroup-item"><input type="radio" name="sidebar" value="1" class="selectgroup-input select-sidebar"><span class="selectgroup-button selectgroup-button-icon" title="Light Sidebar"><i class="fas fa-sun"></i></span></label>
                            <label class="selectgroup-item"><input type="radio" name="sidebar" value="2" class="selectgroup-input select-sidebar"><span class="selectgroup-button selectgroup-button-icon" title="Dark Sidebar"><i class="fas fa-moon"></i></span></label>
                        </div></div>
                        <div class="p-15 border-bottom"><h6 class="font-medium m-b-10">Color Theme</h6><ul class="choose-theme list-unstyled mb-0">
                            <li title="white"><div class="white"></div></li><li title="cyan"><div class="cyan"></div></li><li title="black"><div class="black"></div></li><li title="purple"><div class="purple"></div></li><li title="orange"><div class="orange"></div></li><li title="green"><div class="green"></div></li><li title="red"><div class="red"></div></li>
                        </ul></div>
                        <div class="p-15 border-bottom"><label class="m-b-0"><input type="checkbox" class="custom-switch-input" id="mini_sidebar_setting"><span class="custom-switch-indicator"></span><span class="control-label p-l-10">Mini Sidebar</span></label></div>
                        <div class="p-15 border-bottom"><label class="m-b-0"><input type="checkbox" class="custom-switch-input" id="sticky_header_setting"><span class="custom-switch-indicator"></span><span class="control-label p-l-10">Sticky Header</span></label></div>
                        <div class="mt-4 mb-4 p-3 align-center rt-sidebar-last-ele"><a href="#" class="btn btn-icon icon-left btn-primary btn-restore-theme"><i class="fas fa-undo"></i> Restore Default</a></div>
                    </div></div>
                </div>
            </div>

            <footer class="main-footer"><div class="footer-left m-0"><p class="m-0">Copyrights © {{ date('Y') }} Namikulo</p></div><div class="footer-right"><p class="m-0">Task Management</p></div></footer>
        </div>
    </div>

    <script src="{{ $otikaAssets }}/js/app.min.js"></script>
    <script src="{{ $otikaAssets }}/bundles/prism/prism.js"></script>
    <script src="{{ $otikaAssets }}/bundles/datatables/datatables.min.js"></script>
    <script src="{{ $otikaAssets }}/bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js"></script>
    <script src="{{ $otikaAssets }}/bundles/jquery-ui/jquery-ui.min.js"></script>
    <script src="{{ $otikaAssets }}/bundles/select2/dist/js/select2.full.min.js"></script>
    <script src="{{ $otikaAssets }}/bundles/fullcalendar/fullcalendar.min.js"></script>
    <script src="{{ $otikaAssets }}/js/scripts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.all.min.js"></script>
    <script>
        function errorAjaxResponse(response) { var message = response && response.status === 419 ? 'Halaman kadaluarsa, silakan reload halaman.' : 'Terjadi kesalahan. Silakan coba lagi.'; if (response && response.responseJSON && response.responseJSON.message) message = response.responseJSON.message; Swal.fire({ icon: 'error', title: 'Gagal', text: message }); }
        function idrFormat(angka, format) { var value = Number(angka || 0).toLocaleString('id-ID'); return format ? 'Rp. ' + value : value; }
        function formatNumber(input) { input.value = String(input.value || '').replace(/[^0-9]/g, ''); }

        $(function () {
            var defaults = { layout: 'light', sidebar: 'dark', color: 'white', miniSidebar: false, stickyHeader: true };
            var preference = {
                layout: @json($otikaLayout),
                sidebar: @json($otikaSidebar),
                color: @json($otikaTheme),
                miniSidebar: @json($otikaMiniSidebar),
                stickyHeader: @json($otikaStickyHeader)
            };
            var themeColors = ['white', 'cyan', 'black', 'purple', 'orange', 'green', 'red'];
            function applyPreference() {
                var body = document.body;
                body.classList.remove('light', 'dark', 'light-sidebar', 'dark-sidebar');
                themeColors.forEach(function (color) { body.classList.remove('theme-' + color); });
                body.classList.add(preference.layout === 'dark' ? 'dark' : 'light');
                body.classList.add(preference.sidebar === 'light' ? 'light-sidebar' : 'dark-sidebar');
                body.classList.add('theme-' + (themeColors.indexOf(preference.color) > -1 ? preference.color : 'white'));
                $('.select-layout[value="' + (preference.layout === 'dark' ? '2' : '1') + '"]').prop('checked', true);
                $('.select-sidebar[value="' + (preference.sidebar === 'light' ? '1' : '2') + '"]').prop('checked', true);
                $('.choose-theme li').removeClass('active').filter('[title="' + preference.color + '"]').addClass('active');
                $('#mini_sidebar_setting').prop('checked', preference.miniSidebar);
                $('#sticky_header_setting').prop('checked', preference.stickyHeader);
                $('.main-navbar').toggleClass('sticky', preference.stickyHeader);
            }
            function savePreference() { $.ajax({ url: '{{ url('theme/preferences') }}', method: 'POST', contentType: 'application/json', headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }, data: JSON.stringify(preference) }); }
            function updatePreference(key, value) { preference[key] = value; applyPreference(); savePreference(); }
            applyPreference();
            $('.select-layout').on('change', function () { updatePreference('layout', this.value === '2' ? 'dark' : 'light'); });
            $('.select-sidebar').on('change', function () { updatePreference('sidebar', this.value === '1' ? 'light' : 'dark'); });
            $('.choose-theme li').on('click', function (event) { event.preventDefault(); updatePreference('color', $(this).attr('title')); });
            $('#mini_sidebar_setting').on('change', function () { updatePreference('miniSidebar', this.checked); });
            $('#sticky_header_setting').on('change', function () { updatePreference('stickyHeader', this.checked); });
            $('.btn-restore-theme').on('click', function (event) { event.preventDefault(); preference = Object.assign({}, defaults); applyPreference(); savePreference(); });
        });
    </script>
    @stack('js')
</body>
</html>
