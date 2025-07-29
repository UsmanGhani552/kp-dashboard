<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'username' => 'string|max:255|unique:users,username',
            'phone' => 'required|string|max:12',
            'address' => 'string',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'password' => 'required|string|min:8',
        ];
    }
}
