@extends('layouts.app')

@section('title', 'Edycja użytkownika')

@section('content')
    <div class="page-head">
        <div>
            <h1>Edycja użytkownika</h1>
            <p>{{ $user->full_name }} &middot; {{ $user->email }}</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Wróć</a>
    </div>

    <div class="card" style="max-width: 680px;">
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PUT')
            @include('admin.users._fields', ['roles' => $roles, 'user' => $user, 'passwordRequired' => false])
            <button type="submit" class="btn">Zapisz zmiany</button>
        </form>
    </div>
@endsection
