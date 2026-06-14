<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BorrowingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'book_id' => $this->book_id,
            'member_id' => $this->member_id,
            'book_title' => $this->whenLoaded('book', fn () => $this->book->title),
            'member_name' => $this->whenLoaded('member', fn () => $this->member->name),
            'borrowed_at' => $this->borrowed_at?->toIso8601String(),
            'due_date' => $this->due_date?->format('Y-m-d'),
            'returned_at' => $this->returned_at?->toIso8601String(),
            'status' => $this->status,
            'notes' => $this->notes,
        ];
    }
}
