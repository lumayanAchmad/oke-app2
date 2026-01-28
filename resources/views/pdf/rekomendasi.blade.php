<!DOCTYPE html>
<html>

  <head>
    <title>Surat Rekomendasi</title>
    <style>
      /* 1. Pengaturan Halaman */
      @page {
        header: page-header;
        margin-top: 40mm;
        margin-bottom: 20mm;
        margin-header: 5mm;
      }

      body {
        font-family: 'Times New Roman', Times, serif;
        font-size: 12pt;
        line-height: 1.5;
        color: #000;
      }

      /* 2. Style Kop Surat (Struktur 3 Kolom) */
      .kop-table {
        width: 100%;
        border-collapse: collapse;
      }

      .kop-table td {
        vertical-align: middle;
      }

      .text-instansi {
        text-align: left;
        padding-left: 10px;
        color: #000080;
      }

      .kementerian-nama {
        font-weight: bold;
        font-size: 9pt;
        /* Sedikit diperbesar dari 9pt */
        line-height: 1.1;
      }

      .universitas-nama {
        font-weight: bold;
        font-size: 16pt;
        margin: 1px 0;
      }

      .direktorat-nama {
        font-weight: bold;
        font-size: 11pt;
      }

      .text-alamat {
        text-align: right;
        font-size: 7.5pt;
        /* Diperbesar sedikit agar terbaca */
        line-height: 1.2;
        color: #000080;
      }

      .address-label {
        text-decoration: underline;
        font-weight: bold;
      }

      /* 5. Garis ganda bawah kop surat */
      .double-line {
        margin-top: 5px;
        margin-bottom: 5px;
      }

      .double-line-top {
        border-top: 2px solid #000;
        margin-bottom: 1px;
      }

      .double-line-bottom {
        border-top: 1px solid #000;
      }

      /* 3. Style Konten Utama */
      .title {
        text-align: center;
        font-size: 14pt;
        font-weight: bold;
        text-decoration: underline;
        margin-top: 6px;
        margin-bottom: 5px;
      }

      .nomor {
        text-align: center;
        margin-bottom: 20px;
      }

      .content {
        text-align: justify;
      }

      /* Container untuk area tanda tangan/footer di kanan */
      .signature-wrapper {
        width: 40%;
        margin-top: 20px;
        float: right;
        text-align: center;
        /* Membuat isi di dalamnya (tanggal & QR) center */
      }

      .footer-date {
        margin-bottom: 10px;
      }

      .qr-section {
        margin: 0 auto;
        /* Menengahkan box QR di dalam wrapper */
        width: 130px;
        text-align: center;
        border: 0.5pt solid #ccc;
        padding: 8px;
      }

      .clear {
        clear: both;
      }

      .data-table {
        margin-left: 20px;
        border-collapse: collapse;
      }
    </style>
  </head>

  <body>
    <htmlpageheader name="page-header">
      <table class="kop-table">
        <tr>
          <td width="12%">
            <img src="{{ public_path('img/undip.jpg') }}" style="width: 80px;">
          </td>

          <td width="53%" class="text-instansi">
            <div class="kementerian-nama">
              KEMENTERIAN PENDIDIKAN TINGGI, SAINS, DAN TEKNOLOGI
            </div>
            <div class="universitas-nama">UNIVERSITAS DIPONEGORO</div>
            <div class="direktorat-nama">DIREKTORAT SUMBER DAYA MANUSIA</div>
          </td>

          <td width="35%" class="text-alamat">
            <div style="text-decoration: underline; font-weight: bold;">Address:</div>
            1st floor ICT Building<br>
            Jalan Prof. Sudarto, S.H. Postal Code 50275<br>
            Telp. (024) 7460041 Faks. (024) 760033<br>
            www.sdm.undip.ac.id | email: sdm@live.undip.ac.id
          </td>
        </tr>
      </table>
      <div class="double-line">
        <div class="double-line-top"></div>
        <div class="double-line-bottom"></div>
      </div>
    </htmlpageheader>

    <div class="title">SURAT REKOMENDASI</div>
    <div class="nomor">Nomor: {{ $surat->nomor_surat }}</div>

    <div class="content">
      <p>Yang bertanda tangan di bawah ini menerangkan bahwa:</p>
      <table style="margin-left: 20px;">
        <tr>
          <td width="150">Nama Pegawai</td>
          <td width="10">:</td>
          <td><strong>{{ $rencana->dataPegawai->nama }}</strong></td>
        </tr>
        <tr>
          <td>NPPU</td>
          <td>:</td>
          <td>{{ $rencana->dataPegawai->nppu ?? '-' }}</td>
        </tr>
        <tr>
          <td>Unit Kerja</td>
          <td>:</td>
          <td>{{ $rencana->dataPegawai->unitKerja->unit_kerja }}</td>
        </tr>
      </table>

      <div style="margin-top: 15px;">
        <p>Diberikan rekomendasi untuk mengikuti kegiatan pengembangan kompetensi dengan rincian sebagai berikut:</p>
        <table border="1" width="100%" cellpadding="6"
          style="border-collapse: collapse; font-size: 10pt; margin-top: 5px;">
          <tr style="background-color: #f0f0f0;">
            <th width="35%" align="left">Kategori Informasi</th>
            <th width="65%" align="left">Keterangan Detail</th>
          </tr>
          <tr>
            <td><strong>Tahun & Kode Rencana</strong></td>
            <td>{{ $rencana->tahun }} / {{ $rencana->dataPelatihan->kode ?? '-' }}</td>
          </tr>
          <tr>
            <td><strong>Nama Pelatihan / Jurusan</strong></td>
            <td>
              {{ $rencana->dataPelatihan->nama_pelatihan ?? $rencana->dataPendidikan->nama_pendidikan }}
              @if ($rencana->jenjang)
                (Jenjang: {{ $rencana->jenjang }})
              @endif
            </td>
          </tr>
          <tr>
            <td><strong>Bentuk Jalur</strong></td>
            <td>
              {{ ucfirst($rencana->bentukJalur->bentuk_jalur ?? '-') }} -
              {{ ucfirst(optional($rencana->bentukJalur->kategori)->kategori ?? '-') }}
            </td>
          </tr>
          <tr>
            <td><strong>Jam Pelajaran(JP)/Beban Belajar</strong></td>
            <td>{{ $rencana->jam_pelajaran ?? '-' }} JP</td>
          </tr>
          <tr>
            <td><strong>Estimasi Anggaran</strong></td>
            <td>Rp {{ number_format($rencana->anggaran_rencana ?? '-') }}</td>
          </tr>
          <tr>
            <td><strong>Wilayah / Region</strong></td>
            <td> {{ ucfirst($rencana->region->region ?? '-') }}</td>
          </tr>
        </table>
      </div>

      <div style="margin-top: 15px; font-size: 9pt; border-left: 3px solid #000080; padding-left: 10px;">
        <strong>Catatan Validasi Sistem:</strong><br>
        Validator (Ketua Kelompok): {{ $rencana->kelompokCanValidating->kelompok->ketua->nama ?? '-' }} <br>
        Verifikator (Pimpinan Unit): {{ $rencana->unitKerjaCanverifying->dataPegawai->nama ?? '-' }}
      </div>
    </div>

    <div class="signature-wrapper">
      <div class="footer-date">
        <p>Semarang, {{ date('d F Y') }}</p>
      </div>

      <div class="qr-section">
        <p style="font-size:8px; text-align:center; margin-bottom:5px;">
          Scan untuk Verifikasi:
        </p>
        <div style="width:100px; margin:0 auto; text-align:center;">
          <img src="data:image/svg+xml;base64,{{ $qrcode }}" style="width: 100px;">
          <p style="font-size:7px; margin-top:4px; word-break: break-all; font-family: monospace;">
            {{ $surat->kode_verifikasi }}
          </p>
        </div>
      </div>
    </div>

    <div class="clear"></div>
  </body>

</html>
