@php
  $isOverdue = false;
  if ($rencana->revisi_due_date) {
      $isOverdue = \Carbon\Carbon::now()->greaterThan(\Carbon\Carbon::parse($rencana->revisi_due_date));
  }

  $statusRevisi = $rencana->unitKerjaCanVerifying->status_revisi ?? null;
@endphp

@if ($rencana->unitKerjaCanVerifying->status == 'disetujui')
  <div class="fw-bold">*Rencana yang sudah disetujui Unit Kerja tidak bisa dihapus atau diedit</div>
@elseif ($rencana->unitKerjaCanVerifying->status == 'direvisi')
  @if ($statusRevisi != 'sudah_direvisi')
    {{-- CEK DEADLINE --}}
    @if ($isOverdue)
      <div class="alert alert-danger p-2 mb-2" style="font-size: 0.75rem;">
        <span class="ti ti-clock-stop"></span>
        <strong>Waktu Revisi Habis!</strong><br>
        Akses edit otomatis terkunci oleh sistem.
      </div>
    @else
      {{-- TOMBOL AKSI JIKA MASIH DALAM DEADLINE --}}
      <div class="btn-group mb-2" role="group">
        {{-- Tombol Edit --}}
        <a href="/rencana_pembelajaran/{{ $rencana->id }}/edit" class="btn btn-warning btn-sm" style="font-size: 0.8rem"
          title="Revisi">
          <span class="ti ti-scissors"></span>
        </a>

        {{-- Tombol Kirim: Hanya aktif jika sudah ada perubahan (sedang_direvisi) --}}
        @if ($statusRevisi == 'sedang_direvisi' || $statusRevisi == 'perlu_revisi_ulang')
          <form action="{{ route('rencana_pembelajaran.kirim_revisi', $rencana->id) }}" method="POST"
            id="kirimRevisiUnitKerjaForm-{{ $rencana->id }}">
            @csrf
            <button type="submit" class="btn btn-success btn-sm rounded-end-1 kirimRevisiAlert"
              data-form-id="kirimRevisiUnitKerjaForm-{{ $rencana->id }}" style="font-size: 0.8rem; border-radius: 0"
              title="Kirim Revisi Unit Kerja">
              <span class="ti ti-script"></span>
            </button>
          </form>
        @else
          {{-- Tombol Disabled jika belum ada perubahan data --}}
          <button class="btn btn-dark btn-sm rounded-end-1" disabled
            style="font-size: 0.8rem; opacity: 0.6; cursor: not-allowed;"
            title="Lakukan perubahan data terlebih dahulu">
            <span class="ti ti-script"></span>
          </button>
        @endif
      </div>

      {{-- Pesan bantuan jika belum mulai edit --}}
      @if ($statusRevisi == 'belum_direvisi')
        <div class="text-muted mb-2" style="font-size: 0.65rem;">
          *Klik tombol orange untuk memperbarui data
        </div>
      @endif
    @endif
  @else
    <div class="fw-bold mb-2">*Revisi yang sedang ditinjau tidak bisa dihapus atau diedit.</div>
  @endif

  <div>
    @if ($statusRevisi)
      @include('partials.rencana_aksi.badge_status', [
          'label' => 'Status Pengerjaan Revisi',
          'status' => $statusRevisi,
      ])
    @endif
  </div>
@else
  <div class="fw-bold">*Rencana dalam proses verifikasi Unit Kerja</div>
@endif
