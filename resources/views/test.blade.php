@extends('layouts.sport')

@section('title', 'Kurswahlen')

@section('heading')
    <h3 class="mt-3">Sportwahl für die Jahrgangsstufen 12 und 13: <strong>
    
    @if ( $user != null )
        {{ $user->name }}
    @endif
    
    </strong></h3>
@endsection

@section('content')

    @if ($errors->any())
    <div class="alert alert-danger">
        {{ $errors->first() }}
    </div>
    @endif
    
   

    <p>Eine Neuwahl ist bis zum 12.5.2019, 20 Uhr, möglich.</p>

   
@endsection