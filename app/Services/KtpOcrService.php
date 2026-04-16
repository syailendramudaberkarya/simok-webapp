<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class KtpOcrService
{
    /**
     * Extract text from KTP image using OCR.space API.
     *
     * @return array<string, string>
     */
    public function scan(string $imagePath): array
    {
        $fullPath = file_exists($imagePath) ? $imagePath : Storage::disk('local')->path($imagePath);

        if (!file_exists($fullPath)) {
            Log::error("KTP OCR: File not found at {$fullPath}");

            return [];
        }

        $ocrText = $this->callOcrApi($fullPath);

        if (empty($ocrText)) {
            return [];
        }

        Log::info('KTP OCR raw text: ' . $ocrText);

        return $this->parseKtpText($ocrText);
    }

    /**
     * Call OCR.space API to extract text from image.
     */
    private function callOcrApi(string $filePath): string
    {
        $apiKey = config('services.ocr.api_key', '');

        if (empty($apiKey)) {
            Log::warning('KTP OCR: No API key configured. Set OCR_SPACE_API_KEY in .env');

            return '';
        }

        try {
            $response = Http::timeout(30)
                ->attach('file', file_get_contents($filePath), basename($filePath))
                ->post('https://api.ocr.space/parse/image', [
                    'apikey' => $apiKey,
                    'language' => 'eng',
                    'isOverlayRequired' => 'false',
                    'detectOrientation' => 'true',
                    'scale' => 'true',
                    'OCREngine' => '2',
                ]);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['ParsedResults'][0]['ParsedText'])) {
                    return $data['ParsedResults'][0]['ParsedText'];
                }

                Log::warning('KTP OCR: No parsed text in response', $data);
            } else {
                Log::error('KTP OCR: API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('KTP OCR: Exception - ' . $e->getMessage());
        }

        return '';
    }

    /**
     * Parse raw OCR text to extract KTP fields.
     *
     * @return array<string, string>
     */
    private function parseKtpText(string $text): array
    {
        $result = [];

        $text = preg_replace('/[^\S\n]+/', ' ', $text);

        $cleanLabel = function ($val) {
            $val = preg_replace('/\b(?:Tempat|Tmp|Lahir|Tgl|Jenis|Kelamin|Agama|Status|Perkawinan|RT|RW|Kel|Desa|Kecamatan|Kec|Kabupaten|Kota|Provinsi)\b.*$/i', '', $val);
            return trim(preg_replace('/\s+/', ' ', $val));
        };

        if (preg_match('/(?:NIK|NlK|N[!|l1]K)\s*:?\s*(\d[\d\s]{14,19}\d)/i', $text, $m)) {
            $result['nik'] = preg_replace('/\s+/', '', $m[1]);
        } elseif (preg_match('/\b(\d{16})\b/', $text, $m)) {
            $result['nik'] = $m[1];
        }

        if (preg_match('/(?:Nama|Name)\s*:?\s*([^\n]+)/i', $text, $m)) {
            $result['nama'] = $cleanLabel($m[1]);
        }

        if (preg_match('/(?:Tempat|Tmp)\s*[\/\\\\]?\s*(?:Tgl|Tanggal)?\s*(?:Lahir)?\s*:?\s*([A-Za-z\s]+)[,\s]+(\d{2})\s*[-\/]\s*(\d{2})\s*[-\/]\s*(\d{4})/i', $text, $m)) {
            $result['tempat_lahir'] = $cleanLabel($m[1]);
            $result['tanggal_lahir'] = "{$m[4]}-{$m[3]}-{$m[2]}";
        }

        if (preg_match('/(?:Jenis\s*Kelamin|JK)\s*:?\s*(LAKI[\s-]*LAKI|PEREMPUAN|LK|PR)/i', $text, $m)) {
            $raw = strtoupper(trim($m[1]));
            $result['jenis_kelamin'] = str_contains($raw, 'LAKI') || $raw === 'LK' ? 'Laki-laki' : 'Perempuan';
        }

        if (preg_match('/(?:Agama)\s*:?\s*(ISLAM|KRISTEN|KATOLIK|HINDU|BUDDHA|KONGHUCU)/i', $text, $m)) {
            $result['agama'] = ucfirst(strtolower(trim($m[1])));
        }

        if (preg_match('/(?:Alamat)\s*:?\s*(.+?)(?=\s*(?:RT|RW|Kel|Desa|Kecamatan|Kec|Agama|$))/is', $text, $m)) {
            $result['alamat'] = $cleanLabel($m[1]);
        }

        if (preg_match('/(?:RT)\s*[\/\\\\]?\s*(?:RW)\s*:?\s*(\d{1,3})\s*[\/\\\\]\s*(\d{1,3})/i', $text, $m)) {
            $result['rt_rw'] = str_pad($m[1], 3, '0', STR_PAD_LEFT) . '/' . str_pad($m[2], 3, '0', STR_PAD_LEFT);
        }

        if (preg_match('/\b(?:Kel|Desa|Kelurahan)\b\s*[\/\\\\]?\s*(?:Desa)?\s*:?\s*([^\n]+)/i', $text, $m)) {
            $result['kelurahan'] = $cleanLabel($m[1]);
        }

        if (preg_match('/\b(?:Kecamatan|Kec)\b\s*\.?\s*:?\s*([^\n]+)/i', $text, $m)) {
            $result['kecamatan'] = $cleanLabel($m[1]);
        }

        return $result;
    }
}
