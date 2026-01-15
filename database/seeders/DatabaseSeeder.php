<?php
namespace Database\Seeders;

use App\Models\AnggaranPendidikan;
use App\Models\BentukJalur;
use App\Models\DataPegawai;
use App\Models\Jabatan;
use App\Models\JenisPendidikan;
use App\Models\Jenjang;
use App\Models\JenjangTerakhir;
use App\Models\Kategori;
use App\Models\KategoriTenggat;
use App\Models\PendidikanTerakhir;
use App\Models\Region;
use App\Models\Role;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // Membuat user
            $user = User::create([
                'name'     => 'admin',
                'email'    => 'admin@gmail.com',
                'password' => Hash::make('password'),
            ]);

            // Membuat unit kerja, jabatan, jenjang terakhir, dan pendidikan terakhir
            $unitKerja          = UnitKerja::create(['unit_kerja' => 'Fakultas Teknik']);
            $jabatan            = Jabatan::create(['jabatan' => 'administrasi website']);
            $jenjangTerakhir    = JenjangTerakhir::create(['jenjang_terakhir' => 'S1']);
            $pendidikanTerakhir = PendidikanTerakhir::create([
                'jenjang_terakhir_id' => $jenjangTerakhir->id,
                'jurusan'             => 'Teknik Elektro',
            ]);

            // Menambahkan jenjang dalam array
            $roles = ['admin', 'pegawai', 'ketua_kelompok', 'approver', 'verifikator', 'pimpinan'];

            // Menyimpan setiap role ke dalam database
            foreach ($roles as $role) {
                Role::create(['role' => $role]);
            }

            // Menambahkan role 'admin'
            $roleAdmin = Role::where('role', 'admin')->first();
            if ($roleAdmin) {
                $user->roles()->attach($roleAdmin);
            }

            // Membuat data pegawai
            DataPegawai::create([
                'nama'                   => 'Achmad Admin',
                'nppu'                   => '12345678',
                'status'                 => 'aktif',
                'pendidikan_terakhir_id' => $pendidikanTerakhir->id,
                'jenis_kelamin'          => 'L',
                'nomor_telepon'          => '62896012030124',
                'unit_kerja_id'          => $unitKerja->id,
                'jabatan_id'             => $jabatan->id,
                'user_id'                => $user->id,
            ]);

            // Data kategori dan bentuk jalur
            $data = [
                [
                    'kategori'     => 'klasikal',
                    'bentuk_jalur' => [
                        'Pelatihan struktural kepemimpinan',
                        'Pelatihan manajerial',
                        'Pelatihan teknis',
                        'Pelatihan fungsional',
                        'Pelatihan social kultural',
                        'Seminar/konferensi/ saresehan/Webinar',
                        'Workshop/lokakarya',
                        'Kursus',
                        'Penataran',
                        'Bimbingan teknis',
                    ],
                ],
                [
                    'kategori'     => 'non-klasikal',
                    'bentuk_jalur' => [
                        'Sosialisasi',
                        'Counselling',
                        'Coaching',
                        'Mentoring',
                        'E-Learning',
                        'Pelatihan jarak jauh',
                        'Pembelajaran alam terbuka (outbound)',
                        'Patok Banding (benchmarking)',
                        'Pertukaran antara PNS dg pegawai swasta/ BUMN/BUMD',
                        'Belajar mandiri (self learning)',
                        'Komunitas belajar (community of practices)',
                        'Magang/praktek Kerja',
                        'Datasering (secondment)',
                    ],
                ],
            ];

            // Looping data dan insert ke tabel BentukJalur
            foreach ($data as $item) {
                // Mencari atau membuat kategori
                $kategori = Kategori::firstOrCreate(['kategori' => $item['kategori']]);

                // Menambahkan bentuk jalur yang terkait dengan kategori
                foreach ($item['bentuk_jalur'] as $bentukJalur) {
                    BentukJalur::create([
                        'kategori_id'  => $kategori->id, // Menyimpan ID kategori
                        'bentuk_jalur' => $bentukJalur,  // Menyimpan bentuk jalur
                    ]);
                }
            }

            // Data kategori dan bentuk jalur
            $data = [
                [
                    'jenis_pendidikan' => 'tb+',
                    'keterangan'       => 'meninggalkan tugas jabatan + dengan jaminan pembiayaan/beasiswa',
                ],
                [
                    'jenis_pendidikan' => 'tb',
                    'keterangan'       => 'tidak meninggalkan tugas jabatan + dengan jaminan pembiayaan/beasiswa',
                ],
                [
                    'jenis_pendidikan' => 'tbbm+',
                    'keterangan'       => 'meninggalkan tugas jabatan + dengan biaya mandiri',
                ],
                [
                    'jenis_pendidikan' => 'tbbm',
                    'keterangan'       => 'tidak meninggalkan tugas jabatan + dengan biaya mandiri',
                ],
            ];

            // Looping data dan insert ke tabel JenisPendidikan
            foreach ($data as $item) {
                // Mencari atau membuat jenis_pendidikan
                JenisPendidikan::firstOrCreate(['jenis_pendidikan' => $item['jenis_pendidikan']], ['keterangan' => $item['keterangan']]);
            }

            // Menambahkan jenjang dalam array
            $jenjangs = ['S1', 'S2', 'S3'];

            // Menyimpan setiap jenjang ke dalam database
            foreach ($jenjangs as $jenjang) {
                Jenjang::create(['jenjang' => $jenjang]);
            }

            // Menambahkan jenjang dalam array
            $kategoris = ['perencanaan_pegawai', 'validasi_kelompok', 'verifikasi_unit_kerja', 'approval_universitas'];

            // Menyimpan setiap role ke dalam database
            foreach ($kategoris as $kategori) {
                KategoriTenggat::create(['kategori_tenggat' => $kategori]);
            }

            // Menambahkan jenjang dalam array
            $regions = ['nasional', 'internasional'];

            // Menyimpan setiap role ke dalam database
            foreach ($regions as $region) {
                Region::create(['region' => $region]);
            }

            // Ambil data region berdasarkan nama
            $nasional      = Region::where('region', 'nasional')->firstOrFail();
            $internasional = Region::where('region', 'internasional')->firstOrFail();

            // Seed data anggaran pendidikan
            AnggaranPendidikan::create([
                'jenjang_id'    => 1,
                'region_id'     => $nasional->id, // Menggunakan region_id dari tabel regions
                'anggaran_min'  => 80000000,
                'anggaran_maks' => 251200000,
            ]);

            AnggaranPendidikan::create([
                'jenjang_id'    => 1,
                'region_id'     => $internasional->id, // Menggunakan region_id dari tabel regions
                'anggaran_min'  => 100000000,
                'anggaran_maks' => 563150000,
            ]);

            AnggaranPendidikan::create([
                'jenjang_id'    => 2,
                'region_id'     => $nasional->id,
                'anggaran_min'  => 200000000,
                'anggaran_maks' => 247000000,
            ]);

            AnggaranPendidikan::create([
                'jenjang_id'    => 2,
                'region_id'     => $internasional->id,
                'anggaran_min'  => 10000000,
                'anggaran_maks' => 1217000000,
            ]);

            AnggaranPendidikan::create([
                'jenjang_id'    => 3,
                'region_id'     => $nasional->id,
                'anggaran_min'  => 300000000,
                'anggaran_maks' => 581000000,
            ]);

            AnggaranPendidikan::create([
                'jenjang_id'    => 3,
                'region_id'     => $internasional->id,
                'anggaran_min'  => 2000000000,
                'anggaran_maks' => 2705000000,
            ]);
        });
    }
}
