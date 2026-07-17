<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'role_id',
        'first_name',
        'last_name',
        'email',
        'pesel',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    /* ----------------------------------------------------------------------
     | Relacje
     |----------------------------------------------------------------------*/

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function account(): HasOne
    {
        return $this->hasOne(Account::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class);
    }

    /* ----------------------------------------------------------------------
     | Akcesory
     |----------------------------------------------------------------------*/

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    /* ----------------------------------------------------------------------
     | Pomocnicze metody ról
     |----------------------------------------------------------------------*/

    public function hasRole(string $role): bool
    {
        return $this->role?->name === $role;
    }

    /**
     * @param  array<int, string>  $roles
     */
    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role?->name, $roles, true);
    }

    public function isClient(): bool
    {
        return $this->hasRole(Role::CLIENT);
    }

    public function isEmployee(): bool
    {
        return $this->hasRole(Role::EMPLOYEE);
    }

    public function isManager(): bool
    {
        return $this->hasRole(Role::MANAGER);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(Role::ADMIN);
    }

    /** Pracownik, kierownik lub administrator — personel banku. */
    public function isStaff(): bool
    {
        return $this->isEmployee() || $this->isManager() || $this->isAdmin();
    }
}
