@extends('adminlte::page')

@section('title', 'Pro Effect')
<script src="../vendor/jquery/jquery.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="js/main_charts.js"></script>

@section('content_header')
    <h1 class="m-0 text-dark">Home</h1>
@stop

@section('content')
<div class="right_col" role="main">

        <div class="form-group row">
            <canvas id="grafico"></canvas>
        </div>
</div>

@stop

