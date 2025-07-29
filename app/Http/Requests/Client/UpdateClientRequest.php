<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClientRequest extends FormRequest
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
            'email' => 'email|max:255|unique:users,email,' . $this->route()->id,
            'username' => 'string|max:255|unique:users,username,' . $this->route()->id,
            'phone' => 'string|max:12',
            'address' => 'string',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'password' => 'string|min:8|confirmed',
            'package_id' => 'exists:packages,id',
        ];
    }
}
