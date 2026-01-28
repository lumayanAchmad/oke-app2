<?php
namespace App\Http\Controllers;

use App\Models\RencanaPembelajaran;
use App\Models\SuratRekomendasi;
use Illuminate\Support\Str;
use Mpdf\Mpdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SuratRekomendasiController extends Controller
{
    private function generateNomorSurat($id)
    {
        $count = SuratRekomendasi::count() + 1;
        $bulan = date('n');
        $tahun = date('Y');

        // Konversi bulan ke Romawi (opsional, biar keren)
        $romawi = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];

        return sprintf("%03d", $count) . "/REKOM/" . $romawi[$bulan] . "/" . $tahun;
    }

    // Fungsi utama yang akan dipanggil saat tombol download diklik
    public function getOrCreateSurat($rencana)
    {
        // Cek apakah surat sudah pernah dibuat sebelumnya
        $surat = $rencana->suratRekomendasi;

        if (! $surat) {
            // Jika belum ada, buat baru
            $surat = SuratRekomendasi::create([
                'rencana_pembelajaran_id' => $rencana->id,
                'nomor_surat'             => $this->generateNomorSurat($rencana->id),
                'kode_verifikasi'         => (string) Str::uuid(), // Kode unik untuk QR
            ]);
        }

        return $surat;
    }

    public function downloadRekomendasi($id)
    {
        $rencana = RencanaPembelajaran::with([
            'dataPegawai', 'dataPegawai.unitKerja', 'dataPelatihan', 'dataPendidikan',
            'suratRekomendasi', 'universitasCanApproving', 'bentukJalur.kategori', 'region',
        ])->findOrFail($id);

        if (! $rencana->universitasCanApproving || $rencana->universitasCanApproving->status !== 'disetujui') {
            return redirect()->back()->with('error', 'Dokumen belum tersedia.');
        }

        $surat = $this->getOrCreateSurat($rencana);

        // SETELAH (ganti dengan format data lengkap):
        $qrContent  = "SURAT REKOMENDASI\n";
        $qrContent .= "Nomor: " . $surat->nomor_surat . "\n\n";
        $qrContent .= "PEGAWAI:\n";
        $qrContent .= "Nama: " . $rencana->dataPegawai->nama . "\n";
        $qrContent .= "KEGIATAN:\n";
        $qrContent .= ($rencana->dataPelatihan->nama_pelatihan ?? $rencana->dataPendidikan->nama_pendidikan) . "\n\n";
        $qrContent .= ($rencana->jam_pelajaran ?? '0') . " JP | ";
        $qrContent .= "KODE VERIFIKASI: " . $surat->kode_verifikasi . "\n\n";
        $qrContent .= "*Surat ini sah dan dapat diverifikasi*";

        $qrcode = base64_encode(
            QrCode::format('svg')
                ->size(150)
                ->margin(0)
                ->generate($qrContent)
        );

        $html = view('pdf.rekomendasi', compact('rencana', 'surat', 'qrcode'))->render();

        $mpdf = new Mpdf([
            'mode'          => 'utf-8',
            'format'        => 'A4',
            'margin_top'    => 45,
            'margin_header' => 5,
            'tempDir'       => storage_path('app/public'),
        ]);

        $mpdf->WriteHTML($html);

        $namaFile = 'Rekomendasi_' . str_replace('/', '-', $surat->nomor_surat) . '.pdf';

        return response($mpdf->Output($namaFile, 'S'), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $namaFile . '"',
        ]);
    }
}
