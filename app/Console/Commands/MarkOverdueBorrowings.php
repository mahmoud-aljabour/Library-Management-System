<?php

namespace App\Console\Commands;

use App\Services\BorrowingService;
use Illuminate\Console\Command;

class MarkOverdueBorrowings extends Command
{
    protected $signature = 'library:mark-overdue';

    protected $description = 'Mark active borrowings as overdue when past due date';

    public function handle(BorrowingService $borrowingService): int
    {
        $count = $borrowingService->markOverdueBorrowings();

        $this->info("Marked {$count} borrowing(s) as overdue.");

        return self::SUCCESS;
    }
}
