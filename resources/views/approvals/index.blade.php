@extends('layouts.app')

@section('title', 'Akceptacja przelewów')

@section('content')
    <div class="page-head">
        <div>
            <h1>Akceptacja przelewów</h1>
            <p>Przelewy powyżej progu wymagają Twojej decyzji.</p>
        </div>
    </div>

    <div class="card">
        @if ($transactions->isEmpty())
            <div class="empty">Brak przelewów oczekujących na akceptację.</div>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Referencja</th>
                            <th>Nadawca</th>
                            <th>Odbiorca</th>
                            <th class="wrap">Tytuł</th>
                            <th class="text-right">Kwota</th>
                            <th>Decyzja</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactions as $transaction)
                            <tr>
                                <td>{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                                <td class="mono">{{ $transaction->reference }}</td>
                                <td>
                                    {{ optional($transaction->fromAccount->user)->full_name }}<br>
                                    <span class="muted mono" style="font-size: 0.8rem;">{{ $transaction->fromAccount->number }}</span>
                                </td>
                                <td>
                                    {{ optional($transaction->toAccount->user)->full_name }}<br>
                                    <span class="muted mono" style="font-size: 0.8rem;">{{ $transaction->toAccount->number }}</span>
                                </td>
                                <td class="wrap">{{ $transaction->title ?? '—' }}</td>
                                <td class="text-right"><strong>{{ number_format((float) $transaction->amount, 2, ',', ' ') }} zł</strong></td>
                                <td>
                                    <div class="actions">
                                        <form method="POST" action="{{ route('approvals.approve', $transaction) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">Zatwierdź</button>
                                        </form>
                                        <form method="POST" action="{{ route('approvals.reject', $transaction) }}"
                                              onsubmit="return confirm('Odrzucić przelew i zwrócić środki nadawcy?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger">Odrzuć</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $transactions->links() }}
        @endif
    </div>
@endsection
