<?php

namespace App\Http\Controllers;

use App\Models\JadwalKBM;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Jadwal\StoreKbmRequest;
use App\Http\Requests\Jadwal\UpdateKbmRequest;

class JadwalKBMController extends Controller
{
    public function index(Request $request)
    {
        $query = JadwalKBM::query();

        if ($request->has('guru_id')) {
            $query->where('user_id', $request->guru_id);
        }

        if ($request->has('mapel_id')) {
            $query->where('mapel_id', $request->mapel_id);
        }

        if ($request->has('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        $query->with(['kelas', 'mapel', 'guru']);
        $jadwalKBM = $query->get();

        // response dengan format
        if ($request->has('pretty') && $request->pretty == 1) {
            $grouped = $jadwalKBM->groupBy('kelas_id')->map(function ($items) {
                return [
                    'kelas' => $items->first()->kelas,
                    'jadwal' => $items->groupBy('hari')->mapWithKeys(function ($jadwalPerHari, $hari) {
                        return [
                            $hari => $jadwalPerHari->sortBy('mulai')->map(function ($jadwal) {
                                return [
                                    'id' => $jadwal->id,
                                    'mulai' => $jadwal->mulai,
                                    'selesai' => $jadwal->selesai,
                                    'mapel' => $jadwal->mapel->nama ?? 'Tidak Ada',
                                    'guru' => $jadwal->guru->nama ?? 'Tidak Ada',
                                ];
                            })->values()
                        ];
                    }),
                ];
            })->values();

            return response()->json([
                'success' => true,
                'message' => 'Data jadwal KBM berhasil diambil dalam format terstruktur.',
                'data' => $grouped,
            ]);
        }

        // Default response tanpa formatting khusus
        return response()->json([
            'success' => true,
            'message' => 'Data jadwal KBM berhasil diambil.',
            'data' => $jadwalKBM,
        ]);
    }


    public function store(StoreKbmRequest $request)
    {
        $jadwalKBMId = DB::table('jadwal_kbm')->insertGetId([
            'kelas_id' => $request->kelas_id,
            'mapel_id' => $request->mapel_id,
            'user_id' => $request->user_id,
            'hari' => $request->hari,
            'mulai' => $request->mulai,
            'selesai' => $request->selesai,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data jadwal KBM berhasil ditambahkan.',
            'data' => JadwalKBM::find($jadwalKBMId)
        ], 201);
    }

    public function show($id)
    {
        $jadwalKBM = JadwalKBM::with(['kelas', 'mapel', 'guru'])->find($id);

        if (!$jadwalKBM) {
            return response()->json([
                'success' => false,
                'message' => 'Data jadwal KBM tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data jadwal KBM berhasil ditemukan.',
            'data' => $jadwalKBM
        ]);
    }

    public function update(UpdateKbmRequest $request, $id)
    {
        $jadwalKBM = JadwalKBM::find($id);

        if (!$jadwalKBM) {
            return response()->json([
                'success' => false,
                'message' => 'Data jadwal KBM tidak ditemukan.',
            ], 404);
        }

        $data = array_filter([
            'kelas_id' => $request->kelas_id !== $jadwalKBM->kelas_id ? $request->kelas_id : null,
            'mapel_id' => $request->mapel_id !== $jadwalKBM->mapel_id ? $request->mapel_id : null,
            'user_id' => $request->user_id !== $jadwalKBM->user_id ? $request->user_id : null,
            'hari' => $request->hari !== $jadwalKBM->hari ? $request->hari : null,
            'mulai' => $request->mulai !== $jadwalKBM->mulai ? $request->mulai : null,
            'selesai' => $request->selesai !== $jadwalKBM->selesai ? $request->selesai : null,
        ], fn($value) => !is_null($value));

        if (empty($data)) {
            return response()->json([
                'success' => true,
                'message' => 'Tidak ada perubahan pada data jadwal KBM.',
                'data' => $jadwalKBM
            ], 200);
        }

        DB::table('jadwal_kbm')->where('id', $id)->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Data jadwal KBM berhasil diperbarui.',
            'data' => JadwalKBM::find($id)
        ]);
    }

    public function destroy($id)
    {
        $deleted = DB::table('jadwal_kbm')->where('id', $id)->delete();

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Data jadwal KBM tidak ditemukan.',
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

        $deleted = DB::table('jadwal_kbm')->whereIn('id', $ids)->delete();

        if ($deleted === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data jadwal KBM yang ditemukan untuk dihapus.',
            ], 404);
        }

        return response()->noContent();
    }
}
