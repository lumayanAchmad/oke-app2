@php
  $isOverdue = false;
  if ($rencana->revisi_due_date) {
      $isOverdue = \Carbon\Carbon::now()->greaterThan(\Carbon\Carbon::parse($rencana->revisi_due_date));
  }

  // Ambil status revisi saat ini dari relasi yang ada
  $statusRevisi = $rencana->kelompokCanValidating->status_revisi ?? null;
@endphp

@if ($statusRevisi != 'sudah_direvisi')

  @if ($isOverdue)
    <div class="alert alert-danger p-2 mb-2" style="font-size: 0.75rem;">
      <span class="ti ti-clock-stop"></span>
      <strong>Waktu Habis!</strong> Akses terkunci.
    </div>
  @else
    <div class="btn-group mb-2" role="group">
      {{-- Tombol Edit: Selalu Muncul selama belum lewat deadline --}}
      <a href="/rencana_pembelajaran/{{ $rencana->id }}/edit" class="btn btn-warning btn-sm" style="font-size: 0.8rem"
        title="Revisi">
        <span class="ti ti-scissors"></span>
      </a>

      {{-- Tombol Kirim: Hanya Muncul jika status sudah 'sedang_direvisi' --}}
      @if ($statusRevisi == 'sedang_direvisi')
        <form action="{{ route('rencana_pembelajaran.kirim_revisi', $rencana->id) }}" method="POST"
          id="kirimRevisiForm-{{ $rencana->id }}">
          @csrf
          <button type="submit" class="btn btn-success btn-sm rounded-end-1 kirimRevisiAlert"
            data-form-id="kirimRevisiForm-{{ $rencana->id }}" style="font-size: 0.8rem; border-radius: 0"
            title="Kirim Revisi">
            <span class="ti ti-script"></span>
          </button>
        </form>
      @else
        {{-- Tampilan pengganti jika belum ada perubahan --}}
        <button class="btn btn-dark btn-sm rounded-end-1" disabled
          style="font-size: 0.8rem; opacity: 0.6; cursor: not-allowed;" title="Lakukan perubahan data terlebih dahulu">
          <span class="ti ti-script"></span>
        </button>
      @endif
    </div>

    {{-- Pesan bantuan jika belum ada revisi --}}
    @if ($statusRevisi != 'sedang_direvisi')
      <div class="text-muted mb-2" style="font-size: 0.65rem;">
        *Klik tombol orange untuk mulai revisi
      </div>
    @else
      <div class="text-muted mb-2" style="font-size: 0.65rem;">
        *Klik tombol hijau untuk mengirim revisi
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
