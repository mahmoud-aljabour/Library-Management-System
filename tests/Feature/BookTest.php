<?php

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Category;

test('admin can create a book', function () {
    $admin = createAdmin();

    $this->actingAs($admin)
        ->post(route('books.store'), bookPayload())
        ->assertRedirect(route('books.index'));

    expect(Book::count())->toBe(1);
});

test('librarian cannot create a book', function () {
    $librarian = createLibrarian();

    $this->actingAs($librarian)
        ->post(route('books.store'), bookPayload())
        ->assertForbidden();
});

test('admin can update a book', function () {
    $admin = createAdmin();
    $book = Book::factory()->create();
    $category = Category::factory()->create();

    $this->actingAs($admin)
        ->put(route('books.update', $book), [
            'title' => 'Updated Title',
            'isbn' => $book->isbn,
            'author_id' => $book->author_id,
            'category_ids' => [$category->id],
            'status' => 'available',
        ])
        ->assertRedirect(route('books.index'));

    expect($book->fresh()->title)->toBe('Updated Title');
});

test('books index can filter by title', function () {
    $user = createLibrarian();
    Book::factory()->create(['title' => 'Unique Alpha Book']);
    Book::factory()->create(['title' => 'Other Book']);

    $this->actingAs($user)
        ->get(route('books.index', ['title' => 'Alpha']))
        ->assertOk()
        ->assertSee('Unique Alpha Book')
        ->assertDontSee('Other Book');
});

test('admin cannot delete book with active borrowings', function () {
    $admin = createAdmin();
    $book = Book::factory()->create();
    Borrowing::factory()->create([
        'book_id' => $book->id,
        'member_id' => \App\Models\Member::factory()->create()->id,
        'returned_at' => null,
        'status' => 'borrowed',
    ]);

    $this->actingAs($admin)
        ->delete(route('books.destroy', $book))
        ->assertRedirect(route('books.index'))
        ->assertSessionHas('error');

    expect(Book::find($book->id))->not->toBeNull();
});

test('authenticated user can view book details', function () {
    $user = createLibrarian();
    $book = Book::factory()->create();

    $this->actingAs($user)
        ->get(route('books.show', $book))
        ->assertOk()
        ->assertSee($book->title);
});
