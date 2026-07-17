<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Role;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    /**
     * Lista rachunków klientów z wyszukiwarką (imię, nazwisko, e-mail, numer rachunku).
     */
    public function accounts(Request $request): View
    {
        $this->authorize('viewAny', Account::class);

        $search = trim((string) $request->query('search', ''));

        $accounts = Account::query()
            ->with('user.role')
            ->whereHas('user.role', fn (Builder $q) => $q->where('name', Role::CLIENT))
            ->when($search !== '', function (Builder $query) use ($search) {
                $query->where(function (Builder $query) use ($search) {
                    $query->where('number', 'like', "%{$search}%")
                        ->orWhereHas('user', function (Builder $q) use ($search) {
                            $q->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('employee.accounts', compact('accounts', 'search'));
    }

    /**
     * Szczegóły rachunku klienta wraz z historią transakcji.
     */
    public function showAccount(Account $account): View
    {
        $this->authorize('view', $account);

        $account->load('user');

        $transactions = Transaction::forAccount($account->id)
            ->with(['fromAccount.user', 'toAccount.user'])
            ->latest()
            ->paginate(12);

        return view('employee.account', compact('account', 'transactions'));
    }
}
