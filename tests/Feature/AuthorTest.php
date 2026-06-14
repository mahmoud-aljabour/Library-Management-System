<?php

use App\Models\Author;
use App\Models\Book;

test('admin can create an author', function () {
    $admin = createAdmin();

    $this->actingAs($admin)
        ->post(route('authors.store'), [
            'name' => 'Jane Austen',
            'nationality' => 'British',
            'bio' => 'English novelist.',
        ])
        ->assertRedirect(route('authors.index'));

    expect(Author::where('name', 'Jane Austen')->exists())->toBeTrue();
});

test('librarian cannot create an author', function () {
    $librarian = createLibrarian();

    $this->actingAs($librarian)
        ->post(route('authors.store'), ['name' => 'Blocked Author'])
        ->assertForbidden();
});

test('admin can delete author without books', function () {
    $admin = createAdmin();
    $author = Author::factory()->create();

    $this->actingAs($admin)
        ->delete(route('authors.destroy', $author))
        ->assertRedirect(route('authors.index'));

    expect(Author::find($author->id))->toBeNull();
});

test('admin cannot delete author with associated books', function () {
    $admin = createAdmin();
    $author = Author::factory()->create();
    Book::factory()->create(['author_id' => $author->id]);

    $this->actingAs($admin)
        ->delete(route('authors.destroy', $author))
        ->assertRedirect(route('authors.index'))
        ->assertSessionHas('error');

    expect(Author::find($author->id))->not->toBeNull();
});

test('librarian can view authors index', function () {
    $librarian = createLibrarian();
    Author::factory()->create(['name' => 'Visible Author']);

    $this->actingAs($librarian)
        ->get(route('authors.index'))
        ->assertOk()
        ->assertSee('Visible Author');
});
