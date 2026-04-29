<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pinjam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

/**
 * PinjamController
 * Service: PinjamService
 *
 * PROVIDER: Menyediakan data transaksi peminjaman untuk DendaService dan MemberService
 */
class PinjamController extends Controller
{
    /**
     * Menampilkan semua data peminjaman
     * GET /api/pinjams
     */
    public function index()
    {
        $pinjams = Pinjam::all();

        return response()->json([
            'success' => true,
            'message' => 'Daftar semua transaksi peminjaman',
            'data' => $pinjams,
        ], 200);
    }

    /**
     * Menampilkan detail peminjaman berdasarkan ID
     * GET /api/pinjams/{id}
     */
    public function show($id)
    {
        $pinjam = Pinjam::find($id);

        if (!$pinjam) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi peminjaman tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail peminjaman',
            'data' => $pinjam,
        ], 200);
    }

    /**
     * Menampilkan daftar peminjaman berdasarkan member_id
     * GET /api/pinjams/member/{member_id}
     */
    public function byMember($member_id)
    {
        $pinjams = Pinjam::where('member_id', $member_id)->get();

        return response()->json([
            'success' => true,
            'message' => "Riwayat peminjaman untuk member ID: {$member_id}",
            'data' => $pinjams,
        ], 200);
    }

    /**
     * Menampilkan daftar peminjaman berdasarkan buku_id
     * GET /api/pinjams/buku/{buku_id}
     */
    public function byBuku($buku_id)
    {
        $pinjams = Pinjam::where('buku_id', $buku_id)->get();

        return response()->json([
            'success' => true,
            'message' => "Daftar peminjaman untuk buku ID: {$buku_id}",
            'data' => $pinjams,
        ], 200);
    }

    /**
     * Membuat transaksi peminjaman baru
     * POST /api/pinjams
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'buku_id' => 'required|integer',
            'member_id' => 'required|integer',
            'tgl_pinjam' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        $pinjam = Pinjam::create([
            'buku_id' => $request->buku_id,
            'member_id' => $request->member_id,
            'tgl_pinjam' => $request->tgl_pinjam,
            'tgl_kembali' => null,
            'status' => 'dipinjam',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Transaksi peminjaman berhasil dibuat',
            'data' => $pinjam,
        ], 201);
    }

    /**
     * Alias untuk route yang memanggil kembalikan()
     * PATCH /api/pinjams/{id}/kembalikan
     */
    public function kembalikan(Request $request, $id)
    {
        return $this->kembali($request, $id);
    }

    /**
     * Proses pengembalian buku
     * PATCH /api/pinjams/{id}/kembali
     */
    public function kembali(Request $request, $id)
    {
        $pinjam = Pinjam::find($id);

        if (!$pinjam) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi peminjaman tidak ditemukan',
            ], 404);
        }

        if ($pinjam->tgl_kembali !== null) {
            return response()->json([
                'success' => false,
                'message' => 'Buku pada transaksi ini sudah dikembalikan',
            ], 400);
        }

        $pinjam->update([
            'tgl_kembali' => Carbon::now()->toDateString(),
            'status' => 'dikembalikan',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Buku berhasil dikembalikan',
            'data' => $pinjam,
        ], 200);
    }
}