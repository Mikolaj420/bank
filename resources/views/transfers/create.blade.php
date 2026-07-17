@extends('layouts.app')

@section('title', 'Nowy przelew')

@section('content')
    <div class="page-head">
        <div>
            <h1>Nowy przelew</h1>
            <p>Przelew na rachunek innego klienta banku.</p>
        </div>
        <a href="{{ route('transfers.index') }}" class="btn btn-secondary">Wróć do historii</a>
    </div>

    <div class="grid cols-2">
        <div class="card">
            <form method="POST" action="{{ route('transfers.store') }}">
                @csrf

                <div class="form-group">
                    <label for="recipient_number">Numer rachunku odbiorcy</label>
                    <input type="text" id="recipient_number" name="recipient_number" value="{{ old('recipient_number') }}"
                           inputmode="numeric" maxlength="26" placeholder="26 cyfr"
                           class="mono {{ $errors->has('recipient_number') ? 'is-invalid' : '' }}" required>
                    @error('recipient_number') <div class="error-text">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="amount">Kwota ({{ $account->currency }})</label>
                    <input type="number" id="amount" name="amount" value="{{ old('amount') }}"
                           step="0.01" min="0.01" placeholder="0,00"
                           class="{{ $errors->has('amount') ? 'is-invalid' : '' }}" required>
                    <div class="hint">Dostępne saldo: {{ number_format((float) $account->balance, 2, ',', ' ') }} {{ $account->currency }}.</div>
                    @error('amount') <div class="error-text">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="title">Tytuł przelewu (opcjonalny)</label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" maxlength="140"
                           class="{{ $errors->has('title') ? 'is-invalid' : '' }}">
                    @error('title') <div class="error-text">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn">Wykonaj przelew</button>
            </form>
        </div>

        <div class="card" style="margin-bottom: 0;">
            <h2>Informacje</h2>
            <p class="muted">Środki są pobierane z Twojego rachunku natychmiast po zleceniu przelewu.</p>
            <p class="muted">
                Przelewy o kwocie od <strong>{{ number_format($threshold, 2, ',', ' ') }} {{ $account->currency }}</strong>
                wymagają akceptacji kierownika — do czasu zatwierdzenia środki pozostają zablokowane,
                a operacja ma status „oczekuje akceptacji”.
            </p>
            <p class="muted mb-0">Nie można wykonać przelewu na własny rachunek ani na kwotę przekraczającą saldo.</p>
        </div>
    </div>
@endsection
