@extends('layouts.app')

@section('title', 'Pulpit pracownika')

@section('content')
    <div class="page-head">
        <div>
            <h1>Pulpit pracownika</h1>
            <p>Obsługa klientów i zgłoszeń.</p>
        </div>
    </div>

    <div class="grid cols-3">
        <div class="stat">
            <div class="label">Klienci</div>
            <div class="value">{{ $clientsCount }}</div>
        </div>
        <div class="stat">
            <div class="label">Rachunki</div>
            <div class="value">{{ $accountsCount }}</div>
        </div>
        <div class="stat">
            <div class="label">Otwarte zgłoszenia</div>
            <div class="value">{{ $openTicketsCount }}</div>
        </div>
    </div>

    <div class="card">
        <h2>Najnowsze zgłoszenia</h2>
        @if ($recentTickets->isEmpty())
            <div class="empty">Brak zgłoszeń.</div>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Klient</th>
                            <th class="wrap">Temat</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentTickets as $ticket)
                            <tr>
                                <td>{{ $ticket->created_at->format('Y-m-d H:i') }}</td>
                                <td>{{ $ticket->user->full_name }}</td>
                                <td class="wrap">{{ $ticket->subject }}</td>
                                <td>@include('partials.status-badge', ['status' => $ticket->status, 'label' => $ticket->status_label])</td>
                                <td><a href="{{ route('tickets.show', $ticket) }}">Otwórz</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
        <div style="margin-top: 14px;">
            <a href="{{ route('employee.accounts') }}">Przeglądaj rachunki klientów &rarr;</a>
        </div>
    </div>
@endsection
