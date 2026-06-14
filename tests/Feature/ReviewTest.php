<?php

use App\Models\Book;
use App\Models\Member;
use App\Models\Review;

test('librarian can add a book review', function () {
    $librarian = createLibrarian();
    $book = Book::factory()->create();
    $member = Member::factory()->create();

    $this->actingAs($librarian)
        ->post(route('reviews.store'), [
            'book_id' => $book->id,
            'member_id' => $member->id,
            'rating' => 5,
            'comment' => 'Excellent read.',
        ])
        ->assertRedirect(route('books.show', $book))
        ->assertSessionHas('success');

    expect(Review::where('reviewable_id', $book->id)->where('member_id', $member->id)->exists())->toBeTrue();
});

test('review requires valid rating', function () {
    $admin = createAdmin();
    $book = Book::factory()->create();
    $member = Member::factory()->create();

    $this->actingAs($admin)
        ->post(route('reviews.store'), [
            'book_id' => $book->id,
            'member_id' => $member->id,
            'rating' => 10,
        ])
        ->assertSessionHasErrors('rating');
});
