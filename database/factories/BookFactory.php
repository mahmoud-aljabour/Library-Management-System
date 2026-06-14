<?php

namespace Database\Factories;

use App\Models\Author;
use App\Models\Publisher;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $stauts = ['available', 'borrowed', 'reserved', 'archived'];

        return [
            'title' => fake()->sentence(4),
            'isbn' => fake()->isbn13(),
            'description' => fake()->paragraph(),
            'publish_date' => fake()->date(),
            'page_count' => fake()->numberBetween(10, 300),
            'language' => fake()->languageCode(),
            'edition' => fake()->numberBetween(1, 10),
            'total_copies' => fake()->numberBetween(1, 20),
            'author_id' => Author::factory(),
            'publisher_id' => Publisher::factory(),
            'status' => fake()->randomElement(['available', 'borrowed', 'reserved', 'archived']),
        ];
    }
}
