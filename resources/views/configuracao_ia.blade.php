@extends('adminlte::page')

@section('title', 'Pro Effect')
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="js/jquery.mask.js"></script>
<script src="js/main_custom.js"></script>

@section('content_header')
    <h1 class="m-0 text-dark">{{ $nome_tela }}</h1>
@stop

@section('content')
    <div class="right_col" role="main">

        @if ($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Sucesso!</strong> {{ $message }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <form id="configuracao" action="configuracao-ia" data-parsley-validate="" class="form-horizontal form-label-left" method="post">
            @csrf
            <div class="form-group row">
                <label for="tempo_entrega_dias" class="col-sm-3 col-form-label">Tempo de entrega (em dias)</label>
                <div class="col-sm-2">
                    <input type="text" class="form-control sonumeros" id="tempo_entrega_dias" name="tempo_entrega_dias" value="@if (isset($config->tempo_entrega_dias)){{$config->tempo_entrega_dias}}@else{{''}}@endif">
                </div>
            </div>
            <div class="form-group row">
                <label for="tempo_cliente_sem_compra_dias" class="col-sm-3 col-form-label">Tempo que o cliente não compra (em dias)</label>
                <div class="col-sm-2">
                    <input type="text" class="form-control sonumeros" id="tempo_cliente_sem_compra_dias" name="tempo_cliente_sem_compra_dias" value="@if (isset($config->tempo_cliente_sem_compra_dias)){{$config->tempo_cliente_sem_compra_dias}}@else{{''}}@endif">
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-5">
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </div>
        </form>

    </div>
@stop
