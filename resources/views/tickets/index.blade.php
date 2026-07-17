@extends('layouts.app')

@section('title', 'Zgłoszenia')

@section('content')
    <div class="page-head">
        <div>
            <h1>Zgłoszenia</h1>
            <p>{{ auth()->user()->isStaff() ? 'Zgłoszenia klientów banku.' : 'Twoje zgłoszenia do banku.' }}</p>
        </div>
        @if (auth()->user()->isClient())
            <a href="{{ route('tickets.create') }}" class="btn">Nowe zgłoszenie</a>
        @endif
    </div>

    <div class="card">
        @if ($tickets->isEmpty())
            <div class="empty">Brak zgłoszeń.</div>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Data</th>
                            @if (auth()->user()->isStaff())
                                <th>Klient</th>
                            @endif
                            <th class="wrap">Temat</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tickets as $ticket)
                            <tr>
                                <td>{{ $ticket->created_at->format('Y-m-d H:i') }}</td>
                                @if (auth()->user()->isStaff())
                                    <td>{{ $ticket->user->full_name }}</td>
                                @endif
                                <td class="wrap">{{ $ticket->subject }}</td>
                                <td>@include('partials.status-badge', ['status' => $ticket->status, 'label' => $ticket->status_label])</td>
                                <td><a href="{{ route('tickets.show', $ticket) }}">Otwórz</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $tickets->links() }}
        @endif
    </div>
@endsection
