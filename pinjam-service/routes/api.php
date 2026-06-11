<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PinjamController;

/*
|--------------------------------------------------------------------------
| API Routes - PinjamService (Port 8003)
|--------------------------------------------------------------------------
|
| PROVIDER: GET /api/pinjams/{id}, /member/{id}, /buku/{id}
| CONSUMER: POST /api/pinjams -> memanggil MemberService & BukuService
|
*/

Route::prefix('pinjams')->group(function () {
    Route::get('/', [PinjamController::class, 'index']);
    Route::post('/', [PinjamController::class, 'store']);
    Route::get('/{id}', [PinjamController::class, 'show']);
    Route::patch('/{id}/kembali', [PinjamController::class, 'kembalikan']);
    Route::get('/member/{member_id}', [PinjamController::class, 'byMember']);
    Route::get('/buku/{buku_id}', [PinjamController::class, 'byBuku']);
});
