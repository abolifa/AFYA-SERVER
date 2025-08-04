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
            ['name' => 'طرابلس', 'lat' => 32.8872, 'lng' => 13.1913],
            ['name' => 'بنغازي', 'lat' => 32.1167, 'lng' => 20.0667],
            ['name' => 'مصراتة', 'lat' => 32.3783, 'lng' => 15.0906],
            ['name' => 'سبها', 'lat' => 27.0377, 'lng' => 14.4283],
            ['name' => 'سرت', 'lat' => 31.2089, 'lng' => 16.5887],
            ['name' => 'الزاوية', 'lat' => 32.7631, 'lng' => 12.7365],
            ['name' => 'بني وليد', 'lat' => 32.4674, 'lng' => 14.5687],
            ['name' => 'درنة', 'lat' => 31.9545, 'lng' => 21.7344],
            ['name' => 'طبرق', 'lat' => 32.0836, 'lng' => 23.9764],
        ];

        static $centerCount = 1;
        $city = $this->faker->randomElement($cityCenters);
        $lat = $city['lat'] + $this->faker->randomFloat(6, -0.045, 0.045);
        $lng = $city['lng'] + $this->faker->randomFloat(6, -0.045, 0.045);

        return [
            'name' => 'مركز ' . $city['name'] . ' رقم ' . $centerCount++,
            'phone' => $this->faker->unique()->numerify('091#######'),
            'alt_phone' => $this->faker->optional()->numerify('092#######'),
            'address' => $this->faker->optional()->address(),
            'street' => $this->faker->optional()->streetName(),
            'city' => $city['name'],
            'latitude' => $lat,
            'longitude' => $lng,
        ];
    }
}
