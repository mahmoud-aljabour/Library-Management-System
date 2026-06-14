<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Models\Book;
use App\Models\Review;

class ReviewController extends Controller
{
    public function store(StoreReviewRequest $request)
    {
        $validated = $request->validated();

        Review::create([
            'reviewable_id' => $validated['book_id'],
            'reviewable_type' => Book::class,
            'member_id' => $validated['member_id'],
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
        ]);

        return redirect()
            ->route('books.show', $validated['book_id'])
            ->with('success', 'Review added successfully.');
    }
}
