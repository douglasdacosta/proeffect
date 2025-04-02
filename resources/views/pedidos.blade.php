<?php
use App\Http\Controllers\PedidosController;
use App\Http\Controllers\AjaxOrcamentosController;
use App\Providers\DateHelpers;

$palheta_cores = [1 => '#ff003d', 2 => '#ee7e4c', 3 => '#8f639f', 4 => '#94c5a5', 5 => '#ead56c', 6 => '#0fbab7', 7 => '#f7c41f', 8 => '#898b75', 9 => 
'#c1d9d0', 10 => '#da8f72', 11 => '#00caf8', 12 => '#ffe792', 13 => '#9a5071', 14 => '#4a8583', 15 => '#f7c41f', 16 => '#898b75', 17 => '#c1d9d0'];
?>
@extends('adminlte::page')

@section('title', 'Pro Effect')

@section('adminlte_css')
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/main_style.css') }}" />
@stop

    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="js/bootstrap.4.6.2.js"></script>
    <script src="js/jquery.mask.js"></script>
    <script src="js/select2.min.js"></script>
    <script src="js/main_custom.js"></script>
    <script src="js/pedidos.js"></script>


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
                    @include('layouts.nav-open-incluir', ['rotaIncluir' => $rotaIncluir])
                </div>
            </div>
        @stop
        @section('content')
            <div id='modal_whatsapp' class="modal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content" style="width: 100%">
                        <div class="modal-header">
                            <h5 class="modal-title" id='texto_status_caixas'>Envio de mensagem para WhatsApp</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">                            
                            <p id="texto_link"></p>
                            <input type="hidden" name="input_link_envio" id="input_link_envio" value=""/>
                            <input type="hidden" name="id_pessoa" id="input_id_pessoa" value=""/>
                            <input type="hidden" name="whatsapp_status" id="input_whatsapp_status" value=""/>
                        </div>
                        <div class="modal-footer">
                            <a class="btn btn-success" id="enviar_link" data-dismiss="modal">Enviar</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div id='modal_funcionarios' class="modal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content" style="width: 700px">
                        <div class="modal-header">
                            <h5 class="modal-title" id='texto_status_caixas'>Funcionários Montagens</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            @if (isset($funcionarios))
                                @foreach ($funcionarios as $funcionario)
                                    <div class="form-check">
                                        <input class="form-check-input montadores" type="checkbox" value="{{ $funcionario->id }}" id="montador{{ $funcionario->id }}">
                                        <label class="form-check-label" for="montador{{ $funcionario->id }}">{{ $funcionario->nome }}</label>
                                    </div>
                                @endforeach
                            @endif
                            <input type="hidden" name="funcionarios_selecionados" id="funcionarios_selecionados" value=""/>
                            <input type="hidden" name="pedido_montagem" id="pedido_montagem" value=""/>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" id="adicionar_montador" data-dismiss="modal">Adicionar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div id='modal_caixas' class="modal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content" style="width: 700px">
                        <div class="modal-header">
                            <h5 class="modal-title" id='texto_status_caixas'>Caixas</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <table class="table table-striped text-center" id='tabela_caixas'>
                                <thead>
                                    <tr>
                                        <th>Caixa</th>
                                        <th>A</th>
                                        <th>L</th>
                                        <th>C</th>
                                        <th>Qtde</th>
                                        <th>Peso</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Conteúdo da tabela -->
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
                        <!-- Conteúdo do alerta -->
                    </div>
                </div>
            </div>

            <form id="filtro" action="pedidos" method="get" data-parsley-validate="" novalidate="">
                <div class="row" role="main">
                    <div class="col-md-9 themed-grid-col row">
                        <div class="row">
                            <label for="codigo_cliente" class="col-sm-2 col-form-label text-right">Código cliente</label>
                            <div class="col-sm-1">
                                <input type="text" id="codigo_cliente" name="codigo_cliente" class="form-control col-md-13" value="">
                            </div>
                            <label for="nome_cliente" class="col-sm-2 col-form-label text-right">Nome cliente</label>
                            <div class="col-sm-3">
                                <input type="text" id="nome_cliente" name="nome_cliente" class="form-control col-md-13" value="">
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
                                <input type="text" class="form-control mask_date" id="data_entrega" name="data_entrega" placeholder="DD/MM/AAAA">
                            </div>
                            <label for="data_entrega_fim" class="col-form-label text-right">até</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control mask_date" id="data_entrega_fim" name="data_entrega_fim" placeholder="DD/MM/AAAA">
                            </div>
                            <label for="status" class="col-sm-1 col-form-label">&nbsp;</label>
                            <div class="col-sm-2">
                                <select class="form-control" id="status" name="status">
                                    <option value="A" @if (isset($request) && $request->input('status') == 'A') selected @endif>Ativo</option>
                                    <option value="I" @if (isset($request) && $request->input('status') == 'I') selected @endif>Inativo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 themed-grid-col row">
                        <label for="ep" class="col-sm-4 col-form-label text-right">Status do pedido</label>
                        <div class="col-sm-7" style="overflow-y: auto; height: 175px; border:1px solid #ced4da; border-radius: .25rem;">
                            @foreach ($AllStatus as $status)
                                <div class="col-sm-8 form-check">
                                    <input class="form-check-input col-sm-4" name="status_id[]" id="{{ $status->id }}" type="checkbox" @if(in_array($status->id, [1,2,3,4,5,6,7,8,9,10])) checked @endif value="{{ $status->id }}">
                                    <label class="form-check-label col-sm-6" style="white-space:nowrap" for="{{ $status->id }}">{{ $status->nome }}</label>
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
                            <table class="table table-striped text-center">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>OS</th>
                                        <th>EP</th>
                                        <th>Qtde</th>
                                        <th>Status do pedido</th>
                                        <th>Faturado</th>
                                        <th>Data gerado</th>
                                        <th>Montadores</th>
                                        <th>Data entrega</th>
                                        <th>Data antecipação</th>
                                        <th>Hora retirada</th>
                                        <th>WhatsApp</th>
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
                                                <th scope="row"><a href={{ URL::route($rotaAlterar, ['id' => $pedido->id]) }}>{{ $pedido->id }}</a></th>
                                                <td>{{ $pedido->os }}</td>
                                                <td>{{ $pedido->ep }}</td>
                                                <td>{{ $pedido->qtde }}</td>
                                                <td>
                                                    <select class="form-control alteracao_status_pedido" data-statusatual='{{ $pedido->id_status }}' data-pedido="{{ $pedido->id }}" id="status_id" name="status_id">
                                                        @if (isset($AllStatus))
                                                            @foreach ($AllStatus as $stats)
                                                                <option value="{{ $stats->id }}" @if ($stats->id < $pedido->id_status) disabled @endif @if (isset($pedido->id_status) && $pedido->id_status == $stats->id) selected @endif>{{ $stats->nome }}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </td>
                                                <td title="Marca como faturado"> 
                                                    <i style="cursor: pointer;@if($pedido->faturado==1) color:green @else color:red @endif" data-pedido="{{ $pedido->id }}" data-status_faturado="{{ $pedido->faturado }}" class="fas fa-dollar-sign faturados"></i>
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
                                                    <i data-funcionariomontagem="@foreach($funcionarios_vinculados[$pedido->id]['funcionarios_montagens'] as $funcionario_montagem){{ $funcionario_montagem->id.',' }}@endforeach" title="@foreach($funcionarios_vinculados[$pedido->id]['funcionarios_montagens'] as $funcionario_montagem){{ $funcionario_montagem->nome.', ' }}@endforeach" data-pedido_id="{{ $pedido->id }}" style="cursor:pointer; @if (count($funcionarios_vinculados[$pedido->id]['funcionarios_montagens']) > 0) color:#044f04 @else color:#cacaca @endif" class="fas fa-users add_funcionarios_montagens"></i>
                                                    {{ count($funcionarios_vinculados[$pedido->id]['funcionarios_montagens']) }}
                                                </td>
                                                <td>{{ Carbon\Carbon::parse($pedido->data_entrega)->format('d/m/Y') }}</td>
                                                <td style="@if(!empty($pedido->data_antecipacao)) background-color: red; font-weight: bold @endif">{{ empty($pedido->data_antecipacao) ? '' : Carbon\Carbon::parse($pedido->data_antecipacao)->format('d/m/Y') }}</td>
                                                <td style="@if(!empty($pedido->hora_antecipacao)) background-color: red; font-weight: bold @endif">{{ $pedido->hora_antecipacao }}</td>
                                                @php 
                                                    $telefone = preg_replace("/[^0-9]/", "", $pedido->telefone);
                                                    $hash_codigo_cliente = md5($pedido->id_pessoa);
                                                    $link_eplax = 'https://' . 
                                                                env('URL_LINK_STATUS', 'eplax.com.br') . 
                                                                '/consultar-pedido/' . $hash_codigo_cliente . '/consulta/';

                                                    $mensagem = 'Olá, ' . $pedido->nome_cliente . 
                                                                '. Acesse o link para detalhes do status do seu pedido: ' . $link_eplax;

                                                    $link = 'https://wa.me/55' . $telefone . '?text=' . urlencode($mensagem);
                                                @endphp
                                                <td class="text-center"> 
                                                    <i style="cursor:pointer; @if($pedido->whatsapp_status==1) color:green @else color:red @endif" data-link="{{ $link }}" data-link_eplax="{{ $link_eplax }}"  data-id_pessoa="{{ $pedido->id_pessoa }}" data-whatsapp_status="{{ $pedido->whatsapp_status }}" class="fab fa-whatsapp whatsapp_status {{ 'icone_'.$pedido->id_pessoa }}"></i>
                                                </td>
                                                <td class="{{ $class_dias_alerta }}">{{ $dias_alerta }}</td>
                                                <th scope="row" title="Imprimir ordem de serviço">
                                                    <a target="_blank" href="{{ URL::route('imprimirOS', ['id' => $pedido->id]) }}"><span class="fa fa-print"></span></a>
                                                </th>
                                                <th scope="row">
                                                    <a href="{{ URL::route('imprimirMP', ['id' => $pedido->id]) }}"><span class="fa fa-print"></span></a>
                                                </th>
                                                <th scope="row">
                                                    <a class="show_caixas" data-pedido_id="{{ $pedido->id }}">
                                                        <i style="cursor:pointer; @if($pedido->caixas==0) color:#cacaca @else color:#044f04 @endif" class="fas fa-eye"></i>
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
                    <select class="form-control default-select2" id="clientes_id" name="clientes_id">
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
                        value="@if (isset($pedidos[0]->hora_antecipacao)) {{$pedidos[0]->hora_antecipacao}} @else {{''}} @endif">
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
        @include('followups.pesquisa_followup')
    @break

    @case('followup-detalhes')
        @include('followups.followup-detalhes')
    @break

    @case('followup-detalhes-geral')
        @include('followups.followup-detalhes-geral')
    @break

    @case('pesquisa-gerencial')
        @include('followups.pesquisa-gerencial')
    @break

    @case('followup-realizado')
        @include('followups.followup-realizado')
    @break

    @case('followup-gerencial')
        @include('followups.followup-gerencial')
    @break

    @case('ciclo-producao')
        @include('followups.ciclo-producao')
    @break

    @case('alerta-pedidos')
        @include('layouts.alerta-pedidos')
    @break

@endswitch


