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
        // Ambil data lengkap
        $rencana = RencanaPembelajaran::with([
            'dataPegawai',
            'dataPelatihan',
            'dataPendidikan',
            'suratRekomendasi',
            'universitasCanApproving',
        ])->findOrFail($id);

        // Security check
        if (
            ! $rencana->universitasCanApproving ||
            $rencana->universitasCanApproving->status !== 'disetujui'
        ) {
            return redirect()->back()->with('error', 'Dokumen belum tersedia.');
        }

        // Buat / ambil surat rekomendasi
        $surat = $this->getOrCreateSurat($rencana);

        $qrcode = QrCode::format('svg')
            ->size(120)
            ->margin(0)
            ->generate($surat->kode_verifikasi);

        $qrcode = preg_replace('/<\?xml.*?\?>/i', '', $qrcode);

        $html = view('pdf.rekomendasi', compact(
            'rencana',
            'surat',
            'qrcode'
        ))->render();

        /**
         * =========================
         * INIT mPDF
         * =========================
         */
        $mpdf = new Mpdf([
            'mode'          => 'utf-8',
            'format'        => 'A4',
            'orientation'   => 'P',
            'margin_left'   => 20,
            'margin_right'  => 20,
            'margin_top'    => 20,
            'margin_bottom' => 20,
        ]);

        // Render PDF
        $mpdf->WriteHTML($html);

        // Nama file aman
        $namaFile = 'Surat_Rekomendasi_' .
        str_replace('/', '-', $surat->nomor_surat) . '.pdf';

        return response(
            $mpdf->Output($namaFile, 'S'),
            200,
            [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $namaFile . '"',
            ]
        );
    }
}
