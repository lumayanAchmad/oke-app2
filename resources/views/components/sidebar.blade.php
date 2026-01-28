@auth
  <style>
    .css-hover-animation {
      transition: transform 200ms ease-in-out;
    }

    .css-hover-animation:hover {
      transform: translateX(8px);
      /* kira-kira setara dengan translate-x-2 di Tailwind */
    }
  </style>
  <aside class="left-sidebar with-vertical">
    <!-- Sidebar scroll-->
    <div>
      <div class="brand-logo d-flex align-items-center justify-content-between mt-2" style="padding-left: 20px">
        <div class="logo-img">
          <img src={{ asset('img/sidebar-logo.png') }}
            style="object-fit: cover; height: 100px; width: 200px; object-position: 80% 40%;" />
        </div>
        <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
          <i class="ti ti-x fs-8"></i>
        </div>
      </div>
      <!-- Sidebar navigation-->
      <nav class="sidebar-nav scroll-sidebar pb-5" data-simplebar="">
        <ul id="sidebarnav">
          {{-- HALAMAN LOGIN --}}
          {{-- PROFIL --}}
          <li class="nav-small-cap mt-1">
            <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
            <span class="hide-menu">HALAMAN UTAMA</span>
          </li>
          <li class="sidebar-item {{ Request::is('ganti_password') ? 'selected' : '' }}">
            <a class="sidebar-link css-hover-animation" href="/profil" aria-expanded="false">
              <span>
                <i class="ti ti-home-2"></i>
              </span>
              <span class="hide-menu">Dashboard</span>
            </a>
          </li>

          @can('admin')
            <li class="nav-small-cap mt-2">
              <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
              <span class="hide-menu">KELOLA PERENCANAAN</span>
            </li>
            <li class="sidebar-item {{ Request::is('tenggat_rencana*') ? 'selected' : '' }}">
              <a class="sidebar-link css-hover-animation" href="/tenggat_rencana" aria-expanded="false">
                <span>
                  <i class="ti ti-calendar"></i>
                </span>
                <span class="hide-menu">Atur Tenggat</span>
              </a>
            </li>
            <li class="sidebar-item {{ Request::is('kelompok*') ? 'selected' : '' }}">
              <a class="sidebar-link css-hover-animation" href="/kelompok" aria-expanded="false">
                <span>
                  <i class="ti ti-users"></i>
                </span>
                <span class="hide-menu">Atur Kelompok</span>
              </a>
            </li>
          @endcan

          @can('admin')
            {{-- PEGAWAI --}}
            <li class="nav-small-cap mt-2">
              <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
              <span class="hide-menu">KELOLA DATA PEGAWAI</span>
            </li>

            <li class="sidebar-item {{ Request::is('data_pegawai*') ? 'selected' : '' }}">
              <a class="sidebar-link css-hover-animation" href="/data_pegawai" aria-expanded="false">
                <span>
                  <i class="ti ti-user"></i>
                </span>
                <span class="hide-menu">Data Pegawai</span>
              </a>
            </li>
            <li class="sidebar-item {{ Request::is('edit_akses*') ? 'selected' : '' }}">
              <a class="sidebar-link css-hover-animation" href="/edit_akses" aria-expanded="false">
                <span>
                  <i class="ti ti-accessible"></i>
                </span>
                <span class="hide-menu">Hak Akses</span>
              </a>
            </li>

            {{-- DATA PELATIHAN --}}
            <li class="nav-small-cap mt-2">
              <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
              <span class="hide-menu">DATABASE PEMBELAJARAN</span>
            </li>
            <li class="sidebar-item {{ Request::is('data_pelatihan*') || Request::is('bentuk_jalur*') ? 'selected' : '' }}">
              <a class="sidebar-link css-hover-animation" href="/data_pelatihan" aria-expanded="false">
                <span>
                  <i class="ti ti-file-stack"></i>
                </span>
                <span class="hide-menu">Data Pelatihan</span>
              </a>
            </li>
            <li class="sidebar-item {{ Request::is('data_pendidikan*') ? 'selected' : '' }}">
              <a class="sidebar-link css-hover-animation" href="/data_pendidikan" aria-expanded="false">
                <span>
                  <i class="ti ti-book"></i>
                </span>
                <span class="hide-menu">Data Pendidikan</span>
              </a>
            </li>
          @endcan

          @can('pegawai')
            {{-- RENCANA PEMBELAJARAN --}}
            <li class="nav-small-cap mt-2">
              <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
              <span class="hide-menu">Rencana Pembelajaran (RPP)</span>
            </li>

            <li class="sidebar-item {{ Request::is('rencana_pembelajaran*') ? 'selected' : '' }}">
              <a class="sidebar-link css-hover-animation" href="/rencana_pembelajaran" aria-expanded="false">
                <span>
                  <i class="ti ti-bookmarks "></i>
                </span>
                <span class="hide-menu">RPP Anda</span>
              </a>
            </li>
          @endcannot

          @can('pegawai')
            {{-- KELOMPOK --}}
            <li class="nav-small-cap mt-2">
              <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
              <span class="hide-menu">Kelompok</span>
            </li>
            <li class="sidebar-item {{ Request::is('anggota_kelompok*') ? 'selected' : '' }}">
              <a class="sidebar-link css-hover-animation" href="/anggota_kelompok" aria-expanded="false">
                <span>
                  <i class="ti ti-sitemap"></i>
                </span>
                <span class="hide-menu">Kelompok Anda</span>
              </a>
            </li>
          @endcannot

          @can('ketua_kelompok')
            <li class="sidebar-item {{ Request::is('validasi_kelompok*') ? 'selected' : '' }}">
              <a class="sidebar-link css-hover-animation" href="/validasi_kelompok" aria-expanded="false">
                <span>
                  <i class="ti ti-checklist"></i>
                </span>
                <span class="hide-menu">Validasi RPP Kelompok</span>
              </a>
            </li>
          @endcan

          @can('verifikator')
            {{-- VERIFIKATOR --}}
            <li class="nav-small-cap mt-2">
              <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
              <span class="hide-menu">RPP UNIT KERJA</span>
            </li>
            <li class="sidebar-item {{ Request::is('verifikasi*') ? 'selected' : '' }}">
              <a class="sidebar-link css-hover-animation" href="/verifikasi" aria-expanded="false">
                <span>
                  <i class="ti ti-checklist"></i>
                </span>
                <span class="hide-menu">Verifikasi RPP Unit Kerja</span>
              </a>
            </li>
          @endcan

          @can('approver')
            {{-- VERIFIKATOR --}}
            <li class="nav-small-cap mt-2">
              <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
              <span class="hide-menu">RPP PEGAWAI</span>
            </li>
            <li class="sidebar-item {{ Request::is('approval*') ? 'selected' : '' }}">
              <a class="sidebar-link css-hover-animation" href="/approval" aria-expanded="false">
                <span>
                  <i class="ti ti-checklist"></i>
                </span>
                <span class="hide-menu">Approval RPP Pegawai</span>
              </a>
            </li>
          @endcan
        </ul>
      </nav>
    </div>
  </aside>
@endauth

{{-- AUTO SCROLL --}}
<script>
  document.addEventListener("DOMContentLoaded", function() {
    const sidebar = document.querySelector(".scroll-sidebar"); // Pastikan selector sesuai
    const activeMenu = document.querySelector(".sidebar-item.selected"); // Menu aktif
    const scrollPosition = localStorage.getItem("sidebar-scroll");

    // Menunggu hingga sidebar benar-benar termuat sebelum menerapkan scroll
    setTimeout(() => {
      // Mengembalikan posisi scroll sebelumnya jika ada
      if (scrollPosition) {
        sidebar.scrollTop = scrollPosition;
      }

      // Jika ada menu aktif, scroll agar menu tersebut terlihat
      if (activeMenu) {
        activeMenu.scrollIntoView({
          behavior: "auto",
          block: "center"
        });
      }
    }, 50); // Delay untuk memastikan elemen tersedia

    // Menyimpan posisi scroll saat sidebar digulir
    sidebar.addEventListener("scroll", function() {
      localStorage.setItem("sidebar-scroll", sidebar.scrollTop);
    });
  });
</script>
