<?php

use App\Models\Siswa;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/pdf/3hari/{id}', function ($id) {
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
                
        $pdf = Pdf::loadView('pdf.3hari', ['siswa' => $siswa]);
        return $pdf->stream('surat-teguran.pdf');
    }
);

Route::get('/pdf/3mapel/{id}', function ($id) {
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
                
        $pdf = Pdf::loadView('pdf.3mapel', ['siswa' => $siswa]);
        return $pdf->stream('surat-teguran.pdf');
    }
);
