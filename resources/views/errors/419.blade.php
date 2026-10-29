@extends('layouts.app')
@section('title', 'Je sessie is verlopen')
@section('content')
<section class="card empty-state"><span class="eyebrow">419</span><h1>Je sessie is verlopen</h1><p>Vernieuw de pagina en probeer het opnieuw. Log zo nodig opnieuw in.</p><a class="button" href="{{ route('home') }}">Terug naar StudiePlanner</a></section>
@endsection

