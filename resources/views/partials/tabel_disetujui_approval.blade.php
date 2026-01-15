<div class="table-responsive">
  <table class="table table-hover table-bordered mb-3 px-0 datatables" style="font-size: 0.7rem">
    <thead>
      <tr>
        <th class="align-middle">No.</th>
        <th class="align-middle">Nama</th>
        <th class="align-middle">Tahun <br> Kode</th>
        <th class="align-middle">Bentuk</th>
        <th class="align-middle">Kegiatan</th>
        <th class="align-middle">Rencana</th>
        <th class="align-middle">Keterangan</th>
        <th class="align-middle">AKSI</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($rencana as $rencanaPembelajaran)
        <tr>
          {{-- NOMOR --}}
          <td class="text-center px-2">{{ $loop->iteration }}</td>

          {{-- NAMA PEGAWAI --}}
          <td class="px-2">{{ $rencanaPembelajaran->dataPegawai->nama ?? '-' }}</td>

          {{-- TAHUN DAN KODE --}}
          <td class="text-center px-2">
            {{ $rencanaPembelajaran->tahun ?? '-' }}
            @if ($rencanaPembelajaran->klasifikasi === 'pelatihan' && $rencanaPembelajaran->dataPelatihan)
              <br><span class="fw-semibold">{{ $rencanaPembelajaran->dataPelatihan->kode ?? '-' }}</span>
            @endif
          </td>

          {{-- BENTUK --}}
          <td class="px-2">
            @if ($rencanaPembelajaran->klasifikasi === 'pelatihan')
              @php $kategori = optional($rencanaPembelajaran->bentukJalur->kategori)->kategori; @endphp
              @if ($kategori === 'klasikal')
                <span class="badge text-bg-secondary" style="font-size: 0.7rem">{{ ucwords($kategori) }}</span>
              @else
                <span class="badge text-bg-warning" style="font-size: 0.7rem">{{ ucwords($kategori ?? '-') }}</span>
              @endif
              <br>
              <span class="fw-semibold">Bentuk Jalur:
              </span>{{ $rencanaPembelajaran->bentukJalur->bentuk_jalur ?? '-' }}
              <br>
              <span class="fw-semibold">Rumpun:</span>
              {{ optional($rencanaPembelajaran->dataPelatihan->rumpun)->rumpun ?? '-' }}
            @elseif($rencanaPembelajaran->klasifikasi === 'pendidikan')
              <span class="badge text-bg-primary" style="font-size: 0.7rem">
                {{ ucwords($rencanaPembelajaran->klasifikasi) ?? '-' }}
              </span><br>
              <span class="fw-semibold">Jenjang:</span> {{ optional($rencanaPembelajaran->jenjang)->jenjang ?? '-' }}
              <br><span class="fw-semibold">Jenis Pendidikan:
              </span>{{ optional($rencanaPembelajaran->jenisPendidikan)->jenis_pendidikan ? strtoupper($rencanaPembelajaran->jenisPendidikan->jenis_pendidikan) : '-' }}
            @else
              -
            @endif
          </td>

          {{-- KEGIATAN --}}
          <td class="px-2">
            @if ($rencanaPembelajaran->klasifikasi === 'pelatihan')
              <span class="fw-semibold">Nama Pelatihan:
              </span><br>{{ $rencanaPembelajaran->dataPelatihan->nama_pelatihan ?? '-' }}
            @else
              <span class="fw-semibold">Jurusan: </span><br>{{ $rencanaPembelajaran->dataPendidikan->jurusan ?? '-' }}
            @endif
          </td>

          {{-- RENCANA --}}
          <td class="px-2">
            <span class="fw-semibold">Region:
            </span>{{ optional($rencanaPembelajaran->region)->region ? ucwords($rencanaPembelajaran->region->region) : '-' }}
            <br>
            <span class="fw-semibold">JP: </span>{{ $rencanaPembelajaran->jam_pelajaran ?? '-' }} JP <br>
            <span class="fw-semibold">Anggaran: </span>
            {{ isset($rencanaPembelajaran->anggaran_rencana) ? 'Rp' . number_format($rencanaPembelajaran->anggaran_rencana, 0, ',', '.') : '-' }}
          </td>

          {{-- KETERANGAN --}}
          <td class="px-1">
            <div>
              <span class="fw-semibold">Prioritas:</span>
            </div>
            @if ($rencanaPembelajaran->prioritas === 'rendah')
              <span class="badge rounded-pill text-bg-success" style="font-size: 0.7rem">Rendah</span>
            @elseif ($rencanaPembelajaran->prioritas === 'sedang')
              <span class="badge rounded-pill text-bg-warning" style="font-size: 0.7rem">Sedang</span>
            @elseif ($rencanaPembelajaran->prioritas === 'tinggi')
              <span class="badge rounded-pill text-bg-danger" style="font-size: 0.7rem">Tinggi</span>
            @else
              <span class="text-muted">-</span>
            @endif
            {{-- Ketua Kelompok --}}
            <div class="text-start mt-2">
              <span class="fw-semibold">Ketua Kelompok:</span>
              {{ optional($rencanaPembelajaran->dataPegawai->kelompok->ketua)->nama ?? '-' }}
            </div>
          </td>

          {{-- AKSI --}}
          <td class="px-2 text-center">
            @php $approval = optional($rencanaPembelajaran->universitasCanApproving); @endphp

            @if ($approval->id)
              <form action="{{ route('approval.destroy', $approval->id) }}" method="POST"
                id="batalApproveForm-{{ $rencanaPembelajaran->id }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm batalSetujuiAlert" title="Batalkan Approval"
                  data-form-id="batalApproveForm-{{ $rencanaPembelajaran->id }}">
                  <span class="ti ti-arrow-back fs-3"></span>
                </button>
              </form>
            @else
              <span class="text-muted">Tidak ada action</span>
            @endif
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
