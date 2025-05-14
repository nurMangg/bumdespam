<?php

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\Lapangan\getDataPelangganController;
use App\Http\Controllers\Api\Lapangan\getLastTagihanController;
use App\Http\Controllers\Api\Lapangan\StoreInputTagihanController;
use App\Http\Controllers\Api\TagihanApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthApiController::class, 'login']);

    // Lapangan
    Route::get('/getPelanggan/{id}', [getDataPelangganController::class, 'index']);
    Route::get('/getLastTagihan/{id}', [getLastTagihanController::class, 'index']);
    Route::post('/storeInputTagihan', [StoreInputTagihanController::class, 'store']);
});

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user()->load('pelanggan');
    });

    // Route::get('/user/{id}', [AuthApiController::class, 'getUser']);

    Route::get('/dashboard', [DashboardApiController::class, 'index']);
    Route::get('/tagihan', [TagihanApiController::class, 'index']);

    Route::post('/logout', [AuthApiController::class, 'logout']);
});