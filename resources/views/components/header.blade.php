<style>
  .notification-item:hover {
    background-color: #f8f9fa;
    transition: background-color 0.2s;
  }

  .avatar {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  /* Style untuk tombol WhatsApp outline */
  .whatsapp-btn-outline {
    color: #25D366;
    border: 1px solid #25D366;
    background-color: transparent;
    border-radius: 6px;
    padding: 5px 12px;
    font-size: 0.85rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s ease;
  }

  .whatsapp-btn-outline:hover {
    background-color: rgba(37, 211, 102, 0.08);
    border-color: #128C7E;
    color: #128C7E;
  }

  .whatsapp-btn-outline:active {
    background-color: rgba(37, 211, 102, 0.15);
  }

  .whatsapp-icon {
    font-size: 1.1rem;
  }
</style>

<header class="app-header">
  <nav class="navbar navbar-expand-lg navbar-light">

    {{-- Tombol menu pas kecil --}}
    <ul class="navbar-nav">
      <li class="nav-item d-block d-xl-none">
        <a class="nav-link sidebartoggler nav-icon-hover" id="headerCollapse" href="javascript:void(0)">
          <i class="ti ti-menu-2"></i>
        </a>
      </li>
    </ul>

    {{-- KONTEN SEBELAH KANAN HEADER --}}
    <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
      <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">

        {{-- BADGE NAMA DAN ROLE --}}
        <div class="border border-2 border-primary border-opacity-50 rounded fw-bolder" style="padding: 6px 10px;">
          {{ Auth::user()->name }} (<span class="text-warning">
            @foreach (Auth::user()->roles as $role)
              {{ ucwords(str_replace('_', ' ', $role->role)) }}
              @if (!$loop->last)
                ,
              @endif
            @endforeach
          </span>)</div>

        {{-- FOTO PROFIL DROPDOWN --}}
        <li class="nav-item dropdown">
          <a class="nav-link nav-icon-hover" href="javascript:void(0)" id="drop2" data-bs-toggle="dropdown"
            aria-expanded="false">
            @if (Auth::user()->dataPegawai && Auth::user()->dataPegawai->foto)
              <img src="{{ Storage::url(Auth::user()->dataPegawai->foto) }}" alt="Foto Profil"
                style="object-fit: cover; height: 35px; width: 35px;" class="rounded-circle">
            @else
              <img src={{ asset('modern/src/assets/images/profile/user-1.jpg') }} alt="" width="35"
                height="35" class="rounded-circle">
            @endif
          </a>

          <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2">
            <div class="message-body">
              <a href="profil" class="d-flex align-items-center gap-2 dropdown-item">
                <i class="ti ti-user fs-6"></i>
                <p class="mb-0 fs-3">Profil Saya</p>
              </a>
              <a href="ganti_password" class="d-flex align-items-center gap-2 dropdown-item">
                <i class="ti ti-settings fs-6"></i>
                <p class="mb-0 fs-3">Ganti Password</p>
              </a>
              {{-- Opsi WhatsApp di dropdown --}}
              <a href="javascript:void(0)" class="d-flex align-items-center gap-2 dropdown-item" data-bs-toggle="modal"
                data-bs-target="#whatsappModal">
                <i class="ti ti-brand-whatsapp fs-6" style="color: #25D366;"></i>
                <p class="mb-0 fs-3">
                  @if (Auth::user()->dataPegawai && Auth::user()->dataPegawai->nomor_telepon)
                    Ubah WhatsApp
                  @else
                    Atur WhatsApp
                  @endif
                </p>
              </a>
              <a href="{{ route('logout') }}" class="btn btn-outline-danger mx-3 mt-2 d-block"
                onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                Logout
              </a>
              <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
              </form>
            </div>
          </div>
        </li>
      </ul>
    </div>
  </nav>
</header>

<!-- Modal untuk input nomor WhatsApp -->
<div class="modal fade" id="whatsappModal" tabindex="-1" aria-labelledby="whatsappModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="whatsappModalLabel">
          <i class="ti ti-brand-whatsapp me-2 text-success"></i>
          @if (Auth::user()->dataPegawai && Auth::user()->dataPegawai->nomor_telepon)
            Ubah Nomor WhatsApp
          @else
            Tambah Nomor WhatsApp
          @endif
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="whatsappForm" method="POST" action="{{ route('save-whatsapp-number') }}">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <p class="text-muted mb-3">
              <i class="ti ti-info-circle text-info me-1"></i>
              Masukkan nomor WhatsApp Anda untuk menerima notifikasi sistem.
            </p>

            <div class="input-group">
              <span class="input-group-text">
                +62
              </span>
              <input type="tel" class="form-control" id="whatsapp_number" name="whatsapp_number"
                placeholder="81234567890"
                value="{{ Auth::user()->dataPegawai && Auth::user()->dataPegawai->nomor_telepon ? Auth::user()->dataPegawai->nomor_telepon : '' }}"
                pattern="[0-9]{9,13}" required>
            </div>
            <div class="form-text">
              Contoh: 81234567890 (tanpa +62 dan tanpa awalan 0)
            </div>
          </div>

          {{-- Status saat ini --}}
          @if (Auth::user()->dataPegawai && Auth::user()->dataPegawai->nomor_telepon)
            <div class="alert alert-light border d-flex align-items-center" role="alert">
              <i class="ti ti-circle-check text-success me-2"></i>
              <div>
                Nomor WhatsApp terdaftar:
                <strong>+62{{ Auth::user()->dataPegawai->nomor_telepon }}</strong>
              </div>
            </div>
          @endif
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-outline-success">
            <i class="ti ti-device-floppy me-1"></i>
            @if (Auth::user()->dataPegawai && Auth::user()->dataPegawai->nomor_telepon)
              Perbarui
            @else
              Simpan
            @endif
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  // Validasi format nomor WhatsApp
  document.getElementById('whatsappForm')?.addEventListener('submit', function(e) {
    const input = document.getElementById('whatsapp_number');
    const value = input.value.replace(/\D/g, '');

    if (value.length < 9 || value.length > 13) {
      e.preventDefault();
      alert('Nomor WhatsApp harus antara 9-13 digit angka');
      input.focus();
      return false;
    }

    input.value = value;
  });
</script>
