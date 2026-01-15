<div class="table-responsive">
  <table class="table table-hover table-bordered mb-3 px-0 datatables" style="font-size: 0.7rem">
    <thead>
      <tr>
        <th class="align-middle">
          <input type="checkbox" class="select-all">
        </th>
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
      @foreach ($kelompokData['rencanaBelumDiverifikasi'] as $rencanaPembelajaran)
        <tr>
          <td class="text-center px-2">
            <input type="checkbox" name="rencana_ids[]" value="{{ $rencanaPembelajaran->id }}" class="rencana-checkbox"
              data-unitkerja="{{ $rencanaPembelajaran->dataPegawai->unit_kerja_id }}"
              data-status="{{ $rencanaPembelajaran->kelompokCanValidating->status ?? '' }}">
          </td>

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
                  data-bs-target="#revisiModal-{{ $rencanaPembelajaran->id }}">
                  <span class="ti ti-file-pencil fs-3"></span>
                </a>

                <!-- Modal Revisi -->
                <div class="modal fade" id="revisiModal-{{ $rencanaPembelajaran->id }}" tabindex="-1"
                  aria-labelledby="revisiModalLabel" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h1 class="modal-title fs-5 fw-semibold">Revisi Rencana</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body border border-2 mx-3 rounded-2">
                        <form id="revisiForm-{{ $rencanaPembelajaran->id }}"
                          action="{{ route('verifikasi.revisi', $rencanaPembelajaran->id) }}" method="POST">
                          @csrf
                          <div class="mb-3">
                            <label for="tolak_catatan" class="form-label fw-semibold fs-2">Catatan:<span
                                class="text-danger">*</span></label>
                            <textarea class="form-control" name="catatan" rows="3"></textarea>
                          </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-light fs-3" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-warning fs-3 revisiAlert"
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
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                          aria-label="Close"></button>
                      </div>
                      <div class="modal-body border border-2 mx-3 rounded-2">
                        <form id="setujuiForm-{{ $rencanaPembelajaran->id }}"
                          action="{{ route('verifikasi.setujui', $rencanaPembelajaran->id) }}" method="POST">
                          @csrf
                          <div class="form-group">
                            <div class="mb-2">
                              <label for="setujui_catatan" class="form-label fw-semibold fs-2">Catatan:
                                (opsional)</label>
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

              {{-- Tampilkan Catatan jika Ada --}}
              @php
                $catatanList = $rencanaPembelajaran->kelompokCanValidating->catatanValidasiKelompok ?? collect();
              @endphp

              <div class="mt-2 text-start">
                <strong>Catatan:</strong>
                @if ($catatanList->isNotEmpty())
                  <ul class="mb-0 small" type="dis">
                    @foreach ($catatanList as $catatan)
                      <li>{{ $catatan->catatan }}</li>
                    @endforeach
                  </ul>
                @else
                  <span>-</span>
                @endif
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

  <div class="mx-3 mt-2">
    @if ($isWithinDeadline)
      <button type="button" class="btn btn-success btn-sm setujuiMassal" disabled data-bs-toggle="modal"
        data-bs-target="#setujuiMassalModal-{{ $accordionKey }}">
        <i class="ti ti-check"></i> Setujui Yang Dipilih
      </button>
      <div class="form-text">*Hanya untuk persetujuan. Revisi harus per item.</div>
    @else
      <button type="button" class="btn btn-success btn-sm" disabled>
        <i class="ti ti-check"></i> Setujui Yang Dipilih
      </button>
      <div class="form-text text-danger">*Tidak dalam jenjang waktu approval</div>
    @endif
  </div>

  {{-- Modal massal (ID unik pakai accordionKey) --}}
  <div class="modal fade" id="setujuiMassalModal-{{ $accordionKey }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form class="massalForm" action="{{ route('verifikasi.setujui-massal') }}" method="POST">
          @csrf
          <div class="modal-header">
            <h1 class="modal-title fs-5 fw-semibold">Setujui Massal</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body border border-2 mx-3 rounded-2">
            <div class="form-group mb-3">
              <label class="form-label">Catatan Umum (opsional):</label>
              <textarea class="form-control" name="catatan" rows="2"
                placeholder="Catatan ini akan diterapkan ke semua rencana terpilih"></textarea>
            </div>
            <div class="alert alert-info">
              <i class="ti ti-info-circle"></i>
              Anda akan menyetujui <span class="jumlahRencana">0</span> rencana sekaligus.
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
            <button type="button" class="btn btn-success triggerSetujuiMassal">Setujui Semua</button>
          </div>
        </form>
      </div>
    </div>
  </div>

