@extends('layouts.app')

@section('title', 'Historia przelewów')

@section('content')
    <div class="page-head">
        <div>
            <h1>Historia przelewów</h1>
            <p>Wszystkie operacje na Twoim rachunku.</p>
        </div>
        <a href="{{ route('transfers.create') }}" class="btn">Nowy przelew</a>
    </div>

    <div class="card">
        <div class="toolbar">
            <form method="GET" action="{{ route('transfers.index') }}">
                <div class="form-group">
                    <label for="search">Szukaj (nadawca / odbiorca / nr rachunku)</label>
                    <input type="text" id="search" name="search" value="{{ $filters['search'] }}" placeholder="np. Kowalski">
                </div>
                <div class="form-group">
                    <label for="date_from">Data od</label>
                    <input type="date" id="date_from" name="date_from" value="{{ $filters['date_from'] }}">
                </div>
                <div class="form-group">
                    <label for="date_to">Data do</label>
                    <input type="date" id="date_to" name="date_to" value="{{ $filters['date_to'] }}">
                </div>
                <div class="form-group actions">
                    <button type="submit" class="btn btn-sm">Filtruj</button>
                    <a href="{{ route('transfers.index') }}" class="btn btn-sm btn-secondary">Wyczyść</a>
                </div>
            </form>

            <div style="margin-left: auto;">
                <a href="{{ route('transfers.export', request()->query()) }}" class="btn btn-sm btn-secondary">Eksport CSV</a>
            </div>
        </div>

        @include('transfers._table', ['transactions' => $transactions, 'account' => $account, 'paginated' => true])
    </div>
@endsection
