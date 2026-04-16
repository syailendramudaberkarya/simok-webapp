<?php

namespace App\Jobs;

use App\Livewire\PendaftaranAnggota;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessKtpOcr implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $ktpPath,
        public string $componentId
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Mocking OCR API result (Simulation)
            sleep(2); 

            $simulatedResult = [
                'nik' => '3201112233445566',
                'nama' => 'BUDI SANTOSO',
                'tempat_lahir' => 'BOGOR',
                'tanggal_lahir' => '1990-01-01',
                'jenis_kelamin' => 'Laki-laki',
                'agama' => 'Islam',
                'alamat' => 'JL. MERDEKA NO. 123',
                'rt_rw' => '001/002',
                'kelurahan' => 'CIBINONG',
                'kecamatan' => 'CIBINONG',
            ];

            // Save to cache for polling
            \Illuminate\Support\Facades\Cache::put('ocr-result-' . $this->componentId, [
                'status' => 'success',
                'data' => $simulatedResult
            ], now()->addMinutes(10));

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('OCR processing failed: ' . $e->getMessage());
            \Illuminate\Support\Facades\Cache::put('ocr-result-' . $this->componentId, [
                'status' => 'failed'
            ], now()->addMinutes(10));
        }
    }
}