</div>

{{-- SCRIPT UNTUK SETUJUI MASSAL --}}

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Loop setiap accordion
    document.querySelectorAll('.accordion-item').forEach(item => {
      const selectAll = item.querySelector('.select-all');
      const checkboxes = item.querySelectorAll('.rencana-checkbox');
      const approveMassalBtn = item.querySelector('.setujuiMassal');
      const jumlahRencanaSpan = item.querySelector('.jumlahRencana');
      const massalForm = item.querySelector('.massalForm');
      const triggerSetujuiMassal = item.querySelector('.triggerSetujuiMassal');

      // kalau accordion kosong / nggak ada tabel, skip
      if (!selectAll || checkboxes.length === 0) return;

      // Select All
      selectAll.addEventListener('change', function() {
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
        toggleApproveButton();
      });

      // Checkbox per item
      checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
          if (!this.checked) {
            selectAll.checked = false;
          } else if (Array.from(checkboxes).every(c => c.checked)) {
            selectAll.checked = true;
          }
          toggleApproveButton();
        });
      });

      function toggleApproveButton() {
        const selectedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
        if (approveMassalBtn) {
          approveMassalBtn.disabled = selectedCount === 0;
        }
        if (jumlahRencanaSpan) {
          jumlahRencanaSpan.textContent = selectedCount;
        }
      }

      // Submit massal form
      if (massalForm) {
        massalForm.addEventListener('submit', function(e) {
          const selectedIds = Array.from(checkboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);

          if (selectedIds.length === 0) {
            e.preventDefault();
            Swal.fire('Oops', 'Tidak ada rencana terpilih.', 'warning');
            return;
          }

          // bersihkan input hidden lama
          massalForm.querySelectorAll('input[name="rencana_ids[]"]').forEach(e => e.remove());

          // tambah input hidden baru
          selectedIds.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'rencana_ids[]';
            input.value = id;
            massalForm.appendChild(input);
          });
        });
      }

      // Trigger tombol konfirmasi massal
      if (triggerSetujuiMassal) {
        triggerSetujuiMassal.addEventListener('click', function() {
          const selectedIds = Array.from(checkboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);

          if (selectedIds.length === 0) {
            Swal.fire('Oops', 'Tidak ada rencana terpilih.', 'warning');
            return;
          }

          Swal.fire({
            title: "Konfirmasi Persetujuan Massal",
            html: `Anda akan menyetujui <strong>${selectedIds.length}</strong> rencana pembelajaran sekaligus.`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#13DEB9",
            confirmButtonText: "Ya, Setujui Semua",
            cancelButtonText: "Batal",
          }).then((result) => {
            if (result.isConfirmed) {
              // bersihkan input hidden lama
              massalForm.querySelectorAll('input[name="rencana_ids[]"]').forEach(e => e.remove());

              // tambah input hidden baru
              selectedIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'rencana_ids[]';
                input.value = id;
                massalForm.appendChild(input);
              });

              // tambahkan spinner pada tombol
              triggerSetujuiMassal.innerHTML =
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...';
              triggerSetujuiMassal.disabled = true;

              // submit manual
              massalForm.submit();
            }
          });
        });
      }
    });
  });
</script>
