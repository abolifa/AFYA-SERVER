<?php

namespace Database\Factories;

use App\Models\Center;
use App\Models\Order;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        return [
            'center_id' => Center::factory(),
            'patient_id' => Patient::factory(),
            'appointment_id' => null,
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'cancelled']),
        ];
    }
}
