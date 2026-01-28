{{-- ROW 6: STATISTIK KOMPREHENSIF APPROVER UNIVERSITAS --}}
@if ((in_array('approver', $roles) || in_array('pimpinan', $roles)) && $statistikApprovalUniversitas)
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header bg-success bg-opacity-10">
          <h5 class="card-title mb-0 fw-bold text-success">
            <i class="ti ti-school me-2"></i>Dashboard Analisis Komprehensif - Approver Universitas
          </h5>
        </div>
        <div class="card-body">
          {{-- RINGKASAN UTAMA --}}
          <div class="row mb-4">
            <div class="col-md-3 col-sm-6">
              <div class="bg-light rounded p-3 text-center border-start border-4 border-primary">
                <div class="bg-primary bg-opacity-10 rounded-circle p-3 d-inline-flex mb-2">
                  <i class="ti ti-checklist text-primary fs-3"></i>
                </div>
                <h3 class="fw-bold mb-1">{{ $statistikApprovalUniversitas['total'] }}</h3>
                <p class="text-muted mb-0">Total Rencana</p>
                <small class="text-muted">Terverifikasi Unit Kerja</small>
              </div>
            </div>
            <div class="col-md-3 col-sm-6">
              <div class="bg-light rounded p-3 text-center border-start border-4 border-success">
                <div class="bg-success bg-opacity-10 rounded-circle p-3 d-inline-flex mb-2">
                  <i class="ti ti-cash text-success fs-3"></i>
                </div>
                <h3 class="fw-bold mb-1">Rp
                  {{ number_format($statistikApprovalUniversitas['total_anggaran'] / 1000000, 1) }}JT</h3>
                <p class="text-muted mb-0">Total Anggaran</p>
                <small class="text-muted">Seluruh rencana</small>
              </div>
            </div>
            <div class="col-md-3 col-sm-6">
              <div class="bg-light rounded p-3 text-center border-start border-4 border-info">
                <div class="bg-info bg-opacity-10 rounded-circle p-3 d-inline-flex mb-2">
                  <i class="ti ti-clock text-info fs-3"></i>
                </div>
                <h3 class="fw-bold mb-1">{{ $statistikApprovalUniversitas['total_jam_pelajaran'] }}</h3>
                <p class="text-muted mb-0">Jam Pelajaran</p>
                <small class="text-muted">Total keseluruhan</small>
              </div>
            </div>
            <div class="col-md-3 col-sm-6">
              <div class="bg-light rounded p-3 text-center border-start border-4 border-warning">
                <div class="bg-warning bg-opacity-10 rounded-circle p-3 d-inline-flex mb-2">
                  <i class="ti ti-building text-warning fs-3"></i>
                </div>
                <h3 class="fw-bold mb-1">{{ $statistikApprovalUniversitas['jumlah_unit_kerja'] }}</h3>
                <p class="text-muted mb-0">Unit Kerja</p>
                <small class="text-muted">Terlibat</small>
              </div>
            </div>
          </div>

          {{-- STATUS APPROVAL & ANGGARAN --}}
          <div class="row mb-4">
            <div class="col-md-8">
              <div class="card h-100 shadow-none border">
                <div class="card-header bg-primary bg-opacity-10">
                  <h6 class="card-title mb-0 fw-bold text-primary">
                    <i class="ti ti-progress-check me-1"></i>Status Approval & Alokasi Anggaran
                  </h6>
                </div>
                <div class="card-body">
                  <div class="row mb-3">
                    <div class="col-md-4 text-center">
                      <div class="border rounded p-3 bg-success bg-opacity-10">
                        <h4 class="fw-bold text-success mb-1">{{ $statistikApprovalUniversitas['disetujui'] }}</h4>
                        <p class="mb-1 small text-muted">Disetujui</p>
                        <p class="mb-0 small text-success fw-bold">Rp
                          {{ number_format($statistikApprovalUniversitas['anggaran_disetujui'], 0, ',', '.') }}</p>
                        <p class="mb-0 small text-muted">{{ $statistikApprovalUniversitas['persen_disetujui'] }}%</p>
                      </div>
                    </div>
                    <div class="col-md-4 text-center">
                      <div class="border rounded p-3 bg-danger bg-opacity-10">
                        <h4 class="fw-bold text-danger mb-1">{{ $statistikApprovalUniversitas['ditolak'] }}</h4>
                        <p class="mb-1 small text-muted">Ditolak</p>
                        <p class="mb-0 small text-danger fw-bold">Rp
                          {{ number_format($statistikApprovalUniversitas['anggaran_ditolak'], 0, ',', '.') }}</p>
                        <p class="mb-0 small text-muted">{{ $statistikApprovalUniversitas['persen_ditolak'] }}%</p>
                      </div>
                    </div>
                    <div class="col-md-4 text-center">
                      <div class="border rounded p-3 bg-secondary bg-opacity-10">
                        <h4 class="fw-bold text-secondary mb-1">{{ $statistikApprovalUniversitas['belum'] }}</h4>
                        <p class="mb-1 small text-muted">Belum Diproses</p>
                        <p class="mb-0 small text-secondary fw-bold">Rp
                          {{ number_format($statistikApprovalUniversitas['anggaran_belum'], 0, ',', '.') }}</p>
                        <p class="mb-0 small text-muted">{{ $statistikApprovalUniversitas['persen_belum'] }}%</p>
                      </div>
                    </div>
                  </div>
                  <div class="progress mb-4" style="height: 25px;">
                    <div class="progress-bar bg-success fw-bold"
                      style="width: {{ $statistikApprovalUniversitas['persen_disetujui'] }}%;" role="progressbar"
                      aria-valuenow="{{ $statistikApprovalUniversitas['persen_disetujui'] }}" aria-valuemin="0"
                      aria-valuemax="100" data-bs-toggle="tooltip"
                      title="Disetujui: {{ $statistikApprovalUniversitas['disetujui'] }} rencana (Rp {{ number_format($statistikApprovalUniversitas['anggaran_disetujui'], 0, ',', '.') }})">
                      {{ $statistikApprovalUniversitas['persen_disetujui'] }}%
                    </div>
                    <div class="progress-bar bg-danger fw-bold"
                      style="width: {{ $statistikApprovalUniversitas['persen_ditolak'] }}%;" role="progressbar"
                      aria-valuenow="{{ $statistikApprovalUniversitas['persen_ditolak'] }}" aria-valuemin="0"
                      aria-valuemax="100" data-bs-toggle="tooltip"
                      title="Ditolak: {{ $statistikApprovalUniversitas['ditolak'] }} rencana (Rp {{ number_format($statistikApprovalUniversitas['anggaran_ditolak'], 0, ',', '.') }})">
                      {{ $statistikApprovalUniversitas['persen_ditolak'] }}%
                    </div>
                    <div class="progress-bar fw-bold"
                      style="width: {{ $statistikApprovalUniversitas['persen_belum'] }}%; background-color: #6c757d;"
                      role="progressbar" aria-valuenow="{{ $statistikApprovalUniversitas['persen_belum'] }}"
                      aria-valuemin="0" aria-valuemax="100" data-bs-toggle="tooltip"
                      title="Belum: {{ $statistikApprovalUniversitas['belum'] }} rencana (Rp {{ number_format($statistikApprovalUniversitas['anggaran_belum'], 0, ',', '.') }})">
                      {{ $statistikApprovalUniversitas['persen_belum'] }}%
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="card h-100 shadow-none border">
                <div class="card-header bg-info bg-opacity-10">
                  <h6 class="card-title mb-0 fw-bold text-info">
                    <i class="ti ti-chart-pie me-1"></i>Distribusi Jenis Kegiatan
                  </h6>
                </div>
                <div class="card-body">
                  <canvas id="jenisKegiatanUnivChart" height="200"></canvas>
                </div>
              </div>
            </div>
          </div>

          {{-- ANALISIS PER UNIT KERJA --}}
          <div class="row mb-4">
            <div class="col-12">
              <div class="card h-100 shadow-none border">
                <div class="card-header bg-warning bg-opacity-10">
                  <h6 class="card-title mb-0 fw-bold text-warning">
                    <i class="ti ti-building me-1"></i>Analisis Per Unit Kerja
                  </h6>
                </div>
                <div class="card-body">
                  <canvas id="unitKerjaChart" height="200"></canvas>
                </div>
              </div>
            </div>
          </div>

          {{-- TREND TAHUNAN & PRIORITAS --}}
          <div class="row mb-4">
            <div class="col-md-6">
              <div class="card h-100 shadow-none border">
                <div class="card-header bg-success bg-opacity-10">
                  <h6 class="card-title mb-0 fw-bold text-success">
                    <i class="ti ti-trending-up me-1"></i>Trend Rencana per Tahun
                  </h6>
                </div>
                <div class="card-body">
                  <canvas id="tahunTrendChart" height="250"></canvas>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="card h-100 shadow-none border">
                <div class="card-header bg-primary bg-opacity-10">
                  <h6 class="card-title mb-0 fw-bold text-primary">
                    <i class="ti ti-target-arrow me-1"></i>Distribusi Berdasarkan Prioritas
                  </h6>
                </div>
                <div class="card-body">
                  <canvas id="prioritasChart" height="250"></canvas>
                </div>
              </div>
            </div>
          </div>

          {{-- TABEL DETAIL UNIT KERJA --}}
          <div class="card mb-1 shadow-none border">
            <div class="card-header bg-secondary bg-opacity-10">
              <h6 class="card-title mb-0 fw-bold text-secondary">
                <i class="ti ti-table me-1"></i>Rincian Per Unit Kerja
              </h6>
            </div>
            <div class="card-body p-3">
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>Unit Kerja</th>
                      <th>Total Rencana</th>
                      <th>Total Anggaran</th>
                      <th>Rata-rata Anggaran</th>
                      <th>Jam Pelajaran</th>
                      <th>Disetujui</th>
                      <th>Ditolak</th>
                      <th>Pending</th>
                      <th>Tingkat Approval</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($statistikApprovalUniversitas['analisis_unit_kerja'] as $unit => $data)
                      @php
                        $approvalRate = $data['total'] > 0 ? round(($data['approved'] / $data['total']) * 100, 1) : 0;
                      @endphp
                      <tr>
                        <td>{{ $unit }}</td>
                        <td><span class="badge bg-primary">{{ $data['total'] }}</span></td>
                        <td>Rp {{ number_format($data['total_anggaran'], 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($data['avg_anggaran'], 0, ',', '.') }}</td>
                        <td>{{ $data['avg_jam'] }} jam</td>
                        <td><span class="badge bg-success">{{ $data['approved'] }}</span></td>
                        <td><span class="badge bg-danger">{{ $data['rejected'] }}</span></td>
                        <td><span class="badge bg-secondary">{{ $data['pending'] }}</span></td>
                        <td>
                          <div class="d-flex align-items-center">
                            <div class="progress flex-grow-1 me-2" style="height: 6px;">
                              <div
                                class="progress-bar {{ $approvalRate >= 80 ? 'bg-success' : ($approvalRate >= 50 ? 'bg-warning' : 'bg-danger') }}"
                                style="width: {{ $approvalRate }}%"></div>
                            </div>
                            <small class="text-muted">{{ $approvalRate }}%</small>
                          </div>
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

  @push('scripts')
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        // Chart Jenis Kegiatan Universitas
        const jenisKegiatanUnivCtx = document.getElementById('jenisKegiatanUnivChart').getContext('2d');
        new Chart(jenisKegiatanUnivCtx, {
          type: 'doughnut',
          data: {
            labels: @json(array_keys($statistikApprovalUniversitas['analisis_jenis']->toArray())),
            datasets: [{
              data: @json(array_column($statistikApprovalUniversitas['analisis_jenis']->toArray(), 'count')),
              backgroundColor: ['#198754', '#0dcaf0', '#ffc107'],
              borderWidth: 1
            }]
          },
          options: {
            responsive: true,
            plugins: {
              legend: {
                position: 'bottom'
              }
            }
          }
        });

        // Chart Unit Kerja
        const unitKerjaCtx = document.getElementById('unitKerjaChart').getContext('2d');
        new Chart(unitKerjaCtx, {
          type: 'bar',
          data: {
            labels: @json(array_keys($statistikApprovalUniversitas['analisis_unit_kerja']->toArray())),
            datasets: [{
              label: 'Total Anggaran (Juta)',
              data: @json(array_map(function ($item) {
                      return $item['total_anggaran'] / 1000000;
                  }, $statistikApprovalUniversitas['analisis_unit_kerja']->toArray())),
              backgroundColor: 'rgba(13, 110, 253, 0.7)',
              yAxisID: 'y'
            }, {
              label: 'Jumlah Rencana',
              data: @json(array_column($statistikApprovalUniversitas['analisis_unit_kerja']->toArray(), 'total')),
              backgroundColor: 'rgba(255, 193, 7, 0.7)',
              yAxisID: 'y1',
              type: 'line'
            }]
          },
          options: {
            responsive: true,
            scales: {
              y: {
                type: 'linear',
                position: 'left',
                title: {
                  display: true,
                  text: 'Anggaran (Juta Rupiah)'
                }
              },
              y1: {
                type: 'linear',
                position: 'right',
                title: {
                  display: true,
                  text: 'Jumlah Rencana'
                },
                grid: {
                  drawOnChartArea: false
                }
              }
            }
          }
        });

        // Chart Trend Tahunan
        const tahunTrendCtx = document.getElementById('tahunTrendChart').getContext('2d');
        new Chart(tahunTrendCtx, {
          type: 'line',
          data: {
            labels: @json(array_keys($statistikApprovalUniversitas['analisis_tahun']->toArray())),
            datasets: [{
              label: 'Jumlah Rencana',
              data: @json(array_column($statistikApprovalUniversitas['analisis_tahun']->toArray(), 'count')),
              borderColor: '#198754',
              backgroundColor: 'rgba(25, 135, 84, 0.1)',
              fill: true
            }, {
              label: 'Total Anggaran (Miliar)',
              data: @json(array_map(function ($item) {
                      return $item['total_anggaran'] / 1000000000;
                  }, $statistikApprovalUniversitas['analisis_tahun']->toArray())),
              borderColor: '#0d6efd',
              backgroundColor: 'rgba(13, 110, 253, 0.1)',
              fill: true,
              yAxisID: 'y1'
            }]
          },
          options: {
            responsive: true,
            scales: {
              y: {
                title: {
                  display: true,
                  text: 'Jumlah Rencana'
                }
              },
              y1: {
                position: 'right',
                title: {
                  display: true,
                  text: 'Anggaran (Miliar)'
                },
                grid: {
                  drawOnChartArea: false
                }
              }
            }
          }
        });

        // Chart Prioritas
        const prioritasCtx = document.getElementById('prioritasChart').getContext('2d');
        new Chart(prioritasCtx, {
          type: 'bar',
          data: {
            labels: @json(array_keys($statistikApprovalUniversitas['analisis_prioritas']->toArray())),
            datasets: [{
              label: 'Jumlah Rencana',
              data: @json(array_column($statistikApprovalUniversitas['analisis_prioritas']->toArray(), 'count')),
              backgroundColor: [
                'rgba(220, 53, 69, 0.7)', // Tinggi - Merah
                'rgba(255, 193, 7, 0.7)', // Sedang - Kuning
                'rgba(40, 167, 69, 0.7)' // Rendah - Hijau
              ]
            }]
          },
          options: {
            responsive: true,
            scales: {
              y: {
                beginAtZero: true
              }
            }
          }
        });
      });
    </script>
  @endpush
@endif
