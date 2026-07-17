@extends('layouts.app')

@section('title', 'Pulpit kierownika')

@section('content')
    <div class="page-head">
        <div>
            <h1>Pulpit kierownika</h1>
            <p>Akceptacja operacji i raporty zbiorcze.</p>
        </div>
        <a href="{{ route('approvals.index') }}" class="btn">Kolejka akceptacji</a>
    </div>

    <div class="grid cols-4">
        <div class="stat">
            <div class="label">Oczekujące</div>
            <div class="value">{{ $pendingCount }}</div>
        </div>
        <div class="stat">
            <div class="label">Kwota oczekująca</div>
            <div class="value" style="font-size: 1.25rem;">{{ number_format($pendingVolume, 2, ',', ' ') }} zł</div>
        </div>
        <div class="stat">
            <div class="label">Zrealizowane</div>
            <div class="value">{{ $completedCount }}</div>
        </div>
        <div class="stat">
            <div class="label">Wolumen zrealizowany</div>
            <div class="value" style="font-size: 1.25rem;">{{ number_format($completedVolume, 2, ',', ' ') }} zł</div>
        </div>
    </div>

    <div class="card">
        <h2>Przelewy oczekujące na akceptację</h2>
        @if ($pendingTransactions->isEmpty())
            <div class="empty">Brak przelewów do zatwierdzenia.</div>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Referencja</th>
                            <th>Nadawca</th>
                            <th>Odbiorca</th>
                            <th class="text-right">Kwota</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pendingTransactions as $transaction)
                            <tr>
                                <td class="mono">{{ $transaction->reference }}</td>
                                <td>{{ optional($transaction->fromAccount->user)->full_name }}</td>
                                <td>{{ optional($transaction->toAccount->user)->full_name }}</td>
                                <td class="text-right">{{ number_format((float) $transaction->amount, 2, ',', ' ') }} zł</td>
                                <td><a href="{{ route('approvals.index') }}">Rozpatrz</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
        <div style="margin-top: 14px;">
            <a href="{{ route('reports.index') }}">Zobacz raporty zbiorcze &rarr;</a>
        </div>
    </div>
@endsection
