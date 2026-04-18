<?php

namespace Database\Factories;

use App\Models\Kantor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Kantor>
 */
class KantorFactory extends Factory
{
    protected $model = Kantor::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_kantor' => 'Kantor ' . $this->faker->company(),
            'jenjang' => $this->faker->randomElement(['DPN', 'DPD', 'DPC', 'PR', 'PAR']),
            'telepon' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'alamat' => $this->faker->address(),
            'kode_pos' => $this->faker->postcode(),
            'status' => 'Aktif',
            'latitude' => $this->faker->latitude(-10, 5),
            'longitude' => $this->faker->longitude(95, 140),
        ];
    }
}
