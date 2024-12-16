<?php

use App\Http\Controllers\Data\AksiTagihanController;
use App\Http\Controllers\Data\TagihanController;
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
use App\Http\Controllers\Setting\RoleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::post('transaksi/handle-notification', [MidtransController::class, 'handleNotification'])->name('transaksi.handleNotification')->withoutMiddleware(['auth', 'verified']);


Route::middleware(['auth', 'CheckUserRole'])->prefix('master')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('pelanggan', PelangganController::class);
    Route::resource('golongan-tarif', GolonganController::class);
    Route::resource('tahun', TahunController::class);
});

Route::middleware(['auth', 'CheckUserRole'])->prefix('layanan')->group(function () {
   Route::resource('tagihan', TagihanController::class); 
   Route::resource('aksi-tagihan', AksiTagihanController::class);

   Route::resource('transaksi', TransaksiController::class);
   Route::resource('aksi-transaksi', AksiTransaksiController::class);

   Route::Post('transaksi/create-snap-token', [MidtransController::class, 'createSnapToken'])->name('transaksi.createsnaptoken');
   Route::Post('transaksi/update-database', [MidtransController::class, 'updateDatabase'])->name('transaksi.updateDatabase');


});

Route::middleware(['auth', 'CheckUserRole'])->prefix('laporan')->group(function () {
    Route::get('laporan-pelanggan', [LaporanPenggunaController::class, 'index'])->name('laporan-pelanggan.index'); 
    Route::post('laporan-pelanggan/export-pdf', [LaporanPenggunaController::class, 'exportPdf'])->name('laporan-pelanggan.exportPdf');

    Route::get('laporan-tagihan', [LaporanTagihanController::class, 'index'])->name('laporan-tagihan.index'); 
    Route::post('laporan-tagihan/export-pdf', [LaporanTagihanController::class, 'exportPdf'])->name('laporan-tagihan.exportPdf');
});

Route::middleware(['auth', 'CheckUserRole'])->prefix('import')->group(function () {
    Route::get('import-pelanggan', [ImportPelangganController::class, 'index'])->name('import-pelanggan.index');
    Route::Post('import-pelanggan/store', [ImportPelangganController::class, 'store'])->name('import-pelanggan.store');
});

Route::middleware(['auth', 'CheckUserRole'])->prefix('setting')->group(function () {
    Route::resource('pengguna-aplikasi', PenggunaAplikasiController::class);
    Route::resource('menu-aplikasi', MenuController::class);
    Route::resource('role-aplikasi', RoleController::class);
});

require __DIR__.'/auth.php';
