@extends('layouts.main_layout', ['title' => 'Hak Akses'])
@section('content')
  <div class="card mb-4 pb-4 bg-white">
    <div class="card-body px-0 py-0 ">
      <div class="card-header p-3 fs-5 fw-bolder" style="background-color: #ececec;">Hak Akses Pegawai</div>
      <hr class="my-0">

      <div class="table-responsive">
        {{-- TABEL --}}
        <table class="table table-striped mb-3" style="font-size: 0.8rem" id="myTable">
          <thead>
            <th class="text-center">No.</th>
            <th>Nama</th>
            <th>Unit Kerja</th>
            <th>Email</th>
            <th>Hak Akses</th>
            <th>Aksi</th>
          </thead>
          <tbody>
            @foreach ($users as $user)
              <tr>
                <td class="py-2 text-center">{{ $loop->iteration }}</td>
                <td class="py-2">{{ $user->name }}</td>
                <td class="py-2">{{ $user->dataPegawai->unitKerja->unit_kerja }}</td>
                <td class="py-2">{{ $user->email }}</td>
                <td class="py-2">
                  {{-- Tampilkan daftar roles user dengan warna dan font yang berbeda --}}
                  @foreach ($user->roles as $role)
                    @php
                      // Tentukan warna berdasarkan nama role (diambil dari kolom 'role')
                      $roleName = str_replace('_', ' ', $role->role);
                      $badgeClass = 'bg-secondary';
                      switch (strtolower($roleName)) {
                          case 'admin':
                              $badgeClass = 'bg-danger';
                              break;
                          case 'pimpinan':
                              $badgeClass = 'bg-dark';
                              break;
                          case 'approver':
                              $badgeClass = 'bg-success';
                              break;
                          case 'verifikator':
                              $badgeClass = 'bg-secondary';
                              break;
                          case 'ketua_kelompok':
                              $badgeClass = 'bg-warning';
                              break;
                          case 'pegawai':
                              $badgeClass = 'bg-primary';
                              break;
                          default:
                              $badgeClass = 'bg-secondary'; // Abu-abu untuk role lain
                              break;
                      }
                    @endphp
                    <span class="badge {{ $badgeClass }} d-block mb-1" style="font-size: 0.7rem;">
                      {{ ucwords($roleName) }}
                    </span>
                  @endforeach
                </td>
                <td class="py-2">
                  <a href="#" class="btn btn-warning btn-sm col" data-bs-toggle="modal"
                    data-bs-target="#editAksesModal{{ $user->id }}">
                    <span class="ti ti-pencil"></span>
                  </a>

                  <div class="modal fade" id="editAksesModal{{ $user->id }}" aria-labelledby="editAksesModalLabel"
                    data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h1 class="modal-title fs-5 fw-bolder">
                            Edit Akses <span class="text-primary">{{ $user->name }}</span>
                          </h1>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('edit_akses.update', $user->id) }}" method="POST" id="editFormID">
                          @csrf
                          @method('PUT')
                          <div class="modal-body border border-2 mx-3 rounded-2">
                            <div class="form-group">
                              <label for="roles" class="fw-bolder mb-2 fs-4">Hak Akses</label>
                              {{-- Checkbox roles --}}
                              @foreach ($roles as $role)
                                {{-- HANYA TAMPILKAN ROLE SELAIN 'KETUA KELOMPOK' --}}
                                @if (strtolower($role->role) !== 'ketua_kelompok')
                                  <div class="form-check">
                                    <input type="checkbox" name="roles[]" id="role_{{ $role->id }}"
                                      value="{{ $role->id }}" class="form-check-input"
                                      {{ in_array($role->id, $user->roles->pluck('id')->toArray()) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="role_{{ $role->id }}">
                                      {{ ucwords(str_replace('_', ' ', $role->role)) }}
                                    </label>
                                  </div>
                                @endif
                              @endforeach
                            </div>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

    </div>
  </div>
@endsection

<script>
  document.addEventListener("DOMContentLoaded", function() {
    let saveButtons = document.querySelectorAll('.modal-footer .btn-primary');

    saveButtons.forEach(button => {
      button.addEventListener('click', function(event) {
        event.preventDefault();
        let form = button.closest('form');

        Swal.fire({
          title: "Konfirmasi Perubahan",
          text: "Pastikan hak akses yang Anda berikan sudah benar!",
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: "Simpan",
          cancelButtonText: "Batal"
        }).then((result) => {
          if (result.isConfirmed) {
            form.submit();
          }
        });
      });
    });
  });
</script>
