@extends('layouts.main_layout', ['title' => 'Tenggat Rencana'])
@section('content')
  <div class="card mb-3 bg-white">
    <div class="card-body p-0">
      <div class="card-header p-3 fs-5 fw-bolder" style="background-color: #ececec;">
        Tenggat Perencanaan & Verifikasi Rencana Pegawai
      </div>
      <div class="p-4">
        @foreach ($kategoriTenggats as $kategoriTenggat)
          <div class="mb-3">
            <div class=" fs-4 fw-semibold mb-3">
              @if ($kategoriTenggat->kategori_tenggat == 'perencanaan_pegawai')
                Tenggat Perencanaan <span class="text-primary">Pegawai</span>
              @elseif($kategoriTenggat->kategori_tenggat == 'revisi_pegawai')
                Tenggat Revisi Pegawai <span class="text-danger">Revisi Pegawai</span>
              @elseif($kategoriTenggat->kategori_tenggat == 'validasi_kelompok')
                Tenggat Rencana Validasi <span class="text-secondary">Kelompok</span>
              @elseif($kategoriTenggat->kategori_tenggat == 'verifikasi_unit_kerja')
                Tenggat Rencana Verifikasi <span class="text-warning">Unit Kerja</span>
              @elseif($kategoriTenggat->kategori_tenggat == 'approval_universitas')
                Tenggat Rencana Approval <span class="text-danger">Universitas</span>
              @endif
              <span class="ms-2">
                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                  data-bs-target="#editModal{{ $kategoriTenggat->id }}">
                  <i class="ti ti-edit me-1"></i> Edit
                </button>
              </span>
            </div>
            <div class="row">
              @if (count($kategoriTenggat->tenggatRencana) > 0)
                @foreach ($kategoriTenggat->tenggatRencana as $tenggatRencana)
                  <div class="col-md-6">
                    <div class="card bg-light mb-3">
                      <div class="card-body">
                        <div class="card-title fw-semibold">Mulai</div>
                        <p class="card-text">
                          @if ($tenggatRencana->tanggal_mulai && $tenggatRencana->jam_mulai)
                            {{ Carbon\Carbon::parse($tenggatRencana->tanggal_mulai)->translatedFormat('d F Y') }} -
                            {{ $tenggatRencana->jam_mulai }}
                          @else
                            --
                          @endif
                        </p>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="card bg-light mb-3">
                      <div class="card-body">
                        <div class="card-title fw-semibold">Selesai</div>
                        <p class="card-text">
                          @if ($tenggatRencana->tanggal_selesai && $tenggatRencana->jam_selesai)
                            {{ Carbon\Carbon::parse($tenggatRencana->tanggal_selesai)->translatedFormat('d F Y') }} -
                            {{ $tenggatRencana->jam_selesai }}
                          @else
                            --
                          @endif
                        </p>
                      </div>
                    </div>
                  </div>
                @endforeach
              @else
                <div class="col-md-6">
                  <div class="card bg-light mb-3">
                    <div class="card-body">
                      <div class="card-title fw-semibold">Mulai</div>
                      <p class="card-text">--</p>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="card bg-light mb-3">
                    <div class="card-body">
                      <div class="card-title fw-semibold">Selesai</div>
                      <p class="card-text">--</p>
                    </div>
                  </div>
                </div>
              @endif
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </div>

  {{-- MODAL --}}
  @foreach ($kategoriTenggats as $kategoriTenggat)
    @php
      $tenggatRencana = $kategoriTenggat->tenggatRencana->first();
      $action = $tenggatRencana ? route('tenggat_rencana.update', $tenggatRencana->id) : route('tenggat_rencana.store');
      $method = $tenggatRencana ? 'PUT' : 'POST';
    @endphp

    <div class="modal fade" id="editModal{{ $kategoriTenggat->id }}" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <div class="modal-title fw-semibold fs-5">
              {{ $tenggatRencana ? 'Edit' : 'Tambah' }} Tenggat
              {{ ucwords(str_replace('_', ' ', $kategoriTenggat->kategori_tenggat)) }}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form action="{{ $action }}" method="POST" class="py-2">
            @csrf
            @if ($tenggatRencana)
              @method('PUT')
            @endif
            <div class="modal-body border border-2 mx-3 rounded-2">
              <label class="fw-semibold">Waktu <span class="text-primary">Mulai</span></label>
              <div class="input-group mb-2">
                <input type="date" class="form-control" name="tanggal_mulai"
                  value="{{ old('tanggal_mulai', $tenggatRencana->tanggal_mulai ?? '') }}" required>
                <input type="time" class="form-control" name="jam_mulai"
                  value="{{ old('jam_mulai', $tenggatRencana->jam_mulai ?? '') }}" required>
              </div>
              <label class="fw-semibold">Waktu <span class="text-danger">Selesai</span></label>
              <div class="input-group mb-2">
                <input type="date" class="form-control" name="tanggal_selesai"
                  value="{{ old('tanggal_selesai', $tenggatRencana->tanggal_selesai ?? '') }}" required>
                <input type="time" class="form-control" name="jam_selesai"
                  value="{{ old('jam_selesai', $tenggatRencana->jam_selesai ?? '') }}" required>
              </div>
              <input type="hidden" name="kategori_tenggat_id" value="{{ $kategoriTenggat->id }}">
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
              <button type="submit" class="btn btn-primary createJadwalAlert">Simpan</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  @endforeach

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      @foreach ($kategoriTenggats as $kategoriTenggat)
        @if (
            $errors->has('tanggal_mulai') ||
                $errors->has('jam_mulai') ||
                $errors->has('tanggal_selesai') ||
                $errors->has('jam_selesai'))
          @if (old('kategori_tenggat_id') == $kategoriTenggat->id)
            var modal = new bootstrap.Modal(document.getElementById('editModal{{ $kategoriTenggat->id }}'));
            modal.show();

            // Pastikan modal telah terbuka sebelum menambahkan invalid feedback
            modal._element.addEventListener('shown.bs.modal', function() {
              var modalElement = document.getElementById('editModal{{ $kategoriTenggat->id }}');

              function addInvalidFeedback(input, message) {
                if (!input.classList.contains('is-invalid')) {
                  input.classList.add('is-invalid');

                  // Cek apakah feedback sudah ada, jika belum tambahkan
                  var existingFeedback = input.parentNode.querySelector('.invalid-feedback');
                  if (!existingFeedback) {
                    var feedback = document.createElement('div');
                    feedback.classList.add('invalid-feedback');
                    feedback.innerHTML = message;
                    input.parentNode.appendChild(feedback);
                  }
                }
              }

              var inputs = modalElement.querySelectorAll(
                'input[name="tanggal_mulai"], input[name="jam_mulai"], input[name="tanggal_selesai"], input[name="jam_selesai"]'
              );

              inputs.forEach(function(input) {
                if (input.name === 'tanggal_mulai' && @json($errors->has('tanggal_mulai'))) {
                  addInvalidFeedback(input, '{{ $errors->first('tanggal_mulai') }}');
                }
                if (input.name === 'jam_mulai' && @json($errors->has('jam_mulai'))) {
                  addInvalidFeedback(input, '{{ $errors->first('jam_mulai') }}');
                }
                if (input.name === 'tanggal_selesai' && @json($errors->has('tanggal_selesai'))) {
                  addInvalidFeedback(input, '{{ $errors->first('tanggal_selesai') }}');
                }
                if (input.name === 'jam_selesai' && @json($errors->has('jam_selesai'))) {
                  addInvalidFeedback(input, '{{ $errors->first('jam_selesai') }}');
                }
              });
            });
          @endif
        @endif
      @endforeach
    });
  </script>
@endsection
