<?php

use App\Http\Controllers\Data\AksiTagihanController;
use App\Http\Controllers\Data\TagihanController;
use App\Http\Controllers\Layanan\AksiTransaksiController;
use App\Http\Controllers\Layanan\TransaksiController;
use App\Http\Controllers\Master\GolonganController;
use App\Http\Controllers\Master\PelangganController;
use App\Http\Controllers\Master\TahunController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->prefix('master')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('pelanggan', PelangganController::class);
    Route::resource('golongan-tarif', GolonganController::class);
    Route::resource('tahun', TahunController::class);
});

Route::middleware('auth')->prefix('layanan')->group(function () {
   Route::resource('tagihan', TagihanController::class); 
   Route::resource('aksi-tagihan', AksiTagihanController::class);

   Route::resource('transaksi', TransaksiController::class);
   Route::resource('aksi-transaksi', AksiTransaksiController::class);

});

require __DIR__.'/auth.php';
