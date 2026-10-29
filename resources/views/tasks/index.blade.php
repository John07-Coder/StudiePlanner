@extends('layouts.app')
@section('title', 'Mijn taken')
@section('content')
<div class="page-heading"><div><span class="eyebrow">VAN PLAN NAAR GEDAAN</span><h1>Mijn taken</h1><p class="muted">Huiswerk, toetsen en projecten. Alles op één plek.</p></div><a class="button" href="{{ route('tasks.create') }}">@include('partials.icon', ['name' => 'plus']) Nieuwe taak</a></div>
<form method="GET" action="{{ route('tasks.index') }}" class="card filters">
    <div class="field"><label for="status">Status</label><select id="status" name="status"><option value="">Alle taken</option value="open" @selected(request('status') === 'open')>Openstaande taken</option><option value="completed" @selected(request('status') === 'completed')>Afgeronde taken</option></select></div>
    <div class="field"><label for="type">Type</label><select id="type" name="type"><option value="">Alle typen</option>@foreach(\App\Models\Task::TYPES as $value => $label)<option value="{{ $value }}" @selected(request('type') === $value)>{{ $label }}</option>@endforeach</select></div>
    <div class="field"><label for="subject_id">Vak</label><select id="subject_id" name="subject_id"><option value="">Alle vakken</option>@foreach($subjects as $subject)<option value="{{ $subject->id }}" @selected(request('subject_id') == $subject->id)>{{ $subject->name }}</option>@endforeach</select></div>
    <button class="button">Filteren</button><a class="reset-link" href="{{ route('tasks.index') }}">Wissen</a>
</form>
<section class="card task-section"><div class="section-heading"><h2>Je taken <span class="count">{{ $tasks->total() }}</span></h2><span class="muted">Gesorteerd op deadline</span></div>
    @forelse($tasks as $task) @include('partials.task') @empty<div class="empty-state"><span class="empty-icon">@include('partials.icon', ['name' => 'book'])</span><h3>Een frisse start</h3><p>Geen taken gevonden. Voeg een taak toe of pas je filters aan.</p><a class="button" href="{{ route('tasks.create') }}">Je eerste taak plannen</a></div>@endforelse
</section>
{{ $tasks->links('partials.pagination') }}
@endsection

