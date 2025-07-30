<?php

namespace Database\Factories;

use App\Models\Center;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Center>
 */
class CenterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $cityCenters = [
            ['lat' => 32.8872, 'lng' => 13.1913], // Tripoli
            ['lat' => 32.1167, 'lng' => 20.0667], // Benghazi
            ['lat' => 32.3783, 'lng' => 15.0906], // Misrata
            ['lat' => 27.0377, 'lng' => 14.4283], // Sabha
            ['lat' => 31.2089, 'lng' => 16.5887], // Sirte
            ['lat' => 32.7631, 'lng' => 12.7365], // Zawiya
            ['lat' => 32.4674, 'lng' => 14.5687], // Bani Walid (or replace)
            ['lat' => 31.9545, 'lng' => 21.7344], // Derna
            ['lat' => 32.0836, 'lng' => 23.9764], // Tobruk
        ];

        $center = $this->faker->randomElement($cityCenters);
// Random offset ~ up to ~5 km (0.045 degrees approx)
        $lat = $center['lat'] + $this->faker->randomFloat(6, -0.045, 0.045);
        $lng = $center['lng'] + $this->faker->randomFloat(6, -0.045, 0.045);

        $this->faker->unique(true); // clear previous uniques

        return [
            'name' => 'مركز ' . $this->faker->unique()->city(),
            'phone' => $this->faker->unique()->numerify('091#######'),
            'alt_phone' => $this->faker->optional()->numerify('092#######'),
            'address' => $this->faker->optional()->address(),
            'street' => $this->faker->optional()->streetName(),
            'city' => $this->faker->optional()->city(),
            'latitude' => $this->faker->randomElement([$lat]),
            'longitude' => $this->faker->randomElement([$lng]),
        ];
    }
}
