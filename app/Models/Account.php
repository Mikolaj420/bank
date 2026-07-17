<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'number',
        'balance',
        'currency',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
    ];

    /* ----------------------------------------------------------------------
     | Relacje
     |----------------------------------------------------------------------*/

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function outgoingTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'from_account_id');
    }

    public function incomingTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'to_account_id');
    }

    /* ----------------------------------------------------------------------
     | Formatowanie / generowanie
     |----------------------------------------------------------------------*/

    /** Numer rachunku sformatowany w grupy po 4 cyfry (tylko do prezentacji). */
    public function getFormattedNumberAttribute(): string
    {
        return trim(chunk_split($this->number, 4, ' '));
    }

    /**
     * Generuje unikalny numer rachunku w formacie NRB (domyślnie 26 cyfr).
     */
    public static function generateUniqueNumber(): string
    {
        $length = (int) config('bank.account_number_length', 26);

        do {
            // Pierwsza cyfra różna od zera, pozostałe losowe — budowane cyfra po cyfrze,
            // aby uniknąć przekroczenia zakresu liczb całkowitych PHP dla 26 cyfr.
            $number = (string) random_int(1, 9);
            for ($i = 1; $i < $length; $i++) {
                $number .= (string) random_int(0, 9);
            }
        } while (static::where('number', $number)->exists());

        return $number;
    }
}
