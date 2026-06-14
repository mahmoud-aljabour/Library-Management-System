<?php

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Member;
use App\Services\BorrowingService;

test('borrow creates a borrowing and syncs book status', function () {
    $service = app(BorrowingService::class);
    $member = Member::factory()->create(['is_active' => true]);
    $book = Book::factory()->create(['total_copies' => 2, 'status' => 'available']);

    $borrowing = $service->borrow(
        $member,
        $book,
        now()->format('Y-m-d'),
        now()->addDays(14)->format('Y-m-d'),
    );

    expect($borrowing->status)->toBe('borrowed')
        ->and($book->fresh()->status)->toBe('available');
});

test('borrow throws when member is inactive', function () {
    $service = app(BorrowingService::class);
    $member = Member::factory()->create(['is_active' => false]);
    $book = Book::factory()->create(['total_copies' => 5, 'status' => 'available']);

    expect(fn () => $service->borrow(
        $member,
        $book,
        now()->format('Y-m-d'),
        now()->addDays(14)->format('Y-m-d'),
    ))->toThrow(InvalidArgumentException::class, 'This member is not active.');
});

test('borrow throws when no copies available', function () {
    $service = app(BorrowingService::class);
    $member = Member::factory()->create(['is_active' => true]);
    $book = Book::factory()->create(['total_copies' => 1, 'status' => 'available']);

    $service->borrow($member, $book, now()->format('Y-m-d'), now()->addDays(14)->format('Y-m-d'));

    expect(fn () => $service->borrow(
        $member,
        $book->fresh(),
        now()->format('Y-m-d'),
        now()->addDays(14)->format('Y-m-d'),
    ))->toThrow(InvalidArgumentException::class, 'No copies available for this book.');
});

test('returnBook marks borrowing as returned and frees book', function () {
    $service = app(BorrowingService::class);
    $member = Member::factory()->create(['is_active' => true]);
    $book = Book::factory()->create(['total_copies' => 1, 'status' => 'available']);

    $borrowing = $service->borrow(
        $member,
        $book,
        now()->format('Y-m-d'),
        now()->addDays(14)->format('Y-m-d'),
    );

    expect($book->fresh()->status)->toBe('borrowed');

    $returned = $service->returnBook($borrowing);

    expect($returned->status)->toBe('returned')
        ->and($returned->returned_at)->not->toBeNull()
        ->and($book->fresh()->status)->toBe('available');
});

test('returnBook throws when already returned', function () {
    $service = app(BorrowingService::class);
    $book = Book::factory()->create();
    $member = Member::factory()->create();
    $borrowing = Borrowing::factory()->create([
        'book_id' => $book->id,
        'member_id' => $member->id,
        'returned_at' => now(),
        'status' => 'returned',
    ]);

    expect(fn () => $service->returnBook($borrowing))
        ->toThrow(InvalidArgumentException::class, 'This book has already been returned.');
});

test('markOverdueBorrowings updates past-due active borrowings', function () {
    $service = app(BorrowingService::class);
    $book = Book::factory()->create();
    $member = Member::factory()->create();

    Borrowing::factory()->create([
        'book_id' => $book->id,
        'member_id' => $member->id,
        'borrowed_at' => now()->subDays(20),
        'due_date' => now()->subDays(5),
        'status' => 'borrowed',
        'returned_at' => null,
    ]);

    expect($service->markOverdueBorrowings())->toBe(1)
        ->and(Borrowing::where('status', 'overdue')->count())->toBe(1);
});
