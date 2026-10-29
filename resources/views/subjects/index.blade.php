@extends('layouts.app')
@section('title', 'Mijn vakken')
@section('content')
<div class="page-heading"><div><span class="eyebrow">EEN PLEK VOOR ELK VAK</span><h1>Mijn vakken</h1><p class="muted">Breng kleur en structuur in je studie.</p></div></div>
<div class="subjects-layout">
<section>
    <div class="subjects-grid">
    @forelse($subjects as $subject)
        <article class="card subject-card">
            <div class="subject-card-heading"><span class="subject-symbol color-{{ $subject->color ?? 'blue' }}">@include('partials.icon', ['name' => 'book'])</span><span class="badge pending">{{ $subject->open_tasks_count }} openstaand</span></div>
            <h2>{{ $subject->name }}</h2><a class="subject-tasks-link" href="{{ route('tasks.index', ['subject_id' => $subject->id, 'status' => 'open']) }}">Bekijk de taken →</a>
            <details @if(old('editing_subject') == $subject->id) open @endif><summary>Vak bewerken</summary>
                <form method="POST" action="{{ route('subjects.update', $subject) }}" class="stack-form">
                    @csrf @method('PUT')<input type="hidden" name="editing_subject" value="{{ $subject->id }}">
                    <div class="field"><label for="name-{{ $subject->id }}">Vaknaam</label><input id="name-{{ $subject->id }}" name="name" value="{{ old('editing_subject') == $subject->id ? old('name', $subject->name) : $subject->name }}" required maxlength="100"></div>
                    <div class="field"><label for="color-{{ $subject->id }}">Kleur</label><select id="color-{{ $subject->id }}" name="color">@foreach(\App\Models\Subject::COLORS as $value => $label)<option value="{{ $value }}" @selected((old('editing_subject') == $subject->id ? old('color', $subject->color) : $subject->color) === $value)>{{ $label }}</option>@endforeach</select></div><button class="button small">Opslaan</button>
                </form>
                <form method="POST" action="{{ route('subjects.destroy', $subject) }}" class="delete-area" data-confirm="Dit vak verwijderen? Je taken blijven behouden zonder vak.">@csrf @method('DELETE')<button class="danger-link">Vak verwijderen</button></form>
            </details>
        </article>
    @empty
        <div class="card empty-state"><span class="empty-icon">@include('partials.icon', ['name' => 'book'])</span><h3>Welke vakken volg jij?</h3><p>Voeg je eerste vak toe en koppel er je taken aan.</p></div>
    @endforelse
    </div>
</section>
<section class="card form-card add-subject"><h2>Een vak toevoegen</h2><p class="muted">Maak je planning herkenbaar.</p>
    <form method="POST" action="{{ route('subjects.store') }}" class="stack-form">@csrf
        <div class="field"><label for="name">Vaknaam</label><input id="name" name="name" value="{{ old('editing_subject') ? '' : old('name') }}" required maxlength="100" placeholder="Bijvoorbeeld: Programmeren"></div>
        <div class="field"><label for="color">Kleur</label><select id="color" name="color">@foreach(\App\Models\Subject::COLORS as $value => $label)<option value="{{ $value }}" @selected(!old('editing_subject') && old('color', 'blue') === $value)>{{ $label }}</option>@endforeach</select></div>
        <button class="button full">@include('partials.icon', ['name' => 'plus']) Vak toevoegen</button>
    </form><p class="help-text">Verwijder je later een vak? Je taken blijven gewoon bewaard.</p>
</section>
</div>
@endsection

