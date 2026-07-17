<?php

namespace App\Providers;

use App\Models\Account;
use App\Models\SupportTicket;
use App\Models\Transaction;
use App\Models\User;
use App\Policies\AccountPolicy;
use App\Policies\SupportTicketPolicy;
use App\Policies\TransactionPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Account::class => AccountPolicy::class,
        Transaction::class => TransactionPolicy::class,
        SupportTicket::class => SupportTicketPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Administrator jest super-użytkownikiem — przechodzi każdą bramkę autoryzacji.
        Gate::before(fn (User $user) => $user->isAdmin() ? true : null);
    }
}
