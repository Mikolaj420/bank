<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    /** Nazwy (slug) ról używane w całej aplikacji. */
    public const CLIENT = 'klient';
    public const EMPLOYEE = 'pracownik';
    public const MANAGER = 'kierownik';
    public const ADMIN = 'administrator';

    protected $fillable = [
        'name',
        'label',
    ];

    /**
     * Użytkownicy przypisani do roli.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Pełna lista ról wraz z etykietami — wykorzystywana m.in. w seederze i formularzach.
     *
     * @return array<string, string>
     */
    public static function all_labels(): array
    {
        return [
            self::CLIENT => 'Klient',
            self::EMPLOYEE => 'Pracownik banku',
            self::MANAGER => 'Kierownik',
            self::ADMIN => 'Administrator',
        ];
    }
}
