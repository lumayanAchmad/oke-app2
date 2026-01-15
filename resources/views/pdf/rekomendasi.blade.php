<!DOCTYPE html>
<html>

  <head>
    <title>Surat Rekomendasi</title>
    <style>
      body {
        font-family: 'Times New Roman', Times, serif;
        font-size: 12pt;
        line-height: 1.5;
      }

      .header {
        text-align: center;
        margin-bottom: 20px;
        border-bottom: 2px solid #000;
        padding-bottom: 10px;
      }

      .title {
        text-align: center;
        font-weight: bold;
        text-decoration: underline;
        margin-bottom: 5px;
      }

      .nomor {
        text-align: center;
        margin-bottom: 30px;
      }

      .content {
        margin-bottom: 20px;
        text-align: justify;
      }

      .footer {
        margin-top: 50px;
      }

      .qr-section {
        margin-top: 30px;
        float: left;
        width: 30%;
        text-align: center;
        border: 1px solid #ccc;
        padding: 5px;
      }
    </style>
  </head>

  <body>
    <div class="header">
      <h2 style="margin:0">UNIVERSITAS DIPONEGORO</h2>
      <p style="margin:0">Alamat Kampus No. 1, Kota Kamu</p>
    </div>

    <div class="title">SURAT REKOMENDASI PERENCANAAN PENGEMBANGAN KOMPETENSI PEGAWAI</div>
    <div class="nomor">Nomor: {{ $surat->nomor_surat }}</div>

    <div class="content">
      <p>Yang bertanda tangan di bawah ini menerangkan bahwa:</p>
      {{-- Bagian Identitas Pegawai --}}
      <table style="margin-left: 20px;">
        <tr>
          <td width="120">Nama Pegawai</td>
          <td width="10">:</td>
          {{-- Sesuaikan 'nama' dengan nama kolom di tabel data_pegawais kamu --}}
          <td><strong>{{ $rencana->dataPegawai->nama }}</strong></td>
        </tr>
        <tr>
          <td>NIP / ID</td>
          <td>:</td>
          <td>{{ $rencana->dataPegawai->nppu ?? '-' }}</td>
        </tr>
        <tr>
          <td>Unit Kerja</td>
          <td>:</td>
          {{-- Jika DataPegawai punya relasi unitKerja, panggil seperti ini --}}
          <td>{{ $rencana->dataPegawai->unitKerja->unit_kerja ?? 'Unit Kerja Terkait' }}</td>
        </tr>
      </table>

      <div class="content">
        <p>Diberikan rekomendasi untuk mengikuti kegiatan pengembangan:</p>
        <table border="1" width="100%" cellpadding="5" style="border-collapse: collapse;">
          <tr>
            <th align="left">Jenis Kegiatan</th>
            {{-- Logika untuk menampilkan Pelatihan atau Pendidikan --}}
            <td>
              @if ($rencana->dataPelatihan->nama_pelatihan)
                {{ $rencana->dataPelatihan->nama_pelatihan }}
              @else
                {{ $rencana->dataPendidikan->nama_pendidikan }}
              @endif
            </td>
          </tr>
        </table>
      </div>
    </div>

    <div class="footer">
      <p>Dicetak pada: {{ date('d F Y') }}</p>
    </div>

    {{-- Nanti QR Code ditaruh di sini --}}
    <div class="qr-section">
      <p style="font-size:10px; text-align:center; margin-bottom:5px;">
        Scan untuk Verifikasi:
      </p>

      <div style="width:120px; margin:0 auto; text-align:center;">
        {!! $qrcode !!}
        <p style="font-size:8px; margin-top:4px;">
          {{ $surat->kode_verifikasi }}
        </p>
      </div>
    </div>

  </body>

</html>
