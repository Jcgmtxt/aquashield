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
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'identity_type' => ['required', Rule::enum(IdentityType::class)],
            'identity_number' => ['required', 'string', 'max:100', Rule::unique('clients', 'identity_number')],
            'phone_number' => ['required', 'string', 'max:10', Rule::unique('clients', 'phone_number')],
            'email' => ['required', 'string', 'email', 'max:100', Rule::unique('clients', 'email')],
        ];
    }
}
