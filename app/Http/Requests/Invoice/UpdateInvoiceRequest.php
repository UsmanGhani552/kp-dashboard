<?php

namespace App\Http\Requests\Invoice;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInvoiceRequest extends FormRequest
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
            'client_id' => 'exists:users,id',
            'title' => 'string|max:255',
            'price' => 'numeric|min:0',
            'remaining_price' => 'numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'category_id' => 'exists:categories,id',
            'brand_id' => 'exists:brands,id',
            'payment_type_id' => 'exists:payment_types,id',
            'sale_type' => 'string',
        ];
    }
}
