<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Siswa;
use App\Models\Presensi;
use App\Events\SiswaAlfa;
use Illuminate\Http\Request;
use App\Models\SiswaMapelStack;
use App\Services\PresensiService;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Presensi\StorePresensiRequest;
use App\Http\Requests\Presensi\UpdatePresensiRequest;

class PresensiController extends Controller
{
    public function saat_ini(Request $request)
    {
        $user_id = auth('api')->id();
        $presensiSaatIni = PresensiService::getPresensiSaatIni($user_id);

        return response()->json([
            'success' => true,
            'message' => 'Data presensi saat ini.',
            'data' => $presensiSaatIni,
        ]);
    }

    public function hari_ini(Request $request)
    {
        $user_id = auth('api')->id();
        $presensiHariIni = PresensiService::getPresensiHariIni($user_id);

        return response()->json([
            'success' => true,
            'message' => 'Data presensi hari ini.',
            'data' => $presensiHariIni,
        ]);
    }

    public function alfa(Request $request)
    {
        $query = Presensi::with(['siswa', 'kelas', 'mapel']) // Mengambil relasi siswa
            ->where('kehadiran', 'alfa');

        $query->whereDate('tanggal', Carbon::today('Asia/Jakarta'));
        // Filter berdasarkan query parameter 'pada'
        if  ($request->query('pada') === 'seminggu') {
            $query->whereBetween('tanggal', [Carbon::now('Asia/Jakarta')->subDays(7), Carbon::today('Asia/Jakarta')]);
        }

        $presensi = $query->get();

        return response()->json([
            'success' => true,
            'message' => 'Data presensi siswa yang alfa.',
            'data' => $presensi,
        ]);
    }

