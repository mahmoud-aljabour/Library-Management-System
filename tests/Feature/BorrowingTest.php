<?php

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Member;

test('librarian can borrow a book via web', function () {
    $librarian = createLibrarian();
    $member = Member::factory()->create(['is_active' => true]);
    $book = Book::factory()->create(['total_copies' => 5, 'status' => 'available']);

    $this->actingAs($librarian)
        ->post(route('borrowings.store'), [
            'member_id' => $member->id,
            'book_id' => $book->id,
            'borrowed_at' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(14)->format('Y-m-d'),
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(Borrowing::where('book_id', $book->id)->where('member_id', $member->id)->exists())->toBeTrue();
});

test('librarian can return a borrowed book', function () {
    $librarian = createLibrarian();
    $book = Book::factory()->create(['total_copies' => 1, 'status' => 'available']);
    $member = Member::factory()->create(['is_active' => true]);
    $borrowing = Borrowing::factory()->create([
        'book_id' => $book->id,
        'member_id' => $member->id,
        'borrowed_at' => now()->subDays(5),
        'due_date' => now()->addDays(9),
        'returned_at' => null,
        'status' => 'borrowed',
    ]);

    $this->actingAs($librarian)
        ->put(route('borrowings.update', $borrowing))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($borrowing->fresh()->status)->toBe('returned');
});

test('borrow fails for inactive member via web', function () {
    $librarian = createLibrarian();
    $member = Member::factory()->create(['is_active' => false]);
    $book = Book::factory()->create(['total_copies' => 5, 'status' => 'available']);

    $this->actingAs($librarian)
        ->post(route('borrowings.store'), [
            'member_id' => $member->id,
            'book_id' => $book->id,
            'borrowed_at' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(14)->format('Y-m-d'),
        ])
        ->assertRedirect()
        ->assertSessionHas('error');
});

test('borrowings index can filter by overdue status', function () {
    $user = createLibrarian();
    $book = Book::factory()->create();
    $member = Member::factory()->create();

    Borrowing::factory()->create([
        'book_id' => $book->id,
        'member_id' => $member->id,
        'status' => 'overdue',
        'returned_at' => null,
        'due_date' => now()->subDays(3),
    ]);

    Borrowing::factory()->create([
        'book_id' => $book->id,
        'member_id' => $member->id,
        'status' => 'returned',
        'returned_at' => now(),
    ]);

    $this->actingAs($user)
        ->get(route('borrowings.index', ['status' => 'overdue']))
        ->assertOk();
});
