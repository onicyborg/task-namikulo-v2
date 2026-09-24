@extends('area._base')
@push('head')
    <style>
        .country-flag { flex: 0 0 auto; display: block; object-fit: cover; border-radius: 3px; box-shadow: 0 0 0 1px rgba(18,38,58,.12); }
        .select2-container--default .select2-dropdown {
            min-width: 210px;
            border: 1px solid #dce6ef;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 16px 34px rgba(18,38,58,.16);
        }
        .select2-container--default .select2-search--dropdown { padding: 10px; background: #fff; }
        .select2-container--default .select2-search--dropdown .select2-search__field {
            height: 38px;
            border: 1px solid #dce6ef;
            border-radius: 9px;
            padding: 0 11px;
            color: #12263a;
            font-size: 14px;
            outline: none;
        }
        .select2-container--default .select2-search--dropdown .select2-search__field:focus { border-color: #1464f4; box-shadow: 0 0 0 3px rgba(20,100,244,.12); }
        .select2-container--default .select2-results__option { min-height: 42px; padding: 10px 12px; color: #12263a; font-size: 14px; }
        .select2-container--default .select2-results__option--highlighted[aria-selected] { background: #1464f4; color: #fff; }
        .select2-container--default .select2-results__option[aria-selected=true] { background: #eef4ff; color: #0b4ec7; font-weight: 700; }
        .modal .input-group { display: flex; align-items: stretch; flex-wrap: nowrap; }
        .modal .input-group > .form-control { min-width: 0; border-top-left-radius: 0; border-bottom-left-radius: 0; }
        .country-picker { position: relative; display: flex; flex: 0 0 148px; min-width: 148px !important; }
        .country-native-select { display: none !important; }
        .country-picker-trigger { flex: 1 1 auto; width: 100%; height: auto; min-height: 100%; display: flex; align-items: center; gap: 9px; padding: 0 30px 0 14px; position: relative; border: 1px solid #dce6ef; border-right: 0; border-radius: 12px 0 0 12px; background: #f5f9fc; color: #12263a; font: 700 14px/1 'DM Sans', sans-serif; cursor: pointer; text-align: left; }
        .country-picker-trigger:hover, .country-picker-trigger[aria-expanded=true] { background: #eef4ff; border-color: #1464f4; }
        .country-picker-trigger:focus-visible { outline: 3px solid rgba(20,100,244,.18); outline-offset: 1px; z-index: 1; }
        .country-picker-trigger::after { content: ''; width: 7px; height: 7px; position: absolute; right: 12px; top: 50%; border-right: 1.5px solid #6b7c8f; border-bottom: 1.5px solid #6b7c8f; transform: translateY(-70%) rotate(45deg); transition: transform .15s ease; }
        .country-picker-trigger[aria-expanded=true]::after { transform: translateY(-30%) rotate(225deg); }
        .country-picker-popover { position: fixed; width: 260px; max-height: min(360px, calc(100vh - 24px)); display: flex; flex-direction: column; z-index: 2100; overflow: hidden; border: 1px solid #dce6ef; border-radius: 14px; background: #fff; box-shadow: 0 18px 42px rgba(18,38,58,.2); }
        .country-picker-search-wrap { padding: 10px; border-bottom: 1px solid #eef2f6; background: #fff; }
        .country-picker-search { width: 100%; height: 40px; border: 1px solid #dce6ef; border-radius: 9px; padding: 0 12px; color: #12263a; font: 14px 'DM Sans', sans-serif; outline: none; }
        .country-picker-search:focus { border-color: #1464f4; box-shadow: 0 0 0 3px rgba(20,100,244,.12); }
        .country-picker-list { overflow-y: auto; padding: 5px; overscroll-behavior: contain; }
        .country-picker-option { width: 100%; min-height: 42px; display: flex; align-items: center; gap: 10px; border: 0; border-radius: 8px; padding: 9px 10px; background: transparent; color: #12263a; font: 14px/1.2 'DM Sans', sans-serif; cursor: pointer; text-align: left; }
        .country-picker-option:hover, .country-picker-option.is-selected { background: #eef4ff; color: #0b4ec7; font-weight: 700; }
        .country-picker-option:focus-visible { outline: 2px solid #1464f4; outline-offset: -2px; }
        .country-picker-option .country-flag { width: 28px; height: 18px; }
        .country-picker-empty { padding: 16px 12px; color: #6b7c8f; font-size: 13px; text-align: center; }
        @media (max-width: 575px) { .country-picker { flex-basis: 128px; min-width: 128px !important; } .country-picker-trigger { padding-left: 10px; font-size: 13px; } }
    </style>
@endpush
@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2 class="page-title">Data Client</h2>
            <p class="page-subtitle">Kelola data client dan informasi kontak</p>
        </div>
        <div class="page-header-right">
            <button href="#" class="btn btn-primary" data-toggle="modal" data-target="#modal_add">
                <i data-feather="plus" style="width: 16px; height: 16px;"></i> Tambah Client
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-toolbar">
                <div class="table-toolbar-left">
                    <div class="search-box">
                        <i data-feather="search" style="width: 16px; height: 16px;"></i>
                        <input type="text" id="search_filter" class="form-control" placeholder="Cari client..." />
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table" id="table-1" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Customer</th>
                            <th>Handphone</th>
                            <th>Jenis Kelamin</th>
                            <th>Asal</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('modal')
    <div class="modal fade" id="modal_add" role="dialog" aria-labelledby="formModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="formModal">
                        <i data-feather="user-plus" style="width: 18px; height: 18px; vertical-align: middle; margin-right: 8px;"></i>
                        Tambah Client
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span class="modal-close-mark" aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form_add">
                        <div class="form-group">
                            <label class="form-label">Customer</label>
                            <input type="text" class="form-control" name="customer" id="customer_add" placeholder="Nama customer">
                            <div id="error_customer_add" class="invalid-feedback"></div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Handphone</label>
                            <div class="input-group">
                                <div class="input-group-prepend country-picker" data-country-picker><select name="handphone_country" id="handphone_country_add" class="country-native-select" aria-label="Negara nomor telepon">@foreach ($countries as $country)<option value="{{ $country['code'] }}" data-country-name="{{ $country['name'] }}" data-dial-code="{{ $country['dial_code'] }}" @selected($country['code'] === 'ID')>{{ $country['name'] }} {{ $country['dial_code'] }}</option>@endforeach</select></div>
                                <input type="text" class="form-control" name="handphone" id="handphone_add" inputmode="numeric" pattern="[0-9]*" placeholder="81398238734">
                            </div>
                            <small class="form-text text-muted">Pilih negara, lalu isi nomor lokal tanpa kode negara.</small>
                            <div id="error_handphone_add" class="invalid-feedback"></div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="jk" id="jk_add" class="form-control">
                                <option></option>
                                <option value="Pria">Pria</option>
                                <option value="Wanita">Wanita</option>
                            </select>
                            <div id="error_jk_add" class="invalid-feedback"></div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Asal</label>
                            <input type="text" class="form-control" name="asal" id="asal_add" placeholder="Kota atau asal daerah">
                            <div id="error_asal_add" class="invalid-feedback"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" id="btn_add" class="btn btn-primary">
                                <i data-feather="save" style="width: 16px; height: 16px; vertical-align: middle; margin-right: 4px;"></i>
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal_edit" role="dialog" aria-labelledby="formModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="formModal">
                        <i data-feather="edit-2" style="width: 18px; height: 18px; vertical-align: middle; margin-right: 8px;"></i>
                        Edit Client
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span class="modal-close-mark" aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form_edit">
                        <input type="hidden" name="id" id="id_edit">
                        <div class="form-group">
                            <label class="form-label">Customer</label>
                            <input type="text" class="form-control" name="customer" id="customer_edit" placeholder="Nama customer">
                            <div id="error_customer_edit" class="invalid-feedback"></div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Handphone</label>
                            <div class="input-group">
                                <div class="input-group-prepend country-picker" data-country-picker><select name="handphone_country" id="handphone_country_edit" class="country-native-select" aria-label="Negara nomor telepon">@foreach ($countries as $country)<option value="{{ $country['code'] }}" data-country-name="{{ $country['name'] }}" data-dial-code="{{ $country['dial_code'] }}">{{ $country['name'] }} {{ $country['dial_code'] }}</option>@endforeach</select></div>
                                <input type="text" class="form-control" name="handphone" id="handphone_edit" inputmode="numeric" pattern="[0-9]*" placeholder="81398238734">
                            </div>
                            <small class="form-text text-muted">Pilih negara, lalu isi nomor lokal tanpa kode negara.</small>
                            <div id="error_handphone_edit" class="invalid-feedback"></div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="jk" id="jk_edit" class="form-control">
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
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" id="btn_edit" class="btn btn-primary">
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
        $(document).ready(function() {
            $('#jk_add').select2({width: '100%', placeholder: 'Pilih', dropdownParent: $('#modal_add')});
            $('#jk_edit').select2({width: '100%', placeholder: 'Pilih', dropdownParent: $('#modal_edit')});
            var flagUrl = function(code) { return 'https://flagcdn.com/w40/' + String(code).toLowerCase() + '.png'; };
            var countryPickers = [];
            var closeCountryPickers = function(except) {
                countryPickers.forEach(function(picker) { if (picker !== except) picker.close(); });
            };
            var initCountryPicker = function(select) {
                var wrapper = select.closest('[data-country-picker]');
                var trigger = document.createElement('button');
                trigger.type = 'button';
                trigger.className = 'country-picker-trigger';
                trigger.setAttribute('aria-haspopup', 'listbox');
                trigger.setAttribute('aria-expanded', 'false');
                trigger.setAttribute('aria-label', 'Pilih negara nomor telepon');
                wrapper.appendChild(trigger);

                var popover = document.createElement('div');
                popover.className = 'country-picker-popover';
                popover.setAttribute('role', 'dialog');
                popover.setAttribute('aria-label', 'Daftar negara');
                var searchWrap = document.createElement('div');
                searchWrap.className = 'country-picker-search-wrap';
                var search = document.createElement('input');
                search.type = 'search';
                search.className = 'country-picker-search';
                search.placeholder = 'Cari negara atau kode...';
                search.setAttribute('aria-label', 'Cari negara atau kode negara');
                searchWrap.appendChild(search);
                var list = document.createElement('div');
                list.className = 'country-picker-list';
                list.setAttribute('role', 'listbox');
                popover.appendChild(searchWrap);
                popover.appendChild(list);
                document.body.appendChild(popover);

                var getOptions = function() { return Array.prototype.slice.call(select.options); };
                var updateTrigger = function() {
                    var option = select.options[select.selectedIndex];
                    if (!option) return;
                    trigger.replaceChildren();
                    var img = document.createElement('img');
                    img.className = 'country-flag'; img.width = 28; img.height = 18; img.alt = '';
                    img.src = flagUrl(option.value);
                    var code = document.createElement('span');
                    code.textContent = option.dataset.dialCode || option.textContent;
                    trigger.appendChild(img); trigger.appendChild(code);
                    trigger.title = option.dataset.countryName || option.textContent;
                };
                var renderOptions = function(query) {
                    list.replaceChildren();
                    var needle = String(query || '').trim().toLowerCase();
                    var matches = getOptions().filter(function(option) {
                        var searchable = (option.dataset.countryName || '') + ' ' + (option.dataset.dialCode || '') + ' ' + option.value;
                        return searchable.toLowerCase().indexOf(needle) !== -1;
                    });
                    if (!matches.length) {
                        var empty = document.createElement('div'); empty.className = 'country-picker-empty'; empty.textContent = 'Negara tidak ditemukan'; list.appendChild(empty); return;
                    }
                    matches.forEach(function(option) {
                        var item = document.createElement('button');
                        item.type = 'button'; item.className = 'country-picker-option'; item.setAttribute('role', 'option');
                        item.setAttribute('aria-selected', option.value === select.value ? 'true' : 'false');
                        if (option.value === select.value) item.classList.add('is-selected');
                        var img = document.createElement('img'); img.className = 'country-flag'; img.width = 28; img.height = 18; img.alt = ''; img.src = flagUrl(option.value);
                        var code = document.createElement('span'); code.textContent = option.dataset.dialCode || option.textContent;
                        item.appendChild(img); item.appendChild(code);
                        item.title = option.dataset.countryName || option.textContent;
                        item.addEventListener('click', function() { select.value = option.value; select.dispatchEvent(new Event('change', {bubbles: true})); picker.close(); trigger.focus(); });
                        list.appendChild(item);
                    });
                };
                var position = function() {
                    var rect = trigger.getBoundingClientRect();
                    popover.style.left = Math.max(12, Math.min(rect.left, window.innerWidth - popover.offsetWidth - 12)) + 'px';
                    popover.style.top = (rect.bottom + 6) + 'px';
                    var popoverRect = popover.getBoundingClientRect();
                    if (popoverRect.bottom > window.innerHeight - 12 && rect.top - popoverRect.height - 6 > 12) popover.style.top = (rect.top - popoverRect.height - 6) + 'px';
                };
                var picker = { close: function() { popover.hidden = true; trigger.setAttribute('aria-expanded', 'false'); } };
                var open = function() { closeCountryPickers(picker); popover.hidden = false; search.value = ''; renderOptions(''); position(); trigger.setAttribute('aria-expanded', 'true'); requestAnimationFrame(function() { position(); search.focus(); }); };
                trigger.addEventListener('click', function() { popover.hidden ? open() : picker.close(); });
                search.addEventListener('input', function() { renderOptions(search.value); });
                search.addEventListener('keydown', function(event) { if (event.key === 'Escape') { picker.close(); trigger.focus(); } });
                window.addEventListener('resize', function() { if (!popover.hidden) position(); });
                window.addEventListener('scroll', function() { if (!popover.hidden) position(); }, true);
                document.addEventListener('click', function(event) { if (!wrapper.contains(event.target) && !popover.contains(event.target)) picker.close(); });
                select.addEventListener('change', function() { updateTrigger(); if (!popover.hidden) renderOptions(search.value); });
                popover.hidden = true; updateTrigger(); countryPickers.push(picker);
            };
            initCountryPicker(document.getElementById('handphone_country_add'));
            initCountryPicker(document.getElementById('handphone_country_edit'));
        });

        var datatable = $("#table-1").DataTable({
            "dom": "<'dt--top-section'>" + "<''tr>" + "<'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--length-info d-flex justify-content-center align-middle 'l<'dt--pages-count ml-1'i>><'dt--pagination mt-sm-0 mt-3'p>>",
            "oLanguage": {"sLengthMenu": "_MENU_"},
            processing: true,
            serverSide: true,
            ajax: {url: '{{ url('client/list') }}'},
            columns: [
                {"data": "DT_RowIndex", "name": "DT_RowIndex", "orderable": false, "searchable": false},
                {data: 'customer', name: 'customer'},
                {data: 'handphone', name: 'handphone'},
                {data: 'jk', name: 'jk'},
                {data: 'asal', name: 'asal'},
                {"orderable": false, "searchable": false, "data": null, "render": function(data, type, row, meta) {
                    var url_detail = '{{ asset('client/detail') }}/' + row.id;
                    var html = '<div class="text-nowrap">';
                    html += '<a href="' + url_detail + '" class="btn btn-icon btn-sm btn-primary"><i class="fas fa-file-alt" style="width: 16px"></i></a>';
                    html += '<a href="javascript:;" onclick="editData(\'' + encodeURIComponent(JSON.stringify(row)) + '\')" class="btn btn-icon btn-sm btn-warning mx-1"><i class="fas fa-edit" style="width: 16px"></i></a>';
                    html += '<a href="javascript:;" onclick="deleteData(\'' + row.id + '\')" class="btn btn-icon btn-sm btn-danger"><i class="fas fa-trash" style="width: 16px"></i></a>';
                    html += '</div>';
                    return html;
                }},
            ],
            order: []
        });

        $("#search_filter").on("keyup", function() { datatable.search($(this).val()).draw(); });

        function reloadDataTable() { datatable.ajax.reload(); }

        $("#form_add").submit(function(e) {
            $("#btn_add").prop("disabled", true);
            $("#customer_add").removeClass('is-invalid');
            $("#handphone_add").removeClass('is-invalid');
            $("#jk_add").removeClass('is-invalid');
            $("#asal_add").removeClass('is-invalid');
            var formdata = new FormData(this);
            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                cache: false, contentType: false, processData: false, method: 'post',
                url: "{{ url('client/add') }}", data: formdata, dataType: 'json',
                success: function(response) {
                    $("#btn_add").prop("disabled", false);
                    if (response.status == "1") {
                        swal.fire({icon: "success", title: 'Berhasil', text: response.msg, showConfirmButton: true, timer: 900}).then((result) => {
                            if (result.isConfirmed) {
                                $('#form_add')[0].reset(); $('#jk_add').val('').change(); $('#modal_add').modal('hide'); reloadDataTable();
                            }
                        });
                        setTimeout(function() { $('#form_add')[0].reset(); $('#jk_add').val('').change(); $('#modal_add').modal('hide'); reloadDataTable(); }, 900);
                    } else {
                        swal.fire({icon: "error", title: 'Gagal !', text: response.msg, showConfirmButton: true, timer: 900});
                    }
                },
                error: function(response) {
                    $("#btn_add").prop("disabled", false);
                    if (response.status == 422) {
                        let errorResponse = JSON.parse(response.responseText);
                        if (errorResponse.errors && errorResponse.errors.customer) { $("#customer_add").addClass('is-invalid'); $("#error_customer_add").html(errorResponse.errors.customer[0]); }
                        if (errorResponse.errors && errorResponse.errors.handphone) { $("#handphone_add").addClass('is-invalid'); $("#error_handphone_add").html(errorResponse.errors.handphone[0]); }
                        if (errorResponse.errors && errorResponse.errors.jk) { $("#jk_add").addClass('is-invalid'); $("#error_jk_add").html(errorResponse.errors.jk[0]); }
                        if (errorResponse.errors && errorResponse.errors.asal) { $("#asal_add").addClass('is-invalid'); $("#error_asal_add").html(errorResponse.errors.asal[0]); }
                    } else { errorAjaxResponse(response); }
                }
            });
            e.preventDefault();
        });

        function editData(data) {
            var rowData = JSON.parse(decodeURIComponent(data));
            $("#id_edit").val(rowData.id);
            $("#customer_edit").val(rowData.customer);
            var country = rowData.handphone_country || 'ID';
            $("#handphone_country_edit").val(country).trigger('change');
            var dialCode = $('#handphone_country_edit option:selected').data('dial-code') || '+62';
            var phoneValue = rowData.handphone || '';
            if (phoneValue.indexOf(dialCode) === 0) phoneValue = phoneValue.substring(dialCode.length);
            $("#handphone_edit").val(phoneValue.replace(/^0/, ''));
            $("#asal_edit").val(rowData.asal);
            $("#jk_edit").val(rowData.jk).change();
            $("#modal_edit").modal("show");
        }

        $("#form_edit").submit(function(e) {
            $("#btn_edit").prop("disabled", true);
            var formdata = new FormData(this);
            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                cache: false, contentType: false, processData: false, method: 'post',
                url: "{{ url('client/edit') }}", data: formdata, dataType: 'json',
                success: function(response) {
                    $("#btn_edit").prop("disabled", false);
                    if (response.status == "1") {
                        swal.fire({icon: "success", title: 'Berhasil', text: response.msg, showConfirmButton: true, timer: 900}).then((result) => {
                            if (result.isConfirmed) { $('#form_edit')[0].reset(); $('#jk_edit').val('').change(); $('#modal_edit').modal('hide'); reloadDataTable(); }
                        });
                        setTimeout(function() { $('#form_edit')[0].reset(); $('#jk_edit').val('').change(); $('#modal_edit').modal('hide'); reloadDataTable(); }, 900);
                    } else { swal.fire({icon: "error", title: 'Gagal !', text: response.msg, showConfirmButton: true, timer: 900}); }
                },
                error: function(response) {
                    $("#btn_edit").prop("disabled", false);
                    if (response.status == 422) { errorAjaxResponse(response); }
                    else { errorAjaxResponse(response); }
                }
            });
            e.preventDefault();
        });

        function deleteData(id) {
            swal.fire({
                title: 'Apakah anda yakin??', text: "Anda tidak dapat mengembalikan ini !!", icon: "warning",
                showCancelButton: true, confirmButtonText: 'Hapus!', cancelButtonText: 'Batal',
                customClass: { confirmButton: 'swal2-delete-confirm', cancelButton: 'swal2-delete-cancel' }, buttonsStyling: false
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        type: "POST", url: "{{ url('client/delete') }}", data: 'id=' + id,
                        success: function(response) {
                            if (response.status == "1") {
                                swal.fire({icon: "success", title: 'Berhasil', text: response.msg, showConfirmButton: true, timer: 900}).then((result) => {
                                    if (result.isConfirmed) { reloadDataTable(); }
                                });
                                setTimeout(function() { reloadDataTable(); }, 900);
                            } else { swal.fire("Error!", response.msg, "error"); }
                        },
                        error: function(response) { errorAjaxResponse(response); }
                    });
                }
            });
        }
    </script>
@endpush
