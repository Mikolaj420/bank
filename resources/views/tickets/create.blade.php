@extends('layouts.app')

@section('title', 'Nowe zgłoszenie')

@section('content')
    <div class="page-head">
        <div>
            <h1>Nowe zgłoszenie</h1>
            <p>Opisz sprawę — pracownik banku odpowie na Twoje zgłoszenie.</p>
        </div>
        <a href="{{ route('tickets.index') }}" class="btn btn-secondary">Wróć</a>
    </div>

    <div class="card" style="max-width: 640px;">
        <form method="POST" action="{{ route('tickets.store') }}">
            @csrf

            <div class="form-group">
                <label for="subject">Temat</label>
                <input type="text" id="subject" name="subject" value="{{ old('subject') }}" maxlength="150"
                       class="{{ $errors->has('subject') ? 'is-invalid' : '' }}" required autofocus>
                @error('subject') <div class="error-text">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="message">Treść zgłoszenia</label>
                <textarea id="message" name="message" rows="6" maxlength="2000"
                          class="{{ $errors->has('message') ? 'is-invalid' : '' }}" required>{{ old('message') }}</textarea>
                @error('message') <div class="error-text">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="btn">Wyślij zgłoszenie</button>
        </form>
    </div>
@endsection
