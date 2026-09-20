<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk - Namikulo</title>
    @php($otikaAssets = 'https://otika.namikulo.com/assets')
    <link rel="stylesheet" href="{{ $otikaAssets }}/css/app.min.css">
    <link rel="stylesheet" href="{{ $otikaAssets }}/css/style.css">
    <link rel="stylesheet" href="{{ $otikaAssets }}/css/components.css">
    <link rel="stylesheet" href="{{ $otikaAssets }}/css/custom.css">
    <link rel="stylesheet" href="{{ $otikaAssets }}/fontawesome/css/all.min.css">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('files/img/namikulo.png') }}">
</head>

<body>
    <div class="loader"></div>
    <div id="app">
        <section class="section">
            <div class="container"><div class="row align-items-center" style="min-height: 100vh;">
                <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-5 offset-lg-3 col-xl-4 offset-xl-4">
                    <div class="card card-primary">
                        <div class="card-body">
                            <div class="d-flex justify-content-center align-items-center mb-4">
                                <img src="{{ asset('files/img/namikulo.png') }}" alt="Logo Namikulo" class="mr-2" style="width: 28px; height: 28px;">
                                <h3 class="m-0">NAMIKULO</h3>
                            </div>
                            <p class="text-center text-muted mb-4">Masuk ke Task Management</p>
                            <form id="form_login" novalidate>
                                <div class="form-group"><label for="username">Username</label><input id="username" type="text" class="form-control" name="username" autocomplete="username" autofocus><div id="error_username" class="invalid-feedback"></div></div>
                                <div class="form-group"><label for="password">Password</label><input id="password" type="password" class="form-control" name="password" autocomplete="current-password"><div id="error_password" class="invalid-feedback"></div></div>
                                <button type="submit" id="btn_login" class="btn btn-primary btn-lg btn-block"><span class="btn-label">Masuk</span><span class="spinner-border spinner-border-sm d-none" aria-hidden="true"></span></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div></div>
        </section>
    </div>
    <script src="{{ $otikaAssets }}/js/app.min.js"></script>
    <script src="{{ $otikaAssets }}/js/scripts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.all.min.js"></script>
    <script>
        $('#form_login').on('submit', function (event) {
            event.preventDefault();
            $('#username, #password').removeClass('is-invalid');
            $('#error_username, #error_password').empty();
            var btn = $('#btn_login').prop('disabled', true);
            btn.find('.btn-label').addClass('d-none');
            btn.find('.spinner-border').removeClass('d-none');
            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                cache: false, contentType: false, processData: false, method: 'post', url: '{{ url('auth') }}', data: new FormData(this), dataType: 'json',
                success: function (response) {
                    if (response.status == '1') { Swal.fire({ icon: 'success', title: 'Berhasil', text: response.msg, timer: 900, showConfirmButton: false }).then(function () { window.location.href = response.url; }); }
                    else { Swal.fire({ icon: 'error', title: 'Login gagal', text: response.msg }); }
                },
                error: function (response) {
                    if (response.status === 422 && response.responseJSON && response.responseJSON.errors) {
                        var errors = response.responseJSON.errors;
                        ['username', 'password'].forEach(function (field) { if (errors[field]) { $('#' + field).addClass('is-invalid'); $('#error_' + field).text(errors[field][0]); } });
                    } else { Swal.fire({ icon: 'error', title: 'Gagal', text: response.status === 419 ? 'Sesi berakhir, silakan muat ulang halaman.' : 'Terjadi kesalahan. Silakan coba lagi.' }); }
                },
                complete: function () { btn.prop('disabled', false); btn.find('.btn-label').removeClass('d-none'); btn.find('.spinner-border').addClass('d-none'); }
            });
        });
    </script>
    @if (session('error'))<script>Swal.fire({ icon: 'error', title: 'Gagal', text: @json(session('error')) });</script>@endif
</body>
</html>
