<?php

use App\Http\Controllers\AnggotaKelompokController;
use App\Http\Controllers\BentukJalurController;
use App\Http\Controllers\DataPegawaiController;
use App\Http\Controllers\DataPelatihanController;
use App\Http\Controllers\DataPendidikanController;
use App\Http\Controllers\EditAksesController;
use App\Http\Controllers\kelompokCanValidatingController;
use App\Http\Controllers\KelompokController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\RencanaPembelajaranController;
use App\Http\Controllers\SuratRekomendasiController;
use App\Http\Controllers\TenggatRencanaController;
use App\Http\Controllers\unitKerjaCanVerifyingController;
use App\Http\Controllers\universitasCanApprovingController;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware([Authenticate::class, 'check.default.password'])->group(function () {

    // DATA PEGAWAI
    Route::resource('data_pegawai', DataPegawaiController::class);
    Route::post('/data_pegawai/import', [DataPegawaiController::class, 'importExcelData']);

    // RENCANA PEMBELAJARAN
    Route::resource('rencana_pembelajaran', RencanaPembelajaranController::class);
    Route::post('/rencana/{id}/ajukan', [RencanaPembelajaranController::class, 'ajukanVerifikasi'])
        ->name('rencana.ajukan');
    Route::post('/rencana_pembelajaran/{id}/kirim_revisi', [RencanaPembelajaranController::class, 'kirimRevisi'])->name('rencana_pembelajaran.kirim_revisi');
    Route::get('/get-bentuk-jalur/{kategori_id}', [RencanaPembelajaranController::class, 'getBentukJalur']);
    Route::get('/get-jenjang', [RencanaPembelajaranController::class, 'getJenjang']);
    Route::get('/get-jurusan-by-jenjang', [RencanaPembelajaranController::class, 'getJurusanByJenjang']);
    Route::get('/get-rumpun', [RencanaPembelajaranController::class, 'getRumpun']);
    Route::get('/nama-pelatihan/{rumpunId}', [RencanaPembelajaranController::class, 'getNamaPelatihan']);
    Route::get('/get-jenis-pendidikan', [RencanaPembelajaranController::class, 'getJenisPendidikan']);
    Route::get('/get-pelatihan-info/{id}', [RencanaPembelajaranController::class, 'getPelatihanInfo']);
    Route::get('/get-anggaran-by-pendidikan', [RencanaPembelajaranController::class, 'getAnggaranByPendidikan']);
    Route::get('/get-anggaran-by-pelatihan', [RencanaPembelajaranController::class, 'getAnggaranByPelatihan']);
    Route::get('/get-kategori-by-klasifikasi', [RencanaPembelajaranController::class, 'getKategori']);
    Route::post('/validasi-anggaran', [RencanaPembelajaranController::class, 'validasiAnggaran']);
    Route::get('/getPelatihanDetail/{id}', [RencanaPembelajaranController::class, 'getPelatihanDetail']);
    // Download surat rekomendasi
    Route::get('/rencana-pembelajaran/{id}/download-rekomendasi', [SuratRekomendasiController::class, 'downloadRekomendasi'])
        ->name('rencana.download_rekomendasi');

    // EDIT AKSES
    Route::resource('edit_akses', EditAksesController::class);

    // DATA PELATIHAN
    Route::resource('data_pelatihan', DataPelatihanController::class);
    Route::post('/data_pelatihan/import', [DataPelatihanController::class, 'importExcelData']);

    // DATA PENDIDIKAN
    Route::resource('data_pendidikan', DataPendidikanController::class);
    Route::post('/data_pendidikan/import', [DataPendidikanController::class, 'importExcelData']);

    // BENTUK JALUR
    Route::resource('bentuk_jalur', BentukJalurController::class);

    // DATA KELOMPOK
    Route::resource('kelompok', KelompokController::class);
    Route::post('/kelompok/reset', [KelompokController::class, 'reset']);

    // PROFIL/DASHBOARD
    Route::get('/profil', [ProfilController::class, 'show'])->name('profil');
    Route::post('/profil/tambah_foto', [ProfilController::class, 'processFoto']);
    Route::post('/profil/ganti_foto', [ProfilController::class, 'processFoto']);

    // VALIDASI (KETUA KELOMPOK)
    Route::resource('validasi_kelompok', kelompokCanValidatingController::class);
    Route::post('/validasi-kelompok/setujui/{rencana}', [kelompokCanValidatingController::class, 'setujui'])->name('validasi.setujui');
    Route::post('/validasi-kelompok/revisi/{rencana}', [kelompokCanValidatingController::class, 'revisi'])->name('validasi.revisi');
    Route::post('/validasi-kelompok/setujui-dari-revisi/{id}', [kelompokCanValidatingController::class, 'setujuiDariRevisi'])->name('validasi.setujui-dari-revisi');
    Route::post('/validasi-kelompok/tambah-revisi/{rencana}', [kelompokCanValidatingController::class, 'tambahRevisi'])->name('validasi.tambah-revisi');

    // VERIFIKASI (UNIT KERJA)
    Route::resource('verifikasi', unitKerjaCanVerifyingController::class);
    Route::post('/verifikasi/setujui/{rencana}', [unitKerjaCanVerifyingController::class, 'setujui'])->name('verifikasi.setujui');
    Route::post('/verifikasi/revisi/{rencana}', [unitKerjaCanVerifyingController::class, 'revisi'])->name('verifikasi.revisi');
    Route::post('/verifikasi/setujui-dari-revisi/{id}', [unitKerjaCanVerifyingController::class, 'setujuiDariRevisi'])->name('verifikasi.setujui-dari-revisi');
    Route::post('/verifikasi/tambah-revisi/{rencana}', [unitKerjaCanVerifyingController::class, 'tambahRevisi'])->name('verifikasi.tambah-revisi');
    Route::post('/verifikasi/setujui-massal', [unitKerjaCanVerifyingController::class, 'setujuiMassal'])->name('verifikasi.setujui-massal');
    Route::post('/verifikasi/batalkan-massal', [unitKerjaCanVerifyingController::class, 'batalkanMassal'])->name('verifikasi.batalkanMassal');

    // VERIFIKASI (UNIT KERJA)
    Route::resource('approval', universitasCanApprovingController::class);
    Route::post('/approval/approve/{rencana}', [universitasCanApprovingController::class, 'approve'])->name('approval.approve');
    Route::post('/approval/approve-massal', [universitasCanApprovingController::class, 'approveMassal'])->name('approval.approve-massal');
    Route::post('/approval/reject/{rencana}', [universitasCanApprovingController::class, 'reject'])->name('approval.reject');
    Route::post('/approval/reject-massal', [universitasCanApprovingController::class, 'rejectMassal'])->name('approval.reject-massal');
    Route::post('/approval/catatan/{rencana}', [universitasCanApprovingController::class, 'updateCatatan'])->name('approval.catatan');

    // CEK ANGGOTA KELOMPOK
    Route::resource('anggota_kelompok', AnggotaKelompokController::class);
    Route::post('anggota_kelompok/update-whatsapp-link', [AnggotaKelompokController::class, 'updateWhatsAppLink'])->name('update-whatsapp-link');

    // ATUR TENGGAT RENCANA (ADMIN)
    Route::resource('tenggat_rencana', TenggatRencanaController::class);

    // UNTUK BACA SEMUA NOTIFIKASI
    Route::prefix('notifications')->group(function () {
        Route::get('/dropdown', [NotificationController::class, 'dropdown'])->name('notifications.dropdown');
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
        Route::get('/', [NotificationController::class, 'index'])->name('notifications.index');
    });
});

Route::get('/ganti_password', [ProfilController::class, 'changePassword']);

Route::post('/ganti_password', [ProfilController::class, 'processPassword']);

Route::get('/', function () {
    // Jika pengguna sudah login, arahkan ke /profil
    if (Auth::check()) {
        return redirect('profil');
    }
    return view('welcome');
});

Auth::routes();
