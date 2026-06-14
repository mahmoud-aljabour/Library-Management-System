<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Services\BorrowingService;
use Illuminate\Http\Request;
use InvalidArgumentException;

class BorrowingController extends Controller
{
    public function __construct(private BorrowingService $borrowingService)
    {
        $this->middleware('can:viewAny,' . Borrowing::class)->only('index');
    }

    public function index(Request $request)
    {
        $borrowings = Borrowing::with(['book', 'member'])
            ->filter($request->query())
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('borrowings.index', compact('borrowings'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Borrowing::class);

        $validated = $request->validate([
            'member_id' => 'required|integer|exists:members,id',
            'book_id' => 'required|integer|exists:books,id',
            'borrowed_at' => 'required|date',
            'due_date' => 'required|date|after_or_equal:borrowed_at',
        ]);

        try {
            $this->borrowingService->borrow(
                \App\Models\Member::findOrFail($validated['member_id']),
                \App\Models\Book::findOrFail($validated['book_id']),
                $validated['borrowed_at'],
                $validated['due_date'],
            );
        } catch (InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('success', 'Book borrowed successfully.');
    }

    public function update(Request $request, Borrowing $borrowing)
    {
        $this->authorize('update', $borrowing);

        try {
            $this->borrowingService->returnBook($borrowing);
        } catch (InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('success', 'Book returned successfully.');
    }
}
