@extends('layouts.app')
@section('title', 'Pagina niet gevonden')
@section('content')
<section class="card empty-state"><span class="eyebrow">404</span><h1>Pagina niet gevonden</h1><p>Deze pagina bestaat niet of is niet beschikbaar voor jouw account.</p><a class="button" href="{{ route('home') }}">Terug naar StudiePlanner</a></section>
@endsection

