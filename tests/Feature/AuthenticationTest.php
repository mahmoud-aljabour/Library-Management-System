<?php

use App\Models\Book;
use App\Models\User;

test('dashboard requires authentication', function () {
    $this->get('/dashboard')->assertRedirect('/login');
});

test('authenticated admin can access dashboard', function () {
    $user = User::factory()->admin()->create();

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertOk();
});

test('authenticated librarian can access dashboard', function () {
    $user = User::factory()->librarian()->create();

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertOk();
});

test('admin can create books page', function () {
    $user = User::factory()->admin()->create();

    $this->actingAs($user)
        ->get(route('books.create'))
        ->assertOk();
});

test('librarian cannot access create books page', function () {
    $user = User::factory()->librarian()->create();

    $this->actingAs($user)
        ->get(route('books.create'))
        ->assertForbidden();
});

test('librarian cannot delete books', function () {
    $user = User::factory()->librarian()->create();
    $book = Book::factory()->create();

    $this->actingAs($user)
        ->delete(route('books.destroy', $book))
        ->assertForbidden();
});

test('admin can delete books without active borrowings', function () {
    $user = User::factory()->admin()->create();
    $book = Book::factory()->create();

    $this->actingAs($user)
        ->delete(route('books.destroy', $book))
        ->assertRedirect(route('books.index'));
});

test('api books endpoint requires authentication', function () {
    $this->getJson('/api/books')->assertUnauthorized();
});

test('api login returns token for valid credentials', function () {
    $user = User::factory()->admin()->create([
        'email' => 'api@library.com',
        'password' => bcrypt('password'),
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'api@library.com',
        'password' => 'password',
    ]);

    $response->assertOk()
        ->assertJsonStructure(['user', 'token', 'token_type']);
});

test('authenticated api user can list books', function () {
    $user = User::factory()->admin()->create();
    Book::factory()->create();

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/books')
        ->assertOk();
});
