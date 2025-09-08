<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return false;
    }

    public function rules(): array
    {
        $client = $this->route('client');

        return [
            'name' => ['required','string','max:100'],
            'identity_type' => ['required', Rule::enum(IdentityType::class)],
            'identity_number' => ['required','string','max:100',Rule::unique('clients','identity_number')->ignore($client->id)],
            'phone_number' => ['required','string','max:10',Rule::unique('clients','phone_number')->ignore($client->id)],
            'email' => ['required','string','email','max:100',Rule::unique('clients','email')->ignore($client->id)],
        ];
    }
}
