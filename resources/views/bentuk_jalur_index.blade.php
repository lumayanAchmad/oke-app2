@extends('layouts.main_layout', ['title' => 'Bentuk Jalur'])
@section('content')
  <div class="card pb-4 bg-white">
    <div class="card-body px-0 py-0">
      <div class="card-header p-3 fs-5 fw-bolder" style="background-color: #ececec;">
        <span>
          <a href="/data_pelatihan" class="ti ti-arrow-left fw-bolder mx-2 text-dark text-opacity-50 link-custom"></a>
        </span>
        <a href="/data_pelatihan" class="text-dark text-opacity-50 link-custom">Data Pelatihan / </a>
        Bentuk Jalur
      </div>
      <div class=" row my-3">
        <div class="col d-flex">
          <a href="#" class="btn btn-outline-primary ms-3 " style="font-size: 0.9rem" data-bs-toggle="modal"
            data-bs-target="#createJalurModal">
            <span class="me-1">
              <i class="ti ti-clipboard-plus"></i>
            </span>
            <span>Tambah Bentuk Jalur</span>
          </a>
          {{-- MODAL TAMBAH BENTUK JALUR --}}
          <div class="modal fade" data-bs-backdrop="static" tabindex="-1" aria-hidden="true" id="createJalurModal">
            @include('components.modal.bentuk_jalur_create_modal')
          </div>

        </div>
      </div>
      <hr class="my-0">
      <div class="table-responsive">
        <table class="table table-striped mb-3" style="font-size: 0.8rem" id="myTable">
          <thead>
            <th class="text-center">No.</th>
            <th>Kategori</th>
            <th>Bentuk Jalur</th>
            <th>Aksi</th>
          </thead>
          <tbody>
            @foreach ($bentukJalur as $item)
              <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ ucwords($item->kategori->kategori) }}</td>
                <td>{{ ucwords($item->bentuk_jalur) }}</td>
                <td>
                  <a href="#" class="btn btn-warning btn-sm editButton" data-id="{{ $item->id }}"
                    data-kategori="{{ $item->kategori->kategori }}" data-bentuk_jalur="{{ $item->bentuk_jalur }}"
                    data-bs-toggle="modal" data-bs-target="#editJalurModal" style="font-size: 0.8rem">
                    Edit
                  </a>

                  {{-- MODAL EDIT BENTUK JALUR --}}
                  <div class="modal fade" id="editJalurModal" tabindex="-1" aria-hidden="true">
                    @include('components.modal.bentuk_jalur_edit_modal')
                  </div>

                  <form action="/bentuk_jalur/{{ $item->id }}" method="POST" class="d-inline deleteForm">
                    @csrf
                    @method('delete')
                    <button type="submit" class="btn btn-danger btn-sm deleteAlert" style="font-size: 0.8rem;">
                      Hapus
                    </button>
                  </form>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- UNTUK MEMUNCULKAN DATA YANG AKAN DIEDIT PADA MODAL --}}
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const editButtons = document.querySelectorAll('.editButton');
      const editModal = document.getElementById('editJalurModal');
      const editForm = document.getElementById('editForm');
      const editKategori = document.getElementById('editKategori');
      const editBentukJalur = document.getElementById('editBentukJalur');

      editButtons.forEach(button => {
        button.addEventListener('click', function() {
          // Ambil data dari atribut tombol
          const id = this.getAttribute('data-id');
          const kategori = this.getAttribute('data-kategori');
          const bentukJalur = this.getAttribute('data-bentuk_jalur');

          // Isi data ke dalam form modal
          editForm.action = `/bentuk_jalur/${id}`;
          editKategori.value = kategori;
          editBentukJalur.value = bentukJalur;
        });
      });
    });
  </script>

  <script>
    @if ($errors->any())
      document.addEventListener('DOMContentLoaded', function() {
        var modalImportExcel = new bootstrap.Modal(document.getElementById('createJalurModal'));
        modalImportExcel.show();
      });
    @endif
  </script>
@endsection
