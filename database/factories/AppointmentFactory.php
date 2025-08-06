<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Center;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'center_id' => fn(array $attributes) => $attributes['center_id'] ?? Center::factory(),
            'patient_id' => fn(array $attributes) => $attributes['patient_id'] ?? Patient::factory(),
            'doctor_id' => fn(array $attributes) => $attributes['doctor_id'] ?? User::factory(),
            'date' => $this->faker->date(),
            'time' => $this->faker->time(),
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'cancelled', 'completed']),
            'intended' => $this->faker->boolean(),
            'notes' => $this->faker->optional()->text(),
        ];
    }
}
