<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

/**
 * BukuController
 * Service: BukuService (Port 8002)
 *
 * PROVIDER: Menyediakan data buku dan validasi stok untuk PinjamService
 * CONSUMER: Memanggil PinjamService untuk histori peminjam buku
 */
class BukuController extends Controller
{
    // ===================================================
    // PROVIDER ENDPOINTS
    // ===================================================

    /**
     * Menampilkan semua data buku (katalog)
     * GET /api/bukus
     */
    public function index()
    {
        $bukus = Buku::all();
        return response()->json([
            'success' => true,
            'message' => 'Katalog buku',
            'data' => $bukus,
        ], 200);
    }

    /**
     * Menyimpan data buku baru
     * POST /api/bukus
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'pengarang' => 'required|string|max:255',
            'stok' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        $buku = Buku::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Buku berhasil ditambahkan',
            'data' => $buku,
        ], 201);
    }

    /**
     * Menampilkan detail buku berdasarkan ID
     * GET /api/bukus/{id}
     *
     * Endpoint ini digunakan oleh PinjamService untuk validasi buku & stok
     */
    public function show($id)
    {
        $buku = Buku::find($id);

        if (!$buku) {
            return response()->json([
                'success' => false,
                'message' => 'Buku tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail buku',
            'data' => $buku,
        ], 200);
    }

    /**
     * Update stok buku
     * PATCH /api/bukus/{id}/stok
     */
    public function updateStok(Request $request, $id)
    {
        $buku = Buku::find($id);

        if (!$buku) {
            return response()->json([
                'success' => false,
                'message' => 'Buku tidak ditemukan',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'stok' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        $buku->update(['stok' => $request->stok]);

        return response()->json([
            'success' => true,
            'message' => 'Stok buku berhasil diupdate',
            'data' => $buku,
        ], 200);
    }

    // ===================================================
    // CONSUMER ENDPOINT
    // ===================================================

    /**
     * Menampilkan histori peminjam buku tertentu
     * GET /api/bukus/{id}/histori-peminjam
     *
     * Consumer: Memanggil PinjamService untuk data peminjaman buku
     */
    public function historiPeminjam($id)
    {
        // 1. Cek buku ada di database lokal
        $buku = Buku::find($id);

        if (!$buku) {
            return response()->json([
                'success' => false,
                'message' => 'Buku tidak ditemukan',
            ], 404);
        }

        // 2. Ambil data peminjaman buku dari PinjamService (Consumer)
        $historiPinjam = [];
        try {
            $pinjamUrl = config('services.pinjam_service.url') . "/api/pinjams/buku/{$id}";
            $responsePinjam = Http::timeout(5)->get($pinjamUrl);

            if ($responsePinjam->successful()) {
                $historiPinjam = $responsePinjam->json()['data'] ?? [];
            }
        } catch (\Exception $e) {
            $historiPinjam = ['error' => 'PinjamService tidak tersedia: ' . $e->getMessage()];
        }

        return response()->json([
            'success' => true,
            'message' => 'Histori peminjam buku',
            'data' => [
                'buku' => $buku,
                'histori_peminjam' => $historiPinjam,
            ],
        ], 200);
    }
}
