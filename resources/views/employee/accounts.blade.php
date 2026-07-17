@extends('layouts.app')

@section('title', 'Rachunki klientów')

@section('content')
    <div class="page-head">
        <div>
            <h1>Rachunki klientów</h1>
            <p>Przeglądaj rachunki i historię operacji klientów.</p>
        </div>
    </div>

    <div class="card">
        <div class="toolbar">
            <form method="GET" action="{{ route('employee.accounts') }}">
                <div class="form-group">
                    <label for="search">Szukaj (imię, nazwisko, e-mail, numer rachunku)</label>
                    <input type="text" id="search" name="search" value="{{ $search }}" placeholder="np. Nowak">
                </div>
                <div class="form-group actions">
                    <button type="submit" class="btn btn-sm">Szukaj</button>
                    <a href="{{ route('employee.accounts') }}" class="btn btn-sm btn-secondary">Wyczyść</a>
                </div>
            </form>
        </div>

        @if ($accounts->isEmpty())
            <div class="empty">Nie znaleziono rachunków.</div>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Klient</th>
                            <th>E-mail</th>
                            <th>Numer rachunku</th>
                            <th class="text-right">Saldo</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($accounts as $account)
                            <tr>
                                <td>{{ $account->user->full_name }}</td>
                                <td>{{ $account->user->email }}</td>
                                <td class="mono">{{ $account->number }}</td>
                                <td class="text-right">{{ number_format((float) $account->balance, 2, ',', ' ') }} {{ $account->currency }}</td>
                                <td><a href="{{ route('employee.accounts.show', $account) }}">Szczegóły</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $accounts->links() }}
        @endif
    </div>
@endsection
