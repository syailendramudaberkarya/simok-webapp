<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CardGeneratorController extends Controller
{
    public function downloadPdf()
    {
        $anggota = Auth::user()->anggota;
        
        if (!$anggota || !$anggota->isApproved()) {
            abort(403, 'Kartu belum tersedia.');
        }

        $kartu = $anggota->kartuAnggota()->latest()->first();

        // Serve the already-generated PDF from storage
        if ($kartu && $kartu->pdf_path && Storage::disk('local')->exists($kartu->pdf_path)) {
            return Storage::disk('local')->download($kartu->pdf_path, 'KARTU_ANGGOTA_' . $anggota->nomor_anggota . '.pdf');
        }

        abort(404, 'File PDF kartu tidak ditemukan di storage.');
    }

    public function downloadPng()
    {
        $anggota = Auth::user()->anggota;
        
        if (!$anggota || !$anggota->isApproved()) {
            abort(403, 'Kartu belum tersedia.');
        }

        $kartu = $anggota->kartuAnggota()->latest()->first();

        if ($kartu && $kartu->pdf_path && Storage::disk('local')->exists($kartu->pdf_path)) {
            $pdfPath = storage_path('app/private/' . $kartu->pdf_path);
            
            if (extension_loaded('imagick')) {
                try {
                    $im = new \Imagick();
                    $im->setResolution(300, 300);
                    $im->readImage($pdfPath . '[0]'); // only first page
                    $im->setImageFormat('png');
                    $pngContent = $im->getImageBlob();
                    
                    return response($pngContent)
                            ->header('Content-Type', 'image/png')
                            ->header('Content-Disposition', 'attachment; filename="KARTU_ANGGOTA_' . $anggota->nomor_anggota . '.png"');
                } catch (\Exception $e) {
                    session()->flash('warning', 'Gagal memproses PNG melalui Imagick. Pastikan Ghostscript terinstall. Hubungi Admin.');
                    return redirect()->route('anggota.kartu');
                }
            } else {
                session()->flash('warning', 'Ekstensi PHP Imagick tidak tersedia di server. Harap gunakan Unduh PDF.');
                return redirect()->route('anggota.kartu');
            }
        }

        abort(404, 'File sumber PDF tidak ditemukan.');
    }
}
