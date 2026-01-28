<?php
namespace App\Http\Controllers;

use App\Models\CatatanValidasiKelompok;
use App\Models\Kelompok;
use App\Models\KelompokCanValidating;
use App\Models\RencanaPembelajaran;
use App\Services\DeadlineService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class kelompokCanValidatingController extends Controller
{
    protected $deadlineService;

    public function __construct(DeadlineService $deadlineService)
    {
        $this->middleware('can:ketua_kelompok');
        $this->deadlineService = $deadlineService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(DeadlineService $deadlineService)
    {
        $ketuaKelompok = Auth::user()->dataPegawai;

        // Dapatkan informasi deadline
        $deadlineInfo     = $deadlineService->getDeadlineInfo('validasi_kelompok');
        $isWithinDeadline = $deadlineInfo['is_within_deadline'] ?? false;
        $startDate        = $deadlineInfo['start_date'] ?? null;
        $endDate          = $deadlineInfo['end_date'] ?? null;
        $isDeadlineSet    = $deadlineInfo['is_set'] ?? false;

        $daysUntilStart  = $startDate ? now()->diffInDays($startDate, false) : null;
        $isNotStartedYet = $startDate && $daysUntilStart > 0;

        $kelompok = Kelompok::with(['anggota', 'ketua.unitKerja', 'kelompokCanValidating'])
            ->where('id_ketua', $ketuaKelompok->id)
            ->first();

        if (! $kelompok) {
            return redirect()->back()->with('error', 'Anda tidak memiliki kelompok.');
        }

        $anggota = $kelompok->anggota;

        // PERBAIKAN 1: Ambil semua rencana dari anggota, tidak filter status_pengajuan
        $rencana = RencanaPembelajaran::whereIn('data_pegawai_id', $anggota->pluck('id'))
            ->where(function ($query) {
                // Tampilkan semua yang statusnya masih dalam proses validasi kelompok
                // atau yang sudah disetujui/direvisi oleh kelompok
                $query->where('status_pengajuan', 'diajukan')
                    ->orWhere('status_pengajuan', 'disetujui');
            })
            ->with([
                'dataPegawai',
                'dataPelatihan',
                'dataPendidikan',
                'bentukJalur',
                'region',
                'jenjang',
                'kelompokCanValidating.catatanValidasiKelompok',
                'unitKerjaCanverifying',
                'universitasCanApproving',
            ])
            ->get();

        // PERBAIKAN 2: Ubah logika filtering berdasarkan relasi kelompokCanValidating
        $rencanaDisetujui = $rencana->filter(function ($item) {
            // Tampilkan jika sudah disetujui oleh kelompok (status kelompokCanValidating = 'disetujui')
            return optional($item->kelompokCanValidating)->status === 'disetujui';
        });

        $rencanaDirevisi = $rencana->filter(function ($item) {
            return optional($item->kelompokCanValidating)->status === 'direvisi';
        });

        $rencanaBelumDivalidasi = $rencana->filter(function ($item) {
            // Belum ada validasi dari kelompok
            return is_null($item->kelompokCanValidating) ||
            is_null($item->kelompokCanValidating->status);
        });

        return view('validasi_kelompok_index', [
            'anggota'                => $anggota,
            'rencana'                => $rencana,
            'rencanaDisetujui'       => $rencanaDisetujui,
            'rencanaDirevisi'        => $rencanaDirevisi,
            'rencanaBelumDivalidasi' => $rencanaBelumDivalidasi,
            'kelompok'               => $kelompok,
            'isWithinDeadline'       => $isWithinDeadline,
            'isNotStartedYet'        => $isNotStartedYet,
            'isDeadlineSet'          => $isDeadlineSet,
            'startDate'              => $startDate,
            'endDate'                => $endDate,
            'daysUntilStart'         => $daysUntilStart,
            'totalRencana'           => $rencana->count(),
            'totalDisetujui'         => $rencanaDisetujui->count(),
            'totalPerluRevisi'       => $rencanaDirevisi->count(),
        ]);
    }

    public function setujui(Request $request, $id)
    {
        $rencana = RencanaPembelajaran::findOrFail($id);

        // Cek tenggat waktu
        $deadlineInfo     = $this->deadlineService->getDeadlineInfo('validasi_kelompok');
        $isWithinDeadline = $deadlineInfo['is_within_deadline'] ?? false;

        if (! $isWithinDeadline) {
            return redirect()->route('validasi_kelompok.index')
                ->with('error', 'Tidak dapat menyetujui rencana pembelajaran di luar tenggat waktu yang dittentukan!');
        }

        // Ambil ketua kelompok yang sedang login
        $ketuaKelompok = Auth::user()->dataPegawai;

        // Ambil kelompok yang dipimpin oleh ketua
        $kelompok = Kelompok::where('id_ketua', $ketuaKelompok->id)->first();

        if (! $kelompok) {
            return redirect()->back()->with('error', 'Anda tidak memiliki kelompok.');
        }

        // Simpan validasi
        $validasi = KelompokCanValidating::updateOrCreate([
            'rencana_pembelajaran_id' => $rencana->id,
            'kelompok_id'             => $kelompok->id,
            'status'                  => 'disetujui',
            'status_revisi'           => 'disetujui',
        ]);

        // Simpan catatan validasi kelompok jika catatan tidak kosong
        if ($request->catatan) {
            CatatanValidasiKelompok::create([
                'kelompok_can_validating_id' => $validasi->id,
                'catatan'                    => $request->catatan,
            ]);
        }

        return redirect()->route('validasi_kelompok.index')
            ->with('success', 'Rencana Berhasil Disetujui!');
    }

    public function revisi(Request $request, $id)
    {
        $rencana = RencanaPembelajaran::findOrFail($id);

        // Cek tenggat waktu
        $deadlineInfo     = $this->deadlineService->getDeadlineInfo('validasi_kelompok');
        $isWithinDeadline = $deadlineInfo['is_within_deadline'] ?? false;

        if (! $isWithinDeadline) {
            return redirect()->route('validasi_kelompok.index')
                ->with('error', 'Tidak dapat merevisi rencana pembelajaran di luar tenggat waktu yang ditentukan.!');
        }

        // Ambil ketua kelompok yang sedang login
        $ketuaKelompok = Auth::user()->dataPegawai;

        // Ambil kelompok yang dipimpin oleh ketua
        $kelompok = Kelompok::where('id_ketua', $ketuaKelompok->id)->first();

        if (! $kelompok) {
            return redirect()->back()->with('error', 'Anda tidak memiliki kelompok.');
        }

        // Simpan validasi
        $validasi = KelompokCanValidating::updateOrCreate([
            'rencana_pembelajaran_id' => $rencana->id,
            'kelompok_id'             => $kelompok->id,
        ], [
            'status'        => 'direvisi',
            'status_revisi' => "belum_direvisi",
        ]);

        $newRevisionDueDate = Carbon::now()->addHours(72); // Contoh: Tenggat 48 jam
        $rencana->update([
            'revisi_due_date' => $newRevisionDueDate,
        ]);

        // Simpan catatan validasi kelompok
        CatatanValidasiKelompok::create([
            'kelompok_can_validating_id' => $validasi->id,
            'catatan'                    => $request->catatan,
        ]);

        return redirect()->route('validasi_kelompok.index')
            ->with('warning', 'Revisi rencana berhasil dikirim ke pegawai!');
    }

    public function setujuiDariRevisi(Request $request, $id)
    {
        // Cari data validasi berdasarkan ID rencana
        $validasi = KelompokCanValidating::where('rencana_pembelajaran_id', $id)->first();
        $rencana  = RencanaPembelajaran::findOrFail($id); // Ambil rencana untuk di-update

        // Cek tenggat waktu
        $deadlineInfo     = $this->deadlineService->getDeadlineInfo('validasi_kelompok');
        $isWithinDeadline = $deadlineInfo['is_within_deadline'] ?? false;

        if (! $isWithinDeadline) {
            return redirect()->route('validasi_kelompok.index')
                ->with('error', 'Tidak dapat menyetujui rencana pembelajaran di luar tenggat waktu yang ditentukan!');
        }

        if (! $validasi) {
            return redirect()->back()
                ->with('error', 'Data validasi tidak ditemukan!');
        }

        // Cek apakah status_revisi sudah "sudah_direvisi"
        if ($validasi->status_revisi !== 'sudah_direvisi') {
            return redirect()->back()
                ->with('error', 'Revisi belum selesai, silakan minta revisi kepada pegawai terkait!');
        }

        // Update status menjadi "disetujui" dan hapus status revisi
        $validasi->update([
            'status'        => 'disetujui',
            'status_revisi' => 'disetujui',
        ]);

        $rencana->update(['revisi_due_date' => null]);

        // Hapus catatan validasi sebelumnya
        CatatanValidasiKelompok::where('kelompok_can_validating_id', $validasi->id)->delete();

        // Jika pengguna memasukkan catatan baru, simpan catatan tersebut
        if ($request->filled('catatan')) {
            CatatanValidasiKelompok::create([
                'kelompok_can_validating_id' => $validasi->id,
                'catatan'                    => $request->catatan,
            ]);
        }

        return redirect()->route('validasi_kelompok.index')
            ->with('success', 'Rencana berhasil disetujui!');
    }

    public function tambahRevisi(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'catatan' => 'required|string', // Pastikan catatan tidak kosong
        ]);

        // Cari rencana pembelajaran berdasarkan ID
        $rencana = RencanaPembelajaran::findOrFail($id);

        // Cek tenggat waktu
        $deadlineInfo     = $this->deadlineService->getDeadlineInfo('validasi_kelompok');
        $isWithinDeadline = $deadlineInfo['is_within_deadline'] ?? false;

        if (! $isWithinDeadline) {
            return redirect()->route('validasi_kelompok.index')
                ->with('error', 'Tidak dapat menambah revisi rencana pembelajaran di luar tenggat waktu yang ditentukan!');
        }

        // Ambil ketua kelompok yang sedang login
        $ketuaKelompok = Auth::user()->dataPegawai;

        // Ambil kelompok yang dipimpin oleh ketua
        $kelompok = Kelompok::where('id_ketua', $ketuaKelompok->id)->first();

        if (! $kelompok) {
            return redirect()->back()->with('error', 'Anda tidak memiliki kelompok.');
        }

        // Cari verifikasi terakhir untuk rencana pembelajaran ini
        $verifikasiTerakhir = KelompokCanValidating::where('rencana_pembelajaran_id', $rencana->id)
            ->where('kelompok_id', $kelompok->id)
            ->latest() // Ambil verifikasi terbaru
            ->first();

        // Jika verifikasi terakhir tidak ditemukan, kembalikan error
        if (! $verifikasiTerakhir) {
            return redirect()->back()->with('error', 'Verifikasi tidak ditemukan.');
        }

        // Update status verifikasi menjadi "direvisi"
        $verifikasiTerakhir->update([
            'status'        => 'direvisi',
            'status_revisi' => 'belum_direvisi', // Set status revisi ke "belum_direvisi"
        ]);

        $newRevisionDueDate = Carbon::now()->addHours(72); // Contoh: Tenggat 48 jam
        $rencana->update([
            'revisi_due_date' => $newRevisionDueDate,
        ]);
        // ===============================================

        // Tambahkan catatan revisi baru ke tabel CatatanVerifikasiKelompok
        CatatanValidasiKelompok::create([
            'kelompok_can_validating_id' => $verifikasiTerakhir->id,
            'catatan'                    => $request->catatan,
        ]);

        return redirect()->route('validasi_kelompok.index')
            ->with('warning', 'Revisi berhasil ditambahkan!');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // 1. Ambil data validasi beserta relasi berjenjang (Unit Kerja & Universitas)
        $validasi = KelompokCanValidating::with([
            'rencanaPembelajaran.unitKerjaCanverifying',
            'rencanaPembelajaran.universitasCanApproving',
        ])->find($id);

        if (! $validasi) {
            return redirect()->route('validasi_kelompok.index')->with('error', 'Data rencana tidak ditemukan!');
        }

        // 2. Proteksi Alur: Cek apakah sudah diproses tahap di atasnya (Unit Kerja atau Universitas)
        $rencana = $validasi->rencanaPembelajaran;
        if ($rencana && ($rencana->unitKerjaCanverifying || $rencana->universitasCanApproving)) {
            $tahap = $rencana->universitasCanApproving ? 'Universitas' : 'Unit Kerja';
            return redirect()->route('validasi_kelompok.index')
                ->with('error', "Persetujuan tidak dapat dibatalkan karena sudah diproses oleh $tahap!");
        }

        // 3. Proteksi Waktu: Cek tenggat waktu melalui DeadlineService
        $deadlineInfo     = $this->deadlineService->getDeadlineInfo('validasi_kelompok');
        $isWithinDeadline = $deadlineInfo['is_within_deadline'] ?? false;

        if (! $isWithinDeadline) {
            return redirect()->route('validasi_kelompok.index')
                ->with('error', 'Masa validasi kelompok sudah berakhir, akses pembatalan ditutup!');
        }

        // 4. Proses penghapusan manual tanpa DB Transaction
        // Simpan status untuk pesan sukses
        $statusLama = $validasi->status;

        // Hapus catatan terlebih dahulu untuk menjaga integritas (Foreign Key)
        CatatanValidasiKelompok::where('kelompok_can_validating_id', $validasi->id)->delete();

        // Hapus data validasi utama
        $validasi->delete();

        $message = ($statusLama == 'disetujui') ? 'Persetujuan rencana dibatalkan!' : 'Revisi rencana dibatalkan!';

        return redirect()->route('validasi_kelompok.index')->with('success', $message);
    }
}
