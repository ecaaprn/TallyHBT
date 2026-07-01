<div id="formEditContainer" class="p-3 mb-4 rounded shadow-sm"
    style="display:none;background-color:#EBF5FF;border:1px solid #B3D9FF;">
    <h5 class="fw-bold mb-3" style="color:#0158a4;">Edit Data Monitoring</h5>
    <hr class="my-3">

    <form id="editMonitoringForm" class="row g-3">
        @csrf
        <input type="hidden" id="edit_monitoringId">

        <div class="col-md-4">
            <label class="form-label">Tanggal</label>
            <div class="input-group">
                <input type="text" class="form-control uniform-field" id="edit_tanggal">
                <span class="input-group-text" id="openDateEdit" style="cursor:pointer;">
                    <i class="fas fa-calendar-alt"></i>
                </span>
            </div>
        </div>

        <div class="col-md-4">
            <label class="form-label">Shift</label>
            <input type="text" class="form-control uniform-field" id="edit_shift">
        </div>

        <div class="col-md-4">
            <label class="form-label">Kapal</label>
            <input type="text" class="form-control uniform-field" id="edit_kapal">
        </div>

        <div class="col-md-4">
            <label class="form-label">No Job Order</label>
            <input type="text" class="form-control uniform-field" id="edit_noJobOrder">
        </div>

        <div class="col-md-4">
            <label class="form-label">Petugas Job Order</label>
            <input type="text" class="form-control uniform-field" id="edit_petugasJoborder">
        </div>

        <div class="col-md-4">
            <label class="form-label">Petugas Timelist</label>
            <input type="text" class="form-control uniform-field" id="edit_petugasTimelist">
        </div>

        <div class="col-md-4">
            <label class="form-label">No Truck</label>
            <input type="text" class="form-control uniform-field" id="edit_noTruck">
        </div>

        <div class="col-md-4">
            <label class="form-label">Waktu Tiba</label>
            <input type="time" class="form-control uniform-field" id="edit_waktuTiba">
        </div>

        <div class="col-md-4">
            <label class="form-label">Plugging</label>
            <input type="time" class="form-control uniform-field" id="edit_plugging">
        </div>

        <div class="col-md-4">
            <label class="form-label">Open Valve</label>
            <input type="time" class="form-control uniform-field" id="edit_openValve">
        </div>

        <div class="col-md-4">
            <label class="form-label">Close Valve</label>
            <input type="time" class="form-control uniform-field" id="edit_closeValve">
        </div>

        <div class="col-md-4">
            <label class="form-label">Unplugging</label>
            <input type="time" class="form-control uniform-field" id="edit_unplugging">
        </div>

        <div class="col-md-4">
            <label class="form-label">Kategori</label>
            <select class="form-select select2-init-edit" id="edit_kategori">
                <option value="" disabled selected>Pilih</option>
                <option value="Booster">Booster</option>
                <option value="Non Booster">Non Booster</option>
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label">Status</label>
            <select class="form-select select2-init-edit" id="edit_status">
                <option value="" disabled selected>Pilih</option>
                <option value="Aktif">Aktif</option>
                <option value="NonAktif">NonAktif</option>
                <option value="Batal">Batal</option>
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label">Catatan</label>
            <input type="text" class="form-control uniform-field" id="edit_catatan">
        </div>

        <div class="col-md-12 d-flex justify-content-end gap-2">
            <button type="button" id="edit_cancelBtn" class="btn btn-secondary px-4">Batal</button>
            <button type="submit" class="btn btn-primary px-4" style="background-color:#0158a4;">Simpan</button>
        </div>
    </form>
</div>

<style>
    .select2-container--default .select2-selection--single {
        height: 42px;
        border-radius: 8px;
        border: 1px solid #ced4da;
        display: flex;
        border: 1px solid #ced4da;
        display: flex;
        align-items: center;
        z-index: 9999 !important;
    }
    .select2-container {
        z-index: 9999 !important;
    }

    .select2-selection__rendered {
        line-height: 42px;
        padding-left: 15px;
        font-size: 15px
    }

    .select2-selection__arrow {
        height: 42px
    }
</style>

