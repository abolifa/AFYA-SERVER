<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\Vital;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vital>
 */
class VitalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'recorded_at' => $this->faker->date(),
            'weight' => $this->faker->randomFloat(2, 40, 120),
            'systolic' => $this->faker->randomFloat(2, 90, 180),
            'diastolic' => $this->faker->randomFloat(2, 60, 120),
            'heart_rate' => $this->faker->randomFloat(2, 60, 150),
            'temperature' => $this->faker->randomFloat(2, 36, 40),
            'oxygen_saturation' => $this->faker->randomFloat(2, 90, 100),
            'sugar_level' => $this->faker->randomFloat(2, 70, 180),
            'notes' => $this->faker->text(),
        ];
    }
}
