<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('code') — {{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="guest-wrap">
        <div class="guest-card" style="text-align: center;">
            <div style="font-size: 3.4rem; font-weight: 700; color: var(--navy);">@yield('code')</div>
            <h1 style="margin: 6px 0 10px;">@yield('heading')</h1>
            <p class="muted">@yield('message')</p>
            <a href="{{ url('/') }}" class="btn" style="margin-top: 14px;">Wróć na stronę główną</a>
        </div>
    </div>
</body>
</html>
