@extends('layouts.app')
@section('title', 'We zijn even bezig')
@section('content')
<section class="card empty-state"><span class="eyebrow">503</span><h1>We zijn even bezig</h1><p>StudiePlanner is tijdelijk niet beschikbaar. Probeer het straks opnieuw.</p><a class="button" href="{{ route('home') }}">Terug naar StudiePlanner</a></section>
@endsection

