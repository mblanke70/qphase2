@extends('layouts.app')

@section('title', 'Kurswahlen')

@section('heading')
    <h3 class="mt-4">Kurswahlen Q-Phase</h3>
@endsection

@section('content')

    <h4 class="mt-4">Schwerpunktwahl</h4>

    @if ($errors->any())
    <div class="alert alert-danger">
        {{ $errors->first() }}
    </div>
    @endif
    
    <form class="mt-4" action="{{ url('schwerpunkt') }}" method="POST" role="form">
        @csrf

        @foreach ($schwerpunkte as $sp)

        <div class="form-group">
            <div class="custom-control custom-radio">
                <input type="radio" id="{{ $sp->code }}" name="schwerpunkt" value="{{ $sp->code }}" class="custom-control-input">
                <label class="custom-control-label" for="{{ $sp->code }}">{{ $sp->name }}
                </label>
            </div>
        </div>

        @endforeach
        
        <div>
            <button type="submit" class="btn btn-primary">Weiter</button>
        </div>

    </form>

@endsection