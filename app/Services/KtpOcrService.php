<?php

namespace App\Services;

use App\DataTransferObjects\KtpData;
use App\Services\KtpScanner\KtpTextParser;
use App\Services\KtpScanner\OcrSpaceClient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class KtpOcrService
{
    public function __construct(
        private readonly OcrSpaceClient $ocrClient,
        private readonly KtpTextParser $parser,
    ) {}

    /**
     * Scan a KTP image and return structured data.
     */
    public function scan(string $imagePath): KtpData
    {
        $fullPath = $this->resolveFilePath($imagePath);

        if ($fullPath === null) {
            Log::error("KTP OCR: File not found at {$imagePath}");

            return new KtpData;
        }

        try {
            $rawText = $this->ocrClient->extractText($fullPath);
        } catch (RuntimeException $e) {
            Log::error('KTP OCR: ' . $e->getMessage());

            return new KtpData;
        }

        if (empty($rawText)) {
            return new KtpData;
        }

        Log::info('KTP OCR raw text', ['text' => $rawText]);

        $parsed = $this->parser->parse($rawText);

        return KtpData::fromParsedArray($parsed);
    }

    /**
     * Resolve an image path to an absolute filesystem path.
     */
    private function resolveFilePath(string $path): ?string
    {
        if (file_exists($path)) {
            return $path;
        }

        $storagePath = Storage::disk('local')->path($path);

        return file_exists($storagePath) ? $storagePath : null;
    }
}
