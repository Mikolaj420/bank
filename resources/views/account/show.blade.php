@extends('layouts.app')

@section('title', 'Moje konto')

@section('content')
    <div class="page-head">
        <div>
            <h1>Moje konto</h1>
            <p>Szczegóły rachunku i ostatnie operacje.</p>
        </div>
        <a href="{{ route('transfers.create') }}" class="btn">Nowy przelew</a>
    </div>

    <div class="grid cols-2">
        <div class="balance-box">
            <div class="label">Dostępne saldo</div>
            <div class="amount">{{ number_format((float) $account->balance, 2, ',', ' ') }} {{ $account->currency }}</div>
            <div class="account-number">{{ $account->formatted_number }}</div>
        </div>

        <div class="card" style="margin-bottom: 0;">
            <h2>Dane rachunku</h2>
            <dl class="dl">
                <dt>Właściciel</dt>
                <dd>{{ auth()->user()->full_name }}</dd>
                <dt>Numer rachunku</dt>
                <dd class="mono">{{ $account->number }}</dd>
                <dt>Waluta</dt>
                <dd>{{ $account->currency }}</dd>
                <dt>Data otwarcia</dt>
                <dd>{{ $account->created_at->format('Y-m-d') }}</dd>
            </dl>
        </div>
    </div>

    <div class="card">
        <h2>Ostatnie operacje</h2>
        @include('transfers._table', ['transactions' => $recentTransactions, 'account' => $account, 'paginated' => false])
        @if ($recentTransactions->isNotEmpty())
            <div style="margin-top: 14px;">
                <a href="{{ route('transfers.index') }}">Pełna historia przelewów &rarr;</a>
            </div>
        @endif
    </div>
@endsection
