@extends('layouts.app')

@section('title', 'Pulpit')

@section('content')
    <div class="page-head">
        <div>
            <h1>Witaj, {{ auth()->user()->first_name }}!</h1>
            <p>Twój panel bankowości internetowej.</p>
        </div>
        <a href="{{ route('transfers.create') }}" class="btn">Nowy przelew</a>
    </div>

    @if ($account)
        <div class="grid cols-2">
            <div class="balance-box">
                <div class="label">Dostępne saldo</div>
                <div class="amount">{{ number_format((float) $account->balance, 2, ',', ' ') }} {{ $account->currency }}</div>
                <div class="account-number">{{ $account->formatted_number }}</div>
            </div>

            <div class="card" style="margin-bottom: 0;">
                <h2>Szybkie akcje</h2>
                <div class="actions">
                    <a href="{{ route('transfers.create') }}" class="btn">Wykonaj przelew</a>
                    <a href="{{ route('transfers.index') }}" class="btn btn-secondary">Historia przelewów</a>
                    <a href="{{ route('tickets.create') }}" class="btn btn-secondary">Nowe zgłoszenie</a>
                </div>
            </div>
        </div>

        <div class="card">
            <h2>Ostatnie operacje</h2>
            @include('transfers._table', ['transactions' => $recentTransactions, 'account' => $account, 'paginated' => false])

            @if ($recentTransactions->isNotEmpty())
                <div style="margin-top: 14px;">
                    <a href="{{ route('transfers.index') }}">Zobacz pełną historię &rarr;</a>
                </div>
            @endif
        </div>
    @else
        <div class="card empty">Brak rachunku powiązanego z Twoim kontem. Skontaktuj się z bankiem.</div>
    @endif
@endsection
