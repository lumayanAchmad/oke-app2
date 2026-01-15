<div class="table-responsive">
  <table class="table table-hover table-bordered mb-3 px-0 datatables" style="font-size: 0.7rem">
    <thead>
      <tr>
        <th class="align-middle">No.</th>
        <th class="align-middle">Nama</th>
        <th class="align-middle">Tahun <br> Kode</th>
        <th class="align-middle">Bentuk</th>
        <th class="align-middle">Kegiatan</th>
        <th class="align-middle">Approval</th>
        <th class="align-middle">Rencana</th>
        <th class="align-middle">Prioritas</th>
        <th class="align-middle">AKSI</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($kelompokData['rencanaDisetujui'] as $index => $rencanaPembelajaran)
        <tr>
          {{-- NOMOR --}}
          <td class="text-center px-2">{{ $loop->iteration }}</td>

          {{-- NAMA PEGAWAI --}}
          <td class="px-2">{{ $rencanaPembelajaran->dataPegawai->nama }}</td>

          {{-- TAHUN DAN KODE --}}
          <td class="text-center px-2">{{ $rencanaPembelajaran->tahun }}
            @if ($rencanaPembelajaran->klasifikasi == 'pelatihan')
              <br><span class="fw-semibold">{{ $rencanaPembelajaran->dataPelatihan->kode }}</span>
            @endif
          </td>

          {{-- BENTUK --}}
          <td class="px-2">
            @if ($rencanaPembelajaran->klasifikasi == 'pelatihan')
              @if ($rencanaPembelajaran->bentukJalur->kategori->kategori == 'klasikal')
                <span class="badge text-bg-secondary" style="font-size: 0.7rem">
                  {{ ucwords($rencanaPembelajaran->bentukJalur->kategori->kategori) ?? '-' }}
                </span>
              @else
                <span class="badge text-bg-warning" style="font-size: 0.7rem">
                  {{ ucwords($rencanaPembelajaran->bentukJalur->kategori->kategori) ?? '-' }}
                </span>
              @endif
              <br>
              <span class="fw-semibold">Bentuk Jalur: </span>{{ $rencanaPembelajaran->bentukJalur->bentuk_jalur ?? '' }}
              <br>
              <span class="fw-semibold">Rumpun:</span> {{ $rencanaPembelajaran->dataPelatihan->rumpun->rumpun ?? '' }}
            @elseif($rencanaPembelajaran->klasifikasi == 'pendidikan')
              <span class="badge text-bg-primary" style="font-size: 0.7rem">
                {{ ucwords($rencanaPembelajaran->klasifikasi) ?? '-' }}
              </span><br>
              <span class="fw-semibold">Jenjang:</span>
              {{ $rencanaPembelajaran->jenjang->jenjang ?? '' }}
              <br><span class="fw-semibold">Jenis Pendidikan: </span>
              {{ strtoupper($rencanaPembelajaran->jenisPendidikan->jenis_pendidikan) ?? '' }}
            @endif
          </td>

          {{-- KEGIATAN --}}
          <td class="px-2">
            @if ($rencanaPembelajaran->klasifikasi == 'pelatihan')
              <span class="fw-semibold">Nama Pelatihan: </span><br>
              {{ $rencanaPembelajaran->dataPelatihan->nama_pelatihan ?? '-' }}
            @else
              <span class="fw-semibold">Jurusan: </span><br>
              {{ $rencanaPembelajaran->dataPendidikan->jurusan ?? '-' }}
            @endif
          </td>

          {{-- Approval --}}
          <td class="px-2">
            @if ($rencanaPembelajaran->universitasCanApproving)
              @php
                $statusUniv = $rencanaPembelajaran->universitasCanApproving->status;
                $badgeClassUniv = $statusUniv == 'disetujui' ? 'text-bg-success' : 'text-bg-warning';
                $statusTextUniv = $statusUniv == 'disetujui' ? 'Disetujui' : 'Direvisi';
              @endphp

              <span class="fw-semibold">Tahap:</span> <br>
              <span class="badge {{ $badgeClassUniv }} fs-1">{{ $statusTextUniv }}</span><br>

              {{-- Sisa Waktu jika Universitas meminta revisi --}}
              @if ($statusUniv == 'direvisi' && $rencanaPembelajaran->revisi_due_date)
                @php
                  $deadlineUniv = \Carbon\Carbon::parse($rencanaPembelajaran->revisi_due_date);
                  $diffUniv = now()->diff($deadlineUniv);
                  $isOverdueUniv = now()->greaterThan($deadlineUniv);
                @endphp
                <div class="mt-1 mb-1">
                  <span class="fw-semibold">Sisa Waktu:</span><br>
                  <span class="badge text-bg-{{ $isOverdueUniv ? 'danger' : 'dark' }} fs-1">
                    {{ $isOverdueUniv ? 'Waktu Habis' : ($diffUniv->d > 0 ? $diffUniv->d . 'h ' : '') . $diffUniv->h . 'j ' . $diffUniv->i . 'm' }}
                  </span>
                </div>
              @endif

              {{-- Catatan Universitas --}}
              @if ($statusUniv == 'direvisi')
                <span class="fw-semibold">Catatan:</span>
                @if ($rencanaPembelajaran->universitasCanApproving->catatanApprovalUniversitas->isNotEmpty())
                  <ul class="mb-0 ps-3">
                    @foreach ($rencanaPembelajaran->universitasCanApproving->catatanApprovalUniversitas as $item)
                      <li><small>{{ $item->catatan }}</small></li>
                    @endforeach
                  </ul>
                @else
                  <div>-</div>
                @endif
              @endif
            @elseif($rencanaPembelajaran->unitKerjaCanVerifying && $rencanaPembelajaran->unitKerjaCanVerifying->status === 'disetujui')
              {{-- Status otomatis jika Unit Kerja sudah OK tapi Universitas belum proses --}}
              <span class="fw-semibold">Tahap:</span><br>
              <span class="badge text-bg-primary bg-opacity-75 fs-1">Ditinjau</span>
            @else
              {{-- Belum sampai tahap Universitas --}}
              <span style="font-size: 0.7rem">-</span>
            @endif
          </td>

          {{-- RENCANA --}}
          <td class="px-2">
            <span class="fw-semibold">Region: </span>{{ ucwords($rencanaPembelajaran->region->region) ?? '-' }} <br>
            <span class="fw-semibold">JP: </span>{{ $rencanaPembelajaran->jam_pelajaran }} JP <br>
            <span class="fw-semibold">Anggaran:
            </span>Rp{{ number_format($rencanaPembelajaran->anggaran_rencana, 0, ',', '.') }}
          </td>

          {{-- PRIORITAS --}}
          <td class="px-1 text-center">
            @if ($rencanaPembelajaran->prioritas == 'rendah')
              <span class="badge rounded-pill text-bg-success" style="font-size: 0.7rem">Rendah</span>
            @elseif ($rencanaPembelajaran->prioritas == 'sedang')
              <span class="badge rounded-pill text-bg-warning" style="font-size: 0.7rem">Sedang</span>
            @elseif ($rencanaPembelajaran->prioritas == 'tinggi')
              <span class="badge rounded-pill text-bg-danger" style="font-size: 0.7rem">Tinggi</span>
            @endif
          </td>

          {{-- AKSI Verifikasi Unit Kerja --}}
          <td class="px-2 text-center">
            @php
              // Cek apakah sudah ada tindakan dari pihak Universitas
              $isLockedByUniv = $rencanaPembelajaran->universitasCanApproving !== null;
            @endphp

            @if ($isWithinDeadline && !$isLockedByUniv)
              {{-- TOMBOL BATAL AKTIF --}}
              <form action="{{ route('verifikasi.destroy', $rencanaPembelajaran->unitKerjaCanVerifying->id) }}"
                method="post" id="batalSetujuiForm-{{ $rencanaPembelajaran->id }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm batalSetujuiAlert" title="Batalkan Persetujuan"
                  data-form-id="batalSetujuiForm-{{ $rencanaPembelajaran->id }}">
                  <span class="ti ti-arrow-back fs-3"></span>
                </button>
              </form>
            @else
              {{-- TAMPILAN TERKUNCI --}}
              <div class="d-flex flex-column align-items-center">
                <span class="ti ti-lock text-muted fs-4"></span>
                <small class="text-muted" style="font-size: 0.6rem; text-align: center;">
                  @if ($isLockedByUniv)
                    Diproses Universitas
                  @else
                    Waktu Verifikasi Habis
                  @endif
                </small>
              </div>
            @endif
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
