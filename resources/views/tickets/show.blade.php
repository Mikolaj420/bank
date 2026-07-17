@extends('layouts.app')

@section('title', 'Zgłoszenie')

@section('content')
    <div class="page-head">
        <div>
            <h1>{{ $ticket->subject }}</h1>
            <p>Zgłoszenie #{{ $ticket->id }} &middot; {{ $ticket->created_at->format('Y-m-d H:i') }}</p>
        </div>
        <a href="{{ route('tickets.index') }}" class="btn btn-secondary">Wróć</a>
    </div>

    <div class="card">
        <dl class="dl">
            <dt>Klient</dt>
            <dd>{{ $ticket->user->full_name }} ({{ $ticket->user->email }})</dd>
            <dt>Status</dt>
            <dd>@include('partials.status-badge', ['status' => $ticket->status, 'label' => $ticket->status_label])</dd>
        </dl>

        <h2 style="margin-top: 18px;">Treść zgłoszenia</h2>
        <p style="white-space: pre-line;">{{ $ticket->message }}</p>

        @if ($ticket->response)
            <hr style="border: none; border-top: 1px solid var(--border); margin: 22px 0;">
            <h2>Odpowiedź banku</h2>
            <p class="muted" style="font-size: 0.85rem;">
                {{ optional($ticket->handler)->full_name }} &middot; {{ optional($ticket->handled_at)->format('Y-m-d H:i') }}
            </p>
            <p style="white-space: pre-line;">{{ $ticket->response }}</p>
        @endif
    </div>

    @can('handle', $ticket)
        <div class="card" style="max-width: 640px;">
            <h2>Odpowiedz i zamknij zgłoszenie</h2>
            <form method="POST" action="{{ route('tickets.reply', $ticket) }}">
                @csrf
                <div class="form-group">
                    <label for="response">Odpowiedź</label>
                    <textarea id="response" name="response" rows="5" maxlength="2000"
                              class="{{ $errors->has('response') ? 'is-invalid' : '' }}" required>{{ old('response') }}</textarea>
                    @error('response') <div class="error-text">{{ $message }}</div> @enderror
                </div>
                <button type="submit" class="btn btn-success">Wyślij odpowiedź</button>
            </form>
        </div>
    @endcan
@endsection
