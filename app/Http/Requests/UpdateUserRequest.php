<?php

namespace App\Http\Requests;

use App\Rules\Pesel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $userId = $this->route('user')->id;

        return [
            'role_id' => ['required', Rule::exists('roles', 'id')],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'pesel' => ['required', 'string', 'regex:/^\d{11}$/', Rule::unique('users', 'pesel')->ignore($userId), new Pesel],
            // Hasło opcjonalne — ustawiane tylko, gdy pole zostanie wypełnione.
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'role_id' => 'rola',
            'first_name' => 'imię',
            'last_name' => 'nazwisko',
            'email' => 'adres e-mail',
            'pesel' => 'PESEL',
            'password' => 'hasło',
        ];
    }
}
