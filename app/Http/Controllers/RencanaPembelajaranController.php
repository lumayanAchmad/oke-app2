<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreRencanaPembelajaranRequest;
use App\Http\Requests\UpdateRencanaPembelajaranRequest;
use App\Models\AnggaranPelatihan;
use App\Models\AnggaranPendidikan;
use App\Models\BentukJalur;
use App\Models\DataPegawai;
use App\Models\DataPelatihan;
use App\Models\DataPendidikan;
use App\Models\JenisPendidikan;
use App\Models\Jenjang;
use App\Models\Kategori;
use App\Models\Kelompok;
use App\Models\KelompokCanValidating;
use App\Models\Region;
use App\Models\RencanaPembelajaran;
use App\Models\Rumpun;
use App\Services\DeadlineService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RencanaPembelajaranController extends Controller
{
    protected $deadlineService;

    public function __construct(DeadlineService $deadlineService)
    {
        $this->deadlineService = $deadlineService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        if (! $user->dataPegawai) {
            return redirect()->back()->with('error', 'Anda tidak memiliki data pegawai.');
        }

        // Dapatkan informasi deadline
        $deadlineInfo     = $this->deadlineService->getDeadlineInfo('perencanaan_pegawai');
        $isWithinDeadline = $deadlineInfo['is_within_deadline'] ?? false;
        $startDate        = $deadlineInfo['start_date'] ?? null;
        $endDate          = $deadlineInfo['end_date'] ?? null;
        $isDeadlineSet    = $deadlineInfo['is_set'] ?? false;

        // Hitung status tenggat
        $daysUntilStart  = $startDate ? now()->diffInDays($startDate, false) : null;
        $isNotStartedYet = $startDate && $daysUntilStart > 0;

        $dataPegawai          = $user->dataPegawai;
        $rencana_pembelajaran = $dataPegawai->rencanaPembelajaran()
            ->with(['bentukJalur', 'jenisPendidikan', 'region', 'dataPelatihan', 'dataPendidikan', 'kelompokCanValidating'])
            ->latest()
            ->get();

        $notifikasi = [
            'disetujui' => $rencana_pembelajaran->where('kelompokCanValidating.status', 'disetujui')->count(),
            'direvisi'  => $rencana_pembelajaran->where('kelompokCanValidating.status_revisi', 'belum_direvisi')->count(),
        ];

        return view('rencana_pembelajaran_index', compact(
            'rencana_pembelajaran',
            'dataPegawai',
            'notifikasi',
            'isWithinDeadline',
            'startDate',
            'endDate',
            'isDeadlineSet',
            'isNotStartedYet',
            'daysUntilStart'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $rencana_pembelajaran = RencanaPembelajaran::get();
        $kategori             = Kategori::get();
        $region               = Region::all();

        // Ambil data pegawai yang sedang login
        $user    = Auth::user();
        $pegawai = $user->dataPegawai;

        // Cek tenggat waktu
        $deadlineInfo     = $this->deadlineService->getDeadlineInfo('perencanaan_pegawai');
        $isWithinDeadline = $deadlineInfo['is_within_deadline'] ?? false;

        // Cek apakah pegawai sudah memiliki kelompok
        if (! $pegawai || ! $pegawai->kelompok_id) {
            return redirect()->back()
                ->with('error', 'Anda belum dimasukkan ke dalam kelompok. Silakan hubungi administrator!');
        }

        if (! $isWithinDeadline) {
            return redirect()->route('rencana_pembelajaran.index')
                ->with('error', 'Tidak dapat menambahkan rencana pembelajaran di luar tenggat waktu yang ditentukan!');
        }

        return view('rencana_pembelajaran_create', compact('rencana_pembelajaran', 'kategori', 'region'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRencanaPembelajaranRequest $request)
    {
        $user    = Auth::user();
        $pegawai = $user->dataPegawai;

        // Cek apakah pegawai sudah memiliki kelompok
        if (! $pegawai || ! $pegawai->kelompok_id) {
            return redirect()->back()
                ->with('error', 'Tanda belum dimasukkan ke dalam kelompok. Silakan hubungi administrator!');
        }

        // Cek tenggat waktu
        $deadlineInfo     = $this->deadlineService->getDeadlineInfo('perencanaan_pegawai');
        $isWithinDeadline = $deadlineInfo['is_within_deadline'] ?? false;

        if (! $isWithinDeadline) {
            return redirect()->route('rencana_pembelajaran.index')
                ->with('error', 'Tidak dapat menambahkan rencana pembelajaran di luar tenggat waktu yang ditentukan!');
        }

        // Ambil data pegawai terkait user
        $pegawai = DataPegawai::where('user_id', Auth::id())->first();
        if (! $pegawai) {
            return redirect()->back()->withErrors(['error' => 'Data pegawai tidak ditemukan.']);
        }

        // Simpan rencana pembelajaran
        $validated = $request->validated();

        $rencana                   = new RencanaPembelajaran();
        $rencana->data_pegawai_id  = $pegawai->id;
        $rencana->tahun            = $validated['tahun'];
        $rencana->jam_pelajaran    = $validated['jam_pelajaran'];
        $rencana->klasifikasi      = $validated['klasifikasi'];
        $rencana->region_id        = $validated['regional'];
        $rencana->anggaran_rencana = $validated['anggaran_rencana'];
        $rencana->prioritas        = $validated['prioritas'];

        // Cek klasifikasi dan simpan data yang relevan
        if ($validated['klasifikasi'] == 'pendidikan') {
            // Tangani klasifikasi 'pendidikan'
            $rencana->jenjang_id = $validated['jenjang'];
            $dataPendidikan      = DataPendidikan::where('id', $validated['jurusan'])
                ->whereHas('jenjangs', function ($query) use ($validated) {
                    $query->where('jenjangs.id', $validated['jenjang']);
                })
                ->first();

            if ($dataPendidikan) {
                $rencana->data_pendidikan_id  = $dataPendidikan->id;
                $rencana->jenis_pendidikan_id = $validated['jenis_pendidikan'];
            } else {
                return redirect()->back()->withErrors(['error' => 'Data pendidikan tidak ditemukan.']);
            }
        } elseif ($validated['klasifikasi'] == 'pelatihan') {
            // Tangani klasifikasi 'pelatihan'
            $dataPelatihan = DataPelatihan::find($validated['nama_pelatihan']);
            if ($dataPelatihan) {
                $rencana->data_pelatihan_id = $dataPelatihan->id;
                $rencana->bentuk_jalur_id   = $validated['bentuk_jalur']; // Pastikan 'bentuk_jalur' ada dalam request
            } else {
                return redirect()->back()->withErrors(['error' => 'Data pelatihan tidak ditemukan.']);
            }
        }

        $rencana->save();

        return redirect()->route('rencana_pembelajaran.index')
            ->with('success', 'Rencana pembelajaran berhasil dibuat!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RencanaPembelajaran $rencanaPembelajaran)
    {
        // Cek Autentikasi dan Data Pegawai
        if (! Auth::check()) {
            return redirect()->route('login')->withErrors(['error' => 'Anda harus login terlebih dahulu untuk mengupdate rencana pembelajaran.']);
        }

        $user    = Auth::user();
        $pegawai = $user->dataPegawai;

        if (! $pegawai || ! $pegawai->kelompok_id) {
            return redirect()->back()
                ->with('error', 'Anda belum dimasukkan ke dalam kelompok. Silakan hubungi administrator!');
        }

        // 1. Cek Tenggat Global (Perencanaan Pegawai - untuk submit pertama)
        $deadlineInfo           = $this->deadlineService->getDeadlineInfo('perencanaan_pegawai');
        $isWithinGlobalDeadline = $deadlineInfo['is_within_deadline'] ?? false;

        // 2. Cek Tenggat Revisi Kustom (revisi_due_date)
        $isUnderCustomRevision    = false;
        $isCurrentlyUnderRevision = false;

        // Tentukan apakah rencana sedang direvisi oleh Kelompok ATAU Unit Kerja
        $isUnderKelompokRevision = $rencanaPembelajaran->kelompokCanValidating
        && $rencanaPembelajaran->kelompokCanValidating->status == 'direvisi';

        $isUnderUnitKerjaRevision = $rencanaPembelajaran->unitKerjaCanVerifying
        && $rencanaPembelajaran->unitKerjaCanVerifying->status == 'direvisi';

        if ($isUnderKelompokRevision || $isUnderUnitKerjaRevision) {
            $isCurrentlyUnderRevision = true;

            // Cek apakah custom revision deadline-nya masih berlaku
            if ($rencanaPembelajaran->revisi_due_date) {
                $revisiDueDate = Carbon::parse($rencanaPembelajaran->revisi_due_date);

                if (now()->lessThan($revisiDueDate)) {
                    // Sedang direvisi DAN tenggat revisi kustom masih berlaku
                    $isUnderCustomRevision = true;
                } else {
                    // Direvisi, tapi tenggat revisi kustom sudah habis. Blokir!
                    return redirect()->route('rencana_pembelajaran.index')
                        ->with('error', 'Tenggat waktu revisi telah berakhir!');
                }
            }
        }

        // Syarat Boleh Edit:
        // a. Masih dalam Tenggat Global (saat submit pertama), ATAU
        // b. Sedang dalam Status "direvisi" dan Tenggat Revisi Khusus (revisi_due_date) masih berlaku.
        $canEdit = $isWithinGlobalDeadline || $isUnderCustomRevision;

        if (! $canEdit) {
            // Blokir jika di luar kedua tenggat tersebut
            return redirect()->route('rencana_pembelajaran.index')
                ->with('error', 'Tidak dapat mengedit rencana pembelajaran di luar tenggat waktu yang ditentukan!');
        }

        // Blokir jika sudah disetujui (dan belum dikembalikan revisi oleh level yang lebih tinggi)
        if ($rencanaPembelajaran->kelompokCanValidating && $rencanaPembelajaran->kelompokCanValidating->status == 'disetujui'
            && ($rencanaPembelajaran->unitKerjaCanVerifying == null || $rencanaPembelajaran->unitKerjaCanVerifying->status != 'direvisi')) {

            return redirect()->back()
                ->with('error', 'Rencana pembelajaran ini telah disetujui dan tidak dapat diedit!');
        }

        // Blokir jika revisi terakhir sudah selesai dan tidak ada permintaan revisi baru
        if ($rencanaPembelajaran->kelompokCanValidating && $rencanaPembelajaran->kelompokCanValidating->status_revisi == 'sudah_direvisi'
            && ($rencanaPembelajaran->unitKerjaCanVerifying == null || $rencanaPembelajaran->unitKerjaCanVerifying->status_revisi != 'direvisi')) {

            return redirect()->back()
                ->with('error', 'Revisi sudah selesai dan tidak dapat diubah lagi!');
        }

        $rencana     = RencanaPembelajaran::findOrFail($rencanaPembelajaran->id);
        $kategori    = Kategori::get();
        $region      = Region::all();
        $bentukJalur = BentukJalur::where('kategori_id', $rencana->bentukJalur->kategori_id)->get();

        // Ambil rumpun yang sebelumnya dipilih berdasarkan data_pelatihan_id
        $rumpunTerpilih = $rencana->data_pelatihan_id
            ? DataPelatihan::where('id', $rencana->data_pelatihan_id)->value('rumpun_id')
            : null;

        // Ambil semua rumpun untuk dropdown
        $rumpun = Rumpun::all();

        // Ambil daftar nama pelatihan berdasarkan rumpun yang sebelumnya dipilih
        $pelatihan = $rumpunTerpilih
            ? DataPelatihan::where('rumpun_id', $rumpunTerpilih)->get()
            : collect(); // Jika belum ada rumpun, kosongkan

        // Nama Pelatihan yang sebelumnya dipilih
        $pelatihanTerpilih = DataPelatihan::find($rencana->data_pelatihan_id);

        // Ambil anggaran untuk pelatihan yang dipilih
        $anggaran = $pelatihanTerpilih
            ? AnggaranPelatihan::where('data_pelatihan_id', $pelatihanTerpilih->id)
            ->with('region', 'kategori') // Memuat relasi region dan kategori
            ->get()
            : collect(); // Jika tidak ada pelatihan terpilih, kosongkan anggaran

        // Kelompokkan anggaran berdasarkan region
        $anggaranGrouped = $anggaran->groupBy(function ($item) {
            return $item->region->region; // Kelompokkan berdasarkan nama region
        });

        // Ambil anggaran pendidikan (jika rencana bukan pelatihan)
        $anggaranPendidikan = $rencana->klasifikasi == 'pendidikan'
            ? AnggaranPendidikan::where('jenjang_id', $rencana->jenjang_id)
            ->with('region') // Memuat relasi region
            ->get()
            : collect();

        // Ambil semua jenjang
        $jenjang         = Jenjang::all();
        $jenjangTerpilih = $rencana->jenjang_id;

        // Ambil semua jurusan
        $jurusan         = DataPendidikan::all();
        $jurusanTerpilih = $rencana->data_pendidikan_id;

        // Ambil jenis pendidikan dari entitasnya (asumsi: ada model JenisPendidikan)
        $jenisPendidikan         = JenisPendidikan::all();
        $jenisPendidikanTerpilih = $rencana->jenis_pendidikan_id;

        return view('rencana_pembelajaran_edit', compact(
            'rencana', 'kategori', 'region', 'bentukJalur', 'rumpun', 'rumpunTerpilih', 'pelatihan', 'pelatihanTerpilih', 'anggaranGrouped', 'jenjang', 'jenjangTerpilih', 'jurusan', 'jurusanTerpilih', 'jenisPendidikan', 'jenisPendidikanTerpilih', 'anggaranPendidikan'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRencanaPembelajaranRequest $request, $id)
    {
        // Ambil data pegawai yang sedang login
        $user    = Auth::user();
        $pegawai = $user->dataPegawai;

        // Cek apakah pegawai sudah memiliki kelompok
        if (! $pegawai || ! $pegawai->kelompok_id) {
            return redirect()->back()
                ->with('error', 'Anda belum dimasukkan ke dalam kelompok. Silakan hubungi administrator!');
        }

        // Pastikan user login
        if (! Auth::check()) {
            return redirect()->route('login')->withErrors(['error' => 'Anda harus login terlebih dahulu untuk mengupdate rencana pembelajaran.']);
        }

        // Cek tenggat waktu
        $deadlineInfo     = $this->deadlineService->getDeadlineInfo('perencanaan_pegawai');
        $isWithinDeadline = $deadlineInfo['is_within_deadline'] ?? false;

        if (! $isWithinDeadline) {
            return redirect()->route('rencana_pembelajaran.index')
                ->with('error', 'Tidak dapat menambahkan rencana pembelajaran di luar tenggat waktu yang ditentukan!');
        }

        // Ambil data rencana pembelajaran yang akan diupdate
        $rencana = RencanaPembelajaran::find($id);

        // Pastikan rencana pembelajaran ditemukan
        if (! $rencana) {
            return redirect()->back()->withErrors(['error' => 'Rencana pembelajaran tidak ditemukan.']);
        }

        // Ambil data pegawai terkait user
        $pegawai = DataPegawai::where('user_id', Auth::id())->first();
        if (! $pegawai) {
            return redirect()->back()->withErrors(['error' => 'Data pegawai tidak ditemukan.']);
        }

        // Pastikan rencana pembelajaran milik pegawai
        if ($rencana->data_pegawai_id != $pegawai->id) {
            return redirect()->back()->withErrors(['error' => 'Anda tidak memiliki hak akses untuk mengupdate rencana pembelajaran ini.']);
        }

        // Simpan data lama sebelum diupdate
        $oldData = $rencana->getAttributes();

        // Update rencana pembelajaran
        $validated = $request->validated();

        $rencana->tahun            = $validated['tahun'];
        $rencana->jam_pelajaran    = $validated['jam_pelajaran'];
        $rencana->klasifikasi      = $validated['klasifikasi'];
        $rencana->region_id        = $validated['regional'];
        $rencana->anggaran_rencana = $validated['anggaran_rencana'];
        $rencana->prioritas        = $validated['prioritas'];

        // Cek klasifikasi dan update data yang relevan
        if ($validated['klasifikasi'] == 'pendidikan') {
            // Tangani klasifikasi 'pendidikan'
            $rencana->jenjang_id = $validated['jenjang'];
            $dataPendidikan      = DataPendidikan::where('id', $validated['jurusan'])
                ->whereHas('jenjangs', function ($query) use ($validated) {
                    $query->where('jenjangs.id', $validated['jenjang']);
                })
                ->first();

            if ($dataPendidikan) {
                $rencana->data_pendidikan_id  = $dataPendidikan->id;
                $rencana->jenis_pendidikan_id = $validated['jenis_pendidikan'];
            } else {
                return redirect()->back()->withErrors(['error' => 'Data pendidikan tidak ditemukan.']);
            }
        } elseif ($validated['klasifikasi'] == 'pelatihan') {
            // Tangani klasifikasi 'pelatihan'
            $dataPelatihan = DataPelatihan::find($validated['nama_pelatihan']);
            if ($dataPelatihan) {
                $rencana->data_pelatihan_id = $dataPelatihan->id;
                $rencana->bentuk_jalur_id   = $validated['bentuk_jalur']; // Pastikan 'bentuk_jalur' ada dalam request
            } else {
                return redirect()->back()->withErrors(['error' => 'Data pelatihan tidak ditemukan.']);
            }
        }

        // Simpan data baru setelah diupdate
        $newData = $rencana->getAttributes();

        // Bandingkan data lama dan baru
        if ($oldData == $newData) {
            if ($rencana->kelompokCanValidating) {
                // Jika tidak ada perubahan, kembalikan dengan pesan error
                return redirect()->back()
                    ->with('error', 'Data gagal dierevisi! Anda tidak melakukan pembaruan data.');
            }
            // Jika tidak ada perubahan, kembalikan dengan pesan error
            return redirect()->route('rencana_pembelajaran.index')
                ->with('error', 'Data gagal diedit! Anda tidak melakukan pembaruan data.');
        }

        // Jika ada perubahan, periksa apakah status pengajuan masih draft
        if ($rencana->status_pengajuan !== 'draft') {
            // Cek tahap verifikasi terakhir yang meminta revisi
            if ($rencana->unitKerjaCanVerifying && in_array($rencana->unitKerjaCanVerifying->status_revisi, ['belum_direvisi', 'perlu_revisi_ulang'])) {
                $rencana->unitKerjaCanVerifying->status_revisi = 'sedang_direvisi';
                $rencana->unitKerjaCanVerifying->save();
            } elseif ($rencana->kelompokCanValidating && in_array($rencana->kelompokCanValidating->status_revisi, ['belum_direvisi', 'perlu_revisi_ulang'])) {
                $rencana->kelompokCanValidating->status_revisi = 'sedang_direvisi';
                $rencana->kelompokCanValidating->save();
            }
        }

        $rencana->save();

        return redirect()->route('rencana_pembelajaran.index')
            ->with('success', 'Rencana pembelajaran berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RencanaPembelajaran $rencanaPembelajaran)
    {
        $user    = Auth::user();
        $pegawai = $user->dataPegawai;

        // Cek apakah pegawai sudah memiliki kelompok
        if (! $pegawai || ! $pegawai->kelompok_id) {
            return redirect()->back()
                ->with('error', 'Anda belum dimasukkan ke dalam kelompok. Silakan hubungi administrator!');
        }

        // Cek tenggat waktu
        $deadlineInfo     = $this->deadlineService->getDeadlineInfo('perencanaan_pegawai');
        $isWithinDeadline = $deadlineInfo['is_within_deadline'] ?? false;

        if (! $isWithinDeadline) {
            return redirect()->route('rencana_pembelajaran.index')
                ->with('error', 'Tidak dapat menambahkan rencana pembelajaran di luar tenggat waktu yang ditentukan!');
        }

        if ($rencanaPembelajaran->kelompokCanValidating && $rencanaPembelajaran->kelompokCanValidating->status == 'disetujui') {
            return redirect()->back()
                ->with('error', 'Rencana ini telah disetujui dan tidak dapat dihapus!');
        }
        $rencanaPembelajaran->delete();
        return redirect()->route('rencana_pembelajaran.index')
            ->with('error', 'Rencana pembelajaran berhasil dihapus!');
    }

    // UNTUK MENGAJUKAN VERIFIKASI DAN REVISI
    public function ajukanVerifikasi($id)
    {
        // Pastikan models Kelompok, KelompokCanValidating, dan Auth di-import
        $rencana = RencanaPembelajaran::findOrFail($id);
        $user    = Auth::user();
        $pegawai = $user->dataPegawai;

        // 1. Cek Batas Waktu
        // Menggunakan deadline 'perencanaan_pegawai' sesuai permintaan Anda untuk batas waktu pengajuan
        $deadlineInfo     = $this->deadlineService->getDeadlineInfo('perencanaan_pegawai');
        $isWithinDeadline = $deadlineInfo['is_within_deadline'] ?? false;

        if (! $isWithinDeadline) {
            return redirect()->route('rencana_pembelajaran.index')
                ->with('error', 'Tidak dapat mengajukan rencana pembelajaran di luar tenggat waktu yang ditentukan!');
        }

        // 2. Cek apakah rencana sudah pernah diajukan
        if ($rencana->status_pengajuan === 'draft') {

            // 3. Cek Peran Pegawai (Ketua Kelompok atau Anggota Biasa)
            // Asumsi: id_ketua di tabel kelompok mengacu pada id data_pegawai
            $isKetua = Kelompok::where('id_ketua', $pegawai->id)->exists();

            if ($isKetua) {
                // KASUS A: KETUA KELOMPOK (Bypass Validasi Kelompok)

                // Ini mencatat bahwa tahap Validasi Kelompok telah selesai
                KelompokCanValidating::create([
                    'kelompok_id'             => $pegawai->kelompok_id,
                    'rencana_pembelajaran_id' => $rencana->id,
                    'status'                  => 'disetujui', // Langsung disetujui (sebagai bypass)
                    'status_revisi'           => '',          // Kosong
                ]);

                // 'pending_verifikasi' menunjukkan rencana sudah disetujui di level kelompok dan siap diverifikasi oleh Verifikator.
                $rencana->update(['status_pengajuan' => 'pending_verifikasi']);

                return redirect()->route('rencana_pembelajaran.index')
                    ->with('success', 'Rencana berhasil diajukan dan langsung diteruskan ke Verifikator.');

            } else {
                $rencana->update(['status_pengajuan' => 'diajukan']);

                return redirect()->route('rencana_pembelajaran.index')
                    ->with('success', 'Rencana pembelajaran berhasil diajukan untuk divalidasi oleh Ketua Kelompok.');
            }
        }

        // Jika status bukan draft (sudah pernah diajukan atau sudah diproses)
        return redirect()->back()->with('error', 'Rencana tidak bisa diajukan karena statusnya tidak dalam mode draft.');
    }

    public function kirimRevisi($id)
    {
        $rencana = RencanaPembelajaran::with(['kelompokCanValidating', 'unitKerjaCanVerifying'])
            ->findOrFail($id);

        $tahapRevisiAktif = null;

        if ($rencana->unitKerjaCanVerifying &&
            ($rencana->unitKerjaCanVerifying->status == 'direvisi'
                || $rencana->unitKerjaCanVerifying->status_revisi == 'perlu_revisi_ulang')) {

            $tahapRevisiAktif = 'unit_kerja';

        } elseif ($rencana->kelompokCanValidating &&
            $rencana->kelompokCanValidating->status == 'direvisi') {

            $tahapRevisiAktif = 'kelompok';
        }

        if (! $tahapRevisiAktif) {
            return redirect()->back()
                ->with('error', 'Tidak ada revisi yang sedang dilakukan. Silakan lakukan revisi terlebih dahulu!');
        }

        // cek status revisi
        $statusRevisiSaatIni = $tahapRevisiAktif === 'unit_kerja'
            ? $rencana->unitKerjaCanVerifying->status_revisi
            : $rencana->kelompokCanValidating->status_revisi;

        if ($statusRevisiSaatIni !== 'sedang_direvisi') {
            return redirect()->back()
                ->with('error', 'Anda belum melakukan revisi atau perubahan data.');
        }

        // Update status revisi → menjadi "sudah_direvisi"
        switch ($tahapRevisiAktif) {
            case 'unit_kerja':
                $verifikasiUnitKerja                = $rencana->unitKerjaCanVerifying;
                $verifikasiUnitKerja->status_revisi = 'sudah_direvisi';
                $verifikasiUnitKerja->save();
                break;

            case 'kelompok':
                $rencana->kelompokCanValidating->status_revisi = 'sudah_direvisi';
                $rencana->kelompokCanValidating->save();
                break;
        }

        if ($rencana->revisi_due_date !== null) {
            $rencana->update(['revisi_due_date' => null]);
        }

        return redirect()->back()
            ->with('success', 'Rencana pembelajaran berhasil dikirim!');
    }

    // KEBUTUHAN FORM RENCANA
    public function getKategori()
    {
        $kategori = Kategori::all();
        return response()->json($kategori);
    }

    public function getBentukJalur($kategori_id)
    {
        // Ambil bentuk jalur yang sesuai dengan kategori_id
        $bentuk_jalur = BentukJalur::where('kategori_id', $kategori_id)->get();

        // Kembalikan bentuk jalur dalam format JSON
        return response()->json([
            'bentuk_jalur' => $bentuk_jalur,
        ]);
    }

    public function getJenjang()
    {
        $jenjang = Jenjang::all(); // Ganti sesuai query data jenjang Anda
        return response()->json(['jenjang' => $jenjang]);
    }

    public function getJurusanByJenjang(Request $request)
    {
        // Cek jika ada parameter jenjang_id
        $jenjangId = $request->get('jenjang_id');

        // Ambil jurusan yang terkait dengan jenjang yang dipilih
        $dataPendidikans = DataPendidikan::whereHas('jenjangs', function ($query) use ($jenjangId) {
            $query->where('jenjang_id', $jenjangId);
        })->get();

        // Ambil jurusan dari data pendidikan yang ditemukan
        $jurusans = $dataPendidikans->map(function ($dataPendidikan) {
            return [
                'id'      => $dataPendidikan->id,
                'jurusan' => $dataPendidikan->jurusan,
            ];
        });

        return response()->json($jurusans);
    }

    public function getRumpun()
    {
        $rumpuns = Rumpun::all();
        return response()->json($rumpuns);
    }

    public function getNamaPelatihan($rumpunId)
    {
        $pelatihan = DataPelatihan::where('rumpun_id', $rumpunId)->get();
        return response()->json($pelatihan);
    }

    public function getJenisPendidikan()
    {
        $jenisPendidikan = JenisPendidikan::all();
        return response()->json($jenisPendidikan);
    }

    public function getPelatihanInfo($id)
    {
        $pelatihan = DataPelatihan::find($id);

        if ($pelatihan) {
            return response()->json([
                'kode'           => $pelatihan->kode,
                'nama_pelatihan' => $pelatihan->nama_pelatihan,
                'deskripsi'      => $pelatihan->deskripsi,
                'jp'             => $pelatihan->jp,
            ]);
        } else {
            return response()->json([
                'error' => 'Pelatihan tidak ditemukan.',
            ], 404);
        }
    }

    public function getAnggaranByPendidikan(Request $request)
    {
        $jenjangId = $request->input('jenjang_id');

        // Ambil anggaran berdasarkan jenjang dan region
        $anggaranPendidikan = AnggaranPendidikan::with(['region'])
            ->where('jenjang_id', $jenjangId) // Filter berdasarkan jenjang_id
            ->get();

        // Menyediakan data anggaran dengan informasi region
        return response()->json($anggaranPendidikan);
    }

    public function getAnggaranByPelatihan(Request $request)
    {
        $pelatihanId = $request->pelatihan_id;

                                                                             // Ambil data anggaran berdasarkan pelatihan_id dengan relasi ke kategori dan region
        $anggaranPelatihan = AnggaranPelatihan::with(['kategori', 'region']) // Tambahkan 'region' ke dalam with()
            ->where('data_pelatihan_id', $pelatihanId)
            ->get();

        return response()->json($anggaranPelatihan);
    }

    public function validasiAnggaran(Request $request)
    {
        $request->validate([
            'klasifikasi'      => 'required|in:pendidikan,pelatihan',
            'regional'         => 'required|exists:regions,id',
            'anggaran_rencana' => 'required|integer',
        ]);

        $anggaran = null;

        if ($request->klasifikasi === 'pendidikan') {
            $anggaran = DB::table('anggaran_pendidikans')
                ->where('jenjang_id', $request->jenjang)
                ->where('region_id', $request->regional)
                ->first();
        } else {
            $anggaran = DB::table('anggaran_pelatihans')
                ->where('data_pelatihan_id', $request->nama_pelatihan)
                ->where('kategori_id', $request->kategori)
                ->where('region_id', $request->regional)
                ->first();
        }

        if (! $anggaran) {
            return response()->json([
                'status'  => 'invalid',
                'message' => 'Data anggaran tidak ditemukan untuk pilihan yang dipilih.',
            ]);
        }

        $anggaran_rencana = (int) $request->anggaran_rencana;

        if ($anggaran_rencana < $anggaran->anggaran_min || $anggaran_rencana > $anggaran->anggaran_maks) {
            return response()->json([
                'status'  => 'invalid',
                'message' => "Anggaran harus antara Rp " . number_format($anggaran->anggaran_min, 0, ',', '.') . " - Rp " . number_format($anggaran->anggaran_maks, 0, ',', '.'),
            ]);
        }

        return response()->json([
            'status' => 'valid',
        ]);
    }
}
