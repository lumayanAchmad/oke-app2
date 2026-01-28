<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreBentukJalurRequest;
use App\Http\Requests\UpdateBentukJalurRequest;
use App\Models\BentukJalur;
use Illuminate\Http\Request;

class BentukJalurController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = BentukJalur::query();

        $bentuk_jalur = $query->latest()->get();

        return view('bentuk_jalur_index', compact('bentuk_jalur'));

    }

    public function store(StoreBentukJalurRequest $request)
    {
        // Validasi sudah dilakukan di StoreDataPelatihanRequest
        $requestData = $request->validated();

        // Simpan data ke dalam RencanaPembelajaran
        BentukJalur::create($requestData);

        return redirect()->route('bentuk_jalur.index')
            ->with('success', 'Bentuk jalur berhasil ditambah');
    }

    public function update(UpdateBentukJalurRequest $request, BentukJalur $bentukJalur)
    {
        $validatedData = $request->validated();
        $bentukJalur->update($validatedData);
        return redirect()->route('bentuk_jalur.index')
            ->with('success', 'Bentuk jalur berhasil diubah!');
    }

    public function destroy(BentukJalur $bentukJalur)
    {
        $bentukJalur->delete();
        return redirect()->route('bentuk_jalur.index')
            ->with('error', 'Data berhasil dihapus!');
    }
}
