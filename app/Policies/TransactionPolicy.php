<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;

class TransactionPolicy
{
    /** Kierownik przegląda kolejkę operacji do zatwierdzenia. */
    public function viewAny(User $user): bool
    {
        return $user->isManager();
    }

    /**
     * Zatwierdzenie/odrzucenie oczekującego przelewu — tylko kierownik
     * i tylko dopóki operacja ma status "oczekująca".
     */
    public function approve(User $user, Transaction $transaction): bool
    {
        return $user->isManager() && $transaction->isPending();
    }
}
