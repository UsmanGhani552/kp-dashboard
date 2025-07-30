<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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
            'email' => [
                'email:rfc,dns',
                'max:255',
                Rule::unique('users')->ignore(auth()->id())
            ],
            'phone' => 'string|max:20',
            'address' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'password' => 'nullable|string|min:8|confirmed',
            'emails' => 'array',
        'emails.*' => [
            'email:rfc,dns',
            'max:255',
            function ($attribute, $value, $fail)  {
                // Check if email exists for any OTHER client
                $exists = DB::table('client_emails')
                    ->where('email', $value)
                    ->where('client_id', '!=', auth()->user()->id)
                    ->exists();
                    
                if ($exists) {
                    $fail("The email $value already exists for another client.");
                }
            }
        ]
        ];
    }
    public function messages()
    {
        return [
            'emails.*.email' => 'The email must be a valid email address.',
            'emails.*.unique' => 'The email :input already exists for this client.',
        ];
    }
}
