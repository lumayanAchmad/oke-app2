<div class="table-responsive">
  <table class="table table-hover table-bordered mb-3 px-0 datatables" style="font-size: 0.7rem">
    <thead>
      <tr>
        <th class="align-middle">No.</th>
        <th class="align-middle">Nama</th>
        <th class="align-middle">Tahun <br> Kode</th>
        <th class="align-middle">Bentuk</th>
        <th class="align-middle">Kegiatan</th>
        <th class="align-middle">Rencana</th>
        <th class="align-middle">Prioritas</th>
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

          {{-- AKSI --}}
          <td class="px-2">
            @if ($isNotStartedYet)
              <div class="alert alert-warning p-2 mb-2">
                <span class="ti ti-clock"></span>
                <strong>Waktu Verifikasi Belum Dimulai!</strong><br>
                Mulai {{ $startDate ? $startDate->format('d M Y') : '---' }}
              </div>
            @elseif ($isWithinDeadline)
              <div class="btn-group" role="group">
                {{-- Tombol Revisi --}}
                <a href="#" class="btn btn-warning btn-sm" title="Beri Revisi" data-bs-toggle="modal"
                  data-bs-target="#tolakModal-{{ $rencanaPembelajaran->id }}">
                  <span class="ti ti-file-pencil fs-3"></span>
                </a>

                <!-- Modal Revisi -->
                <div class="modal fade" id="tolakModal-{{ $rencanaPembelajaran->id }}" tabindex="-1"
                  aria-labelledby="tolakModalLabel" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h1 class="modal-title fs-5 fw-semibold">Revisi Rencana</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body border border-2 mx-3 rounded-2">
                        <form id="revisiForm-{{ $rencanaPembelajaran->id }}"
                          action="{{ route('validasi.revisi', $rencanaPembelajaran->id) }}" method="POST">
                          @csrf
                          <div class="mb-3">
                            <label for="tolak_catatan" class="form-label fw-semibold fs-3">Catatan:<span
                                class="text-danger">*</span></label>
                            <textarea class="form-control" name="catatan" rows="3" required></textarea>
                          </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-light fs-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning fs-3 revisiAlert"
                          data-form-id="revisiForm-{{ $rencanaPembelajaran->id }}">Revisi</button>
                      </div>
                      </form>
                    </div>
                  </div>
                </div>

                {{-- Tombol Setujui --}}
                <a href="#" class="btn btn-success btn-sm rounded-end-1" style="border-radius: 0" title="Setujui"
                  data-bs-toggle="modal" data-bs-target="#setujuiModal-{{ $rencanaPembelajaran->id }}">
                  <span class="ti ti-circle-check fs-3"></span>
                </a>

                <!-- Modal Setujui -->
                <div class="modal fade" id="setujuiModal-{{ $rencanaPembelajaran->id }}" tabindex="-1"
                  aria-labelledby="setujuiModalLabel" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h1 class="modal-title fs-5 fw-semibold">Setujui Rencana</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body border border-2 mx-3 rounded-2">
                        <form id="setujuiForm-{{ $rencanaPembelajaran->id }}"
                          action="{{ route('validasi.setujui', $rencanaPembelajaran->id) }}" method="POST">
                          @csrf
                          <div class="form-group">
                            <div class="mb-2">
                              <label for="setujui_catatan" class="form-label fw-semibold fs-3">Catatan:
                                (opsional)
                              </label>
                              <textarea class="form-control" name="catatan" rows="3"></textarea>
                            </div>
                          </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-light fs-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success fs-3 setujuiAlert"
                          data-form-id="setujuiForm-{{ $rencanaPembelajaran->id }}">Setujui</button>
                      </div>
                      </form>
                    </div>
                  </div>
                </div>

              </div>
            @else
              <div class="alert alert-danger p-2 mb-2">
                <span class="ti ti-clock-stop"></span>
                @if ($endDate)
                  <strong>Waktu Verifikasi Sudah Berakhir!</strong><br>
                  Berakhir {{ $endDate->format('d M Y') }}
                @else
                  <strong>Tenggat Waktu Belum Diatur!</strong><br>
                  Hubungi Admin untuk informasi lebih lanjut
                @endif
              </div>
            @endif
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
