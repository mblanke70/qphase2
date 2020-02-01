@extends('layouts.app')

@section('title', 'Kurswahlen')

@section('heading')
    <h3 class="mt-4">{{ $schwerpunkt->name }}</h3>
@endsection

@section('choices')
    <div class="table-responsive mt-4">

        <table class="table table-bordered table-striped table-dark table-sm">
           <tr>
                <th scope="col">Typ</th>
                <th scope="col">Fach</th>
                <th scope="col">Lernfeld</th>
                <th scope="col">Stunden</th>
                <th scope="col">Halbjahre</th>
                <th scope="col">Einbringung</th>
            </tr>

        @foreach($matrix as $fachwahl)
            <tr>
                <td scope="col">
                    @if( $fachwahl['typ'] == 'wf')
                        <a href="#" onclick="event.preventDefault(); document.getElementById('delete-form-{{ $fachwahl['fach']->code }}').submit();">
                            <i class="fa fa-fw fa-trash" style="color:white"></i>
                        </a>
                       
                        <form id="delete-form-{{ $fachwahl['fach']->code }}" action="{{ url($stufe->code) }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                            {{ method_field('DELETE') }}
                            <input type="hidden" name="wahlfach" value="{{ $fachwahl['fach']->code }}" />
                        </form>
                    @else
                        {{ $fachwahl['typ'] }}
                    @endif
                </td>
                <td>{{ $fachwahl['fach']->name }}</td>
                <td>
                    @if( isset($fachwahl['fach']->lf) )
                        {{ chr(65 + $fachwahl['fach']->lf) }}
                    @endif
                </td>
                <td>{{ $fachwahl['stunden'] }}</td>
                <td>
                    {{ $fachwahl['halbjahre'] }}
                    <!--
                    @if($fachwahl['halbjahre'] == 2)
                    <form action="{{ url($stufe->code.'/'.$fachwahl['fach']->code) }}" method="POST">
                        {{ csrf_field() }}

                        <div class="form-check form-check-inline">
                            <input type="radio" id="halbjahre[{{$fachwahl['typ']}}]" name="halbjahre" value="2" class="form-check-input" @if( $fachwahl['halbjahre'] == 2) checked="checked" @endif>
                            <label class="form-check-label" for="halbjahre[{{$fachwahl['typ']}}]">2</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" id="halbjahre[{{$fachwahl['typ']}}]" name="halbjahre" value="4" class="form-check-input" @if( $fachwahl['halbjahre'] == 4) checked="checked" @endif>
                            <label class="form-check-label" for="halbjahre[{{$fachwahl['typ']}}]">4</label>
                        </div>
                    </form>
                    @else
                        {{ $fachwahl['halbjahre'] }}
                    @endif  
                    -->   
                </td>
                <td>{{ $fachwahl['einbringung'] }} Kurse</td>
            </tr>
        @endforeach

            <tr>
                <td scope="col"></td>
                <td></td>
                <td style="text-align: right;">Summe:</td>
                <td colspan="2">
                    @if(isset($summe1)) {{ $summe1 }} Kurse @endif
                    @if(isset($summe2)) ({{ $summe2 }} Stunden) @endif
                </td>
            </tr>

        </table>
        
    </div>

@endsection

@section('content')

    @if($summe1>36 )
        <div class="alert alert-danger">
            Die Anzahl der einzubringenden Kurse ({{$summe1}}) ist größer als 36.<br />Die Wahl ist nicht gültig.
        </div>
    @else
        <div class="alert alert-success">
            Die Anzahl der einzubringenden Kurse ({{$summe1}}) ist nicht größer als 36<br />Die Wahl ist gültig.
        </div>
    @endif

    <!--

    <h4 class="mt-3">{{ $stufe->name }}</h4>

    <div class="alert alert-warning">
        Es sind insgesamt {{$matrix->count()-5}} 3-stündigen Kurse belegt worden.
    </div>

    <form action="{{ url($stufe->code) }}" method="POST" role="form">

        {{ csrf_field() }}

        @if(isset($optionen))

            <div class="form-group">
                <select class="custom-select" name="wahlfach">
                    <option selected>Bitte auswählen...</option>

                @foreach ($optionen as $fach)
                    <option value="{{ $fach->code }}">{{ $fach->name }}</option>
                @endforeach

                </select>
            </div>

        @endif

        <div>
            <button type="submit" class="btn btn-primary">Hinzufügen</button>
        </div>

    </form>

    -->

@endsection

@section('footer')
    <a href="{{url('/')}}" class="btn btn-danger" role="button">Neustart</a>
@endsection

@section('js')
    <script>
        $(document).ready( function() {
            $('input[type=radio]').on('change', function(){
                $(this).closest('form').submit();
            });
        });
    </script>
@stop