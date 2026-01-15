<div class="card w-100">
  <div class="card-body p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="fw-bolder m-0">
        <i class="ti ti-calendar-time me-2"></i>Manajemen Jadwal
      </h5>
      <span class="badge bg-danger bg-opacity-10 text-danger">
        <i class="ti ti-shield-lock me-1"></i>Admin
      </span>
    </div>
    <hr class="mb-2">

    {{-- Tab Navigasi --}}
    <ul class="nav nav-tabs mb-4" id="scheduleTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="perencanaan-tab" data-bs-toggle="tab" data-bs-target="#perencanaan"
          type="button" role="tab">
          Perencanaan
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="validasi-tab" data-bs-toggle="tab" data-bs-target="#validasi" type="button"
          role="tab">
          Validasi
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="verifikasi-tab" data-bs-toggle="tab" data-bs-target="#verifikasi" type="button"
          role="tab">
          Verifikasi
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="approval-tab" data-bs-toggle="tab" data-bs-target="#approval" type="button"
          role="tab">
          Approval
        </button>
      </li>
    </ul>

    {{-- Konten Tab --}}
    <div class="tab-content" id="scheduleTabsContent">
      {{-- Tab Perencanaan Pegawai --}}
      <div class="tab-pane fade show active" id="perencanaan" role="tabpanel">
        @if ($jadwalPerencanaan)
          <div class="timeline-wrapper mb-4">
            <div class="d-flex align-items-center mb-2">
              <div class="badge bg-primary rounded-circle p-2 me-3">
                <i class="ti ti-player-play"></i>
              </div>
              <div>
                <h6 class="mb-0">Mulai Perencanaan</h6>
                <small class="text-muted">{{ $jadwalPerencanaan['waktuMulai']->translatedFormat('d F Y, H:i') }}</small>
              </div>
            </div>

            <div class="d-flex align-items-center">
              <div class="badge bg-danger rounded-circle p-2 me-3">
                <i class="ti ti-flag"></i>
              </div>
              <div>
                <h6 class="mb-0">Batas Akhir</h6>
                <small
                  class="text-muted">{{ $jadwalPerencanaan['waktuSelesai']->translatedFormat('d F Y, H:i') }}</small>
              </div>
            </div>
          </div>

          <div class="mb-3">
            @if ($jadwalPerencanaan['isActive'])
              @php
                $now = Carbon\Carbon::now();
                $waktuMulai = $jadwalPerencanaan['waktuMulai'];
                $waktuSelesai = $jadwalPerencanaan['waktuSelesai'];

                // Pastikan waktu sudah dimulai
                if ($now->lt($waktuMulai)) {
                    $progressPercentage = 0;
                } elseif ($now->gt($waktuSelesai)) {
                    $progressPercentage = 100;
                } else {
                    $totalDuration = $waktuMulai->diffInSeconds($waktuSelesai);
                    $elapsedDuration = $waktuMulai->diffInSeconds($now);
                    $progressPercentage = min(100, max(0, ($elapsedDuration / $totalDuration) * 100));
                }
              @endphp

              <div class="d-flex justify-content-between mb-2">
                <span class="text-success fw-semibold">
                  <i class="ti ti-clock me-1"></i> Sisa: {{ $jadwalPerencanaan['sisaHari'] }} hari
                </span>
                <span class="text-primary">{{ round($progressPercentage, 1) }}%</span>
              </div>
              <div class="progress" style="height: 8px;">
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar"
                  style="width: {{ $progressPercentage }}%">
                </div>
              </div>
            @else
              <div class="alert alert-{{ $jadwalPerencanaan['sisaHari'] > 0 ? 'info' : 'danger' }} py-2 mb-0">
                <i class="ti ti-{{ $jadwalPerencanaan['sisaHari'] > 0 ? 'clock' : 'alert-circle' }} me-2"></i>
                {{ $jadwalPerencanaan['sisaHari'] > 0 ? 'Perencanaan belum dimulai' : 'Tenggat waktu telah berakhir!' }}
              </div>
            @endif
          </div>
        @else
          <div class="alert alert-warning d-flex align-items-center">
            <i class="ti ti-alert-triangle me-3 fs-4"></i>
            <div>
              <h6 class="mb-1">Jadwal perencanaan belum ditetapkan</h6>
              <small class="m-0">Atur jadwal untuk pegawai</small>
            </div>
          </div>
        @endif
      </div>

      {{-- Tab Validasi Kelompok --}}
      <div class="tab-pane fade" id="validasi" role="tabpanel">
        @if ($jadwalValidasi)
          <div class="timeline-wrapper mb-4">
            <div class="d-flex align-items-center mb-2">
              <div class="badge bg-primary rounded-circle p-2 me-3">
                <i class="ti ti-player-play"></i>
              </div>
              <div>
                <h6 class="mb-0">Mulai Validasi</h6>
                <small class="text-muted">{{ $jadwalValidasi['waktuMulai']->translatedFormat('d F Y, H:i') }}</small>
              </div>
            </div>

            <div class="d-flex align-items-center">
              <div class="badge bg-danger rounded-circle p-2 me-3">
                <i class="ti ti-flag"></i>
              </div>
              <div>
                <h6 class="mb-0">Batas Akhir</h6>
                <small class="text-muted">{{ $jadwalValidasi['waktuSelesai']->translatedFormat('d F Y, H:i') }}</small>
              </div>
            </div>
          </div>

          <div class="mb-3">
            @if ($jadwalValidasi['isActive'])
              @php
                $now = Carbon\Carbon::now();
                $waktuMulai = $jadwalValidasi['waktuMulai'];
                $waktuSelesai = $jadwalValidasi['waktuSelesai'];

                // Pastikan waktu sudah dimulai
                if ($now->lt($waktuMulai)) {
                    $progressPercentage = 0;
                } elseif ($now->gt($waktuSelesai)) {
                    $progressPercentage = 100;
                } else {
                    $totalDuration = $waktuMulai->diffInSeconds($waktuSelesai);
                    $elapsedDuration = $waktuMulai->diffInSeconds($now);
                    $progressPercentage = min(100, max(0, ($elapsedDuration / $totalDuration) * 100));
                }
              @endphp

              <div class="d-flex justify-content-between mb-2">
                <span class="text-success fw-semibold">
                  <i class="ti ti-clock me-1"></i> Sisa: {{ $jadwalValidasi['sisaHari'] }} hari
                </span>
                <span class="text-primary">{{ round($progressPercentage, 1) }}%</span>
              </div>
              <div class="progress" style="height: 8px;">
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar"
                  style="width: {{ $progressPercentage }}%">
                </div>
              </div>
            @else
              <div class="alert alert-{{ $jadwalValidasi['sisaHari'] > 0 ? 'info' : 'danger' }} py-2 mb-0">
                <i class="ti ti-{{ $jadwalValidasi['sisaHari'] > 0 ? 'clock' : 'alert-circle' }} me-2"></i>
                {{ $jadwalValidasi['sisaHari'] > 0 ? 'Validasi belum dimulai' : 'Tenggat waktu telah berakhir!' }}
              </div>
            @endif
          </div>
        @else
          <div class="alert alert-warning d-flex align-items-center">
            <i class="ti ti-alert-triangle me-3 fs-4"></i>
            <div>
              <h6 class="mb-1">Jadwal validasi belum ditetapkan</h6>
              <small class="m-0">Atur jadwal untuk ketua kelompok</small>
            </div>
          </div>
        @endif
      </div>

      {{-- Tab Verifikasi Unit Kerja --}}
      <div class="tab-pane fade" id="verifikasi" role="tabpanel">
        @if ($jadwalVerifikasi)
          <div class="timeline-wrapper mb-4">
            <div class="d-flex align-items-center mb-2">
              <div class="badge bg-primary rounded-circle p-2 me-3">
                <i class="ti ti-player-play"></i>
              </div>
              <div>
                <h6 class="mb-0">Mulai Verifikasi</h6>
                <small
                  class="text-muted">{{ $jadwalVerifikasi['waktuMulai']->translatedFormat('d F Y, H:i') }}</small>
              </div>
            </div>

            <div class="d-flex align-items-center">
              <div class="badge bg-danger rounded-circle p-2 me-3">
                <i class="ti ti-flag"></i>
              </div>
              <div>
                <h6 class="mb-0">Batas Akhir</h6>
                <small
                  class="text-muted">{{ $jadwalVerifikasi['waktuSelesai']->translatedFormat('d F Y, H:i') }}</small>
              </div>
            </div>
          </div>

          <div class="mb-3">
            @if ($jadwalVerifikasi['isActive'])
              @php
                $now = Carbon\Carbon::now();
                $waktuMulai = $jadwalVerifikasi['waktuMulai'];
                $waktuSelesai = $jadwalVerifikasi['waktuSelesai'];

                if ($now->lt($waktuMulai)) {
                    $progressPercentage = 0;
                } elseif ($now->gt($waktuSelesai)) {
                    $progressPercentage = 100;
                } else {
                    $totalDuration = $waktuMulai->diffInSeconds($waktuSelesai);
                    $elapsedDuration = $waktuMulai->diffInSeconds($now);
                    $progressPercentage = min(100, max(0, ($elapsedDuration / $totalDuration) * 100));
                }
              @endphp

              <div class="d-flex justify-content-between mb-2">
                <span class="text-success fw-semibold">
                  <i class="ti ti-clock me-1"></i> Sisa: {{ $jadwalVerifikasi['sisaHari'] }} hari
                </span>
                <span class="text-primary">{{ round($progressPercentage, 1) }}%</span>
              </div>
              <div class="progress" style="height: 8px;">
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-warning" role="progressbar"
                  style="width: {{ $progressPercentage }}%">
                </div>
              </div>
            @else
              <div class="alert alert-{{ $jadwalVerifikasi['sisaHari'] > 0 ? 'info' : 'danger' }} py-2 mb-0">
                <i class="ti ti-{{ $jadwalVerifikasi['sisaHari'] > 0 ? 'clock' : 'alert-circle' }} me-2"></i>
                {{ $jadwalVerifikasi['sisaHari'] > 0 ? 'Verifikasi belum dimulai' : 'Tenggat waktu telah berakhir!' }}
              </div>
            @endif
          </div>
        @else
          <div class="alert alert-warning d-flex align-items-center">
            <i class="ti ti-alert-triangle me-3 fs-4"></i>
            <div>
              <h6 class="mb-1">Jadwal verifikasi belum ditetapkan</h6>
              <small class="m-0">Atur jadwal untuk unit kerja</small>
            </div>
          </div>
        @endif
      </div>

      {{-- Tab Approval Universitas --}}
      <div class="tab-pane fade" id="approval" role="tabpanel">
        @if ($jadwalApproval)
          <div class="timeline-wrapper mb-4">
            <div class="d-flex align-items-center mb-2">
              <div class="badge bg-primary rounded-circle p-2 me-3">
                <i class="ti ti-player-play"></i>
              </div>
              <div>
                <h6 class="mb-0">Mulai Approval</h6>
                <small class="text-muted">{{ $jadwalApproval['waktuMulai']->translatedFormat('d F Y, H:i') }}</small>
              </div>
            </div>

            <div class="d-flex align-items-center">
              <div class="badge bg-danger rounded-circle p-2 me-3">
                <i class="ti ti-flag"></i>
              </div>
              <div>
                <h6 class="mb-0">Batas Akhir</h6>
                <small
                  class="text-muted">{{ $jadwalApproval['waktuSelesai']->translatedFormat('d F Y, H:i') }}</small>
              </div>
            </div>
          </div>

          <div class="mb-3">
            @if ($jadwalApproval['isActive'])
              @php
                $now = Carbon\Carbon::now();
                $waktuMulai = $jadwalApproval['waktuMulai'];
                $waktuSelesai = $jadwalApproval['waktuSelesai'];

                if ($now->lt($waktuMulai)) {
                    $progressPercentage = 0;
                } elseif ($now->gt($waktuSelesai)) {
                    $progressPercentage = 100;
                } else {
                    $totalDuration = $waktuMulai->diffInSeconds($waktuSelesai);
                    $elapsedDuration = $waktuMulai->diffInSeconds($now);
                    $progressPercentage = min(100, max(0, ($elapsedDuration / $totalDuration) * 100));
                }
              @endphp

              <div class="d-flex justify-content-between mb-2">
                <span class="text-success fw-semibold">
                  <i class="ti ti-clock me-1"></i> Sisa: {{ $jadwalApproval['sisaHari'] }} hari
                </span>
                <span class="text-primary">{{ round($progressPercentage, 1) }}%</span>
              </div>
              <div class="progress" style="height: 8px;">
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger" role="progressbar"
                  style="width: {{ $progressPercentage }}%">
                </div>
              </div>
            @else
              <div class="alert alert-{{ $jadwalApproval['sisaHari'] > 0 ? 'info' : 'danger' }} py-2 mb-0">
                <i class="ti ti-{{ $jadwalApproval['sisaHari'] > 0 ? 'clock' : 'alert-circle' }} me-2"></i>
                {{ $jadwalApproval['sisaHari'] > 0 ? 'Approval belum dimulai' : 'Tenggat waktu telah berakhir!' }}
              </div>
            @endif
          </div>
        @else
          <div class="alert alert-warning d-flex align-items-center">
            <i class="ti ti-alert-triangle me-3 fs-4"></i>
            <div>
              <h6 class="mb-1">Jadwal approval belum ditetapkan</h6>
              <small class="m-0">Atur jadwal untuk universitas</small>
            </div>
          </div>
        @endif
      </div>
    </div>

    {{-- Tombol Aksi untuk Admin --}}
    <div class="d-grid gap-2 mt-3">
      <a class="btn btn-primary" href="tenggat_rencana">
        <i class="ti ti-calendar-edit me-2"></i> Kelola Jadwal
      </a>
    </div>

    {{-- Informasi ringkas tenggat waktu --}}
    <div class="border-top pt-3 mt-3">
      <div class="d-flex justify-content-between align-items-center">
        <small class="text-muted">Total Tenggat Berjalan:</small>
        <span class="badge bg-success">
          {{ ($jadwalPerencanaan ? 1 : 0) + ($jadwalValidasi ? 1 : 0) + ($jadwalVerifikasi ? 1 : 0) + ($jadwalApproval ? 1 : 0) }}/4
        </span>
      </div>
    </div>
  </div>
</div>
