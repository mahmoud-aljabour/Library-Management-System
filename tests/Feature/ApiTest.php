<?php

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Member;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('api login rejects invalid credentials', function () {
    User::factory()->admin()->create([
        'email' => 'api@library.com',
        'password' => bcrypt('password'),
    ]);

    $this->postJson('/api/login', [
        'email' => 'api@library.com',
        'password' => 'wrong-password',
    ])->assertUnprocessable();
});

test('authenticated api user can get profile', function () {
    $user = createAdmin();

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/user')
        ->assertOk()
        ->assertJsonPath('email', $user->email)
        ->assertJsonPath('role', 'admin');
});

test('api logout revokes token', function () {
    $user = createAdmin();
    Sanctum::actingAs($user);

    $this->postJson('/api/logout')
        ->assertOk()
        ->assertJsonPath('message', 'Logged out successfully.');
});

test('api can create and return a borrowing', function () {
    $user = createLibrarian();
    $member = Member::factory()->create(['is_active' => true]);
    $book = Book::factory()->create(['total_copies' => 3, 'status' => 'available']);

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/borrowings', [
            'member_id' => $member->id,
            'book_id' => $book->id,
            'borrowed_at' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(14)->format('Y-m-d'),
            'notes' => 'API borrow test',
        ]);

    $response->assertCreated()
        ->assertJsonPath('data.notes', 'API borrow test');

    $borrowingId = $response->json('data.id');

    $this->actingAs($user, 'sanctum')
        ->putJson("/api/borrowings/{$borrowingId}")
        ->assertOk()
        ->assertJsonPath('data.status', 'returned');
});

test('api cannot delete active borrowing', function () {
    $user = createAdmin();
    $book = Book::factory()->create();
    $member = Member::factory()->create();
    $borrowing = Borrowing::factory()->create([
        'book_id' => $book->id,
        'member_id' => $member->id,
        'returned_at' => null,
        'status' => 'borrowed',
    ]);

    $this->actingAs($user, 'sanctum')
        ->deleteJson("/api/borrowings/{$borrowing->id}")
        ->assertUnprocessable();
});

test('api can delete returned borrowing', function () {
    $user = createAdmin();
    $book = Book::factory()->create();
    $member = Member::factory()->create();
    $borrowing = Borrowing::factory()->create([
        'book_id' => $book->id,
        'member_id' => $member->id,
        'returned_at' => now(),
        'status' => 'returned',
    ]);

    $this->actingAs($user, 'sanctum')
        ->deleteJson("/api/borrowings/{$borrowing->id}")
        ->assertOk();

    expect(Borrowing::find($borrowing->id))->toBeNull();
});

test('api librarian cannot create books', function () {
    $librarian = createLibrarian();

    $this->actingAs($librarian, 'sanctum')
        ->postJson('/api/books', bookPayload())
        ->assertForbidden();
});

test('api admin can create a book', function () {
    $admin = createAdmin();

    $this->actingAs($admin, 'sanctum')
        ->postJson('/api/books', bookPayload())
        ->assertCreated();
});

test('health endpoint is available', function () {
    $this->get('/up')->assertOk();
});
