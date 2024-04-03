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

@section('content')

    <div class="container_default" style="background-color:   #f8fafc; padding: 50px">
        <div class="w-auto text-center">
            <h1>Manutenção de produção</h1>
        </div>
        <hr class="my-5">
        <div class="right_col" role="main">

            <form id="filtro" action="manutencao-status" method="post" data-parsley-validate="" class="form-horizontal form-label-left" novalidate="">
                <div class="form-group row">
                    @if ($mensagem!='')
                        <span class="text-danger">{{$mensagem}}</span>
                    @endif
                </div>
                <div class="form-group row">
                    @csrf <!--{{ csrf_field() }}-->
                    <label for="senha" class="col-sm-1 col-form-label">Senha</label>
                    <div class="col-sm-2">
                        <input type="password" id="senha" name="senha" class="form-control col-md-7 col-xs-12" value="">
                    </div>
                    <label for="os" class="col-sm-1 col-form-label">OS</label>
                    <div class="col-sm-2">
                        <input type="text" id="os" name="os" class="form-control col-md-7 col-xs-12" value="">
                    </div>
                    <div class="col-sm-5">
                        <button type="submit" class="btn btn-primary">Pesquisar</button>
                    </div>
                </div>
            </form>

            <hr class="my-5">
            <h3><b>Encontrados</b></h3>
            <table class="table table-striped text-center" id="table_composicao">
                <thead >
                    <tr>
                        <th scope="col">EP</th>
                        <th scope="col">OS</th>
                        <th scope="col">Status</th>
                        <th scope="col">Ação</th>
                    </tr>
                </thead>
                <tbody>
                    @if(!empty($pedidos))
                        @foreach ($pedidos as $pedido)
                            <tr style="padding-top: 10px">
                                <td style="margin-top: 10px" scope="col">{{$pedido->ep}}</td>
                                <td scope="col">{{$pedido->os}}</td>
                                <td scope="col">{{$pedido->nomeStatus}}</td>
                                <td scope="col">
                                    <button data-pedidoid={{$pedido->id}} data-descricaoproximostatus='{{$status[$pedido->id_status + 1]['nome']}}' data-proximostatus='{{$status[$pedido->id_status + 1]['id']}}' type="button" class="btn btn-primary alteracao_status_pedido">
                                        <span  style="font-size: 25px">&#9998;</button>
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>

    </div>

@stop
