@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<div class="page-heading"><div><span class="eyebrow">JOUW DAG, IN BEELD</span><h1>Welkom terug, {{ auth()->user()->name }}!</h1><p class="muted">Een helder overzicht. Klaar voor je volgende stap.</p></div><a class="button" href="{{ route('tasks.create') }}">@include('partials.icon', ['name' => 'plus']) Nieuwe taak</a></div>
<section class="welcome-banner"><div><span class="banner-kicker">ELKE STAP TELT</span><h2>Grote plannen beginnen<br>met kleine taken.</h2><p>Focus op vandaag. De rest komt stap voor stap.</p><a href="{{ route('week') }}">Bekijk jouw week @include('partials.icon', ['name' => 'arrow'])</a></div><div class="banner-art" aria-hidden="true"><div class="paper"><span class="paper-title">Mijn planning <b>✦</b></span><span><i>✓</i><em></em></span><span><i>✓</i><em></em></span><span><i></i><em></em></span><div class="paper-progress"></div></div><span class="art-spark">✧</span><span class="art-check">✓</span></div></section>
<div class="stats-grid">
    @foreach([['open', 'Openstaande taken', 'Alles wat nog op je wacht', 'book', 'blue'], ['today', 'Vandaag af te ronden', 'Een beetje focus voor vandaag', 'clock', 'orange'], ['week', 'Deze week af te ronden', 'Maandag tot en met zondag', 'calendar', 'purple'], ['completed', 'Afgeronde taken', 'Daar mag je trots op zijn', 'check', 'green']] as [$key, $label, $hint, $icon, $color])
    <section class="stat-card"><div class="stat-top"><span>{{ $label }}</span><span class="stat-icon tone-{{ $color }}">@include('partials.icon', ['name' => $icon])</span></div><strong>{{ $stats[$key] }}</strong><small>{{ $hint }}</small></section>
    @endforeach
</div>
<div class="dashboard-grid">
    <div>
        <section class="card task-section"><div class="section-heading"><h2>Vandaag <span class="count">{{ $todayTasks->count() }}</span></h2><span class="muted">{{ today()->translatedFormat('j F') }}</span></div>
            @forelse($todayTasks as $task) @include('partials.task') @empty<div class="empty-state"><span class="empty-icon">@include('partials.icon', ['name' => 'check'])</span><h3>Alle ruimte voor vandaag</h3><p>Je hebt vandaag geen taken. Goed bezig!</p><a href="{{ route('tasks.create') }}">Plan een nieuwe taak →</a></div>@endforelse
        </section>
        <section class="card task-section"><div class="section-heading"><h2>Binnenkort <span class="count">{{ $upcomingTasks->count() }}</span></h2><span class="muted">Komende 7 dagen</span></div>
            @forelse($upcomingTasks as $task) @include('partials.task') @empty<div class="empty-state compact"><p>Geen openstaande taken in de komende zeven dagen.</p><a href="{{ route('week') }}">Bekijk je weekoverzicht →</a></div>@endforelse
        </section>
    </div>
    <div>
        <section class="card task-section overdue-section"><div class="section-heading"><h2>@include('partials.icon', ['name' => 'alert']) Verlopen deadlines <span class="count">{{ $overdueTasks->count() }}</span></h2></div>
            @forelse($overdueTasks as $task) @include('partials.task') @empty<div class="empty-state compact"><span class="empty-icon green">@include('partials.icon', ['name' => 'check'])</span><h3>Helemaal bij!</h3><p>Je hebt geen verlopen deadlines.<br>Houd dat vast.</p></div>@endforelse
        </section>
        <div class="study-tip"><span class="eyebrow">EEN KLEINE STUDIETIP</span><h3>Begin met één ding.</h3><p>Kies een taak, leg je telefoon weg en werk er even aan. Beginnen is vaak de grootste stap.</p><span>Jij kunt dit. ✦</span></div>
    </div>
</div>
@endsection

