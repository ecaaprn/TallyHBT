@extends('layouts.app')

@section('title', 'Input Data')

@section('content')
<style>
    :root {
        --dark-blue: #0158a4;
        --medium-blue: #1C6EA4;
        --light-blue: #33A1E0;
        --accent-yellow: #FFF9AF;
        --custom-gray-bg: #eeeeee;
    }

    #page-input {
        max-width: 800px;
        margin: 0 auto;
        padding: 0 15px;
    }

    #page-input .h3 {
        color: white;
        text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.25);
    }

    #form-joborder,
    .job-card,
    .filter-row-container,
    .form-control,
    .form-select,
    .btn-success,
    .btn-secondary {
        border: 1px solid #ced4da !important;
    }

    .btn-danger {
        border: 1px solid #ced4da !important;
    }

    .btn-danger[data-bs-target="#logoutModal"] {
        border: none !important;
    }

    .form-label {
        color: #000 !important;
        font-weight: 700 !important;
        margin-bottom: 0.3rem;
    }

    .form-control,
    .form-select,
    .select2-selection__rendered {
        color: #000 !important;
        font-weight: 400 !important;
    }

    .form-control:focus,
    .form-select:focus,
    .btn:focus {
        outline: none !important;
        box-shadow: none !important;
    }

    #page-input select:disabled,
    #page-input input:disabled {
        color: #000 !important;
        opacity: 1 !important;
        background-color: var(--custom-gray-bg) !important;
    }

    #page-input button:disabled {
        color: #000 !important;
        opacity: 1 !important;
        background-color: #d1d5db !important;
        border: 1px solid #ced4da !important;
    }

    .filter-row-container {
        background-color: #fff !important;
        padding: 8px 15px;
        border-radius: 8px;
    }

    .status-badge-sync {
        padding: 0.25rem 0.75rem;
        font-size: 0.875rem;
        font-weight: 700;
        border-radius: 4px;
        display: inline-block;
        border: 1px solid #ced4da;
        line-height: 1.5;
        text-align: center;
        min-width: 80px;
    }

    .status-badge-sync.bg-primary {
        background-color: var(--dark-blue) !important;
    }

    #toggle-filter-label {
        font-weight: 800;
        min-width: 70px;
    }

    #toggle-filter-label.status-aktif {
        color: var(--dark-blue);
    }

    #toggle-filter-label.status-nonaktif {
        color: #b91c1c;
    }

    .form-switch .form-check-input {
        width: 2.2em;
        height: 1.1em;
        cursor: pointer;
        border: 1px solid #000 !important;
        background-color: #cbd5e1;
    }

    .form-switch .form-check-input:checked {
        background-color: var(--dark-blue);
        border-color: var(--dark-blue) !important;
    }

    .btn-primary {
        background-color: var(--dark-blue) !important;
        border: 1px solid #000 !important;
    }

    .job-card .btn-primary {
        border: 1px solid #000 !important;
    }

    #page-input .select2-container--default .select2-selection--single {
        height: 38px !important;
        padding: 0.4rem 0.75rem;
        display: flex;
        align-items: center;
        border: 1px solid #ced4da !important;
        border-radius: 4px;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #000 !important;
        line-height: 38px !important;
        padding-left: 0 !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
    }

    .select2-dropdown {
        border: 1px solid #ced4da !important;
    }

    .select2-results__option--highlighted[aria-selected] {
        background-color: var(--dark-blue) !important;
    }

    .select2-container--default .select2-search--dropdown {
        padding: 8px !important;
        border-bottom: 1px solid #ced4da !important;
        background-color: #fff !important;
        display: block !important;
        visibility: visible !important;
    }

    .select2-container--default .select2-search--dropdown .select2-search__field {
        border: 1px solid #ced4da !important;
        border-radius: 4px !important;
        padding: 6px 10px !important;
        width: 100% !important;
        font-size: 0.875rem !important;
        outline: none !important;
        display: block !important;
        visibility: visible !important;
    }

    .pagination .page-link {
        color: white;
        border: none;
        padding: 0.4rem 0.6rem;
        min-width: 32px;
        text-align: center;
        background: transparent;
        font-weight: 500;
        text-decoration: none;
        cursor: pointer;
    }

    .pagination .page-item.aktif .page-link {
        background-color: white;
        color: #0158a4;
        border-radius: 4px;
    }

    .custom-radio .form-check-input {
        display: none;
    }

    .custom-radio .form-check-label {
        position: relative;
        padding-left: 28px;
        cursor: pointer;
        line-height: 20px;
        display: inline-block;
        color: #000;
        font-weight: 700;
    }

    .custom-radio .form-check-label::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        width: 18px;
        height: 18px;
        border: 1px solid #000;
        border-radius: 50%;
        background: #fff;
    }

    .custom-radio .form-check-label::after {
        content: '';
        position: absolute;
        left: 4px;
        top: 4px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: var(--dark-blue);
        transform: scale(0);
        transition: transform 0.2s;
    }

    .custom-radio .form-check-input:checked+.form-check-label::after {
        transform: scale(1);
    }
