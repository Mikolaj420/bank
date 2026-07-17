@extends('layouts.app')

@section('title', 'Raporty zbiorcze')

@section('content')
    <div class="page-head">
        <div>
            <h1>Raporty zbiorcze</h1>
            <p>Podsumowanie operacji w systemie.</p>
        </div>
    </div>

    <div class="grid cols-3">
        <div class="stat">
            <div class="label">Zrealizowane przelewy</div>
            <div class="value">{{ $summary['completed_count'] }}</div>
        </div>
        <div class="stat">
            <div class="label">Wolumen zrealizowany</div>
            <div class="value" style="font-size: 1.3rem;">{{ number_format($summary['completed_volume'], 2, ',', ' ') }} zł</div>
        </div>
        <div class="stat">
            <div class="label">Wolumen dzisiaj</div>
            <div class="value" style="font-size: 1.3rem;">{{ number_format($summary['today_volume'], 2, ',', ' ') }} zł</div>
        </div>
        <div class="stat">
            <div class="label">Oczekujące</div>
            <div class="value">{{ $summary['pending_count'] }}</div>
        </div>
        <div class="stat">
            <div class="label">Kwota oczekująca</div>
            <div class="value" style="font-size: 1.3rem;">{{ number_format($summary['pending_volume'], 2, ',', ' ') }} zł</div>
        </div>
        <div class="stat">
            <div class="label">Odrzucone</div>
            <div class="value">{{ $summary['rejected_count'] }}</div>
        </div>
    </div>

    <div class="card">
        <h2>Najaktywniejsze rachunki (wolumen wychodzący)</h2>
        @if ($topAccounts->isEmpty())
            <div class="empty">Brak danych.</div>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Właściciel</th>
                            <th>Numer rachunku</th>
                            <th class="text-right">Liczba przelewów</th>
                            <th class="text-right">Suma</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($topAccounts as $index => $row)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ optional(optional($row['account'])->user)->full_name ?? '—' }}</td>
                                <td class="mono">{{ optional($row['account'])->number ?? '—' }}</td>
                                <td class="text-right">{{ $row['transfers_count'] }}</td>
                                <td class="text-right">{{ number_format($row['total'], 2, ',', ' ') }} zł</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
