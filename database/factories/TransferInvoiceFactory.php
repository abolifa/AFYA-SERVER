<?php

namespace Database\Factories;

use App\Models\Center;
use App\Models\TransferInvoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TransferInvoice>
 */
class TransferInvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        return [
            'from_center_id' => Center::factory(),
            'to_center_id' => Center::factory(),
            'status' => $this->faker->randomElement([
                'pending', 'confirmed', 'cancelled'
            ]),
        ];
    }
}
