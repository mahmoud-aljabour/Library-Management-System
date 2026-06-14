<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class BorrowingService
{
    public function borrow(Member $member, Book $book, string $borrowedAt, string $dueDate): Borrowing
    {
        if (! $member->is_active) {
            throw new InvalidArgumentException('This member is not active.');
        }

        if ($book->available_copies <= 0) {
            throw new InvalidArgumentException('No copies available for this book.');
        }

        $maxBorrowings = config('library.max_borrowings_per_member');

        if ($member->currentBorrowings()->count() >= $maxBorrowings) {
            throw new InvalidArgumentException("Member has reached the maximum of {$maxBorrowings} active borrowings.");
        }

        return DB::transaction(function () use ($member, $book, $borrowedAt, $dueDate) {
            $borrowing = Borrowing::create([
                'member_id' => $member->id,
                'book_id' => $book->id,
                'borrowed_at' => $borrowedAt,
                'due_date' => $dueDate,
                'status' => 'borrowed',
            ]);

            $this->syncBookStatus($book->fresh());

            return $borrowing;
        });
    }

    public function returnBook(Borrowing $borrowing): Borrowing
    {
        if ($borrowing->returned_at !== null) {
            throw new InvalidArgumentException('This book has already been returned.');
        }

        return DB::transaction(function () use ($borrowing) {
            $borrowing->update([
                'returned_at' => now(),
                'status' => 'returned',
            ]);

            $this->syncBookStatus($borrowing->book->fresh());

            return $borrowing->fresh();
        });
    }

    public function markOverdueBorrowings(): int
    {
        return Borrowing::query()
            ->whereNull('returned_at')
            ->where('status', 'borrowed')
            ->where('due_date', '<', Carbon::today())
            ->update(['status' => 'overdue']);
    }

    public function syncBookStatus(Book $book): void
    {
        $availableCopies = $book->available_copies;

        if ($availableCopies <= 0) {
            $book->update(['status' => 'borrowed']);
        } elseif ($book->status === 'borrowed' && $availableCopies > 0) {
            $book->update(['status' => 'available']);
        }
    }
}
