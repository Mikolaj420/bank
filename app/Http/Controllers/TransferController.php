<?php

namespace App\Http\Controllers;

use App\Exceptions\TransferException;
use App\Http\Requests\StoreTransferRequest;
use App\Models\Account;
use App\Models\Transaction;
use App\Services\TransferService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TransferController extends Controller
{
    /**
     * Historia przelewów klienta z paginacją, filtrem dat i wyszukiwarką
     * po nadawcy/odbiorcy (imię, nazwisko, numer rachunku).
     */
    public function index(Request $request): View
    {
        $account = $this->clientAccount($request);

        $filters = $this->validatedFilters($request);

        $transactions = $this->historyQuery($account, $filters)
            ->paginate(12)
            ->withQueryString();

        return view('transfers.index', [
            'account' => $account,
            'transactions' => $transactions,
            'filters' => $filters,
        ]);
    }

    public function create(Request $request): View
    {
        $account = $this->clientAccount($request);

        return view('transfers.create', [
            'account' => $account,
            'threshold' => (float) config('bank.approval_threshold', 10000),
        ]);
    }

    public function store(StoreTransferRequest $request, TransferService $service): RedirectResponse
    {
        $account = $this->clientAccount($request);
        $this->authorize('transfer', $account);

        try {
            $transaction = $service->transfer(
                $request->user(),
                $request->validated('recipient_number'),
                (string) $request->validated('amount'),
                $request->validated('title'),
            );
        } catch (TransferException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        $message = $transaction->isPending()
            ? 'Przelew został przyjęty i oczekuje na akceptację kierownika.'
            : 'Przelew został zrealizowany.';

        return redirect()->route('transfers.index')->with('success', $message);
    }

    /**
     * Eksport historii przelewów (z uwzględnieniem filtrów) do pliku CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $account = $this->clientAccount($request);
        $filters = $this->validatedFilters($request);

        $transactions = $this->historyQuery($account, $filters)->get();

        $filename = 'historia-przelewow-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($transactions, $account) {
            $handle = fopen('php://output', 'wb');

            // BOM dla poprawnego odczytu polskich znaków w Excelu.
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['Referencja', 'Data', 'Kierunek', 'Z rachunku', 'Na rachunek', 'Kwota', 'Waluta', 'Status', 'Tytuł'], ';');

            foreach ($transactions as $transaction) {
                $outgoing = $transaction->from_account_id === $account->id;

                fputcsv($handle, [
                    $transaction->reference,
                    $transaction->created_at->format('Y-m-d H:i'),
                    $outgoing ? 'Wychodzący' : 'Przychodzący',
                    optional($transaction->fromAccount)->number,
                    optional($transaction->toAccount)->number,
                    number_format((float) $transaction->amount, 2, ',', ' '),
                    $account->currency,
                    $transaction->status_label,
                    $transaction->title,
                ], ';');
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /* ----------------------------------------------------------------------
     | Pomocnicze
     |----------------------------------------------------------------------*/

    protected function clientAccount(Request $request): Account
    {
        $account = $request->user()->account;

        abort_unless($account, 404, 'Brak rachunku powiązanego z kontem.');

        return $account;
    }

    /**
     * @return array{search: ?string, date_from: ?string, date_to: ?string}
     */
    protected function validatedFilters(Request $request): array
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        return [
            'search' => $validated['search'] ?? null,
            'date_from' => $validated['date_from'] ?? null,
            'date_to' => $validated['date_to'] ?? null,
        ];
    }

    /**
     * @param  array{search: ?string, date_from: ?string, date_to: ?string}  $filters
     */
    protected function historyQuery(Account $account, array $filters): Builder
    {
        return Transaction::forAccount($account->id)
            ->with(['fromAccount.user', 'toAccount.user'])
            ->when($filters['date_from'], fn (Builder $q, $date) => $q->whereDate('created_at', '>=', $date))
            ->when($filters['date_to'], fn (Builder $q, $date) => $q->whereDate('created_at', '<=', $date))
            ->when($filters['search'], function (Builder $query, $search) {
                $query->where(function (Builder $query) use ($search) {
                    $query->whereHas('fromAccount', fn (Builder $q) => $this->matchAccount($q, $search))
                        ->orWhereHas('toAccount', fn (Builder $q) => $this->matchAccount($q, $search));
                });
            })
            ->latest();
    }

    /** Dopasowanie po numerze rachunku lub imieniu/nazwisku właściciela. */
    protected function matchAccount(Builder $query, string $search): void
    {
        $query->where('number', 'like', "%{$search}%")
            ->orWhereHas('user', function (Builder $q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            });
    }
}
