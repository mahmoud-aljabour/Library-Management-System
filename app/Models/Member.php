<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'membership_date',
        'is_active',
    ];

    protected $casts = [
        'membership_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function currentBorrowings()
    {
        return $this->hasMany(Borrowing::class)->active();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function getMembershipDurationAttribute(): string
    {
        return Carbon::parse($this->membership_date)
            ->diffForHumans(now(), Carbon::DIFF_ABSOLUTE);
    }
}
