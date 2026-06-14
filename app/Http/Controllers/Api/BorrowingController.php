<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BorrowingResource;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Member;
use App\Services\BorrowingService;
use Illuminate\Http\Request;
use InvalidArgumentException;

class BorrowingController extends Controller
{
    public function __construct(private BorrowingService $borrowingService)
    {
        $this->authorizeResource(Borrowing::class, 'borrowing');
    }

    public function index(Request $request)
    {
        $borrowings = Borrowing::with(['book', 'member'])
            ->filter($request->query())
            ->orderByDesc('created_at')
            ->paginate(15);

        return BorrowingResource::collection($borrowings);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|integer|exists:members,id',
            'book_id' => 'required|integer|exists:books,id',
            'borrowed_at' => 'required|date',
            'due_date' => 'required|date|after_or_equal:borrowed_at',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $borrowing = $this->borrowingService->borrow(
                Member::findOrFail($validated['member_id']),
                Book::findOrFail($validated['book_id']),
                $validated['borrowed_at'],
                $validated['due_date'],
            );

            if (! empty($validated['notes'])) {
                $borrowing->update(['notes' => $validated['notes']]);
            }
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return (new BorrowingResource($borrowing->load(['book', 'member'])))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Borrowing $borrowing)
    {
        $borrowing->load(['book', 'member']);

        return new BorrowingResource($borrowing);
    }

    public function update(Request $request, Borrowing $borrowing)
    {
        try {
            $borrowing = $this->borrowingService->returnBook($borrowing);
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return new BorrowingResource($borrowing->load(['book', 'member']));
    }

    public function destroy(Borrowing $borrowing)
    {
        if ($borrowing->returned_at === null) {
            return response()->json([
                'message' => 'Return the book before deleting the borrowing record.',
            ], 422);
        }

        $borrowing->delete();

        return response()->json(['message' => 'Borrowing deleted successfully.']);
    }
}
