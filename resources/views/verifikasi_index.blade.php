@extends('layouts.main_layout', ['title' => 'Verifikasi Rencana Pembelajaran Unit Kerja'])
@section('content')
  <div class="container-fluid px-3">
    <!-- Page Header dengan Informasi Penting -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h2 class="mb-1">Verifikasi Rencana Pembelajaran</h2>
        <div class="d-flex align-items-center gap-2">
          <span class="badge bg-primary bg-opacity-10 text-primary">
            <i class="ti ti-user me-1"></i> {{ $namaPegawai }}
          </span>
          <span class="badge bg-secondary bg-opacity-10 text-secondary">
            <i class="ti ti-building me-1"></i> {{ $unitKerja->unit_kerja }}
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
              Admin belum menetapkan periode verifikasi
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
              Periode Verifikasi Akan Dimulai
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
              Batas Verifikasi:
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
              Masa Verifikasi Telah Berakhir
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
                <h3 class="mb-0">
                  {{ $kelompoksData->sum(fn($k) => $k['rencanaDisetujui']->count() + $k['rencanaDirevisi']->count() + $k['rencanaBelumDiverifikasi']->count()) }}
                </h3>
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
                <h3 class="mb-0">{{ $kelompoksData->sum(fn($k) => $k['rencanaDisetujui']->count()) }}</h3>
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
                <h3 class="mb-0">{{ $kelompoksData->sum(fn($k) => $k['rencanaDirevisi']->count()) }}</h3>
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
    <div class="card mb-4 pb-2 bg-white">
      <div class="card-header p-3 fs-5 fw-bolder bg-white">
        <div class="d-flex justify-content-between align-items-center">
          <h5 class="mb-0 fw-semibold">Daftar Rencana Unit Kerja</h5>
          <div class="input-group input-group-sm" style="width: 25%">
            <input type="text" class="form-control form-control-sm" placeholder="Cari..." aria-label="Cari"
              aria-describedby="button-addon2">
            <button class="btn btn-outline-dark" style="opacity: 15%" type="button" id="button-addon2"><i
                class="ti ti-search"></i></button>
          </div>
        </div>
      </div>
      <hr class="mt-0 mb-1 text-body-tertiary">

      <div class="card-body p-3">
        @if (empty($kelompoksData))
          <div class="alert alert-warning">
            Tidak ada data kelompok atau rencana pembelajaran yang perlu diverifikasi.
          </div>
        @else
          <div class="accordion" id="kelompokAccordion">
            @foreach ($kelompoksData as $idx => $kelompokData)
              <div class="accordion-item">
                <div class="accordion-header" id="heading-{{ $kelompokData['kelompok']->id }}">
                  <button class="accordion-button bg-white shadow-none collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapse-{{ $kelompokData['kelompok']->id }}" aria-expanded="true"
                    aria-controls="collapse-{{ $kelompokData['kelompok']->id }}">
                    <span class="fw-semibold fs-5">
                      Kelompok: {{ $kelompokData['kelompok']->ketua->nama ?? 'Ketua Tidak Ditemukan' }}
                    </span>
                    <span class="badge bg-primary ms-2 fs-2">
                      {{ $kelompokData['kelompok']->anggota->count() }} Anggota
                    </span>
                  </button>
                </div>

                <div id="collapse-{{ $kelompokData['kelompok']->id }}" class="accordion-collapse collapse show"
                  data-bs-parent="#kelompokAccordion" aria-labelledby="heading-{{ $kelompokData['kelompok']->id }}">
                  <div class="accordion-body p-0 pt-3">
                    @php
                      $totalRencana =
                          $kelompokData['rencanaDisetujui']->count() +
                          $kelompokData['rencanaBelumDiverifikasi']->count() +
                          $kelompokData['rencanaDirevisi']->count();
                    @endphp

                    <ul class="nav nav-tabs px-3" role="tablist">
                      <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-semibold" data-bs-toggle="tab"
                          data-bs-target="#belumdiverifikasi-{{ $kelompokData['kelompok']->id }}" type="button"
                          role="tab">
                          Belum Diverifikasi
                          <span
                            class="badge bg-danger ms-1 fs-1">{{ $kelompokData['rencanaBelumDiverifikasi']->count() }}</span>
                        </button>
                      </li>
                      <li class="nav-item" role="presentation">
                        <button class="nav-link fw-semibold" data-bs-toggle="tab"
                          data-bs-target="#disetujui-{{ $kelompokData['kelompok']->id }}" type="button"
                          role="tab">
                          Disetujui
                          <span
                            class="badge bg-success ms-1 fs-1">{{ $kelompokData['rencanaDisetujui']->count() }}</span>
                        </button>
                      </li>
                      <li class="nav-item" role="presentation">
                        <button class="nav-link fw-semibold" data-bs-toggle="tab"
                          data-bs-target="#direvisi-{{ $kelompokData['kelompok']->id }}" type="button"
                          role="tab">
                          Revisi
                          <span
                            class="badge bg-warning ms-1 fs-1">{{ $kelompokData['rencanaDirevisi']->count() }}</span>
                        </button>
                      </li>
                    </ul>

                    <div class="tab-content p-2 px-0">
                      <div class="tab-pane fade show active" id="belumdiverifikasi-{{ $kelompokData['kelompok']->id }}"
                        role="tabpanel">
                        @if ($kelompokData['rencanaBelumDiverifikasi']->isEmpty())
                          @if (
                              $kelompokData['rencanaDisetujui']->isEmpty() &&
                                  $kelompokData['rencanaDirevisi']->isEmpty() &&
                                  $kelompokData['rencanaBelumDiverifikasi']->isEmpty())
                            {{-- Kasus 1: Tidak ada rencana pembelajaran sama sekali --}}
                            <div class="alert alert-warning m-2 mx-3">
                              <i class="ti ti-info-circle me-2"></i>Belum ada data pembelajaran dari kelompok ini
                            </div>
                          @elseif ($kelompokData['rencanaDisetujui']->count() + $kelompokData['rencanaDirevisi']->count() > 0)
                            {{-- Kasus 2: Ada rencana, tapi semuanya sudah diverifikasi (disetujui/direvisi) --}}
                            <div class="alert alert-success m-2 mx-3">
                              <i class="ti ti-circle-check me-2"></i>Semua rencana pembelajaran telah diverifikasi
                            </div>
                          @else
                            {{-- Kasus lain yang tidak terduga --}}
                            <div class="alert alert-info m-2 mx-3">
                              <i class="ti ti-alert-circle me-2"></i>Tidak ada rencana yang belum diverifikasi
                            </div>
                          @endif
                        @else
                          {{-- Ada data yang belum diverifikasi --}}
                          @include('partials.tabel_belum_verifikasi', [
                              'kelompokData' => $kelompokData,
                              'accordionKey' => $idx, // <-- kirim index unik per accordion
                          ])
                        @endif
                      </div>

                      <!-- Tab Ditolak / Perlu Revisi -->
                      <div class="tab-pane fade" id="direvisi-{{ $kelompokData['kelompok']->id }}" role="tabpanel">
                        @if ($kelompokData['rencanaDirevisi']->isEmpty())
                          <div class="alert alert-info m-2 mx-3">
                            <i class="ti ti-info-circle me-2"></i>Tidak ada rencana yang perlu revisi
                          </div>
                        @else
                          @include('partials.tabel_direvisi_verifikasi', [
                              'rencana' => $kelompokData['rencanaDirevisi'],
                              'kelompok' => $kelompokData['kelompok'],
                          ])
                        @endif
                      </div>

                      <!-- Tab Disetujui -->
                      <div class="tab-pane fade" id="disetujui-{{ $kelompokData['kelompok']->id }}" role="tabpanel">
                        @if ($kelompokData['rencanaDisetujui']->isEmpty())
                          <div class="alert alert-info m-2">
                            <i class="ti ti-info-circle me-2"></i>Tidak ada rencana yang disetujui
                          </div>
                        @else
                          @include('partials.tabel_disetujui_verifikasi', [
                              'rencana' => $kelompokData['rencanaDisetujui'],
                              'kelompok' => $kelompokData['kelompok'],
                          ])
                        @endif
                      </div>
                    </div>

                  </div>
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </div>
  </div>
@endsection
