<?php

use App\Models\Book;
use App\Models\Member;

test('admin can toggle member status', function () {
    $admin = createAdmin();
    $member = Member::factory()->create(['is_active' => true]);

    $this->actingAs($admin)
        ->patch(route('members.toggle-status', $member))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($member->fresh()->is_active)->toBeFalse();
});

test('admin cannot delete member with active borrowings', function () {
    $admin = createAdmin();
    $member = Member::factory()->create();
    $book = Book::factory()->create();

    \App\Models\Borrowing::factory()->create([
        'book_id' => $book->id,
        'member_id' => $member->id,
        'returned_at' => null,
        'status' => 'borrowed',
    ]);

    $this->actingAs($admin)
        ->delete(route('members.destroy', $member))
        ->assertRedirect(route('members.index'))
        ->assertSessionHas('error');

    expect(Member::find($member->id))->not->toBeNull();
});

test('admin can delete member without active borrowings', function () {
    $admin = createAdmin();
    $member = Member::factory()->create();

    $this->actingAs($admin)
        ->delete(route('members.destroy', $member))
        ->assertRedirect(route('members.index'));

    expect(Member::find($member->id))->toBeNull();
});

test('librarian cannot create members', function () {
    $librarian = createLibrarian();

    $this->actingAs($librarian)
        ->post(route('members.store'), [
            'name' => 'Blocked Member',
            'email' => 'blocked@example.com',
            'membership_date' => now()->format('Y-m-d'),
        ])
        ->assertForbidden();
});
