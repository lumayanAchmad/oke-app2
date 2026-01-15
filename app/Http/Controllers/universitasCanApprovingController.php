<?php
namespace App\Http\Controllers;

use App\Models\RencanaPembelajaran;
use App\Models\UnitKerja;
use App\Models\UniversitasCanApproving;
use App\Services\DeadlineService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class universitasCanApprovingController extends Controller
{
    protected $deadlineService;

    public function __construct(DeadlineService $deadlineService)
    {
        $this->middleware('can:approver');
        $this->deadlineService = $deadlineService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(DeadlineService $deadlineService)
    {
        $user = Auth::user();

        // Validasi dasar
        if (! $user->dataPegawai) {
            return redirect()->back()->with('error', 'Anda tidak terdaftar sebagai pegawai.');
        }

        // Dapatkan informasi deadline untuk verifikasi universitas
        $deadlineInfo     = $deadlineService->getDeadlineInfo('approval_universitas');
        $isWithinDeadline = $deadlineInfo['is_within_deadline'] ?? false;
        $startDate        = $deadlineInfo['start_date'] ?? null;
        $endDate          = $deadlineInfo['end_date'] ?? null;
        $isDeadlineSet    = $deadlineInfo['is_set'] ?? false;

        // Hitung status tenggat
        $daysUntilStart  = $startDate ? now()->diffInDays($startDate, false) : null;
        $isNotStartedYet = $startDate && $daysUntilStart > 0;

        // Ambil semua unit kerja yang memiliki rencana yang sudah disetujui oleh unit kerja
        $unitKerjas = UnitKerja::with([
            'dataPegawai.rencanaPembelajaran' => function ($query) {
                $query->whereHas('unitKerjaCanVerifying', fn($q) => $q->where('status', 'disetujui'))
                    ->with([
                        'dataPelatihan',
                        'dataPendidikan',
                        'bentukJalur',
                        'region',
                        'jenjang',
                        'unitKerjaCanVerifying',
                        'kelompokCanValidating.catatanValidasiKelompok',
                        'universitasCanApproving',
                        'dataPegawai.kelompok.ketua',
                    ]);
            },
            'dataPegawai'                     => function ($query) {
                $query->whereHas('rencanaPembelajaran', function ($q) {
                    $q->whereHas('unitKerjaCanVerifying', fn($q) => $q->where('status', 'disetujui'));
                });
            },
        ])->whereHas('dataPegawai.rencanaPembelajaran', function ($query) {
            $query->whereHas('unitKerjaCanVerifying', fn($q) => $q->where('status', 'disetujui'));
        })->get();

        // Format data per unit kerja
        $unitKerjasData = $unitKerjas->map(function ($unitKerja) {
            $rencanaDariUnit = $unitKerja->dataPegawai->flatMap->rencanaPembelajaran
                ->filter(fn($r) => optional($r->unitKerjaCanVerifying)->status === 'disetujui');

            // Di dalam method index(), ubah bagian return array menjadi:
            return [
                'unit_kerja'            => $unitKerja,
                'rencanaDisetujui'      => $rencanaDariUnit
                    ->filter(fn($r) => optional($r->universitasCanApproving)->status === 'disetujui'),
                'rencanaDitolak'        => $rencanaDariUnit                                      // UBAH INI dari 'rencanaDirevisi'
                    ->filter(fn($r) => optional($r->universitasCanApproving)->status === 'ditolak'), // UBAH INI dari 'direvisi'
                'rencanaBelumDiapprove' => $rencanaDariUnit
                    ->filter(fn($r) => ! optional($r->universitasCanApproving)->status),
                'totalRencana'          => $rencanaDariUnit->count(),
            ];
        })->filter(fn($data) => $data['totalRencana'] > 0); // Hanya tampilkan unit kerja yang memiliki rencana

        return view('approval_index', [
            'unitKerjasData'   => $unitKerjasData,
            'namaPegawai'      => $user->dataPegawai->nama,
            'isWithinDeadline' => $isWithinDeadline,
            'isNotStartedYet'  => $isNotStartedYet,
            'isDeadlineSet'    => $isDeadlineSet,
            'startDate'        => $startDate,
            'endDate'          => $endDate,
            'daysUntilStart'   => $daysUntilStart,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function approve(RencanaPembelajaran $rencana)
    {
        // Validasi deadline
        $deadlineInfo = $this->deadlineService->getDeadlineInfo('approval_universitas');
        if (! $deadlineInfo['is_within_deadline']) {
            return redirect()->back()->with('error', 'Tidak dalam periode approval universitas.');
        }

        // Update status approval universitas
        $rencana->universitasCanApproving()->updateOrCreate(
            ['rencana_pembelajaran_id' => $rencana->id],
            [
                'data_pegawai_id' => $rencana->data_pegawai_id,
                'status'          => 'disetujui',
                'catatan'         => request('catatan', ''),
            ]
        );

        return redirect()->back()->with('success', 'Rencana pembelajaran berhasil disetujui.');
    }

    public function reject(RencanaPembelajaran $rencana)
    {
        // Validasi deadline
        $deadlineInfo = $this->deadlineService->getDeadlineInfo('approval_universitas');
        if (! $deadlineInfo['is_within_deadline']) {
            return redirect()->back()->with('error', 'Tidak dalam periode approval universitas.');
        }

        // Validasi catatan untuk penolakan
        request()->validate([
            'catatan' => 'required|string|max:1000',
        ]);

        // Update status approval universitas
        $rencana->universitasCanApproving()->updateOrCreate(
            ['rencana_pembelajaran_id' => $rencana->id],
            [
                'data_pegawai_id' => $rencana->data_pegawai_id,
                'status'          => 'ditolak',
                'catatan'         => request('catatan'),
            ]
        );

        return redirect()->back()->with('success', 'Rencana pembelajaran ditolak.');
    }

    public function approveMassal(Request $request)
    {
        // Validasi deadline
        $deadlineInfo = $this->deadlineService->getDeadlineInfo('approval_universitas');
        if (! $deadlineInfo['is_within_deadline']) {
            return redirect()->back()->with('error', 'Tidak dalam periode approval universitas.');
        }

        $request->validate([
            'rencana_ids'   => 'required|array',
            'rencana_ids.*' => 'exists:rencana_pembelajarans,id',
            'catatan'       => 'nullable|string|max:1000',
        ]);

        $rencanaIds = $request->rencana_ids;
        $catatan    = $request->catatan ?? '';

        foreach ($rencanaIds as $rencanaId) {
            $rencana = RencanaPembelajaran::find($rencanaId);

            $rencana->universitasCanApproving()->updateOrCreate(
                ['rencana_pembelajaran_id' => $rencana->id],
                [
                    'data_pegawai_id' => $rencana->data_pegawai_id,
                    'status'          => 'disetujui',
                    'catatan'         => $catatan,
                ]
            );
        }

        return redirect()->back()->with('success', count($rencanaIds) . ' rencana pembelajaran berhasil disetujui.');
    }

    public function rejectMassal(Request $request)
    {
        // Validasi deadline
        $deadlineInfo = $this->deadlineService->getDeadlineInfo('approval_universitas');
        if (! $deadlineInfo['is_within_deadline']) {
            return redirect()->back()->with('error', 'Tidak dalam periode approval universitas.');
        }

        $request->validate([
            'rencana_ids'   => 'required|array',
            'rencana_ids.*' => 'exists:rencana_pembelajarans,id',
            'catatan'       => 'required|string|max:1000', // Catatan wajib untuk penolakan
        ]);

        $rencanaIds = $request->rencana_ids;
        $catatan    = $request->catatan;

        foreach ($rencanaIds as $rencanaId) {
            $rencana = RencanaPembelajaran::find($rencanaId);

            $rencana->universitasCanApproving()->updateOrCreate(
                ['rencana_pembelajaran_id' => $rencana->id],
                [
                    'data_pegawai_id' => $rencana->data_pegawai_id,
                    'status'          => 'ditolak',
                    'catatan'         => $catatan,
                ]
            );
        }

        return redirect()->back()->with('success', count($rencanaIds) . ' rencana pembelajaran berhasil ditolak.');
    }

    public function destroy($id)
    {
        $approval = universitasCanApproving::find($id);

        if (! $approval) {
            return redirect()->route('approval.index')
                ->with('error', 'Data approval tidak ditemukan!');
        }

        // Cek tenggat waktu
        $deadlineInfo     = $this->deadlineService->getDeadlineInfo('approval_universitas');
        $isWithinDeadline = $deadlineInfo['is_within_deadline'] ?? false;

        if (! $isWithinDeadline) {
            return redirect()->route('approval.index')
                ->with('error', 'Tidak dapat membatalkan approval di luar tenggat waktu yang ditentukan!');
        }

        // Simpan status sebelum dihapus untuk menentukan pesan
        $status = $approval->status;

        // Hapus data approval
        $approval->delete();

        // Tentukan pesan berdasarkan status sebelumnya
        if ($status == 'disetujui') {
            return redirect()->route('approval.index')
                ->with('success', 'Persetujuan rencana berhasil dibatalkan!');
        } elseif ($status == 'ditolak') {
            return redirect()->route('approval.index')
                ->with('success', 'Penolakan rencana berhasil dibatalkan!');
        } else {
            return redirect()->route('approval.index')
                ->with('success', 'Status approval berhasil dihapus!');
        }
    }
}
