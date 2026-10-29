@extends('layouts.app')
@section('title', 'Er ging iets mis')
@section('content')
<section class="card empty-state"><span class="eyebrow">500</span><h1>Er ging iets mis</h1><p>Probeer het later opnieuw.</p><a class="button" href="{{ route('home') }}">Terug naar StudiePlanner</a></section>
@endsection

