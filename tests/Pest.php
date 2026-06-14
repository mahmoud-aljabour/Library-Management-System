<?php

use App\Models\Author;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

pest()->extend(Tests\TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

pest()->extend(Tests\TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Unit');

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

function createAdmin(): User
{
    return User::factory()->admin()->create();
}

function createLibrarian(): User
{
    return User::factory()->librarian()->create();
}

function bookPayload(array $overrides = []): array
{
    $author = $overrides['author'] ?? Author::factory()->create();
    $category = $overrides['category'] ?? Category::factory()->create();

    unset($overrides['author'], $overrides['category']);

    return array_merge([
        'title' => 'Test Book '.fake()->unique()->word(),
        'isbn' => fake()->isbn13(),
        'author_id' => $author->id,
        'category_ids' => [$category->id],
        'total_copies' => 5,
        'status' => 'available',
    ], $overrides);
}
