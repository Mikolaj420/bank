{{--
    Współdzielona tabela historii operacji.
    Oczekiwane zmienne: $transactions (kolekcja lub paginator), $account (punkt odniesienia
    dla kierunku), $paginated (bool — czy renderować linki paginacji).
--}}
@if ($transactions->isEmpty())
    <div class="empty">Brak operacji do wyświetlenia.</div>
@else
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Referencja</th>
                    <th>Kontrahent</th>
                    <th class="wrap">Tytuł</th>
                    <th class="text-right">Kwota</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transactions as $transaction)
                    @php($outgoing = $transaction->from_account_id === $account->id)
                    @php($counterparty = $outgoing ? $transaction->toAccount : $transaction->fromAccount)
                    <tr>
                        <td>{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                        <td class="mono">{{ $transaction->reference }}</td>
                        <td>
                            {{ optional(optional($counterparty)->user)->full_name ?? '—' }}<br>
                            <span class="muted mono" style="font-size: 0.8rem;">{{ optional($counterparty)->number }}</span>
                        </td>
                        <td class="wrap">{{ $transaction->title ?? '—' }}</td>
                        <td class="text-right {{ $outgoing ? 'amount-out' : 'amount-in' }}">
                            {{ $outgoing ? '−' : '+' }}{{ number_format((float) $transaction->amount, 2, ',', ' ') }}
                        </td>
                        <td>@include('partials.status-badge', ['status' => $transaction->status, 'label' => $transaction->status_label])</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if (($paginated ?? false) && method_exists($transactions, 'links'))
        {{ $transactions->links() }}
    @endif
@endif
