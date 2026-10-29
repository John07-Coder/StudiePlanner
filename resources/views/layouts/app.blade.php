<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#172e50">
    <title>@yield('title', 'Dashboard') · StudiePlanner</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="{{ asset('js/app.js') }}" defer></script>
</head>
<body class="@auth app-body @else guest-body @endauth">
<a href="#main" class="skip-link">Naar de inhoud</a>
@auth
    <aside class="sidebar">
        <a class="brand" href="{{ route('dashboard') }}"><span class="brand-mark">s<span>p</span></span><span>StudiePlanner<small>Ruimte in je hoofd.</small></span></a>
        <div class="sidebar-label">MIJN STUDIE</div>
        <nav class="main-nav" aria-label="Hoofdnavigatie">
            @foreach(['dashboard' => ['Dashboard', 'grid'], 'tasks.index' => ['Mijn taken', 'check'], 'week' => ['Weekoverzicht', 'calendar'], 'subjects.index' => ['Mijn vakken', 'book']] as $route => [$label, $icon])
                <a href="{{ route($route) }}" @class(['nav-link', 'active' => request()->routeIs($route === 'tasks.index' ? 'tasks.*' : $route)]) @if(request()->routeIs($route === 'tasks.index' ? 'tasks.*' : $route)) aria-current="page" @endif>
                    @include('partials.icon', ['name' => $icon]) {{ $label }}
                </a>
            @endforeach
        </nav>
        <div class="sidebar-note"><span class="note-spark">✦</span><strong>Stap voor stap vooruit.</strong><p>Een kleine taak vandaag is een zorg minder voor morgen.</p></div>
        <div class="account">
            <span class="avatar">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
            <span class="account-name">{{ auth()->user()->name }}<small>Mijn studieplek</small></span>
            <form method="POST" action="{{ route('logout') }}">@csrf<button class="logout" title="Uitloggen" aria-label="Uitloggen">@include('partials.icon', ['name' => 'logout'])</button></form>
        </div>
    </aside>
@endauth
<div class="page-shell">
    @auth
        <header class="topbar"><span>@yield('title', 'Dashboard')</span><span class="topbar-date">@include('partials.icon', ['name' => 'calendar']) {{ now()->translatedFormat('l j F Y') }}</span></header>
    @endauth
    <main id="main" class="@auth main-content @else guest-content @endauth">
        @if(session('success'))<div class="alert success" role="status">@include('partials.icon', ['name' => 'check']) {{ session('success') }}</div>@endif
        @if($errors->any())
            <div class="alert error" role="alert"><strong>Controleer je gegevens.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif
        @yield('content')
    </main>
    @auth<footer class="footer"><span>StudiePlanner</span><span>Jouw studie. Jouw overzicht.</span></footer>@endauth
</div>
</body>
</html>

