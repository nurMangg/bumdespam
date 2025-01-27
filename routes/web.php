<?php

use App\Http\Controllers\Data\AksiTagihanController;
use App\Http\Controllers\Data\InputTagihanController;
use App\Http\Controllers\Data\TagihanController;
use App\Http\Controllers\Import\ImportDataController;
use App\Http\Controllers\Import\ImportPelangganController;
use App\Http\Controllers\Import\ImportPenggunaController;
use App\Http\Controllers\Laporan\LaporanPenggunaController;
use App\Http\Controllers\Laporan\LaporanTagihanController;
use App\Http\Controllers\Layanan\AksiTransaksiController;
use App\Http\Controllers\Layanan\MidtransController;
use App\Http\Controllers\Layanan\TransaksiController;
use App\Http\Controllers\Master\GolonganController;
use App\Http\Controllers\Master\PelangganController;
use App\Http\Controllers\Master\TahunController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Setting\MenuController;
use App\Http\Controllers\Setting\PenggunaAplikasiController;
use App\Http\Controllers\Setting\ResetPasswordController;
use App\Http\Controllers\Setting\RiwayatController;
use App\Http\Controllers\Setting\RoleController;
use App\Http\Controllers\Setting\SettingPenggunaController;
use App\Http\Controllers\Setting\WebController;
use App\Models\Roles;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/dashboard', function () {
    if (Auth::user()->userRoleId == Roles::where('roleName', 'pelanggan')->first()->roleId) {
        return view('pelanggan.dashboard');

    }
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::post('transaksi/handle-notification', [MidtransController::class, 'handleNotification'])->name('transaksi.handleNotification')->withoutMiddleware(['auth', 'verified']);


Route::middleware(['auth', 'CheckUserRole'])->prefix('master')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('pelanggan', PelangganController::class);
    Route::get('pelanggan/ViewKartu/{pelanggan}', [PelangganController::class, 'viewKartu'])->name('pelanggan.viewKartu');
    Route::resource('golongan-tarif', GolonganController::class);
    Route::resource('tahun', TahunController::class);
});

Route::middleware(['auth', 'CheckUserRole'])->prefix('layanan')->group(function () {
   Route::resource('tagihan', TagihanController::class); 
   Route::resource('aksi-tagihan', AksiTagihanController::class);

   Route::resource('transaksi', TransaksiController::class);
   Route::resource('aksi-transaksi', AksiTransaksiController::class);

   Route::Post('transaksi/pembayaran-tunai', [AksiTransaksiController::class, 'pembayaranTunai'])->name('transaksi.pembayarantunai');
   Route::get('transaksi/unduh-struk/{id}', [TransaksiController::class, 'unduhStruk'])->name('transaksi.struk');


   Route::Post('transaksi/create-snap-token', [MidtransController::class, 'createSnapToken'])->name('transaksi.createsnaptoken');
   Route::Post('transaksi/update-database', [MidtransController::class, 'updateDatabase'])->name('transaksi.updateDatabase');


});

Route::resource('input-tagihan', InputTagihanController::class);
Route::post('input-tagihan/scanqrcode', [InputTagihanController::class, 'scanQRCode'])->name('input-tagihan.scanqrcode');

// Route::middleware(['auth', 'CheckUserRole'])->prefix('input-tagihan')->group(function () {
//     Route::resource('input-tagihan', InputTagihanController::class);
// });

Route::middleware(['auth', 'CheckUserRole'])->prefix('laporan')->group(function () {
    Route::get('laporan-pelanggan', [LaporanPenggunaController::class, 'index'])->name('laporan-pelanggan.index'); 
    Route::post('laporan-pelanggan/export-pdf', [LaporanPenggunaController::class, 'exportPdf'])->name('laporan-pelanggan.exportPdf');

    Route::get('laporan-tagihan', [LaporanTagihanController::class, 'index'])->name('laporan-tagihan.index'); 
    Route::post('laporan-tagihan/export-pdf', [LaporanTagihanController::class, 'exportPdf'])->name('laporan-tagihan.exportPdf');
});

Route::middleware(['auth', 'CheckUserRole'])->prefix('import')->group(function () {
    Route::get('import-pelanggan', [ImportPelangganController::class, 'index'])->name('import-pelanggan.index');
    Route::Post('import-pelanggan/store', [ImportPelangganController::class, 'store'])->name('import-pelanggan.store');

    Route::get('import-data-tagihan', [ImportDataController::class, 'index'])->name('import-data-tagihan.index');
    Route::Post('import-data-tagihan/store', [ImportDataController::class, 'store'])->name('import-data-tagihan.store');
});

Route::middleware(['auth', 'CheckUserRole'])->prefix('setting')->group(function () {
    Route::resource('pengguna-aplikasi', PenggunaAplikasiController::class);
    Route::resource('menu-aplikasi', MenuController::class);
    Route::resource('role-aplikasi', RoleController::class);
    Route::resource('setting-pengguna', SettingPenggunaController::class);

    Route::resource('setting-web', WebController::class);
    Route::get('riwayat-website', [RiwayatController::class, 'index'])->name('riwayat-website.index');

    Route::get('reset-password', [ResetPasswordController::class, 'index'])->name('reset-password.index');
    Route::post('reset-password/{id}', [ResetPasswordController::class, 'resetPassword'])->name('reset-password.resetPassword');
});

require __DIR__.'/auth.php';
