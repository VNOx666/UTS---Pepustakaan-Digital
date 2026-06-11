<?php

namespace App\Http\Controllers\Api;

use App\Jobs\UpdateStokJob;
use App\Http\Controllers\Controller;
use App\Models\Pinjam;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PinjamController extends Controller
{
    public function index()
    {
        $pinjams = Pinjam::all();

        return response()->json([
            'success' => true,
            'message' => 'Daftar semua transaksi peminjaman',
            'data' => $pinjams,
        ], 200);
    }

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

    public function byMember($member_id)
    {
        $pinjams = Pinjam::where('member_id', $member_id)->get();

        return response()->json([
            'success' => true,
            'message' => "Riwayat peminjaman untuk member ID: {$member_id}",
            'data' => $pinjams,
        ], 200);
    }

    public function byBuku($buku_id)
    {
        $pinjams = Pinjam::where('buku_id', $buku_id)->get();

        return response()->json([
            'success' => true,
            'message' => "Daftar peminjaman untuk buku ID: {$buku_id}",
            'data' => $pinjams,
        ], 200);
    }

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
            'status' => 'dipinjam',
        ]);

        UpdateStokJob::dispatch($pinjam->buku_id, 'pinjam')
            ->onConnection('rabbitmq')
            ->onQueue('stok-buku');

        return response()->json([
            'success' => true,
            'message' => 'Transaksi peminjaman berhasil dibuat',
            'data' => $pinjam,
        ], 201);
    }

    public function kembalikan(Request $request, $id)
    {
        return $this->kembali($request, $id);
    }

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

        UpdateStokJob::dispatch($pinjam->buku_id, 'kembali')
            ->onConnection('rabbitmq')
            ->onQueue('stok-buku');

        return response()->json([
            'success' => true,
            'message' => 'Buku berhasil dikembalikan',
            'data' => $pinjam,
        ], 200);
    }
}
