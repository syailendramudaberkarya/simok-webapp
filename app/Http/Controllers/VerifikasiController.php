<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use Illuminate\Http\Request;

class VerifikasiController extends Controller
{
    /**
     * Tampilkan data publik jika nomor anggota ditemukan.
     */
    public function show($nomor)
    {
        $anggota = Anggota::where('nomor_anggota', $nomor)->first();
        return view('verifikasi', compact('anggota', 'nomor'));
    }
}
