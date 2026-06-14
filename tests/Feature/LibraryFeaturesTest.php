<?php

use App\Models\Book;
use App\Models\Member;
use App\Models\User;
use App\Services\BorrowingService;

test('admin can create a member', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('members.store'), [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'membership_date' => now()->format('Y-m-d'),
            'is_active' => '1',
        ])
        ->assertRedirect(route('members.index'));

    expect(Member::where('email', 'john@example.com')->exists())->toBeTrue();
});

test('borrowing service enforces max borrowings limit', function () {
    $member = Member::factory()->create(['is_active' => true]);
    $service = app(BorrowingService::class);
    $max = config('library.max_borrowings_per_member');

    for ($i = 0; $i < $max; $i++) {
        $book = Book::factory()->create(['total_copies' => 5, 'status' => 'available']);
        $service->borrow($member, $book, now()->format('Y-m-d'), now()->addDays(14)->format('Y-m-d'));
    }

    $extraBook = Book::factory()->create(['total_copies' => 5, 'status' => 'available']);

    expect(fn () => $service->borrow(
        $member,
        $extraBook,
        now()->format('Y-m-d'),
        now()->addDays(14)->format('Y-m-d'),
    ))->toThrow(InvalidArgumentException::class);
});

test('mark overdue command updates borrowings', function () {
    $book = Book::factory()->create();
    $member = Member::factory()->create();

    \App\Models\Borrowing::factory()->create([
        'book_id' => $book->id,
        'member_id' => $member->id,
        'borrowed_at' => now()->subDays(20),
        'due_date' => now()->subDays(5),
        'status' => 'borrowed',
        'returned_at' => null,
    ]);

    $this->artisan('library:mark-overdue')->assertSuccessful();

    expect(\App\Models\Borrowing::where('status', 'overdue')->count())->toBe(1);
});

test('borrowings index requires authentication', function () {
    $this->get(route('borrowings.index'))->assertRedirect('/login');
});

test('authenticated user can view borrowings page', function () {
    $user = User::factory()->librarian()->create();

    $this->actingAs($user)
        ->get(route('borrowings.index'))
        ->assertOk();
});

test('api members endpoint returns data for authenticated user', function () {
    $user = User::factory()->admin()->create();
    Member::factory()->create();

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/members')
        ->assertOk();
});
