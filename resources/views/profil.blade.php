@extends('layouts.main_layout', ['title' => 'Profil'])
@section('content')
  {{-- MODAL NOTIFIKASI TENGGAT WAKTU --}}
  @if (session('deadline_notification') && !session('default_password'))
    @include('components.modal.notifikasi_deadline_modal')
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        var deadlineModal = new bootstrap.Modal(document.getElementById('notifDeadlineModal'));
        deadlineModal.show();
      });
    </script>
  @endif

  {{-- ROW 1 --}}
  <div class="row d-flex align-items-stretch">
    {{-- KOLOM 1 (PROFIL PENGGUNA) --}}
    <div class="@if (auth()->user()->roles()->where('role', 'admin')->exists()) col-lg-6 d-flex @else col-lg-7 d-flex @endif">
      <div class="card w-100">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <!-- Foto Profil -->
            <div class="me-4 flex-shrink-0">
              @if ($dataPegawai->foto)
                <a href="{{ Storage::url($dataPegawai->foto) }}" target="blank" class="d-block position-relative">
                  <img src="{{ Storage::url($dataPegawai->foto) }}"
                    class="rounded-circle border border-3 border-primary border-opacity-25 shadow-sm"
                    style="object-fit: cover; height: 120px; width: 120px;">
                </a>
              @else
                <div
                  class="rounded-circle bg-primary bg-opacity-10 d-flex flex-column justify-content-center align-items-center border border-2 border-dashed border-primary border-opacity-25 shadow-sm"
                  style="height: 120px; width: 120px;">
                  <i class="ti ti-user text-primary mb-1" style="font-size: 24px;"></i>
                  <a href="#" class="btn btn-primary btn-sm rounded-pill px-3 py-1" data-bs-toggle="modal"
                    data-bs-target="#tambahFotoModal">Upload</a>
                </div>
                {{-- MODAL TAMBAH FOTO --}}
                <div class="modal fade" data-bs-backdrop="static" tabindex="-1" aria-hidden="true" id="tambahFotoModal">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h1 class="modal-title fs-5 fw-bold">
                          Unggah Foto Profil
                        </h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <form action="profil/tambah_foto" method="POST" enctype="multipart/form-data" id="createFormID">
                        <div class="modal-body border border-2 mx-3 rounded-2">
                          @csrf
                          <div class="form-group">
                            <label for="unggahFoto" class="fw-semibold">Unggah File Foto (Maks : 5 MB)</label>
                            <input type="file" class="form-control @error('foto') is-invalid @enderror" name="foto"
                              id="unggahFoto">
                            <span class="text-danger">{{ $errors->first('foto') }}</span>
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                          <button type="submit" class="btn btn-warning" id="createAlert">Unggah</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              @endif
            </div>

            <!-- Nama dan Informasi -->
            <div class="flex-grow-1 me-3">
              <div class="card-title fw-bolder mb-1 fs-4">{{ ucwords($dataPegawai->nama) }}
                <span>
                  @if ($dataPegawai->jenis_kelamin === 'L')
                    <i class="text-primary ti ti-gender-male" style="font-size: 22px;"></i>
                  @else
                    <i class="ti ti-gender-female" style="font-size: 22px; color: #ff70e7"></i>
                  @endif
                </span>
              </div>
              <p class="text-muted mb-1">NPPU: {{ $dataPegawai->nppu }}</p>
              @if ($dataPegawai->foto)
                <div class="mt-2">
                  <a href="#" class="btn btn-warning btn-sm rounded-2 px-3" data-bs-toggle="modal"
                    data-bs-target="#editFotoModal">
                    <span class="ti ti-pencil me-1"></span>Edit Foto
                  </a>
                </div>
                {{-- MODAL EDIT FOTO --}}
                <div class="modal fade" data-bs-backdrop="static" tabindex="-1" aria-hidden="true" id="editFotoModal">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h1 class="modal-title fs-5 fw-semibold">
                          Update Foto Profil
                        </h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <form action="profil/tambah_foto" method="POST" enctype="multipart/form-data" id="editFormID">
                        <div class="modal-body border border-2 mx-3 rounded-2">
                          @csrf
                          <div class="row">
                            <div class="col-md-5 pe-0">
                              <label class="fw-semibold ms-2 mb-1">Foto Lama:</label>
                              <a href="{{ Storage::url($dataPegawai->foto) }}" target="blank">
                                <img src="{{ Storage::url($dataPegawai->foto) }}"
                                  class="d-flex justify-content-center rounded-3 border border-2 border-dark border-opacity-25"
                                  style="object-fit: cover; height: 200px; width: 150px;" id="fotoLama">
                              </a>
                            </div>
                            <div class="col-md-7 ps-md-0 mt-1 mt-md-0 d-flex flex-column justify-content-center">
                              <div class="form-group">
                                <label for="unggahFoto" class="fw-semibold mb-1">Unggah Foto Baru (Maks : 5 MB)</label>
                                <input type="file" class="form-control @error('foto') is-invalid @enderror"
                                  name="foto" id="unggahFoto">
                                <span class="text-danger">{{ $errors->first('foto') }}</span>
                                <div class="mt-2 text-end">
                                  <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                                  <button type="submit" class="btn btn-warning" id="editAlert">Unggah</button>
                                </div>

                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="modal-footer">
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              @endif
            </div>

            <!-- Unit Kerja -->
            <div class="ms-auto text-end">
              <div class="d-flex flex-column align-items-end">
                <span class="text-muted small mb-1">Unit Kerja</span>
                <span
                  class="badge bg-primary bg-opacity-10 text-primary py-2 px-3 d-inline-flex align-items-center border border-primary border-opacity-25">
                  <i class="ti ti-building-community me-2"></i>
                  <span class="fw-medium text-truncate"
                    style="max-width: 150px;">{{ $dataPegawai->unitKerja->unit_kerja ?? 'Belum ditentukan' }}</span>
                </span>
              </div>
            </div>
          </div>

          <hr class="my-3">

          <!-- Tampilan Berbeda untuk Admin dan Non-Admin -->
          @can('admin')
            <!-- INFORMASI SISTEM UNTUK ADMIN -->
            <h6 class="fw-bold mb-3">Informasi Sistem</h6>
            <div class="row">
              <div class="col-md-6 mb-3">
                <div class="bg-light rounded p-3 d-flex align-items-center">
                  <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                    <i class="ti ti-users text-primary fs-5"></i>
                  </div>
                  <div>
                    <p class="mb-0 small text-muted">Total Pengguna</p>
                    <p class="mb-0 fw-bold fs-5">{{ App\Models\User::count() }}</p>
                  </div>
                </div>
              </div>
              <div class="col-md-6 mb-3">
                <div class="bg-light rounded p-3 d-flex align-items-center">
                  <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
                    <i class="ti ti-user text-success fs-5"></i>
                  </div>
                  <div>
                    <p class="mb-0 small text-muted">Total Pegawai</p>
                    <p class="mb-0 fw-bold fs-5">{{ App\Models\DataPegawai::count() }}</p>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="bg-light rounded p-3 d-flex align-items-center">
                  <div class="bg-info bg-opacity-10 rounded-circle p-2 me-3">
                    <i class="ti ti-building-community text-info fs-5"></i>
                  </div>
                  <div>
                    <p class="mb-0 small text-muted">Unit Kerja</p>
                    <p class="mb-0 fw-bold fs-5">{{ App\Models\UnitKerja::count() }}</p>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="bg-light rounded p-3 d-flex align-items-center">
                  <div class="bg-warning bg-opacity-10 rounded-circle p-2 me-3">
                    <i class="ti ti-sitemap text-warning fs-5"></i>
                  </div>
                  <div>
                    <p class="mb-0 small text-muted">Kelompok</p>
                    <p class="mb-0 fw-bold fs-5">{{ App\Models\Kelompok::count() }}</p>
                  </div>
                </div>
              </div>
            </div>
          @else
            <!-- INFORMASI PRIBADI UNTUK NON-ADMIN -->
            <div class="row mb-2">
              <div class="col-md-6">
                <div class="d-flex align-items-center">
                  <div class="bg-light rounded-circle p-2 me-2">
                    <i class="ti ti-mail text-primary"></i>
                  </div>
                  <div>
                    <p class="mb-0 small text-muted">Email</p>
                    <p class="mb-0 fw-medium">{{ $dataPegawai->user->email ?? 'Tidak tersedia' }}</p>
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="d-flex align-items-center">
                  <div class="bg-light rounded-circle p-2 me-2">
                    <i class="ti ti-school text-primary"></i>
                  </div>
                  <div>
                    <p class="mb-0 small text-muted">Pendidikan Terakhir</p>
                    <p class="mb-0 fw-medium">
                      {{ $dataPegawai->pendidikanTerakhir->jenjangTerakhir->jenjang_terakhir ?? 'Tidak tersedia' }}
                      <span class="mb-0 fw-medium">
                        {{ $dataPegawai->pendidikanTerakhir->jurusan ?? '' }}
                      </span>
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="d-flex align-items-center">
                  <div class="bg-light rounded-circle p-2 me-2">
                    <i class="ti ti-key text-primary"></i>
                  </div>
                  <div>
                    <p class="mb-0 small text-muted">Role</p>
                    <span class="fw-medium">
                      @foreach ($roles as $index => $role)
                        {{ ucwords(str_replace('_', ' ', $role)) }}{{ $index < count($roles) - 1 ? ', ' : '' }}
                      @endforeach
                    </span>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="d-flex align-items-center">
                  <div class="bg-light rounded-circle p-2 me-2">
                    <i class="ti ti-user-check text-primary"></i>
                  </div>
                  <div>
                    <p class="mb-0 small text-muted">Status</p>
                    <p class="mb-0 fw-medium">{{ ucfirst($dataPegawai->status) }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Informasi Kelompok untuk Non-Admin -->
            <hr class="mb-3">
            <h6 class="fw-bold mb-2">Ketua Kelompok</h6>
            <div class="bg-light rounded p-2">
              <div class="d-flex align-items-center">
                <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                  <i class="ti ti-affiliate text-primary"></i>
                </div>
                <div>
                  <p class="mb-0 fw-medium">
                    {{ $dataPegawai->kelompok ? $dataPegawai->kelompok->ketua->nama : 'Tidak memiliki kelompok' }}
                  </p>
                </div>
              </div>
            </div>
          @endcan
        </div>
      </div>
    </div>

    {{-- KOLOM MANAJEMEN JADWAL UNTUK ADMIN --}}
    @can('admin')
      <div class="col-lg-6 d-flex">
        @include('partials.manajemen_jadwal_admin')
      </div>
    @else
      {{-- KOLOM JADWAL UNTUK AKTOR LAIN --}}
      @php
        $rolePartials = [
            'pegawai' => 'partials.jadwal_rencana_pegawai',
            'ketua_kelompok' => 'partials.jadwal_validasi_kelompok',
            'verifikator' => 'partials.jadwal_verifikasi_unit_kerja',
            'approver' => 'partials.jadwal_approval_universitas',
        ];

        // Filter roles yang ada di $rolePartials dan dimiliki oleh user
        $filteredRoles = array_filter($roles, function ($role) use ($rolePartials) {
            return isset($rolePartials[$role]);
        });

        $hasMultipleRoles = count($filteredRoles) > 1;
      @endphp

      <div class="col-lg-5 d-flex">
        <div class="card w-100">
          <div class="card-body">
            @if ($hasMultipleRoles)
              <ul class="nav nav-tabs mb-3" id="roleTabs" role="tablist">
                @foreach ($roles as $index => $role)
                  @if (isset($rolePartials[$role]))
                    <li class="nav-item" role="presentation">
                      <button class="nav-link @if ($index === 0) active @endif"
                        id="tab-{{ $role }}" data-bs-toggle="tab" data-bs-target="#content-{{ $role }}"
                        type="button" role="tab">
                        {{ ucwords(str_replace('_', ' ', $role)) }}
                      </button>
                    </li>
                  @endif
                @endforeach
              </ul>
            @endif

            {{-- Konten Tiap Tab --}}
            <div class="tab-content" id="roleTabContent">
              @foreach ($roles as $index => $role)
                @if (isset($rolePartials[$role]))
                  <div class="tab-pane h-100 fade @if ($index === 0) show active @endif"
                    id="content-{{ $role }}" role="tabpanel" aria-labelledby="tab-{{ $role }}">
                    @include($rolePartials[$role])
                  </div>
                @endif
              @endforeach
            </div>
          </div>
        </div>
        {{-- Tab Navigasi --}}
      </div>
    @endcan
  </div>

  {{-- ROW 2: STATISTIK RENCANA PEMBELAJARAN --}}
  @if ($dataPegawai && $dataPegawai->rencanaPembelajaran->count() > 0)
    @include('partials.statistik.rencana_pembelajaran')
  @endif

  {{-- ROW 3: STATISTIK PROGRES VERIFIKASI --}}
  @if ($dataPegawai && $dataPegawai->rencanaPembelajaran->count() > 0)
    @include('partials.statistik.progres_verifikasi')
  @endif

  {{-- ROW 4: STATISTIK KHUSUS KETUA KELOMPOK --}}
  @can('ketua_kelompok')
    @include('partials.statistik.kelompok')
  @endcan

  {{-- ROW 5: STATISTIK KHUSUS KETUA KELOMPOK --}}
  @can('verifikator')
    @include('partials.statistik.unit_kerja')
  @endcan

  {{-- ROW 5: STATISTIK KHUSUS APPROVER --}}
  @can('approver')
    @include('partials.statistik.universitas')
  @endcan

@endsection

{{-- TOOLTIPS --}}
@push('scripts')
  <script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    })
  </script>
@endpush

{{-- PAKSA BUKA MODAL JIKA ADA ERROR --}}
<script>
  @if ($errors->any())
    document.addEventListener('DOMContentLoaded', function() {
      var editFotoModal = new bootstrap.Modal(document.getElementById('editFotoModal'));
      editFotoModal.show();
    });
  @endif
</script>

<script>
  @if ($errors->any())
    document.addEventListener('DOMContentLoaded', function() {
      var tambahFotoModal = new bootstrap.Modal(document.getElementById('tambahFotoModal'));
      tambahFotoModal.show();
    });
  @endif
</script>

<script>
  document.addEventListener("DOMContentLoaded", function() {
    const modalElement = document.getElementById('notifPasswordModal');
    if (modalElement) {
      const modal = new bootstrap.Modal(modalElement);
      modal.show();
    }
  });
</script>
