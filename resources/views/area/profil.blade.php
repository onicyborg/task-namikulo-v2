@extends('area._base')
@push('head')
@endpush
@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2 class="page-title">Profil</h2>
            <p class="page-subtitle">Informasi akun dan data diri</p>
        </div>
    </div>

    <div class="profile-layout">
        <div class="profile-card card">
            <div class="profile-card-header">
                <div class="profile-avatar-wrapper">
                    <img src="{{ asset(Auth::user()->img) }}" class="profile-avatar" alt="">
                </div>
                <div class="profile-identity">
                    <h3 class="profile-name">{{ Auth::User()->fullname }}</h3>
                    <span class="profile-role badge">{{ Auth::User()->jk ?? '-' }}</span>
                </div>
            </div>
            <div class="profile-card-body">
                <div class="profile-info-list">
                    <div class="profile-info-item">
                        <div class="profile-info-icon">
                            <i data-feather="user" style="width: 16px; height: 16px;"></i>
                        </div>
                        <div class="profile-info-content">
                            <span class="profile-info-label">Username</span>
                            <span class="profile-info-value">{{ Auth::User()->username }}</span>
                        </div>
                    </div>
                    <div class="profile-info-item">
                        <div class="profile-info-icon">
                            <i data-feather="user" style="width: 16px; height: 16px;"></i>
                        </div>
                        <div class="profile-info-content">
                            <span class="profile-info-label">Fullname</span>
                            <span class="profile-info-value">{{ Auth::User()->fullname }}</span>
                        </div>
                    </div>
                    <div class="profile-info-item">
                        <div class="profile-info-icon">
                            <i data-feather="smartphone" style="width: 16px; height: 16px;"></i>
                        </div>
                        <div class="profile-info-content">
                            <span class="profile-info-label">Handphone</span>
                            <span class="profile-info-value">{{ Auth::User()->handphone ?? '-' }}</span>
                        </div>
                    </div>
                    <div class="profile-info-item">
                        <div class="profile-info-icon">
                            <i data-feather="map-pin" style="width: 16px; height: 16px;"></i>
                        </div>
                        <div class="profile-info-content">
                            <span class="profile-info-label">Asal</span>
                            <span class="profile-info-value">{{ Auth::User()->asal ?? '-' }}</span>
                        </div>
                    </div>
                    <div class="profile-info-item">
                        <div class="profile-info-icon">
                            <i data-feather="users" style="width: 16px; height: 16px;"></i>
                        </div>
                        <div class="profile-info-content">
                            <span class="profile-info-label">Jenis Kelamin</span>
                            <span class="profile-info-value">{{ Auth::User()->jk ?? '-' }}</span>
                        </div>
                    </div>
                    <div class="profile-info-item">
                        <div class="profile-info-icon">
                            <i data-feather="mail" style="width: 16px; height: 16px;"></i>
                        </div>
                        <div class="profile-info-content">
                            <span class="profile-info-label">Email</span>
                            <span class="profile-info-value">{{ Auth::User()->email ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="profile-card-footer">
                <button type="button" class="btn btn-primary w-100" onclick="editData('{{ Auth::user() }}')">
                    <i data-feather="edit-2" style="width: 16px; height: 16px; vertical-align: middle; margin-right: 6px;"></i>
                    Edit Profil
                </button>
            </div>
        </div>
    </div>
@endsection
@section('modal')
    <div class="modal fade" id="modal_edit" role="dialog" aria-labelledby="formModal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="formModal">
                        <i data-feather="edit-2" style="width: 18px; height: 18px; vertical-align: middle; margin-right: 8px;"></i>
                        Edit Profil
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i data-feather="x" style="width: 18px; height: 18px;"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form_edit">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-label">Fullname</label>
                                    <input type="text" class="form-control" name="fullname" id="fullname_edit" placeholder="Nama lengkap">
                                    <div id="error_fullname_edit" class="invalid-feedback"></div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Handphone</label>
                                    <input type="text" class="form-control" name="handphone" id="handphone_edit" placeholder="Nomor telepon">
                                    <div id="error_handphone_edit" class="invalid-feedback"></div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Jenis Kelamin</label>
                                    <select name="jk" id="jk_edit" class="form-control">
                                        <option></option>
                                        <option value="Pria">Pria</option>
                                        <option value="Wanita">Wanita</option>
                                    </select>
                                    <div id="error_jk_edit" class="invalid-feedback"></div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Asal</label>
                                    <input type="text" class="form-control" name="asal" id="asal_edit" placeholder="Kota atau asal daerah">
                                    <div id="error_asal_edit" class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-label">Username</label>
                                    <input type="text" class="form-control" name="username" id="username_edit" placeholder="Username login">
                                    <div id="error_username_edit" class="invalid-feedback"></div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Email</label>
                                    <input type="text" class="form-control" name="email" id="email_edit" placeholder="Alamat email">
                                    <div id="error_email_edit" class="invalid-feedback"></div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Password</label>
                                    <input type="text" class="form-control" name="password" id="password_edit" placeholder="Kosongkan jika tidak ingin mengubah">
                                    <span class="form-hint">*kosongkan bila tidak ingin mengganti password</span>
                                    <div id="error_password_edit" class="invalid-feedback"></div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Foto</label>
                                    <input type="file" accept=".png, .jpg, .jpeg" class="form-control" name="img" id="img_edit">
                                    <span class="form-hint">*kosongkan bila tidak ingin mengganti foto</span>
                                    <div id="error_img_edit" class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submmit" id="btn_edit" class="btn btn-primary">
                                <i data-feather="save" style="width: 16px; height: 16px; vertical-align: middle; margin-right: 4px;"></i>
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>
        $(document).ready(function() { feather.replace(); $('#jk_edit').select2({width: '100%', placeholder: 'Pilih'}); });

        function editData(data) {
            var rowData = JSON.parse(decodeURIComponent(data));
            $("#fullname_edit").val(rowData.fullname);
            $("#handphone_edit").val(rowData.handphone);
            $("#asal_edit").val(rowData.asal);
            $("#jk_edit").val(rowData.jk).change();
            $("#username_edit").val(rowData.username);
            $("#email_edit").val(rowData.email);
            $("#modal_edit").modal("show");
        }

        $("#form_edit").submit(function(e) {
            $("#btn_edit").prop("disabled", true);
            var formdata = new FormData(this);
            $.ajax({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, cache: false, contentType: false, processData: false, method: 'post', url: "{{ url('profil/edit') }}", data: formdata, dataType: 'json',
                success: function(response) {
                    $("#btn_edit").prop("disabled", false);
                    if (response.status == "1") { swal.fire({icon: "success", title: 'Berhasil', text: response.msg, showConfirmButton: true, timer: 900}).then((result) => { if (result.isConfirmed) { location.reload(); } }); setTimeout(function() { location.reload(); }, 900); }
                    else { swal.fire({icon: "error", title: 'Gagal !', text: response.msg, showConfirmButton: true, timer: 900}); }
                },
                error: function(response) { $("#btn_edit").prop("disabled", false); errorAjaxResponse(response); }
            });
            e.preventDefault();
        });
    </script>
@endpush
