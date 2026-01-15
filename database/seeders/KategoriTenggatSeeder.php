<?php
namespace Database\Seeders;

use App\Models\KategoriTenggat;
use Illuminate\Database\Seeder;

class KategoriTenggatSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Menambahkan jenjang dalam array
        $kategoris = ['perencanaan_pegawai', 'validasi_kelompok', 'verifikasi_unit_kerja', 'approval_universitas'];

        // Menyimpan setiap role ke dalam database
        foreach ($kategoris as $kategori) {
            KategoriTenggat::create(['kategori_tenggat' => $kategori]);
        }
    }
}
