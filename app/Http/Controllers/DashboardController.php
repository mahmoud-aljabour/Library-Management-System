<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Member;
use App\Services\BorrowingService;

class DashboardController extends Controller
{
    public function index(BorrowingService $borrowingService)
    {
        $borrowingService->markOverdueBorrowings();

        $totalBooks = Book::count();
        $totalMembers = Member::count();
        $activeBorrowingsCount = Borrowing::active()->count();
        $overdueCount = Borrowing::overdue()->count();
        $borrowings = Borrowing::with(['book', 'member'])
            ->orderByDesc('created_at')
            ->paginate(5);
        $overdueBorrowings = Borrowing::with(['book', 'member'])
            ->overdue()
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'totalBooks',
            'totalMembers',
            'activeBorrowingsCount',
            'overdueCount',
            'borrowings',
            'overdueBorrowings'
        ));
    }
}
