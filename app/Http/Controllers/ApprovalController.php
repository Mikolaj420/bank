<?php

namespace App\Http\Controllers;

use App\Exceptions\TransferException;
use App\Models\Transaction;
use App\Services\TransferService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ApprovalController extends Controller
{
    /**
     * Kolejka przelewów oczekujących na decyzję kierownika.
     */
    public function index(): View
    {
        $this->authorize('viewAny', Transaction::class);

        $transactions = Transaction::pending()
            ->with(['fromAccount.user', 'toAccount.user'])
            ->oldest()
            ->paginate(12);

        return view('approvals.index', compact('transactions'));
    }

    public function approve(Transaction $transaction, TransferService $service): RedirectResponse
    {
        $this->authorize('approve', $transaction);

        try {
            $service->approve($transaction, auth()->user());
        } catch (TransferException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', "Przelew {$transaction->reference} został zatwierdzony.");
    }

    public function reject(Transaction $transaction, TransferService $service): RedirectResponse
    {
        $this->authorize('approve', $transaction);

        try {
            $service->reject($transaction, auth()->user());
        } catch (TransferException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', "Przelew {$transaction->reference} został odrzucony, środki zwrócono nadawcy.");
    }
}
