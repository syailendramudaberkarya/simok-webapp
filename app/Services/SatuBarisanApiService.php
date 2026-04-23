<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SatuBarisanApiService
{
    protected string $baseUrl = 'https://satubarisan.id/api/v1';

    protected ?string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.satubarisan.key', env('SATUBARISAN_API_KEY'));
    }

    protected function getHeaders()
    {
        return [
            'X-API-Key' => $this->apiKey,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    public function createAnggota(array $data)
    {
        if (! $this->apiKey) {
            Log::warning('SatuBarisan API Key not configured. Skipping createAnggota.');

            return ['success' => false, 'message' => 'API Key not configured'];
        }

        try {
            $response = Http::withHeaders($this->getHeaders())
                ->post($this->baseUrl.'/anggota', $data);

            if (! $response->successful()) {
                Log::error('SatuBarisan API Error (Create): '.$response->body());
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('SatuBarisan API Exception (Create): '.$e->getMessage());

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function updateAnggota($key, array $data)
    {
        if (! $this->apiKey) {
            Log::warning('SatuBarisan API Key not configured. Skipping updateAnggota.');

            return ['success' => false, 'message' => 'API Key not configured'];
        }

        try {
            $response = Http::withHeaders($this->getHeaders())
                ->put($this->baseUrl.'/anggota/'.$key, $data);

            if (! $response->successful()) {
                Log::error('SatuBarisan API Error (Update): '.$response->body());
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('SatuBarisan API Exception (Update): '.$e->getMessage());

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function deleteAnggota($key)
    {
        if (! $this->apiKey) {
            Log::warning('SatuBarisan API Key not configured. Skipping deleteAnggota.');

            return ['success' => false, 'message' => 'API Key not configured'];
        }

        try {
            $response = Http::withHeaders($this->getHeaders())
                ->delete($this->baseUrl.'/anggota/'.$key);

            if (! $response->successful()) {
                Log::error('SatuBarisan API Error (Delete): '.$response->body());
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('SatuBarisan API Exception (Delete): '.$e->getMessage());

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
