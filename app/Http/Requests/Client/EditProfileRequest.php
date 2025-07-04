<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class EditProfileRequest extends FormRequest
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
            'name' => 'string|max:255',
            'email' => 'email|max:255|unique:users,email,' . $this->route('id'),
            'phone' => 'string|max:20',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'password' => 'string|min:8|confirmed',
            'emails' => 'array',
            'emails.*' => 'email|max:255|unique:client_emails,email,' . $this->route('id') . ',client_id' // Assuming client_id is the foreign
        ];
    }
}
