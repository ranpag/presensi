<?php

namespace App\Http\Controllers;

use App\Models\Mapel;
use App\Models\JadwalKBM;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Mapel\StoreMapelRequest;
use App\Http\Requests\Mapel\UpdateMapelRequest;

class MapelController extends Controller
{
    public function index(Request $request)
    {
        $query = Mapel::query();

        if ($request->has('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }

        $mapel = $query->get();

        return response()->json([
            'success' => true,
            'message' => 'Data mapel berhasil diambil.',
            'data' => $mapel
        ]);
    }

    public function store(StoreMapelRequest $request)
    {
        $mapel = DB::table('mapel')->insertGetId([
            'nama' => $request->nama,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Data mapel berhasil ditambahkan.',
            'data' => Mapel::find($mapel)
        ], 201);
    }

    public function show($id)
    {
        $mapel = Mapel::find($id);

        if (!$mapel) {
            return response()->json([
                'success' => false,
                'message' => 'Data mapel tidak ditemukan.',
            ], 404);
        }

        // Ambil jadwal KBM dan format berdasarkan hari
        $jadwalKBM = JadwalKBM::where('mapel_id', $id)
            ->with(['kelas', 'guru'])
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
            ->orderBy('mulai', 'asc')
            ->get();

        $jadwalTerformat = [];

        foreach ($jadwalKBM as $jadwal) {
            $hari = $jadwal->hari;
            $jadwalTerformat[$hari][] = [
                'id' => $jadwal->id,
                'kelas' => [
                    'id' => $jadwal->kelas->id,
                    'nama' => $jadwal->kelas->nama
                ],
                'guru' => [
                    'id' => $jadwal->guru->id,
                    'nama' => $jadwal->guru->nama
                ],
                'mulai' => $jadwal->mulai,
                'selesai' => $jadwal->selesai
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Data mapel berhasil ditemukan.',
            'data' => [
                'id' => $mapel->id,
                'nama' => $mapel->nama,
                'jadwal' => $jadwalTerformat
            ]
        ]);
    }

    public function update(UpdateMapelRequest $request, $id)
    {
        $mapel = Mapel::find($id);

        if (!$mapel) {
            return response()->json([
                'success' => false,
                'message' => 'Data mapel tidak ditemukan.',
            ], 404);
        }

        // Filter hanya data yang ada dan berbeda dari nilai saat ini
        $data = array_filter([
            'nama' => $request->nama !== $mapel->nama ? $request->nama : null,
        ], fn($value) => !is_null($value));

        // Jika tidak ada perubahan, tetap kembalikan response 200
        if (empty($data)) {
            return response()->json([
                'success' => true,
                'message' => 'Tidak ada perubahan pada data mapel.',
                'data' => $mapel
            ], 200);
        }

        DB::table('mapel')->where('id', $id)->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Data mapel berhasil diperbarui.',
            'data' => Mapel::find($id)
        ]);
    }

    public function destroy($id)
    {
        $deleted = DB::table('mapel')->where('id', $id)->delete();
            
        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Data mapel tidak ditemukan.',
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
                'errors' =>  ['ids' => 'Harap kirimkan array ID yang valid (hanya angka).']
            ], 400);
        }

        $deleted = DB::table('mapel')->whereIn('id', $ids)->delete();

        if ($deleted === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data mapel yang ditemukan untuk dihapus.',
            ], 404);
        }

        return response()->noContent();
    }
}
