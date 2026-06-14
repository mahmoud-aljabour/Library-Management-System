<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Borrowing extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'member_id',
        'borrowed_at',
        'due_date',
        'returned_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'borrowed_at' => 'datetime',
        'due_date' => 'date',
        'returned_at' => 'datetime',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->whereNull('returned_at')
            ->where('due_date', '<', Carbon::today());
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull('returned_at')
            ->whereIn('status', ['borrowed', 'overdue']);
    }

    public function scopeReturned(Builder $query): Builder
    {
        return $query->where('status', 'returned');
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        $query->when($filters['status'] ?? null, function (Builder $query, string $status) {
            match ($status) {
                'active' => $query->active(),
                'overdue' => $query->overdue(),
                'returned' => $query->returned(),
                default => $query->where('status', $status),
            };
        });

        $query->when($filters['search'] ?? null, function (Builder $query, string $search) {
            $query->where(function (Builder $query) use ($search) {
                $query->whereHas('book', function (Builder $query) use ($search) {
                    $query->where('title', 'like', "%{$search}%");
                })->orWhereHas('member', function (Builder $query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            });
        });

        return $query;
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->returned_at === null
            && Carbon::parse($this->due_date)->isPast();
    }
}