    public function index(Request $request)
    {
        $query = Presensi::with(['siswa', 'kelas', 'mapel']);

        if ($request->filled('dari') && $request->filled('sampai')) {
            $query->whereBetween('tanggal', [
                Carbon::parse($request->input('dari'))->startOfDay(),
                Carbon::parse($request->input('sampai'))->endOfDay(),
            ]);
        } 
        // Jika tidak ada 'dari' dan 'sampai', gunakan query 'pada' untuk filtering
        elseif ($request->has('pada')) {
            if ($request->input('pada') === 'hari') {
                $query->whereDate('tanggal', Carbon::today('Asia/Jakarta'));
            } elseif ($request->input('pada') === 'minggu') {
                $query->whereBetween('tanggal', [Carbon::now('Asia/Jakarta')->subDays(7), Carbon::today('Asia/Jakarta')]);
            }
        }

        // Filter berdasarkan status kehadiran jika ada
        if ($request->filled('status')) {
            $query->where('kehadiran', $request->input('status'));
        }

        // Filter berdasarkan kelas_id jika ada
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->input('kelas_id'));
        }

        // Filter berdasarkan mapel_id jika ada
        if ($request->filled('mapel_id')) {
            $query->where('mapel_id', $request->input('mapel_id'));
        }

        $presensi = $query->get();

        return response()->json([
            'success' => true,
            'message' => 'Semua data presensi siswa.',
            'data' => $presensi,
        ]);
    }

    public function store(StorePresensiRequest $request)
    {
        $updateData = collect($request->validated())->mapWithKeys(function ($presensi) {
            return [$presensi['presensi_id'] => ['kehadiran' => $presensi['status']]];
        });

        $updatedPresensi = DB::transaction(function () use ($updateData) {
            // 1️⃣ **Batch Update Presensi**
            $caseQuery = "CASE id ";
            foreach ($updateData as $id => $data) {
                $caseQuery .= "WHEN {$id} THEN '{$data['kehadiran']}' ";
            }
            $caseQuery .= "END";

            DB::table('presensi')
                ->whereIn('id', $updateData->keys())
                ->update(['kehadiran' => DB::raw($caseQuery), 'updated_at' => now('Asia/Jakarta')]);

            // 2️⃣ **Ambil daftar siswa yang diperbarui**
            $siswaIds = Presensi::whereIn('id', $updateData->keys())
                ->pluck('siswa_id')
                ->unique();

            // 3️⃣ **Hitung total Alfa per siswa & per mapel**
            $siswaAlfaHariIni = Presensi::where('kehadiran', 'Alfa')
                ->whereDate('created_at', today('Asia/Jakarta'))
                ->whereIn('siswa_id', $siswaIds)
                ->select('siswa_id', DB::raw('COUNT(*) as total_alfa'))
                ->groupBy('siswa_id')
                ->pluck('total_alfa', 'siswa_id');

            $totalJadwalPerSiswa = Presensi::whereDate('created_at', today('Asia/Jakarta'))
                ->whereIn('siswa_id', $siswaIds)
                ->select('siswa_id', DB::raw('COUNT(*) as total_jadwal'))
                ->groupBy('siswa_id')
                ->pluck('total_jadwal', 'siswa_id');

            // 4️⃣ **Hitung Alfa Per Mapel Per Siswa**
            $siswaAlfaPerMapel = Presensi::where('kehadiran', 'Alfa')
                ->whereDate('created_at', today('Asia/Jakarta'))
                ->whereIn('siswa_id', $siswaIds)
                ->select('siswa_id', 'mapel_id')
                ->groupBy('siswa_id', 'mapel_id')
                ->get();

            // 5️⃣ **Proses Stack Alfa**
            foreach ($siswaIds as $siswaId) {
                $siswa = Siswa::find($siswaId);
                if (!$siswa) continue;

                $totalJadwal = $totalJadwalPerSiswa[$siswaId] ?? 0;
                $totalAlfa = $siswaAlfaHariIni[$siswaId] ?? 0;

                // 🟢 **Tambah Stack Alfa Harian Jika Tidak Masuk Sama Sekali**
                if ($totalAlfa == $totalJadwal && $totalJadwal > 0) {
                    if (!$siswa->last_alfa_update || $siswa->last_alfa_update != today('Asia/Jakarta')) {
                        $siswa->increment('stack_alfa_hari');
                        $siswa->update(['last_alfa_update' => today('Asia/Jakarta')]);
                    }
                }
            }

            // 🔵 **Tambah Stack Alfa Per Mapel Sesuai Jumlah Jadwal yang Dilewatkan**
            foreach ($siswaAlfaPerMapel as $data) 
            {
                $stackAlfaMapel = SiswaMapelStack::firstOrNew([
                    'siswa_id' => $data->siswa_id,
                    'mapel_id' => $data->mapel_id
                ]);

                // Cek apakah hari ini sudah ada stack untuk mapel ini
                if (!$stackAlfaMapel->last_alfa_update || $stackAlfaMapel->last_alfa_update != today('Asia/Jakarta')) {
                    // Jika belum ada update hari ini, tambahkan stack 
                    $stackAlfaMapel->increment('stack_alfa');
                    $stackAlfaMapel->last_alfa_update = today('Asia/Jakarta');
                    $stackAlfaMapel->save();
                } else {
                    // Jika sudah ada update hari ini, cek apakah ada jadwal dengan mapel yang sama 
                    $totalJadwalMapelHariIni = Presensi::where('kehadiran', 'Alfa')
                        ->whereDate('created_at', today('Asia/Jakarta'))
                        ->where('siswa_id', $data->siswa_id)
                        ->where('mapel_id', $data->mapel_id)
                        ->count();

                    // Cek jumlah Alfa yang sudah dicatat di stack hari ini
                    $stackHariIni = $stackAlfaMapel->stack_alfa - SiswaMapelStack::where('siswa_id', $data->siswa_id)
                        ->where('mapel_id', $data->mapel_id)
                        ->whereDate('last_alfa_update', today('Asia/Jakarta'))
                        ->value('stack_alfa');

                    // Jika ada jadwal baru di hari ini yang belum dihitung dalam stack
                    if ($totalJadwalMapelHariIni > $stackHariIni) {
                        $stackAlfaMapel->increment('stack_alfa');
                        $stackAlfaMapel->save();
                    }
                }
            }


            // 6️⃣ **Ambil Data yang Diperbarui untuk Broadcast**
            return Presensi::with(['siswa', 'kelas', 'mapel'])
                ->whereIn('id', $updateData->keys())
                ->where('kehadiran', 'Alfa')
                ->get();
        });

        broadcast(new SiswaAlfa($updatedPresensi))->toOthers();
        
        return response()->json([
            'success' => true,
            'message' => 'Presensi berhasil diperbarui.',
        ]);
    }

    public function update(UpdatePresensiRequest $request, $id)
    {
        $presensi = Presensi::find($id);

        if (!$presensi) {
            return response()->json([
                'success' => false,
                'message' => 'Data presensi tidak ditemukan.',
            ], 404);
        }

        $data = array_filter([
            'siswa_id' => $request->siswa_id !== $presensi->siswa_id ? $request->siswa_id : null,
            'tanggal' => $request->tanggal !== $presensi->tanggal ? $request->tanggal : null,
            'kehadiran' => $request->kehadiran !== $presensi->kehadiran ? $request->kehadiran : null,
            'kelas_id' => $request->kelas_id !== $presensi->kelas_id ? $request->kelas_id : null,
            'jadwal_id' => $request->jadwal_id !== $presensi->jadwal_id ? $request->jadwal_id : null,
            'mapel_id' => $request->mapel_id !== $presensi->mapel_id ? $request->mapel_id : null,
            'updated_at' => now(),
        ], fn($value) => !is_null($value));

        if (empty($data)) {
            return response()->json([
                'success' => true,
                'message' => 'Tidak ada perubahan pada data presensi.',
                'data' => $presensi
            ], 200);
        }

        DB::table('presensi')->where('id', $id)->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Data presensi berhasil diperbarui.',
            'data' => Presensi::find($id)
        ]);
    }

    public function show($id)
    {
        $tanggalMerah = Presensi::with(['siswa', 'kelas', 'mapel', 'jadwal'])->find($id);

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
        $deleted = DB::table('presensi')->where('id', $id)->delete();

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

        $deleted = DB::table('presensi')->whereIn('id', $ids)->delete();

        if ($deleted === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data tanggal merah yang ditemukan untuk dihapus.',
            ], 404);
        }

        return response()->noContent();
    }
}


