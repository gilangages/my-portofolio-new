<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Visitor>
 */
class VisitorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'device_id' => $this->faker->uuid(),
            'device_type' => $this->faker->randomElement(['desktop', 'mobile', 'tablet']),
            'os' => $this->faker->randomElement(['Windows', 'iOS', 'Android', 'macOS']),
            'browser' => $this->faker->randomElement(['Chrome', 'Safari', 'Firefox', 'Edge']),
            'device_name' => $this->faker->word(),
        ];
    }
}
