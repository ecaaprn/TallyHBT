@extends('layouts.app')

@section('title', 'Input Data')

@section('content')

<style>
    :root {
        --custom-gray-bg: #eeeeee;
    }
    select:disabled, input:disabled, button:disabled {
        color: #000 !important;
        -webkit-text-fill-color: #000 !important;
        opacity: 1 !important;
        background-color: var(--custom-gray-bg) !important;
    }
    button.btn-secondary:disabled {
        border: 1px solid #dee2e6 !important;
    }
    .filled-bg {
        background-color: var(--custom-gray-bg) !important;
    }
    #page-input .bg-light {
        background-color: var(--custom-gray-bg) !important;
    }
    #page-input .select2-container--default .select2-selection--single {
        border-color: #dee2e6 !important;
    }
    #page-input select.filled-bg + .select2-container--default .select2-selection--single,
    #page-input select:disabled + .select2-container--default .select2-selection--single {
        background-color: var(--custom-gray-bg) !important;
    }
    .time-picker-btn {
        border: none;
        background-color: transparent;
        cursor: pointer;
        line-height: 1;
        padding: 4px !important;
    }
    .time-picker-btn:hover {
        color: var(--bs-primary);
    }
    input[readonly] {
        background-color: var(--custom-gray-bg) !important;
    }
    #WaktuTiba:not(.filled-bg) {
        background-color: #fff !important;
    }
    .select2-container--default .select2-selection--single {
        height: calc(1.5em + 0.5rem + 2px) !important;
        padding: 0.25rem 0.5rem !important;
        display: flex;
        align-items: center;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: calc(1.5em + 0.5rem) !important;
    }
</style>

<div class="container">
    <div id="page-input" class="d-flex flex-column">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('monitoring') }}" class="btn btn-link text-secondary fs-5 p-0" title="Kembali">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <h2 id="form-title-heading" class="h3 fw-bold text-dark mb-0">Masukan Data Baru</h2>
        </div>
        <div class="d-flex align-items-center gap-2">
            <div id="input-realtime-clock" class="fs-6 fw-bold bg-white border rounded-3 px-3 py-2 shadow-sm"></div>
            <a href="#" class="btn btn-danger btn-sm shadow-sm" title="Logout" data-bs-toggle="modal" data-bs-target="#logoutModal">
                <i class="fa-solid fa-right-from-bracket"></i>
            </a>
        </div>
    </div>

    <div id="notification-area"></div>

    <form id="form-joborder" class="p-4 border rounded-3 bg-white shadow-sm mb-4">
        <div class="row g-3 align-items-end mb-3">
            <div class="col-12 col-md-4">
                <label class="form-label fw-bold small">Tanggal</label>
                <input type="text" class="form-control form-control-sm w-100 fw-bold" value="{{ $currentDate->locale('id')->translatedFormat('d F Y') }}" disabled />
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label fw-bold small">Shift</label>
                <input type="text" class="form-control form-control-sm w-100 fw-bold" value="Shift {{ $currentShift }}" disabled />
            </div>
            <div class="col-12 col-md-4">
                <label for="Kapal" class="form-label fw-bold small">Kapal</label>
                <select id="Kapal" name="Kapal" class="form-select form-select-sm select2 w-100" required>
                    <option value="">Pilih</option>
                    <option value="Kapal-A" @if(isset($selectedKapal) && $selectedKapal == 'Kapal-A') selected @endif>Kapal-A</option>
                    <option value="Kapal-B" @if(isset($selectedKapal) && $selectedKapal == 'Kapal-B') selected @endif>Kapal-B</option>
                    <option value="Kapal-C" @if(isset($selectedKapal) && $selectedKapal == 'Kapal-C') selected @endif>Kapal-C</option>
                </select>
            </div>
        </div>

        <div class="row g-3 align-items-end">
            <div class="col-12 col-md-4">
                <label for="NoJobOrder" class="form-label fw-bold small">No Job Order</label>
                <div class="input-group input-group-sm">
                    <input type="text" id="NoJobOrder" name="NoJobOrder" placeholder="Scan atau ketik..." class="form-control form-control-sm" required />
                    <button type="button" id="btn-scan-qr-input" class="btn btn-sm btn-primary" title="Scan QR Code">
                        <i class="fa-solid fa-qrcode"></i>
                    </button>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <label for="NoTruck" class="form-label fw-bold small">No Truck</label>
                <select id="NoTruck" name="NoTruck" class="form-select form-select-sm select2 w-100" required>
                    <option value="">Pilih</option>
                    <option value="Truck-1">Truck-1</option>
                    <option value="Truck-2">Truck-2</option>
                    <option value="Truck-3">Truck-3</option>
                </select>
            </div>
            <div class="col-12 col-md-4">
                <label for="WaktuTiba" class="form-label fw-bold small">Waktu Tiba</label>
                <div class="input-group input-group-sm">
                    <input type="text" id="WaktuTiba" name="WaktuTiba" class="form-control form-control-sm text-center" placeholder="--:--" readonly required>
                    <button type="button" id="btn-set-arrival-time" class="btn btn-sm btn-success">Start</button>
                </div>
            </div>
        </div>

        <div class="row mt-3">
             <div class="col-12">
                <div class="d-grid mt-2">
                    <button type="submit" id="btn-submit-joborder" class="btn btn-primary py-2 shadow-sm">
                        <i class="fa-solid fa-plus me-2"></i>Tambah
                    </button>
                </div>
            </div>
        </div>
    </form>

    <div class="row g-3 align-items-center mb-3">
        <div class="col-12 col-md-auto">
            <div class="form-check form-switch fs-5 d-flex align-items-center">
                <input class="form-check-input" type="checkbox" role="switch" id="toggle-filter" checked>
                <label id="toggle-filter-label" class="fw-bold ms-2" for="toggle-filter">Aktif</label>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="input-group">
                <input type="search" id="search-input" class="form-control" placeholder="Cari No Job Order atau No Truck...">
            </div>
        </div>
    </div>

    <div id="joborder-list" class="row g-3"></div>
