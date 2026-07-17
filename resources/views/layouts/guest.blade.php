<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Logowanie') — {{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="guest-wrap">
        <div class="guest-card">
            <div class="brand">🏦 {{ config('app.name') }}</div>
            <p class="subtitle">@yield('subtitle', 'Zaloguj się do swojego konta')</p>

            @include('partials.flash')

            @yield('content')
        </div>
    </div>
</body>
</html>
