<div class="table-responsive">
  <table class="table table-hover table-bordered mb-3 px-0 datatables" style="font-size: 0.7rem">
    <thead>
      <tr>
        <th class="align-middle">
          <input type="checkbox" id="selectAll-{{ $index }}">
        </th>
        <th class="align-middle">No.</th>
        <th class="align-middle">Nama</th>
        <th class="align-middle">Unit Kerja</th>
        <th class="align-middle">Tahun <br> Kode</th>
        <th class="align-middle">Bentuk</th>
        <th class="align-middle">Kegiatan</th>
        <th class="align-middle">Rencana</th>
        <th class="align-middle">Keterangan</th>
        <th class="align-middle">AKSI</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($unitData['rencanaBelumDiapprove'] as $rencanaPembelajaran)
        <tr>
          <td class="text-center px-2">
            <input type="checkbox" name="rencana_ids[]" class="rencana-checkbox" value="{{ $rencanaPembelajaran->id }}"
              data-unitkerja="{{ $rencanaPembelajaran->dataPegawai->unit_kerja_id }}"
              data-status="{{ $rencanaPembelajaran->universitasCanApproving->status ?? '' }}">
          </td>

          {{-- NOMOR --}}
          <td class="text-center px-2">{{ $loop->iteration }}</td>

          {{-- NAMA PEGAWAI --}}
          <td class="px-2">{{ $rencanaPembelajaran->dataPegawai->nama }}</td>

          {{-- UNIT KERJA --}}
          <td class="px-2">{{ $rencanaPembelajaran->dataPegawai->unitKerja->unit_kerja ?? '-' }}</td>

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
              <span class="fw-semibold">Bentuk Jalur:
              </span>{{ $rencanaPembelajaran->bentukJalur->bentuk_jalur ?? '' }}
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

          {{-- KETERANGAN --}}
          <td class="px-2">
            <div>
              <span class="fw-semibold">Prioritas:</span>
            </div>
            @if ($rencanaPembelajaran->prioritas == 'rendah')
              <span class="badge rounded-pill text-bg-success" style="font-size: 0.7rem">Rendah</span>
            @elseif ($rencanaPembelajaran->prioritas == 'sedang')
              <span class="badge rounded-pill text-bg-warning" style="font-size: 0.7rem">Sedang</span>
            @elseif ($rencanaPembelajaran->prioritas == 'tinggi')
              <span class="badge rounded-pill text-bg-danger" style="font-size: 0.7rem">Tinggi</span>
            @endif
            {{-- Ketua Kelompok --}}
            <div class="text-start mt-2">
              <span class="fw-semibold">Ketua Kelompok:</span>
              {{ optional($rencanaPembelajaran->dataPegawai->kelompok->ketua)->nama ?? '-' }}
            </div>
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
                {{-- Tombol Tolak --}}
                <a href="#" class="btn btn-danger btn-sm" title="Tolak" data-bs-toggle="modal"
                  data-bs-target="#tolakModal-{{ $rencanaPembelajaran->id }}">
                  <span class="ti ti-x fs-3"></span>
                </a>

                <!-- Modal Tolak -->
                <div class="modal fade" id="tolakModal-{{ $rencanaPembelajaran->id }}" tabindex="-1"
                  aria-labelledby="tolakModalLabel" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h1 class="modal-title fs-5 fw-semibold">Tolak Rencana</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body border border-2 mx-3 rounded-2">
                        <form id="tolakForm-{{ $rencanaPembelajaran->id }}"
                          action="{{ route('approval.reject', $rencanaPembelajaran->id) }}" method="POST">
                          @csrf
                          <div class="mb-3">
                            <label for="tolak_catatan" class="form-label fw-semibold fs-3">Alasan Penolakan:<span
                                class="text-danger">*</span></label>
                            <textarea class="form-control" name="catatan" rows="3" required></textarea>
                          </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-light fs-3" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-danger fs-3 tolakAlert"
                          data-form-id="tolakForm-{{ $rencanaPembelajaran->id }}">Tolak</button>
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
                          action="{{ route('approval.approve', $rencanaPembelajaran->id) }}" method="POST">
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

              {{-- Tampilkan Catatan jika Ada --}}
              @php
                $catatanUnitKerja = $rencanaPembelajaran->unitKerjaCanVerifying->catatan ?? '';
              @endphp

              <div class="mt-2 text-start">
                <strong>Catatan Unit Kerja:</strong>
                @if ($catatanUnitKerja)
                  <p class="mb-0 small">{{ $catatanUnitKerja }}</p>
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

  {{-- TOMBOL SETUJUI DAN TOLAK MASSAL --}}
  <div class="mx-3 mt-2">
    @if ($isWithinDeadline)
      <button type="button" class="btn btn-success btn-sm me-2" id="setujuiMassal-{{ $index }}" disabled
        data-bs-toggle="modal" data-bs-target="#setujuiMassalModal-{{ $index }}">
        <i class="ti ti-check"></i> Setujui Yang Dipilih
      </button>

      <button type="button" class="btn btn-danger btn-sm" id="tolakMassal-{{ $index }}" disabled
        data-bs-toggle="modal" data-bs-target="#tolakMassalModal-{{ $index }}">
        <i class="ti ti-x"></i> Tolak Yang Dipilih
      </button>
    @else
      <button type="button" class="btn btn-success btn-sm me-2" disabled>
        <i class="ti ti-check"></i> Setujui Yang Dipilih
      </button>

      <button type="button" class="btn btn-danger btn-sm" disabled>
        <i class="ti ti-x"></i> Tolak Yang Dipilih
      </button>
      <div class="form-text text-danger">*Tidak dalam jenjang waktu approval</div>
    @endif
  </div>

  <!-- Modal Setujui Massal -->
  <div class="modal fade" id="setujuiMassalModal-{{ $index }}" tabindex="-1"
    aria-labelledby="setujuiMassalModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form class="massalForm" id="massalForm-{{ $index }}"
          action="{{ route('approval.approve-massal') }}" method="POST">
          @csrf
          <div class="modal-header">
            <h1 class="modal-title fs-5 fw-semibold">Setujui Massal</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body border border-2 mx-3 rounded-2">
            <div class="form-group mb-3">
              <label class="form-label">Catatan Umum (opsional):</label>
              <textarea class="form-control" name="catatan" rows="2"
                placeholder="Catatan ini akan diterapkan ke semua rencana terpilih"></textarea>
            </div>

            <div class="alert alert-info">
              <i class="ti ti-info-circle"></i>
              Anda akan menyetujui <span id="jumlahRencana-{{ $index }}">0</span> rencana sekaligus.
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
            <button type="button" class="btn btn-success" id="confirmMassal-{{ $index }}">Setujui
              Semua</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Tolak Massal -->
  <div class="modal fade" id="tolakMassalModal-{{ $index }}" tabindex="-1"
    aria-labelledby="tolakMassalModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form class="tolakMassalForm" id="tolakMassalForm-{{ $index }}"
          action="{{ route('approval.reject-massal') }}" method="POST">
          @csrf
          <div class="modal-header">
            <h1 class="modal-title fs-5 fw-semibold">Tolak Massal</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body border border-2 mx-3 rounded-2">
            <div class="form-group mb-3">
              <label class="form-label">Alasan Penolakan (wajib):</label>
              <textarea class="form-control" name="catatan" rows="3" required
                placeholder="Alasan penolakan yang akan diterapkan ke semua rencana terpilih"></textarea>
            </div>

            <div class="alert alert-warning">
              <i class="ti ti-alert-triangle"></i>
              Anda akan menolak <span id="jumlahRencanaTolak-{{ $index }}">0</span> rencana sekaligus.
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
            <button type="button" class="btn btn-danger" id="confirmTolakMassal-{{ $index }}">Tolak
              Semua</button>
          </div>
        </form>
      </div>
    </div>
  </div>

