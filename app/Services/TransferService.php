<?php

namespace App\Services;

use App\Exceptions\InsufficientFundsException;
use App\Exceptions\TransferException;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransferService
{
    /**
     * Realizuje przelew z rachunku nadawcy na rachunek o podanym numerze.
     *
     * Całość wykonywana jest w transakcji bazodanowej z blokadą wierszy
     * (lockForUpdate), co chroni przed sytuacją wyścigu (race condition)
     * i podwójnym wydaniem środków. Środki są od razu pobierane z konta
     * nadawcy; przy przelewach powyżej progu księgowanie u odbiorcy
     * następuje dopiero po akceptacji kierownika.
     */
    public function transfer(User $sender, string $recipientNumber, string $amount, ?string $title): Transaction
    {
        return DB::transaction(function () use ($sender, $recipientNumber, $amount, $title) {
            $sourceId = $sender->account?->id;

            if (! $sourceId) {
                throw new TransferException('Brak rachunku źródłowego dla nadawcy.');
            }

            $destination = Account::where('number', $recipientNumber)->first();

            if (! $destination) {
                throw new TransferException('Rachunek odbiorcy nie istnieje.');
            }

            if ($destination->id === $sourceId) {
                throw new TransferException('Nie można wykonać przelewu na własne konto.');
            }

            // Blokujemy oba rachunki w kolejności rosnących identyfikatorów,
            // aby uniknąć zakleszczeń (deadlock) przy przelewach wzajemnych.
            $locked = Account::whereIn('id', [$sourceId, $destination->id])
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            /** @var Account $source */
            $source = $locked[$sourceId];
            /** @var Account $destination */
            $destination = $locked[$destination->id];

            if (bccomp((string) $source->balance, $amount, 2) < 0) {
                throw new InsufficientFundsException('Kwota przelewu przekracza dostępne saldo.');
            }

            $requiresApproval = $this->requiresApproval($amount);

            // Środki opuszczają konto nadawcy natychmiast (blokada/hold).
            $source->decrement('balance', $amount);

            if (! $requiresApproval) {
                $destination->increment('balance', $amount);
            }

            return Transaction::create([
                'reference' => $this->generateReference(),
                'from_account_id' => $source->id,
                'to_account_id' => $destination->id,
                'amount' => $amount,
                'title' => $title,
                'status' => $requiresApproval
                    ? Transaction::STATUS_PENDING
                    : Transaction::STATUS_COMPLETED,
            ]);
        });
    }

    /**
     * Akceptacja oczekującego przelewu przez kierownika — księguje środki u odbiorcy.
     */
    public function approve(Transaction $transaction, User $manager): Transaction
    {
        return DB::transaction(function () use ($transaction, $manager) {
            $transaction = Transaction::whereKey($transaction->id)->lockForUpdate()->firstOrFail();

            if (! $transaction->isPending()) {
                throw new TransferException('Ta operacja została już rozpatrzona.');
            }

            $destination = Account::whereKey($transaction->to_account_id)->lockForUpdate()->firstOrFail();
            $destination->increment('balance', $transaction->amount);

            $transaction->update([
                'status' => Transaction::STATUS_COMPLETED,
                'approved_by' => $manager->id,
                'approved_at' => now(),
            ]);

            return $transaction;
        });
    }

    /**
     * Odrzucenie oczekującego przelewu — zwraca zablokowane środki nadawcy.
     */
    public function reject(Transaction $transaction, User $manager): Transaction
    {
        return DB::transaction(function () use ($transaction, $manager) {
            $transaction = Transaction::whereKey($transaction->id)->lockForUpdate()->firstOrFail();

            if (! $transaction->isPending()) {
                throw new TransferException('Ta operacja została już rozpatrzona.');
            }

            $source = Account::whereKey($transaction->from_account_id)->lockForUpdate()->firstOrFail();
            $source->increment('balance', $transaction->amount);

            $transaction->update([
                'status' => Transaction::STATUS_REJECTED,
                'approved_by' => $manager->id,
                'approved_at' => now(),
            ]);

            return $transaction;
        });
    }

    /** Czy kwota wymaga akceptacji kierownika. */
    public function requiresApproval(string $amount): bool
    {
        $threshold = (string) config('bank.approval_threshold', 10000);

        return bccomp($amount, $threshold, 2) >= 0;
    }

    /** Generuje unikalny numer referencyjny transakcji. */
    protected function generateReference(): string
    {
        do {
            $reference = 'TRX-'.strtoupper(Str::random(10));
        } while (Transaction::where('reference', $reference)->exists());

        return $reference;
    }
}
