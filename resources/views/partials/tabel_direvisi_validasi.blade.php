<div class="table-responsive">
  <table class="table table-hover table-bordered mb-3 datatables" style="font-size: 0.7rem">
    <thead>
      <tr>
        <th class="align-middle">No.</th>
        <th class="align-middle">Nama</th>
        <th class="align-middle">Tahun <br> Kode</th>
        <th class="align-middle">Bentuk</th>
        <th class="align-middle">Kegiatan</th>
        <th class="align-middle">Rencana</th>
        <th class="align-middle">Prioritas</th>
        <th class="align-middle">Catatan</th>
        <th class="align-middle">Status Revisi</th>
        <th class="align-middle">AKSI</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($rencana as $rencanaPembelajaran)
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

          {{-- CATATAN --}}
          <td class="px-2">
            <strong>Catatan :</strong>
            @if (
                $rencanaPembelajaran->kelompokCanValidating &&
                    $rencanaPembelajaran->kelompokCanValidating->catatanValidasiKelompok->isNotEmpty())
              <ul class="m-0 p-0">
                @foreach ($rencanaPembelajaran->kelompokCanValidating->catatanValidasiKelompok as $index => $catatan)
                  <li><span>{{ $index + 1 }}.</span> {{ $catatan->catatan }}</li>
                @endforeach
              </ul>
            @else
              <span class="text-muted">Tidak ada catatan</span>
            @endif
          </td>

          {{-- STATUS REVISI --}}
          <td class="px-2">
            <span class="fw-semibold">Status Revisi:</span>
            <span
              class="badge {{ $rencanaPembelajaran->kelompokCanValidating->status_revisi == 'belum_direvisi'
                  ? 'text-bg-danger'
                  : ($rencanaPembelajaran->kelompokCanValidating->status_revisi == 'sedang_direvisi'
                      ? 'text-bg-warning'
                      : 'text-bg-success') }} fs-1 p-1">
              {{ ucwords(str_replace('_', ' ', $rencanaPembelajaran->kelompokCanValidating->status_revisi ?? '-')) }}
            </span>
          </td>

          {{-- AKSI --}}
          <td class="px-2">
            @if ($rencanaPembelajaran->kelompokCanValidating->status_revisi === 'sudah_direvisi')
              <div class="text-center">
                <div class="btn-group" role="group">
                  {{-- Tombol Revisi Ulang --}}
                  <a href="#" class="btn btn-warning btn-sm" title="Beri Revisi Tambahan" data-bs-toggle="modal"
                    data-bs-target="#tambahRevisiModal-{{ $rencanaPembelajaran->id }}">
                    <span class="ti ti-file-settings fs-3"></span>
                  </a>

                  {{-- Tombol Setujui --}}
                  <a href="#" class="btn btn-success btn-sm" title="Setujui" data-bs-toggle="modal"
                    data-bs-target="#setujuiModal-{{ $rencanaPembelajaran->id }}">
                    <span class="ti ti-circle-check fs-3"></span>
                  </a>
                </div>
              </div>
            @else
              <div class="fw-bold">*Revisi rencana pembelajaran belum dikirim oleh pegawai.</div>
            @endif
          </td>
        </tr>
        <!-- Modal Tambah Revisi -->
        <div class="modal fade" id="tambahRevisiModal-{{ $rencanaPembelajaran->id }}" tabindex="-1"
          aria-labelledby="tambahRevisiModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h1 class="modal-title fs-5 fw-semibold">Tambah Revisi Rencana</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body border border-2 mx-3 rounded-2">
                <form action="{{ route('validasi.tambah-revisi', $rencanaPembelajaran->id) }}" method="POST"
                  id="tambahRevisiForm-{{ $rencanaPembelajaran->id }}">
                  @csrf
                  <div class="mb-3">
                    <label for="tolak_catatan" class="form-label fw-semibold">Catatan:<span
                        class="text-danger">*</span></label>
                    <textarea class="form-control" name="catatan" rows="3" required></textarea>
                  </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-warning tambahRevisiAlert"
                  data-form-id="tambahRevisiForm-{{ $rencanaPembelajaran->id }}">Revisi</button>
              </div>
              </form>
            </div>
          </div>
        </div>

        {{-- Modal Setujui --}}
        <div class="modal fade" id="setujuiModal-{{ $rencanaPembelajaran->id }}" tabindex="-1"
          aria-labelledby="setujuiModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h1 class="modal-title fs-5 fw-semibold">Setujui Rencana</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body border border-2 mx-3 rounded-2">
                <form action="{{ route('validasi.setujui-dari-revisi', $rencanaPembelajaran->id) }}" method="POST"
                  id="setujuiDirevisiForm-{{ $rencanaPembelajaran->id }}">
                  @csrf
                  <div class="form-group">
                    <div class="mb-2">
                      <label for="setujui_catatan" class="form-label fw-semibold">Catatan: (opsional)</label>
                      <textarea class="form-control" name="catatan" rows="3"></textarea>
                    </div>
                  </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success setujuiDirevisiAlert"
                  data-form-id="setujuiDirevisiForm-{{ $rencanaPembelajaran->id }}">Setujui</button>
              </div>
              </form>
            </div>
          </div>
        </div>
      @endforeach
    </tbody>
  </table>
</div>
