<?php

namespace Database\Factories;

use App\Models\KartuTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<KartuTemplate>
 */
class KartuTemplateFactory extends Factory
{
    protected $model = KartuTemplate::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_template' => fake()->words(2, true) . ' Template',
            'warna_utama' => fake()->hexColor(),
            'warna_sekunder' => fake()->hexColor(),
            'is_active' => false,
        ];
    }

    /**
     * Indicate that the template is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }
}
