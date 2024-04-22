<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileDetailUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:255',
            'profession' => 'nullable|string|max:255',
            'about_text' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:255',
            'contact_telegram' => 'nullable|string|max:255',
            'avatar' => 'nullable',
            'jobs_count' => 'nullable',
            'skills' => 'nullable',
        ];
    }
}
