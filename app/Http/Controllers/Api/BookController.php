<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Book::class, 'book');
    }

    public function index(Request $request)
    {
        $books = Book::with('author', 'categories')
            ->filter($request->query())
            ->paginate(15);

        return BookResource::collection($books);
    }

    public function store(StoreBookRequest $request)
    {
        $validated = $request->validated();
        $book = Book::create($validated);

        if (! empty($validated['category_ids'])) {
            $book->categories()->sync($validated['category_ids']);
        }

        return new BookResource($book->load('author', 'categories'));
    }

    public function show(Book $book)
    {
        $book->load('author', 'publisher', 'categories');

        return new BookResource($book);
    }

    public function update(UpdateBookRequest $request, Book $book)
    {
        $validated = $request->validated();
        $book->update($validated);
        $book->categories()->sync($validated['category_ids']);

        return new BookResource($book->load('author', 'categories'));
    }

    public function destroy(Book $book)
    {
        if ($book->borrowings()->active()->exists()) {
            return response()->json([
                'message' => 'Cannot delete a book with active borrowings.',
            ], 422);
        }

        $book->delete();

        return response()->json([
            'message' => 'Book deleted successfully.',
        ]);
    }
}
