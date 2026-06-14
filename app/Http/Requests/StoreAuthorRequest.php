<?php

namespace App\Http\Requests;

use App\Models\Author;
use Illuminate\Foundation\Http\FormRequest;

class StoreAuthorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Author::class);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:authors,name',
            'bio' => 'nullable|string|max:2000',
            'birth_date' => 'nullable|date',
            'nationality' => 'nullable|string|max:100',
        ];
    }
}
