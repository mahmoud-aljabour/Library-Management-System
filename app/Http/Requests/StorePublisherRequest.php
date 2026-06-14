<?php

namespace App\Http\Requests;

use App\Models\Publisher;
use Illuminate\Foundation\Http\FormRequest;

class StorePublisherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Publisher::class);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:publishers,name',
            'address' => 'nullable|string|max:500',
            'website' => 'nullable|url|max:255',
        ];
    }
}
