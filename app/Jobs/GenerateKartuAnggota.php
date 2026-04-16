<?php

namespace App\Jobs;

use App\Models\Anggota;
use App\Services\CardGenerationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateKartuAnggota implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3; // Retry 3 times if failed

    /**
     * Create a new job instance.
     */
    public function __construct(public Anggota $anggota)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(CardGenerationService $service): void
    {
        try {
            $service->generate($this->anggota);
        } catch (\Exception $e) {
            Log::error("Failed generating digital card for member {$this->anggota->id}: {$e->getMessage()}");
            throw $e;
        }
    }
}
