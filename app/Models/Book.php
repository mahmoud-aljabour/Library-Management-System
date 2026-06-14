<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'isbn',
        'description',
        'publish_date',
        'page_count',
        'language',
        'edition',
        'total_copies',
        'author_id',
        'publisher_id',
        'status',
    ];

    protected $casts = [
        'publish_date' => 'date',
    ];

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function publisher()
    {
        return $this->belongsTo(Publisher::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function currentBorrowing()
    {
        return $this->hasOne(Borrowing::class)
            ->whereNull('returned_at')
            ->latestOfMany('borrowed_at');
    }

    public function scopeFilter(Builder $builder, $filters): Builder
    {
        $builder->when($filters['title'] ?? false, function ($builder, $value) {
            $builder->where('title', 'LIKE', "%{$value}%");
        });

        $builder->when($filters['status'] ?? false, function ($builder, $value) {
            $builder->where('status', $value);
        });

        $builder->when($filters['search'] ?? false, function ($builder, $value) {
            $builder->where(function ($query) use ($value) {
                $query->where('title', 'LIKE', "%{$value}%")
                    ->orWhere('isbn', 'LIKE', "%{$value}%");
            });
        });

        $builder->when($filters['language'] ?? false, function ($builder, $value) {
            $builder->where('language', 'LIKE', "%{$value}%");
        });

        return $builder;
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', 'available');
    }

    public function scopeByLanguage(Builder $query, $lang): Builder
    {
        return $query->where('language', $lang);
    }

    public function getIsAvailableAttribute(): bool
    {
        return $this->status === 'available' && $this->available_copies > 0;
    }

    public function getAvailableCopiesAttribute(): int
    {
        $activeBorrowings = $this->borrowings()
            ->whereNull('returned_at')
            ->whereIn('status', ['borrowed', 'overdue'])
            ->count();

        return max(0, $this->total_copies - $activeBorrowings);
    }
}
