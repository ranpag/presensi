<?php

namespace App\Http\Controllers;

use App\Models\TanggalMerah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\TanggalMerah\StoreTanggalMerahRequest;
use App\Http\Requests\TanggalMerah\UpdateTanggalMerahRequest;

class TanggalMerahController extends Controller
{
    public function index()
    {
        $tanggalMerah = TanggalMerah::all();
        return response()->json([
            'success' => true,
            'message' => 'Tanggal merah berhasil diambil',
            'data' => $tanggalMerah
        ]);
    }

    public function store(StoreTanggalMerahRequest $request)
    {
        $tanggalMerahId = DB::table('tanggal_merah')->insertGetId([
            'mulai' => $request->mulai,
            'selesai' => $request->selesai,
            'deskripsi' => $request->deskripsi,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tanggal merah berhasil ditambahkan.',
            'data' => TanggalMerah::find($tanggalMerahId)
        ], 201);
    }

    public function update(UpdateTanggalMerahRequest $request, $id)
    {
        $tanggalMerah = TanggalMerah::find($id);

        if (!$tanggalMerah) {
            return response()->json([
                'success' => false,
                'message' => 'Data tanggal merah tidak ditemukan.',
            ], 404);
        }

        $data = array_filter([
            'mulai' => $request->mulai !== $tanggalMerah->mulai ? $request->mulai : null,
            'selesai' => $request->selesai !== $tanggalMerah->selesai ? $request->selesai : null,
            'deskripsi' => $request->deskripsi !== $tanggalMerah->deskripsi ? $request->deskripsi : null,
        ], fn($value) => !is_null($value));

        if (empty($data)) {
            return response()->json([
                'success' => true,
                'message' => 'Tidak ada perubahan pada data tanggal merah.',
                'data' => $tanggalMerah
            ], 200);
        }

        DB::table('tanggal_merah')->where('id', $id)->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Data tanggal merah berhasil diperbarui.',
            'data' => TanggalMerah::find($id)
        ]);
    }


    public function show($id)
    {
        $tanggalMerah = TanggalMerah::find($id);

        if (!$tanggalMerah) {
            return response()->json([
                'success' => false,
                'message' => 'Data tanggal merah tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Tanggal merah berhasil diambil',
            'data' => $tanggalMerah
        ]);
    }

    public function destroy($id)
    {
        $deleted = DB::table('tanggal_merah')->where('id', $id)->delete();

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Data siswa tidak ditemukan.',
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

        $deleted = DB::table('tanggal_merah')->whereIn('id', $ids)->delete();

        if ($deleted === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data tanggal merah yang ditemukan untuk dihapus.',
            ], 404);
        }

        return response()->noContent();
    }
}

