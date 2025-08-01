<?php

namespace App\Http\Requests\Brand;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBrandRequest extends FormRequest
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
            'email' => 'email|max:255|unique:brands,email,' . $this->route()->id,
            'address' => 'nullable|string',
            'logo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'logo_mini' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }
}
