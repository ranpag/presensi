<?php

namespace App\Http\Controllers;

use App\Models\JadwalPiket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Jadwal\StorePiketRequest;
use App\Http\Requests\Jadwal\UpdatePiketRequest;

class JadwalPiketController extends Controller
{
    public function index(Request $request)
    {
        $query = JadwalPiket::query();

        if ($request->has('search')) {
            $query->where('hari', 'like', '%' . $request->search . '%');
        }

        $query->with('guru');
        $jadwalPiket = $query->get();

        return response()->json([
            'success' => true,
            'message' => 'Data jadwal piket berhasil diambil.',
            'data' => $jadwalPiket
        ]);
    }

    public function store(StorePiketRequest $request)
    {
        $jadwalPiketId = DB::table('jadwal_piket')->insertGetId([
            'user_id' => $request->user_id,
            'mulai' => $request->mulai,
            'selesai' => $request->selesai,
            'tanggal' => $request->tanggal,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data jadwal piket berhasil ditambahkan.',
            'data' => JadwalPiket::find($jadwalPiketId)
        ], 201);
    }

    public function show($id)
    {
        $jadwalPiket = JadwalPiket::with('guru')->find($id);

        if (!$jadwalPiket) {
            return response()->json([
                'success' => false,
                'message' => 'Data jadwal piket tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data jadwal piket berhasil ditemukan.',
            'data' => $jadwalPiket
        ]);
    }

    public function update(UpdatePiketRequest $request, $id)
    {
        $jadwalPiket = JadwalPiket::find($id);

        if (!$jadwalPiket) {
            return response()->json([
                'success' => false,
                'message' => 'Data jadwal piket tidak ditemukan.',
            ], 404);
        }

        $data = array_filter([
            'user_id' => $request->user_id !== $jadwalPiket->user_id ? $request->user_id : null,
            'mulai' => $request->mulai !== $jadwalPiket->mulai ? $request->mulai : null,
            'selesai' => $request->selesai !== $jadwalPiket->selesai ? $request->selesai : null,
            'tanggal' => $request->tanggal !== $jadwalPiket->tanggal ? $request->tanggal : null,
        ], fn($value) => !is_null($value));

        if (empty($data)) {
            return response()->json([
                'success' => true,
                'message' => 'Tidak ada perubahan pada data jadwal piket.',
                'data' => $jadwalPiket
            ], 200);
        }

        DB::table('jadwal_piket')->where('id', $id)->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Data jadwal piket berhasil diperbarui.',
            'data' => JadwalPiket::find($id)
        ]);
    }

    public function destroy($id)
    {
        $deleted = DB::table('jadwal_piket')->where('id', $id)->delete();

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Data jadwal piket tidak ditemukan.',
            ], 404);
        }

        return response()->noContent();
    }

    public function destroyMany(Request $request)
    {
        $ids = $request->input('ids');

        if (!is_array($ids) || empty($ids) || !collect($ids)->every(fn($id) => is_numeric($id))) {
            return response()->json([
                'success' => false,
                'message' => 'Harap kirimkan array ID yang valid (hanya angka).',
                'errors' => ['ids' => 'Harap kirimkan array ID yang valid (hanya angka).']
            ], 400);
        }

        $deleted = DB::table('jadwal_piket')->whereIn('id', $ids)->delete();

        if ($deleted === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data jadwal piket yang ditemukan untuk dihapus.',
            ], 404);
        }

        return response()->noContent();
    }
}
