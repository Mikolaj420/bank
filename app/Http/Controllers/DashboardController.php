<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Role;
use App\Models\SupportTicket;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Wspólny punkt wejścia po zalogowaniu — rozgałęzia widok zależnie od roli.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        return match (true) {
            $user->isClient() => $this->clientDashboard($user),
            $user->isEmployee() => $this->employeeDashboard(),
            $user->isManager() => $this->managerDashboard(),
            $user->isAdmin() => $this->adminDashboard(),
            default => abort(403),
        };
    }

    protected function clientDashboard(User $user): View
    {
        $account = $user->account;

        $recentTransactions = $account
            ? Transaction::forAccount($account->id)->with(['fromAccount.user', 'toAccount.user'])->latest()->limit(5)->get()
            : collect();

        return view('dashboards.client', [
            'account' => $account,
            'recentTransactions' => $recentTransactions,
        ]);
    }

    protected function employeeDashboard(): View
    {
        return view('dashboards.employee', [
            'clientsCount' => User::whereHas('role', fn ($q) => $q->where('name', Role::CLIENT))->count(),
            'accountsCount' => Account::count(),
            'openTicketsCount' => SupportTicket::open()->count(),
            'recentTickets' => SupportTicket::with('user')->latest()->limit(5)->get(),
        ]);
    }

    protected function managerDashboard(): View
    {
        return view('dashboards.manager', [
            'pendingCount' => Transaction::pending()->count(),
            'pendingVolume' => (float) Transaction::pending()->sum('amount'),
            'completedCount' => Transaction::completed()->count(),
            'completedVolume' => (float) Transaction::completed()->sum('amount'),
            'pendingTransactions' => Transaction::pending()->with(['fromAccount.user', 'toAccount.user'])->latest()->limit(5)->get(),
        ]);
    }

    protected function adminDashboard(): View
    {
        $roleCounts = Role::withCount('users')->get();

        return view('dashboards.admin', [
            'roleCounts' => $roleCounts,
            'usersCount' => User::count(),
            'accountsCount' => Account::count(),
            'transactionsCount' => Transaction::count(),
        ]);
    }
}
