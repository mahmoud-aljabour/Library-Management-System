<?php

namespace App\Http\Requests;

use App\Models\Book;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Book::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|unique:books,title|string|max:255',
            'isbn' => 'required|string|size:13',
            'description' => 'nullable|string|max:255',
            'publish_date' => 'nullable|date',
            'page_count' => 'nullable|int|min:1',
            'language' => 'nullable|string',
            'edition' => 'nullable|int',
            'total_copies' => 'nullable|int',
            'author_id' => 'required|int|exists:authors,id',
            'publisher_id' => 'exists:publishers,id',
            'status' => 'nullable|in:available,borrowed,reserved,archived',
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id',
        ];
    }
}
