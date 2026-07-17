@extends('layouts.app')

@section('title', 'Rachunek klienta')

@section('content')
    <div class="page-head">
        <div>
            <h1>{{ $account->user->full_name }}</h1>
            <p>Szczegóły rachunku klienta.</p>
        </div>
        <a href="{{ route('employee.accounts') }}" class="btn btn-secondary">Wróć do listy</a>
    </div>

    <div class="grid cols-2">
        <div class="balance-box">
            <div class="label">Saldo rachunku</div>
            <div class="amount">{{ number_format((float) $account->balance, 2, ',', ' ') }} {{ $account->currency }}</div>
            <div class="account-number">{{ $account->formatted_number }}</div>
        </div>

        <div class="card" style="margin-bottom: 0;">
            <h2>Dane klienta</h2>
            <dl class="dl">
                <dt>Imię i nazwisko</dt>
                <dd>{{ $account->user->full_name }}</dd>
                <dt>E-mail</dt>
                <dd>{{ $account->user->email }}</dd>
                <dt>Numer rachunku</dt>
                <dd class="mono">{{ $account->number }}</dd>
                <dt>Data otwarcia</dt>
                <dd>{{ $account->created_at->format('Y-m-d') }}</dd>
            </dl>
        </div>
    </div>

    <div class="card">
        <h2>Historia transakcji</h2>
        @include('transfers._table', ['transactions' => $transactions, 'account' => $account, 'paginated' => true])
    </div>
@endsection
