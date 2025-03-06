<?php

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/surat-teguran', function ()
    {
        $pdf = Pdf::loadView('pdf.surat-teguran');
        return $pdf->stream('surat-teguran.pdf');
    }
);
