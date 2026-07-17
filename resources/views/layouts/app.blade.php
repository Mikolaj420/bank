<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Bankowość internetowa') — {{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    @php($user = auth()->user())

    <header class="topbar">
        <div class="container">
            <a href="{{ route('dashboard') }}" class="brand">🏦 {{ config('app.name') }}</a>

            <nav class="nav">
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Pulpit</a>

                @if ($user->isClient())
                    <a href="{{ route('account.show') }}" class="{{ request()->routeIs('account.*') ? 'active' : '' }}">Moje konto</a>
                    <a href="{{ route('transfers.index') }}" class="{{ request()->routeIs('transfers.*') ? 'active' : '' }}">Przelewy</a>
                    <a href="{{ route('tickets.index') }}" class="{{ request()->routeIs('tickets.*') ? 'active' : '' }}">Zgłoszenia</a>
                @endif

                @if ($user->isEmployee())
                    <a href="{{ route('employee.accounts') }}" class="{{ request()->routeIs('employee.*') ? 'active' : '' }}">Rachunki klientów</a>
                    <a href="{{ route('tickets.index') }}" class="{{ request()->routeIs('tickets.*') ? 'active' : '' }}">Zgłoszenia</a>
                @endif

                @if ($user->isManager())
                    <a href="{{ route('approvals.index') }}" class="{{ request()->routeIs('approvals.*') ? 'active' : '' }}">Akceptacje</a>
                    <a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">Raporty</a>
                @endif

                @if ($user->isAdmin())
                    <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">Użytkownicy</a>
                @endif
            </nav>

            <div class="nav-user">
                <span class="role-chip">{{ $user->role->label }}</span>
                <span>{{ $user->full_name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-secondary">Wyloguj</button>
                </form>
            </div>
        </div>
    </header>

    <main class="content">
        <div class="container">
            @include('partials.flash')
            @yield('content')
        </div>
    </main>
</body>
</html>
