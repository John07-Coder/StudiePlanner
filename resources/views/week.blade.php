@extends('layouts.app')
@section('title', 'Weekoverzicht')
@section('content')
<div class="page-heading"><div><span class="eyebrow">KIJK EEN STAP VOORUIT</span><h1>Jouw weekoverzicht</h1><p class="muted">Een hele week in beeld. Plan met ruimte voor jezelf.</p></div><a class="button" href="{{ route('tasks.create') }}">@include('partials.icon', ['name' => 'plus']) Nieuwe taak</a></div>
<div class="week-toolbar"><div><h2>Week {{ $start->isoWeek() }}</h2><span class="muted">{{ $start->translatedFormat('j F Y') }} – {{ $end->translatedFormat('j F Y') }}</span></div><nav aria-label="Weken" class="week-navigation"><a class="button secondary" href="{{ route('week', ['date' => $start->copy()->subWeek()->toDateString()]) }}" aria-label="Vorige week">← <span>Vorige</span></a><a class="button secondary" href="{{ route('week') }}">Deze week</a><a class="button secondary" href="{{ route('week', ['date' => $start->copy()->addWeek()->toDateString()]) }}" aria-label="Volgende week"><span>Volgende</span> →</a></nav></div>
<div class="week-grid">
    @foreach($days as $day)
    <section @class(['day-column', 'is-today' => $day->isToday()]) data-date="{{ $day->toDateString() }}">
        <div class="day-heading"><span>{{ ucfirst($day->translatedFormat('l')) }}</span><strong>{{ $day->format('d') }}</strong><small>{{ $day->isToday() ? 'Vandaag' : $day->translatedFormat('F') }}</small></div>
        <div class="day-tasks">
        @forelse($tasksByDay->get($day->toDateString(), collect()) as $task)
            @include('partials.task')
        @empty<p class="day-empty">Geen taken<br><span>Even ademruimte.</span></p>@endforelse
        </div>
    </section>
    @endforeach
</div>
<p class="week-hint">@include('partials.icon', ['name' => 'check']) Vink een taak af of klik op de titel om deze te bewerken.</p>
@endsection

