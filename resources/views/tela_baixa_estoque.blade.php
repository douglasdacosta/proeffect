@extends('layouts.app')

<style type="text/css">
    .container_default {
        width: 100%;
        height: 100%;
        padding: 10px;
    }

    .container_default .table {
        font-size: 1.2em;
    }
</style>

@section('content')

    <div class="container_default" style="background-color:   #f8fafc; padding: 50px">
        <div class="w-auto text-center">
            <h2>Baixa de estoque</h2>
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
            <input type="hidden" id="senha_funcionario" name="senha_funcionario" value="{{isset($senha) ? $senha : ''}}">
            <hr class="my-5">
            <h4><b>Encontrados</b></h4>
            <table class="table table-striped text-center" id="table_composicao">
                <thead >
                    <tr>
                        <th scope="col">EP</th>
                        <th scope="col">OS</th>
                        <th scope="col">Status</th>
                        <th scope="col">Etapa</th>
                        <th scope="col">Nº máquina</th>
                        <th scope="col">Motivo pausa</th>
                        <th scope="col">Qtde</th>
                        <th scope="col">Responsável</th>
                        <th scope="col">Colaborador</th>
                        <th scope="col">Ação</th>
                    </tr>
                </thead>
                <tbody>
                    @if(!empty($pedidos))
                        @foreach ($pedidos as $pedido)
                            @if(isset($pedido->colaboradores[$pedido->id]))
                                <?php $contador =0 ?>
                                @foreach ($pedido->colaboradores[$pedido->id] as $colaborador)
                                    <tr style="padding-top: 10px">
                                        <td style="margin-top: 10px" scope="col">{{$pedido->ep}}</td>
                                        <td scope="col">{{$pedido->os}}</td>
                                        <td scope="col">{{$pedido->nomeStatus}}</td>
                                        <td scope="col">{{$colaborador['nome_etapa'] }}</td>
                                        <td scope="col">{{$colaborador['numero_maquina'] }}</td>
                                        <td scope="col">{{$colaborador['select_motivo_pausas']}}</td>
                                        <td scope="col">{{$colaborador['texto_quantidade']}}</td>
                                        <td scope="col">@if($pedido->id_status == 6){{$pedido->funcionario}}@else @endif</td>
                                        <td scope="col">{{$colaborador['nome']}}</td>
                                        <td scope="col">
                                            <?php $st = ($pedido->id_status == 11) ? $pedido->id_status : $pedido->id_status + 1 ?>

                                            <button data-pedidoid={{$pedido->id}} data-etapasalteracao='{{$colaborador['etapas_alteracao_id']}}' data-statusatual='{{$pedido->id_status}}' data-descricaoproximostatus='{{$status[$st]['nome']}}' data-proximostatus='{{$status[$st]['id']}}' type="button" class="btn btn-primary alteracao_status_pedido">
                                                <span  style="font-size: 25px">&#9998;</button>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php $contador++ ?>
                                @endforeach
                            @else
                                <tr>
                                    <td scope="col">{{$pedido->ep}}</td>
                                    <td scope="col">{{$pedido->os}}</td>
                                    <td scope="col">{{$pedido->nomeStatus}}</td>
                                    <td scope="col"></td>
                                    <td scope="col"></td>
                                    <td scope="col"></td>
                                    <td scope="col"></td>
                                    <td scope="col"></td>
                                    <td scope="col"></td>
                                    <td scope="col">
                                        <?php $st = ($pedido->id_status == 11) ? $pedido->id_status : $pedido->id_status + 1 ?>
                                        <button data-pedidoid={{$pedido->id}} data-statusatual='{{$pedido->id_status}}' data-descricaoproximostatus='{{$status[$st]['nome']}}' data-proximostatus='{{$status[$st]['id']}}' type="button" class="btn btn-primary alteracao_status_pedido">
                                            <span  style="font-size: 25px">&#9998;</button>
                                        </span>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    @endif
                </tbody>
            </table>
    </div>
@stop
