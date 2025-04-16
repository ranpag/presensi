<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\JadwalKBM;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Kelas\StoreKelasRequest;
use App\Http\Requests\Kelas\UpdateKelasRequest;

class KelasController extends Controller
{
    public function index(Request $request)
    {
        $query = Kelas::query();

        if ($request->has('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }

        $query->with(['walas']);
        $kelas = $query->get();

        return response()->json([
            'success' => true,
            'message' => 'Data kelas berhasil diambil.',
            'data' => $kelas
        ]);
    }

    public function store(StoreKelasRequest $request)
    {
        $walasBentrok = Kelas::where('user_id', $request->user_id)
            ->exists();

        if ($walasBentrok) {
            return response()->json([
                'success' => false,
                'message' => 'Guru tersebut telah menjadi wali kelas di kelas yang lain.'
            ], 409);
        }

        $kelas = DB::table('kelas')->insertGetId([
            'nama' => $request->nama,
            'tingkatan' => $request->tingkatan,
            'user_id' => $request->user_id,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Data kelas berhasil ditambahkan.',
            'data' => Kelas::find($kelas)
        ], 201);
    }

    public function show($id)
    {
        $kelas = Kelas::with(['siswa', 'walas'])->find($id);

        if (!$kelas) {
            return response()->json([
                'success' => false,
                'message' => 'Data kelas tidak ditemukan.',
            ], 404);
        }

        // Ambil jadwal KBM dan format berdasarkan hari
        $jadwalKBM = JadwalKBM::where('kelas_id', $id)
            ->with(['mapel', 'guru'])
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
            ->orderBy('mulai', 'asc')
            ->get();

        $jadwalTerformat = [];

        foreach ($jadwalKBM as $jadwal) {
            $hari = $jadwal->hari;
            $jadwalTerformat[$hari][] = [
                'id' => $jadwal->id,
                'mapel' => [
                    'id' => $jadwal->mapel->id,
                    'nama' => $jadwal->mapel->nama
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
            'message' => 'Data kelas berhasil ditemukan.',
            'data' => [
                'id' => $kelas->id,
                'nama' => $kelas->nama,
                'tingkatan' => $kelas->tingkatan,
                'walas' => [
                    'id' => $kelas->walas->id,
                    'nama' => $kelas->walas->nama
                ],
                'siswa' => $kelas->siswa,
                'jadwal' => $jadwalTerformat
            ]
        ]);
    }

    public function update(UpdateKelasRequest $request, $id)
    {
        $kelas = Kelas::find($id);

        if (!$kelas) {
            return response()->json([
                'success' => false,
                'message' => 'Data kelas tidak ditemukan.',
            ], 404);
        }

        if ($request->has("user_id")) {
            $walasBentrok = Kelas::where('user_id', $request->user_id)
                ->where("id", "!=", $id)
                ->exists();

            if ($walasBentrok) {
                return response()->json([
                    'success' => false,
                    'message' => 'Guru tersebut telah menjadi wali kelas di kelas yang lain.'
                ], 409);
            }
        }

        $data = array_filter([
            'nama' => $request->nama !== $kelas->nama ? $request->nama : null,
            'tingkatan' => $request->tingkatan !== $kelas->tingkatan ? $request->tingkatan : null,
            'user_id' => $request->user_id !== $kelas->user_id ? $request->user_id : null,
        ], fn($value) => !is_null($value));

        if (empty($data)) {
            return response()->json([
                'success' => true,
                'message' => 'Tidak ada perubahan pada data kelas.',
                'data' => $kelas
            ], 200);
        }

        DB::table('kelas')->where('id', $id)->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Data kelas berhasil diperbarui.',
            'data' => Kelas::find($id)
        ]);
    }

    public function destroy($id)
    {
        $deleted = DB::table('kelas')->where('id', $id)->delete();
        
        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Data kelas tidak ditemukan.',
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

        $deleted = DB::table('kelas')->whereIn('id', $ids)->delete();

        if ($deleted === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data kelas yang ditemukan untuk dihapus.',
            ], 404);
        }

        return response()->noContent();
    }
}

