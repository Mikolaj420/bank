<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountController extends Controller
{
    /**
     * Podgląd własnego rachunku klienta: numer, saldo i ostatnie operacje.
     */
    public function show(Request $request): View
    {
        $account = $request->user()->account;

        abort_unless($account, 404, 'Brak rachunku powiązanego z kontem.');

        $this->authorize('view', $account);

        $recentTransactions = Transaction::forAccount($account->id)
            ->with(['fromAccount.user', 'toAccount.user'])
            ->latest()
            ->limit(10)
            ->get();

        return view('account.show', compact('account', 'recentTransactions'));
    }
}
