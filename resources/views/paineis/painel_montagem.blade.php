@extends('layouts.app')

<style type="text/css">
    .container_default {
        width: 100%;
        height: 100%;
        padding: 10px;
    }

    .container_default .table {
        font-size: 1.5em;
    }
</style>
<script type="text/javascript" >

setTimeout(function () {
    location.reload();
    }, 3000);
</script>
@section('content')


    <div class="container_default" style="background-color: #ffffff;">
        <div class="w-auto text-center">
            <h1>Painel de Montagem</h1>
        </div>
        <?php $montagem = true ?>
        @include('paineis.div_table')
    </div>

@stop
