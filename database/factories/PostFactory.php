<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(6, true),
            'slug' => $this->faker->slug(),
            'content' => $this->faker->paragraphs(3, true),
            'active' => true,
            'tags' => json_encode($this->faker->words(3, true)),
        ];
    }
}
