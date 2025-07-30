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
        $center = Center::factory();
        $patient = Patient::factory()->for($center, 'center');

        return [
            'center_id' => $center,
            'patient_id' => $patient,
            'appointment_id' => null,
            'status' => $this->faker->randomElement([
                'pending', 'confirmed', 'cancelled'
            ]),
        ];
    }
}
