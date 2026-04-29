<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MemberController;

/*
|--------------------------------------------------------------------------
| API Routes - MemberService (Port 8001)
|--------------------------------------------------------------------------
|
| PROVIDER: GET /api/members/{id} -> digunakan oleh PinjamService
| CONSUMER: GET /api/members/{id}/profile -> memanggil PinjamService & DendaService
|
*/

Route::prefix('members')->group(function () {
    Route::get('/', [MemberController::class, 'index']);
    Route::post('/', [MemberController::class, 'store']);
    Route::get('/{id}', [MemberController::class, 'show']);
    Route::get('/{id}/profile', [MemberController::class, 'profile']);
});
