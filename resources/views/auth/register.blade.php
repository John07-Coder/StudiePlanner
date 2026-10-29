@extends('layouts.app')
@section('title', 'Account aanmaken')
@section('content')
<div class="auth-layout">
    <section class="auth-story">
        <a class="brand" href="{{ route('login') }}"><span class="brand-mark">s<span>p</span></span><span>StudiePlanner</span></a>
        <div><span class="eyebrow">BEGIN MET OVERZICHT</span><h1>Maak ruimte<br>voor je studie.</h1><p>Een eigen plek voor je vakken, taken en deadlines. Je volgende stap begint hier.</p></div>
        <div class="auth-benefits"><p>✓ Al je deadlines op één plek</p><p>✓ Een helder overzicht van je week</p><p>✓ Je voortgang in één oogopslag</p></div>
        <span class="auth-footnote">Voor studenten in het mbo, hbo en wo.</span>
    </section>
    <section class="auth-form card">
        <span class="eyebrow">EEN FRISSE START</span><h2>Maak je account</h2><p class="muted">Een beetje planning maakt het verschil.</p>
        <form method="POST" action="{{ route('register.store') }}" class="stack-form">
            @csrf
            <div class="field"><label for="name">Naam</label><input id="name" name="name" value="{{ old('name') }}" required maxlength="100" autocomplete="name" autofocus placeholder="Je naam"></div>
            <div class="field"><label for="email">E-mailadres</label><input id="email" type="email" name="email" value="{{ old('email') }}" required maxlength="255" autocomplete="email" placeholder="jij@voorbeeld.nl"></div>
            <div class="field"><label for="password">Wachtwoord</label><input id="password" type="password" name="password" required minlength="8" maxlength="72" autocomplete="new-password" aria-describedby="password-help"><small id="password-help">Gebruik minimaal 8 tekens.</small></div>
            <div class="field"><label for="password_confirmation">Bevestig wachtwoord</label><input id="password_confirmation" type="password" name="password_confirmation" required minlength="8" maxlength="72" autocomplete="new-password"></div>
            <button class="button full">Account aanmaken @include('partials.icon', ['name' => 'arrow'])</button>
        </form>
        <p class="auth-switch">Al een account? <a href="{{ route('login') }}">Log in</a></p>
    </section>
</div>
@endsection

