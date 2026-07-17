<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    /** Statusy przelewu. */
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_PENDING = 'pending';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'reference',
        'from_account_id',
        'to_account_id',
        'amount',
        'title',
        'status',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    /* ----------------------------------------------------------------------
     | Relacje
     |----------------------------------------------------------------------*/

    public function fromAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'from_account_id');
    }

    public function toAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'to_account_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /* ----------------------------------------------------------------------
     | Scope'y zapytań
     |----------------------------------------------------------------------*/

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    /** Ogranicza wynik do transakcji powiązanych z danym rachunkiem. */
    public function scopeForAccount(Builder $query, int $accountId): Builder
    {
        return $query->where(function (Builder $q) use ($accountId) {
            $q->where('from_account_id', $accountId)
                ->orWhere('to_account_id', $accountId);
        });
    }

    /* ----------------------------------------------------------------------
     | Pomocnicze
     |----------------------------------------------------------------------*/

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /** Etykieta statusu w języku polskim. */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_COMPLETED => 'Zrealizowany',
            self::STATUS_PENDING => 'Oczekuje akceptacji',
            self::STATUS_REJECTED => 'Odrzucony',
            default => ucfirst((string) $this->status),
        };
    }
}
