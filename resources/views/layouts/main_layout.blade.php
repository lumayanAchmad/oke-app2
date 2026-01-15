<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? '' }} | {{ config('app.name', 'Laravel') }}</title>
    <link rel="shortcut icon" href="{{ asset('img/undip.png') }}">
    <link rel="stylesheet" href={{ asset('modern/src/assets/css/styles.min.css') }}>
  </head>

  <style>
    /* Override hover dengan selector yang lebih spesifik */
    .card-header a.link-custom:hover {
      color: #5D87FF !important;
      /* Warna saat hover (hitam solid) */
      opacity: 1 !important;
      /* Hilangkan opacity saat hover */
    }
  </style>

  <body>
    <!--  BODY WRAPPER -->
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
      data-sidebar-position="fixed" data-header-position="fixed">
      <!-- SIDEBAR -->
      <x-sidebar></x-sidebar>
      {{-- SIDEBAR END --}}

      <!--  MAIN WRAPPER -->
      <div class="body-wrapper">
        <!--  HEADER  -->
        @auth
          <x-header></x-header>
        @endauth
        {{-- HEADER END --}}
        {{-- KONTEN --}}
        <div class="container-fluid ">
          {{-- MODAL PERINGATAN GANTI PASSWORD --}}
          @if (session('default_password'))
            {{-- MODAL NOTIFIKASI GANTI PASSWORD --}}
            <div class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true"
              id="notifPasswordModal">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-body border border-2 mx-3 rounded-2" style="margin-top: 24px">
                    <div class="d-flex">
                      <div class="icon me-3">
                        <i class="ti ti-alert-triangle text-warning display-6"></i>
                      </div>
                      <div>
                        <p class="fs-5 fw-semibold text-dark mb-1">
                          Perhatian: Password Anda masih menggunakan password default!
                        </p>
                        <p class="text-muted mb-3">
                          Untuk mengakses halaman ini, harap segera mengganti password Anda dengan yang lebih aman.
                        </p>
                        <a href="ganti_password" class="btn btn-warning btn-sm text-white px-4">
                          <i class="ti ti-key me-2"></i> Ganti Password
                        </a>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    {{-- Tidak perlu tombol close --}}
                  </div>
                </div>
              </div>
            </div>
          @endif
          @yield('content')
        </div>
      </div>
    </div>

    {{-- SCRIPT TEMPLATE --}}
    <script src={{ asset('modern/src/assets/libs/jquery/dist/jquery.min.js') }}></script>
    <script src={{ asset('modern/src/assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}></script>
    @stack('scripts')
    @stack('scripts2')
    <script src={{ asset('modern/src/assets/js/sidebarmenu.js') }}></script>
    <script src={{ asset('modern/src/assets/js/app.min.js') }}></script>
    <script src={{ asset('modern/src/assets/libs/simplebar/dist/simplebar.js') }}></script>

    {{-- SCRIPT CHART --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- password visibility togle --}}
    @stack('password_visibility')
    @stack('password_login')

    {{-- SCRIPT MODAL PERINGATAN GANTI PASSWORD --}}
    <script>
      document.addEventListener("DOMContentLoaded", function() {
        const modalElement = document.getElementById('notifPasswordModal');
        if (modalElement && modalElement.dataset.show === undefined) {
          const modal = new bootstrap.Modal(modalElement);
          modal.show();
        }
      });
    </script>

    {{-- DATA TABLES --}}
    <link rel="stylesheet" href={{ asset('assets/css/datatables.min.css') }}>
    <script src={{ asset('assets/js/datatables.min.js') }}></script>

    {{-- UMUM --}}
    <script>
      $(document).ready(function() {
        $('#myTable').DataTable({
          "language": {
            "url": "{{ asset('modern/src/assets/js/datatables_custom.json') }}",
            "sEmptyTable": "Data Tidak Tersedia"
          }
        });
      });
    </script>

    <script>
      $(document).ready(function() {
        $('.datatables').DataTable({
          "language": {
            "url": "{{ asset('modern/src/assets/js/datatables_custom.json') }}",
            "sEmptyTable": "Data Tidak Tersedia"
          }
        });
      });
    </script>

    {{-- SELECT 2 --}}
    <link rel="stylesheet" href={{ asset('assets/css/select2.min.css') }}>
    <link rel="stylesheet" href={{ asset('assets/css/select2-bootstrap4.min.css') }}>
    <script src={{ asset('assets/js/select2.full.min.js') }}></script>
    @stack('bentuk_jalur')

    <script>
      $(".placeholder-single").select2({
        dropdownParent: $('#createKelompokModal'),
        theme: 'bootstrap4',
        placeholder: "-- Pilih Ketua Kelompok -- (Ketik untuk mencari pegawai!)",
        allowClear: true
      });

      $(".placeholder-single-edit").select2({
        theme: 'bootstrap4',
        placeholder: "-- Pilih Ketua Kelompok -- (Ketik untuk mencari pegawai!)",
        allowClear: true
      });

      $(".placeholder-multiple").select2({
        dropdownParent: $('#createKelompokModal'),
        theme: 'bootstrap4',
        placeholder: "-- Pilih Anggota Kelompok -- (Ketik untuk mencari pegawai!)"
      });

      $(".placeholder-multiple-edit").select2({
        theme: 'bootstrap4',
        placeholder: "-- Pilih Anggota Kelompok -- (Ketik untuk mencari pegawai!)"
      });

      $(".bentukjalur-placeholder-single").select2({
        theme: 'bootstrap4',
        placeholder: "-- Pilih Bentuk Jalur --",
        allowClear: true,
        dropdownParent: '#createPelatihanModal'
      });

      $(".bentuk-jalur-single").select2({
        theme: 'bootstrap4',
        placeholder: "-- Pilih Bentuk Jalur --",
        allowClear: true,
      });

      $(".kategori-single").select2({
        theme: 'bootstrap4',
        placeholder: "-- Pilih Kategori --",
        allowClear: true
      });

      $(".kategori-edit-single").select2({
        theme: 'bootstrap4',
      });

      $(".bentuk-jalur-single-edit").select2({
        theme: 'bootstrap4',
      });

      $(".jenjang-single").select2({
        theme: 'bootstrap4',
        placeholder: "-- Pilih Jenjang --",
        allowClear: true
      });

      $(".rumpun-single").select2({
        theme: 'bootstrap4',
        placeholder: "-- Pilih Rumpun --",
        allowClear: true
      });

      $(".jurusan-single").select2({
        theme: 'bootstrap4',
        placeholder: "-- Pilih Jurusan --",
        allowClear: true
      });

      $(".nama-pelatihan-single").select2({
        theme: 'bootstrap4',
        placeholder: "-- Pilih Nama Pelatihan --",
        allowClear: true
      });

      $(".jenis-pendidikan-single").select2({
        theme: 'bootstrap4',
        placeholder: "-- Pilih Jenis Pendidikan --",
        allowClear: true
      });

      $(".rumpun-single-pelatihan").select2({
        theme: 'bootstrap4',
        placeholder: "-- Pilih Rumpun --",
        allowClear: true
      });
    </script>

    {{-- SWEET ALERT --}}
    <script src={{ asset('assets/js/sweetalert2.min.js') }}></script>

    {{-- Untuk Main Layout --}}
    <script>
      // Untuk alert success
      @if (session('success'))
        Swal.fire({
          icon: 'success',
          toast: true,
          position: 'top-end',
          title: '{{ session('success') }}',
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true
        });
      @endif

      // Untuk alert error
      @if (session('error'))
        Swal.fire({
          icon: 'error',
          toast: true,
          position: 'top-end',
          title: '{{ session('error') }}',
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true
        });
      @endif

      // Untuk alert info
      @if (session('info'))
        Swal.fire({
          icon: 'info',
          toast: true,
          position: 'top-end',
          title: '{{ session('info') }}',
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true
        });
      @endif

      // Untuk alert warning
      @if (session('warning'))
        Swal.fire({
          icon: 'warning',
          toast: true,
          position: 'top-end',
          title: '{{ session('warning') }}',
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true
        });
      @endif

      // Untuk info (login message)
      @if (session('status'))
        Swal.fire({
          icon: 'success',
          title: '{{ session('status') }}',
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true
        });
      @endif
    </script>

    {{-- SWEET ALERT HALAMAN RENCANA PEMBELAJARAN / CREATE --}}
    @stack('alert-rencana')

    {{-- ALERT IMPORT --}}
    <script>
      document.querySelectorAll('.importAlert').forEach(button => {
        button.addEventListener('click', event => {
          event.preventDefault();
          let form = button.closest('form');
          let fileInput = form.querySelector('input[type="file"]');
          let file = fileInput.files[0];

          if (!file) {
            Swal.fire({
              title: "Peringatan!",
              text: "Silakan unggah file Excel terlebih dahulu!",
              icon: "warning",
              confirmButtonText: "OK"
            });
            return;
          }

          let allowedExtensions = ['xls', 'xlsx'];
          let fileExtension = file.name.split('.').pop().toLowerCase();

          if (!allowedExtensions.includes(fileExtension)) {
            Swal.fire({
              title: "Peringatan!",
              text: "Format file tidak sesuai! Silakan unggah file dengan format .xls atau .xlsx",
              icon: "warning",
              confirmButtonText: "OK"
            });
            return;
          }

          Swal.fire({
            title: "Konfirmasi Import Data!",
            text: "Pastikan file Excel yang anda unggah sudah benar!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#13DEB9",
            confirmButtonText: "Import",
            cancelButtonText: "Batal"
          }).then(result => {
            if (result.isConfirmed) {
              // Tambahkan spinner pada tombol
              button.innerHTML =
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mengimport...';
              button.disabled = true;

              form.submit();
            }
          });
        });
      });
    </script>

    {{-- ALERT UNTUK validasi KELOMPOK --}}

    <script>
      document.querySelectorAll('.setujuiAlert').forEach(button => {
        button.addEventListener('click', event => {
          event.preventDefault();
          let formId = button.getAttribute('data-form-id');
          Swal.fire({
            title: "Konfirmasi Persetujuan",
            text: "Apakah Anda yakin ingin menyetujui rencana ini?",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#13DEB9",
            confirmButtonText: "Setujui",
            cancelButtonText: "Batal"
          }).then(result => {
            if (result.isConfirmed) {
              // Tambahkan spinner pada tombol
              button.innerHTML =
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...';
              button.disabled = true;

              document.getElementById(formId).submit();
            }
          });
        });
      });
    </script>

    <script>
      document.querySelectorAll('.revisiAlert').forEach(button => {
        button.addEventListener('click', event => {
          event.preventDefault();
          let formId = button.getAttribute('data-form-id');
          let form = document.getElementById(formId);
          let catatan = form.querySelector('textarea[name="catatan"]').value.trim();

          if (catatan === '') {
            Swal.fire({
              title: "Catatan revisi harus diisi!",
              text: "Silakan mengisikan alasan atau catatan revisi sebelum melanjutkan.",
              icon: "error",
              confirmButtonText: "OK"
            });
            return;
          }

          Swal.fire({
            title: "Konfirmasi Revisi",
            text: "Pastikan catatan revisi sudah sesuai sebelum dikirim.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#ffae1f",
            confirmButtonText: "Revisi",
            cancelButtonText: "Batal"
          }).then(result => {
            if (result.isConfirmed) {
              // Tambahkan spinner pada tombol
              button.innerHTML =
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...';
              button.disabled = true;

              form.submit();
            }
          });
        });
      });
    </script>

    <script>
      document.querySelectorAll('.batalSetujuiAlert').forEach(button => {
        button.addEventListener('click', event => {
          event.preventDefault();
          let formId = button.getAttribute('data-form-id');
          Swal.fire({
            title: "Batalkan Persetujuan?",
            text: "Anda akan membatalkan persetujuan RPP ini. Yakin ingin melanjutkan?",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#FA896B",
            confirmButtonText: "Batalkan",
            cancelButtonText: "Tidak"
          }).then(result => {
            if (result.isConfirmed) {
              document.getElementById(formId).submit();
            }
          });
        });
      });
    </script>

    <script>
      document.querySelectorAll('.batalTolakAlert').forEach(button => {
        button.addEventListener('click', event => {
          event.preventDefault();
          let formId = button.getAttribute('data-form-id');
          Swal.fire({
            title: "Batalkan Penolakan?",
            text: "Anda akan membatalkan penolakan RPP ini. Yakin ingin melanjutkan?",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#FA896B",
            confirmButtonText: "Batalkan",
            cancelButtonText: "Tidak"
          }).then(result => {
            if (result.isConfirmed) {
              document.getElementById(formId).submit();
            }
          });
        });
      });
    </script>

    <script>
      document.querySelectorAll('.setujuiDirevisiAlert').forEach(button => {
        button.addEventListener('click', event => {
          event.preventDefault();
          let formId = button.getAttribute('data-form-id');
          Swal.fire({
            title: "Konfirmasi Persetujuan",
            text: "Apakah Anda yakin ingin menyetujui rencana ini?",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#13DEB9",
            confirmButtonText: "Setujui",
            cancelButtonText: "Batal"
          }).then(result => {
            if (result.isConfirmed) {
              document.getElementById(formId).submit();
            }
          });
        });
      });
    </script>

    <script>
      document.querySelectorAll('.kirimRevisiAlert').forEach(button => {
        button.addEventListener('click', event => {
          event.preventDefault();
          let formId = button.getAttribute('data-form-id');
          Swal.fire({
            title: "Kirim Revisi?",
            text: "Setelah Anda mengirimkan revisi ini, data tidak dapat diedit kembali. Yakin ingin melanjutkan?",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#13DEB9",
            confirmButtonText: "Kirim",
            cancelButtonText: "Batal"
          }).then(result => {
            if (result.isConfirmed) {
              document.getElementById(formId).submit();
            }
          });
        });
      });
    </script>

    <script>
      document.querySelectorAll('.tambahRevisiAlert').forEach(button => {
        button.addEventListener('click', event => {
          event.preventDefault();
          let formId = button.getAttribute('data-form-id');
          Swal.fire({
            title: "Konfirmasi Revisi",
            text: "Pastikan catatan revisi sudah sesuai sebelum dikirim.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#ffae1f",
            confirmButtonText: "Kirim Revisi",
            cancelButtonText: "Batal"
          }).then(result => {
            if (result.isConfirmed) {
              document.getElementById(formId).submit();
            }
          });
        });
      });
    </script>

    {{-- SWEET ALERT UNTUK CREATE --}}
    <script>
      let createAlert = document.getElementById('createAlert');
      if (createAlert) {
        createAlert.addEventListener('click', function(event) {
          event.preventDefault();
          Swal.fire({
            title: "Konfirmasi Data",
            text: "Pastikan data yang anda isikan sudah benar!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Simpan",
            cancelButtonText: "Batal"
          }).then((result) => {
            if (result.isConfirmed) {
              document.getElementById('createFormID').submit();
            }
          });
        });
      }
    </script>

    {{-- SWEET ALERT UNTUK DELETE --}}
    <script>
      document.querySelectorAll('.deleteAlert').forEach(function(button) {
        button.addEventListener('click', function(event) {
          event.preventDefault(); // Mencegah submit langsung
          Swal.fire({
            title: "Hapus Data?",
            text: "Data yang dihapus tidak dapat dipulihkan. Yakin ingin melanjutkan?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#f9886b",
            confirmButtonText: "Hapus",
            cancelButtonText: "Batal"
          }).then((result) => {
            if (result.isConfirmed) {
              // Langsung submit form tanpa popup tambahan
              button.closest('form').submit();
            }
          });
        });
      });
    </script>

    {{-- SWEET ALERT UNTUK AJUKAN VERIFIKASI --}}
    <script>
      // Alert untuk ajukan verifikasi - TANPA alert sukses
      document.querySelectorAll('.ajukanAlert').forEach(button => {
        button.addEventListener('click', function(event) {
          event.preventDefault();
          const formId = this.getAttribute('data-form-id');
          const ajukanForm = document.getElementById(formId);

          Swal.fire({
            title: "Ajukan Verifikasi",
            text: "Apakah Anda yakin ingin mengajukan Rencana Pembelajaran ini untuk diverifikasi?",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#13deb9",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Ya, Ajukan!",
            cancelButtonText: "Batal"
          }).then((result) => {
            if (result.isConfirmed) {
              // Langsung submit form TANPA alert sukses
              ajukanForm.submit();
            }
          });
        });
      });
    </script>

    {{-- UNTUK EDIT --}}
    <script>
      let editAlert = document.getElementById('editAlert');
      if (editAlert) {
        editAlert.addEventListener('click', function(event) {
          event.preventDefault();
          Swal.fire({
            title: "Ajukan Verifikasi?",
            text: "Setelah diajukan, rencana pembelajaran tidak dapat diedit lagi. Yakin ingin melanjutkan?",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#13deb9",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Ajukan",
            cancelButtonText: "Batal"
          }).then((result) => {
            if (result.isConfirmed) {
              document.getElementById('editFormID').submit();
            }
          });
        });
      }
    </script>

    {{-- UNTUK TENGGAT RENCANA --}}
    <script>
      document.querySelectorAll('.createJadwalAlert').forEach(function(button, index) {
        button.addEventListener('click', function(event) {
          event.preventDefault(); // Mencegah submit langsung
          Swal.fire({
            title: "Simpan Tenggat Rencana?",
            text: "Pastikan tanggal tenggat yang Anda tentukan sudah benar sebelum disimpan.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Simpan",
            cancelButtonText: "Batal"
          }).then(() => {
            // Submit form yang terkait dengan tombol ini
            button.closest('form').submit(); // Submit form terkait
          });
        });
      });
    </script>
  </body>

</html>