</div>

<div class="modal fade" id="qr-scanner-popup" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Scan QR Code</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body"><div id="qr-reader" style="width:100%;"></div></div>
    </div></div>
</div>

<div class="modal fade" id="logoutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"><div class="modal-content rounded-4">
        <div class="modal-header border-0 pb-0">
            <h5 class="modal-title fw-bold">Konfirmasi Keluar</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body text-center py-4">Apakah Anda yakin ingin keluar dari akun?</div>
        <div class="modal-footer d-flex justify-content-center gap-3 border-0 pt-0">
            <form id="logout-form" action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-danger rounded-pill px-4">Ya, Keluar</button>
            </form>
            <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
        </div>
    </div></div>
</div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let jobOrderList = @json($jobOrders);
    const selectedKapal = @json($selectedKapal ?? null);
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const el = id => document.getElementById(id);
    let qrScannerModal = new bootstrap.Modal(el('qr-scanner-popup'));
    let currentlyEditingId = null;
    let html5QrCode = null;

    const showNotification = (message, type = 'error') => {
        const iconMap = { 'success': 'success', 'danger': 'error', 'warning': 'warning', 'info': 'info' };
        Swal.fire({
            position: 'center',
            icon: iconMap[type] || 'error',
            title: message,
            showConfirmButton: false,
            timer: 2000
        });
    };

    const isComplete = item => item.time_list && item.time_list.plugging && item.time_list.open_valve && item.time_list.unplugging && item.time_list.close_valve;
    const formatTimeHM = timeStr => (typeof timeStr === 'string' && timeStr) ? timeStr.substring(0, 5) : null;

    function createTimePickerHTML(waktuKey, item) {
        const timeValue = (waktuKey === 'WaktuTiba') ? item.WaktuTiba : (item.time_list ? item.time_list[waktuKey] : null);
        let [h, m] = timeValue ? formatTimeHM(timeValue).split(':') : ['--', '--'];
        return `
        <div class="time-picker d-flex align-items-center justify-content-center gap-1 p-1 border rounded w-100" data-field="${waktuKey}">
            <div class="d-flex flex-column align-items-center">
                <button type="button" class="time-picker-btn px-1" onclick="adjustTime(event, 'h', 1, this)"><i class="fas fa-chevron-up" style="font-size:0.8rem;"></i></button>
                <span class="fw-bold hour-display" style="width: 25px; text-align: center; font-size:1.2rem;">${h}</span>
                <button type="button" class="time-picker-btn px-1" onclick="adjustTime(event, 'h', -1, this)"><i class="fas fa-chevron-down" style="font-size:0.8rem;"></i></button>
            </div>
            <span class="fw-bold fs-5">:</span>
            <div class="d-flex flex-column align-items-center">
                <button type="button" class="time-picker-btn px-1" onclick="adjustTime(event, 'm', 1, this)"><i class="fas fa-chevron-up" style="font-size:0.8rem;"></i></button>
                <span class="fw-bold minute-display" style="width: 25px; text-align: center; font-size:1.2rem;">${m}</span>
                <button type="button" class="time-picker-btn px-1" onclick="adjustTime(event, 'm', -1, this)"><i class="fas fa-chevron-down" style="font-size:0.8rem;"></i></button>
            </div>
        </div>`;
    }

    window.adjustTime = (event, unit, amount, element) => {
        event.preventDefault();
        const timePicker = element.closest('.time-picker');
        const hourSpan = timePicker.querySelector('.hour-display');
        const minuteSpan = timePicker.querySelector('.minute-display');
        let hours = isNaN(parseInt(hourSpan.textContent)) ? new Date().getHours() : parseInt(hourSpan.textContent);
        let minutes = isNaN(parseInt(minuteSpan.textContent)) ? new Date().getMinutes() : parseInt(minuteSpan.textContent);
        if (unit === 'h') hours = (hours + amount + 24) % 24;
        if (unit === 'm') minutes = (minutes + amount + 60) % 60;
        hourSpan.textContent = String(hours).padStart(2, '0');
        minuteSpan.textContent = String(minutes).padStart(2, '0');
    };

    function createDisplayCardHTML(item) {
        const complete = isComplete(item);
        const isEditingThisCard = (currentlyEditingId === item.id);
        const tl = item.time_list || {};

        const createActionRow = (label, field) => {
            const timeVal = formatTimeHM(tl[field]);
            const hasTime = !!timeVal;
            const sequence = { open_valve: 'plugging', close_valve: 'open_valve', unplugging: 'close_valve' };
            let isBtnDisabled = complete || hasTime;

            if (!isBtnDisabled && !isEditingThisCard) {
                if (field === 'plugging') {
                    isBtnDisabled = !item.WaktuTiba;
                } else {
                    isBtnDisabled = !tl[sequence[field]];
                }
            }

            const btnClass = isBtnDisabled ? 'btn-secondary' : 'btn-success';
            const btnText = hasTime ? 'Selesai' : 'Mulai';
            const actionFunc = `setTime(this, ${item.id}, '${field}')`;
            const bgClass = hasTime || (complete && !isEditingThisCard) ? 'filled-bg' : '';
            return `<div class="form-group">
                        <label class="form-label fw-bold small">${label}</label>
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control form-control-sm text-center ${bgClass}" value="${timeVal || '--:--'}" readonly>
                            <button onclick="${actionFunc}" class="btn ${btnClass} btn-sm" ${isBtnDisabled ? 'disabled' : ''}>${btnText}</button>
                        </div>
                    </div>`;
        };

        const timeFieldsForEdit = { plugging:'Plugging', open_valve: 'Open Valve', close_valve: 'Close Valve', unplugging: 'Unplugging' };
        const timeFieldsHTML = isEditingThisCard ?
            Object.entries(timeFieldsForEdit).map(([field, label]) => `<div class="col-6"><label class="form-label fw-bold small">${label}</label>${createTimePickerHTML(field, item)}</div>`).join('') :
            Object.entries(timeFieldsForEdit).map(([field, label]) => `<div class="col-6">${createActionRow(label, field)}</div>`).join('');

        return `<div class="card-body p-3 d-flex flex-column">
                    <div class="row g-2 align-items-start mb-2">
                        <div class="col-6"><label class="form-label fw-bold small">No Job Order</label><input type="text" class="form-control form-control-sm" value="${item.NoJobOrder}" disabled></div>
                        <div class="col-6 text-end">
                            <span class="badge ${complete ? 'bg-secondary' : 'bg-success'} mb-1 w-50">${complete ? 'NonAktif' : 'Aktif' }</span>
                             <div class="d-flex gap-1 justify-content-end">
                                 ${isEditingThisCard ? `<button onclick="saveCardChanges(${item.id})" class="btn btn-sm btn-success">Simpan</button><button onclick="cancelCardEdit()" class="btn btn-sm btn-secondary">Batal</button>` : `<button onclick="editJobOrder(${item.id})" class="btn btn-sm btn-primary w-50">Edit</button>`}
                             </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label fw-bold small">No Truck</label>
                            ${isEditingThisCard ? `<select id="NoTruck-${item.id}" class="form-select form-select-sm select2"><option value="Truck-1" ${item.NoTruck === 'Truck-1' ? 'selected' : ''}>Truck-1</option><option value="Truck-2" ${item.NoTruck === 'Truck-2' ? 'selected' : ''}>Truck-2</option><option value="Truck-3" ${item.NoTruck === 'Truck-3' ? 'selected' : ''}>Truck-3</option></select>` : `<input type="text" class="form-control form-control-sm" value="${item.NoTruck}" disabled>`}
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label fw-bold small">Waktu Tiba</label>
                                ${isEditingThisCard
                                    ? createTimePickerHTML('WaktuTiba', item)
                                    : `<input type="text" class="form-control form-control-sm text-center filled-bg" value="${formatTimeHM(item.WaktuTiba) || '--:--'}" disabled>`
                                }
                            </div>
                        </div>
                        <div class="col-6"><label class="form-label fw-bold small">No Hose</label><select id="NoHose-${item.id}" class="form-select form-select-sm select2" onchange="updateJobOrderDetail(this, ${item.id}, 'NoHose')" ${isEditingThisCard ? '' : (tl.NoHose || complete ? 'disabled' : '')}><option value="">Pilih</option><option value="Hose-1" ${tl.NoHose === 'Hose-1' ? 'selected' : ''}>Hose-1</option><option value="Hose-2" ${tl.NoHose === 'Hose-2' ? 'selected' : ''}>Hose-2</option><option value="Hose-3" ${tl.NoHose === 'Hose-3' ? 'selected' : ''}>Hose-3</option></select></div>
                        <div class="col-6"><label class="form-label fw-bold small">No Palka</label><select id="NoPalka-${item.id}" class="form-select form-select-sm select2 ${!isEditingThisCard && tl.NoPalka ? 'filled-bg' : ''}" onchange="updateJobOrderDetail(this, ${item.id}, 'NoPalka')" ${isEditingThisCard ? '' : (tl.NoPalka || complete ? 'disabled' : '')}><option value="">Pilih</option><option value="Palka-A" ${tl.NoPalka === 'Palka-A' ? 'selected' : ''}>Palka-A</option><option value="Palka-B" ${tl.NoPalka === 'Palka-B' ? 'selected' : ''}>Palka-B</option><option value="Palka-C" ${tl.NoPalka === 'Palka-C' ? 'selected' : ''}>Palka-C</option></select></div>
                    </div>
                    <hr class="my-2">
                    <div class="row g-2">${timeFieldsHTML}</div>
                </div>`;
    }

    function renderJobOrderList(listToRender) {
        const container = el('joborder-list');
        container.innerHTML = '';
        const sortedList = listToRender.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
        if (sortedList.length === 0) {
            container.innerHTML = `<p class="col-12 text-center text-muted mt-5">Tidak ada data yang cocok.</p>`;
            return;
        }
        sortedList.forEach(item => {
            const div = document.createElement('div');
            div.className = 'col-12 col-lg-6';
            const card = document.createElement('div');
            card.id = `card-${item.id}`;
            card.className = `card h-100 shadow-sm ${currentlyEditingId === item.id ? 'border-primary border-2' : ''}`;
            card.innerHTML = createDisplayCardHTML(item);
            div.appendChild(card);
            container.appendChild(div);
        });
        $('.select2').select2({ width: '100%' });
    }

    function filterAndRender() {
        const showAktif = el('toggle-filter').checked;
        const searchTerm = el('search-input').value.toLowerCase();
        el('toggle-filter-label').textContent = showAktif ? 'Aktif' : 'NonAktif';
        el('toggle-filter-label').className = `fw-bold ms-2 ${showAktif ? 'text-success' : 'text-muted'}`;
        const filteredList = jobOrderList.filter(item => {
            const matchesStatus = showAktif ? !isComplete(item) : isComplete(item);
            if (!matchesStatus) return false;
            if (searchTerm) {
                const joMatch = item.NoJobOrder.toLowerCase().includes(searchTerm);
                const truckMatch = item.NoTruck.toLowerCase().includes(searchTerm);
                return joMatch || truckMatch;
            }
            return true;
        });
        renderJobOrderList(filteredList);
    }

    async function updateJobOrder(jobOrderId, updatedData) {
        const item = jobOrderList.find(jo => jo.id === jobOrderId);
        if (!item) return;

        const originalItemState = JSON.parse(JSON.stringify(item));

        const optimisticData = JSON.parse(JSON.stringify(updatedData));
        if (optimisticData.time_list) {
            optimisticData.time_list = { ...(item.time_list || {}), ...optimisticData.time_list };
        }
        Object.assign(item, optimisticData);
        filterAndRender();

        try {
            const response = await fetch(`/job-orders/${jobOrderId}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
                body: JSON.stringify(updatedData)
            });
            if (!response.ok) {
                const err = await response.json();
                const firstError = err.errors ? Object.values(err.errors)[0][0] : (err.message || 'Gagal menyimpan data.');
                throw new Error(firstError);
            }
            const resultData = await response.json();
            const index = jobOrderList.findIndex(jo => jo.id === jobOrderId);
            if (index !== -1) { jobOrderList[index] = resultData; }
            showNotification('Data berhasil diperbarui!', 'success');
        } catch (error) {
            Object.assign(item, originalItemState);
            showNotification(error.message, 'danger');
        } finally {
            currentlyEditingId = null;
            filterAndRender();
        }
    }

    window.setTime = (button, jobOrderId, field) => {
        button.disabled = true;
        const now = new Date();
        const time = `${String(now.getHours()).padStart(2,'0')}:${String(now.getMinutes()).padStart(2,'0')}`;
        const item = jobOrderList.find(jo => jo.id === jobOrderId);
        const tl = item.time_list || {};
        const sequence = { open_valve: 'plugging', close_valve: 'open_valve', unplugging: 'close_valve' };

        const prevTime = (field === 'plugging') ? formatTimeHM(item.WaktuTiba) : formatTimeHM(tl[sequence[field]]);

        if (prevTime && time <= prevTime) {
            showNotification(`Waktu tidak boleh sama atau lebih kecil dari waktu sebelumnya`, 'warning');
            button.disabled = false;
            return;
        }
        updateJobOrder(jobOrderId, { time_list: { [field]: time } });
    };

    window.updateJobOrderDetail = (select, jobOrderId, field) => {
        updateJobOrder(jobOrderId, { time_list: { [field]: select.value } });
    };
    window.editJobOrder = (id) => { currentlyEditingId = id; filterAndRender(); };
    window.cancelCardEdit = () => { currentlyEditingId = null; filterAndRender(); };

    window.saveCardChanges = async (jobOrderId) => {
        const card = el(`card-${jobOrderId}`);
        const item = jobOrderList.find(jo => jo.id === jobOrderId);
        if (!card || !item) return;

        let updatedData = {
            NoTruck: card.querySelector(`#NoTruck-${jobOrderId}`).value,
            time_list: { ...(item.time_list || {}) }
        };

        card.querySelectorAll('.time-picker').forEach(picker => {
            const field = picker.dataset.field;
            const hours = picker.querySelector('.hour-display').textContent;
            const minutes = picker.querySelector('.minute-display').textContent;
            const timeValue = (hours !== '--' && minutes !== '--') ? `${hours}:${minutes}` : null;

            if (field === 'WaktuTiba') {
                updatedData.WaktuTiba = timeValue;
            } else {
                updatedData.time_list[field] = timeValue;
            }
        });

        updatedData.time_list.NoPalka = card.querySelector(`#NoPalka-${jobOrderId}`).value;
        updatedData.time_list.NoHose = card.querySelector(`#NoHose-${jobOrderId}`).value;

        await updateJobOrder(jobOrderId, updatedData);
    };

    const form = el('form-joborder');
    const arrivalTimeBtn = el('btn-set-arrival-time');
    const waktuTibaInput = el('WaktuTiba');
    const noJobOrderInput = el('NoJobOrder');
    const noTruckSelect = el('NoTruck');
    const kapalSelectEl = el('Kapal');

    const resetFormInputs = () => {
        form.reset();
        waktuTibaInput.classList.remove('filled-bg');
        noJobOrderInput.classList.remove('filled-bg');
        noTruckSelect.classList.remove('filled-bg');

        $('#NoTruck').val('').trigger('change');

        if(selectedKapal) {
            $('#Kapal').val(selectedKapal).trigger('change');
        } else {
            kapalSelectEl.classList.remove('filled-bg');
            $('#Kapal').val('').trigger('change');
            $('#Kapal').prop('disabled', false);
        }

        arrivalTimeBtn.textContent = 'Start';
        arrivalTimeBtn.classList.remove('btn-secondary');
        arrivalTimeBtn.classList.add('btn-success');
        arrivalTimeBtn.disabled = false;
    };

    arrivalTimeBtn.addEventListener('click', function() {
        const now = new Date();
        const time = `${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}`;
        waktuTibaInput.value = time;
        waktuTibaInput.classList.add('filled-bg');
        this.textContent = 'Done';
        this.classList.remove('btn-success');
        this.classList.add('btn-secondary');
        this.disabled = true;
    });

    noJobOrderInput.addEventListener('blur', function() {
        if (this.value) { this.classList.add('filled-bg'); }
        else { this.classList.remove('filled-bg'); }
    });

    $('#NoTruck').on('change', function() {
        if (this.value) { noTruckSelect.classList.add('filled-bg'); }
        else { noTruckSelect.classList.remove('filled-bg'); }
    });

    $('#Kapal').on('change', function() {
        const newKapal = this.value;

        if (selectedKapal && newKapal && newKapal !== selectedKapal) {
            const urlParts = window.location.pathname.split('/');
            const newUrl = `/${urlParts[1]}/${urlParts[2]}/${urlParts[3]}/${newKapal}`;
            window.location.href = newUrl;
            return;
        }

        if (this.value) {
            if (!selectedKapal) {
                kapalSelectEl.classList.add('filled-bg');
                $(this).prop('disabled', true);
            }
        } else {
            kapalSelectEl.classList.remove('filled-bg');
        }
    });

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        const submitBtn = el('btn-submit-joborder');
        const form = e.target;
        const waktuTibaValue = form.WaktuTiba.value;

        const payload = {
            NoJobOrder: form.NoJobOrder.value,
            NoTruck: form.NoTruck.value,
            Kapal: form.Kapal.value,
            Tanggal: '{{ $currentDate->toDateString() }}',
            NoShift: {{ $currentShift }},
            WaktuTiba: waktuTibaValue
        };

        if (!payload.WaktuTiba) {
            return showNotification('Harap atur Waktu Tiba terlebih dahulu!', 'warning');
        }
        if (!payload.NoJobOrder || !payload.NoTruck || !payload.Kapal) {
            return showNotification('Semua field (Kapal, No Job Order, No Truck) tidak boleh kosong!', 'warning');
        }
        if (jobOrderList.some(item => item.NoJobOrder === payload.NoJobOrder && !isComplete(item))) {
            return showNotification(`No Job Order ${payload.NoJobOrder} yang aktif sudah ada.`, 'danger');
        }
        const toggleButtonLoading = (isLoading) => {
            if (isLoading) {
                submitBtn.disabled = true; submitBtn.dataset.originalHtml = submitBtn.innerHTML;
                submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>`;
            } else {
                if (submitBtn.dataset.originalHtml) submitBtn.innerHTML = submitBtn.dataset.originalHtml;
                submitBtn.disabled = false;
            }
        };
        toggleButtonLoading(true);
        try {
            const response = await fetch("{{ route('joborders.store') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
                body: JSON.stringify(payload)
            });
            if (!response.ok) {
                const err = await response.json();
                const firstError = err.errors ? Object.values(err.errors)[0][0] : (err.message || 'Gagal menyimpan data.');
                throw new Error(firstError);
            }
            const resultData = await response.json();
            jobOrderList.push(resultData);
            showNotification('Data berhasil ditambahkan', 'success');
            resetFormInputs();
            filterAndRender();
        } catch (error) {
            showNotification(error.message, 'danger');
        } finally {
            toggleButtonLoading(false);
        }
    });

    el('toggle-filter').onchange = filterAndRender;
    el('search-input').oninput = filterAndRender;
    el('btn-scan-qr-input').onclick = () => {
        qrScannerModal.show();
        if (!html5QrCode) html5QrCode = new Html5Qrcode("qr-reader");
        html5QrCode.start({ facingMode: "environment" }, { fps: 10, qrbox: {width: 250, height: 250} },
            decodedText => {
                el('NoJobOrder').value = decodedText;
                el('NoJobOrder').dispatchEvent(new Event('blur'));
                html5QrCode.stop().then(() => qrScannerModal.hide());
            }, () => {}
        ).catch(err => showNotification("Gagal mengakses kamera: " + err, 'danger'));
    };
    el('qr-scanner-popup').addEventListener('hidden.bs.modal', () => {
        if (html5QrCode && html5QrCode.isScanning) { html5QrCode.stop().catch(() => {}); }
    });
    const clockEl = el('input-realtime-clock');
    if (clockEl) {
        const updateClock = () => { clockEl.textContent = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false }).replace(/\./g, ':'); };
        updateClock(); setInterval(updateClock, 1000);
    }
    $('#NoTruck').select2({ width: '100%', placeholder: 'Pilih' });
    $('#Kapal').select2({ width: '100%', placeholder: 'Pilih' });
    filterAndRender();
});
</script>
@endpush
