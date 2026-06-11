<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BukuController;

/*
|--------------------------------------------------------------------------
| API Routes - BukuService (Port 8002)
|--------------------------------------------------------------------------
|
| PROVIDER: GET /api/bukus/{id} -> digunakan oleh PinjamService
| CONSUMER: GET /api/bukus/{id}/histori-peminjam -> memanggil PinjamService
|
*/

Route::prefix('bukus')->group(function () {
    Route::get('/', [BukuController::class, 'index']);
    Route::post('/', [BukuController::class, 'store']);
    Route::get('/{id}', [BukuController::class, 'show']);
    Route::patch('/{id}/stok', [BukuController::class, 'updateStok']);
    Route::get('/{id}/histori-peminjam', [BukuController::class, 'historiPeminjam']);
});
