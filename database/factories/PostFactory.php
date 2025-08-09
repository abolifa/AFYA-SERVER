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
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraphs(3, true),
            'slug' => $this->faker->unique()->slug,
            'main_image' => $this->faker->imageUrl(640, 480, 'posts', true),
            'is_published' => $this->faker->boolean(50), // 50% chance of being published
            'tags' => $this->faker->words(3, true), // Comma-separated string of tags
            'images' => json_encode($this->faker->imageUrl(3, 640, 480, 'posts', true)),
            'user_id' => null, // Assuming user_id is nullable and will be set later
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}


//$table->id();
//$table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
//$table->string('title');
//$table->text('content');
//$table->string('slug')->unique();
//$table->string('main_image')->nullable();
//$table->boolean('is_published')->default(false);
//$table->json('tags')->nullable();
//$table->json('images')->nullable();
//$table->timestamps();
