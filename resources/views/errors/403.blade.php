@extends('layouts.app')
@section('title', 'Geen toegang')
@section('content')
<section class="card empty-state"><span class="eyebrow">403</span><h1>Geen toegang</h1><p>Je hebt geen toegang tot deze pagina.</p><a class="button" href="{{ route('home') }}">Terug naar StudiePlanner</a></section>
@endsection

