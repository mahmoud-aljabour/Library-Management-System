<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Author;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Category;
use App\Models\Member;
use App\Models\Publisher;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::factory()->admin()->create([
            'name' => 'System Admin',
            'email' => 'admin@library.com',
            'password' => Hash::make('password'),
            'role' => UserRole::Admin,
        ]);

        User::factory()->librarian()->create([
            'name' => 'Library Staff',
            'email' => 'librarian@library.com',
            'password' => Hash::make('password'),
            'role' => UserRole::Librarian,
        ]);

        Author::factory(10)->create();
        Publisher::factory(10)->create();
        Category::factory(8)->create();
        Book::factory(50)->create();
        Member::factory(20)->create();
        Borrowing::factory(30)->create();
        Review::factory(20)->create([
            'reviewable_id' => Book::all()->random()->id,
            'reviewable_type' => Book::class,
        ]);
        Review::factory(20)->create([
            'reviewable_id' => Author::all()->random()->id,
            'reviewable_type' => Author::class,
        ]);
    }
}
