@extends('layouts.admin')

@section('title', 'Dashboard Monitoring')

@section('content')
<div class="container-fluid py-4">
    <div class="dashboard-banner mb-4">
        <div class="row align-items-center">
            <div class="col-lg-4 ps-4">
                <h1 class="text-white fw-bold mb-1" style="font-size: 1.8rem;">Dashboard Monitoring</h1>
                <p class="text-white-50 mb-0">Aktivitas Job Order & Ritase Real-time</p>
            </div>
            <div class="col-lg-8">
                <div class="filters-glass-card">
                    <div class="row g-2 align-items-end justify-content-end">
                        <div class="col-md-3">
                            <label class="filter-label"><i class="fas fa-calendar-alt me-1"></i> TAHUN</label>
                            <select id="tahunFilter" class="form-select select2-custom">
                                <option value="all">Semua</option>
                                @foreach($tahunList ?? [] as $tahun)
                                    <option value="{{ $tahun }}" {{ ($selectedYear ?? '') == $tahun ? 'selected' : '' }}>{{ $tahun }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="filter-label"><i class="fas fa-calendar-day me-1"></i> BULAN</label>
                            <select id="bulanFilter" class="form-select select2-custom">
                                <option value="all">Semua</option>
                                @php $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']; @endphp
                                @foreach($months as $index => $month)
                                    <option value="{{ $index + 1 }}" {{ ($selectedMonth ?? '') == ($index + 1) ? 'selected' : '' }}>{{ $month }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="filter-label"><i class="fas fa-ship me-1"></i> KAPAL</label>
                            <select id="kapalFilter" class="form-select select2-custom">
                                <option value="all">Semua Kapal</option>
                                @foreach($kapalList ?? [] as $kapal)
                                    <option value="{{ $kapal }}" {{ ($selectedKapal ?? '') == $kapal ? 'selected' : '' }}>{{ $kapal }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button id="resetFilter" class="btn btn-reset-custom w-100">
                                <i class="fas fa-sync-alt me-2"></i>Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card-modern">
                <div class="card-modern-body">
                    <div class="icon-box-vibrant bg-success"><i class="fas fa-play"></i></div>
                    <div class="ms-3">
                        <h6 class="text-muted fw-bold mb-0" style="font-size: 0.75rem;">JOB ORDER AKTIF</h6>
                        <h2 class="fw-bold mb-0 text-success" id="jobOrderAktifValue">{{ $jobOrderAktif ?? 0 }}</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-modern">
                <div class="card-modern-body">
                    <div class="icon-box-vibrant bg-danger"><i class="fas fa-stop"></i></div>
                    <div class="ms-3">
                        <h6 class="text-muted fw-bold mb-0" style="font-size: 0.75rem;">JOB ORDER NONAKTIF</h6>
                        <h2 class="fw-bold mb-0 text-danger" id="jobOrderNonaktifValue">{{ $jobOrderNonaktif ?? 0 }}</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-modern">
                <div class="card-modern-body">
                    <div class="icon-box-vibrant bg-warning"><i class="fas fa-times text-white"></i></div>
                    <div class="ms-3">
                        <h6 class="text-muted fw-bold mb-0" style="font-size: 0.75rem;">JOB ORDER BATAL</h6>
                        <h2 class="fw-bold mb-0 text-warning" id="jobOrderBatalValue">{{ $jobOrderBatal ?? 0 }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card-container h-100">
                <div class="card-container-header bg-success text-white">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-bolt me-2 text-warning"></i>Truck Tercepat</h6>
                </div>
                <div class="card-container-body p-4">
                    @if($fastestTruck ?? false)
                        <div class="truck-row"><span class="t-label">No. Truck</span><span class="t-value">{{ $fastestTruck->NoTruck }}</span></div>
                        <div class="truck-row"><span class="t-label">Job Order</span><span class="t-value">{{ $fastestTruck->NoJobOrder }}</span></div>
                        <div class="truck-row">
                            <span class="t-label">Waktu Tempuh</span>
                            <span class="badge-time-success">{{ $fastestTruck->ritase_minutes }} Menit</span>
                        </div>
                    @else
                        <p class="text-muted mb-0">Data tidak ditemukan</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card-container h-100">
                <div class="card-container-header bg-danger text-white">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-hourglass-half me-2 text-white"></i>Truck Terlama</h6>
                </div>
                <div class="card-container-body p-4">
                    @if($slowestTruck ?? false)
                        <div class="truck-row"><span class="t-label">No. Truck</span><span class="t-value">{{ $slowestTruck->NoTruck }}</span></div>
                        <div class="truck-row"><span class="t-label">Job Order</span><span class="t-value">{{ $slowestTruck->NoJobOrder }}</span></div>
                        <div class="truck-row">
                            <span class="t-label">Waktu Tempuh</span>
                            <span class="badge-time-danger">{{ $slowestTruck->ritase_minutes }} Menit</span>
                        </div>
                    @else
                        <p class="text-muted mb-0">Data tidak ditemukan</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card-chart">
                <div class="card-chart-header d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-info"><i class="fas fa-route me-2"></i>Ritase Truck</h6>
                    <div style="width: 280px;">
                        <select id="truckFilter" class="form-select select2-truck-border">
                            <option value="all">Semua Truck</option>
                            @foreach($truckList ?? [] as $truck)
                                <option value="{{ $truck }}">{{ $truck }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="p-4 pt-0">
                    <canvas id="ritaseChart" height="400"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
body {
    background-color: #f4f7fa;
    font-family: 'Inter', sans-serif;
}

.dashboard-banner {
    background-color: #242e42;
    padding: 2.5rem 1.5rem;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.filters-glass-card {
    background: rgba(255, 255, 255, 0.05);
    padding: 1.5rem;
    border-radius: 15px;
}

.filter-label {
    font-size: 0.7rem;
    color: #94a3b8;
    text-transform: uppercase;
    font-weight: 800;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
    display: block;
}

.btn-reset-custom {
    background-color: #3d495e;
    color: white;
    border: none;
    height: 45px;
    border-radius: 12px;
    font-weight: 600;
    transition: 0.3s;
}

.btn-reset-custom:hover {
    background-color: #4e5b72;
    color: white;
}

.select2-custom + .select2-container .select2-selection--single {
    height: 45px !important;
    border-radius: 12px !important;
    border: none !important;
    display: flex;
    align-items: center;
}

.select2-custom + .select2-container .select2-selection__rendered {
    line-height: 45px !important;
    padding-left: 15px !important;
}

.select2-custom + .select2-container .select2-selection__arrow {
    height: 45px !important;
}

.card-modern {
    background: white;
    border-radius: 20px;
    border: none;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}

.card-modern-body {
    padding: 1.5rem;
    display: flex;
    align-items: center;
}

.icon-box-vibrant {
    width: 55px;
    height: 55px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    color: white;
    box-shadow: 0 8px 15px rgba(0,0,0,0.1);
}

.card-container {
    background: white;
    border-radius: 20px;
    border: none;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}

.card-container-header {
    padding: 15px 25px;
}

.truck-row {
    display: flex;
    margin-bottom: 12px;
    align-items: center;
}

.t-label {
    width: 130px;
    color: #64748b;
    font-size: 0.9rem;
    font-weight: 500;
}

.t-value {
    color: #1e293b;
    font-weight: 700;
    font-size: 0.95rem;
}

.badge-time-success {
    background: rgba(40, 167, 69, 0.15);
    color: #28a745;
    padding: 5px 15px;
    border-radius: 50px;
    font-weight: 700;
    font-size: 0.85rem;
}

.badge-time-danger {
    background: rgba(220, 53, 69, 0.15);
    color: #dc3545;
    padding: 5px 15px;
    border-radius: 50px;
    font-weight: 700;
    font-size: 0.85rem;
}

.card-chart {
    background: white;
    border-radius: 20px;
    border: none;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}

.card-chart-header {
    padding: 1.5rem;
}

.select2-truck-border + .select2-container .select2-selection--single {
    border: 1px solid #d1d5db !important;
    border-radius: 10px !important;
    height: 45px !important;
    background-color: #ffffff !important;
    display: flex;
    align-items: center;
}

.select2-truck-border + .select2-container .select2-selection__rendered {
    line-height: 45px !important;
    color: #4b5563 !important;
    font-weight: 600;
    padding-left: 15px !important;
}

.select2-truck-border + .select2-container .select2-selection__arrow {
    height: 45px !important;
}

</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(function(){
    $('.select2-custom').select2({ width: '100%' });
    $('.select2-truck-border').select2({ width: '100%' });

    let ritaseChart;

    function initCharts(data) {
        const ctxRitase = document.getElementById('ritaseChart');
        if (ritaseChart) ritaseChart.destroy();

        const filtered = (data.ritaseChartData || []).filter(d => d.ritase > 0);
        const selectedTruck = $('#truckFilter').val();

        ritaseChart = new Chart(ctxRitase, {
            type: 'line',
            data: {
                labels: filtered.map((d, i) => selectedTruck === 'all' ? (d.truck_name || d.label) : `R${i + 1}`),
                datasets: [{
                    data: filtered.map(d => Math.round(d.ritase)),
                    borderColor: '#0ea5e9',
                    fill: false,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: (c) => ` Durasi: ${c.raw} Menit` } }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { display: false },
                        ticks: { stepSize: 10, color: '#94a3b8', callback: (v) => v + ' Menit' }
                    },
                    x: { grid: { display: false }, offset: false, ticks: { autoSkip: false, color: '#94a3b8' } }
                },
                elements: {
                    line: { borderWidth: 4 },
                    point: { radius: 5, backgroundColor: '#0ea5e9', borderWidth: 2, borderColor: '#fff' }
                }
            }
        });
    }

    function updateDashboard() {
        $.get('{{ route("dashboard.chart-data") }}', {
            year: $('#tahunFilter').val(),
            month: $('#bulanFilter').val(),
            kapal: $('#kapalFilter').val(),
            truck: $('#truckFilter').val()
        }, function(r) {
            $('#jobOrderAktifValue').text(r.jobOrderAktif);
            $('#jobOrderNonaktifValue').text(r.jobOrderNonaktif);
            $('#jobOrderBatalValue').text(r.jobOrderBatal);
            initCharts(r);
        });
    }

    $('#tahunFilter, #kapalFilter, #bulanFilter, #truckFilter').on('change', updateDashboard);
    $('#resetFilter').on('click', function() { location.reload(); });

    initCharts({
        ritaseChartData: @json($ritaseChartData ?? [])
    });
});
</script>
@endpush
@endsection
