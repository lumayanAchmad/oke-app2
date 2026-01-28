<?php
namespace App\Http\Controllers;

use App\Models\DataPegawai;
use App\Models\KategoriTenggat;
use App\Models\KelompokCanValidating;
use App\Models\RencanaPembelajaran;
use App\Models\TenggatRencana;
use App\Models\UnitKerjaCanVerifying;
use App\Models\UniversitasCanApproving;
use App\Models\User;
use Carbon\Carbon;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfilController extends Controller
{
    public function show()
    {
        // Ambil user yang sedang login
        $user  = Auth::user();
        $roles = $user->roles()->pluck('role')->toArray();

        $tenggatRencana = TenggatRencana::all();

        // Ambil data pegawai terkait user
        $dataPegawai = $user->dataPegawai;

        // Inisialisasi variabel statistik
        $rencanaPembelajaran          = null;
        $progresValidasi              = null;
        $progresVerifikasi            = null;
        $progresApproval              = null;
        $statistikValidasiKetua       = null;
        $statistikVerifikasiUnit      = null;
        $statistikApprovalUniversitas = null;

        // Ambil pelatihan yang terkait dengan pegawai dan grupkan berdasarkan tahun
        if ($dataPegawai) {
            $rencanaPembelajaran = $dataPegawai->rencanaPembelajaran()
                ->selectRaw('tahun, SUM(jam_pelajaran) as total_jam_pelajaran')
                ->groupBy('tahun')
                ->orderBy('tahun', 'asc')
                ->get();

            // Data untuk statistik progres verifikasi
            $progresValidasi   = $this->getProgresValidasi($dataPegawai);
            $progresVerifikasi = $this->getProgresVerifikasi($dataPegawai);
            $progresApproval   = $this->getProgresApproval($dataPegawai);

            // Data untuk statistik khusus role
            if (in_array('ketua_kelompok', $roles)) {
                $statistikValidasiKetua = $this->getStatistikValidasiKetua($dataPegawai);
            }

            if (in_array('verifikator', $roles)) {
                $statistikVerifikasiUnit = $this->getStatistikVerifikasiUnitKerja($dataPegawai);
            }
        }

        if (in_array('approver', $roles) || in_array('pimpinan', $roles)) {
            $statistikApprovalUniversitas = $this->getStatistikApprovalUniversitas();
        }

        $jadwalPerencanaan = $this->getJadwalPerencanaan();
        $jadwalValidasi    = $this->getjadwalValidasi();
        $jadwalVerifikasi  = $this->getjadwalVerifikasi();
        $jadwalApproval    = $this->getjadwalApproval();

        return view('profil', compact(
            'user',
            'dataPegawai',
            'rencanaPembelajaran',
            'tenggatRencana',
            'jadwalPerencanaan',
            'jadwalValidasi',
            'jadwalVerifikasi',
            'jadwalApproval',
            'roles',
            'progresValidasi',
            'progresVerifikasi',
            'progresApproval',
            'statistikValidasiKetua',
            'statistikVerifikasiUnit',
            'statistikApprovalUniversitas'
        ));
    }

    public function simpanNomorWhatsApp(Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'whatsapp_number' => 'required|digits_between:9,13|numeric',
            ]);

            // Ambil user yang sedang login
            $user = Auth::user();

            // Cek apakah user memiliki data pegawai
            if (! $user->dataPegawai) {
                return redirect()->back()->with('error', 'Data pegawai tidak ditemukan.');
            }

            // Format nomor: hapus awalan 0 jika ada, pastikan hanya angka
            $whatsappNumber = preg_replace('/^0+/', '', $request->whatsapp_number);
            $whatsappNumber = preg_replace('/[^0-9]/', '', $whatsappNumber);

            // Validasi panjang nomor setelah diformat
            if (strlen($whatsappNumber) < 9 || strlen($whatsappNumber) > 13) {
                return redirect()->back()->with('error', 'Nomor WhatsApp harus antara 9-13 digit angka.');
            }

            // Cek apakah nomor sudah ada sebelumnya
            $existingNumber = $user->dataPegawai->nomor_telepon;

            // Update nomor WhatsApp di DataPegawai
            $user->dataPegawai->update([
                'nomor_telepon' => $whatsappNumber,
                'updated_at'    => now(),
            ]);

            // Tentukan pesan sukses berdasarkan kondisi
            if ($existingNumber) {
                return redirect()->back()->with('success', 'Nomor WhatsApp berhasil diperbarui!');
            } else {
                return redirect()->back()->with('success', 'Nomor WhatsApp berhasil ditambahkan!');
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->with('error', 'Terjadi kesalahan validasi.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function getJadwalPerencanaan()
    {
        $kategoriTenggat = KategoriTenggat::where('kategori_tenggat', 'perencanaan_pegawai')->first();
        $tenggatRencana  = $kategoriTenggat ? TenggatRencana::where('kategori_tenggat_id', $kategoriTenggat->id)->first() : null;
        $waktuSekarang   = Carbon::now();

        if ($tenggatRencana) {
            $waktuMulai   = Carbon::parse($tenggatRencana->tanggal_mulai . ' ' . $tenggatRencana->jam_mulai);
            $waktuSelesai = Carbon::parse($tenggatRencana->tanggal_selesai . ' ' . $tenggatRencana->jam_selesai);
            $sisaHari     = ceil($waktuSekarang->diffInDays($waktuSelesai, false));
            $isActive     = $waktuSekarang->between($waktuMulai, $waktuSelesai);

            $jadwalPerencanaan = [
                'waktuMulai'   => $waktuMulai,
                'waktuSelesai' => $waktuSelesai,
                'sisaHari'     => $sisaHari,
                'isActive'     => $isActive,
            ];
        } else {
            $jadwalPerencanaan = null;
        }

        return $jadwalPerencanaan;
    }

    public function getjadwalValidasi()
    {
        $kategoriTenggat = KategoriTenggat::where('kategori_tenggat', 'validasi_kelompok')->first();
        $tenggatRencana  = $kategoriTenggat ? TenggatRencana::where('kategori_tenggat_id', $kategoriTenggat->id)->first() : null;
        $waktuSekarang   = Carbon::now();

        if ($tenggatRencana) {
            $waktuMulai   = Carbon::parse($tenggatRencana->tanggal_mulai . ' ' . $tenggatRencana->jam_mulai);
            $waktuSelesai = Carbon::parse($tenggatRencana->tanggal_selesai . ' ' . $tenggatRencana->jam_selesai);
            $sisaHari     = ceil($waktuSekarang->diffInDays($waktuSelesai, false));
            $isActive     = $waktuSekarang->between($waktuMulai, $waktuSelesai);

            $jadwalValidasi = [
                'waktuMulai'   => $waktuMulai,
                'waktuSelesai' => $waktuSelesai,
                'sisaHari'     => $sisaHari,
                'isActive'     => $isActive,
            ];
        } else {
            $jadwalValidasi = null;
        }

        return $jadwalValidasi;
    }

    public function getjadwalVerifikasi()
    {
        $kategoriTenggat = KategoriTenggat::where('kategori_tenggat', 'verifikasi_unit_kerja')->first();
        $tenggatRencana  = $kategoriTenggat ? TenggatRencana::where('kategori_tenggat_id', $kategoriTenggat->id)->first() : null;
        $waktuSekarang   = Carbon::now();

        if ($tenggatRencana) {
            $waktuMulai   = Carbon::parse($tenggatRencana->tanggal_mulai . ' ' . $tenggatRencana->jam_mulai);
            $waktuSelesai = Carbon::parse($tenggatRencana->tanggal_selesai . ' ' . $tenggatRencana->jam_selesai);
            $sisaHari     = ceil($waktuSekarang->diffInDays($waktuSelesai, false));
            $isActive     = $waktuSekarang->between($waktuMulai, $waktuSelesai);

            $jadwalVerifikasi = [
                'waktuMulai'   => $waktuMulai,
                'waktuSelesai' => $waktuSelesai,
                'sisaHari'     => $sisaHari,
                'isActive'     => $isActive,
            ];
        } else {
            $jadwalVerifikasi = null;
        }

        return $jadwalVerifikasi;
    }

    public function getjadwalApproval()
    {
        $kategoriTenggat = KategoriTenggat::where('kategori_tenggat', 'approval_universitas')->first();
        $tenggatRencana  = $kategoriTenggat ? TenggatRencana::where('kategori_tenggat_id', $kategoriTenggat->id)->first() : null;
        $waktuSekarang   = Carbon::now();

        if ($tenggatRencana) {
            $waktuMulai   = Carbon::parse($tenggatRencana->tanggal_mulai . ' ' . $tenggatRencana->jam_mulai);
            $waktuSelesai = Carbon::parse($tenggatRencana->tanggal_selesai . ' ' . $tenggatRencana->jam_selesai);
            $sisaHari     = ceil($waktuSekarang->diffInDays($waktuSelesai, false));
            $isActive     = $waktuSekarang->between($waktuMulai, $waktuSelesai);

            $jadwalApproval = [
                'waktuMulai'   => $waktuMulai,
                'waktuSelesai' => $waktuSelesai,
                'sisaHari'     => $sisaHari,
                'isActive'     => $isActive,
            ];
        } else {
            $jadwalApproval = null;
        }

        return $jadwalApproval;
    }

// Method untuk mendapatkan progres validasi
    private function getProgresValidasi($dataPegawai)
    {
        $rencanaIds = $dataPegawai->rencanaPembelajaran->pluck('id');

        $total = $rencanaIds->count();
        if ($total === 0) {
            return null;
        }

        $validasiData = kelompokCanValidating::whereIn('rencana_pembelajaran_id', $rencanaIds)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        $disetujui = $validasiData->where('status', 'disetujui')->first()->count ?? 0;
        $direvisi  = $validasiData->where('status', 'direvisi')->first()->count ?? 0;
        $belum     = $total - $disetujui - $direvisi;

        return [
            'total'            => $total,
            'disetujui'        => $disetujui,
            'direvisi'         => $direvisi,
            'belum'            => $belum,
            'persen_disetujui' => $total > 0 ? round(($disetujui / $total) * 100, 1) : 0,
            'persen_direvisi'  => $total > 0 ? round(($direvisi / $total) * 100, 1) : 0,
            'persen_belum'     => $total > 0 ? round(($belum / $total) * 100, 1) : 0,
        ];
    }

// Method untuk mendapatkan progres verifikasi
    private function getProgresVerifikasi($dataPegawai)
    {
        $rencanaIds = $dataPegawai->rencanaPembelajaran->pluck('id');

        $total = $rencanaIds->count();
        if ($total === 0) {
            return null;
        }

        $verifikasiData = unitKerjaCanVerifying::whereIn('rencana_pembelajaran_id', $rencanaIds)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        $disetujui = $verifikasiData->where('status', 'disetujui')->first()->count ?? 0;
        $direvisi  = $verifikasiData->where('status', 'direvisi')->first()->count ?? 0;
        $belum     = $total - $disetujui - $direvisi;

        return [
            'total'            => $total,
            'disetujui'        => $disetujui,
            'direvisi'         => $direvisi,
            'belum'            => $belum,
            'persen_disetujui' => $total > 0 ? round(($disetujui / $total) * 100, 1) : 0,
            'persen_direvisi'  => $total > 0 ? round(($direvisi / $total) * 100, 1) : 0,
            'persen_belum'     => $total > 0 ? round(($belum / $total) * 100, 1) : 0,
        ];
    }

// Method untuk mendapatkan progres approval
    private function getProgresApproval($dataPegawai)
    {
        $rencanaIds = $dataPegawai->rencanaPembelajaran->pluck('id');

        $total = $rencanaIds->count();
        if ($total === 0) {
            return null;
        }

        $approvalData = universitasCanApproving::whereIn('rencana_pembelajaran_id', $rencanaIds)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        $disetujui = $approvalData->where('status', 'disetujui')->first()->count ?? 0;
        $ditolak   = $approvalData->where('status', 'ditolak')->first()->count ?? 0;
        $belum     = $total - $disetujui - $ditolak;

        return [
            'total'            => $total,
            'disetujui'        => $disetujui,
            'ditolak'          => $ditolak,
            'belum'            => $belum,
            'persen_disetujui' => $total > 0 ? round(($disetujui / $total) * 100, 1) : 0,
            'persen_ditolak'   => $total > 0 ? round(($ditolak / $total) * 100, 1) : 0,
            'persen_belum'     => $total > 0 ? round(($belum / $total) * 100, 1) : 0,
        ];
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function processFoto(Request $request)
    {
        $dataPegawai = Auth::user()->dataPegawai;

        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:5000',
        ]);

        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($dataPegawai->foto && Storage::exists($dataPegawai->foto)) {
                Storage::delete($dataPegawai->foto);
            }

            // Simpan foto baru
            $dataPegawai->foto = $request->file('foto')->store('public/foto');
        }

        $dataPegawai->save();

        return redirect()->route('profil')
            ->with('success', 'Foto profil berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function changePassword()
    {
        // Hapus session default_password
        session()->forget('default_password');

        return view('password_edit');
    }

    public function processPassword(Request $request)
    {
        $request->validate([
            'password_sekarang'   => 'required',
            'password_baru'       => 'required',
            'konfirmasi_password' => 'required',
        ]);

        $user = Auth::user();

        if (! Hash::check($request->password_sekarang, $user->password)) {
            return back()->withErrors([
                'password_sekarang' => 'Password tidak sesuai',
            ]);
        }

        if ($request->password_baru != $request->konfirmasi_password) {
            return back()->withErrors([
                'konfirmasi_password' => 'Konfirmasi password tidak cocok dengan password baru.',
            ]);
        }

        User::whereId(Auth::user()->id)->update([
            'password' => Hash::make($request->password_baru),
        ]);

        return redirect()->route('profil')
            ->with('success', 'Password berhasil diubah!');
    }

    // Method untuk mendapatkan statistik validasi ketua kelompok
    private function getStatistikValidasiKetua($dataPegawai)
    {
        // Ambil ID kelompok yang dipimpin oleh ketua kelompok ini
        $kelompokId = $dataPegawai->kelompok_id;
        if (! $kelompokId) {
            return null;
        }

        // Ambil semua rencana pembelajaran dari anggota kelompok
        $anggotaKelompok = DataPegawai::where('kelompok_id', $kelompokId)
            ->where('id', '!=', $dataPegawai->id) // Exclude ketua sendiri
            ->pluck('id');

        // PERBAIKAN: Ambil rencana yang perlu divalidasi ketua
        $rencanaAnggota = RencanaPembelajaran::whereIn('data_pegawai_id', $anggotaKelompok)
            ->where(function ($query) {
                $query->whereHas('kelompokCanValidating') // Yang sudah/sedang divalidasi
                    ->orWhere(function ($q) {
                        // Pendidikan yang belum divalidasi
                        $q->where('klasifikasi', 'pendidikan')
                            ->whereNotNull('data_pendidikan_id');
                    });
            })
            ->pluck('id');

        $total = $rencanaAnggota->count();
        if ($total === 0) {
            return null;
        }

        // Ambil data validasi oleh ketua kelompok ini
        $validasiData = KelompokCanValidating::whereIn('rencana_pembelajaran_id', $rencanaAnggota)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        $disetujui = $validasiData->where('status', 'disetujui')->first()->count ?? 0;
        $direvisi  = $validasiData->where('status', 'direvisi')->first()->count ?? 0;
        $belum     = $total - $disetujui - $direvisi;

        // PERBAIKAN TAMBAHAN: Hitung per jenis kegiatan
        $rencanaPerJenis = RencanaPembelajaran::whereIn('id', $rencanaAnggota)
            ->get()
            ->groupBy(function ($item) {
                return $item->klasifikasi === 'pelatihan' ? 'Pelatihan' : 'Pendidikan';
            })
            ->map(function ($item) {
                return [
                    'jumlah'        => $item->count(),
                    'jam_pelajaran' => $item->sum('jam_pelajaran'),
                ];
            });

        return [
            'total'               => $total,
            'disetujui'           => $disetujui,
            'direvisi'            => $direvisi,
            'belum'               => $belum,
            'persen_disetujui'    => $total > 0 ? round(($disetujui / $total) * 100, 1) : 0,
            'persen_direvisi'     => $total > 0 ? round(($direvisi / $total) * 100, 1) : 0,
            'persen_belum'        => $total > 0 ? round(($belum / $total) * 100, 1) : 0,
            'jumlah_anggota'      => $anggotaKelompok->count(),
            'rencana_per_anggota' => $total > 0 ? round($total / $anggotaKelompok->count(), 1) : 0,
            // TAMBAHAN: Data per jenis
            'rencana_per_jenis'   => $rencanaPerJenis,
        ];
    }

    // Method untuk mendapatkan statistik verifikasi unit kerja
    private function getStatistikVerifikasiUnitKerja($dataPegawai)
    {
        // Pastikan user adalah bagian dari unit kerja
        if (! $dataPegawai->unit_kerja_id) {
            return null;
        }

        $unitKerjaId = $dataPegawai->unit_kerja_id;

        // Ambil semua pegawai di unit kerja ini
        $pegawaiUnitKerja = DataPegawai::where('unit_kerja_id', $unitKerjaId)->pluck('id');

        // PERBAIKAN: Ambil semua rencana pembelajaran (baik pendidikan maupun pelatihan)
        $rencanaUnitKerja = RencanaPembelajaran::whereIn('data_pegawai_id', $pegawaiUnitKerja)
            ->where(function ($query) {
                $query->whereHas('kelompokCanValidating', function ($q) {
                    $q->where('status', 'disetujui');
                })
                    ->orWhere(function ($q) {
                        // Untuk pelatihan yang mungkin tidak melalui validasi kelompok
                        $q->where('klasifikasi', 'pelatihan')
                            ->whereNotNull('data_pelatihan_id');
                    });
            })
            ->get();

        $total = $rencanaUnitKerja->count();
        if ($total === 0) {
            return null;
        }

        // Ambil data verifikasi oleh unit kerja ini
        $verifikasiData = unitKerjaCanVerifying::whereIn('rencana_pembelajaran_id', $rencanaUnitKerja->pluck('id'))
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        $disetujui = $verifikasiData->where('status', 'disetujui')->first()->count ?? 0;
        $direvisi  = $verifikasiData->where('status', 'direvisi')->first()->count ?? 0;
        $belum     = $total - $disetujui - $direvisi;

        // Data untuk analisis lebih lanjut
        $rencanaPerKelompok = RencanaPembelajaran::whereIn('data_pegawai_id', $pegawaiUnitKerja)
            ->where(function ($query) {
                $query->whereHas('kelompokCanValidating', function ($q) {
                    $q->where('status', 'disetujui');
                })
                    ->orWhere(function ($q) {
                        // Untuk pelatihan
                        $q->where('klasifikasi', 'pelatihan')
                            ->whereNotNull('data_pelatihan_id');
                    });
            })
            ->with(['dataPegawai.kelompok', 'unitKerjaCanVerifying'])
            ->get()
            ->groupBy(function ($item) {
                return $item->dataPegawai->kelompok->ketua->nama ?? 'Tidak dikelompokkan';
            })
            ->map(function ($item, $key) {
                return [
                    'jumlah'    => $item->count(),
                    'disetujui' => $item->where('unitKerjaCanVerifying.status', 'disetujui')->count(),
                    'direvisi'  => $item->where('unitKerjaCanVerifying.status', 'direvisi')->count(),
                    'belum'     => $item->where('unitKerjaCanVerifying.status', null)->count(),
                ];
            });

        // PERBAIKAN PENTING: Data per jenis pendidikan/pelatihan
        $rencanaPerJenis = RencanaPembelajaran::whereIn('data_pegawai_id', $pegawaiUnitKerja)
            ->where(function ($query) {
                $query->whereHas('kelompokCanValidating', function ($q) {
                    $q->where('status', 'disetujui');
                })
                    ->orWhere(function ($q) {
                        // Untuk pelatihan
                        $q->where('klasifikasi', 'pelatihan')
                            ->whereNotNull('data_pelatihan_id');
                    });
            })
            ->get()
            ->groupBy(function ($item) {
                // Gunakan field klasifikasi dari database
                return $item->klasifikasi === 'pelatihan' ? 'Pelatihan' : 'Pendidikan';
            })
            ->map(function ($item, $key) {
                return [
                    'jumlah'        => $item->count(),
                    'jam_pelajaran' => $item->sum('jam_pelajaran'),
                ];
            });

        // Data anggaran
        $totalAnggaran     = $rencanaUnitKerja->sum('anggaran_rencana');
        $anggaranDisetujui = $rencanaUnitKerja->where('unitKerjaCanVerifying.status', 'disetujui')->sum('anggaran_rencana');
        $anggaranDirevisi  = $rencanaUnitKerja->where('unitKerjaCanVerifying.status', 'direvisi')->sum('anggaran_rencana');

        // Total jam pelajaran
        $totalJamPelajaran     = $rencanaUnitKerja->sum('jam_pelajaran');
        $jamPelajaranDisetujui = $rencanaUnitKerja->where('unitKerjaCanVerifying.status', 'disetujui')->sum('jam_pelajaran');
        $jamPelajaranDirevisi  = $rencanaUnitKerja->where('unitKerjaCanVerifying.status', 'direvisi')->sum('jam_pelajaran');

        return [
            'total'                   => $total,
            'disetujui'               => $disetujui,
            'direvisi'                => $direvisi,
            'belum'                   => $belum,
            'persen_disetujui'        => $total > 0 ? round(($disetujui / $total) * 100, 1) : 0,
            'persen_direvisi'         => $total > 0 ? round(($direvisi / $total) * 100, 1) : 0,
            'persen_belum'            => $total > 0 ? round(($belum / $total) * 100, 1) : 0,
            'jumlah_pegawai'          => $pegawaiUnitKerja->count(),
            'rencana_per_pegawai'     => $total > 0 ? round($total / $pegawaiUnitKerja->count(), 1) : 0,
            'rencana_per_kelompok'    => $rencanaPerKelompok,
            'rencana_per_jenis'       => $rencanaPerJenis,
            'total_anggaran'          => $totalAnggaran,
            'anggaran_disetujui'      => $anggaranDisetujui,
            'anggaran_direvisi'       => $anggaranDirevisi,
            'total_jam_pelajaran'     => $totalJamPelajaran,
            'jam_pelajaran_disetujui' => $jamPelajaranDisetujui,
            'jam_pelajaran_direvisi'  => $jamPelajaranDirevisi,
            'unit_kerja'              => $dataPegawai->unitKerja->unit_kerja,
        ];
    }

    // Method untuk mendapatkan statistik komprehensif approver universitas
    private function getStatistikApprovalUniversitas()
    {
        // PERBAIKAN: Ambil semua rencana yang valid (baik pendidikan maupun pelatihan)
        $rencanaTerverifikasi = RencanaPembelajaran::where(function ($query) {
            $query->whereHas('unitKerjaCanVerifying', function ($q) {
                $q->where('status', 'disetujui');
            })
                ->orWhere(function ($q) {
                    // Untuk pelatihan yang mungkin langsung ke universitas
                    $q->where('klasifikasi', 'pelatihan')
                        ->whereNotNull('data_pelatihan_id');
                });
        })
            ->with([
                'dataPegawai.unitKerja',
                'dataPegawai.kelompok',
                'dataPendidikan',
                'dataPelatihan',
                'bentukJalur',
                'jenisPendidikan',
                'region',
                'jenjang',
                'universitasCanApproving',
            ])->get();

        $total = $rencanaTerverifikasi->count();
        if ($total === 0) {
            return null;
        }

        // Data approval status
        $approvalData = UniversitasCanApproving::whereIn('rencana_pembelajaran_id', $rencanaTerverifikasi->pluck('id'))
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        $disetujui = $approvalData->where('status', 'disetujui')->first()->count ?? 0;
        $ditolak   = $approvalData->where('status', 'ditolak')->first()->count ?? 0;
        $belum     = $total - $disetujui - $ditolak;

        // Analisis per Unit Kerja
        $analisisUnitKerja = $rencanaTerverifikasi->groupBy('dataPegawai.unitKerja.unit_kerja')
            ->map(function ($item, $key) {
                $totalAnggaran = $item->sum('anggaran_rencana');
                $avgJam        = $item->avg('jam_pelajaran');
                $approved      = $item->where('universitasCanApproving.status', 'disetujui')->count();
                $rejected      = $item->where('universitasCanApproving.status', 'ditolak')->count();

                return [
                    'total'          => $item->count(),
                    'total_anggaran' => $totalAnggaran,
                    'avg_jam'        => round($avgJam, 1),
                    'approved'       => $approved,
                    'rejected'       => $rejected,
                    'pending'        => $item->count() - $approved - $rejected,
                    'avg_anggaran'   => round($totalAnggaran / $item->count(), 0),
                ];
            })->sortByDesc('total_anggaran');

        // PERBAIKAN: Analisis per Jenis Kegiatan berdasarkan klasifikasi
        $analisisJenis = $rencanaTerverifikasi->groupBy(function ($item) {
            return $item->klasifikasi === 'pelatihan' ? 'Pelatihan' : 'Pendidikan Formal';
        })->map(function ($item) {
            return [
                'count'          => $item->count(),
                'total_anggaran' => $item->sum('anggaran_rencana'),
                'avg_anggaran'   => round($item->avg('anggaran_rencana'), 0),
                'total_jam'      => $item->sum('jam_pelajaran'),
                'approved'       => $item->where('universitasCanApproving.status', 'disetujui')->count(),
            ];
        });

        // Analisis per Prioritas
        $analisisPrioritas = $rencanaTerverifikasi->groupBy('prioritas')
            ->map(function ($item) {
                return [
                    'count'          => $item->count(),
                    'total_anggaran' => $item->sum('anggaran_rencana'),
                    'approved_rate'  => $item->where('universitasCanApproving.status', 'disetujui')->count() / $item->count() * 100,
                ];
            });

        // Analisis per Klasifikasi (sekarang lebih akurat)
        $analisisKlasifikasi = $rencanaTerverifikasi->groupBy('klasifikasi')
            ->map(function ($item) {
                return [
                    'count'          => $item->count(),
                    'total_anggaran' => $item->sum('anggaran_rencana'),
                    'avg_jam'        => round($item->avg('jam_pelajaran'), 1),
                ];
            });

        // Trend per Tahun
        $analisisTahun = $rencanaTerverifikasi->groupBy('tahun')
            ->map(function ($item) {
                return [
                    'count'          => $item->count(),
                    'total_anggaran' => $item->sum('anggaran_rencana'),
                    'approved'       => $item->where('universitasCanApproving.status', 'disetujui')->count(),
                    'avg_anggaran'   => round($item->avg('anggaran_rencana'), 0),
                ];
            })->sortKeys();

        // Ringkasan Anggaran
        $totalAnggaranAll  = $rencanaTerverifikasi->sum('anggaran_rencana');
        $anggaranDisetujui = $rencanaTerverifikasi->where('universitasCanApproving.status', 'disetujui')->sum('anggaran_rencana');
        $anggaranDitolak   = $rencanaTerverifikasi->where('universitasCanApproving.status', 'ditolak')->sum('anggaran_rencana');

        return [
            // Status Approval
            'total'                    => $total,
            'disetujui'                => $disetujui,
            'ditolak'                  => $ditolak,
            'belum'                    => $belum,
            'persen_disetujui'         => $total > 0 ? round(($disetujui / $total) * 100, 1) : 0,
            'persen_ditolak'           => $total > 0 ? round(($ditolak / $total) * 100, 1) : 0,
            'persen_belum'             => $total > 0 ? round(($belum / $total) * 100, 1) : 0,

            // Analisis Komprehensif
            'analisis_unit_kerja'      => $analisisUnitKerja,
            'analisis_jenis'           => $analisisJenis,
            'analisis_prioritas'       => $analisisPrioritas,
            'analisis_klasifikasi'     => $analisisKlasifikasi,
            'analisis_tahun'           => $analisisTahun,

            // Ringkasan Anggaran
            'total_anggaran'           => $totalAnggaranAll,
            'anggaran_disetujui'       => $anggaranDisetujui,
            'anggaran_ditolak'         => $anggaranDitolak,
            'anggaran_belum'           => $totalAnggaranAll - $anggaranDisetujui - $anggaranDitolak,

            // Statistik Tambahan
            'total_jam_pelajaran'      => $rencanaTerverifikasi->sum('jam_pelajaran'),
            'avg_jam_per_rencana'      => round($rencanaTerverifikasi->avg('jam_pelajaran'), 1),
            'avg_anggaran_per_rencana' => round($rencanaTerverifikasi->avg('anggaran_rencana'), 0),
            'jumlah_unit_kerja'        => $analisisUnitKerja->count(),
            'top_unit_kerja'           => $analisisUnitKerja->first(),
        ];
    }
}
