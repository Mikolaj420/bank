@extends('layouts.app')

@section('title', 'Podgląd użytkownika')

@section('content')
    <div class="page-head">
        <div>
            <h1>{{ $user->full_name }}</h1>
            <p>Podgląd konta użytkownika.</p>
        </div>
        <div class="actions">
            <a href="{{ route('admin.users.edit', $user) }}" class="btn">Edytuj</a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Wróć</a>
        </div>
    </div>

    <div class="grid cols-2">
        <div class="card" style="margin-bottom: 0;">
            <h2>Dane konta</h2>
            <dl class="dl">
                <dt>Imię i nazwisko</dt>
                <dd>{{ $user->full_name }}</dd>
                <dt>E-mail</dt>
                <dd>{{ $user->email }}</dd>
                <dt>PESEL</dt>
                <dd class="mono">{{ $user->pesel ?? '—' }}</dd>
                <dt>Rola</dt>
                <dd><span class="badge badge-role">{{ $user->role->label }}</span></dd>
                <dt>Data rejestracji</dt>
                <dd>{{ $user->created_at->format('Y-m-d') }}</dd>
            </dl>
        </div>

        <div class="card" style="margin-bottom: 0;">
            <h2>Rachunek</h2>
            @if ($user->account)
                <dl class="dl">
                    <dt>Numer rachunku</dt>
                    <dd class="mono">{{ $user->account->number }}</dd>
                    <dt>Saldo</dt>
                    <dd>{{ number_format((float) $user->account->balance, 2, ',', ' ') }} {{ $user->account->currency }}</dd>
                </dl>
            @else
                <p class="muted mb-0">Ten użytkownik nie posiada rachunku.</p>
            @endif
        </div>
    </div>

    @if ($user->account)
        <div class="card">
            <h2>Ostatnie operacje</h2>
            @include('transfers._table', ['transactions' => $transactions, 'account' => $user->account, 'paginated' => false])
        </div>
    @endif
@endsection
