<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportTicket extends Model
{
    use HasFactory;

    public const STATUS_OPEN = 'open';
    public const STATUS_CLOSED = 'closed';

    protected $fillable = [
        'user_id',
        'subject',
        'message',
        'status',
        'response',
        'handled_by',
        'handled_at',
    ];

    protected $casts = [
        'handled_at' => 'datetime',
    ];

    /* ----------------------------------------------------------------------
     | Relacje
     |----------------------------------------------------------------------*/

    /** Klient, który złożył zgłoszenie. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Pracownik, który obsłużył zgłoszenie. */
    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    /* ----------------------------------------------------------------------
     | Scope'y i pomocnicze
     |----------------------------------------------------------------------*/

    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    public function isOpen(): bool
    {
        return $this->status === self::STATUS_OPEN;
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->isOpen() ? 'Otwarte' : 'Zamknięte';
    }
}
