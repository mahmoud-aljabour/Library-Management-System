<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Member;
use App\Models\Publisher;

class BookController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Book::class, 'book');
    }

    public function index()
    {
        $books = Book::with('author')
            ->filter(request()->query())
            ->paginate(15);

        return view('books.index', compact('books'));
    }

    public function create()
    {
        $authors = Author::orderBy('name')->get();
        $publishers = Publisher::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('books.create', compact('publishers', 'authors', 'categories'));
    }

    public function store(StoreBookRequest $request)
    {
        $validated = $request->validated();
        $book = Book::create($validated);
        $book->categories()->sync($validated['category_ids']);

        return redirect()
            ->route('books.index')
            ->with('success', 'Book created successfully.');
    }

    public function show(Book $book)
    {
        $members = Member::active()->orderBy('name')->get(['id', 'name']);
        $book->load([
            'author',
            'categories',
            'borrowings.member',
            'reviews.member',
        ]);

        return view('books.show', compact('book', 'members'));
    }

    public function edit(Book $book)
    {
        $authors = Author::orderBy('name')->get();
        $publishers = Publisher::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $book->load('author', 'categories');

        return view('books.edit', compact('book', 'publishers', 'authors', 'categories'));
    }

    public function update(UpdateBookRequest $request, Book $book)
    {
        $validated = $request->validated();
        $book->update($validated);
        $book->categories()->sync($validated['category_ids']);

        return redirect()
            ->route('books.index')
            ->with('success', 'Book updated successfully.');
    }

    public function destroy(Book $book)
    {
        if ($book->borrowings()->active()->exists()) {
            return redirect()
                ->route('books.index')
                ->with('error', 'Cannot delete a book with active borrowings.');
        }

        $book->delete();

        return redirect()
            ->route('books.index')
            ->with('success', 'Book deleted successfully.');
    }
}
