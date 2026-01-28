<?php
namespace App\Http\Controllers;

use App\Imports\DataPegawaiImport;
use App\Models\DataPegawai;
use App\Models\Jabatan;
use App\Models\JenjangTerakhir;
use App\Models\PendidikanTerakhir;
use App\Models\Role;
use App\Models\UnitKerja;
use App\Models\User;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Validators\ValidationException;

class DataPegawaiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware('can:admin');
    }

    public function index()
    {
        $data['data_pegawai'] = DataPegawai::with([
            'user',
            'unitKerja',
            'jabatan',
            'pendidikanTerakhir.jenjangTerakhir',
        ])->latest()->get();

        return view('pegawai_index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles            = Role::all();
        $jenjang_terakhir = JenjangTerakhir::all();
        return view('pegawai_create', compact('roles', 'jenjang_terakhir'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $requestData = $request->validate([
            'nama'               => 'required|min:3',
            'nppu'               => 'required|unique:data_pegawais,nppu',
            'status'             => 'required',
            'unit_kerja'         => 'required',
            'jabatan'            => 'required',
            'pendidikan'         => 'required',
            'jurusan_pendidikan' => 'required',
            'jenis_kelamin'      => 'required',
            'nomor_telepon'      => 'nullable|integer',
            'foto'               => 'nullable|image|mimes:jpeg,png,jpg|max:10000',
            'email'              => 'required|email|unique:users,email',
            'roles'              => 'required|array',
        ]);

        $unitKerja = UnitKerja::updateOrCreate([
            'unit_kerja' => $requestData['unit_kerja'],
        ]);

        $jabatan = Jabatan::updateOrCreate([
            'jabatan' => $requestData['jabatan'],
        ]);

        $jenjang = JenjangTerakhir::updateOrCreate([
            'jenjang_terakhir' => $requestData['pendidikan'],
        ]);

        $pendidikan = PendidikanTerakhir::updateOrCreate([
            'jenjang_terakhir_id' => $jenjang->id,
            'jurusan'             => $requestData['jurusan_pendidikan'],
        ]);

        $user = User::create([
            'name'     => $requestData['nama'],
            'email'    => $requestData['email'],
            'password' => Hash::make('password'),
        ]);

        $roleIds = Role::whereIn('role', $requestData['roles'])->pluck('id');
        $user->roles()->attach($roleIds);

        $data_pegawai                         = new DataPegawai;
        $data_pegawai->user_id                = $user->id;
        $data_pegawai->unit_kerja_id          = $unitKerja->id;
        $data_pegawai->jabatan_id             = $jabatan->id;
        $data_pegawai->pendidikan_terakhir_id = $pendidikan->id;
        $data_pegawai->nama                   = $requestData['nama'];
        $data_pegawai->nppu                   = $requestData['nppu'];
        $data_pegawai->status                 = $requestData['status'];
        $data_pegawai->jenis_kelamin          = $requestData['jenis_kelamin'];
        $data_pegawai->nomor_telepon          = $requestData['nomor_telepon'] ?? null;

        if ($request->hasFile('foto')) {
            $data_pegawai->foto = $request->file('foto')->store('public');
        }

        $data_pegawai->save();

        return redirect()->route('data_pegawai.index')
            ->with('success', 'Data berhasil disimpan!');

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data['data_pegawai']        = DataPegawai::findOrFail($id);
        $data['user']                = $data['data_pegawai']->user;
        $data['roles']               = Role::all();
        $data['userRoles']           = $data['user']->roles->pluck('id')->toArray();
        $data['pendidikan_terakhir'] = $data['data_pegawai']->pendidikanTerakhir;
        $data['jenjang_terakhir']    = JenjangTerakhir::all();
        return view('pegawai_edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data_pegawai = DataPegawai::findOrFail($id);
        $user         = $data_pegawai->user;
        $pendidikan   = PendidikanTerakhir::findOrFail($data_pegawai->pendidikan_terakhir_id);
        $jenjang      = $data_pegawai->pendidikanTerakhir->jenjangTerakhir;

        $requestData = $request->validate([
            'nama'               => 'nullable|min:3',
            'nppu'               => [
                'nullable', Rule::unique('data_pegawais', 'nppu')->ignore($data_pegawai->id),
            ],
            'status'             => 'nullable',
            'jenis_kelamin'      => 'nullable',
            'nomor_telepon'      => 'nullable',
            'foto'               => 'nullable|image|mimes:jpeg,png,jpg|max:10000',
            'email'              => [
                'nullable',
                'email',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'pendidikan'         => 'nullable',
            'jurusan_pendidikan' => 'nullable',
        ]);

        // Update data_pegawais tanpa email
        $data_pegawai->update([
            'nama'          => $requestData['nama'],
            'nppu'          => $requestData['nppu'],
            'status'        => $requestData['status'],
            'jenis_kelamin' => $requestData['jenis_kelamin'],
            'nomor_telepon' => $requestData['nomor_telepon'],
        ]);

        if ($request->hasFile('foto')) {
            Storage::delete($data_pegawai->foto);
            $data_pegawai->foto = $request->file('foto')->store('public');
            $data_pegawai->save();
        }

        // Update users dengan email
        $user->update(['email' => $requestData['email']]);

        $jenjang->updateOrCreate(['jenjang_terakhir' => $requestData['pendidikan']]);

        // Update pendidikan_terakhir
        $pendidikan->updateOrCreate([
            'jenjang_terakhir_id' => $jenjang->id,
            'jurusan'             => $requestData['jurusan_pendidikan'],
        ]);

        if ($request->has('roles')) {
            $roles = $request->input('roles');
            $user->roles()->sync($roles);
        }

        return redirect()->route('data_pegawai.index')
            ->with('success', 'Data berhasil diubah!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data_pegawai = DataPegawai::findOrFail($id);

        if ($data_pegawai->foto && Storage::exists($data_pegawai->foto)) {
            Storage::delete($data_pegawai->foto);
        }

        $pendidikan = PendidikanTerakhir::find($data_pegawai->pendidikan_terakhir_id);
        if ($pendidikan) {
            $pendidikan->delete();
        }

        $user = $data_pegawai->user;
        if ($user) {
            $user->roles()->detach();
            $user->delete();
        }

        $data_pegawai->delete();

        return redirect()->route('data_pegawai.index')
            ->with('error', 'Data berhasil dihapus!');
    }

    public function importExcelData(Request $request)
    {
        $request->validate([
            'importDataPegawai' => [
                'required',
                'file',
                'mimes:xlsx,xls,csv',
            ],
        ], [
            'importDataPegawai.required' => 'File impor wajib diunggah.',
            'importDataPegawai.file'     => 'Berkas harus berupa file.',
            'importDataPegawai.mimes'    => 'Format file harus berupa xlsx, xls, atau csv.',
        ]);

        try {
            Excel::import(new DataPegawaiImport, $request->file('importDataPegawai'));

            return redirect()->route('data_pegawai.index')
                ->with('success', 'Data berhasil diimpor!');

        } catch (ValidationException $e) {
            $failures     = $e->failures();
            $errorMessage = "Gagal mengimpor data. Periksa file Anda.";

            foreach ($failures as $failure) {
                $errorMessage .= " Baris: {$failure->row()}, Kolom: " . implode(', ', $failure->attribute());
            }

            return redirect()->route('data_pegawai.index')
                ->with('error', $errorMessage);

        } catch (\Exception $e) {
            return redirect()->route('data_pegawai.index')
                ->with('error', 'Impor file gagal. Pastikan format file sesuai dengan template dan struktur data terkini!');
        }
    }
}
