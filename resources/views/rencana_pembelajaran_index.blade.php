@extends('layouts.main_layout', ['title' => 'Rencana Pembelajaran Anda'])
@section('content')
  <style>
    .no-column {
      width: 50px;
      /* Sesuaikan lebar sesuai kebutuhan */
      max-width: 50px;
      /* Batasi lebar maksimum */
      text-align: center;
      /* Pusatkan teks */
    }
  </style>

  <div class="container-fluid px-3">
    <!-- Page Header dengan Informasi Penting -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h2 class="mb-1">Rencana Pembelajaran</h2>
        <div class="d-flex align-items-center gap-2">
          <span class="badge bg-primary bg-opacity-10 text-primary">
            <i class="ti ti-user me-1"></i> {{ $dataPegawai->nama }}
          </span>
          <span class="badge bg-secondary bg-opacity-10 text-secondary">
            <i class="ti ti-id me-1"></i> {{ $dataPegawai->nppu }}
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
                <h6 class="text-muted mb-2">Total Rencana</h6>
                <h3 class="mb-0">{{ $rencana_pembelajaran->count() }}</h3>
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
                <h6 class="text-muted mb-2">Disetujui</h6>
                <h3 class="mb-0">{{ $notifikasi['disetujui'] }}</h3>
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
                <h6 class="text-muted mb-2">Perlu Revisi</h6>
                <h3 class="mb-0">{{ $notifikasi['direvisi'] }}</h3>
              </div>
              <div class="bg-warning bg-opacity-10 p-3 rounded">
                <i class="ti ti-alert-triangle text-warning" style="font-size: 1.5rem"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Tabel -->
    <div class="card mb-4 pb-4 bg-white">
      <div class="card-header p-3 bg-white">
        <div class="d-flex justify-content-between align-items-center">
          <h5 class="mb-0 fw-semibold">Daftar Rencana Pembelajaran</h5>
          <!-- Tombol Tambah -->
          @if ($isWithinDeadline && auth()->user()->dataPegawai->kelompok_id)
            <a href="/rencana_pembelajaran/create" class="btn btn-outline-primary btn-sm ms-3">
              <span class="me-1">
                <i class="ti ti-clipboard-plus"></i>
              </span>
              <span>Tambah Rencana Pembelajaran</span>
            </a>
          @else
            <button class="btn btn-outline-dark opacity-25 btn-sm ms-3" disabled>
              <span class="me-1">
                <i class="ti ti-clipboard-plus"></i>
              </span>
              <span>Tambah Rencana Pembelajaran</span>
              @unless (auth()->user()->dataPegawai->kelompok_id)
                <span class="ms-1 small text-dark fw-bolder">(Anda belum memiliki kelompok)</span>
              @endunless
            </button>
          @endif
        </div>
      </div>
      <hr class="mt-0 mb-1 text-body-tertiary">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover table-bordered mb-3" style="font-size: 0.7rem" id="myTable">
            <thead>
              <tr>
                <th class="align-middle" rowspan="2">No.</th>
                <th class="align-middle" rowspan="2">Tahun <br> Kode</th>
                <th class="align-middle" rowspan="2">Bentuk</th>
                <th class="align-middle" rowspan="2">Kegiatan</th>
                <th class="align-middle text-center" colspan="3">Validasi & Verifikasi</th>
                <th class="align-middle" rowspan="2">Rencana</th>
                <th class="align-middle" rowspan="2">Keterangan</th>
                <th class="align-middle" rowspan="2">AKSI</th>
              </tr>
              <tr>
                <th class="align-middle" style="font-size: 0.6rem">Ketua Kelompok</th>
                <th class="align-middle" style="font-size: 0.6rem">Pimpinan Unit Kerja</th>
                <th class="align-middle" style="font-size: 0.6rem">Universitas</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($rencana_pembelajaran as $index => $rencana)
                <tr>
                  <td class="px-2 text-center no-column">{{ $index + 1 }}</td>

                  {{-- TAHUN DAN KODE --}}
                  <td class="px-2 text-center">{{ $rencana->tahun }}
                    @if ($rencana->klasifikasi == 'pelatihan')
                      <br> <span class="fw-semibold">{{ ucwords($rencana->dataPelatihan->kode) }}</span>
                    @endif
                  </td>

                  {{-- BENTUK --}}
                  <td class="px-2">
                    @if ($rencana->klasifikasi == 'pelatihan')
                      @if ($rencana->bentukJalur->kategori->kategori == 'klasikal')
                        <span class="badge text-bg-secondary" style="font-size: 0.7rem">
                          {{ ucwords($rencana->bentukJalur->kategori->kategori) ?? '-' }}
                        </span>
                      @else
                        <span class="badge text-bg-warning" style="font-size: 0.7rem">
                          {{ ucwords($rencana->bentukJalur->kategori->kategori) ?? '-' }}
                        </span>
                      @endif
                      <br>
                      <span class="fw-semibold">Bentuk Jalur: </span>{{ $rencana->bentukJalur->bentuk_jalur ?? '' }}
                      <br>
                      <span class="fw-semibold">Rumpun:</span> {{ $rencana->dataPelatihan->rumpun->rumpun ?? '' }}
                    @elseif($rencana->klasifikasi == 'pendidikan')
                      <span class="badge text-bg-primary" style="font-size: 0.7rem">
                        {{ ucwords($rencana->klasifikasi) ?? '-' }}
                      </span><br>
                      <span class="fw-semibold">Jenjang:</span>
                      {{ $rencana->jenjang->jenjang ?? '' }}
                    @endif
                  </td>

                  {{-- KEGIATAN --}}
                  <td class="px-2">
                    @if ($rencana->klasifikasi == 'pelatihan')
                      <span class="fw-semibold">Nama Pelatihan: </span><br>
                      {{ $rencana->dataPelatihan->nama_pelatihan ?? '-' }}
                    @else
                      <span class="fw-semibold">Jurusan: </span><br>
                      {{ $rencana->dataPendidikan->jurusan ?? '-' }}
                    @endif
                  </td>

                  {{-- VERIFIKASI DAN VALIDASI --}}

                  {{-- Validasi Kelompok --}}
                  <td class="px-2">
                    @if ($rencana->kelompokCanValidating)
                      @php
                        $status = $rencana->kelompokCanValidating->status;
                        $badgeClass = $status == 'disetujui' ? 'text-bg-success' : 'text-bg-warning';
                        $statusText = $status == 'disetujui' ? 'Disetujui' : 'Direvisi';
                        $namaValidator = $rencana->kelompokCanValidating->kelompok->ketua->nama ?? 'Belum ditentukan';
                      @endphp
                      <span class="fw-semibold">Tahap:</span> <br>
                      <span class="badge {{ $badgeClass }} fs-1">{{ $statusText }}</span><br>

                      <div class="mt-1">
                        <span class="fw-semibold">Validator:</span><br>
                        <small class="text-muted">{{ $namaValidator }}</small>
                      </div>

                      @if ($status == 'direvisi' && $rencana->revisi_due_date)
                        @php
                          $deadline = \Carbon\Carbon::parse($rencana->revisi_due_date);
                          $diff = now()->diff($deadline);
                          $isOverdue = now()->greaterThan($deadline);
                        @endphp
                        <div class="mt-1 mb-1">
                          <span class="fw-semibold">Sisa Waktu Revisi:</span><br>
                          <span class="badge text-bg-{{ $isOverdue ? 'danger' : 'dark' }} fs-1">
                            {{ $isOverdue ? 'Waktu Habis' : ($diff->d > 0 ? $diff->d . 'h ' : '') . $diff->h . 'j ' . $diff->i . 'm' }}
                          </span>
                        </div>
                      @endif

                      @if ($status == 'direvisi')
                        <span class="fw-semibold">Catatan:</span>
                        @if ($catatan = $rencana->kelompokCanValidating->catatanValidasiKelompok)
                          <ul>
                            @foreach ($catatan as $item)
                              <li>{{ $item->catatan }}</li>
                            @endforeach
                          </ul>
                        @else
                          <div>-</div>
                        @endif
                      @endif
                    @elseif($rencana->status_pengajuan === 'diajukan')
                      <span class="fw-semibold">Tahap:</span>
                      <span class="badge text-bg-primary bg-opacity-75 fs-1">Ditinjau</span>
                    @else
                      <span style="font-size: 0.7rem">-</span>
                    @endif
                  </td>

                  {{-- Verifikasi Unit Kerja --}}
                  <td class="px-2">
                    @if ($rencana->unitKerjaCanverifying)
                      @php
                        $status = $rencana->unitKerjaCanverifying->status;
                        $badgeClass = $status == 'disetujui' ? 'text-bg-success' : 'text-bg-warning';
                        $statusText = $status == 'disetujui' ? 'Disetujui' : 'Direvisi';
                        $namaVerifikator = $rencana->unitKerjaCanverifying->dataPegawai->nama ?? 'Nama tidak ditemukan';
                      @endphp
                      <span class="fw-semibold">Tahap:</span> <br>
                      <span class="badge {{ $badgeClass }} fs-1">{{ $statusText }}</span><br>

                      <div class="mt-1">
                        <span class="fw-semibold">Verifikator:</span><br>
                        <small class="text-muted">{{ $namaVerifikator }}</small>
                      </div>

                      @if ($status == 'direvisi' && $rencana->revisi_due_date)
                        @php
                          $deadline = \Carbon\Carbon::parse($rencana->revisi_due_date);
                          $diff = now()->diff($deadline);
                          $isOverdue = now()->greaterThan($deadline);
                        @endphp
                        <div class="mt-1 mb-1">
                          <span class="fw-semibold">Sisa Waktu Revisi:</span><br>
                          <span class="badge text-bg-{{ $isOverdue ? 'danger' : 'dark' }} fs-1">
                            {{ $isOverdue ? 'Waktu Habis' : ($diff->d > 0 ? $diff->d . 'h ' : '') . $diff->h . 'j ' . $diff->i . 'm' }}
                          </span>
                        </div>
                      @endif

                      @if ($status == 'direvisi')
                        <span class="fw-semibold">Catatan:</span>
                        @if ($catatan = $rencana->unitKerjaCanverifying->catatanVerifikasi)
                          <ul>
                            @foreach ($catatan as $item)
                              <li>{{ $item->catatan }}</li>
                            @endforeach
                          </ul>
                        @else
                          <div>-</div>
                        @endif
                      @endif
                    @elseif($rencana->kelompokCanValidating && $rencana->kelompokCanValidating->status == 'disetujui')
                      <span class="fw-semibold">Tahap:</span>
                      <span class="badge text-bg-primary bg-opacity-75 fs-1">Ditinjau</span>
                    @else
                      <span style="font-size: 0.7rem">-</span>
                    @endif
                  </td>

                  {{-- Approval Universitas --}}
                  <td class="px-2">
                    @if ($rencana->universitasCanApproving)
                      @php
                        $approval = $rencana->universitasCanApproving;
                        $status = $approval->status;
                        $badgeClass = $status == 'disetujui' ? 'text-bg-success' : 'text-bg-danger';
                        $statusText = $status == 'disetujui' ? 'Disetujui' : 'Ditolak';
                      @endphp

                      <span class="fw-semibold">Tahap:</span> <br>
                      <span class="badge {{ $badgeClass }} fs-1">{{ $statusText }}</span>

                      {{-- Tampilkan Alasan jika Ditolak --}}
                      @if ($status == 'ditolak' && $approval->alasan_penolakan)
                        <div class="mt-1">
                          <span class="fw-semibold text-danger" style="font-size: 0.8rem">Alasan Penolakan:</span><br>
                          <small class="text-muted italic">"{{ $approval->alasan_penolakan }}"</small>
                        </div>
                      @endif
                    @elseif($rencana->unitKerjaCanverifying && $rencana->unitKerjaCanverifying->status == 'disetujui')
                      <span class="fw-semibold">Tahap:</span> <br>
                      <span class="badge text-bg-primary bg-opacity-75 fs-1">Ditinjau</span>
                    @else
                      <span style="font-size: 0.7rem">-</span>
                    @endif
                  </td>

                  {{-- RENCANA --}}
                  <td class="px-2">
                    <span class="fw-semibold">Region: </span>{{ ucwords($rencana->region->region) ?? '-' }} <br>
                    <span class="fw-semibold">JP: </span>{{ $rencana->jam_pelajaran }} JP <br>
                    <span class="fw-semibold">Anggaran:
                    </span>Rp{{ number_format($rencana->anggaran_rencana, 0, ',', '.') }}
                  </td>

                  <td class="px-2">
                    <span class="fw-semibold">Prioritas:</span>
                    @if ($rencana->prioritas == 'rendah')
                      <span class="badge rounded-pill text-bg-success" style="font-size: 0.7rem">Rendah</span>
                    @elseif ($rencana->prioritas == 'sedang')
                      <span class="badge rounded-pill text-bg-warning" style="font-size: 0.7rem">Sedang</span>
                    @elseif ($rencana->prioritas == 'tinggi')
                      <span class="badge rounded-pill text-bg-danger" style="font-size: 0.7rem">Tinggi</span>
                    @endif
                    <br>

                    <span class="fw-semibold">Status:</span>
                    {{ ucwords($rencana->status_pengajuan) }}
                    <br>
                  </td>
                  {{-- AKSI --}}
                  @if ($isWithinDeadline)
                    <td class="px-2">
                      @if ($rencana->kelompokCanValidating)
                        @if ($rencana->kelompokCanValidating->status == 'disetujui')
                          {{-- Cek Unit Kerja --}}
                          @if ($rencana->unitKerjaCanverifying)
                            @if ($rencana->universitasCanApproving && $rencana->universitasCanApproving->status == 'disetujui')
                              <strong>Download Surat Rekomendasi:</strong><br>
                              <a href="{{ route('rencana.download_rekomendasi', $rencana->id) }}"
                                class="btn btn-primary btn-sm mt-1" target="_blank" {{-- Agar terbuka di tab baru --}}
                                style="font-size: 0.8rem">
                                <span class="ti ti-file-download"></span>
                              </a>
                            @else
                              {{-- Jika belum disetujui Univ, tampilkan aksi verifikasi unit kerja seperti biasa --}}
                              @include('partials.rencana_aksi.aksi_unit_kerja_verifikasi', [
                                  'rencana' => $rencana,
                              ])
                            @endif
                          @else
                            <div class="alert alert-info p-2 mb-2" style="font-size: 0.75rem;">
                              <span class="ti ti-clock"></span>
                              <strong>Rencana Menunggu Verifikasi Unit Kerja</strong><br>
                              Silahkan tunggu proses verifikasi oleh Unit Kerja
                            </div>
                          @endif
                        @else
                          @include('partials.rencana_aksi.aksi_kelompok_validating', [
                              'rencana' => $rencana,
                          ])
                        @endif
                      @elseif($rencana->status_pengajuan === 'draft')
                        <div class="btn-group" role="group">
                          <a href="/rencana_pembelajaran/{{ $rencana->id }}/edit" class="btn btn-warning btn-sm"
                            style="font-size: 0.8rem" title="Edit">
                            <span class="ti ti-pencil"></span>
                          </a>
                          <form action="/rencana_pembelajaran/{{ $rencana->id }}" method="POST"
                            class="d-inline deleteForm">
                            @csrf @method('delete')
                            <button type="submit" class="btn btn-danger btn-sm deleteAlert"
                              style="font-size: 0.8rem; border-radius: 0" title="Hapus">
                              <span class="ti ti-trash"></span>
                            </button>
                          </form>
                          <form action="{{ route('rencana.ajukan', $rencana->id) }}" method="POST"
                            id="ajukanForm-{{ $rencana->id }}">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm rounded-end-1 ajukanAlert"
                              style="font-size: 0.8rem; border-radius: 0" title="Ajukan verifikasi"
                              data-form-id="ajukanForm-{{ $rencana->id }}">
                              <span class="ti ti-send"></span>
                            </button>
                          </form>
                        </div>
                      @else
                        <div class="alert alert-warning p-2 mb-2" style="font-size: 0.75rem;">
                          <span class="ti ti-clock"></span>
                          <strong>Rencana Menunggu Validasi Ketua Kelompok</strong><br>
                          Silahkan tunggu proses validasi oleh Ketua Kelompok
                        </div>
                      @endif
                    </td>
                  @else
                    <td class="px-2">
                      <div class="alert alert-danger p-2 mb-2" style="font-size: 0.75rem;">
                        <span class="ti ti-clock-stop"></span>
                        <strong>Tenggat Waktu Sudah Berakhir</strong><br>
                        Tidak dapat melakukan edit atau hapus di luar tenggat waktu
                      </div>
                    </td>
                  @endif
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
@endsection
