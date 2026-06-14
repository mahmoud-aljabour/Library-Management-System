<?php

namespace App\Http\Requests;

use App\Models\Author;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAuthorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('author'));
    }

    public function rules(): array
    {
        $id = $this->route('author');

        return [
            'name' => 'required|string|max:255|unique:authors,name,' . $id,
            'bio' => 'nullable|string|max:2000',
            'birth_date' => 'nullable|date',
            'nationality' => 'nullable|string|max:100',
        ];
    }
}
