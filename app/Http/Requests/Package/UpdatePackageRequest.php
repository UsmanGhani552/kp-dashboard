<?php

namespace App\Http\Requests\Package;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePackageRequest extends FormRequest
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
            'description' => 'string',
            'category_id' => 'exists:categories,id',
            'price' => 'numeric|min:0',
            'additional_notes' => 'nullable|string',
            'document' => 'file|mimes:pdf,doc,docx,.txt',
            // 'deliverables' => 'array',
            // 'deliverables.*.name' => 'string|max:255',
        ];
    }
}
