<?php
use App\Http\Controllers\PedidosController;
use App\Http\Controllers\AjaxOrcamentosController;
use App\Providers\DateHelpers;

$palheta_cores = [1 => '#ff003d', 2 => '#ee7e4c', 3 => '#8f639f', 4 => '#94c5a5', 5 => '#ead56c', 6 => '#0fbab7', 7 => '#f7c41f', 8 => '#898b75', 9 => '#c1d9d0', 10 => '#da8f72', 11 => '#00caf8', 12 => '#ffe792', 13 => '#9a5071'];
?>
@extends('adminlte::page')

@section('title', 'Pro Effect')
<script src="../vendor/jquery/jquery.min.js?cache={{time()}}"></script>
<script src="js/bootstrap.4.6.2.js?cache={{time()}}"></script>
<script src="js/main_custom.js?cache={{time()}}"></script>
<script src="js/jquery.mask.js?cache={{time()}}"></script>
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
            <div id='modal_funcionarios'  class="modal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content" style="width: 700px">
                        <div class="modal-header">
                        <h5 class="modal-title" id='texto_status_caixas'>Funcionários Montagens</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        </div>

                        <div class="modal-body" >
                            @if (isset($funcionarios))
                            @foreach ($funcionarios as $funcionario)
                                <div class="form-check">
                                    <input class="form-check-input montadores" type="checkbox" value="{{$funcionario->id}}" id="montador{{$funcionario->id}}">
                                    <label class="form-check-label" for="montador{{$funcionario->id}}">{{$funcionario->nome}}</label>
                              </div>
                            @endforeach
                        @endif
                        <input type="hidden" name="funcionarios_selecionados" id="funcionarios_selecionados" value=""/>
                        <input type="hidden" name="pedido_montagem" id="pedido_montagem" value=""/>
                        </div>
                        <div class="modal-footer">
                        <button type="button" class="btn btn-success" id="adicionar_montador" data-dismiss="modal" >Adicionar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div id='modal_caixas'  class="modal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content" style="width: 700px">
                        <div class="modal-header">
                        <h5 class="modal-title" id='texto_status_caixas'>Caixas</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        </div>

                        <div class="modal-body" >
                            <table class="table table-striped  text-center" id='tabela_caixas'>
                                <thead>
                                    <th>Caixa</th>
                                    <th>A</th>
                                    <th>L</th>
                                    <th>C</th>
                                    <th>Qtde</th>
                                    <th>Peso</th>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>

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

            <form id="filtro" action="pedidos" method="get" data-parsley-validate=""  novalidate="">
                <div class="row" role="main">
                    <div class="col-md-9 themed-grid-col row">
                        <div class="row">
                            <label for="codigo_cliente" class="col-sm-2 col-form-label text-right">Código cliente</label>
                            <div class="col-sm-1">
                                <input type="text" id="codigo_cliente" name="codigo_cliente" class="form-control col-md-13"
                                    value="">
                            </div>
                            <label for="nome_cliente" class="col-sm-2 col-form-label text-right">Nome cliente</label>
                            <div class="col-sm-3">
                                <input type="text" id="nome_cliente" name="nome_cliente" class="form-control col-md-13"
                                    value="">
                            </div>
                            <label for="os" class="col-sm-1 col-form-label text-right">OS</label>
                            <div class="col-sm-2">
                                <input type="text" id="os" name="os" class="form-control" value="">
                            </div>
                        </div>
                        <div class="row">
                            <label for="ep" class="col-sm-1 col-form-label text-right">EP</label>
                            <div class="col-sm-1">
                                <input type="text" id="ep" name="ep" class="form-control col-md-13" value="">
                            </div>
                            <label for="data_entrega" class="col-sm-2 col-form-label text-right">Data entrega: de</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control mask_date" id="data_entrega" name="data_entrega"
                                    placeholder="DD/MM/AAAA">
                            </div>
                            <label for="data_entrega_fim" class="col-form-label text-right">até</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control mask_date" id="data_entrega_fim" name="data_entrega_fim"
                                    placeholder="DD/MM/AAAA">
                            </div>
                            <label for="status" class="col-sm-1 col-form-label">&nbsp;</label>
                            <div class="col-sm-2">
                                <select class="form-control " id="status" name="status">
                                    <option value="A" @if (isset($request) && $request->input('status') == 'A') {{ ' selected ' }}@else @endif>Ativo
                                    </option>
                                    <option value="I" @if (isset($request) && $request->input('status') == 'I') {{ ' selected ' }}@else @endif>Inativo
                                    </option>
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="col-md-3 themed-grid-col row">
                        <label for="ep" class="col-sm-4 col-form-label text-right">Status do pedido</label>
                            <div class="col-sm-7" style="overflow-y: auto; height: 175px; border:1px solid #ced4da; border-radius: .25rem;">
                                @foreach ($AllStatus as $status)
                                    <div class="col-sm-8 form-check">
                                        <input class="form-check-input col-sm-4"  name="status_id[]" id="{{$status->id}}" type="checkbox"
                                        @if($status->id == 11 || $status->id == 12 || $status->id == 13) {{''}} @else {{ 'checked'}}@endif value="{{$status->id}}">
                                        <label class="form-check-label col-sm-6" style="white-space:nowrap" for="{{$status->id}}">{{$status->nome}}</label>
                                    </div>
                                @endforeach
                            </div>
                    </div>

                    <div class="col-md-8 form-group row">
                        <div class="col-sm-5">
                            <button type="submit" class="btn btn-primary">Pesquisar</button>
                        </div>
                        <div class="col-sm-5">
                            <div class="overlay" style="display: none;">
                                <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                            </div>
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
                                        <th>Status do pedido</th>
                                        <th>Data gerado</th>
                                        <th>Montadores</th>
                                        <th>Data entrega</th>
                                        <th>Data antecipação</th>
                                        <th>Hora retirada</th>
                                        <th>Alerta dias</th>
                                        <th>OS</th>
                                        <th>MP</th>
                                        <th>CX</th>
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
                                                <td>
                                                    <i data-funcionariomontagem="@foreach($funcionarios_vinculados[$pedido->id]['funcionarios_montagens'] as $funcionario_montagem){{$funcionario_montagem->id.','}}@endforeach"
                                                        title="@foreach($funcionarios_vinculados[$pedido->id]['funcionarios_montagens'] as $funcionario_montagem){{$funcionario_montagem->nome.', '}}@endforeach"
                                                        data-pedido_id="{{$pedido->id}}"
                                                        style="cursor:pointer; @if (count($funcionarios_vinculados[$pedido->id]['funcionarios_montagens']) > 0) {{'color:#044f04'}}@else {{'color:#cacaca'}}@endif"  class="fas fa-users add_funcionarios_montagens">
                                                    </i>
                                                    {{ count($funcionarios_vinculados[$pedido->id]['funcionarios_montagens']) }}
                                                </td>
                                                <td>{{ Carbon\Carbon::parse($pedido->data_entrega)->format('d/m/Y') }}</td>
                                                <td style="@if(!empty($pedido->data_antecipacao)) {{'background-color: red; font-weight: bold'}} @endif ">{{ Carbon\Carbon::parse($pedido->data_antecipacao)->format('d/m/Y') }}</td>
                                                <td style="@if(!empty($pedido->hora_antecipacao)) {{'background-color: red; font-weight: bold'}} @endif ">{{ $pedido->hora_antecipacao }}</td>
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
                                                <th scope="row">
                                                    <a class="show_caixas" data-pedido_id="{{$pedido->id}}">
                                                        <i style="cursor:pointer;@if($pedido->caixas==0){{'color:#cacaca'}}@else{{'color:#044f04'}}@endif" class="fas fa-eye"></i>
                                                    </a>
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
                <label for="data_antecipacao" class="col-sm-2 col-form-label">Data antecipação</label>
                <div class="col-sm-2">
                    <input type="text" class="form-control mask_date" id="data_antecipacao" name="data_antecipacao"
                        value="@if (isset($pedidos[0]->data_antecipacao)) {{ Carbon\Carbon::parse($pedidos[0]->data_antecipacao)->format('d/m/Y') }} @else {{ '' }} @endif">
                </div>
            </div>
            <div class="form-group row">
                <label for="hora_antecipacao" class="col-sm-2 col-form-label">Hora retirada</label>
                <div class="col-sm-2">
                    <input type="text" class="form-control mask_horas" id="hora_antecipacao" name="hora_antecipacao"
                        value="{{$pedidos[0]->hora_antecipacao}}">
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
                                {{ '[' . \Carbon\Carbon::parse($historico->created_at)->format('d/m/Y H:i:s') . '] ' . $historico->historico }}</br>
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

        <form id="filtro" action="followup" method="get" data-parsley-validate="" class="form-horizontal form-label-left" novalidate="">


            <div class="form-group row">

                <div class="col-md-9 themed-grid-col row">
                    <div class="form-group row">
                        <label for="os" class="col-sm-1 col-form-label text-right">OS</label>
                        <div class="col-sm-1">
                            <input type="text" id="os" name="os" class="form-control col-md-13" value="">
                        </div>
                        <label for="ep" class="col-sm-1 col-form-label text-right">EP</label>
                        <div class="col-sm-1">
                            <input type="text" id="ep" name="ep" class="form-control col-md-13" value="">
                        </div>
                        <label for="lote" class="col-sm-3 col-form-label text-right tipo_consulta ">Tipo de consulta</label>
                        <div class="col-sm-3">
                            <select class="form-control col-sm-12 tipo_consulta_followup" id="tipo_consulta" name="tipo_consulta">
                                <option value="F" @if($request->input('tipo_consulta') == 'F'){{ ' selected '}}@else @endif>Followup</option>
                                <option value="R" @if($request->input('tipo_consulta') == 'R'){{ ' selected '}}@else @endif>Realizado</option>
                                <option value="C" @if($request->input('tipo_consulta') == 'C'){{ ' selected '}}@else @endif>Ciclo de produção</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row campos_followup">
                        <label for="data_entrega" class="col-sm-4 col-form-label text-right ">Data entrega: de</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control mask_date" id="data_entrega" name="data_entrega"
                                placeholder="DD/MM/AAAA">
                        </div>
                        <label for="data_entrega_fim" class=" col-form-label text-right">até</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control mask_date" id="data_entrega_fim" name="data_entrega_fim"
                                placeholder="DD/MM/AAAA">
                        </div>
                    </div>
                    <div class="form-group row campos_followup">
                        <label for="data_gerado" class="col-sm-4 col-form-label text-right">Data pedido: de</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control mask_date" id="data_gerado" name="data_gerado"
                                placeholder="DD/MM/AAAA">
                        </div>
                        <label for="data_gerado_fim" class=" col-form-label text-right">até</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control mask_date" id="data_gerado_fim" name="data_gerado_fim"
                                placeholder="DD/MM/AAAA">
                        </div>
                    </div>
                    <div class="form-group row campos_ciclo_producao">
                        <label for="data_apontamento" class="col-sm-4 col-form-label text-right">Data apontamento: de</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control mask_date" id="data_apontamento" name="data_apontamento"
                                placeholder="DD/MM/AAAA">
                        </div>
                        <label for="data_apontamento_fim" class=" col-form-label text-right">até</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control mask_date" id="data_apontamento_fim" name="data_apontamento_fim"
                                placeholder="DD/MM/AAAA">
                        </div>
                    </div>
                </div>
                <div class="col-md-3 themed-grid-col row" >
                    <div class="row">
                        <label for="ep" class="col-sm-4 col-form-label text-right">Status do pedido</label>
                        <div class="col-sm-8" style="overflow-y: auto; height: 175px; border:1px solid #ced4da; border-radius: .25rem;">
                            <div class="right_col col-sm-6" role="main">
                                    @foreach ($status as $status)
                                        <div class="col-sm-6 form-check">
                                            <input class="form-check-input col-sm-4 status_pedido"  name="status_id[]" id="{{$status->id}}" type="checkbox"
                                            @if($status->id == 11 || $status->id == 12 || $status->id == 13) {{''}} @else {{' checked '}}@endif value="{{$status->id}}">
                                            <label class="form-check-label col-sm-6" style="white-space:nowrap" for="{{$status->id}}">{{$status->nome}}</label>
                                        </div>
                                    @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-5">
                        <button type="submit" class="btn btn-primary">Pesquisar</button>
                    </div>
                </div>
            </div>
            </form>

            <div class="right_col" role="main">
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for=""></label>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="x_panel">
                            <div class="x_title">
                                @if (!empty($pedidos_encontrados))
                                    <h4>Encontrados {{ count($pedidos_encontrados) }} ordens de serviço</h4>
                                    @if($request->input('tipo_consulta') == 'F')
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
                                                    <input type="hidden" id="" name="nome_tela" value="{{ 'geral' }}">
                                                    <button type="submit" class="btn btn-primary"><span
                                                            class="far fa-fw fa-calendar"></span> Visualizar followups geral</button>
                                                    <div class="clearfix"></div>
                                                </form>
                                            </div>
                                        </div>
                                        @endif
                                        @if($request->input('tipo_consulta') == 'R')
                                            <div class="col-sm-5">
                                                <form id="filtro" action="followup-realizado" method="post"
                                                    data-parsley-validate="" class="form-horizontal form-label-left" novalidate="">
                                                    @csrf <!--{{ csrf_field() }}-->
                                                    <input type="hidden" id="pedidos_encontrados" name="pedidos_encontrados"
                                                        value="{{ json_encode($pedidos_encontrados) }}">
                                                    <input type="hidden" id="" name="nome_tela" value="{{ 'realizados' }}">
                                                    <input type="hidden" id="data_inicio" name="data_apontamento" value="{{$request->input('data_apontamento')}}">
                                                    <input type="hidden" id="data_fim" name="data_apontamento_fim" value="{{$request->input('data_apontamento_fim')}}">
                                                    <input type="hidden" id="status_id" name="status_id" value="{{ json_encode($request->input('status_id')) }}">
                                                    <button type="submit" class="btn btn-primary"><span
                                                            class="far fa-fw fa-calendar"></span> Visualizar followups realizados</button>
                                                    <div class="clearfix"></div>
                                                </form>
                                            </div>
                                        @endif
                                        @if($request->input('tipo_consulta') == 'C')
                                            <div class="col-sm-5">
                                                <form id="filtro" action="followup-ciclo-producao" method="post"
                                                    data-parsley-validate="" class="form-horizontal form-label-left" novalidate="">
                                                    @csrf <!--{{ csrf_field() }}-->
                                                    <input type="hidden" id="pedidos_encontrados" name="pedidos_encontrados"
                                                        value="{{ json_encode($pedidos_encontrados) }}">
                                                    <input type="hidden" id="" name="nome_tela" value="{{ 'ciclo_producao' }}">
                                                    <button type="submit" class="btn btn-primary"><span
                                                            class="far fa-fw fa-calendar"></span> Visualizar followups ciclo de produção</button>
                                                    <div class="clearfix"></div>
                                                </form>
                                            </div>
                                        @endif
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
                                    <th scope="col">Obs</th>
                                    <th scope="col">Prioridade</th>
                                    <th scope="col">Data status</th>
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
                                        <td title="{{ $pedido->observacao }}">{!! Str::words($pedido->observacao, 2, '...') !!}</td>
                                        <td>@if(!empty($pedido->tabelaPrioridades->nome)){{ $pedido->tabelaPrioridades->nome }}@else{{''}}@endif</td>
                                        <td>{{ $dado_pedido_status['pedido'][$pedido->id]['data_alteracao_status'] }}</td>
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
                                    <th scope="col">Data Status</th>
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
                                        <td>@if(!empty($pedido->tabelaPrioridades->nome)){{ $pedido->tabelaPrioridades->nome }}@else{{''}}@endif</td>
                                        <td title="{{ $pedido->observacao }}">{!! Str::words($pedido->observacao, 1, '...') !!}</td>
                                        <td title="{{$pedido->tabelaTransportes->nome}}">{!! Str::words($pedido->tabelaTransportes->nome, 2, '...') !!}</td>
                                        <td>{{ $pedido->tabelaStatus->nome }}</td>
                                        <td>{{ $dado_pedido_status['pedido'][$pedido->id]['data_alteracao_status'] }}</td>
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
        @stop
    @break

    @case('pesquisa-gerencial')
        @section('content_header')
            <div class="form-group row">
                <h1 class="m-0 text-dark col-sm-11 col-form-label">Pesquisa de {{ $nome_tela }}</h1>
                <div class="col-sm-1">
                    @include('layouts.nav-open-incluir', ['rotaIncluir => $rotaIncluir'])
                </div>
            </div>
        @stop
        @section('content')

            <form id="filtro" action="followup-gerencial" method="get" data-parsley-validate="" class="form-horizontal form-label-left" novalidate="">
                <div class="form-group row">
                    <div class="col-md-9 themed-grid-col row">
                        <div class="form-group row">
                            <label for="os" class="col-sm-1 col-form-label text-right">OS</label>
                            <div class="col-sm-1">
                                <input type="text" id="os" name="os" class="form-control col-md-13" value="">
                            </div>
                            <label for="ep" class="col-sm-1 col-form-label text-right">EP</label>
                            <div class="col-sm-1">
                                <input type="text" id="ep" name="ep" class="form-control col-md-13" value="">
                            </div>
                            <label for="lote" class="col-sm-3 col-form-label text-right tipo_consulta ">Tipo de consulta</label>
                            <div class="col-sm-3">
                                <select class="form-control col-sm-12 tipo_consulta_followup" id="tipo_consulta" name="tipo_consulta">
                                    <option value="F" @if($request->input('tipo_consulta') == 'F'){{ ' selected '}}@else @endif>Followup</option>
                                    <option value="R" @if($request->input('tipo_consulta') == 'R'){{ ' selected '}}@else @endif>Realizado</option>
                                    <option value="G" @if($request->input('tipo_consulta') == 'G'){{ ' selected '}}@else @endif>Gerêncial</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row campos_followup">
                            <label for="data_entrega" class="col-sm-4 col-form-label text-right ">Data entrega: de</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control mask_date" id="data_entrega" name="data_entrega"
                                    placeholder="DD/MM/AAAA">
                            </div>
                            <label for="data_entrega_fim" class=" col-form-label text-right">até</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control mask_date" id="data_entrega_fim" name="data_entrega_fim"
                                    placeholder="DD/MM/AAAA">
                            </div>
                        </div>
                        <div class="form-group row campos_followup">
                            <label for="data_gerado" class="col-sm-4 col-form-label text-right">Data pedido: de</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control mask_date" id="data_gerado" name="data_gerado"
                                    placeholder="DD/MM/AAAA">
                            </div>
                            <label for="data_gerado_fim" class=" col-form-label text-right">até</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control mask_date" id="data_gerado_fim" name="data_gerado_fim"
                                    placeholder="DD/MM/AAAA">
                            </div>
                        </div>
                        <div class="form-group row campos_ciclo_producao">
                            <label for="data_apontamento" class="col-sm-4 col-form-label text-right">Data apontamento: de</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control mask_date" id="data_apontamento" name="data_apontamento"
                                    placeholder="DD/MM/AAAA">
                            </div>
                            <label for="data_apontamento_fim" class=" col-form-label text-right">até</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control mask_date" id="data_apontamento_fim" name="data_apontamento_fim"
                                    placeholder="DD/MM/AAAA">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 themed-grid-col row" >
                        <div class="row">
                            <label for="ep" class="col-sm-4 col-form-label text-right">Status do pedido</label>
                            <div class="col-sm-8" style="overflow-y: auto; height: 175px; border:1px solid #ced4da; border-radius: .25rem;">
                                <div class="right_col col-sm-6" role="main">
                                        @foreach ($status as $status)
                                            <div class="col-sm-6 form-check">
                                                <input class="form-check-input col-sm-4 status_pedido"  name="status_id[]" id="{{$status->id}}" type="checkbox"
                                                @if($status->id == 11 || $status->id == 12 || $status->id == 13) {{''}} @else {{' checked '}}@endif value="{{$status->id}}">
                                                <label class="form-check-label col-sm-6" style="white-space:nowrap" for="{{$status->id}}">{{$status->nome}}</label>
                                            </div>
                                        @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-5">
                            <button type="submit" class="btn btn-primary">Pesquisar</button>
                        </div>
                    </div>
                </div>
            </form>

            <div class="right_col" role="main">
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for=""></label>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="x_panel">
                            <div class="x_title">
                                @if (!empty($pedidos_encontrados))
                                    <h4>Encontrados {{ count($pedidos_encontrados) }} ordens de serviço</h4>
                                    @if($request->input('tipo_consulta') == 'G')
                                        <div class="form-group row">
                                            <div class="col-sm-5">
                                                <form id="filtro" action="followup-gerencial-dados" method="post"
                                                    data-parsley-validate="" class="form-horizontal form-label-left" novalidate="">
                                                    @csrf <!--{{ csrf_field() }}-->
                                                    <input type="hidden" id="pedidos_encontrados" name="pedidos_encontrados"
                                                        value="{{ json_encode($pedidos_encontrados) }}">
                                                    <input type="hidden" id="" name="nome_tela"
                                                        value="{{ 'gerencial' }}">
                                                    <button type="submit" class="btn btn-primary"><span
                                                            class="far fa-fw fa-calendar"></span> Visualizar followups Gerêncial</button>
                                                    <div class="clearfix"></div>
                                                </form>
                                            </div>
                                        </div>
                                    @endif
                                    @if($request->input('tipo_consulta') == 'F')
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
                                                    <input type="hidden" id="" name="nome_tela" value="{{ 'geral' }}">
                                                    <button type="submit" class="btn btn-primary"><span
                                                            class="far fa-fw fa-calendar"></span> Visualizar followups geral</button>
                                                    <div class="clearfix"></div>
                                                </form>
                                            </div>
                                        </div>
                                        @endif
                                        @if($request->input('tipo_consulta') == 'R')
                                            <div class="col-sm-5">
                                                <form id="filtro" action="followup-realizado" method="post"
                                                    data-parsley-validate="" class="form-horizontal form-label-left" novalidate="">
                                                    @csrf <!--{{ csrf_field() }}-->
                                                    <input type="hidden" id="pedidos_encontrados" name="pedidos_encontrados"
                                                        value="{{ json_encode($pedidos_encontrados) }}">
                                                    <input type="hidden" id="" name="nome_tela" value="{{ 'realizados' }}">
                                                    <input type="hidden" id="data_inicio" name="data_apontamento" value="{{$request->input('data_apontamento')}}">
                                                    <input type="hidden" id="data_fim" name="data_apontamento_fim" value="{{$request->input('data_apontamento_fim')}}">
                                                    <input type="hidden" id="status_id" name="status_id" value="{{ json_encode($request->input('status_id')) }}">
                                                    <button type="submit" class="btn btn-primary"><span
                                                            class="far fa-fw fa-calendar"></span> Visualizar followups realizados</button>
                                                    <div class="clearfix"></div>
                                                </form>
                                            </div>
                                        @endif
                                        @if($request->input('tipo_consulta') == 'C')
                                            <div class="col-sm-5">
                                                <form id="filtro" action="followup-ciclo-producao" method="post"
                                                    data-parsley-validate="" class="form-horizontal form-label-left" novalidate="">
                                                    @csrf <!--{{ csrf_field() }}-->
                                                    <input type="hidden" id="pedidos_encontrados" name="pedidos_encontrados"
                                                        value="{{ json_encode($pedidos_encontrados) }}">
                                                    <input type="hidden" id="" name="nome_tela" value="{{ 'ciclo_producao' }}">
                                                    <button type="submit" class="btn btn-primary"><span
                                                            class="far fa-fw fa-calendar"></span> Visualizar followups ciclo de produção</button>
                                                    <div class="clearfix"></div>
                                                </form>
                                            </div>
                                        @endif
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

    @case('followup-realizado')
        @section('content_header')
            <div class="form-group row">
                <h1 class="m-0 text-dark col-sm-6 col-form-label">Tela de Followup realizado</h1>
            </div>
        @stop
        @section('content')
            @if (isset($dados_pedido_status))
            <div class="form-group row" style="overflow-x:auto;  ">
                <table class="table table-sm table-striped text-center" id="table_composicao">
                    <thead>
                        {{-- style="background-color: {{ $palheta_cores[$dado_pedido_status['id_status'][0]] }} --}}
                        <tr>
                            <th scope="col">EP</th>
                            <th scope="col">OS</th>
                            <th scope="col">Qtde</th>
                            <th scope="col" title="Data do pedido">Data</th>
                            <th scope="col" title="Data da entrega">Entrega</th>
                            <th scope="col" style="background-color: {{ $palheta_cores[4] }}">Apontamento</th>
                            <th scope="col" style="background-color: {{ $palheta_cores[4] }}">Usinagem</th>
                            <th scope="col" style="background-color: {{ $palheta_cores[5] }}">Apontamento</th>
                            <th scope="col" style="background-color: {{ $palheta_cores[5] }}">Acabamento</th>
                            <th scope="col" style="background-color: {{ $palheta_cores[6] }}">Apontamento</th>
                            <th scope="col" style="background-color: {{ $palheta_cores[6] }}">Montagem</th>
                            <th scope="col" style="background-color: {{ $palheta_cores[7] }}">Apontamento</th>
                            <th scope="col" style="background-color: {{ $palheta_cores[7] }}">Montagem Torre</th>
                            <th scope="col" style="background-color: {{ $palheta_cores[8] }}">Apontamento</th>
                            <th scope="col" style="background-color: {{ $palheta_cores[8] }}">Inspeção</th>
                            <th scope="col" style="background-color: {{ $palheta_cores[9] }}">Embalar</th>
                            <th scope="col" style="background-color: {{ $palheta_cores[10] }}">Expedição</th>
                            <th scope="col" style="background-color: {{ $palheta_cores[11] }}">Entregue</th>

                        </tr>
                    </thead>
                    <tbody>
                        @php
                                    $totais_usinagem='00:00:00';
                                    $totais_acabamento='00:00:00';
                                    $totais_montagem='00:00:00';
                                    $totais_montagem_torre='00:00:00';
                                    $totais_inspecao='00:00:00';
                                    $totais_embalagem='00:00:00';
                                    $totais_expedicao='00:00:00';
                                    $totais_entregue='00:00:00';
                                    $totais_producao='00:00:00';
                        @endphp
                         @foreach ($dados_pedido_status as $key => $dado_pedido_status)
                            @foreach ($dado_pedido_status['classe'] as $pedido)
                                <tr>
                                    <td>{{ $pedido->tabelaFichastecnicas->ep }}</td>
                                    <td>{{ $pedido->os }}</td>
                                    <td>{{ $pedido->qtde }}</td>
                                    <td>{{ \Carbon\Carbon::parse($pedido->data_gerado)->format('d/m/Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($pedido->data_entrega)->format('d/m/Y') }}</td>
                                    <td style="background-color: {{ $palheta_cores[4] }}">{{ !empty($pedido->apontamento_usinagem) ? \Carbon\Carbon::parse($pedido->apontamento_usinagem)->format('d/m/Y') : '' }}</td>
                                    <td style="background-color: {{ $palheta_cores[4] }}">{{ !empty($pedido->apontamento_usinagem) ? PedidosController::formatarHoraMinuto($dado_pedido_status['pedido'][$pedido->id]['usinagem']) : '' }}</td>
                                    @php
                                        $totais_usinagem = (new PedidosController)->somarHoras($dado_pedido_status['pedido'][$pedido->id]['usinagem'], $totais_usinagem);
                                    @endphp
                                    <td style="background-color: {{ $palheta_cores[5] }}">{{ !empty($pedido->apontamento_acabamento) ? \Carbon\Carbon::parse($pedido->apontamento_acabamento)->format('d/m/Y') : '' }}</td>
                                    <td style="background-color: {{ $palheta_cores[5] }}">{{ !empty($pedido->apontamento_acabamento) ? PedidosController::formatarHoraMinuto($dado_pedido_status['pedido'][$pedido->id]['acabamento']) : '' }}</td>
                                    @php
                                        $totais_acabamento = (new PedidosController)->somarHoras($dado_pedido_status['pedido'][$pedido->id]['acabamento'], $totais_acabamento);
                                    @endphp
                                    <td style="background-color: {{ $palheta_cores[6] }}">{{ !empty($pedido->apontamento_montagem) ? \Carbon\Carbon::parse($pedido->apontamento_montagem)->format('d/m/Y') : '' }}</td>
                                    <td style="background-color: {{ $palheta_cores[6] }}">{{ !empty($pedido->apontamento_montagem) ? PedidosController::formatarHoraMinuto($dado_pedido_status['pedido'][$pedido->id]['montagem']) : '' }}</td>
                                    @php
                                        $totais_montagem = (new PedidosController)->somarHoras($dado_pedido_status['pedido'][$pedido->id]['montagem'], $totais_montagem);
                                    @endphp
                                    <td style="background-color: {{ $palheta_cores[7] }}">{{ !empty($pedido->apontamento_montagem_torre) ? \Carbon\Carbon::parse($pedido->apontamento_montagem_torre)->format('d/m/Y') : '' }}</td>
                                    <td style="background-color: {{ $palheta_cores[7] }}">{{ !empty($pedido->apontamento_montagem_torre) ? PedidosController::formatarHoraMinuto($dado_pedido_status['pedido'][$pedido->id]['montagem_torre']) : '' }}</td>
                                    @php
                                        $totais_montagem_torre = (new PedidosController)->somarHoras($dado_pedido_status['pedido'][$pedido->id]['montagem_torre'], $totais_montagem_torre);
                                    @endphp
                                    <td style="background-color: {{ $palheta_cores[8] }}">{{ !empty($pedido->apontamento_inspecao) ? \Carbon\Carbon::parse($pedido->apontamento_inspecao)->format('d/m/Y') : '' }}</td>
                                    <td style="background-color: {{ $palheta_cores[8] }}">{{ !empty($pedido->apontamento_inspecao) ? PedidosController::formatarHoraMinuto($dado_pedido_status['pedido'][$pedido->id]['inspecao']) : '' }}</td>
                                    @php
                                        $totais_inspecao = (new PedidosController)->somarHoras($dado_pedido_status['pedido'][$pedido->id]['inspecao'], $totais_inspecao);
                                    @endphp
                                    <td style="background-color: {{ $palheta_cores[9] }}">{{ !empty($pedido->apontamento_embalagem) ? \Carbon\Carbon::parse($pedido->apontamento_embalagem)->format('d/m/Y') : '' }}</td>
                                    <td style="background-color: {{ $palheta_cores[10] }}">{{ !empty($pedido->apontamento_expedicao) ? \Carbon\Carbon::parse($pedido->apontamento_expedicao)->format('d/m/Y') : '' }}</td>
                                    <td style="background-color: {{ $palheta_cores[11] }}">{{ !empty($pedido->apontamento_entregue) ? \Carbon\Carbon::parse($pedido->apontamento_entregue)->format('d/m/Y') : '' }}</td>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th style="background-color: {{ $palheta_cores[4] }}"></th>
                            <th style="background-color: {{ $palheta_cores[4] }}">{{ PedidosController::formatarHoraMinuto($totais_usinagem) }}</th>
                            <th style="background-color: {{ $palheta_cores[5] }}"></th>
                            <th style="background-color: {{ $palheta_cores[5] }}">{{ PedidosController::formatarHoraMinuto($totais_acabamento) }}</th>
                            <th style="background-color: {{ $palheta_cores[6] }}"></th>
                            <th style="background-color: {{ $palheta_cores[6] }}">{{ PedidosController::formatarHoraMinuto($totais_montagem) }}</th>
                            <th style="background-color: {{ $palheta_cores[7] }}"></th>
                            <th style="background-color: {{ $palheta_cores[7] }}">{{ PedidosController::formatarHoraMinuto($totais_montagem_torre) }}</th>
                            <th style="background-color: {{ $palheta_cores[8] }}"></th>
                            <th style="background-color: {{ $palheta_cores[8] }}">{{ PedidosController::formatarHoraMinuto($totais_inspecao) }}</th>
                            <th style="background-color: {{ $palheta_cores[9] }}"></th>
                            <th style="background-color: {{ $palheta_cores[10] }}"></th>
                            <th style="background-color: {{ $palheta_cores[11] }}"></th>
                        </tr>
                    </tfoot>

                </table>
                </div>
                    <hr class="my-4">
                </div>
            @endif
        @stop
    @break

    @case('followup-gerencial')
    @section('content_header')
        <div class="form-group row">
            <h1 class="m-0 text-dark col-sm-6 col-form-label">Tela de Followup Gerêncial</h1>
        </div>
    @stop
    @section('content')
        @if (isset($dados_pedido_status))
            @foreach ($dados_pedido_status as $key => $dado_pedido_status)
                @php
                $total_mo = $total_mp = $total_soma = 0.00;
                @endphp
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
                                <th scope="col">Prioridade</th>
                                <th scope="col" title="Observações">Obs</th>
                                <th scope="col">Valor Unit</th>
                                <th scope="col">Total</th>
                                <th scope="col">Total MO</th>
                                <th scope="col">Total MP</th>
                                <th scope="col">$MP</th>
                                <th scope="col">%MP</th>
                                <th scope="col">$MO</th>
                                <th scope="col">%MO</th>
                                <th scope="col">$HM</th>
                                <th scope="col">Transporte</th>
                                <th scope="col">Status</th>
                                <th scope="col">Data status</th>
                                <th scope="col">Usinagem</th>
                                <th scope="col">Acabamento</th>
                                <th scope="col">Montagem</th>
                                <th scope="col">Inspeção</th>
                                <th scope="col" title="Alerta de dias">Alerta</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($dado_pedido_status['classe'] as $pedido)
                                @php
                                    $entrega = \Carbon\Carbon::createFromDate($pedido->data_entrega)->format('Y-m-d');
                                    $hoje = date('Y-m-d');
                                    $dias_alerta = \Carbon\Carbon::createFromDate($hoje)->diffInDays($entrega, false);
                                    if ($dias_alerta < 6) {
                                        $class_dias_alerta = 'text-danger';
                                    } else {
                                        $class_dias_alerta = 'text-primary';
                                    }
                                    $mp = DateHelpers::formatFloatValue($dado_pedido_status['pedido'][$pedido->id]['totais']['subTotalMP']);
                                    $mp_numerico = $dado_pedido_status['pedido'][$pedido->id]['totais']['subTotalMP'];
                                    $mo = $pedido->valor_unitario_adv - $mp;


                                    list($horas, $minutos, $segundos) = explode(':', $dado_pedido_status['pedido'][$pedido->id]['usinagem']);

                                    $tempoEmDias = ($horas / 24) + ($minutos / 1440) + ($segundos / 86400);
                                    $resultado = (($tempoEmDias / $pedido->qtde) * 24) * 60;
                                    $tempo_usinagem = number_format($resultado, 2, '.', '');

                                    $total = $pedido->valor_unitario_adv * $pedido->qtde;
                                    $total_soma = isset($total_soma) ? $total_soma + $total : $total;

                                    $valor_mo = $mo * $pedido->qtde;
                                    $valor_mp = $mp * $pedido->qtde;

                                    $total_mo = isset($total_mo) ? $total_mo + $valor_mo : $valor_mo ;
                                    $total_mp = isset($total_mp) ? $total_mp + $valor_mp : $valor_mp ;

                                    $percentual_mo = ($mo == 0 || $pedido->valor_unitario_adv == 0) ? 0 : ($mo/$pedido->valor_unitario_adv)*100;
                                    $percentual_mp = ($mp == 0 || $pedido->valor_unitario_adv == 0) ? 0 : ($mp/$pedido->valor_unitario_adv)*100;

                                @endphp

                                <tr>
                                    <td>{{ $pedido->tabelaPessoas->codigo_cliente }}</td>
                                    <td>{{ $pedido->tabelaPessoas->nome_assistente }}</td>
                                    <td>{{ $pedido->tabelaFichastecnicas->ep }}</td>
                                    <td>{{ $pedido->os }}</td>
                                    <td>{{ $pedido->qtde }}</td>
                                    <td>{{ \Carbon\Carbon::parse($pedido->data_gerado)->format('d/m/Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($pedido->data_entrega)->format('d/m/Y') }}</td>
                                    <td>@if(!empty($pedido->tabelaPrioridades->nome)){{ $pedido->tabelaPrioridades->nome }}@else{{''}}@endif</td>
                                    <td title="{{ $pedido->observacao }}">{!! Str::words($pedido->observacao, 1, '...') !!}</td>
                                    <td>{{ number_format($pedido->valor_unitario_adv, 2, ',', '.')  }}</td> <!--valor_unitário-->
                                    <td style="background-color: #d9edf7">{{ number_format($total, 2, ',', '.');  }}</td> <!--total-->
                                    <td style="background-color: #d9edf7">{{ number_format($valor_mo, 2, ',', '.')  }}</td> <!--total mo-->
                                    <td style="background-color: #d9edf7">{{ number_format($valor_mp, 2, ',', '.')  }}</td> <!--total mp-->
                                    <td style="background-color: #f3f2b6">{{ number_format(($mp), 2, ',', '.')  }}</td> <!--mp-->
                                    <td style="background-color: #f3f2b6">{{ number_format($percentual_mp, 2, ',', '.') }}%</td> <!--%mp-->
                                    <td style="background-color: #b2eeaa">{{ number_format(($mo), 2, ',', '.')  }}</td> <!--mp-->
                                    <td style="background-color: #b2eeaa">{{ number_format($percentual_mo, 2, ',', '.') }}%</td> <!--%MO-->

                                    @php
                                    $valotHM = ($mp ==0 || $tempo_usinagem == 0) ? '0,00' : number_format($pedido->valor_unitario_adv - ((($mp*1.53))/(($tempo_usinagem/60)*1.16)), 2, ',', '.');

                                    if($mp ==0 || $tempo_usinagem == 0){
                                        $valotHM_float = 0.00;
                                    } else {
                                        $valotHM_float = ($pedido->valor_unitario_adv - ($mp*1.53))/(($tempo_usinagem/60)*1.16);
                                    }


                                    @endphp
                                    <td @if($valotHM_float <300 ) style="background-color: #e25050; color: #FFFFFF" @endif >{{  number_format($valotHM_float, 2, ',', '.') }}</td> <!-- HM  unitario - ((MP*1,53))/((Tempo_usinagem/60)*1,16) -->

                                    <td title="{{$pedido->tabelaTransportes->nome}}">{!! Str::words($pedido->tabelaTransportes->nome, 2, '...') !!}</td>
                                    <td>{{ $pedido->tabelaStatus->nome }} </td>
                                    <td>{{ $dado_pedido_status['pedido'][$pedido->id]['data_alteracao_status'] }} </td>
                                    <td>{{ PedidosController::formatarHoraMinuto($dado_pedido_status['pedido'][$pedido->id]['usinagem']) }}
                                    </td>
                                    <td>{{ PedidosController::formatarHoraMinuto($dado_pedido_status['pedido'][$pedido->id]['acabamento']) }}
                                    </td>
                                    <td>{{ PedidosController::formatarHoraMinuto($dado_pedido_status['pedido'][$pedido->id]['montagem']) }}
                                    </td>
                                    <td>{{ PedidosController::formatarHoraMinuto($dado_pedido_status['pedido'][$pedido->id]['inspecao']) }}
                                    </td>
                                    <td class="{{ $class_dias_alerta }}">{{ $dias_alerta }}</td>
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
                                <th scope="col" style="background-color: #d9edf7">{{number_format($total_soma, 2, ',', '.') }}</th>
                                <th scope="col" style="background-color: #d9edf7">{{ number_format($total_mo, 2, ',', '.') }}</th>
                                <th scope="col" style="background-color: #d9edf7">{{number_format($total_mp, 2, ',', '.')}}</th>
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
                                <th scope="col"></th>
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
                                @php
                                    if($total_soma == 0 || $total_mo == 0 ){
                                        $totalMO = 0;
                                    } else {
                                        $totalMO = $total_mo/$total_soma;
                                    }

                                    if($total_soma == 0 || $total_mo == 0 ){
                                        $totalMP = 0;
                                    } else {
                                        $totalMP = $total_mp/$total_soma;
                                    }
                                @endphp
                                <th scope="col" style="background-color: #d9edf7">{{ number_format(($totalMO) * 100, 2, ',', '.') }}%</th>
                                <th scope="col" style="background-color: #d9edf7">{{ number_format(($totalMP) * 100, 2, ',', '.') }}%</th>
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
                                <th scope="col"></th>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <hr class="my-4">
            @endforeach
        @endif
    @stop
    @break

    @case('ciclo-producao')
        @section('content_header')
            <div class="form-group row">
                <h1 class="m-0 text-dark col-sm-6 col-form-label">Tela de Followup ciclo de produção</h1>
            </div>
        @stop
        @section('content')
            @if (isset($dados_pedido_status))
            <div class="form-group row" style="overflow-x:auto;  ">
                <table class="table table-sm table-striped text-center" id="table_composicao">
                    <thead>
                        <tr>
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col" title="Data do pedido"></th>
                            <th scope="col" title="Data da entrega"></th>
                            <th scope="col" title="Data da entrega"></th>
                            <th scope="col" colspan="2" style="background-color: {{ $palheta_cores[4] }}">Usinagem</th>

                            <th scope="col" colspan="2" style="background-color: {{ $palheta_cores[5] }}">Acabamento</th>

                            <th scope="col" colspan="2" style="background-color: {{ $palheta_cores[6] }}">Montagem</th>

                            <th scope="col" colspan="2" style="background-color: {{ $palheta_cores[8] }}">Inspeção</th>

                            <th scope="col" colspan="2" style="background-color: {{ $palheta_cores[9] }}">Embalar</th>

                            <th scope="col" colspan="2" style="background-color: {{ $palheta_cores[10] }}">Expedição</th>

                            <th scope="col" colspan="2" style="background-color: {{ $palheta_cores[11] }}">Entregue</th>

                            <th scope="col"></th>

                        </tr>
                        {{-- style="background-color: {{ $palheta_cores[$dado_pedido_status['id_status'][0]] }} --}}
                        <tr>
                            <th scope="col">EP</th>
                            <th scope="col">OS</th>
                            <th scope="col">Qtde</th>
                            <th scope="col">Prioridade</th>
                            <th scope="col" title="Data do pedido">Data</th>
                            <th scope="col" title="Data da entrega">Entrega</th>
                            <th scope="col" title="Data da entrega">Data de contagem</th>
                            <th scope="col" style="background-color: {{ $palheta_cores[4] }}">Apontamento</th>
                            <th scope="col" style="background-color: {{ $palheta_cores[4] }}">Dias Parados</th>
                            <th scope="col" style="background-color: {{ $palheta_cores[5] }}">Apontamento</th>
                            <th scope="col" style="background-color: {{ $palheta_cores[5] }}">Dias Parados</th>
                            <th scope="col" style="background-color: {{ $palheta_cores[6] }}">Apontamento</th>
                            <th scope="col" style="background-color: {{ $palheta_cores[6] }}">Dias Parados</th>
                            <th scope="col" style="background-color: {{ $palheta_cores[8] }}">Apontamento</th>
                            <th scope="col" style="background-color: {{ $palheta_cores[8] }}">Dias Parados</th>
                            <th scope="col" style="background-color: {{ $palheta_cores[9] }}">Apontamento</th>
                            <th scope="col" style="background-color: {{ $palheta_cores[9] }}">Dias Parados</th>
                            <th scope="col" style="background-color: {{ $palheta_cores[10] }}">Apontamento</th>
                            <th scope="col" style="background-color: {{ $palheta_cores[10] }}">Dias Parados</th>
                            <th scope="col" style="background-color: {{ $palheta_cores[11] }}">Apontamento</th>
                            <th scope="col" style="background-color: {{ $palheta_cores[11] }}">Dias Parados</th>
                            <th scope="col" title="Alerta de dias">Tempo da produção</th>
                            <th scope="col" title="Alerta de dias">Tempo de entrega</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php
                                    $contador=0;
                                    $dias_totais_usinagem=0;
                                    $dias_totais_acabamento=0;
                                    $dias_totais_montagem=0;
                                    $dias_totais_inspecao=0;
                                    $dias_totais_embalagem=0;
                                    $dias_totais_expedicao=0;
                                    $dias_totais_entregue=0;
                                    $dias_totais_producao=0;
                                ?>
                         @foreach ($dados_pedido_status as $key => $dado_pedido_status)
                            @foreach ($dado_pedido_status['classe'] as $pedido)
                                @php
                                    $contador++;
                                    $tempo_producao=0;

                                    $entrega = \Carbon\Carbon::createFromDate($pedido->data_entrega)->format('Y-m-d');
                                    $hoje = date('Y-m-d');
                                    $dias_alerta = \Carbon\Carbon::createFromDate($hoje)->diffInDays($entrega, false);
                                    if ($dias_alerta < 6) {
                                        $class_dias_alerta = 'text-danger';
                                    } else {
                                        $class_dias_alerta = 'text-primary';
                                    }

                                @endphp
                                <tr>
                                    <td>{{ $pedido->tabelaFichastecnicas->ep }}</td>
                                    <td>{{ $pedido->os }}</td>
                                    <td>{{ $pedido->qtde }}</td>
                                    <td>@if(!empty($pedido->tabelaPrioridades->nome)){{ $pedido->tabelaPrioridades->nome }}@else{{''}}@endif</td>
                                    <td>{{ \Carbon\Carbon::parse($pedido->data_gerado)->format('d/m/Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($pedido->data_entrega)->format('d/m/Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($dado_pedido_status['pedido'][$pedido->id]['data_prazo'])->format('d/m/Y') }}</td>
                                    <td style="background-color: {{ $palheta_cores[4] }}">{{ !empty($pedido->apontamento_usinagem) ? \Carbon\Carbon::parse($pedido->apontamento_usinagem)->format('d/m/Y') : '' }}</td>
                                    <td style="background-color: {{ $palheta_cores[4] }}">{{ !empty($pedido->apontamento_usinagem) ? \Carbon\Carbon::parse($pedido->apontamento_usinagem)->startOfDay()->diffInDays(\Carbon\Carbon::parse($dado_pedido_status['pedido'][$pedido->id]['data_prazo'])->startOfDay()) : ''  }}</td>
                                    @php
                                        $soma = !empty($pedido->apontamento_usinagem) ? \Carbon\Carbon::parse($pedido->apontamento_usinagem)->startOfDay()->diffInDays(\Carbon\Carbon::parse(\Carbon\Carbon::parse($dado_pedido_status['pedido'][$pedido->id]['data_prazo'])->startOfDay())->startOfDay()) : 0;
                                        $dias_totais_usinagem += $soma;
                                        $tempo_producao += $soma;
                                        if(!empty($pedido->apontamento_usinagem)) {
                                            $proxima_data=$pedido->apontamento_usinagem;
                                        } else {
                                            $proxima_data=$dado_pedido_status['pedido'][$pedido->id]['data_prazo'];
                                        }

                                     @endphp
                                    <td style="background-color: {{ $palheta_cores[5] }}">{{  !empty($pedido->apontamento_acabamento) ? \Carbon\Carbon::parse($pedido->apontamento_acabamento)->format('d/m/Y') : '' }}</td>
                                    <td style="background-color: {{ $palheta_cores[5] }}">{{ !empty($pedido->apontamento_acabamento) ? \Carbon\Carbon::parse($pedido->apontamento_acabamento)->startOfDay()->diffInDays(\Carbon\Carbon::parse($proxima_data)->startOfDay()) : ''  }}</td>
                                    @php
                                        $soma = !empty($pedido->apontamento_acabamento) ? \Carbon\Carbon::parse($pedido->apontamento_acabamento)->startOfDay()->diffInDays(\Carbon\Carbon::parse($proxima_data)->startOfDay()) : 0;
                                        $dias_totais_acabamento += $soma;
                                        $tempo_producao += $soma;
                                        if(!empty($pedido->apontamento_acabamento)) {
                                            $proxima_data =$pedido->apontamento_acabamento;
                                        }

                                     @endphp
                                    <td style="background-color: {{ $palheta_cores[6] }}">{{ !empty($pedido->apontamento_montagem) ? \Carbon\Carbon::parse($pedido->apontamento_montagem)->format('d/m/Y') : '' }}</td>
                                    <td style="background-color: {{ $palheta_cores[6] }}">{{ !empty($pedido->apontamento_montagem) ? \Carbon\Carbon::parse($pedido->apontamento_montagem)->startOfDay()->diffInDays(\Carbon\Carbon::parse($proxima_data)->startOfDay()) : ''  }}</td>
                                    @php
                                        $soma = !empty($pedido->apontamento_montagem) ? \Carbon\Carbon::parse($pedido->apontamento_montagem)->startOfDay()->diffInDays(\Carbon\Carbon::parse($proxima_data)->startOfDay()) : 0;
                                        $dias_totais_montagem += $soma;
                                        $tempo_producao += $soma;
                                        if(!empty($pedido->apontamento_montagem)) {
                                            $proxima_data =$pedido->apontamento_montagem;
                                        }

                                     @endphp
                                    <td style="background-color: {{ $palheta_cores[8] }}">{{ !empty($pedido->apontamento_inspecao) ? \Carbon\Carbon::parse($pedido->apontamento_inspecao)->format('d/m/Y') : '' }}</td>
                                    <td style="background-color: {{ $palheta_cores[8] }}">{{ !empty($pedido->apontamento_inspecao) ? \Carbon\Carbon::parse($pedido->apontamento_inspecao)->startOfDay()->diffInDays(\Carbon\Carbon::parse($proxima_data)->startOfDay()) : ''  }}</td>
                                    @php
                                        $soma = !empty($pedido->apontamento_inspecao) ? \Carbon\Carbon::parse($pedido->apontamento_inspecao)->startOfDay()->diffInDays(\Carbon\Carbon::parse($proxima_data)->startOfDay()) : 0;
                                        $dias_totais_inspecao += $soma;
                                        $tempo_producao += $soma;
                                        if(!empty($pedido->apontamento_inspecao)) {
                                            $proxima_data =$pedido->apontamento_inspecao;
                                        }

                                     @endphp

                                    <td style="background-color: {{ $palheta_cores[9] }}">{{ !empty($pedido->apontamento_embalagem) ? \Carbon\Carbon::parse($pedido->apontamento_embalagem)->format('d/m/Y') : '' }}</td>
                                    <td style="background-color: {{ $palheta_cores[9] }}">{{ !empty($pedido->apontamento_embalagem) ? \Carbon\Carbon::parse($pedido->apontamento_embalagem)->startOfDay()->diffInDays(\Carbon\Carbon::parse($proxima_data)->startOfDay()) : ''  }}</td>
                                    @php
                                        $soma = !empty($pedido->apontamento_embalagem) ? \Carbon\Carbon::parse($pedido->apontamento_embalagem)->startOfDay()->diffInDays(\Carbon\Carbon::parse($proxima_data)->startOfDay()) : 0;
                                        $dias_totais_embalagem += $soma;
                                        $tempo_producao += $soma;
                                        if(!empty($pedido->apontamento_embalagem)) {
                                            $proxima_data =$pedido->apontamento_embalagem;
                                        }

                                     @endphp
                                    <td style="background-color: {{ $palheta_cores[10] }}">{{ !empty($pedido->apontamento_expedicao) ? \Carbon\Carbon::parse($pedido->apontamento_expedicao)->format('d/m/Y') : '' }}</td>
                                    <td style="background-color: {{ $palheta_cores[10] }}">{{ !empty($pedido->apontamento_expedicao) ? \Carbon\Carbon::parse($pedido->apontamento_expedicao)->startOfDay()->diffInDays(\Carbon\Carbon::parse($proxima_data)->startOfDay()) : ''  }}</td>
                                    @php
                                        $soma = !empty($pedido->apontamento_expedicao) ? \Carbon\Carbon::parse($pedido->apontamento_expedicao)->startOfDay()->diffInDays(\Carbon\Carbon::parse($proxima_data)->startOfDay()) : 0;
                                        $dias_totais_expedicao += $soma;
                                        $tempo_producao += $soma;
                                        if(!empty($pedido->apontamento_expedicao)) {
                                            $proxima_data =$pedido->apontamento_expedicao;
                                        }

                                     @endphp
                                    <td style="background-color: {{ $palheta_cores[11] }}">{{ !empty($pedido->apontamento_entregue) ? \Carbon\Carbon::parse($pedido->apontamento_entregue)->format('d/m/Y') : '' }}</td>
                                    <td style="background-color: {{ $palheta_cores[11] }}">{{ !empty($pedido->apontamento_entregue) ? \Carbon\Carbon::parse($pedido->apontamento_entregue)->startOfDay()->diffInDays(\Carbon\Carbon::parse($proxima_data)->startOfDay()) : ''  }}</td>
                                    @php
                                        $soma = !empty($pedido->apontamento_entregue) ? \Carbon\Carbon::parse($pedido->apontamento_entregue)->startOfDay()->diffInDays(\Carbon\Carbon::parse($proxima_data)->startOfDay()) : 0;
                                        $dias_totais_entregue += $soma;
                                        $tempo_producao += $soma;
                                        if(!empty($pedido->apontamento_entregue)) {
                                            $proxima_data =$pedido->apontamento_entregue;
                                        }

                                     @endphp
                                    <td >{{ $tempo_producao }}</td>
                                    <td> {{ \Carbon\Carbon::parse($pedido->data_entrega)->startOfDay()->diffInDays(\Carbon\Carbon::parse($pedido->apontamento_entregue)->startOfDay()) }}</td>
                                    @php $dias_totais_producao += $tempo_producao; @endphp
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th style="background-color: {{ $palheta_cores[4] }}"></th>
                            <th style="background-color: {{ $palheta_cores[4] }}">{{ number_format($dias_totais_usinagem/$contador, 2, '.','.') }}</th>
                            <th style="background-color: {{ $palheta_cores[5] }}"></th>
                            <th style="background-color: {{ $palheta_cores[5] }}">{{ number_format($dias_totais_acabamento/$contador, 2, '.','.') }}</th>
                            <th style="background-color: {{ $palheta_cores[6] }}"></th>
                            <th style="background-color: {{ $palheta_cores[6] }}">{{ number_format($dias_totais_montagem/$contador, 2, '.','.') }}</th>
                            <th style="background-color: {{ $palheta_cores[8] }}"></th>
                            <th style="background-color: {{ $palheta_cores[8] }}">{{ number_format($dias_totais_inspecao/$contador, 2, '.','.') }}</th>
                            <th style="background-color: {{ $palheta_cores[9] }}"></th>
                            <th style="background-color: {{ $palheta_cores[9] }}">{{ number_format($dias_totais_embalagem/$contador, 2, '.','.') }}</th>
                            <th style="background-color: {{ $palheta_cores[10] }}"></th>
                            <th style="background-color: {{ $palheta_cores[10] }}">{{ number_format($dias_totais_expedicao/$contador, 2, '.','.') }}</th>
                            <th style="background-color: {{ $palheta_cores[11] }}"></th>
                            <th style="background-color: {{ $palheta_cores[11] }}">{{ number_format($dias_totais_entregue/$contador, 2, '.','.') }}</th>
                            <th>{{ number_format($dias_totais_producao/$contador, 2, '.','.') }}</th>
                            <th>{{ number_format( \Carbon\Carbon::parse($pedido->data_entrega)->startOfDay()->diffInDays(\Carbon\Carbon::parse($pedido->apontamento_entregue)->startOfDay()) /$contador , 2, '.','.') }}</th>
                        </tr>
                    </tfoot>

                </table>
                </div>
                    <hr class="my-4">

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
                                <th scope="col" title="Data da entrega">Data Entrega</th>
                                <th scope="col" title="Alerta de dias">Alerta</th>
                                <th scope="col">Email</th>
                                <th scope="col">Enviar
                                    <input type="checkbox" class="checkbox_emails_todos" checked>
                                </th>
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
                                    <td>{{ \Carbon\Carbon::parse($pedido->data_entrega)->format('d/m/Y') }}</td>
                                    <td class="{{ $class_dias_alerta }}">{{ $dias_alerta }}</td>
                                    <td>{{ $pedido->email }}</td>
                                    <td>
                                        <input type="hidden" name="emails[]" value="{{ $pedido->id }}">
                                        <input type="checkbox" class="checkbox_emails" checked value="{{ $pedido->id }}"
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
