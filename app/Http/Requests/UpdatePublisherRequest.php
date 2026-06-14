<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePublisherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('publisher'));
    }

    public function rules(): array
    {
        $id = $this->route('publisher');

        return [
            'name' => 'required|string|max:255|unique:publishers,name,'.$id,
            'address' => 'nullable|string|max:500',
            'website' => 'nullable|url|max:255',
        ];
    }
}
