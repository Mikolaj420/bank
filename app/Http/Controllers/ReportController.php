<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Raporty zbiorcze dla kierownika: wolumeny, statusy, najaktywniejsze rachunki.
     */
    public function index(): View
    {
        $this->authorize('viewAny', Transaction::class);

        $summary = [
            'completed_count' => Transaction::completed()->count(),
            'completed_volume' => (float) Transaction::completed()->sum('amount'),
            'pending_count' => Transaction::pending()->count(),
            'pending_volume' => (float) Transaction::pending()->sum('amount'),
            'rejected_count' => Transaction::rejected()->count(),
            'today_volume' => (float) Transaction::completed()->whereDate('created_at', today())->sum('amount'),
        ];

        // Grupowana agregacja wolumenu wychodzącego — najaktywniejsze rachunki.
        $topAccountRows = Transaction::completed()
            ->selectRaw('from_account_id, COUNT(*) as transfers_count, SUM(amount) as total')
            ->groupBy('from_account_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $accounts = Account::with('user')->whereIn('id', $topAccountRows->pluck('from_account_id'))->get()->keyBy('id');

        $topAccounts = $topAccountRows->map(function ($row) use ($accounts) {
            return [
                'account' => $accounts->get($row->from_account_id),
                'transfers_count' => (int) $row->transfers_count,
                'total' => (float) $row->total,
            ];
        });

        return view('reports.index', compact('summary', 'topAccounts'));
    }
}
