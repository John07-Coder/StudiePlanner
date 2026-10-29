@extends('layouts.app')
@section('title', $task->exists ? 'Taak bewerken' : 'Nieuwe taak')
@section('content')
<a class="back-link" href="{{ route('tasks.index') }}">← Terug naar mijn taken</a>
<div class="page-heading"><div><span class="eyebrow">GEEF JE PLANNEN EEN PLEK</span><h1>{{ $task->exists ? 'Taak bewerken' : 'Een nieuwe taak' }}</h1><p class="muted">Maak het concreet. Wat wil je af hebben?</p></div></div>
<div class="form-layout">
<section class="card form-card">
    <form method="POST" action="{{ $task->exists ? route('tasks.update', $task) : route('tasks.store') }}" class="stack-form">
        @csrf @if($task->exists) @method('PUT') @endif
        <div class="field"><label for="title">Titel <span class="required">*</span></label><input id="title" name="title" value="{{ old('title', $task->title) }}" required maxlength="255" autofocus placeholder="Bijvoorbeeld: hoofdstuk 3 samenvatten"></div>
        <div class="field"><label for="description">Omschrijving <span class="optional">optioneel</span></label><textarea id="description" name="description" rows="5" maxlength="10000" placeholder="Wat moet je doen? Voeg hier de details toe.">{{ old('description', $task->description) }}</textarea></div>
        <div class="field"><label for="subject_id">Vak <span class="optional">optioneel</span></label><select id="subject_id" name="subject_id"><option value="">Zonder vak</option>@foreach($subjects as $subject)<option value="{{ $subject->id }}" @selected(old('subject_id', $task->subject_id) == $subject->id)>{{ $subject->name }}</option>@endforeach</select><small>Je kunt vakken toevoegen bij <a href="{{ route('subjects.index') }}">Mijn vakken</a>.</small></div>
        <div class="form-grid">
            <div class="field"><label for="type">Type <span class="required">*</span></label><select id="type" name="type" required>@foreach(\App\Models\Task::TYPES as $value => $label)<option value="{{ $value }}" @selected(old('type', $task->type ?? 'homework') === $value)>{{ $label }}</option>@endforeach</select></div>
            <div class="field"><label for="priority">Prioriteit <span class="required">*</span></label><select id="priority" name="priority" required>@foreach(\App\Models\Task::PRIORITIES as $value => $label)<option value="{{ $value }}" @selected(old('priority', $task->priority ?? 'medium') === $value)>{{ $label }}</option>@endforeach</select></div>
        </div>
        <div class="field"><label for="due_date">Deadline <span class="required">*</span></label><input id="due_date" type="date" name="due_date" min="1900-01-01" max="2100-12-31" value="{{ old('due_date', $task->due_date?->format('Y-m-d') ?? today()->toDateString()) }}" required></div>
        <div class="form-actions"><button class="button">{{ $task->exists ? 'Wijzigingen opslaan' : 'Taak opslaan' }}</button><a class="button secondary" href="{{ route('tasks.index') }}">Annuleren</a></div>
    </form>
    @if($task->exists)<form method="POST" action="{{ route('tasks.destroy', $task) }}" class="delete-area" data-confirm="Weet je zeker dat je deze taak wilt verwijderen?">@csrf @method('DELETE')<button class="danger-link">Taak verwijderen</button></form>@endif
</section>
<aside class="form-help"><span class="empty-icon">@include('partials.icon', ['name' => 'book'])</span><h3>Klein maken helpt.</h3><p>Geef je taak een duidelijke titel en een haalbare deadline. Zo weet je precies waar je aan toe bent.</p><p><span class="required">*</span> Deze velden zijn verplicht.</p></aside>
</div>
@endsection

