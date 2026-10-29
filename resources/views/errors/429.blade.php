@extends('layouts.app')
@section('title', 'Even rustig aan')
@section('content')
<section class="card empty-state"><span class="eyebrow">429</span><h1>Even rustig aan</h1><p>Je hebt te veel verzoeken verstuurd. Wacht even en probeer het opnieuw.</p><a class="button" href="{{ route('home') }}">Terug naar StudiePlanner</a></section>
@endsection

