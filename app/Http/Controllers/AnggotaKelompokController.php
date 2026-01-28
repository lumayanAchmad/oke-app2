<?php
namespace App\Http\Controllers;

use App\Models\DataPegawai;
use App\Models\Kelompok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnggotaKelompokController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pegawai = DataPegawai::with([
            'kelompok.ketua',
            'kelompok.anggota.unitKerja',
        ])->where('user_id', Auth::id())->first();

        $belumKelompok = ! $pegawai || $pegawai->kelompok_id == 0;
        $hasKelompok   = ! $belumKelompok;

        $kelompok      = null;
        $isKetua       = false;
        $ketuaKelompok = null;
        $dataPegawai   = collect();

        if ($hasKelompok && $pegawai->kelompok) {
            $kelompok = $pegawai->kelompok;
            $isKetua  = $kelompok->id_ketua === $pegawai->id;

            if (! $isKetua) {
                $ketuaKelompok = $kelompok->ketua;
            }

            $dataPegawai = $kelompok->anggota
                ->where('id', '!=', $kelompok->id_ketua);
        }

        return view('anggota_kelompok_index', compact(
            'dataPegawai',
            'belumKelompok',
            'isKetua',
            'hasKelompok',
            'ketuaKelompok',
            'kelompok'
        ));
    }

    public function updateWhatsAppLink(Request $request)
    {
        $request->validate([
            'whatsapp_link' => 'required|url|starts_with:https://chat.whatsapp.com/',
        ]);

        $kelompok = Kelompok::where('id_ketua', Auth::user()->dataPegawai->id)->firstOrFail();

        $kelompok->update([
            'link_grup_whatsapp' => $request->whatsapp_link,
        ]);

        return back()
            ->with('success', 'Link grup berhasil diperbarui!');
    }
}
