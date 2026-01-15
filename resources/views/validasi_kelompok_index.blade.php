@extends('layouts.main_layout', ['title' => 'Validasi Rencana Pembelajaran Kelompok'])
@section('content')
  <div class="container-fluid px-3">
    <!-- Page Header dengan Informasi Penting -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h2 class="mb-1">Validasi Rencana Pembelajaran Kelompok</h2>
        <div class="d-flex align-items-center gap-2">
          <span class="badge bg-primary bg-opacity-10 text-primary">
            <i class="ti ti-user me-1"></i> {{ $kelompok->ketua->nama }}
          </span>
          <span class="badge bg-secondary bg-opacity-10 text-secondary">
            <i class="ti ti-building me-1"></i> {{ $kelompok->ketua->unitKerja->unit_kerja ?? '-' }}
          </span>
        </div>
      </div>

      <!-- Alert Deadline -->
      @if (!$isDeadlineSet)
        <!-- Alert ketika deadline belum diset admin -->
        <div class="d-flex align-items-center px-3 py-2 rounded"
          style="background-color: #fff3e0; border-left: 4px solid #ff9800;">
          <i class="ti ti-alert-triangle me-2" style="color: #ff9800;"></i>
          <div>
            <div class="fw-semibold" style="font-size: 0.85rem; color: #ff9800;">
              Tenggat Belum Ditetapkan
            </div>
            <div style="font-size: 0.8rem; color: #e65100;">
              Admin belum menetapkan periode pengajuan
            </div>
          </div>
        </div>
      @elseif($isNotStartedYet)
        <!-- Alert ketika tenggat sudah ditetapkan tapi belum mulai -->
        <div class="d-flex align-items-center px-3 py-2 rounded"
          style="background-color: #e3f2fd; border-left: 4px solid #2196f3;">
          <i class="ti ti-clock me-2" style="color: #2196f3;"></i>
          <div>
            <div class="fw-semibold" style="font-size: 0.85rem; color: #2196f3;">
              Periode Pengajuan Akan Dimulai
            </div>
            <div style="font-size: 0.8rem; color: #0d47a1;">
              Mulai: {{ $startDate->isoFormat('dddd, D MMMM YYYY [pukul] HH:mm') }}
              @if ($daysUntilStart > 0)
                ({{ floor($daysUntilStart) }} hari lagi)
              @else
                (Segera)
              @endif
            </div>
          </div>
        </div>
      @elseif($isWithinDeadline)
        <!-- Alert deadline normal -->
        <div class="d-flex align-items-center px-3 py-2 rounded"
          style="background-color: #e8f5e9; border-left: 4px solid #4caf50;">
          <i class="ti ti-calendar me-2" style="color: #4caf50;"></i>
          <div>
            <div class="fw-semibold" style="font-size: 0.85rem; color: #4caf50;">
              Batas Pengajuan:
            </div>
            <div style="font-size: 0.8rem; color: #2e7d32;">
              {{ $endDate->isoFormat('dddd, D MMMM YYYY [pukul] HH:mm') }}
            </div>
          </div>
        </div>
      @else
        <!-- Alert deadline telah berakhir -->
        <div class="d-flex align-items-center px-3 py-2 rounded"
          style="background-color: #ffebee; border-left: 4px solid #f44336;">
          <i class="ti ti-alert-circle me-2" style="color: #f44336;"></i>
          <div>
            <div class="fw-semibold" style="font-size: 0.85rem; color: #f44336;">
              Masa Pengajuan Telah Berakhir
            </div>
            <div style="font-size: 0.8rem; color: #c62828;">
              Batas akhir: {{ $endDate->isoFormat('dddd, D MMMM YYYY [pukul] HH:mm') }}
            </div>
          </div>
        </div>
      @endif
    </div>
    <!-- Statistik Ringkas -->
    <div class="row mb-3">
      <div class="col-md-4">
        <div class="card mb-1 shadow-none border">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h5 class="text-muted mb-2">Total Rencana</h5>
                <h3 class="mb-0"> {{ $totalRencana }} </h3>
              </div>
              <div class="bg-primary bg-opacity-10 p-3 rounded">
                <i class="ti ti-list text-primary" style="font-size: 1.5rem"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card mb-1 shadow-none border">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h5 class="text-muted mb-2">Disetujui</h5>
                <h3 class="mb-0">{{ $totalDisetujui }}</h3>
              </div>
              <div class="bg-success bg-opacity-10 p-3 rounded">
                <i class="ti ti-circle-check text-success" style="font-size: 1.5rem"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card mb-1 shadow-none border">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h5 class="text-muted mb-2">Perlu Revisi</h5>
                <h3 class="mb-0">{{ $totalPerluRevisi }}</h3>
              </div>
              <div class="bg-warning bg-opacity-10 p-3 rounded">
                <i class="ti ti-alert-triangle text-warning" style="font-size: 1.5rem"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Card Daftar --}}
    <div class="card mb-2 pb-1 bg-white">
      <div class="card-header p-3 fs-5 fw-bolder bg-white">
        <div class="d-flex justify-content-between align-items-center">
          <span class="mb-0 fw-semibold fs-5">Daftar Rencana Pembelajaran Kelompok</span>
        </div>
      </div>

      <div class="card-body p-0">
        <ul class="nav nav-tabs mt-2 px-3" id="statusTabs">
          <li class="nav-item">
            <a class="nav-link fw-semibold active" data-bs-toggle="tab" href="#belumdivalidasi">
              Belum Divalidasi
              <span class="badge bg-danger ms-1 fs-1">{{ $rencanaBelumDivalidasi->count() }}</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link fw-semibold" data-bs-toggle="tab" href="#disetujui">
              Disetujui
              <span class="badge bg-success ms-1 fs-1">{{ $rencanaDisetujui->count() }}</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link fw-semibold" data-bs-toggle="tab" href="#direvisi">
              Revisi
              <span class="badge bg-warning ms-1 fs-1">{{ $rencanaDirevisi->count() }}</span>
            </a>
          </li>
        </ul>

        <div class="tab-content p-2">
          <!-- Tab Belum Divalidasi -->
          <div class="tab-pane fade show active p-2" id="belumdivalidasi">
            @if ($rencana->isEmpty())
              {{-- Kasus 1: Tidak ada rencana pembelajaran sama sekali --}}
              <div class="alert alert-warning mb-0">
                <i class="ti ti-info-circle me-2"></i>Belum ada data pembelajaran dari kelompok ini
              </div>
            @elseif ($rencanaBelumDivalidasi->isEmpty())
              {{-- Kasus 2: Ada rencana, tapi semuanya sudah divalidasi --}}
              <div class="alert alert-success mb-0">
                <i class="ti ti-circle-check me-2"></i>Semua rencana pembelajaran telah divalidasi
              </div>
            @else
              {{-- Ada data yang belum divalidasi --}}
              @include('partials.tabel_belum_validasi', ['rencana' => $rencanaBelumDivalidasi])
            @endif

          </div>

          <!-- Tab Disetujui -->
          <div class="tab-pane fade p-2" id="disetujui">
            @if ($rencanaDisetujui->isEmpty())
              <div class="alert alert-info mb-0"><i class="ti ti-info-circle me-2"></i>Tidak ada rencana yang
                disetujui
              </div>
            @else
              @include('partials.tabel_disetujui_validasi', ['rencana' => $rencanaDisetujui])
            @endif
          </div>

          <!-- Tab Ditolak / Perlu Revisi -->
          <div class="tab-pane fade p-2" id="direvisi">
            @if ($rencanaDirevisi->isEmpty())
              <div class="alert alert-info mb-0"><i class="ti ti-info-circle me-2"></i>Tidak ada rencana yang perlu
                revisi
              </div>
            @else
              @include('partials.tabel_direvisi_validasi', ['rencana' => $rencanaDirevisi])
            @endif
          </div>
        </div>

      </div>
    </div>
  </div>
@endsection