<script>
    $(function () {

        $('#edit_tanggal').datepicker({
            language: 'id',
            format: 'dd-mm-yyyy',
            autoclose: true,
            todayHighlight: true
        });

        $('#openDateEdit').on('click', function () {
            $('#edit_tanggal').datepicker('show');
        });

        $(document).on('click', '.editMonitoringBtn', function () {
            const d = $(this).data();
            editingId = d.id;

            $('#formEditContainer').slideDown(300, function() {
                $('.select2-init-edit').select2({
                    width: '100%',
                    dropdownParent: $('#formEditContainer'),
                    minimumResultsForSearch: Infinity
                });
            });
            $('#edit_monitoringId').val(editingId);

            let t = '';
            if (d.tanggal) {
                const p = d.tanggal.split('-');
                if (p.length === 3) t = `${p[2]}-${p[1]}-${p[0]}`;
            }

            let kategori = (d.kategori || '').toString();
            if (kategori.toLowerCase().replace(/\s/g, '') === 'nonbooster' || kategori.toLowerCase() === 'non-booster') {
                kategori = 'Non Booster';
            } else if (kategori.toLowerCase() === 'booster') {
                kategori = 'Booster';
            }

            let status = (d.status || '').toString().toLowerCase();
            if (['active', 'aktif'].includes(status)) status = 'Aktif';
            if (['inactive', 'nonaktif', 'non-aktif'].includes(status)) status = 'NonAktif';
            if (['cancel', 'batal'].includes(status)) status = 'Batal';

            $('#edit_tanggal').val(t);
            $('#edit_shift').val(d.shift);
            $('#edit_kapal').val(d.kapal);
            $('#edit_noJobOrder').val(d.no_job_order);
            $('#edit_petugasJoborder').val(d.petugas_joborder);
            $('#edit_petugasTimelist').val(d.petugas_timelist);
            $('#edit_noTruck').val(d.no_truck);
            $('#edit_waktuTiba').val(d.waktu_tiba);
            $('#edit_plugging').val(d.plugging);
            $('#edit_openValve').val(d.open_valve);
            $('#edit_closeValve').val(d.close_valve);
            $('#edit_unplugging').val(d.unplugging);
            $('#edit_kategori').val(kategori).trigger('change');
            $('#edit_status').val(status).trigger('change');
            $('#edit_catatan').val(d.catatan);
        });

        $('#edit_cancelBtn').on('click', function () {
            $('#editMonitoringForm')[0].reset();
            $('#formEditContainer').slideUp();
            $('.select2-init-edit').val(null).trigger('change');
            editingId = null;
        });

        $('#editMonitoringForm').on('submit', function (e) {
            e.preventDefault();

            let t = null;
            const v = $('#edit_tanggal').val();
            if (v) {
                const p = v.split('-');
                if (p.length === 3) t = `${p[2]}-${p[1]}-${p[0]}`;
            }

            const payload = {
                _token: '{{ csrf_token() }}',
                _method: 'PUT',
                tanggal: t,
                no_truck: $('#edit_noTruck').val(),
                waktu_tiba: $('#edit_waktuTiba').val(),
                plugging: $('#edit_plugging').val(),
                open_valve: $('#edit_openValve').val(),
                close_valve: $('#edit_closeValve').val(),
                unplugging: $('#edit_unplugging').val(),
                kategori: $('#edit_kategori').val(),
                status: $('#edit_status').val(),
                catatan: $('#edit_catatan').val()
            };

            const base = '{{ route("monitoring-data", [], false) }}';
            const url = base.replace(/\/$/, '') + '/' + editingId;

            $.post(url, payload).done(function () {
                if (typeof fetchMonitoringData === 'function') fetchMonitoringData(currentSearch);
                $('#edit_cancelBtn').click();
                Swal.fire({ icon: 'success', title: 'Data berhasil diperbarui', showConfirmButton: false, timer: 2000 });
            }).fail(function (xhr) {
                Swal.fire({ icon: 'error', title: xhr.responseJSON?.message || 'Terjadi kesalahan', showConfirmButton: false, timer: 2000 });
            });
        });
    });
</script>