</style>

<div id="page-input" class="d-flex flex-column py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('monitoring') }}" class="btn btn-link fs-5 p-0 text-white border-0 shadow-none">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <h2 class="h3 fw-bold mb-0">Input Data</h2>
        </div>
        <div class="d-flex align-items-center gap-2">
            <div id="input-realtime-clock" class="fw-bold bg-white border rounded shadow-sm"
                style="height: 31px; display: flex; align-items: center; padding: 0 10px; font-size: 0.875rem; border-color: #ced4da !important;">
            </div>
            <a href="#" class="btn btn-danger btn-sm shadow-sm border-0" title="Logout" data-bs-toggle="modal"
                data-bs-target="#logoutModal"
                style="border: none !important; height: 31px; display: flex; align-items: center;">
                <i class="fa-solid fa-right-from-bracket"></i>
            </a>
        </div>
    </div>

    <form id="form-joborder" class="p-4 border rounded-3 bg-white shadow-sm mb-4">
        @csrf
        <div class="row g-3">
            <div class="col-12 col-md-6">
                <label class="form-label">Tanggal</label>
                <input type="text" class="form-control" value="{{ $currentDate->locale('id')->translatedFormat('d F Y') }}" disabled />
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label">Shift</label>
                <select id="NoShift" name="NoShift" class="form-select select2-basic" required disabled>
                    <option value="1" @if($currentShift == 1) selected @endif>Shift 1</option>
                    <option value="2" @if($currentShift == 2) selected @endif>Shift 2</option>
                    <option value="3" @if($currentShift == 3) selected @endif>Shift 3</option>
                </select>
            </div>
            <div class="col-12 col-md-6">
                <label for="Kapal" class="form-label">Kapal</label>
                <select id="Kapal" name="Kapal" class="form-select select2-basic w-100" required>
                    <option value=""></option>
                    @foreach($masterKapal as $kapalOption)
                    <option value="{{ $kapalOption->nama }}" @if(isset($selectedKapal) && $selectedKapal == $kapalOption->nama) selected @endif>{{ $kapalOption->display }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-6">
                <label for="WaktuTiba" class="form-label">Waktu Tiba</label>
                <div class="input-group">
                    <input type="text" id="WaktuTiba" name="WaktuTiba" class="form-control text-center" placeholder="--:--" readonly required style="border: 1px solid #ced4da !important;">
                    <button type="button" id="btn-set-arrival-time" class="btn btn-success px-4" style="border: 1px solid #ced4da !important;">Start</button>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <label for="NoJobOrder" class="form-label">No Job Order</label>
                <div class="input-group">
                    <input type="text" id="NoJobOrder" name="NoJobOrder" placeholder="Scan atau ketik..." class="form-control" required style="border: 1px solid #ced4da !important;" />
                    <button type="button" id="btn-scan-qr-input" class="btn btn-primary shadow-none" style="border: 1px solid #ced4da !important;">
                        <i class="fa-solid fa-qrcode"></i>
                    </button>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <label for="NoTruck" class="form-label">No Truck</label>
                <select id="NoTruck" name="NoTruck" class="form-select select2-basic w-100" required>
                    <option value=""></option>
                    @foreach($masterTruck as $truckOption)
                    <option value="{{ $truckOption->nama }}">{{ $truckOption->nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="d-grid mt-4">
            <button type="submit" id="btn-submit-joborder" class="btn btn-primary py-2 shadow-sm" style="border: 1px solid #ced4da !important;">
                <i class="fa-solid fa-plus me-2"></i>Tambah Job Order
            </button>
        </div>
    </form>

    <div class="filter-row-container mb-3 shadow-sm" style="border: 1px solid #ced4da !important;">
        <div class="d-flex align-items-center gap-3 flex-nowrap">
            <div class="d-flex align-items-center">
                <div class="form-check form-switch d-flex align-items-center mb-0">
                    <input class="form-check-input" type="checkbox" role="switch" id="toggle-filter" checked>
                    <label id="toggle-filter-label" class="ms-2 status-aktif" for="toggle-filter">Aktif</label>
                </div>
            </div>
            <div class="flex-grow-1">
                <input type="search" id="search-input" class="form-control" placeholder="Cari No Job Order atau No Truck"
                    style="background-color: #f1f5f9 !important; border: 2px solid #64748b !important; font-weight: 700 !important;">
            </div>
            <div style="width: 150px;">
                <select id="shift-filter" class="form-select" style="background-color: #f1f5f9 !important; border: 2px solid #64748b !important; font-weight: 700 !important;">
                    <option value="">Semua Shift</option>
                    <option value="1">Shift 1</option>
                    <option value="2">Shift 2</option>
                    <option value="3">Shift 3</option>
                </select>
            </div>
        </div>
    </div>

    <div id="joborder-list" class="row g-3"></div>
    <div id="pagination-container" class="d-flex justify-content-center mt-4"></div>

    <div class="modal fade" id="qr-scanner-popup" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border: 1px solid #ced4da !important;">
                <div class="modal-header bg-primary text-white" style="border-bottom: 1px solid #ced4da !important;">
                    <h5 class="modal-title fw-bold">Scan QR Code</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <div id="qr-reader" class="w-100"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let jobOrderList = @json($jobOrders);
        const masterData = {
            trucks: @json($masterTruck ?? []),
            hoses: @json($masterHose ?? []),
            palkas: @json($masterPalka ?? [])
        };
        const config = {
            selectedKapal: @json($selectedKapal ?? null),
            csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            currentShift: '{{ $currentShift }}',
            currentDate: '{{ $currentDate->toDateString() }}',
            storeUrl: "{{ route('joborders.store') }}"
        };

        let currentlyEditingId = null;
        let html5QrCode = null;
        let currentPage = 1;
        const itemsPerPage = 8;
        let tempTimeChanges = {};

        const el = id => document.getElementById(id);
        const DOM = {
            jobOrderContainer: el('joborder-list'),
            form: el('form-joborder'),
            waktuTibaInput: el('WaktuTiba'),
            noJobOrderInput: el('NoJobOrder'),
            noTruckSelect: el('NoTruck'),
            kapalSelect: el('Kapal'),
            statusToggle: el('toggle-filter'),
            statusToggleLabel: el('toggle-filter-label'),
            searchInput: el('search-input'),
            shiftFilter: el('shift-filter'),
            arrivalTimeBtn: el('btn-set-arrival-time'),
            submitBtn: el('btn-submit-joborder'),
            qrScannerModalEl: el('qr-scanner-popup'),
            realtimeClock: el('input-realtime-clock'),
            scanQrBtn: el('btn-scan-qr-input'),
        };
        const qrScannerModal = new bootstrap.Modal(DOM.qrScannerModalEl);

        const isComplete = i => i.time_list && i.time_list.plugging && i.time_list.open_valve && i.time_list.unplugging && i.time_list.close_valve;

        const normalizeStatus = s => {
            if (!s && s !== '') return 'Aktif';
            const v = String(s).toLowerCase().trim();
            if (v === 'aktif' || v === '1' || v === 'true') return 'Aktif';
            if (v === 'nonaktif' || v === 'non-aktif' || v === '0' || v === 'false') return 'NonAktif';
            if (v === 'batal') return 'Batal';
            return s;
        };

        const normalizeJobOrder = o => {
            if (!o) return o;
            const copy = { ...o };
            copy.status = normalizeStatus(copy.status);
            if (copy.time_list && typeof copy.time_list === 'object') {
                copy.time_list.kategori = copy.time_list.kategori || 'Non Booster';
                copy.time_list.Catatan = copy.time_list.Catatan || '';
            }
            return copy;
        };

        const formatTimeHM = t => (typeof t === 'string' && t) ? t.substring(0, 5) : null;
        const getCurrentTime = () => {
            const n = new Date();
            return `${String(n.getHours()).padStart(2, '0')}:${String(n.getMinutes()).padStart(2, '0')}`;
        };

        const naturalSort = (a, b) => {
            const getNumber = (str) => {
                const match = str.match(/(\d+)/);
                return match ? parseInt(match[1], 10) : 0;
            };
            const numA = getNumber(a.nama || a);
            const numB = getNumber(b.nama || b);
            if (numA !== numB) return numA - numB;
            return (a.nama || a).localeCompare(b.nama || b);
        };

        const compareTime = (time1, time2) => {
            if (!time1 || !time2) return 0;
            const [h1, m1] = time1.split(':').map(Number);
            const [h2, m2] = time2.split(':').map(Number);
            if (h1 !== h2) return h1 - h2;
            return m1 - m2;
        };

        const addOneMinute = (timeStr) => {
            if (!timeStr || timeStr === '--:--') return null;
            const [hours, minutes] = timeStr.split(':').map(Number);
            let newMinutes = minutes + 1;
            let newHours = hours;
            if (newMinutes >= 60) {
                newMinutes = 0;
                newHours = (newHours + 1) % 24;
            }
            return `${String(newHours).padStart(2, '0')}:${String(newMinutes).padStart(2, '0')}`;
        };

        const showNotification = (msg, type = 'success') => {
            Swal.fire({ position: 'center', icon: type, title: msg, showConfirmButton: false, timer: 1500 });
        };

        const populateAvailableTrucks = () => {
            const activeTrucks = jobOrderList
                .filter(item => normalizeStatus(item.status) === 'Aktif' && !isComplete(item))
                .map(item => item.NoTruck);
            const available = masterData.trucks.filter(t => !activeTrucks.includes(t.nama));
            const $s = $(DOM.noTruckSelect);
            const val = $s.val();
            $s.empty().append('<option value=""></option>');
            available.forEach(t => $s.append(new Option(t.nama, t.nama)));
            $s.val(val).trigger('change.select2');
        };

        async function updateJobOrder(jobOrderId, updatedData) {
            try {
                const response = await fetch(`/job-orders/${jobOrderId}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': config.csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify(updatedData)
                });
                if (!response.ok) throw new Error('Update gagal');
                const res = await response.json();
                const idx = jobOrderList.findIndex(jo => jo.id === jobOrderId);
                jobOrderList[idx] = normalizeJobOrder(res.jobOrder || res);
                render();
                return true;
            } catch (error) {
                showNotification('Gagal memperbarui data', 'error');
                return false;
            }
        }

        function createDisplayCardHTML(item) {
            const complete = isComplete(item), cancelled = normalizeStatus(item.status) === 'Batal', tl = item.time_list || {};
            const isEdit = (currentlyEditingId === item.id);

            let badge = cancelled ?
                '<div class="status-badge-sync bg-danger text-white border">NONAKTIF</div>' :
                (complete ? '<div class="status-badge-sync bg-secondary text-white border">NONAKTIF</div>' : '<div class="status-badge-sync bg-primary text-white border" style="background-color: #0158a4 !important;">AKTIF</div>');

            let actions = isEdit ?
                `<button onclick="saveCardChanges(${item.id})" class="btn btn-sm btn-success fw-bold" style="border: 1px solid #ced4da !important;">Simpan</button>
                 <button onclick="cancelCardEdit()" class="btn btn-sm btn-secondary ms-1 fw-bold" style="border: 1px solid #ced4da !important;">Batal</button>` :
                (!cancelled && !complete ?
                    `<button onclick="openNotePopup(${item.id})" class="btn btn-sm btn-secondary" style="border: 1px solid #ced4da !important;">Catatan</button>
                     <button onclick="editJobOrder(${item.id})" class="btn btn-sm btn-primary ms-1 border-0">Edit</button>
                     <button onclick="cancelJobOrder(${item.id})" class="btn btn-sm btn-danger ms-1" style="border: 1px solid #ced4da !important;">Batal</button>` :
                    (complete ? `<button onclick="editJobOrder(${item.id})" class="btn btn-sm btn-primary border-0">Edit</button>` : ''));

            const activeTrucksForEdit = jobOrderList
                .filter(i => i.id !== item.id && normalizeStatus(i.status) === 'Aktif' && !isComplete(i))
                .map(i => i.NoTruck);
            const truckOpts = masterData.trucks.filter(t => !activeTrucksForEdit.includes(t.nama)).map(t => `<option value="${t.nama}" ${item.NoTruck === t.nama ? 'selected' : ''}>${t.nama}</option>`).join('');

            const rowTime = (label, field) => {
                const val = formatTimeHM(tl[field]), seq = { open_valve: 'plugging', close_valve: 'open_valve', unplugging: 'close_valve' };
                const prevField = seq[field];
                const prevTime = field === 'plugging' ? item.WaktuTiba : (prevField ? tl[prevField] : null);
                let dis = (normalizeStatus(item.status) !== 'Aktif' || complete) || !!val || (field !== 'plugging' && !prevTime);

                if (isEdit) {
                    const tempTimeKey = `${field}-${item.id}`;
                    let timeValue = tempTimeChanges[tempTimeKey] || val;
                    if (!timeValue || timeValue === '--:--' || timeValue.trim() === '') timeValue = '00:00';
                    const [h, m] = timeValue.split(':');
                    return `
                        <div class="col-6 mb-2">
                            <label class="form-label small text-secondary">${label}</label>
                            <div class="time-picker d-flex align-items-center justify-content-center gap-1 p-1 border rounded w-100" style="border-color: #ced4da !important;" data-field="${field}-${item.id}">
                                <div class="d-flex flex-column align-items-center">
                                    <button type="button" class="px-1" onclick="adjustTime(event, 'h', 1, '${field}-${item.id}')" style="border: none !important; background-color: transparent !important; padding: 12px !important; min-width: 40px;"><i class="fas fa-chevron-up"></i></button>
                                    <span class="fw-bold hour-display" style="font-size: 1.5rem !important;">${h || '00'}</span>
                                    <button type="button" class="px-1" onclick="adjustTime(event, 'h', -1, '${field}-${item.id}')" style="border: none !important; background-color: transparent !important; padding: 12px !important; min-width: 40px;"><i class="fas fa-chevron-down"></i></button>
                                </div>
                                <span class="fw-bold fs-5">:</span>
                                <div class="d-flex flex-column align-items-center">
                                    <button type="button" class="px-1" onclick="adjustTime(event, 'm', 1, '${field}-${item.id}')" style="border: none !important; background-color: transparent !important; padding: 12px !important; min-width: 40px;"><i class="fas fa-chevron-up"></i></button>
                                    <span class="fw-bold minute-display" style="font-size: 1.5rem !important;">${m || '00'}</span>
                                    <button type="button" class="px-1" onclick="adjustTime(event, 'm', -1, '${field}-${item.id}')" style="border: none !important; background-color: transparent !important; padding: 12px !important; min-width: 40px;"><i class="fas fa-chevron-down"></i></button>
                                </div>
                            </div>
                        </div>`;
                }

                const buttonText = val ? 'Done' : 'Start';
                return `
                    <div class="col-6 mb-2">
                        <label class="form-label small text-secondary">${label}</label>
                        <div class="input-group">
                            <input type="text" class="form-control text-center ${dis ? 'filled-bg' : 'actionable-input'}" style="border-color: #ced4da !important;" value="${val || '--:--'}" readonly>
                            <button onclick="setTime(this, ${item.id}, '${field}')" class="btn btn-primary fw-bold" ${dis ? 'disabled' : ''} style="background-color:${dis ? '#d1d5db' : '#0158a4'}; border: 1px solid #ced4da !important; color:${dis ? '#000' : 'white'}">${buttonText}</button>
                        </div>
                    </div>`;
            };

            return `
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>${badge}</div>
                        <div class="d-flex gap-1 align-items-center">${cancelled ? '<div class="status-badge-sync bg-danger text-white border">BATAL</div>' : ''}${actions}</div>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-12">
                            <label class="form-label">No Job Order</label>
                            <input type="text" class="form-control" style="border-color: #ced4da !important;" value="${item.NoJobOrder}" disabled>
                        </div>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label">Tanggal</label>
                            <input type="text" class="form-control" style="border-color: #ced4da !important;" value="${item.Tanggal ? (() => {
                                const d = new Date(item.Tanggal);
                                const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                return d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
                            })() : '-'}" disabled>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Shift</label>
                            <input type="text" class="form-control text-center fw-bold" value="Shift ${item.NoShift}" disabled>
                        </div>
                    </div>
                    ${tl.Catatan ? `<div class="mb-3"><label class="form-label">Catatan</label><div style="border: 1px solid #ced4da !important; background-color: #eeeeee !important; padding: 10px; border-radius: 6px; color: #000; font-weight: 400; font-size: 0.875rem;">${tl.Catatan}</div></div>` : ''}
                    <div class="row g-2 mb-2">
                        <div class="col-6"><label class="form-label">No Truck</label>${isEdit ? `<select id="NoTruck-${item.id}" class="form-select select2-basic">${truckOpts}</select>` : `<input type="text" class="form-control" style="border-color: #ced4da !important;" value="${item.NoTruck}" disabled>`}</div>
                        <div class="col-6"><label class="form-label">Waktu Tiba</label>${isEdit ? (() => {
                            const tempTimeKey = `WaktuTiba-${item.id}`;
                            let waktuTiba = tempTimeChanges[tempTimeKey] || formatTimeHM(item.WaktuTiba);
                            if (!waktuTiba || waktuTiba === '--:--' || waktuTiba.trim() === '') waktuTiba = '00:00';
                            const [h, m] = waktuTiba.split(':');
                            return `
                                <div class="time-picker d-flex align-items-center justify-content-center gap-1 p-1 border rounded w-100" style="border-color: #ced4da !important;" data-field="WaktuTiba-${item.id}">
                                    <div class="d-flex flex-column align-items-center">
                                        <button type="button" class="px-1" onclick="adjustTime(event, 'h', 1, 'WaktuTiba-${item.id}')" style="border: none !important; background-color: transparent !important; padding: 12px !important; min-width: 40px;"><i class="fas fa-chevron-up"></i></button>
                                        <span class="fw-bold hour-display" style="font-size: 1.5rem !important;">${h || '00'}</span>
                                        <button type="button" class="px-1" onclick="adjustTime(event, 'h', -1, 'WaktuTiba-${item.id}')" style="border: none !important; background-color: transparent !important; padding: 12px !important; min-width: 40px;"><i class="fas fa-chevron-down"></i></button>
                                    </div>
                                    <span class="fw-bold fs-5">:</span>
                                    <div class="d-flex flex-column align-items-center">
                                        <button type="button" class="px-1" onclick="adjustTime(event, 'm', 1, 'WaktuTiba-${item.id}')" style="border: none !important; background-color: transparent !important; padding: 12px !important; min-width: 40px;"><i class="fas fa-chevron-up"></i></button>
                                        <span class="fw-bold minute-display" style="font-size: 1.5rem !important;">${m || '00'}</span>
                                        <button type="button" class="px-1" onclick="adjustTime(event, 'm', -1, 'WaktuTiba-${item.id}')" style="border: none !important; background-color: transparent !important; padding: 12px !important; min-width: 40px;"><i class="fas fa-chevron-down"></i></button>
                                    </div>
                                </div>`;
                        })() : `<input type="text" class="form-control text-center" style="border-color: #ced4da !important;" value="${formatTimeHM(item.WaktuTiba) || '--:--'}" disabled>`}</div>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-6"><label class="form-label">Hose</label>${isEdit ? `<select id="NoHose-${item.id}" class="form-select select2-basic"><option value=""></option>${[...masterData.hoses].sort(naturalSort).map(h => `<option value="${h.nama}" ${tl.NoHose === h.nama ? 'selected' : ''}>${h.nama}</option>`).join('')}</select>` : `<select id="NoHose-${item.id}" class="form-select select2-basic" onchange="updateJobOrderDetail(this, ${item.id}, 'NoHose')" ${complete || cancelled ? 'disabled' : ''}><option value=""></option>${[...masterData.hoses].sort(naturalSort).map(h => `<option value="${h.nama}" ${tl.NoHose === h.nama ? 'selected' : ''}>${h.nama}</option>`).join('')}</select>`}</div>
                        <div class="col-6"><label class="form-label">Palka</label>${isEdit ? `<select id="NoPalka-${item.id}" class="form-select select2-basic"><option value=""></option>${[...masterData.palkas].sort(naturalSort).map(p => `<option value="${p.nama}" ${tl.NoPalka === p.nama ? 'selected' : ''}>${p.nama}</option>`).join('')}</select>` : `<select id="NoPalka-${item.id}" class="form-select select2-basic" onchange="updateJobOrderDetail(this, ${item.id}, 'NoPalka')" ${complete || cancelled ? 'disabled' : ''}><option value=""></option>${[...masterData.palkas].sort(naturalSort).map(p => `<option value="${p.nama}" ${tl.NoPalka === p.nama ? 'selected' : ''}>${p.nama}</option>`).join('')}</select>`}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label d-block text-start" style="padding-left: 0;">Kategori</label>
                        <div class="d-flex gap-3 justify-content-start">
                            <div class="form-check custom-radio" style="padding-left: 0;"><input class="form-check-input" type="radio" name="k-${item.id}" id="b-${item.id}" value="Booster" ${tl.kategori === 'Booster' ? 'checked' : ''} onchange="${isEdit ? '' : `updateBoosterStatus(this,${item.id})`}" ${!isEdit && (complete || cancelled) ? 'disabled' : ''}><label class="form-check-label small" for="b-${item.id}" style="padding-left: 24px;">Booster</label></div>
                            <div class="form-check custom-radio" style="padding-left: 0;"><input class="form-check-input" type="radio" name="k-${item.id}" id="nb-${item.id}" value="Non Booster" ${tl.kategori !== 'Booster' ? 'checked' : ''} onchange="${isEdit ? '' : `updateBoosterStatus(this,${item.id})`}" ${!isEdit && (complete || cancelled) ? 'disabled' : ''}><label class="form-check-label small" for="nb-${item.id}" style="padding-left: 24px;">Non Booster</label></div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">${rowTime('Plugging', 'plugging')}${rowTime('Open Valve', 'open_valve')}${rowTime('Close Valve', 'close_valve')}${rowTime('Unplugging', 'unplugging')}</div>
                </div>`;
        }

        function render() {
            populateAvailableTrucks();
            const showAktif = DOM.statusToggle.checked, search = DOM.searchInput.value.toLowerCase(), shift = DOM.shiftFilter.value;

            DOM.statusToggleLabel.textContent = showAktif ? 'Aktif' : 'NonAktif';
            DOM.statusToggleLabel.className = `ms-2 ${showAktif ? 'status-aktif' : 'status-nonaktif'}`;

            DOM.jobOrderContainer.innerHTML = '';
            const filtered = jobOrderList.filter(item => {
                const s = normalizeStatus(item.status), completed = isComplete(item), cancelled = (s === 'Batal');
                const matchesKapal = !config.selectedKapal || item.Kapal === config.selectedKapal;
                const matchesTab = showAktif ? (s === 'Aktif' && !completed && !cancelled) : (s === 'NonAktif' || completed || cancelled);
                const matchesShift = !shift || item.NoShift == shift;
                const matchesSearch = !search || (item.NoJobOrder && item.NoJobOrder.toLowerCase().includes(search)) || (item.NoTruck && item.NoTruck.toLowerCase().includes(search));
                return matchesKapal && matchesTab && matchesShift && matchesSearch;
            });

            const sorted = filtered.sort((a, b) => {
                const aComplete = isComplete(a), bComplete = isComplete(b);
                const aCancelled = normalizeStatus(a.status) === 'Batal', bCancelled = normalizeStatus(b.status) === 'Batal';
                const aIncomplete = !aComplete && !aCancelled, bIncomplete = !bComplete && !bCancelled;

                if (aIncomplete && !bIncomplete) return -1;
                if (!aIncomplete && bIncomplete) return 1;
                if (aComplete && bCancelled) return -1;
                if (aCancelled && bComplete) return 1;

                const aDate = new Date(a.created_at || a.updated_at), bDate = new Date(b.created_at || b.updated_at);
                return aIncomplete ? aDate - bDate : bDate - aDate;
            });

            const totalPages = Math.ceil(sorted.length / itemsPerPage);
            currentPage = Math.max(1, Math.min(currentPage, totalPages || 1));

            const startIndex = (currentPage - 1) * itemsPerPage;
            sorted.slice(startIndex, startIndex + itemsPerPage).forEach(item => {
                const div = document.createElement('div');
                div.className = 'col-12 col-xl-6';
                div.innerHTML = `<div id="card-${item.id}" class="card job-card h-100 shadow-sm" style="border-color: #ced4da !important; ${currentlyEditingId === item.id ? 'border-width: 3px !important;' : ''}">${createDisplayCardHTML(item)}</div>`;
                DOM.jobOrderContainer.appendChild(div);
            });

            renderPagination(totalPages);
            $('.select2-basic').select2({ width: '100%', placeholder: 'Pilih' });
        }

        window.setTime = (btn, id, field) => {
            const item = jobOrderList.find(jo => jo.id === id);
            if (!item) return;
            const tl = item.time_list || {}, seq = { open_valve: 'plugging', close_valve: 'open_valve', unplugging: 'close_valve' };
            const prevTime = formatTimeHM(field === 'plugging' ? item.WaktuTiba : tl[seq[field]]);
            const timeToSet = getCurrentTime();

            if (prevTime && (compareTime(timeToSet, prevTime) === 0 || compareTime(timeToSet, addOneMinute(prevTime)) < 0)) {
                return Swal.fire({ icon: 'error', title: 'Waktu harus minimal 1 menit setelah waktu sebelumnya', timer: 2000 });
            }

            btn.disabled = true;
            updateJobOrder(id, { time_list: { [field]: timeToSet } }).then(ok => ok && showNotification('Waktu disimpan'));
        };

        window.updateJobOrderDetail = (sel, id, f) => updateJobOrder(id, { time_list: { [f]: sel.value } });
        window.updateBoosterStatus = (rad, id) => updateJobOrder(id, { time_list: { kategori: rad.value } });

        function renderPagination(totalPages) {
            const container = el('pagination-container');
            container.innerHTML = '';
            if (totalPages <= 1) return;
            const ul = document.createElement('ul');
            ul.className = 'pagination mb-0 justify-content-center';
            for (let i = 1; i <= totalPages; i++) {
                const li = document.createElement('li');
                li.className = `page-item ${i === currentPage ? 'aktif' : ''}`;
                li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
                li.onclick = (e) => { e.preventDefault(); currentPage = i; render(); };
                ul.appendChild(li);
            }
            container.appendChild(ul);
        }

        window.editJobOrder = id => { currentlyEditingId = id; tempTimeChanges = {}; render(); };
        window.cancelCardEdit = () => { currentlyEditingId = null; tempTimeChanges = {}; render(); };

        window.adjustTime = (event, type, change, fieldId) => {
            if (event) event.preventDefault();
            const picker = document.querySelector(`[data-field="${fieldId}"]`);
            const hDisp = picker.querySelector('.hour-display'), mDisp = picker.querySelector('.minute-display');
            let h = parseInt(hDisp.textContent), m = parseInt(mDisp.textContent);

            if (type === 'h') h = (h + change + 24) % 24;
            else {
                m += change;
                if (m < 0) { m = 59; h = (h - 1 + 24) % 24; }
                else if (m > 59) { m = 0; h = (h + 1) % 24; }
            }
            hDisp.textContent = String(h).padStart(2, '0');
            mDisp.textContent = String(m).padStart(2, '0');
            tempTimeChanges[fieldId] = `${hDisp.textContent}:${mDisp.textContent}`;
        };

        const getTimeFromPicker = (fId) => {
            const p = document.querySelector(`[data-field="${fId}"]`);
            return p ? `${p.querySelector('.hour-display').textContent}:${p.querySelector('.minute-display').textContent}` : null;
        };

        window.saveCardChanges = async id => {
            const c = el(`card-${id}`);
            const originalItem = jobOrderList.find(jo => jo.id === id);
            const originalTL = originalItem.time_list || {};

            const getValOrNull = (field, originalVal) => {
                const val = getTimeFromPicker(`${field}-${id}`);
                if (val === '00:00') {
                    if (!originalVal || originalVal === '--:--' || originalVal === '') {
                        return null;
                    }
                }
                return val;
            };

            const data = {
                NoTruck: c.querySelector(`#NoTruck-${id}`).value,
                WaktuTiba: getValOrNull('WaktuTiba', originalItem.WaktuTiba),
                time_list: {
                    NoHose: c.querySelector(`#NoHose-${id}`).value,
                    NoPalka: c.querySelector(`#NoPalka-${id}`).value,
                    kategori: c.querySelector(`input[name="k-${id}"]:checked`)?.value,
                    plugging: getValOrNull('plugging', originalTL.plugging),
                    open_valve: getValOrNull('open_valve', originalTL.open_valve),
                    close_valve: getValOrNull('close_valve', originalTL.close_valve),
                    unplugging: getValOrNull('unplugging', originalTL.unplugging)
                }
            };

            if (await updateJobOrder(id, data)) {
                currentlyEditingId = null;
                tempTimeChanges = {};
                render();
                showNotification('Data diperbarui');
            }
        };

        window.cancelJobOrder = id => {
            Swal.fire({
                title: 'Apakah anda yakin?', icon: 'warning', input: 'text', inputPlaceholder: 'Catatan...',
                showCancelButton: true, confirmButtonText: 'Ya', confirmButtonColor: '#b91c1c'
            }).then(async r => {
                if (r.isConfirmed) {
                    const res = await fetch(`/job-order/${id}/batal`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': config.csrfToken },
                        body: JSON.stringify({ Catatan: r.value })
                    });
                    if (res.ok) {
                        const d = await res.json();
                        jobOrderList[jobOrderList.findIndex(jo => jo.id === id)] = normalizeJobOrder(d.jobOrder || d);
                        render();
                        showNotification('Dibatalkan');
                    }
                }
            });
        };

        window.openNotePopup = id => {
            const item = jobOrderList.find(jo => jo.id === id);
            Swal.fire({
                title: 'Catatan', input: 'text', inputValue: item?.time_list?.Catatan || '',
                showCancelButton: true, confirmButtonText: 'Simpan'
            }).then(async r => {
                if (r.isConfirmed && await updateJobOrder(id, { time_list: { Catatan: r.value } })) showNotification('Disimpan');
            });
        };

        function init() {
            const tick = () => DOM.realtimeClock.textContent = new Date().toLocaleTimeString('id-ID', { hour12: false }).replace(/\./g, ':');
            tick(); setInterval(tick, 1000);

            $(DOM.kapalSelect).on('change', function () {
                const val = $(this).val();
                if (val && val !== config.selectedKapal) {
                    window.location.href = `{{ route('job-order.input', ['date' => 'DATE', 'shift' => 'SHIFT', 'kapal' => 'KAPAL']) }}`
                        .replace('DATE', config.currentDate).replace('SHIFT', config.currentShift).replace('KAPAL', encodeURIComponent(val));
                }
            });

            DOM.form.addEventListener('submit', async e => {
                e.preventDefault();
                const p = { NoJobOrder: el('NoJobOrder').value, NoTruck: el('NoTruck').value, Kapal: el('Kapal').value, Tanggal: config.currentDate, NoShift: el('NoShift').value, WaktuTiba: DOM.waktuTibaInput.value };
                if (!p.WaktuTiba) return showNotification('Harap klik Start dulu', 'warning');
                try {
                    const res = await fetch(config.storeUrl, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': config.csrfToken }, body: JSON.stringify(p) });
                    const d = await res.json();
                    if (!res.ok) throw new Error(d.message || 'Gagal simpan');
                    jobOrderList.push(normalizeJobOrder(d.jobOrder || d));
                    DOM.form.reset(); DOM.waktuTibaInput.value = ''; DOM.arrivalTimeBtn.disabled = false;
                    render(); showNotification('Job Order berhasil ditambah');
                } catch (err) { showNotification(err.message, 'error'); }
            });

            DOM.arrivalTimeBtn.onclick = function () { DOM.waktuTibaInput.value = getCurrentTime(); this.disabled = true; };
            DOM.statusToggle.onchange = DOM.searchInput.oninput = DOM.shiftFilter.onchange = () => { currentPage = 1; render(); };

            if (DOM.scanQrBtn) {
                DOM.scanQrBtn.onclick = () => {
                    qrScannerModal.show();
                    if (!html5QrCode) html5QrCode = new Html5Qrcode("qr-reader");
                    html5QrCode.start({ facingMode: "environment" }, { fps: 10, qrbox: 250 }, t => { DOM.noJobOrderInput.value = t; qrScannerModal.hide(); });
                };
            }

            jobOrderList = jobOrderList.map(normalizeJobOrder);
            render();

            setInterval(async () => {
                if (currentlyEditingId !== null) return;
                const res = await fetch('/job-orders', { headers: { 'Accept': 'application/json' } });
                if (res.ok) {
                    const data = await res.json();
                    const all = (Array.isArray(data) ? data : data.jobOrders).map(normalizeJobOrder);
                    jobOrderList = config.selectedKapal ? all.filter(i => i.Kapal === config.selectedKapal) : all;
                    render();
                }
            }, 10000);
        }
        init();
    });
</script>
@endpush