</div>

{{-- SCRIPT UNTUK SETUJUI DAN TOLAK MASSAL --}}
<script>
  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.accordion-item').forEach(item => {
      const key = item.dataset.key; // Ini akan mengambil nilai dari data-key="{{ $index }}"

      const selectAll = item.querySelector(`#selectAll-${key}`);
      const approveBtn = item.querySelector(`#setujuiMassal-${key}`);
      const rejectBtn = item.querySelector(`#tolakMassal-${key}`);
      const jumlahApprove = item.querySelector(`#jumlahRencana-${key}`);
      const jumlahReject = item.querySelector(`#jumlahRencanaTolak-${key}`);
      const approveForm = item.querySelector(`#massalForm-${key}`);
      const rejectForm = item.querySelector(`#tolakMassalForm-${key}`);
      const confirmApprove = item.querySelector(`#confirmMassal-${key}`);
      const confirmReject = item.querySelector(`#confirmTolakMassal-${key}`);

      function getCheckboxes() {
        return Array.from(item.querySelectorAll('.rencana-checkbox'));
      }

      function getSelectedIds() {
        return getCheckboxes().filter(cb => cb.checked).map(cb => cb.value);
      }

      function addHiddenInputs(form, ids) {
        form.querySelectorAll('input[name="rencana_ids[]"]').forEach(e => e.remove());
        ids.forEach(id => {
          const input = document.createElement('input');
          input.type = 'hidden';
          input.name = 'rencana_ids[]';
          input.value = id;
          form.appendChild(input);
        });
      }

      function toggleButtons() {
        const count = getSelectedIds().length;
        if (approveBtn) approveBtn.disabled = count === 0;
        if (rejectBtn) rejectBtn.disabled = count === 0;
        if (jumlahApprove) jumlahApprove.textContent = count;
        if (jumlahReject) jumlahReject.textContent = count;
      }

      // Event checkbox
      getCheckboxes().forEach(cb => {
        cb.addEventListener('change', () => {
          if (!cb.checked && selectAll) {
            selectAll.checked = false;
          } else if (selectAll) {
            // Perbaiki kondisi ini
            const allChecked = getCheckboxes().every(c => c.checked);
            selectAll.checked = allChecked;
            selectAll.indeterminate = !allChecked && getSelectedIds().length > 0;
          }
          toggleButtons();
        });
      });

      // Event selectAll - PERBAIKAN UTAMA
      if (selectAll) {
        selectAll.addEventListener('change', () => {
          const isChecked = selectAll.checked;
          getCheckboxes().forEach(cb => {
            cb.checked = isChecked;
            // Trigger event change pada checkbox jika diperlukan
            const event = new Event('change');
            cb.dispatchEvent(event);
          });
          toggleButtons();
        });
      }

      // Approve
      if (confirmApprove) {
        confirmApprove.addEventListener('click', () => {
          const ids = getSelectedIds();
          if (ids.length === 0) {
            Swal.fire('Oops', 'Tidak ada rencana terpilih.', 'warning');
            return;
          }
          Swal.fire({
            title: "Konfirmasi Persetujuan Massal",
            html: `Anda akan <strong>menyetujui</strong> <strong>${ids.length}</strong> rencana.`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#13DEB9",
            confirmButtonText: "Ya, Setujui Semua",
            cancelButtonText: "Batal",
          }).then(result => {
            if (result.isConfirmed) {
              addHiddenInputs(approveForm, ids);
              confirmApprove.disabled = true; // Disable button
              confirmApprove.innerHTML = `
          <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
          Memproses...
        `; // Tambahkan spinner
              approveForm.submit();
            }
          });
        });
      }

      // Event listener untuk tombol konfirmasi reject
      if (confirmReject) {
        confirmReject.addEventListener('click', () => {
          const ids = getSelectedIds();
          const catatan = document.querySelector(`#tolakMassalForm-${key} textarea[name="catatan"]`).value
            .trim();
          // Jika tidak ada rencana yang dipilih, maka menampilkan pesan warning
          if (ids.length === 0) {
            Swal.fire('Oops', 'Tidak ada rencana terpilih.', 'warning');
            return;
          }
          // Jika alasan penolakan kosong, maka menampilkan pesan warning
          if (catatan === '') {
            Swal.fire('Catatan wajib diisi!',
              'Silakan tuliskan alasan atau catatan penolakan sebelum melanjutkan.', 'error');
            return;
          }
          // Menampilkan konfirmasi reject
          Swal.fire({
            title: "Konfirmasi Penolakan Massal",
            html: `Anda akan <strong>menolak</strong> <strong>${ids.length}</strong> rencana.`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#fa896b",
            confirmButtonText: "Ya, Tolak Semua",
            cancelButtonText: "Batal",
          }).then(result => {
            if (result.isConfirmed) {
              addHiddenInputs(rejectForm, ids); // Menambahkan input hidden pada form reject
              confirmReject.disabled = true; // Disable button
              confirmReject.innerHTML =
                `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...`; // Tambahkan spinner
              rejectForm.submit(); // Mengirimkan form reject
            }
          });
        });
      }

      // Inisialisasi awal
      toggleButtons();
    });
  });
</script>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.tolakAlert').forEach(button => {
      button.addEventListener('click', event => {
        let formId = button.getAttribute('data-form-id');
        let form = document.getElementById(formId);
        let textarea = form.querySelector('textarea[name="catatan"]');

        // Cek apakah textarea kosong
        if (textarea.value.trim() === '') {
          // Hentikan pengiriman form dan tampilkan SweetAlert warning
          event.preventDefault();
          Swal.fire({
            title: 'Catatan wajib diisi!',
            text: 'Silakan tuliskan alasan atau catatan penolakan sebelum melanjutkan.',
            icon: 'error',
            confirmButtonText: "OK",
          });
        } else {
          // Jika tidak kosong, lanjutkan dengan SweetAlert konfirmasi
          event.preventDefault();
          Swal.fire({
            title: "Konfirmasi Data!",
            text: "Pastikan data yang anda isikan sudah benar!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#FA896B",
            confirmButtonText: "Tolak",
            cancelButtonText: "Batal"
          }).then(result => {
            if (result.isConfirmed) {
              // Tambahkan spinner dan nonaktifkan tombol sebelum submit
              button.disabled = true;
              button.innerHTML =
                `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...`;
              // Submit form
              form.submit();
            }
          });
        }
      });
    });
  });
</script>
