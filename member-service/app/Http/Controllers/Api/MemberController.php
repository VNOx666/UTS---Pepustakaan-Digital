<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

/**
 * MemberController
 * Service: MemberService (Port 8001)
 *
 * PROVIDER: Menyediakan data member untuk PinjamService
 * CONSUMER: Memanggil PinjamService & DendaService untuk profil member
 */
class MemberController extends Controller
{
    // ===================================================
    // PROVIDER ENDPOINTS
    // ===================================================

    /**
     * Menampilkan semua data member
     * GET /api/members
     */
    public function index()
    {
        $members = Member::all();
        return response()->json([
            'success' => true,
            'message' => 'Daftar semua member',
            'data' => $members,
        ], 200);
    }

    /**
     * Menyimpan data member baru
     * POST /api/members
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_member' => 'required|string|max:255',
            'nim' => 'required|string|unique:members,nim',
            'email' => 'required|email|unique:members,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        $member = Member::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Member berhasil ditambahkan',
            'data' => $member,
        ], 201);
    }

    /**
     * Menampilkan detail member berdasarkan ID
     * GET /api/members/{id}
     *
     * Endpoint ini digunakan oleh PinjamService untuk validasi member
     */
    public function show($id)
    {
        $member = Member::find($id);

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Member tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail member',
            'data' => $member,
        ], 200);
    }

    // ===================================================
    // CONSUMER ENDPOINT
    // ===================================================

    /**
     * Menampilkan profil lengkap member beserta riwayat pinjam & denda
     * GET /api/members/{id}/profile
     *
     * Consumer: Memanggil PinjamService dan DendaService
     */
    public function profile($id)
    {
        // 1. Ambil data member dari database lokal
        $member = Member::find($id);

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Member tidak ditemukan',
            ], 404);
        }

        // 2. Ambil riwayat peminjaman dari PinjamService (Consumer)
        $riwayatPinjam = [];
        try {
            $pinjamUrl = config('services.pinjam_service.url') . "/api/pinjams/member/{$id}";
            $responsePinjam = Http::timeout(5)->get($pinjamUrl);

            if ($responsePinjam->successful()) {
                $riwayatPinjam = $responsePinjam->json()['data'] ?? [];
            }
        } catch (\Exception $e) {
            $riwayatPinjam = ['error' => 'PinjamService tidak tersedia: ' . $e->getMessage()];
        }

        // 3. Ambil data denda dari DendaService (Consumer)
        $dataDenda = [];
        try {
            $dendaUrl = config('services.denda_service.url') . "/api/dendas/member/{$id}";
            $responseDenda = Http::timeout(5)->get($dendaUrl);

            if ($responseDenda->successful()) {
                $dataDenda = $responseDenda->json()['data'] ?? [];
            }
        } catch (\Exception $e) {
            $dataDenda = ['error' => 'DendaService tidak tersedia: ' . $e->getMessage()];
        }

        // 4. Gabungkan semua data untuk profil
        return response()->json([
            'success' => true,
            'message' => 'Profil lengkap member',
            'data' => [
                'member' => $member,
                'riwayat_pinjam' => $riwayatPinjam,
                'denda' => $dataDenda,
            ],
        ], 200);
    }
}
