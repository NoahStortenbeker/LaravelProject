<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'sometimes',
                'nullable',
                'string',
                'max:8',
                Rule::unique(User::class, 'username')->ignore($this->user()->id),
            ],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'phone_number' => [
                'sometimes',
                'nullable',
                'string',
                'max:31',
            ],
            'date_of_birth' => [
                'sometimes',
                'nullable',
                'date',
                'before:today',
            ],
            'address_line' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],
            'zip_code' => [
                'sometimes',
                'nullable',
                'string',
                'max:16',
            ],
            'city' => [
                'sometimes',
                'nullable',
                'string',
                'max:128',
            ],
            'country' => [
                'sometimes',
                'nullable',
                'string',
                'max:128',
            ],
        ];
    }
}
