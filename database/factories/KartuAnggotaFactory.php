<?php

namespace Database\Factories;

use App\Models\KartuAnggota;
use App\Models\Anggota;
use App\Models\KartuTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<KartuAnggota>
 */
class KartuAnggotaFactory extends Factory
{
    protected $model = KartuAnggota::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'anggota_id' => Anggota::factory()->approved(),
            'nomor_anggota' => str_pad((string) fake()->unique()->numberBetween(1, 99999), 5, '0', STR_PAD_LEFT),
            'template_id' => KartuTemplate::factory(),
            'berlaku_hingga' => now()->addYear(),
            'generated_at' => now(),
        ];
    }
}
