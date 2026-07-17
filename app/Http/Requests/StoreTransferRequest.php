<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Tylko klient posiadający rachunek może zlecić przelew.
        return (bool) $this->user()?->isClient() && $this->user()->account !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'recipient_number' => ['required', 'string', 'regex:/^\d{26}$/', 'exists:accounts,number'],
            'amount' => ['required', 'numeric', 'gt:0', 'decimal:0,2'],
            'title' => ['nullable', 'string', 'max:140'],
        ];
    }

    /**
     * Dodatkowa walidacja serwerowa: brak przelewu na własne konto i brak
     * przekroczenia salda. Twarda ochrona przed race condition znajduje się
     * dodatkowo w TransferService (transakcja + lockForUpdate).
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $account = $this->user()->account;

            if (! $account) {
                return;
            }

            if ($this->input('recipient_number') === $account->number) {
                $validator->errors()->add('recipient_number', 'Nie można wykonać przelewu na własne konto.');
            }

            if ($this->filled('amount') && bccomp((string) $this->input('amount'), (string) $account->balance, 2) > 0) {
                $validator->errors()->add('amount', 'Kwota przelewu przekracza dostępne saldo.');
            }
        });
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'recipient_number' => 'numer rachunku odbiorcy',
            'amount' => 'kwota',
            'title' => 'tytuł przelewu',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'recipient_number.exists' => 'Rachunek odbiorcy nie istnieje.',
            'recipient_number.regex' => 'Numer rachunku musi składać się z 26 cyfr.',
        ];
    }
}
