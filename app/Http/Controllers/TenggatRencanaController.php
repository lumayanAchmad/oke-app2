<?php
namespace App\Http\Controllers;

use App\Models\KategoriTenggat;
use App\Models\TenggatRencana;
use Illuminate\Http\Request;

class TenggatRencanaController extends Controller
{
    public function index()
    {
        $kategoriTenggats = KategoriTenggat::with('tenggatRencana')->get();

        return view('tenggat_rencana_index', compact('kategoriTenggats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kategori_tenggat_id' => 'required|exists:kategori_tenggats,id',
            'tanggal_mulai'       => 'required|date|after_or_equal:today',
            'jam_mulai'           => 'required',
            'tanggal_selesai'     => 'required|date|after:tanggal_mulai',
            'jam_selesai'         => 'required',
        ]);

        TenggatRencana::create($request->all());

        return redirect()->back()
            ->with('success', 'Tenggat Rencana Berhasil Dibuat!');
    }

    public function update(Request $request, TenggatRencana $tenggat_rencana)
    {
        // Validasi input
        $validatedData = $request->validate([
            'tanggal_mulai'   => 'required|date|after_or_equal:today',
            'jam_mulai'       => 'required',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'jam_selesai'     => 'required',
        ]);

        // Cek apakah ada perubahan data
        if ($tenggat_rencana->fill($validatedData)->isDirty()) {
            // Update data
            $tenggat_rencana->save();

            // Set message sukses
            return redirect()->back()->with('success', 'Tenggat Rencana Berhasil Diperbarui!');
        } else {
            // Set message tidak ada perubahan
            return redirect()->back()->with('info', 'Tidak ada perubahan data');
        }
    }
}
