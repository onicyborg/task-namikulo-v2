<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk - Namikulo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.min.css">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('files/img/namikulo.png') }}">
    <style>
        :root { --primary: #2196f3; --primary-dark: #0d47a1; --ink: #0d47a1; --muted: #546e7a; --line: #bbdefb; }
        * { box-sizing: border-box; }
        body { min-height: 100vh; margin: 0; background: #e3f2fd; color: #263238; font-family: 'Plus Jakarta Sans', sans-serif; }
        .login-page { display: grid; min-height: 100vh; grid-template-columns: minmax(0, 1.1fr) minmax(360px, .9fr); }
        .login-intro { display: flex; flex-direction: column; justify-content: space-between; padding: clamp(32px, 6vw, 88px); background: #0d47a1; color: #fff; }
        .brand { display: inline-flex; align-items: center; gap: 11px; color: #fff; font-size: 17px; font-weight: 800; letter-spacing: 0; text-decoration: none; }
        .brand:hover, .brand:focus, .brand:active { color: #fff; text-decoration: none; cursor: pointer; }
        .brand img { width: 36px; height: 36px; border-radius: 7px; background: #fff; object-fit: contain; }
        .intro-copy { max-width: 540px; margin: auto 0; }
        .intro-kicker { margin: 0 0 16px; color: #90caf9; font-size: 12px; font-weight: 800; letter-spacing: 0; text-transform: uppercase; }
        .intro-copy h1 { max-width: 500px; margin: 0; color: #fff; font-size: clamp(32px, 4vw, 52px); font-weight: 800; line-height: 1.15; letter-spacing: 0; }
        .intro-copy p { max-width: 450px; margin: 20px 0 0; color: #dbeafe; font-size: 16px; line-height: 1.7; }
        .intro-footer { color: #bbdefb; font-size: 12px; }
        .login-panel { display: flex; align-items: center; justify-content: center; padding: 32px; background: #fff; }
        .login-card { width: 100%; max-width: 400px; }
        .login-card h2 { margin: 0; color: var(--ink); font-size: 26px; font-weight: 800; }
        .login-card > p { margin: 8px 0 30px; color: var(--muted); font-size: 14px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { margin-bottom: 7px; color: #263238; font-size: 13px; font-weight: 700; }
        .input-wrap { position: relative; }
        .input-wrap svg { position: absolute; top: 50%; left: 14px; width: 18px; height: 18px; color: #78909c; transform: translateY(-50%); pointer-events: none; }
        .input-wrap .password-toggle { position: absolute; top: 50%; right: 10px; display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; padding: 0; border: 0; border-radius: 5px; background: transparent; color: #78909c; transform: translateY(-50%); cursor: pointer; }
        .input-wrap .password-toggle:hover { background: #e3f2fd; color: var(--primary-dark); }
        .input-wrap .password-toggle:focus-visible { outline: 3px solid rgba(33,150,243,.2); outline-offset: 1px; }
        .input-wrap .password-toggle svg { position: static; width: 17px; height: 17px; transform: none; pointer-events: none; }
        .form-control { height: 46px; border: 1px solid #bbdefb; border-radius: 6px; padding-left: 44px; color: #263238; font-size: 14px; }
        .input-wrap .form-control[type=password], .input-wrap .form-control[type=text] { padding-right: 48px; }
        .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(33,150,243,.16); }
        .form-check { display: flex; align-items: center; gap: 8px; min-height: 24px; margin: 4px 0 24px; padding-left: 0; }
        .form-check-input { position: static; flex: 0 0 17px; width: 17px; height: 17px; margin: 0; accent-color: var(--primary); }
        .form-check-label { display: inline-block; margin: 0; color: var(--muted); font-size: 13px; line-height: 1.4; cursor: pointer; }
        .login-btn { display: inline-flex; align-items: center; justify-content: center; gap: 9px; width: 100%; min-height: 46px; border: 1px solid var(--primary); border-radius: 6px; background: var(--primary); color: #fff; font-size: 14px; font-weight: 800; transition: background .16s ease; }
        .login-btn:hover { background: var(--primary-dark); border-color: var(--primary-dark); }
        .login-btn:disabled { cursor: not-allowed; opacity: .68; }
        .spinner { display: none; width: 17px; height: 17px; border: 2px solid rgba(255,255,255,.45); border-top-color: #fff; border-radius: 50%; animation: spin .8s linear infinite; }
        .login-btn.loading .spinner { display: block; }
        .login-btn.loading .btn-text { display: none; }
        .invalid-feedback { margin-top: 6px; font-size: 12px; }
        @keyframes spin { to { transform: rotate(360deg); } }
        @media (max-width: 767.98px) { .login-page { grid-template-columns: 1fr; } .login-intro { display: none; } .login-panel { min-height: 100vh; padding: 24px; } }
        @media (prefers-reduced-motion: reduce) { * { animation-duration: .01ms !important; transition-duration: .01ms !important; } }
    </style>
</head>
<body>
    <div class="login-page">
        <section class="login-intro" aria-label="Tentang Namikulo">
            <a class="brand" href="{{ url('/') }}"><img src="{{ asset('files/img/namikulo.png') }}" alt="Logo Namikulo"><span>NAMIKULO</span></a>
            <div class="intro-copy">
                <p class="intro-kicker">Task management system</p>
                <h1>Semua pekerjaan, lebih terarah.</h1>
                <p>Kelola task, jadwal, dan kolaborasi tim Anda dari satu ruang kerja yang jelas dan rapi.</p>
            </div>
            <span class="intro-footer">&copy; {{ date('Y') }} Namikulo</span>
        </section>
        <main class="login-panel">
            <div class="login-card">
                <h2>Selamat datang</h2>
                <p>Masuk untuk melanjutkan ke ruang kerja Anda.</p>
                <form id="form_login" novalidate>
                    <div class="form-group">
                        <label for="username">Username</label>
                        <div class="input-wrap"><i data-feather="user"></i><input id="username" type="text" class="form-control" name="username" autocomplete="username" autofocus placeholder="Masukkan username"></div>
                        <div id="error_username" class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-wrap"><i data-feather="lock"></i><input id="password" type="password" class="form-control" name="password" autocomplete="current-password" placeholder="Masukkan password"><button type="button" class="password-toggle" id="passwordToggle" aria-label="Tampilkan password" title="Tampilkan password"><i data-feather="eye" id="passwordEye"></i><i data-feather="eye-off" id="passwordEyeOff" class="d-none"></i></button></div>
                        <div id="error_password" class="invalid-feedback"></div>
                    </div>
                    <div class="form-check"><input type="checkbox" class="form-check-input" id="remember" name="remember"><label class="form-check-label" for="remember">Ingat saya</label></div>
                    <button type="submit" id="btn_login" class="login-btn"><span class="btn-text">Masuk</span><span class="spinner" aria-hidden="true"></span></button>
                </form>
            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons@4.29.2/dist/feather.min.js"></script>
    <script>
        feather.replace();
        document.getElementById("passwordToggle").addEventListener("click", function () {
            var password = document.getElementById("password");
            var isHidden = password.type === "password";
            password.type = isHidden ? "text" : "password";
            document.getElementById("passwordEye").classList.toggle("d-none", !isHidden);
            document.getElementById("passwordEyeOff").classList.toggle("d-none", isHidden);
            this.setAttribute("aria-label", isHidden ? "Sembunyikan password" : "Tampilkan password");
            this.setAttribute("title", isHidden ? "Sembunyikan password" : "Tampilkan password");
        });
        $('#form_login').submit(function (e) {
            e.preventDefault();
            $('#username, #password').removeClass('is-invalid');
            $('#error_username, #error_password').empty();
            var btn = $('#btn_login').addClass('loading').prop('disabled', true);
            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                cache: false, contentType: false, processData: false, method: 'post', url: "{{ url('auth') }}", data: new FormData(this), dataType: 'json',
                success: function (response) {
                    btn.removeClass('loading').prop('disabled', false);
                    if (response.status == '1') { Swal.fire({icon: 'success', title: 'Berhasil', text: response.msg, timer: 900, showConfirmButton: false}).then(function () { window.location.href = response.url; }); }
                    else { Swal.fire({icon: 'error', title: 'Login gagal', text: response.msg}); }
                },
                error: function (response) {
                    btn.removeClass('loading').prop('disabled', false);
                    if (response.status === 422 && response.responseJSON && response.responseJSON.errors) {
                        var errors = response.responseJSON.errors;
                        ['username', 'password'].forEach(function (field) { if (errors[field]) { $('#' + field).addClass('is-invalid'); $('#error_' + field).text(errors[field][0]).show(); } });
                    } else if (response.status === 419) { Swal.fire({icon: 'error', title: 'Sesi berakhir', text: 'Halaman akan dimuat ulang.'}).then(function () { location.reload(); }); }
                    else { Swal.fire({icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan. Silakan coba lagi.'}); }
                }
            });
        });
    </script>
    @if (session('error'))
        <script>Swal.fire({icon: 'error', title: 'Gagal', text: @json(session('error'))});</script>
    @endif
</body>
</html>
