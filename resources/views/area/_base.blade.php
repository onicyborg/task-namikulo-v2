<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Namikulo' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/datatables.net-bs4@1.13.11/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="{{ asset('css/namikulo.css') }}?v={{ filemtime(public_path('css/namikulo.css')) }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('files/img/namikulo.png') }}">
    @stack('head')
</head>
<body class="{{ $layout ?? 'light' }} {{ $sidebarColor ?? 'dark-sidebar' }} theme-{{ $themeColor ?? 'blue' }}{{ ($miniSidebar ?? false) ? ' sidebar-mini' : '' }}{{ !($stickyHeader ?? true) ? ' topbar-static' : '' }}">
    <a class="skip-link" href="#main-content">Langsung ke konten utama</a>
    <div class="app-shell">
        <aside class="app-sidebar" id="appSidebar" aria-label="Navigasi utama">
            <div class="brand-bar">
                <a href="{{ url('dashboard') }}" class="brand-link" aria-label="Namikulo dashboard">
                    <img src="{{ asset('files/img/namikulo.png') }}" alt="Logo Namikulo" class="brand-logo">
                    <span>NAMIKULO</span>
                </a>
                <button class="sidebar-close d-lg-none" type="button" id="sidebarClose" aria-label="Tutup navigasi"><i data-feather="x"></i></button>
            </div>
            <nav class="sidebar-nav">
                <p class="nav-section-label">Workspace</p>
                <a href="{{ url('dashboard') }}" class="sidebar-link {{ ($page ?? '') == 'dashboard' ? 'is-active' : '' }}" {{ ($page ?? '') == 'dashboard' ? 'aria-current=page' : '' }}><i data-feather="grid"></i><span>Dashboard</span></a>
                <a href="{{ url('calendar') }}" class="sidebar-link {{ ($page ?? '') == 'calendar' ? 'is-active' : '' }}" {{ ($page ?? '') == 'calendar' ? 'aria-current=page' : '' }}><i data-feather="calendar"></i><span>Kalender</span></a>
                <a href="{{ url('task') }}" class="sidebar-link {{ ($page ?? '') == 'task' ? 'is-active' : '' }}" {{ ($page ?? '') == 'task' ? 'aria-current=page' : '' }}><i data-feather="clipboard"></i><span>Task</span></a>
                @if (Auth::user()->role == 'Admin')
                    <p class="nav-section-label nav-section-label--spaced">Data master</p>
                    <a href="{{ url('client') }}" class="sidebar-link {{ ($page ?? '') == 'client' ? 'is-active' : '' }}" {{ ($page ?? '') == 'client' ? 'aria-current=page' : '' }}><i data-feather="users"></i><span>Client</span></a>
                    <a href="{{ url('worker') }}" class="sidebar-link {{ ($page ?? '') == 'worker' ? 'is-active' : '' }}" {{ ($page ?? '') == 'worker' ? 'aria-current=page' : '' }}><i data-feather="user-check"></i><span>Worker</span></a>
                @endif
            </nav>
            <div class="sidebar-account">
                <img src="{{ asset(Auth::user()->img) }}" alt="" class="sidebar-avatar">
                <div class="sidebar-account-copy"><strong>{{ Auth::user()->fullname }}</strong><span>{{ Auth::user()->role }}</span></div>
            </div>
        </aside>
        <div class="app-main">
            <header class="app-topbar">
                <button class="icon-button" type="button" id="sidebarToggle" aria-label="Buka navigasi" aria-controls="appSidebar" aria-expanded="false"><i data-feather="menu"></i></button>
                <div class="topbar-title d-none d-sm-block"><span>Task Management</span></div>
                <button class="icon-button theme-settings-toggle" type="button" id="themeSettingsToggle" aria-label="Buka pengaturan tampilan" title="Pengaturan tampilan" aria-controls="themeSettings" aria-expanded="false"><i data-feather="settings"></i></button>
                <div class="ml-auto dropdown">
                    <button class="profile-menu" type="button" id="profileMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img src="{{ asset(Auth::user()->img) }}" alt="Foto profil {{ Auth::user()->fullname }}"><span class="d-none d-md-inline">{{ Auth::user()->fullname }}</span><i data-feather="chevron-down" class="d-none d-md-inline"></i></button>
                    <div class="dropdown-menu dropdown-menu-right profile-dropdown" aria-labelledby="profileMenu">
                        <div class="profile-dropdown-header"><strong>{{ Auth::user()->fullname }}</strong><span>{{ Auth::user()->role }}</span></div>
                        <a class="dropdown-item" href="{{ url('profil') }}"><i data-feather="user"></i> Profil saya</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item text-danger" href="{{ url('logout') }}"><i data-feather="log-out"></i> Keluar</a>
                    </div>
                </div>
            </header>
            <main class="app-content" id="main-content" tabindex="-1">@yield('content')</main>
            @yield('modal')
            <footer class="app-footer"><span>&copy; {{ date('Y') }} Namikulo</span><span>Task Management System</span></footer>
        </div>
    </div>
    <div class="sidebar-backdrop" id="sidebarBackdrop" aria-hidden="true"></div>
    <div class="theme-panel-backdrop" id="themePanelBackdrop" aria-hidden="true"></div>
    <aside class="theme-panel" id="themeSettings" aria-labelledby="themeSettingsTitle" aria-hidden="true">
        <div class="theme-panel-header">
            <div>
                <p>Preferensi pribadi</p>
                <h2 id="themeSettingsTitle">Tampilan</h2>
            </div>
            <button class="icon-button" type="button" id="themeSettingsClose" aria-label="Tutup pengaturan tampilan"><i data-feather="x"></i></button>
        </div>
        <form class="theme-panel-body" id="themePreferenceForm">
            <fieldset class="theme-control-group">
                <legend>Mode tampilan</legend>
                <div class="theme-segmented-control">
                    <label><input type="radio" name="layout" value="light" data-theme-preference="layout"><span><i data-feather="sun" aria-hidden="true"></i> Terang</span></label>
                    <label><input type="radio" name="layout" value="dark" data-theme-preference="layout"><span><i data-feather="moon" aria-hidden="true"></i> Gelap</span></label>
                </div>
            </fieldset>
            <fieldset class="theme-control-group">
                <legend>Warna sidebar</legend>
                <div class="theme-segmented-control">
                    <label><input type="radio" name="sidebar" value="dark" data-theme-preference="sidebar"><span><i data-feather="square" aria-hidden="true"></i> Gelap</span></label>
                    <label><input type="radio" name="sidebar" value="light" data-theme-preference="sidebar"><span><i data-feather="square" aria-hidden="true"></i> Terang</span></label>
                </div>
            </fieldset>
            <fieldset class="theme-control-group">
                <legend>Warna aksen</legend>
                <div class="theme-color-grid" role="radiogroup" aria-label="Warna aksen">
                    <label title="Biru"><input type="radio" name="color" value="blue" data-theme-preference="color"><span class="theme-swatch theme-swatch--blue"><i data-feather="check" aria-hidden="true"></i><b>Biru</b></span></label>
                    <label title="Langit"><input type="radio" name="color" value="cyan" data-theme-preference="color"><span class="theme-swatch theme-swatch--cyan"><i data-feather="check" aria-hidden="true"></i><b>Langit</b></span></label>
                    <label title="Navy"><input type="radio" name="color" value="purple" data-theme-preference="color"><span class="theme-swatch theme-swatch--purple"><i data-feather="check" aria-hidden="true"></i><b>Navy</b></span></label>
                    <label title="Ice"><input type="radio" name="color" value="green" data-theme-preference="color"><span class="theme-swatch theme-swatch--green"><i data-feather="check" aria-hidden="true"></i><b>Ice</b></span></label>
                </div>
            </fieldset>
            <div class="theme-control-group theme-toggle-list">
                <label class="theme-toggle-row" for="miniSidebarPreference"><span><strong>Sidebar ringkas</strong><small>Hanya ikon pada layar lebar</small></span><input type="checkbox" id="miniSidebarPreference" data-theme-preference="miniSidebar"><i aria-hidden="true"></i></label>
                <label class="theme-toggle-row" for="stickyHeaderPreference"><span><strong>Header sticky</strong><small>Header tetap terlihat saat scroll</small></span><input type="checkbox" id="stickyHeaderPreference" data-theme-preference="stickyHeader"><i aria-hidden="true"></i></label>
            </div>
        </form>
        <div class="theme-panel-footer"><button class="btn btn-secondary" type="button" id="themeReset">Kembalikan default</button></div>
        <span class="sr-only" id="themeStatus" aria-live="polite"></span>
    </aside>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/datatables.net@1.13.11/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/datatables.net-bs4@1.13.11/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons@4.29.2/dist/feather.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
    <script>
        window.swal = window.Swal;
        function errorAjaxResponse(response) { var message = 'Terjadi kesalahan. Silakan coba lagi.'; if (response && response.responseJSON && response.responseJSON.message) message = response.responseJSON.message; else if (response && response.responseText) { try { message = JSON.parse(response.responseText).message || message; } catch (e) {} } Swal.fire({ icon: 'error', title: 'Gagal', text: message }); }
        function idrFormat(angka, format) { var value = Number(angka || 0).toLocaleString('id-ID'); return format ? 'Rp ' + value : value; }
        function formatNumber(input) { input.value = String(input.value || '').replace(/[^0-9]/g, ''); }
        (function () {
            var allowedColors = ['blue', 'cyan', 'purple', 'green', 'orange', 'red', 'pink'];
            var defaults = { layout: 'light', sidebar: 'dark', color: 'blue', miniSidebar: false, stickyHeader: true };
            var preferences = {
                layout: @json($layout ?? 'light'),
                sidebar: @json(($sidebarColor ?? 'dark-sidebar') === 'light-sidebar' ? 'light' : 'dark'),
                color: @json($themeColor ?? 'blue'),
                miniSidebar: @json($miniSidebar ?? false),
                stickyHeader: @json($stickyHeader ?? true)
            };
            var body = document.body;
            var panel = document.getElementById('themeSettings');
            var panelBackdrop = document.getElementById('themePanelBackdrop');
            var panelToggle = document.getElementById('themeSettingsToggle');
            var panelClose = document.getElementById('themeSettingsClose');
            var status = document.getElementById('themeStatus');

            function normalize(theme) {
                return {
                    layout: theme.layout === 'dark' ? 'dark' : 'light',
                    sidebar: theme.sidebar === 'light' ? 'light' : 'dark',
                    color: allowedColors.indexOf(theme.color) > -1 ? theme.color : defaults.color,
                    miniSidebar: Boolean(theme.miniSidebar),
                    stickyHeader: theme.stickyHeader !== false
                };
            }
            function renderControls() {
                document.querySelectorAll('[data-theme-preference]').forEach(function (control) {
                    var key = control.getAttribute('data-theme-preference');
                    control.checked = control.type === 'checkbox' ? preferences[key] : control.value === preferences[key];
                });
            }
            function applyTheme() {
                body.classList.toggle('dark', preferences.layout === 'dark');
                body.classList.toggle('light', preferences.layout !== 'dark');
                body.classList.toggle('light-sidebar', preferences.sidebar === 'light');
                body.classList.toggle('dark-sidebar', preferences.sidebar !== 'light');
                body.classList.toggle('sidebar-mini', preferences.miniSidebar);
                body.classList.toggle('topbar-static', !preferences.stickyHeader);
                allowedColors.forEach(function (color) { body.classList.remove('theme-' + color); });
                body.classList.add('theme-' + preferences.color);
                renderControls();
            }
            function saveTheme() {
                fetch('{{ url('theme/preferences') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 'Accept': 'application/json' },
                    body: JSON.stringify(preferences)
                }).then(function (response) {
                    if (!response.ok) throw new Error('Unable to save preferences');
                    return response.json();
                }).then(function () {
                    status.textContent = 'Preferensi tampilan disimpan.';
                }).catch(function () {
                    status.textContent = 'Preferensi belum dapat disimpan.';
                });
            }
            function updatePreference(key, value) {
                preferences[key] = value;
                preferences = normalize(preferences);
                applyTheme();
                saveTheme();
            }
            function setPanel(open) {
                body.classList.toggle('theme-panel-open', open);
                panel.setAttribute('aria-hidden', open ? 'false' : 'true');
                panelToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
                if (open) panelClose.focus();
                else panelToggle.focus();
            }

            preferences = normalize(preferences);
            applyTheme();
            document.querySelectorAll('[data-theme-preference]').forEach(function (control) {
                control.addEventListener('change', function () {
                    updatePreference(control.getAttribute('data-theme-preference'), control.type === 'checkbox' ? control.checked : control.value);
                });
            });
            panelToggle.addEventListener('click', function () { setPanel(!body.classList.contains('theme-panel-open')); });
            panelClose.addEventListener('click', function () { setPanel(false); });
            panelBackdrop.addEventListener('click', function () { setPanel(false); });
            document.getElementById('themeReset').addEventListener('click', function () { preferences = Object.assign({}, defaults); applyTheme(); saveTheme(); });
            window.addEventListener('keydown', function (event) { if (event.key === 'Escape' && body.classList.contains('theme-panel-open')) setPanel(false); });
        }());        (function () { var backdrop = document.getElementById('sidebarBackdrop'); var toggle = document.getElementById('sidebarToggle'); var close = document.getElementById('sidebarClose'); function setSidebar(open) { document.body.classList.toggle('sidebar-open', open); toggle.setAttribute('aria-expanded', open ? 'true' : 'false'); } toggle.addEventListener('click', function () { setSidebar(!document.body.classList.contains('sidebar-open')); }); backdrop.addEventListener('click', function () { setSidebar(false); }); if (close) close.addEventListener('click', function () { setSidebar(false); }); window.addEventListener('keydown', function (event) { if (event.key === 'Escape') setSidebar(false); }); window.addEventListener('resize', function () { if (window.innerWidth >= 992) setSidebar(false); }); }());
        document.addEventListener('DOMContentLoaded', function () { feather.replace(); });
    </script>
    @stack('js')
</body>
</html>
