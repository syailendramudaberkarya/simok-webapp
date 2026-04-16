<?php

namespace App\Services\KtpScanner;

use Illuminate\Support\Str;

/**
 * Parses raw OCR text from a KTP image into structured fields.
 *
 * Each extraction method is isolated so individual fields can be
 * tested and improved independently.
 */
class KtpTextParser
{
    private string $text;

    private string $normalizedText;

    /**
     * @return array<string, string>
     */
    public function parse(string $rawText): array
    {
        $this->text = $rawText;
        $this->normalizedText = $this->normalize($rawText);

        $result = [];

        $extractors = [
            'nik' => 'extractNik',
            'nama' => 'extractNama',
            'tempat_lahir' => 'extractTempatLahir',
            'tanggal_lahir' => 'extractTanggalLahir',
            'jenis_kelamin' => 'extractJenisKelamin',
            'agama' => 'extractAgama',
            'alamat' => 'extractAlamat',
            'rt_rw' => 'extractRtRw',
            'kelurahan' => 'extractKelurahan',
            'kecamatan' => 'extractKecamatan',
        ];

        foreach ($extractors as $field => $method) {
            $value = $this->{$method}();

            if ($value !== null && $value !== '') {
                $result[$field] = $value;
            }
        }

        return $result;
    }

    /**
     * Normalize whitespace while keeping line breaks intact.
     */
    private function normalize(string $text): string
    {
        return preg_replace('/[^\S\n]+/', ' ', $text);
    }

    /**
     * Strip trailing KTP labels that sometimes bleed onto the same line.
     */
    private function cleanTrailingLabels(string $value): string
    {
        $labels = 'Tempat|Tmp|Lahir|Tgl|Jenis|Kelamin|Agama|Status|Perkawinan|RT|RW|Kel|Desa|Kecamatan|Kec|Kabupaten|Kota|Provinsi';
        $value = preg_replace('/\b(?:' . $labels . ')\b.*$/i', '', $value);

        return Str::squish($value);
    }

    // ---------------------------------------------------------------
    // Individual Field Extractors
    // ---------------------------------------------------------------

    private function extractNik(): ?string
    {
        // Try labeled NIK first (handles OCR mis-reads like NlK, N1K, etc.)
        if (preg_match('/(?:NIK|NlK|N[!|l1]K)\s*[:=]?\s*[^\d]*(\d[\d\s]{14,19}\d)/i', $this->normalizedText, $m)) {
            return preg_replace('/\s+/', '', $m[1]);
        }

        // Fallback: any standalone 16-digit number
        if (preg_match('/\b(\d{16})\b/', $this->normalizedText, $m)) {
            return $m[1];
        }

        return null;
    }

    private function extractNama(): ?string
    {
        if (preg_match('/(?:Nama|Name)\s*[:=]?\s*[^\w]*([^\n]+)/i', $this->normalizedText, $m)) {
            return $this->cleanTrailingLabels($m[1]);
        }

        return null;
    }

    /**
     * Extract birth place from "Tempat/Tgl Lahir" line.
     * Birth place and date are parsed from the same regex but returned separately.
     */
    private function extractTempatLahir(): ?string
    {
        if ($this->matchBirthLine($m)) {
            return $this->cleanTrailingLabels($m[1]);
        }

        return null;
    }

    private function extractTanggalLahir(): ?string
    {
        if ($this->matchBirthLine($m)) {
            return "{$m[4]}-{$m[3]}-{$m[2]}"; // YYYY-MM-DD
        }

        return null;
    }

    /**
     * Shared regex for Tempat/Tgl Lahir line.
     *
     * @param  array<int, string>|null  $matches
     */
    private function matchBirthLine(?array &$matches = null): bool
    {
        return (bool) preg_match(
            '/(?:Tempat|Tmp|Tgl|Lahir)\s*[\/\\\\]?\s*(?:Tgl|Tanggal)?\s*(?:Lahir)?\s*[:=]?\s*[^\w\s]*([A-Za-z\s.\-]+)[,\s]+(\d{2})\s*[-\/]\s*(\d{2})\s*[-\/]\s*(\d{4})/i',
            $this->normalizedText,
            $matches
        );
    }

    private function extractJenisKelamin(): ?string
    {
        if (preg_match('/(?:Jenis\s*Kelamin|JK|Kelamin)\s*[:=]?\s*(LAKI[\s-]*LAKI|PEREMPUAN|LK|PR|LAKI|PEREMP)/i', $this->normalizedText, $m)) {
            $raw = strtoupper(trim($m[1]));

            return (str_contains($raw, 'LAKI') || $raw === 'LK') ? 'Laki-laki' : 'Perempuan';
        }

        return null;
    }

    private function extractAgama(): ?string
    {
        if (preg_match('/(?:Agama)\s*[:=]?\s*(ISLAM|KRISTEN|KATOLIK|HINDU|BUDDHA|KONGHUCU)/i', $this->normalizedText, $m)) {
            return ucfirst(strtolower(trim($m[1])));
        }

        return null;
    }

    private function extractAlamat(): ?string
    {
        if (preg_match('/(?:Alamat)\s*[:=]?\s*(.+?)(?=\s*(?:RT|RW|Kel|Desa|Kecamatan|Kec|Agama|$))/is', $this->normalizedText, $m)) {
            return $this->cleanTrailingLabels($m[1]);
        }

        return null;
    }

    private function extractRtRw(): ?string
    {
        if (preg_match('/(?:RT)\s*[\/\\\\]?\s*(?:RW)\s*[:=]?\s*(\d{1,3})\s*[\/\\\\]\s*(\d{1,3})/i', $this->normalizedText, $m)) {
            return str_pad($m[1], 3, '0', STR_PAD_LEFT) . '/' . str_pad($m[2], 3, '0', STR_PAD_LEFT);
        }

        return null;
    }

    private function extractKelurahan(): ?string
    {
        if (preg_match('/\b(?:Kel|Desa|Kelurahan|Kel\/Desa)\b\s*[\/\\\\]?\s*(?:Desa)?\s*[:=]?\s*([^\n]+)/i', $this->normalizedText, $m)) {
            return $this->cleanTrailingLabels($m[1]);
        }

        return null;
    }

    private function extractKecamatan(): ?string
    {
        if (preg_match('/\b(?:Kecamatan|Kec)\b\s*\.?\s*[:=]?\s*([^\n]+)/i', $this->normalizedText, $m)) {
            return $this->cleanTrailingLabels($m[1]);
        }

        return null;
    }
}
