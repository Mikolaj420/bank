<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /*
     | Administrator przechodzi te bramki dzięki Gate::before w AuthServiceProvider.
     | Metody zwracają false dla pozostałych ról, dając jawną, drugą warstwę ochrony
     | oprócz middleware 'role:administrator' na trasach panelu.
     */

    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, User $model): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, User $model): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, User $model): bool
    {
        // Administrator nie może usunąć własnego konta.
        return $user->isAdmin() && $user->id !== $model->id;
    }
}
