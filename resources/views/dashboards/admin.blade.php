@extends('layouts.app')

@section('title', 'Pulpit administratora')

@section('content')
    <div class="page-head">
        <div>
            <h1>Pulpit administratora</h1>
            <p>Zarządzanie użytkownikami i rolami.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn">Dodaj użytkownika</a>
    </div>

    <div class="grid cols-3">
        <div class="stat">
            <div class="label">Użytkownicy</div>
            <div class="value">{{ $usersCount }}</div>
        </div>
        <div class="stat">
            <div class="label">Rachunki</div>
            <div class="value">{{ $accountsCount }}</div>
        </div>
        <div class="stat">
            <div class="label">Transakcje</div>
            <div class="value">{{ $transactionsCount }}</div>
        </div>
    </div>

    <div class="card">
        <h2>Użytkownicy wg ról</h2>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Rola</th>
                        <th class="text-right">Liczba użytkowników</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($roleCounts as $role)
                        <tr>
                            <td>{{ $role->label }}</td>
                            <td class="text-right">{{ $role->users_count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="margin-top: 14px;">
            <a href="{{ route('admin.users.index') }}">Zarządzaj użytkownikami &rarr;</a>
        </div>
    </div>
@endsection
