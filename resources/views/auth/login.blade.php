@extends('layouts.guest')

@section('title', 'Logowanie')
@section('subtitle', 'Zaloguj się do bankowości internetowej')

@section('content')
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label for="email">Adres e-mail</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}"
                   class="{{ $errors->has('email') ? 'is-invalid' : '' }}" required autofocus>
            @error('email') <div class="error-text">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="password">Hasło</label>
            <input type="password" id="password" name="password"
                   class="{{ $errors->has('password') ? 'is-invalid' : '' }}" required>
            @error('password') <div class="error-text">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label style="font-weight: 400; display: flex; align-items: center; gap: 8px;">
                <input type="checkbox" name="remember" style="width: auto;"> Zapamiętaj mnie
            </label>
        </div>

        <button type="submit" class="btn" style="width: 100%;">Zaloguj się</button>
    </form>

    <div class="foot">
        Nie masz konta? <a href="{{ route('register') }}">Zarejestruj się</a>
    </div>
@endsection
