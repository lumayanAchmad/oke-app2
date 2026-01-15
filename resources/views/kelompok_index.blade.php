@extends('layouts.main_layout', ['title' => 'Data Kelompok'])
@section('content')
  <div class="card mb-4 pb-4 bg-white">
    <div class="card-body px-0 py-0">
      <div class="card-header p-3 fs-5 fw-bolder" style="background-color: #ececec;">Data Kelompok</div>
      <div class="row my-3">
        <div class="col-md-12 d-flex">
          <a href="#" class="btn btn-outline-primary ms-3" style="font-size: 0.9rem" data-bs-toggle="modal"
            data-bs-target="#createKelompokModal">
            <span class="me-1">
              <i class="ti ti-file-plus"></i>
            </span>
            <span>Buat Kelompok Baru</span>
          </a>

          {{-- MODAL CREATE KELOMPOK --}}
          <div class="modal fade" data-bs-backdrop="static" tabindex="-1" aria-hidden="true" id="createKelompokModal">
            @include('components.modal.kelompok_create_modal')
          </div>

          <form action="/kelompok/reset" method="POST"
            onsubmit="return confirm('Apakah Anda yakin ingin mereset semua kelompok? Semua pegawai akan dikeluarkan dari kelompok mereka.')">
            @csrf
            <button type="submit" class="btn btn-warning ms-2">Reset</button>
          </form>
        </div>
      </div>
      <hr class="my-0">
      <div class="table-responsive">
        <table class="table table-striped mb-3" style="font-size: 0.8rem" id="myTable">
          <thead>
            <th class="text-center">No.</th>
            <th>Ketua Kelompok</th>
            <th>NIP / NPPU</th>
            <th>Unit Kerja</th>
            <th>Email</th>
            <th class="text-center">AKSI</th>
          </thead>
          <tbody>
            @foreach ($kelompok as $item)
              <tr>
                <td class="py-3 text-center"> {{ $loop->iteration }} </td>
                <td class="py-3">{{ $item->ketua->nama }}</td>
                <td class="py-3">{{ $item->ketua->nppu }}</td>
                <td class="py-3">{{ $item->ketua->unitKerja->unit_kerja }}</td>
                <td class="py-3">{{ $item->ketua->user->email }}</td>
                <td class="py-3 text-center">
                  <div class="btn-group" role="group">
                    <button class="btn btn-primary btn-sm" style="font-size: 0.8rem" data-bs-toggle="modal"
                      data-bs-target="#detailModal{{ $item->id }}" title="Detail Kelompok"><span
                        class="ti ti-eye"></span>
                    </button>

                    <a href="/kelompok/{{ $item->id }}/edit" class="btn btn-warning btn-sm" style="font-size: 0.8rem"
                      title="Edit"><span class="ti ti-pencil"></span></a>

                    <form action="/kelompok/{{ $item->id }}" method="POST">
                      @csrf
                      @method('delete')
                      <button type="submit" class="btn btn-danger btn-sm rounded-end-1 deleteAlert"
                        style="font-size: 0.8rem; border-radius: 0" title="Hapus">
                        <span class="ti ti-trash"></span>
                      </button>
                    </form>

                  </div>
                  {{-- MODAL --}}
                  <div class="modal fade" id="detailModal{{ $item->id }}" data-bs-backdrop="static"
                    data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel{{ $item->id }}"
                    aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h1 class="modal-title fs-5 fw-semibold" id="staticBackdropLabel{{ $item->id }}">
                            Detail Kelompok <span class="text-primary">{{ $item->ketua->nama }}</span>
                          </h1>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body border border-2 mx-3 rounded-2">
                          <table class="table table-bordered fs-3 mb-0">
                            <thead>
                              <tr>
                                <th>Ketua</th>
                                <th>NPPU</th>
                                <th>Unit Kerja</th>
                                <th>Email</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr class="text-start">
                                <td>{{ $item->ketua->nama }}</td>
                                <td>{{ $item->ketua->nppu }}</td>
                                <td>{{ $item->ketua->unitKerja->unit_kerja }}</td>
                                <td>{{ $item->ketua->user->email }}</td>
                              </tr>
                            </tbody>
                            <thead>
                              <tr>
                                <th>Anggota</th>
                                <th>NPPU</th>
                                <th>Unit Kerja</th>
                                <th>Email</th>
                              </tr>
                            </thead>
                            <tbody>
                              @foreach ($item->anggota->where('id', '!=', $item->ketua->id) as $pegawai)
                                <tr class="text-start">
                                  <td>{{ $pegawai->nama }}</td>
                                  <td>{{ $pegawai->nppu }}</td>
                                  <td>{{ $pegawai->unitKerja->unit_kerja }}</td>
                                  <td>{{ $pegawai->user->email }}</td>
                                </tr>
                              @endforeach
                            </tbody>
                          </table>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                          <a href="/kelompok/{{ $item->id }}/edit" class="btn btn-warning"
                            style="font-size: 0.8rem">Edit</a>
                        </div>
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

  {{-- PAKSA BUKA MODAL JIKA ADA ERROR --}}
  <script>
    @if ($errors->any())
      document.addEventListener('DOMContentLoaded', function() {
        var modalCreateKelompok = new bootstrap.Modal(document.getElementById('createKelompokModal'));
        modalCreateKelompok.show();
      });
    @endif
  </script>
@endsection
