@extends('layouts.guest')

@section('title', 'Rejestracja')
@section('subtitle', 'Załóż konto klienta banku')

@section('content')
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="form-row">
            <div class="form-group">
                <label for="first_name">Imię</label>
                <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}"
                       class="{{ $errors->has('first_name') ? 'is-invalid' : '' }}" required autofocus>
                @error('first_name') <div class="error-text">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="last_name">Nazwisko</label>
                <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}"
                       class="{{ $errors->has('last_name') ? 'is-invalid' : '' }}" required>
                @error('last_name') <div class="error-text">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-group">
            <label for="email">Adres e-mail</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}"
                   class="{{ $errors->has('email') ? 'is-invalid' : '' }}" required>
            @error('email') <div class="error-text">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="pesel">PESEL</label>
            <input type="text" id="pesel" name="pesel" value="{{ old('pesel') }}" inputmode="numeric" maxlength="11"
                   class="{{ $errors->has('pesel') ? 'is-invalid' : '' }}" required>
            <div class="hint">11 cyfr — używany do weryfikacji tożsamości.</div>
            @error('pesel') <div class="error-text">{{ $message }}</div> @enderror
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="password">Hasło</label>
                <input type="password" id="password" name="password"
                       class="{{ $errors->has('password') ? 'is-invalid' : '' }}" required>
                <div class="hint">Minimum 8 znaków.</div>
                @error('password') <div class="error-text">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">Powtórz hasło</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required>
            </div>
        </div>

        <button type="submit" class="btn" style="width: 100%;">Załóż konto</button>
    </form>

    <div class="foot">
        Masz już konto? <a href="{{ route('login') }}">Zaloguj się</a>
    </div>
@endsection
