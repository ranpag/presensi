<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Siswa\StoreSiswaRequest;
use App\Http\Requests\Siswa\UpdateSiswaRequest;

class SiswaController extends Controller
{
    public function siswa_kelas_saya()
    {
        $user_id = auth('api')->id();

        $siswa = Siswa::whereHas('kelas', function ($query) use ($user_id) {
            $query->where('user_id', $user_id);
        })->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Data siswa berhasil diambil.',
            'data' => $siswa
        ]);
    }

    public function siswa_kelas_terperingat()
    {
        $user_id = auth('api')->id();

        $siswa = Siswa::with(['kelas.walas', 'stackAlfaMapel' => function ($query) {
            $query->where('stack_alfa', '>=', 3);
        }])->whereHas('kelas', function ($query) use ($user_id) {
            $query->where('user_id', $user_id);
        })->where(function ($query) {
            $query->where('stack_alfa_hari', '>=', 3)
                ->orWhereHas('stackAlfaMapel', function ($query) {
                    $query->where('stack_alfa', '>=', 3);
                });
        })->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Data siswa yang alfanya lebih dari 3 berhasil diambil.',
            'data' => $siswa
        ]);
    }

    public function buat_surat_siswa(Request $request,$id)
    {
        $tipe = $request->query('tipe');

        if (!$tipe || $tipe === '') {      
            return response()->json([
                'success' => false,
                'message' => 'Gunakan query tipe.',
                'errors' => [
                    'tipe' => 'Gunakan query tipe antara: 3hari | 3mapel.',
                ],
            ], 404);
        }

        $siswa = Siswa::with(['kelas.walas', 'stackAlfaMapel' => function ($query) {
                    $query->where('stack_alfa', '>=', 3);
                }])
                ->where(function ($query) use ($id) {
                    $query->where('id', $id)
                        ->where('stack_alfa_hari', '>=', 3)
                        ->orWhereHas('stackAlfaMapel', function ($query) {
                            $query->where('stack_alfa', '>=', 3);
                        });
                })
                ->first(); 

        if (!$siswa) {
            return response()->json([
                'success' => false,
                'message' => 'Data siswa tidak ditemukan.',
            ], 404);
        }
        
        if ($tipe === '3hari') {
            $pdf = Pdf::loadView('pdf.3hari', ['siswa' => $siswa]);
        }

        if ($tipe === '3mapel') {      
            $pdf = Pdf::loadView('pdf.3mapel', ['siswa' => $siswa]);
        }
        
        $namaFile = 'SURAT_SANKSI_SISWA_' . preg_replace('/[^A-Za-z0-9_]/', '_', $siswa->nama) . '.pdf';
        return $pdf->stream($namaFile);
    }

    public function index(Request $request)
    {
        $query = Siswa::query();

        if ($request->has('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('nis', 'like', '%' . $request->search . '%');
        }

        $query->with('kelas');
        $siswa = $query->get();

        return response()->json([
            'success' => true,
            'message' => 'Data siswa berhasil diambil.',
            'data' => $siswa
        ]);
    }

    public function store(StoreSiswaRequest $request)
    {
        $siswaId = DB::table('siswa')->insertGetId([
            'nama' => $request->nama,
            'gender' => $request->gender,
            'nis' => $request->nis,
            'no_telp' => $request->no_telp,
            'walimurid' => $request->walimurid,
            'alamat' => $request->alamat,
            'kelas_id' => $request->kelas_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data siswa berhasil ditambahkan.',
            'data' => Siswa::find($siswaId)
        ], 201);
    }

    public function show($id)
    {
        $siswa = Siswa::with(['kelas'])->find($id);

        if (!$siswa) {
            return response()->json([
                'success' => false,
                'message' => 'Data siswa tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data siswa berhasil ditemukan.',
            'data' => $siswa
        ]);
    }

    public function update(UpdateSiswaRequest $request, $id)
    {
        $siswa = Siswa::find($id);

        if (!$siswa) {
            return response()->json([
                'success' => false,
                'message' => 'Data siswa tidak ditemukan.',
            ], 404);
        }

        $data = array_filter([
            'nama' => $request->nama !== $siswa->nama ? $request->nama : null,
            'gender' => $request->gender !== $siswa->gender ? $request->gender : null,
            'nis' => $request->nis !== $siswa->nis ? $request->nis : null,
            'no_telp' => $request->no_telp !== $siswa->no_telp ? $request->no_telp : null,
            'walimurid' => $request->walimurid !== $siswa->walimurid ? $request->walimurid : null,
            'alamat' => $request->alamat !== $siswa->alamat ? $request->alamat : null,
            'kelas_id' => $request->kelas_id !== $siswa->kelas_id ? $request->kelas_id : null,
        ], fn($value) => !is_null($value));

        if (empty($data)) {
            return response()->json([
                'success' => true,
                'message' => 'Tidak ada perubahan pada data siswa.',
                'data' => $siswa
            ], 200);
        }

        DB::table('siswa')->where('id', $id)->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Data siswa berhasil diperbarui.',
            'data' => Siswa::find($id)
        ]);
    }

    public function destroy($id)
    {
        $deleted = DB::table('siswa')->where('id', $id)->delete();

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

        $deleted = DB::table('siswa')->whereIn('id', $ids)->delete();

        if ($deleted === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data siswa yang ditemukan untuk dihapus.',
            ], 404);
        }

        return response()->noContent();
    }
}
