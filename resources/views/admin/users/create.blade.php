@extends('layouts.app')

@section('title', 'Nowy użytkownik')

@section('content')
    <div class="page-head">
        <div>
            <h1>Nowy użytkownik</h1>
            <p>Dla roli „Klient” rachunek zostanie utworzony automatycznie.</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Wróć</a>
    </div>

    <div class="card" style="max-width: 680px;">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            @include('admin.users._fields', ['roles' => $roles, 'passwordRequired' => true])
            <button type="submit" class="btn">Utwórz użytkownika</button>
        </form>
    </div>
@endsection
