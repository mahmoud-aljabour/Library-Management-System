<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('member'));
    }

    public function rules(): array
    {
        $id = $this->route('member');

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:members,email,'.$id,
            'phone' => 'nullable|string|max:20',
            'membership_date' => 'required|date',
            'is_active' => 'nullable|boolean',
        ];
    }
}
