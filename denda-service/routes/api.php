<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DendaController;

/*
|--------------------------------------------------------------------------
| API Routes - DendaService (Port 8004)
|--------------------------------------------------------------------------
|
| PROVIDER: GET /api/dendas/member/{member_id} -> digunakan oleh MemberService
| CONSUMER: POST /api/dendas/hitung -> memanggil PinjamService
|
*/

Route::prefix('dendas')->group(function () {
    Route::get('/', [DendaController::class, 'index']);
    Route::post('/hitung', [DendaController::class, 'hitung']);
    Route::get('/member/{member_id}', [DendaController::class, 'byMember']);
    Route::patch('/{id}/bayar', [DendaController::class, 'bayar']);
});
