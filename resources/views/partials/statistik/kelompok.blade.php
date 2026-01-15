@if ($statistikValidasiKetua)
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-10">
          <h5 class="card-title mb-0 fw-bold text-primary">
            <i class="ti ti-crown me-2"></i>Statistik Validasi Ketua Kelompok
          </h5>
        </div>
        <div class="card-body">
          {{-- Ringkasan Statistik --}}
          <div class="row mb-2">
            <div class="col-md-3 col-sm-6 mb-3">
              <div class="bg-light rounded border p-3 text-center">
                <div class="bg-primary bg-opacity-10 rounded-circle p-3 d-inline-flex mb-2">
                  <i class="ti ti-users text-primary fs-3"></i>
                </div>
                <h4 class="fw-bold mb-0">{{ $statistikValidasiKetua['jumlah_anggota'] }}</h4>
                <p class="text-muted mb-0">Anggota Kelompok</p>
              </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
              <div class="bg-light rounded border p-3 text-center">
                <div class="bg-success bg-opacity-10 rounded-circle p-3 d-inline-flex mb-2">
                  <i class="ti ti-checklist text-success fs-3"></i>
                </div>
                <h4 class="fw-bold mb-0">{{ $statistikValidasiKetua['total'] }}</h4>
                <p class="text-muted mb-0">Total Rencana</p>
              </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
              <div class="bg-light rounded border p-3 text-center">
                <div class="bg-info bg-opacity-10 rounded-circle p-3 d-inline-flex mb-2">
                  <i class="ti ti-chart-bar text-info fs-3"></i>
                </div>
                <h4 class="fw-bold mb-0">{{ $statistikValidasiKetua['rencana_per_anggota'] }}</h4>
                <p class="text-muted mb-0">Rata-rata per Anggota</p>
              </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
              <div class="bg-light rounded border p-3 text-center">
                <div class="bg-warning bg-opacity-10 rounded-circle p-3 d-inline-flex mb-2">
                  <i class="ti ti-circle-check text-warning fs-3"></i>
                </div>
                <h4 class="fw-bold mb-0">
                  {{ $statistikValidasiKetua['persen_disetujui'] + $statistikValidasiKetua['persen_direvisi'] }}%</h4>
                <p class="text-muted mb-0">Telah Divalidasi</p>
              </div>
            </div>
          </div>

          {{-- Progress Validasi --}}
          <div class="card border shadow-none mb-4">
            <div class="card-header bg-warning bg-opacity-10">
              <h6 class="card-title mb-0 fw-bold text-warning">
                <i class="ti ti-checklist me-1"></i>Progress Validasi Rencana Anggota
              </h6>
            </div>
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted">Total: {{ $statistikValidasiKetua['total'] }} Rencana</span>
                <span
                  class="badge bg-primary">{{ $statistikValidasiKetua['persen_disetujui'] + $statistikValidasiKetua['persen_direvisi'] }}%
                  Tervalidasi</span>
              </div>

              <div class="progress mb-4" style="height:25px;">
                <div class="progress-bar bg-success fw-bold"
                  style="width: {{ $statistikValidasiKetua['persen_disetujui'] }}%;"
                  title="Disetujui: {{ $statistikValidasiKetua['disetujui'] }} ({{ $statistikValidasiKetua['persen_disetujui'] }}%)">
                  {{ $statistikValidasiKetua['persen_disetujui'] }}%
                </div>
                <div class="progress-bar bg-warning fw-bold"
                  style="width: {{ $statistikValidasiKetua['persen_direvisi'] }}%;"
                  title="Direvisi: {{ $statistikValidasiKetua['direvisi'] }} ({{ $statistikValidasiKetua['persen_direvisi'] }}%)">
                  {{ $statistikValidasiKetua['persen_direvisi'] }}%
                </div>
                <div class="progress-bar fw-bold"
                  style="width: {{ $statistikValidasiKetua['persen_belum'] }}%; background-color:#959595;"
                  title="Belum: {{ $statistikValidasiKetua['belum'] }} ({{ $statistikValidasiKetua['persen_belum'] }}%)">
                  {{ $statistikValidasiKetua['persen_belum'] }}%
                </div>
              </div>

              <div class="row text-center">
                <div class="col-md-4 mb-2">
                  <div class="border rounded p-3 bg-success bg-opacity-10">
                    <h3 class="fw-bold text-success mb-1">{{ $statistikValidasiKetua['disetujui'] }}</h3>
                    <p class="mb-0 small text-muted">Disetujui</p>
                  </div>
                </div>
                <div class="col-md-4 mb-2">
                  <div class="border rounded p-3 bg-warning bg-opacity-10">
                    <h3 class="fw-bold text-warning mb-1">{{ $statistikValidasiKetua['direvisi'] }}</h3>
                    <p class="mb-0 small text-muted">Direvisi</p>
                  </div>
                </div>
                <div class="col-md-4 mb-2">
                  <div class="border rounded p-3" style="background-color: rgba(149, 149, 149, 0.1);">
                    <h3 class="fw-bold mb-1" style="color: #959595">{{ $statistikValidasiKetua['belum'] }}</h3>
                    <p class="mb-0 small text-muted" style="color: #959595">Belum Divalidasi</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          {{-- Tabel Anggota Kelompok --}}
          @php
            $anggotaKelompok = App\Models\DataPegawai::where('kelompok_id', $dataPegawai->kelompok_id)
                ->where('id', '!=', $dataPegawai->id) // Exclude ketua sendiri
                ->withCount(['rencanaPembelajaran as total_rencana'])
                ->get();
          @endphp

          <div class="card mb-1 border shadow-none">
            <div class="card-header bg-info bg-opacity-10">
              <h6 class="card-title mb-0 fw-bold text-info">
                <i class="ti ti-users me-1"></i>Anggota Kelompok
              </h6>
            </div>
            <div class="card-body p-3">
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead class="fs-3">
                    <tr>
                      <th class="w-25">Nama</th>
                      <th>NPPU</th>
                      <th>Jumlah Rencana</th>
                      <th>Status</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody class="fs-3">
                    @foreach ($anggotaKelompok as $anggota)
                      @php
                        $rencanaDisetujui = App\Models\kelompokCanValidating::where(
                            'kelompok_id',
                            $dataPegawai->kelompok_id,
                        )
                            ->whereIn('rencana_pembelajaran_id', $anggota->rencanaPembelajaran->pluck('id'))
                            ->where('status', 'disetujui')
                            ->count();

                        $rencanaDirevisi = App\Models\kelompokCanValidating::where(
                            'kelompok_id',
                            $dataPegawai->kelompok_id,
                        )
                            ->whereIn('rencana_pembelajaran_id', $anggota->rencanaPembelajaran->pluck('id'))
                            ->where('status', 'direvisi')
                            ->count();

                        $persentase =
                            $anggota->total_rencana > 0
                                ? round((($rencanaDisetujui + $rencanaDirevisi) / $anggota->total_rencana) * 100, 1)
                                : 0;
                      @endphp
                      <tr>
                        <td class="fw-medium w-25">{{ $anggota->nama }}</td>
                        <td>{{ $anggota->nppu }}</td>
                        <td class="text-center">
                          <span class="badge bg-primary">{{ $anggota->total_rencana }}</span>
                        </td>
                        <td>
                          @if ($anggota->total_rencana > 0)
                            <div class="d-flex align-items-center">
                              <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                <div class="progress-bar {{ $persentase == 100 ? 'bg-success' : 'bg-info' }}"
                                  role="progressbar" style="width: {{ $persentase }}%;"
                                  aria-valuenow="{{ $persentase }}" aria-valuemin="0" aria-valuemax="100"></div>
                              </div>
                              <small class="text-muted">{{ $persentase }}%</small>
                            </div>
                            <small class="text-muted">
                              {{ $rencanaDisetujui }} disetujui, {{ $rencanaDirevisi }} direvisi
                            </small>
                          @else
                            <span class="badge fs-2" style="background-color: #959595">Belum ada rencana</span>
                          @endif
                        </td>
                        <td>
                          <a href="{{ route('validasi_kelompok.index', $anggota->id) }}"
                            class="btn btn-sm btn-outline-primary">
                            <i class="ti ti-eye me-1"></i>Lihat Rencana
                          </a>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endif
