<?php
use App\Http\Controllers\PedidosController;
$palheta_cores = [1 => '#ff003d', 2 => '#ee7e4c', 3 => '#8f639f', 4 => '#94c5a5', 5 => '#ead56c', 6 => '#0fbab7', 7 => '#f7c41f', 8 => '#898b75', 9 => '#c1d9d0', 10 => '#da8f72', 11 => '#00caf8', 12 => '#ffe792', 13 => '#9a5071'];
?>
@extends('adminlte::page')

@section('title', 'Pro Effect')
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="js/jquery.mask.js"></script>
<script src="js/main_custom.js"></script>
<link rel="stylesheet" href="{{ asset('css/main_style.css') }}" />
@switch($tela)
    @case('pesquisar')
        @section('content_header')
            <div class="form-group row">
                <h1 class="m-0 text-dark col-sm-10 col-form-label">Pesquisa de {{ $nome_tela }}</h1>
                <div class="col-sm-1">
                    <h1>
                        @if ($alertasPendentes > 0)
                            <a title="Pendência de Alerta para o cliente" href={{ URL::route('alertas-pedidos') }}>
                                <i class="fa fa-solid fa-bell fa-2xl text-danger"></i>
                            </a>
                        @endif
                    </h1>
                </div>
                <div class="col-sm-1">
                    @include('layouts.nav-open-incluir', ['rotaIncluir => $rotaIncluir'])
                </div>
            </div>
        @stop
        @section('content')
            <div id="toastsContainerTopRight" class="toasts-top-right fixed">
                <div class="toast fade show" role="alert" style="width: 350px" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header">
                        <strong class="mr-auto">Alerta!</strong>
                        <small></small>
                        <button data-dismiss="toast" type="button" class="ml-2 mb-1 close" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="toast-body textoAlerta" style="text-decoration-style: solid; font-weight: bold; font-size: larger;">
                    </div>
                </div>
            </div>
            <div class="right_col" role="main">

                <form id="filtro" action="pedidos" method="get" data-parsley-validate=""
                    class="form-horizontal form-label-left" novalidate="">
                    <div class="form-group row">
                        <label for="codigo_cliente" class="col-sm-1 col-form-label text-right">Código cliente</label>
                        <div class="col-sm-1">
                            <input type="text" id="codigo_cliente" name="codigo_cliente" class="form-control col-md-13"
                                value="">
                        </div>

                        <label for="nome_cliente" class="col-sm-2 col-form-label text-right">Nome cliente</label>
                        <div class="col-sm-1">
                            <input type="text" id="nome_cliente" name="nome_cliente" class="form-control col-md-13"
                                value="">
                        </div>
                        <label for="os" class="col-sm-2 col-form-label text-right">OS</label>
                        <div class="col-sm-1">
                            <input type="text" id="os" name="os" class="form-control" value="">
                        </div>
                        <label for="blank" class="col-sm-2 col-form-label text-right text-sm-end">Status do pedido</label>
                        <div class="col-sm-2">
                            <select class="form-control" id="status_id" name="status_id">
                                <option value=""></option>
                                @if (isset($AllStatus))
                                    @foreach ($AllStatus as $stats)
                                        <option value="{{ $stats->id }}">{{ $stats->nome }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">


                        <label for="ep" class="col-sm-1 col-form-label text-right">EP</label>
                        <div class="col-sm-1">
                            <input type="text" id="ep" name="ep" class="form-control col-md-13" value="">
                        </div>
                        <label for="data_entrega" class="col-sm-2 col-form-label text-right">Data entrega: de</label>
                        <div class="col-sm-1">
                            <input type="text" class="form-control mask_date" id="data_entrega" name="data_entrega"
                                placeholder="DD/MM/AAAA">
                        </div>
                        <label for="data_entrega_fim" class="col-form-label text-right">até</label>
                        <div class="col-sm-1">
                            <input type="text" class="form-control mask_date" id="data_entrega_fim" name="data_entrega_fim"
                                placeholder="DD/MM/AAAA">
                        </div>
                        <label for="status" class="col-sm-1 col-form-label">&nbsp;</label>
                        <div class="col-sm-2">
                            <select class="form-control col-md-5" id="status" name="status">
                                <option value="A" @if (isset($request) && $request->input('status') == 'A') {{ ' selected ' }}@else @endif>Ativo
                                </option>
                                <option value="I" @if (isset($request) && $request->input('status') == 'I') {{ ' selected ' }}@else @endif>Inativo
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-5">
                            <button type="submit" class="btn btn-primary">Pesquisar</button>
                        </div>
                        <div class="col-sm-5">
                            <div class="overlay" style="display: none;">
                                <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for=""></label>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="x_panel">
                            <div class="x_title">
                                <h4>Encontrados</h4>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">
                                <table class="table table-striped  text-center">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>OS</th>
                                            <th>EP</th>
                                            <th>Qtde</th>
                                            <th>Cliente</th>
                                            <th>Status do pedido</th>
                                            <th>Data gerado</th>
                                            <th>Data entrega</th>
                                            <th>Alerta dias</th>
                                            <th>OS</th>
                                            <th>MP</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (isset($pedidos))
                                            @foreach ($pedidos as $pedido)
                                                <tr>
                                                    <th scope="row"><a
                                                            href={{ URL::route($rotaAlterar, ['id' => $pedido->id]) }}>{{ $pedido->id }}</a>
                                                    </th>
                                                    <td>{{ $pedido->os }}</td>
                                                    <td>{{ $pedido->ep }}</td>
                                                    <td>{{ $pedido->qtde }}</td>
                                                    <td>{{ $pedido->nome_cliente }}</td>
                                                    <td>
                                                        <select class="form-control alteracao_status_pedido"
                                                            data-statusatual='{{ $pedido->id_status }}'
                                                            data-pedido="{{ $pedido->id }}" id="status_id" name="status_id">
                                                            @if (isset($AllStatus))
                                                                @foreach ($AllStatus as $stats)
                                                                    <option value="{{ $stats->id }}"
                                                                        @if ($stats->id < $pedido->id_status) {{ 'disabled' }} @else {{ '' }} @endif
                                                                        @if (isset($pedido->id_status) && $pedido->id_status == $stats->id) selected="selected" @else{{ '' }} @endif>
                                                                        {{ $stats->nome }}
                                                                    </option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </td>
                                                    <?php
                                                    $entrega = \Carbon\Carbon::createFromDate($pedido->data_entrega)->format('Y-m-d');
                                                    $hoje = date('Y-m-d');
                                                    $dias_alerta = \Carbon\Carbon::createFromDate($hoje)->diffInDays($entrega, false);
                                                    if ($dias_alerta < 6) {
                                                        $class_dias_alerta = 'text-danger';
                                                    } else {
                                                        $class_dias_alerta = 'text-primary';
                                                    }
                                                    ?>
                                                    <td>{{ Carbon\Carbon::parse($pedido->data_gerado)->format('d/m/Y') }}</td>
                                                    <td>{{ Carbon\Carbon::parse($pedido->data_entrega)->format('d/m/Y') }}</td>
                                                    <td class="{{ $class_dias_alerta }}">{{ $dias_alerta }}</td>
                                                    <th scope="row" title="Imprimir ordem de serviço">
                                                        <a target="_blank"
                                                            href="{{ URL::route('imprimirOS', ['id' => $pedido->id]) }}" <span
                                                            class="fa fa-print"></span></a>
                                                    </th>
                                                    <th scope="row">
                                                        <a href="{{ URL::route('imprimirMP', ['id' => $pedido->id]) }}" <span
                                                            class="fa fa-print"></span></a>
                                                    </th>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @stop
    @break

    @case('alterar')
    @case('incluir')
        @section('content')
            @if ($tela == 'alterar')
                @section('content_header')
                    <h1 class="m-0 text-dark">Alteração de {{ $nome_tela }}</h1>
                @stop
                <form id="alterar" action="{{ $rotaAlterar }}" data-parsley-validate=""
                    class="form-horizontal form-label-left" method="post">
                    <div class="form-group row">
                        <label for="codigo" class="col-sm-2 col-form-label">Id</label>
                        <div class="col-sm-2">
                            <input type="text" id="id" name="id" class="form-control col-md-7 col-xs-12"
                                readonly="true"
                                value="@if (isset($pedidos[0]->id)) {{ $pedidos[0]->id }}@else{{ '' }} @endif">
                        </div>
                    </div>
                @else
                    @section('content_header')
                        <h1 class="m-0 text-dark">Cadastro de {{ $nome_tela }}</h1>
                    @stop
                    <form id="incluir" action="{{ $rotaIncluir }}" data-parsley-validate=""
                        class="form-horizontal form-label-left" method="post">
            @endif
            @csrf <!--{{ csrf_field() }}-->
            <div class="form-group row">
                <label for="os" class="col-sm-2 col-form-label">OS</label>
                <div class="col-sm-1">
                    <input type="text" class="form-control" id="os" name="os"
                        value="@if (isset($pedidos[0]->os)) {{ $pedidos[0]->os }}@else{{ '' }} @endif">
                </div>
            </div>
            <div class="form-group row">
                <label for="fichatecnica" class="col-sm-2 col-form-label">Produto (Ficha técnica)</label>
                <div class="col-sm-2">
                    <select class="form-control  is-invalid" id="fichatecnica" required name="fichatecnica">
                        <option value=""></option>
                        @if (isset($fichastecnicas))
                            @foreach ($fichastecnicas as $fichatecnica)
                                <option value="{{ $fichatecnica->id }}"
                                    @if (isset($pedidos[0]->fichatecnica_id) && $pedidos[0]->fichatecnica_id == $fichatecnica->id) selected="selected" @else{{ '' }} @endif>
                                    {{ $fichatecnica->ep }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="clientes_id" class="col-sm-2 col-form-label">Cliente</label>
                <div class="col-sm-4">
                    <select class="form-control" id="clientes_id" name="clientes_id">
                        <option value=""></option>
                        @if (isset($clientes))
                            @foreach ($clientes as $clientes)
                                <option value="{{ $clientes->id }}"
                                    @if (isset($pedidos[0]->pessoas_id) && $pedidos[0]->pessoas_id == $clientes->id) selected="selected" @else{{ '' }} @endif>
                                    {{ $clientes->codigo_cliente . ' - ' . $clientes->nome_cliente }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="transporte_id" class="col-sm-2 col-form-label">Trasporte</label>
                <div class="col-sm-4">
                    <select class="form-control" id="transporte_id" name="transporte_id">
                        <option value=""></option>
                        @if (isset($transportes))
                            @foreach ($transportes as $transporte)
                                <option value="{{ $transporte->id }}"
                                    @if (isset($pedidos[0]->transporte_id) && $pedidos[0]->transporte_id == $transporte->id) selected="selected" @else{{ '' }} @endif>
                                    {{ $transporte->nome }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="prioridade_id" class="col-sm-2 col-form-label">Prioridade</label>
                <div class="col-sm-2">
                    <select class="form-control" id="prioridade_id" name="prioridade_id">
                        <option value=""></option>
                        @if (isset($prioridades))
                            @foreach ($prioridades as $prioridade)
                                <option value="{{ $prioridade->id }}"
                                    @if (isset($pedidos[0]->prioridade_id) && $pedidos[0]->prioridade_id == $prioridade->id) selected="selected" @else{{ '' }} @endif>
                                    {{ $prioridade->nome }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label for="qtde" class="col-sm-2 col-form-label">Qtde</label>
                <div class="col-sm-1">
                    <input type="text" class="form-control sonumeros" id="qtde" name="qtde"
                        value="@if (isset($pedidos[0]->qtde)) {{ $pedidos[0]->qtde }}@else{{ '' }} @endif">
                </div>
            </div>
            <div class="form-group row">
                <label for="data_gerado" class="col-sm-2 col-form-label">Data gerado</label>
                <div class="col-sm-2">
                    <input type="text" class="form-control mask_date" id="data_gerado" name="data_gerado"
                    @if ($tela == 'alterar') readonly='readonly' @else {{''}} @endif
                        value="@if (isset($pedidos[0]->data_gerado)) {{ Carbon\Carbon::parse($pedidos[0]->data_gerado)->format('d/m/Y') }}@else{{ Carbon\Carbon::now()->format('d/m/Y') }} @endif">
                </div>
            </div>
            <div class="form-group row">
                <label for="data_entrega" class="col-sm-2 col-form-label">Data entrega</label>
                <div class="col-sm-2">
                    <input type="text" class="form-control mask_date" id="data_entrega" name="data_entrega"
                        value="@if (isset($pedidos[0]->data_entrega)) {{ Carbon\Carbon::parse($pedidos[0]->data_entrega)->format('d/m/Y') }} @else {{ '' }} @endif">
                </div>
            </div>
            <div class="form-group row">
                <label for="status_id" class="col-sm-2 col-form-label">Status do pedido</label>
                <div class="col-sm-4">
                    <select class="form-control" id="status_id" name="status_id">
                        <option value=""></option>
                        @if (isset($status))
                            @foreach ($status as $stats)
                                <option value="{{ $stats->id }}"
                                    @if ((isset($pedidos[0]->status_id) && $pedidos[0]->status_id == $stats->id) || ($tela == 'incluir' && $stats->id == 1)) selected="selected" @else{{ '' }} @endif>
                                    {{ $stats->nome }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="observacao" class="col-sm-2 col-form-label">Observações</label>
                <div class="col-sm-6">
                    <textarea class="form-control" id="observacao" name="observacao">
@if (isset($pedidos[0]->observacao))
{{ trim($pedidos[0]->observacao) }}@else{{ '' }}
@endif
</textarea>
                </div>
            </div>
            @if (!empty($historicos))
                <div class="form-group row">
                    <label for="observacao" class="col-sm-2 col-form-label">Histórico</label>
                    <div class="col-sm-8">
                        <div class="d-flex p-2 bd-highlight overflow-auto">
                            @foreach ($historicos as $historico)
                                {{ '[' . \Carbon\Carbon::parse($historico->created_at)->format('d/m/Y h:i:s') . '] ' . $historico->historico }}</br>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
            <div class="form-group row">
                <label for="status" class="col-sm-2 col-form-label"></label>
                <select class="form-control col-md-1" id="status" name="status">
                    <option value="A" @if (isset($pedidos[0]->status) && $pedidos[0]->status == 'A') {{ ' selected ' }}@else @endif>Ativo</option>
                    <option value="I" @if (isset($pedidos[0]->status) && $pedidos[0]->status == 'I') {{ ' selected ' }}@else @endif>Inativo</option>
                </select>
            </div>
            <div class="form-group row">
                <div class="col-sm-5">
                    <button class="btn btn-danger" onclick="window.history.back();" type="button">Cancelar</button>
                </div>
                <div class="col-sm-5">
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </div>
            </form>

        @stop
    @break

    @case('pesquisa-followup')
        @section('content_header')
            <div class="form-group row">
                <h1 class="m-0 text-dark col-sm-11 col-form-label">Pesquisa de {{ $nome_tela }}</h1>
                <div class="col-sm-1">
                    @include('layouts.nav-open-incluir', ['rotaIncluir => $rotaIncluir'])
                </div>
            </div>
        @stop
        @section('content')
            <div class="right_col" role="main">

                <form id="filtro" action="followup" method="get" data-parsley-validate=""
                    class="form-horizontal form-label-left" novalidate="">
                    <div class="form-group row">
                        <label for="os" class="col-sm-1 col-form-label text-right">OS</label>
                        <div class="col-sm-1">
                            <input type="text" id="os" name="os" class="form-control col-md-13" value="">
                        </div>

                        <label for="ep" class="col-sm-1 col-form-label text-right">EP</label>
                        <div class="col-sm-1">
                            <input type="text" id="ep" name="ep" class="form-control col-md-13" value="">
                        </div>
                        <label for="ep" class="col-sm-2 col-form-label text-right">Status do pedido</label>
                        <div class="col-sm-5" style="overflow-y: auto; height: 75px; border:1px solid #97928b">
                            <div class="right_col col-sm-6" role="main">
                                    @foreach ($status as $status)
                                        <div class="col-sm-6 form-check">
                                            <input class="form-check-input col-sm-4"  name="status_id[]" type="checkbox"
                                            @if($status->id == 11) {{''}} @else {{ 'checked'}}@endif value="{{$status->id}}" id="status_id">
                                            <label class="fform-check-label col-sm-6" style="white-space:nowrap" for="status_id">{{$status->nome}}</label>
                                        </div>
                                    @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="data_gerado" class="col-sm-1 col-form-label text-right">Data pedido: de</label>
                        <div class="col-sm-1">
                            <input type="text" class="form-control mask_date" id="data_gerado" name="data_gerado"
                                placeholder="DD/MM/AAAA">
                        </div>
                        <label for="data_gerado_fim" class=" col-form-label text-right">até</label>
                        <div class="col-sm-1">
                            <input type="text" class="form-control mask_date" id="data_gerado_fim" name="data_gerado_fim"
                                placeholder="DD/MM/AAAA">
                        </div>
                        <label for="data_entrega" class="col-sm-2 col-form-label text-right">Data entrega: de</label>
                        <div class="col-sm-1">
                            <input type="text" class="form-control mask_date" id="data_entrega" name="data_entrega"
                                placeholder="DD/MM/AAAA">
                        </div>
                        <label for="data_entrega_fim" class=" col-form-label text-right">até</label>
                        <div class="col-sm-1">
                            <input type="text" class="form-control mask_date" id="data_entrega_fim" name="data_entrega_fim"
                                placeholder="DD/MM/AAAA">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-5">
                            <button type="submit" class="btn btn-primary">Pesquisar</button>
                        </div>
                        <div class="col-sm-5">
                        </div>
                    </div>
                </form>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for=""></label>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="x_panel">
                            <div class="x_title">
                                @if (!empty($pedidos_encontrados))
                                    <h4>Encontrados {{ count($pedidos_encontrados) }} ordens de serviço</h4>
                                    <div class="form-group row">
                                        <div class="col-sm-5">
                                            <form id="filtro" action="followup-detalhes" method="post"
                                                data-parsley-validate="" class="form-horizontal form-label-left" novalidate="">
                                                @csrf <!--{{ csrf_field() }}-->
                                                <input type="hidden" id="pedidos_encontrados" name="pedidos_encontrados"
                                                    value="{{ json_encode($pedidos_encontrados) }}">
                                                <input type="hidden" id="" name="nome_tela"
                                                    value="{{ 'tempos' }}">
                                                <button type="submit" class="btn btn-primary"><span
                                                        class="far fa-fw fa-calendar"></span> Visualizar followups tempos</button>
                                                <div class="clearfix"></div>
                                            </form>
                                        </div>
                                        <div class="col-sm-5">
                                            <form id="filtro" action="followup-detalhes" method="post"
                                                data-parsley-validate="" class="form-horizontal form-label-left" novalidate="">
                                                @csrf <!--{{ csrf_field() }}-->
                                                <input type="hidden" id="pedidos_encontrados" name="pedidos_encontrados"
                                                    value="{{ json_encode($pedidos_encontrados) }}">
                                                <input type="hidden" id="" name="nome_tela"
                                                    value="{{ 'geral' }}">
                                                <button type="submit" class="btn btn-primary"><span
                                                        class="far fa-fw fa-calendar"></span> Visualizar followups geral</button>
                                                <div class="clearfix"></div>
                                            </form>
                                        </div>
                                    </div>
                                @else
                                    <h4>Nenhum registro encontrado</h4>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @stop
    @break

    @case('followup-detalhes')
        @section('content_header')
            <div class="form-group row">
                <h1 class="m-0 text-dark col-sm-6 col-form-label">Tela de Followup Tempos</h1>
            </div>
        @stop
        @section('content')
            @if (isset($dados_pedido_status))
                @foreach ($dados_pedido_status as $key => $dado_pedido_status)
                    <label for="codigo" class="col-sm-10 col-form-label">Status do Pedido: {{ Str::upper($key) }} </label>
                    <div class="form-group row">
                        <table class="table table-sm table-striped text-center" id="table_composicao">
                            <thead>
                                <tr style="background-color: {{ $palheta_cores[$dado_pedido_status['id_status'][0]] }}">
                                    <th scope="col">OS</th>
                                    <th scope="col">EP</th>
                                    <th scope="col">Qtde</th>
                                    <th scope="col">Usinagem</th>
                                    <th scope="col">Acabamento</th>
                                    <th scope="col">Montagem Torre</th>
                                    <th scope="col">Montagem</th>
                                    <th scope="col">Inspeção</th>
                                    <th scope="col">Data entrega</th>
                                    <th scope="col">Alerta de dias</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($dado_pedido_status['classe'] as $pedido)
                                    <?php
                                    $entrega = \Carbon\Carbon::createFromDate($pedido->data_entrega)->format('Y-m-d');
                                    $hoje = date('Y-m-d');
                                    $dias_alerta = \Carbon\Carbon::createFromDate($hoje)->diffInDays($entrega, false);
                                    if ($dias_alerta < 6) {
                                        $class_dias_alerta = 'text-danger';
                                    } else {
                                        $class_dias_alerta = 'text-primary';
                                    }
                                    ?>
                                    <tr>
                                        <td>{{ $pedido->os }}
                                        <td>{{ $pedido->tabelaFichastecnicas->ep }}</td>
                                        <td>{{ $pedido->qtde }}</td>
                                        <td>{{ PedidosController::formatarHoraMinuto($dado_pedido_status['pedido'][$pedido->id]['usinagem']) }}
                                        </td>
                                        <td>{{ PedidosController::formatarHoraMinuto($dado_pedido_status['pedido'][$pedido->id]['acabamento']) }}
                                        </td>
                                        <td>{{ PedidosController::formatarHoraMinuto($dado_pedido_status['pedido'][$pedido->id]['montagem_torre']) }}
                                        </td>
                                        <td>{{ PedidosController::formatarHoraMinuto($dado_pedido_status['pedido'][$pedido->id]['montagem']) }}
                                        </td>
                                        <td>{{ PedidosController::formatarHoraMinuto($dado_pedido_status['pedido'][$pedido->id]['inspecao']) }}
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($pedido->data_entrega)->format('d/m/Y') }}</td>
                                        <td class="{{ $class_dias_alerta }}">{{ $dias_alerta }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col">
                                        {{ PedidosController::formatarHoraMinuto($dado_pedido_status['totais']['total_tempo_usinagem']) . ' horas' }}
                                    </th>
                                    <th scope="col">
                                        {{ PedidosController::formatarHoraMinuto($dado_pedido_status['totais']['total_tempo_acabamento']) . ' horas' }}
                                    </th>
                                    <th scope="col">
                                        {{ PedidosController::formatarHoraMinuto($dado_pedido_status['totais']['total_tempo_montagem_torre']) . ' horas' }}
                                    </th>
                                    <th scope="col">
                                        {{ PedidosController::formatarHoraMinuto($dado_pedido_status['totais']['total_tempo_montagem']) . ' horas' }}
                                    </th>
                                    <th scope="col">
                                        {{ PedidosController::formatarHoraMinuto($dado_pedido_status['totais']['total_tempo_inspecao']) . ' horas' }}
                                    </th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                </tr>
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col">{{ $dado_pedido_status['maquinas_usinagens'] }}</th>
                                    <th scope="col">{{ $dado_pedido_status['pessoas_acabamento'] }}</th>
                                    <th scope="col">{{ $dado_pedido_status['pessoas_montagem_torre'] }}</th>
                                    <th scope="col">{{ $dado_pedido_status['pessoas_montagem'] }}</th>
                                    <th scope="col">{{ $dado_pedido_status['pessoas_inspecao'] }}</th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                </tr>
                            </tbody>
                        </table>
                    </div>


                    <hr class="my-4">
                @endforeach
                <div class="form-group row">
                    <table class="table table-sm table-striped text-center" id="table_composicao">
                        <thead>
                            <tr style="background-color: #463a2a; color: #FFFFFF">
                                <th scope="col"></th>
                                <th scope="col"></th>
                                <th scope="col"></th>
                                <th scope="col">Usinagem</th>
                                <th scope="col">Acabamento</th>
                                <th scope="col">Montagem Torre</th>
                                <th scope="col">Montagem</th>
                                <th scope="col">Inspeção</th>
                                <th scope="col"></th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="">
                                <th scope="col" colspan="2">Total geral</th>
                                <th scope="col">&nbsp;</th>
                                <th scope="col">
                                    {{ !empty($totalGeral['totalGeralusinagens']) ? $totalGeral['totalGeralusinagens'] . ' dias' : '0' }}
                                </th>
                                <th scope="col">
                                    {{ !empty($totalGeral['totalGeralacabamento']) ? $totalGeral['totalGeralacabamento'] . ' dias' : '0 dias' }}
                                </th>
                                <th scope="col">
                                    {{ !empty($totalGeral['totalGeralmontagem_torre']) ? $totalGeral['totalGeralmontagem_torre'] . ' dias' : '0 dias' }}
                                </th>
                                <th scope="col">
                                    {{ !empty($totalGeral['totalGeralmontagem']) ? $totalGeral['totalGeralmontagem'] . ' dias' : '0 dias' }}
                                </th>
                                <th scope="col">
                                    {{ !empty($totalGeral['totalGeralinspecao']) ? $totalGeral['totalGeralinspecao'] . ' dias' : '0 dias' }}
                                </th>
                                <th scope="col"></th>
                                <th scope="col"></th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @endif
            </div>

        @stop
    @break

    @case('followup-detalhes-geral')
        @section('content_header')
            <div class="form-group row">
                <h1 class="m-0 text-dark col-sm-6 col-form-label">Tela de Followup Geral</h1>
            </div>
        @stop
        @section('content')
            @if (isset($dados_pedido_status))
                @foreach ($dados_pedido_status as $key => $dado_pedido_status)
                    <label for="codigo" class="col-sm-10 col-form-label">Status do Pedido: {{ Str::upper($key) }} </label>
                    <div class="form-group row" style="overflow-x:auto;  ">
                        <table class="table table-sm table-striped text-center" id="table_composicao">
                            <thead style="background-color: {{ $palheta_cores[$dado_pedido_status['id_status'][0]] }}">
                                <tr>
                                    <th scope="col" title="Código do cliente">Cliente</th>
                                    <th scope="col">Assistente</th>
                                    <th scope="col">EP</th>
                                    <th scope="col">OS</th>
                                    <th scope="col">Qtde</th>
                                    <th scope="col" title="Data do pedido">Data</th>
                                    <th scope="col" title="Data da entrega">Entrega</th>
                                    <th scope="col" title="Alerta de dias">Alerta</th>
                                    <th scope="col">Prioridade</th>
                                    <th scope="col" title="Observações">Obs</th>
                                    <th scope="col">Transporte</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Usinagem</th>
                                    <th scope="col">Acabamento</th>
                                    <th scope="col">Montagem</th>
                                    <th scope="col">Inspeção</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($dado_pedido_status['classe'] as $pedido)
                                    <?php
                                    $entrega = \Carbon\Carbon::createFromDate($pedido->data_entrega)->format('Y-m-d');
                                    $hoje = date('Y-m-d');
                                    $dias_alerta = \Carbon\Carbon::createFromDate($hoje)->diffInDays($entrega, false);
                                    if ($dias_alerta < 6) {
                                        $class_dias_alerta = 'text-danger';
                                    } else {
                                        $class_dias_alerta = 'text-primary';
                                    }
                                    ?>
                                    <tr>
                                        <td>{{ $pedido->tabelaPessoas->codigo_cliente }}</td>
                                        <td>{{ $pedido->tabelaPessoas->nome_assistente }}</td>
                                        <td>{{ $pedido->tabelaFichastecnicas->ep }}</td>
                                        <td>{{ $pedido->os }}</td>
                                        <td>{{ $pedido->qtde }}</td>
                                        <td>{{ \Carbon\Carbon::parse($pedido->data_gerado)->format('d/m/Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($pedido->data_entrega)->format('d/m/Y') }}</td>
                                        <td class="{{ $class_dias_alerta }}">{{ $dias_alerta }}</td>
                                        <td>{{ $pedido->tabelaPrioridades->nome }}</td>
                                        <td title="{{ $pedido->observacao }}">{!! Str::words($pedido->observacao, 1, '...') !!}</td>
                                        <td>{{ $pedido->tabelaTransportes->nome }}</td>
                                        <td>{{ $pedido->tabelaStatus->nome }}</td>
                                        <td>{{ PedidosController::formatarHoraMinuto($dado_pedido_status['pedido'][$pedido->id]['usinagem']) }}
                                        </td>
                                        <td>{{ PedidosController::formatarHoraMinuto($dado_pedido_status['pedido'][$pedido->id]['acabamento']) }}
                                        </td>
                                        <td>{{ PedidosController::formatarHoraMinuto($dado_pedido_status['pedido'][$pedido->id]['montagem']) }}
                                        </td>
                                        <td>{{ PedidosController::formatarHoraMinuto($dado_pedido_status['pedido'][$pedido->id]['inspecao']) }}
                                        </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col">
                                        {{ PedidosController::formatarHoraMinuto($dado_pedido_status['totais']['total_tempo_usinagem']) . ' horas' }}
                                    </th>
                                    <th scope="col">
                                        {{ PedidosController::formatarHoraMinuto($dado_pedido_status['totais']['total_tempo_acabamento']) . ' horas' }}
                                    </th>
                                    <th scope="col">
                                        {{ PedidosController::formatarHoraMinuto($dado_pedido_status['totais']['total_tempo_montagem']) . ' horas' }}
                                    </th>
                                    <th scope="col">
                                        {{ PedidosController::formatarHoraMinuto($dado_pedido_status['totais']['total_tempo_inspecao']) . ' horas' }}
                                    </th>
                                </tr>
                                <tr>
                                    <th scope="col" colspan="2"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col">{!! Str::words($dado_pedido_status['maquinas_usinagens'], 2, '') !!}</th>
                                    <th scope="col">{!! Str::words($dado_pedido_status['pessoas_acabamento'], 2, '') !!}</th>
                                    <th scope="col">{!! Str::words($dado_pedido_status['pessoas_montagem'], 2, '') !!}</th>
                                    <th scope="col">{!! Str::words($dado_pedido_status['pessoas_inspecao'], 2, '') !!}</th>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <hr class="my-4">
                @endforeach
            @endif
            </div>

        @stop
    @break

    @case('alerta-pedidos')
        @section('content_header')
            <div class="form-group row">
                <h1 class="m-0 text-dark col-sm-6 col-form-label">Tela de envio de alertas ao cliente</h1>
            </div>
        @stop
        @section('content')
            @if (isset($pedidos))
                <form id="filtro" action="alertas-pedidos" method="post" data-parsley-validate=""
                    class="form-horizontal form-label-left">
                    @csrf <!--{{ csrf_field() }}-->
                    <table class="table table-sm table-striped  text-center">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col" title="Código do cliente">Cliente</th>
                                <th scope="col">Responsável</th>
                                <th scope="col">OS</th>
                                <th scope="col">Status do pedido</th>
                                <th scope="col" title="Data do status">Data Status</th>
                                <th scope="col" title="Data da entrega">Data Entrega</th>
                                <th scope="col" title="Alerta de dias">Alerta</th>
                                <th scope="col">Email</th>
                                <th scope="col">Enviar</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($pedidos as $pedido)
                                <?php
                                $entrega = \Carbon\Carbon::createFromDate($pedido->data_entrega)->format('Y-m-d');
                                $hoje = date('Y-m-d');
                                $dias_alerta = \Carbon\Carbon::createFromDate($hoje)->diffInDays($entrega, false);
                                if ($dias_alerta < 6) {
                                    $class_dias_alerta = 'text-danger';
                                } else {
                                    $class_dias_alerta = 'text-primary';
                                }
                                ?>
                                <tr>
                                    <td>{{ $pedido->nome_cliente }}</td>
                                    <td>{{ $pedido->nome_contato }}</td>
                                    <td>{{ $pedido->os }}</td>
                                    <td>{{ $pedido->nome_status }}</td>
                                    <td>{{ \Carbon\Carbon::parse($pedido->data_ultimo_historico)->format('d/m/Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($pedido->data_entrega)->format('d/m/Y') }}</td>
                                    <td class="{{ $class_dias_alerta }}">{{ $dias_alerta }}</td>
                                    <td>{{ $pedido->email }}</td>
                                    <td>
                                        <input type="hidden" name="emails[]" value="{{ $pedido->id }}">
                                        <input type="checkbox" class="" checked value="{{ $pedido->id }}"
                                            id="enviar" name="enviar[]">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-5">
                            <button class="btn btn-danger" onclick="window.history.back();" type="button">Cancelar</button>
                        </div>
                        <div class="col-sm-5">
                            <button type="submit" class="btn btn-primary">Enviar Email</button>
                        </div>
                    </div>
                </form>
            @else
                Nenhum alerta pendente de envio!
            @endif
        @stop
    @break

@endswitch
