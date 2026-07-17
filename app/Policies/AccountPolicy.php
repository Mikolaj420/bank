<?php

namespace App\Policies;

use App\Models\Account;
use App\Models\User;

class AccountPolicy
{
    /** Personel (pracownik/kierownik) może przeglądać listę rachunków. */
    public function viewAny(User $user): bool
    {
        return $user->isEmployee() || $user->isManager();
    }

    /**
     * Wgląd w rachunek ma jego właściciel oraz personel banku.
     * Klient nigdy nie zobaczy cudzego konta.
     */
    public function view(User $user, Account $account): bool
    {
        return $user->id === $account->user_id
            || $user->isEmployee()
            || $user->isManager();
    }

    /** Przelew może zlecić wyłącznie właściciel rachunku będący klientem. */
    public function transfer(User $user, Account $account): bool
    {
        return $user->isClient() && $user->id === $account->user_id;
    }
}
