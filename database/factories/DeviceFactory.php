<?php

namespace Database\Factories;

use App\Models\Device;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Device>
 */
class DeviceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'manufacturer' => $this->faker->company(),
            'model' => $this->faker->word(),
            'serial_number' => $this->faker->unique()->numerify('SN-#####'),
            'active' => true,
        ];
    }
}
