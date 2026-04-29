<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Denda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class DendaController extends Controller
{
    /**
     * GET /api/dendas
     */
    public function index()
    {
        $dendas = Denda::all();

        if ($dendas->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'Belum ada data denda yang tercatat di database DendaService.',
                'data' => []
            ], 200);
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil semua daftar denda.',
            'data' => $dendas,
        ], 200);
    }

    /**
     * GET /api/dendas/member/{member_id}
     */
    public function byMember($member_id)
    {
        try {
            // 1. Cek dulu apakah Member ID ini ada di MemberService?
            $memberUrl = env('MEMBER_SERVICE_URL') . "/api/members/{$member_id}";
            $resMember = Http::get($memberUrl);

            if ($resMember->failed()) {
                return response()->json([
                    'success' => false,
                    'message' => "Gagal: Member dengan ID {$member_id} tidak ditemukan di MemberService."
                ], 404);
            }

            // 2. Ambil semua pinjaman member ini dari PinjamService
            $pinjamUrl = env('PINJAM_SERVICE_URL') . "/api/pinjams/member/{$member_id}";
            $resPinjam = Http::get($pinjamUrl);
            $pinjams = $resPinjam->json()['data'] ?? [];

            if (empty($pinjams)) {
                return response()->json([
                    'success' => true,
                    'message' => "Member ditemukan, tapi ID {$member_id} belum pernah melakukan peminjaman buku.",
                    'data' => []
                ], 200);
            }

            // 3. Cari dendanya di database lokal
            $pinjamIds = array_column($pinjams, 'id');
            $dendas = Denda::whereIn('pinjam_id', $pinjamIds)->get();

            if ($dendas->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => "Member ID {$member_id} memiliki riwayat pinjam, tapi tidak ada denda (semua kembali tepat waktu).",
                    'data' => []
                ], 200);
            }

            return response()->json(['success' => true, 'data' => $dendas], 200);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan koneksi antar service.'], 500);
        }
    }

    /**
     * POST /api/dendas/hitung
     */
    public function hitung(Request $request)
    {
        $validator = Validator::make($request->all(), ['pinjam_id' => 'required|integer']);
        if ($validator->fails()) return response()->json(['success' => false, 'errors' => $validator->errors()], 422);

        try {
            // 1. Cek apakah ID Pinjam ada di PinjamService?
            $url = env('PINJAM_SERVICE_URL') . "/api/pinjams/{$request->pinjam_id}";
            $response = Http::get($url);

            if ($response->failed()) {
                return response()->json([
                    'success' => false,
                    'message' => "Gagal Hitung: ID Pinjam {$request->pinjam_id} tidak ditemukan di database PinjamService."
                ], 404);
            }

            $pinjamData = $response->json()['data'];

            // 2. Logika Hitung (Sama seperti sebelumnya)
            $tglPinjam = Carbon::parse($pinjamData['tgl_pinjam'])->startOfDay();
            $batasKembali = $tglPinjam->copy()->addDays(7);
            $tglSelesai = $pinjamData['tgl_kembali'] ? Carbon::parse($pinjamData['tgl_kembali'])->startOfDay() : Carbon::now()->startOfDay();

            $nominalDenda = 0;
            $statusBayar = 'lunas';
            if ($tglSelesai->gt($batasKembali)) {
                $nominalDenda = $tglSelesai->diffInDays($batasKembali, true) * 2000;
                $statusBayar = 'belum_bayar';
            }

            // 3. Update atau Create
            $denda = Denda::updateOrCreate(
                ['pinjam_id' => $request->pinjam_id],
                ['nominal_denda' => (int)$nominalDenda, 'status_bayar' => $statusBayar]
            );

            return response()->json(['success' => true, 'message' => 'Denda berhasil diproses.', 'data' => $denda], 200);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'PinjamService Offline.'], 503);
        }
    }

    /**
     * PATCH /api/dendas/pinjam/{pinjam_id}/bayar
     */
    public function bayar($pinjam_id)
    {
        $denda = Denda::where('pinjam_id', $pinjam_id)->first();

        if (!$denda) {
            // Kita cek ke PinjamService, ID-nya ada nggak?
            $resPinjam = Http::get(env('PINJAM_SERVICE_URL') . "/api/pinjams/{$pinjam_id}");

            if ($resPinjam->failed()) {
                return response()->json([
                    'success' => false,
                    'message' => "Gagal Bayar: ID Pinjam {$pinjam_id} sama sekali tidak ada di database perpustakaan."
                ], 404);
            }

            return response()->json([
                'success' => false,
                'message' => "Gagal Bayar: Transaksi ID {$pinjam_id} ditemukan, tapi denda belum dihitung/tidak ada denda."
            ], 404);
        }

        if ($denda->status_bayar === 'lunas') {
            return response()->json(['success' => false, 'message' => 'Denda ini sudah lunas sebelumnya.'], 400);
        }

        $denda->update(['status_bayar' => 'lunas']);
        return response()->json(['success' => true, 'message' => 'Denda berhasil dibayar.', 'data' => $denda], 200);
    }
}