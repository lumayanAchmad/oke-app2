<div class="table-responsive">
  <!-- Tabel untuk menampilkan daftar rencana pembelajaran -->
  <table class="table table-hover table-bordered mb-3 datatables" style="font-size: 0.7rem">
    <thead>
      <tr>
        <!-- Kolom-kolom tabel -->
        <th class="align-middle" rowspan="2">No.</th>
        <th class="align-middle" rowspan="2">Nama</th>
        <th class="align-middle" rowspan="2">Tahun <br> Kode</th>
        <th class="align-middle" rowspan="2">Bentuk</th>
        <th class="align-middle" rowspan="2">Kegiatan</th>
        <th class="align-middle" colspan="2">Verifikasi & Approval</th>
        <th class="align-middle" rowspan="2">Rencana</th>
        <th class="align-middle" rowspan="2">Prioritas</th>
        <th rowspan="2" class="align-middle">AKSI</th>
      </tr>
      <tr>
        <th class="align-middle">Unit Kerja</th>
        <th class="align-middle">Universitas</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($rencana as $rencanaPembelajaran)
        <!-- Baris tabel untuk setiap rencana pembelajaran -->
        <tr>
          <!-- No. -->
          <td class="text-center px-2">{{ $loop->iteration }}</td>

          <!-- Nama -->
          <td class="px-2">{{ $rencanaPembelajaran->dataPegawai->nama }}</td>

          <!-- Tahun Kode -->
          <td class="text-center px-2">{{ $rencanaPembelajaran->tahun }}
            @if ($rencanaPembelajaran->klasifikasi == 'pelatihan')
              <br><span class="fw-semibold">{{ $rencanaPembelajaran->dataPelatihan->kode }}</span>
            @endif
          </td>

          <!-- Bentuk -->
          <td class="px-2">
            @if ($rencanaPembelajaran->klasifikasi == 'pelatihan')
              <span
                class="badge {{ $rencanaPembelajaran->bentukJalur->kategori->kategori == 'klasikal' ? 'text-bg-secondary' : 'text-bg-warning' }}"
                style="font-size: 0.7rem">
                {{ ucwords($rencanaPembelajaran->bentukJalur->kategori->kategori) ?? '-' }}
              </span>
              <br><span class="fw-semibold">Bentuk Jalur:
              </span>{{ $rencanaPembelajaran->bentukJalur->bentuk_jalur ?? '' }}
              <br><span class="fw-semibold">Rumpun:</span>
              {{ $rencanaPembelajaran->dataPelatihan->rumpun->rumpun ?? '' }}
            @elseif($rencanaPembelajaran->klasifikasi == 'pendidikan')
              <span class="badge text-bg-primary" style="font-size: 0.7rem">Pendidikan</span><br>
              <span class="fw-semibold">Jenjang:</span> {{ $rencanaPembelajaran->jenjang->jenjang ?? '' }}
              <br><span class="fw-semibold">Jenis: </span>
              {{ strtoupper($rencanaPembelajaran->jenisPendidikan->jenis_pendidikan) ?? '' }}
            @endif
          </td>

          <!-- Kegiatan -->
          <td class="px-2">
            @if ($rencanaPembelajaran->klasifikasi == 'pelatihan')
              <span class="fw-semibold">Nama Pelatihan:
              </span><br>{{ $rencanaPembelajaran->dataPelatihan->nama_pelatihan ?? '-' }}
            @else
              <span class="fw-semibold">Jurusan: </span><br>{{ $rencanaPembelajaran->dataPendidikan->jurusan ?? '-' }}
            @endif
          </td>

          <!-- Verifikasi Unit Kerja -->
          <td class="px-2">
            @if ($rencanaPembelajaran->unitKerjaCanverifying)
              @php
                $statusUK = $rencanaPembelajaran->unitKerjaCanverifying->status;
                $badgeClassUK = $statusUK == 'disetujui' ? 'text-bg-success' : 'text-bg-warning';
                $statusTextUK = $statusUK == 'disetujui' ? 'Disetujui' : 'Direvisi';
              @endphp
              <span class="fw-semibold">Tahap:</span> <br>
              <span class="badge {{ $badgeClassUK }} fs-1">{{ $statusTextUK }}</span><br>
              <div class="mt-1">
                <span class="fw-semibold">Verifikator:</span><br>
                <small
                  class="text-muted">{{ $rencanaPembelajaran->unitKerjaCanverifying->dataPegawai->nama ?? 'Belum ditentukan' }}</small>
              </div>
            @elseif($rencanaPembelajaran->kelompokCanValidating && $rencanaPembelajaran->kelompokCanValidating->status === 'disetujui')
              <span class="fw-semibold">Tahap:</span><br>
              <span class="badge text-bg-primary bg-opacity-75 fs-1">Ditinjau</span>
            @else
              <span style="font-size: 0.7rem">-</span>
            @endif
          </td>

          <!-- Approval Universitas -->
          <td class="px-2">
            @if ($rencanaPembelajaran->universitasCanApproving)
              @php
                $statusUniv = $rencanaPembelajaran->universitasCanApproving->status;
                $badgeClassUniv = $statusUniv == 'disetujui' ? 'text-bg-success' : 'text-bg-warning';
                $statusTextUniv = $statusUniv == 'disetujui' ? 'Disetujui' : 'Direvisi';
              @endphp
              <span class="fw-semibold">Tahap:</span> <br>
              <span class="badge {{ $badgeClassUniv }} fs-1">{{ $statusTextUniv }}</span>
            @elseif($rencanaPembelajaran->unitKerjaCanverifying && $rencanaPembelajaran->unitKerjaCanverifying->status === 'disetujui')
              <span class="fw-semibold">Tahap:</span><br>
              <span class="badge text-bg-primary bg-opacity-75 fs-1">Ditinjau</span>
            @else
              <span style="font-size: 0.7rem">-</span>
            @endif
          </td>

          <!-- Rencana -->
          <td class="px-2">
            <span class="fw-semibold">Region: </span>{{ ucwords($rencanaPembelajaran->region->region) ?? '-' }} <br>
            <span class="fw-semibold">JP: </span>{{ $rencanaPembelajaran->jam_pelajaran }} JP <br>
            <span class="fw-semibold">Anggaran:
            </span>Rp{{ number_format($rencanaPembelajaran->anggaran_rencana, 0, ',', '.') }} <br>
          </td>

          <!-- Prioritas -->
          <td class="px-1 text-center">
            @php
              $prioColor =
                  ['rendah' => 'success', 'sedang' => 'warning', 'tinggi' => 'danger'][
                      $rencanaPembelajaran->prioritas
                  ] ?? 'secondary';
            @endphp
            <span class="badge rounded-pill text-bg-{{ $prioColor }}"
              style="font-size: 0.7rem">{{ ucfirst($rencanaPembelajaran->prioritas) }}</span>
          </td>

          <!-- AKSI -->
          <td class="px-2 text-center">
            @php
              $isLockedByUnit = $rencanaPembelajaran->unitKerjaCanverifying !== null;
              $isLockedByUniv = $rencanaPembelajaran->universitasCanApproving !== null;
              $isLocked = $isLockedByUnit || $isLockedByUniv;
            @endphp

            @if ($isWithinDeadline && !$isLocked)
              <form action="{{ route('validasi_kelompok.destroy', $rencanaPembelajaran->kelompokCanValidating->id) }}"
                method="post" id="batalForm-{{ $rencanaPembelajaran->id }}">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm batalSetujuiAlert"
                  data-form-id="batalForm-{{ $rencanaPembelajaran->id }}">
                  <span class="ti ti-arrow-back fs-3"></span>
                </button>
              </form>
            @else
              <div class="d-flex flex-column align-items-center">
                <span class="ti ti-lock text-muted fs-4"></span>
                <small class="text-muted" style="font-size: 0.6rem; text-align: center;">
                  @if ($isLockedByUniv)
                    Diproses Universitas
                  @elseif ($isLockedByUnit)
                    Diproses Unit Kerja
                  @else
                    Waktu Habis
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
