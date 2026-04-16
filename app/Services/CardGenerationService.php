<?php

namespace App\Services;

use App\Models\Anggota;
use App\Models\KartuAnggota;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class CardGenerationService
{
    /**
     * Generate PDF card for an Anggota and save to private storage.
     * Updates/Creates the KartuAnggota record.
     */
    public function generate(Anggota $anggota): KartuAnggota
    {
        $kartu = $anggota->kartuAnggota()->latest()->first();
        if (!$kartu) {
            throw new \Exception("KartuAnggota database record missing for Anggota {$anggota->id}");
        }

        $verifyUrl = route('verify', ['nomor' => $anggota->nomor_anggota]);
        $template = $kartu->template;

        // Render PDF
        $pdf = Pdf::loadView('pdf.kartu-anggota', [
            'anggota' => $anggota,
            'kartu' => $kartu,
            'verifyUrl' => $verifyUrl,
            'template' => $template,
        ])->setPaper([0, 0, 382.68, 240.94]);

        // Determine save path
        $filename = "{$anggota->nomor_anggota}.pdf";
        $directory = "kartu_pdf";
        $fullPath = "{$directory}/{$filename}";

        // Save to private disk (local is private in this app)
        Storage::disk('local')->put($fullPath, $pdf->output());

        // Update Kartu path
        $kartu->update([
            'pdf_path' => $fullPath,
            'qr_code_url' => $verifyUrl
        ]);

        return $kartu;
    }
}
