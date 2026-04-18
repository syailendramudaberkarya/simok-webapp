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
            'status_kawin' => 'extractStatusKawin',
            'pekerjaan' => 'extractPekerjaan',
            'kewarganegaraan' => 'extractKewarganegaraan',
            'golongan_darah' => 'extractGolonganDarah',
            'alamat' => 'extractAlamat',
            'rt_rw' => 'extractRtRw',
            'kelurahan' => 'extractKelurahan',
            'kecamatan' => 'extractKecamatan',
            'kabupaten' => 'extractKabupaten',
            'provinsi' => 'extractProvinsi',
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


    private function cleanTrailingLabels(string $value): string
    {
        $labels = 'Tempat|Tmp|Lahir|Tgl|Jenis|Kelamin|Agama|Status|Perkawinan|RT|RW|Kel|Desa|Kecamatan|Kec|Kabupaten|Kota|Provinsi';
        $value = preg_replace('/\b(?:' . $labels . ')\b.*$/i', '', $value);

        return Str::squish($value);
    }


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

    private function extractStatusKawin(): ?string
    {
        // Handle common variations and OCR misreadings of "Status Perkawinan"
        if (preg_match('/(?:Status\s*(?:Perkawinan|Perkawman|Perkavvinan))\s*[:=]?\s*([^\n]+)/i', $this->normalizedText, $m)) {
            $val = trim($m[1]);
            if (stripos($val, 'BELUM') !== false) return 'Belum Kawin';
            if (stripos($val, 'CERAI MATI') !== false) return 'Cerai Mati';
            if (stripos($val, 'CERAI HIDUP') !== false) return 'Cerai Hidup';
            if (stripos($val, 'KAWIN') !== false || stripos($val, 'KAVVIN') !== false) return 'Kawin';
            return Str::title(strtolower($val));
        }

        return null;
    }

    private function extractPekerjaan(): ?string
    {
        if (preg_match('/\b(?:Pekerjaan)\b\s*[:=]?\s*([^\n]+)/i', $this->normalizedText, $m)) {
            return strtoupper(trim($m[1]));
        }

        return null;
    }

    private function extractKewarganegaraan(): ?string
    {
        if (preg_match('/\b(?:Kewarganegaraan)\b\s*[:=]?\s*([^\n]+)/i', $this->normalizedText, $m)) {
            $val = strtoupper(trim($m[1]));
            if (str_contains($val, 'WNI')) return 'WNI';
            if (str_contains($val, 'WNA')) return 'WNA';
            return $val;
        }

        return null;
    }

    private function extractGolonganDarah(): ?string
    {
        // Gol Darah is often small and misread.
        // We look for the label and capture only A, B, O, or AB.
        if (preg_match('/(?:Gol\.?\s*Darah|Gol)\s*[:=]?\s*([ABO0\-\s1I|]+)/i', $this->normalizedText, $m)) {
            $val = strtoupper(trim($m[1]));
            
            // Priority 1: Check for AB
            if (str_contains($val, 'AB')) return 'AB';
            
            // Priority 2: Check for B (check B before A if there's risk of noise, 
            // but usually A and B are distinct enough unless OCR is very bad)
            // However, we should be careful not to match 'B' inside 'AB' (handled by priority 1)
            
            // Let's use more strict matching for single letters
            if (preg_match('/\bB\b/i', $val) || (str_contains($val, 'B') && !str_contains($val, 'AB'))) return 'B';
            if (preg_match('/\bA\b/i', $val) || (str_contains($val, 'A') && !str_contains($val, 'AB'))) return 'A';
            if (preg_match('/\bO\b/i', $val) || str_contains($val, 'O') || str_contains($val, '0')) return 'O';
        }

        return null;
    }

    private function extractAlamat(): ?string
    {
        if (preg_match('/(?:Alamat|Alanat|Alatnat)\s*[:=]?\s*(.+?)(?=\s*(?:\bRT\b|\bRW\b|\bKel\b|\bDesa\b|\bKecamatan\b|\bAgama\b|$))/is', $this->normalizedText, $m)) {
            $val = $m[1];
            return Str::squish(trim($val));
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
            $val = $this->cleanTrailingLabels($m[1]);
            // User requested: "kelurahan huruf besar diawal kata" (Title Case)
            return Str::title(strtolower(trim($val)));
        }

        return null;
    }

    private function extractKecamatan(): ?string
    {
        if (preg_match('/\b(?:Kecamatan|Kec)\b\s*\.?\s*[:=]?\s*([^\n]+)/i', $this->normalizedText, $m)) {
            $val = $this->cleanTrailingLabels($m[1]);
            return strtoupper(trim($val));
        }

        return null;
    }

    private function extractKabupaten(): ?string
    {
        if (preg_match('/\b(?:Kabupaten|Kota|Kab\.?|Kodya)\b\s*[:=]?\s*([^\n]+)/i', $this->normalizedText, $m)) {
            $val = $this->cleanTrailingLabels($m[1]);
            return $this->processKabupatenResult($val);
        }
        $lines = explode("\n", $this->normalizedText);
        foreach ($lines as $i => $line) {
            if (preg_match('/(?:PROVINSI|PROV)/i', $line)) {
                // Check the next non-empty line
                for ($j = 1; $j <= 2; $j++) {
                    if (isset($lines[$i + $j])) {
                        $nextLine = trim($lines[$i + $j]);
                        if (empty($nextLine)) {
                            continue;
                        }

                        // Ensure it's not the NIK or Nama line
                        if (preg_match('/(?:NIK|NlK|N[1!]K|Nama|Name)/i', $nextLine)) {
                            break;
                        }

                        return $this->processKabupatenResult($nextLine);
                    }
                }
            }
        }

        return null;
    }

    private function processKabupatenResult(string $val): ?string
    {
        $upper = strtoupper(trim(ltrim($val, '.: ')));

        $name = trim(Str::replaceFirst('KABUPATEN', '', $upper));
        $name = trim(Str::replaceFirst('KAB.', '', $name));
        $name = trim(Str::replaceFirst('KAB', '', $name));
        $name = trim(Str::replaceFirst('KOTA', '', $name));
        $name = trim(ltrim($name, '.: '));

        if (empty($name)) {
            return null;
        }

        return Str::title(strtolower($name));
    }

    private function extractProvinsi(): ?string
    {
        if (preg_match('/(?:PROVINSI|PROV)\s*([^\n]+)/i', $this->normalizedText, $m)) {
            $val = $this->cleanTrailingLabels($m[1]);
            return Str::title(strtolower(trim($val)));
        }

        return null;
    }
}
