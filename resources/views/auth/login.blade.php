@extends('layouts.app')
@section('title', 'Inloggen')
@section('content')
<div class="auth-layout">
    <section class="auth-story">
        <a class="brand" href="{{ route('login') }}"><span class="brand-mark">s<span>p</span></span><span>StudiePlanner</span></a>
        <div><span class="eyebrow">MEER OVERZICHT. MINDER STRESS.</span><h1>Een helder plan.<br>Een rustig hoofd.</h1><p>Huiswerk, toetsen en projecten op één plek. Zo houd jij ruimte voor alles naast je studie.</p></div>
        <div class="mini-planner" aria-hidden="true"><div class="mini-header">Jouw week, op een rij <span>✦</span></div><div class="mini-task"><span class="mini-check">✓</span><span>Huiswerk afronden<small>Weer een stap vooruit</small></span><span class="badge completed">Gedaan</span></div><div class="mini-task"><span class="mini-check open"></span><span>Voorbereiden op je toets<small>Op jouw tempo</small></span></div><div class="mini-progress"><span></span></div><small>Elke kleine stap telt.</small></div>
        <span class="auth-footnote">Voor studenten in het mbo, hbo en wo.</span>
    </section>
    <section class="auth-form card">
        <span class="eyebrow">JOUW STUDIEPLEK</span><h2>Welkom terug!</h2><p class="muted">Log in en pak je planning weer op.</p>
        <form method="POST" action="{{ route('login.store') }}" class="stack-form">
            @csrf
            <div class="field"><label for="email">E-mailadres</label><input id="email" type="email" name="email" value="{{ old('email') }}" required maxlength="255" autocomplete="email" autofocus placeholder="jij@voorbeeld.nl"></div>
            <div class="field"><label for="password">Wachtwoord</label><input id="password" type="password" name="password" required maxlength="72" autocomplete="current-password" placeholder="Je wachtwoord"></div>
            <button class="button full">Inloggen @include('partials.icon', ['name' => 'arrow'])</button>
        </form>
        <p class="auth-switch">Nog geen account? <a href="{{ route('register') }}">Maak een account</a></p>
    </section>
</div>
@endsection

