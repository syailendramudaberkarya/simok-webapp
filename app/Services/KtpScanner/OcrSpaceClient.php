<?php

namespace App\Services\KtpScanner;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * HTTP client for the OCR.space API.
 *
 * Handles the API communication only — no parsing logic.
 */
class OcrSpaceClient
{
    private const API_URL = 'https://api.ocr.space/parse/image';

    private const TIMEOUT_SECONDS = 25;

    public function __construct(
        private readonly string $apiKey,
    ) {
    }

    /**
     * Send an image to OCR.space and return the raw extracted text.
     *
     * @throws RuntimeException When the API key is missing or the request fails.
     */
    public function extractText(string $filePath): string
    {
        if (empty($this->apiKey)) {
            throw new RuntimeException('KTP OCR: No API key configured. Set OCR_SPACE_API_KEY in .env');
        }

        $response = Http::timeout(self::TIMEOUT_SECONDS)
            ->attach('file', file_get_contents($filePath), basename($filePath))
            ->post(self::API_URL, [
                'apikey' => $this->apiKey,
                'language' => 'eng',
                'isOverlayRequired' => 'false',
                'detectOrientation' => 'true',
                'scale' => 'true',
                'OCREngine' => '2',
            ]);

        if (!$response->successful()) {
            Log::error('KTP OCR: API request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new RuntimeException("KTP OCR: API returned HTTP {$response->status()}");
        }

        $data = $response->json();

        $parsedText = $data['ParsedResults'][0]['ParsedText'] ?? '';

        if (empty($parsedText)) {
            Log::warning('KTP OCR: No parsed text in response', $data);
        }

        return $parsedText;
    }
}
