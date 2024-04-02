@extends('layouts.app')

@section('content')

@foreach ($folhas as $key => $folha)

    @include('layouts.os.'.Str::lower($folha['indicador_status']))

    @endforeach
@stop
