<?php

namespace App\Http\Controllers;

use App\Events\SiswaAlfa;
use App\Http\Requests\Presensi\StorePresensiRequest;
use App\Http\Requests\Presensi\UpdatePresensiRequest;
use App\Models\JadwalKBM;
use App\Models\Kelas;
use App\Models\Presensi;
use App\Models\Siswa;
use App\Models\SiswaMapelStack;
use App\Services\PresensiService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PresensiController extends Controller
{
    public function rekap_siswa($id)
    {
        $presensi = Presensi::where('siswa_id', $id)
                        ->orderBy('tanggal')
                        ->get()
                        ->groupBy(function ($item) {
                                      $tanggalCarbon = Carbon::parse($item->tanggal);
                                      return $tanggalCarbon->toDateString();
                                  });

        $totalAlfa  = 0;
        $totalSakit = 0;
        $totalIzin  = 0;

        foreach ($presensi as $tanggal => $dataPresensiHarian) {
            $jumlah     = $dataPresensiHarian->count();
            $jumlahAlfa = $dataPresensiHarian->where('kehadiran', 'Alfa')->count();

            if ($jumlahAlfa === $jumlah) {
                // Semua Alfa
                $totalAlfa += 1;
            } elseif ($jumlahAlfa > 0) {
                // Sebagian Alfa
                $totalAlfa += round($jumlahAlfa / $jumlah, 2);  // desimal 2 digit
            }
        }

        foreach ($presensi as $tanggal => $dataPresensiHarian) {
            $jumlah      = $dataPresensiHarian->count();
            $jumlahSakit = $dataPresensiHarian->where('kehadiran', 'Sakit')->count();

            if ($jumlahSakit === $jumlah) {
                // Semua Alfa
                $totalSakit += 1;
            } elseif ($jumlahSakit > 0) {
                // Sebagian Alfa
                $totalSakit += round($jumlahSakit / $jumlah, 2);  // desimal 2 digit
            }
        }

        foreach ($presensi as $tanggal => $dataPresensiHarian) {
            $jumlah     = $dataPresensiHarian->count();
            $jumlahIzin = $dataPresensiHarian->where('kehadiran', 'Sakit')->count();

            if ($jumlahIzin === $jumlah) {
                // Semua Alfa
                $totalIzin += 1;
            } elseif ($jumlahIzin > 0) {
                // Sebagian Alfa
                $totalIzin += round($jumlahIzin / $jumlah, 2);  // desimal 2 digit
            }
        }

        $siswa = Siswa::with(['kelas'])->find($id);

        $pdf = PDF::loadView('pdf.rekap_siswa', [
                                 'siswa'       => $siswa,
                                 'total_alfa'  => $totalAlfa,
                                 'total_sakit' => $totalSakit,
                                 'total_izin'  => $totalIzin,
                             ])->setPaper('A4', 'potrait');

        return $pdf->stream('rekap-' . str_replace(' ', '-', $siswa->nama) . '.pdf');
    }

    public function rekap_kelas(Request $request, $id)
    {
        if (!$request->filled('dari')) {
            return response()->json([
                                        'success' => false,
                                        'message' => 'Query ?dari=YYYY-MM-DD harus diisi.',
                                    ], 400);
        }

        $kelas = Kelas::with(['siswa', 'walas'])->findOrFail($id);

        // Ambil tanggal "dari" dan set ke hari Senin minggu itu
        $dari = Carbon::parse($request->input('dari'))->startOfWeek(Carbon::MONDAY);

        // Tentukan "sampai" = Sabtu minggu yang sama
        $sampai = (clone $dari)->addDays(5);

        $presensi = Presensi::where('kelas_id', $id)
                        ->whereBetween('tanggal', [$dari->copy()->startOfDay(), $sampai->copy()->endOfDay()])
                        ->get();

        $jadwalKBM = JadwalKBM::where('kelas_id', $id)
                         ->with(['mapel', 'guru'])
                         ->orderByRaw("FIELD(hari, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu')")
                         ->orderBy('mulai')
                         ->get()
                         ->groupBy('hari');

        $pdf = PDF::loadView('pdf.rekap_kelas', [
                                 'kelas'         => $kelas,
                                 'presensi'      => $presensi,
                                 'jadwalPerHari' => $jadwalKBM,
                             ])->setPaper('A4', 'landscape');

        return $pdf->stream("rekap-{$kelas->nama}-$dari-$sampai.pdf");
    }

    public function presensi_kosong_download($id)
    {
        $kelas = Kelas::with(['siswa', 'walas'])->find($id);

        if (!$kelas) {
            return response()->json([
                                        'success' => false,
                                        'message' => 'Data kelas tidak ditemukan.',
                                    ], 404);
        }

        $jadwalKBM = JadwalKBM::where('kelas_id', $id)
                         ->with(['mapel', 'guru'])
                         ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
                         ->orderBy('mulai', 'asc')
                         ->get();

        $hariList      = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $jadwalPerHari = [];

        foreach ($hariList as $hari) {
            $jadwalPerHari[$hari] = $jadwalKBM->where('hari', $hari)->values();
        }

        $data = [
            'kelas'         => $kelas,
            'siswa'         => $kelas->siswa,
            'walas'         => $kelas->walas,
            'jadwalPerHari' => $jadwalPerHari,
            'hariList'      => $hariList
        ];

        $pdf = Pdf::loadView('pdf.presensi_kosong', $data)->setPaper('A2', 'landscape');

        return $pdf->stream('presensi-kelas-' . $kelas->nama . '.pdf');
    }

    public function saat_ini()
    {
        $user_id         = auth('api')->id();
        $presensiSaatIni = PresensiService::getPresensiSaatIni($user_id);

        return response()->json([
                                    'success' => true,
                                    'message' => 'Data presensi saat ini.',
                                    'data'    => $presensiSaatIni,
                                ]);
    }

    public function hari_ini()
    {
        $user_id         = auth('api')->id();
        $presensiHariIni = PresensiService::getPresensiHariIni($user_id);

        return response()->json([
                                    'success' => true,
                                    'message' => 'Data presensi hari ini.',
                                    'data'    => $presensiHariIni,
                                ]);
    }

    public function alfa(Request $request)
    {
        $query = Presensi::with(['siswa', 'kelas', 'mapel'])  // Mengambil relasi siswa
                     ->where('kehadiran', 'alfa');

        $query->whereDate('tanggal', Carbon::today('Asia/Jakarta'));
        // Filter berdasarkan query parameter 'pada'
        if ($request->query('pada') === 'seminggu') {
            $query->whereBetween('tanggal', [Carbon::now('Asia/Jakarta')->subDays(7), Carbon::today('Asia/Jakarta')]);
        }

        $presensi = $query->get();

        return response()->json([
                                    'success' => true,
                                    'message' => 'Data presensi siswa yang alfa.',
                                    'data'    => $presensi,
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
                                    'data'    => $presensi,
                                ]);
    }

    public function store(StorePresensiRequest $request)
    {
        $updateData = collect($request->validated())->mapWithKeys(function ($presensi) {
                                                                      return [$presensi['presensi_id'] => ['kehadiran' => $presensi['status']]];
                                                                  });

        $updatedPresensi = DB::transaction(function () use ($updateData) {
                                               $presensiLama = Presensi::whereIn('id', $updateData->keys())->get()->keyBy('id');
                                               // 1️⃣ **Batch Update Presensi**
                                               $caseQuery    = 'CASE id ';
                                               foreach ($updateData as $id => $data) {
                                                   $caseQuery .= "WHEN {$id} THEN '{$data['kehadiran']}' ";
                                               }
                                               $caseQuery .= 'END';

                                               DB::table('presensi')
                                                   ->whereIn('id', $updateData->keys())
                                                   ->update(['kehadiran' => DB::raw($caseQuery), 'updated_at' => now('Asia/Jakarta')]);

                                               // 2️⃣ **Ambil daftar siswa yang diperbarui**
                                               $siswaIds = Presensi::whereIn('id', $updateData->keys())
                                                               ->pluck('siswa_id')
                                                               ->unique();

                                               // 3️⃣ **Hitung total Alfa per siswa & per mapel**
                                               $siswaAlfaHariIni = Presensi::where('kehadiran', 'Alfa')
                                                                       ->whereDate('created_at', today('Asia/Jakarta')->toDateString())
                                                                       ->whereIn('siswa_id', $siswaIds)
                                                                       ->select('siswa_id', DB::raw('COUNT(*) as total_alfa'))
                                                                       ->groupBy('siswa_id')
                                                                       ->pluck('total_alfa', 'siswa_id');

                                               $totalJadwalPerSiswa = Presensi::whereDate('created_at', today('Asia/Jakarta')->toDateString())
                                                                          ->whereIn('siswa_id', $siswaIds)
                                                                          ->select('siswa_id', DB::raw('COUNT(*) as total_jadwal'))
                                                                          ->groupBy('siswa_id')
                                                                          ->pluck('total_jadwal', 'siswa_id');

                                               // 4️⃣ **Hitung Alfa Per Mapel Per Siswa**
                                               $siswaAlfaPerMapel = Presensi::where('kehadiran', 'Alfa')
                                                                        ->whereDate('created_at', today('Asia/Jakarta')->toDateString())
                                                                        ->whereIn('siswa_id', $siswaIds)
                                                                        ->select('siswa_id', 'mapel_id')
                                                                        ->groupBy('siswa_id', 'mapel_id')
                                                                        ->get();

                                               // 5️⃣ **Proses Stack Alfa**
                                               foreach ($siswaIds as $siswaId) {
                                                   $siswa = Siswa::find($siswaId);
                                                   if (!$siswa)
                                                       continue;

                                                   $totalJadwal = $totalJadwalPerSiswa[$siswaId] ?? 0;
                                                   $totalAlfa   = $siswaAlfaHariIni[$siswaId] ?? 0;

                                                   // 🟢 **Tambah Stack Alfa Harian Jika Tidak Masuk Sama Sekali**
                                                   if ($totalAlfa == $totalJadwal && $totalJadwal > 0) {
                                                       if (!$siswa->last_alfa_update || $siswa->last_alfa_update != today('Asia/Jakarta')->toDateString()) {
                                                           $siswa->increment('stack_alfa_hari');
                                                           $siswa->update(['last_alfa_update' => today('Asia/Jakarta')]);
                                                       }
                                                   }
                                               }

                                               // 7️⃣ **Kurangi Stack Alfa Harian Jika Kehadiran Diperbarui**
                                               foreach ($siswaIds as $siswaId) {
                                                   $siswa = Siswa::find($siswaId);
                                                   if (!$siswa)
                                                       continue;

                                                   $totalJadwal = $totalJadwalPerSiswa[$siswaId] ?? 0;
                                                   $totalAlfa   = Presensi::where('kehadiran', 'Alfa')
                                                                      ->whereDate('created_at', today('Asia/Jakarta')->toDateString())
                                                                      ->where('siswa_id', $siswaId)
                                                                      ->count();

                                                   // 🟢 **Cek apakah siswa sebelumnya dianggap 100% Alfa hari ini**
                                                   $sebelumnyaAlfaPenuh = $siswa->last_alfa_update == today('Asia/Jakarta')->toDateString();

                                                   // ✅ **Jika tadinya Alfa 100% lalu ada perubahan (tidak Alfa 100%) -> Kurangi Stack**
                                                   if ($sebelumnyaAlfaPenuh && $totalAlfa < $totalJadwal) {
                                                       $siswa->decrement('stack_alfa_hari');

                                                       // Hapus penanda jika tidak ada stack tersisa
                                                       if ($siswa->stack_alfa_hari == 0) {
                                                           $siswa->update(['last_alfa_update' => null]);
                                                       }
                                                   }

                                                   // 🔄 **Jika tadinya tidak Alfa 100% lalu menjadi Alfa 100% lagi -> Tambah Stack**
                                                   if (!$sebelumnyaAlfaPenuh && $totalAlfa == $totalJadwal && $totalJadwal > 0) {
                                                       $siswa->increment('stack_alfa_hari');
                                                       $siswa->update(['last_alfa_update' => today('Asia/Jakarta')]);
                                                   }
                                               }

                                               // 🔵 **Tambah Stack Alfa Per Mapel Sesuai Jumlah Jadwal yang Dilewatkan**
                                               foreach ($siswaAlfaPerMapel as $data) {
                                                   $stackAlfaMapel = SiswaMapelStack::firstOrCreate([
                                                                                                        'siswa_id' => $data->siswa_id,
                                                                                                        'mapel_id' => $data->mapel_id
                                                                                                    ], [
                                                                                                        'stack_alfa'   => 0,
                                                                                                        'stack_harian' => 0,
                                                                                                    ]);

                                                   // Ambil jumlah jadwal Alfa unik untuk mapel ini hari ini
                                                   $totalJadwalMapelHariIni = Presensi::where('kehadiran', 'Alfa')
                                                                                  ->whereDate('created_at', today('Asia/Jakarta'))
                                                                                  ->where('siswa_id', $data->siswa_id)
                                                                                  ->where('mapel_id', $data->mapel_id)
                                                                                  ->distinct('jadwal_id')  // Hanya menghitung jadwal unik
                                                                                  ->count();

                                                   // Jika ini adalah hari baru, reset stack_harian
                                                   if (!$stackAlfaMapel->last_alfa_update || $stackAlfaMapel->last_alfa_update != today('Asia/Jakarta')->toDateString()) {
                                                       $stackAlfaMapel->update([
                                                                                   'stack_harian'     => 0,
                                                                                   'last_alfa_update' => today('Asia/Jakarta')
                                                                               ]);
                                                   }

                                                   // Jika stack_harian belum mencapai batas jadwal unik hari ini, tambahkan stack
                                                   if ($stackAlfaMapel->stack_harian < $totalJadwalMapelHariIni) {
                                                       // Update dengan cara yang lebih pasti
                                                       $stackAlfaMapel->increment('stack_alfa');
                                                       $stackAlfaMapel->increment('stack_harian');

                                                       $stackAlfaMapel->refresh();  // Pastikan objek diperbarui dari database
                                                   }
                                               }

                                               foreach ($updateData as $id => $data) {
                                                   $presensiSebelumnya = $presensiLama[$id] ?? null;

                                                   if ($presensiSebelumnya && $presensiSebelumnya->kehadiran === 'Alfa' && $data['kehadiran'] === 'Hadir') {
                                                       $stackAlfaMapel = SiswaMapelStack::where('siswa_id', $presensiSebelumnya->siswa_id)
                                                                             ->where('mapel_id', $presensiSebelumnya->mapel_id)
                                                                             ->first();

                                                       if ($stackAlfaMapel && $stackAlfaMapel->stack_alfa > 0) {
                                                           $stackAlfaMapel->decrement('stack_alfa');
                                                           $stackAlfaMapel->decrement('stack_harian');
                                                       }
                                                   }
                                               }

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
                                 'siswa_id'   => $request->siswa_id !== $presensi->siswa_id ? $request->siswa_id : null,
                                 'tanggal'    => $request->tanggal !== $presensi->tanggal ? $request->tanggal : null,
                                 'kehadiran'  => $request->kehadiran !== $presensi->kehadiran ? $request->kehadiran : null,
                                 'kelas_id'   => $request->kelas_id !== $presensi->kelas_id ? $request->kelas_id : null,
                                 'jadwal_id'  => $request->jadwal_id !== $presensi->jadwal_id ? $request->jadwal_id : null,
                                 'mapel_id'   => $request->mapel_id !== $presensi->mapel_id ? $request->mapel_id : null,
                                 'updated_at' => now(),
                             ], fn($value) => !is_null($value));

        if (empty($data)) {
            return response()->json([
                                        'success' => true,
                                        'message' => 'Tidak ada perubahan pada data presensi.',
                                        'data'    => $presensi
                                    ], 200);
        }

        DB::table('presensi')->where('id', $id)->update($data);

        return response()->json([
                                    'success' => true,
                                    'message' => 'Data presensi berhasil diperbarui.',
                                    'data'    => Presensi::find($id)
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
                                    'data'    => $tanggalMerah
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
                                        'errors'  => ['ids' => 'Harap kirimkan array ID yang valid (hanya angka).']
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
