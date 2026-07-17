@extends('layouts.app')

@section('title', 'Użytkownicy')

@section('content')
    <div class="page-head">
        <div>
            <h1>Użytkownicy</h1>
            <p>Zarządzanie kontami i rolami.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn">Dodaj użytkownika</a>
    </div>

    <div class="card">
        <div class="toolbar">
            <form method="GET" action="{{ route('admin.users.index') }}">
                <div class="form-group">
                    <label for="search">Szukaj (imię, nazwisko, e-mail, numer rachunku)</label>
                    <input type="text" id="search" name="search" value="{{ $search }}" placeholder="np. Kowalski">
                </div>
                <div class="form-group actions">
                    <button type="submit" class="btn btn-sm">Szukaj</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-secondary">Wyczyść</a>
                </div>
            </form>
        </div>

        @if ($users->isEmpty())
            <div class="empty">Nie znaleziono użytkowników.</div>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Imię i nazwisko</th>
                            <th>E-mail</th>
                            <th>Rola</th>
                            <th>Numer rachunku</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->full_name }}</td>
                                <td>{{ $user->email }}</td>
                                <td><span class="badge badge-role">{{ $user->role->label }}</span></td>
                                <td class="mono">{{ optional($user->account)->number ?? '—' }}</td>
                                <td>
                                    <div class="actions">
                                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-secondary">Podgląd</a>
                                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-secondary">Edytuj</a>
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                              onsubmit="return confirm('Na pewno usunąć użytkownika {{ $user->full_name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Usuń</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $users->links() }}
        @endif
    </div>
@endsection
