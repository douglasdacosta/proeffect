<?php
use App\Providers\DateHelpers;
use App\Http\Controllers\PedidosController;
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
    <script src="js/projetos.js"></script>
    <link rel="stylesheet" href="{{ asset('css/main_style.css') }}" />
@switch($tela)


    @case('pesquisar')

        @section('content_header')
            <div class="form-group row">
                <h1 class="m-0 text-dark col-sm-10 col-form-label">Pesquisa de {{ $nome_tela }}</h1>
            </div>
        @stop
        @section('content')
        {{-- <div id="toastsContainerTopRight" class="toasts-top-right fixed">
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
            </div> --}}
            <input type="hidden" class="projeto_id" name="projeto_id" id="projeto_id" value=""/>
            <div id='modal_funcionarios_projetos' class="modal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content" style="width: 800px">
                        <div class="modal-header">
                            <h5 class="modal-title" id='texto_status_caixas'>Colaborador do projeto</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            {{-- <label for="funcionarios_id" class="col-sm-3 col-form-label">Colaborador </label> --}}
                            <div class="col-sm-6">
                                <select class="form-control" id="funcionarios_id" name="funcionarios_id">
                                    <option value="" selected="selected"></option>
                                    @if (isset($AllFuncionarios))
                                        @foreach ($AllFuncionarios as $funcionario)
                                            <option value="{{ $funcionario->id }}">
                                                {{ $funcionario->nome }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>


                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" id="adicionar_funcionario_projetos" data-dismiss="modal">Adicionar</button>
                        </div>
                    </div>
                </div>
            </div>
            <div id='modal_apontamento_projetos' class="modal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content" style="width: 800px">
                        <div class="modal-header">
                            <h5 class="modal-title" id='texto_status_caixas'>Apontamentos</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            {{-- <label for="funcionarios_id" class="col-sm-3 col-form-label">Colaborador </label> --}}
                            <div class="col-sm-6">
                                <select class="form-control" id="apontamento_id" name="apontamento_id">
                                    <option value="" selected="selected"></option>
                                    <option value="1" selected="selected">Início</option>
                                    <option value="2" selected="selected">Pausa</option>
                                    <option value="3" selected="selected">Continuar</option>
                                    <option value="4" selected="selected">Finalizar</option>
                                </select>
                            </div>


                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" id="adicionar_apontamento_projetos" data-dismiss="modal">Adicionar</button>
                        </div>
                    </div>
                </div>
            </div>
            <div id='modal_tarefa_projetos' class="modal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content" style="width: 800px; height: 1000px; overflow-y: auto; background-color: #f4f4f4">
                        <div class="modal-header">
                            <h5 class="modal-title" id='texto_status_caixas'>Tarefas</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group row">
                                <label for="tarefa_modal" class="col-sm-2 col-form-label text-right">Descrição</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" id="tarefa_modal" name="tarefa_modal"></textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                 <label for="funcionarios_id_modal" class="col-sm-2 col-form-label text-right">Colaborador</label>
                                <div class="col-sm-4">
                                    <select class="form-control funcionarios_id_modal" id="funcionarios_id_modal" name="funcionarios_id_modal">
                                            <option value="" selected="selected">Sem compromisso</option>
                                            @if (isset($AllFuncionarios))
                                                @foreach ($AllFuncionarios as $funcionario)
                                                    <option value="{{ $funcionario->id }}">
                                                        {{ $funcionario->nome }}
                                                    </option>
                                                @endforeach
                                            @endif
                                    </select>
                                </div>
                                <label for="data_tarefa_modal" class="col-sm-3 col-form-label text-right data_tarefa_modal">Data compromisso </label>
                                <div class="col-sm-3">
                                    <input type="date" class="form-control data_tarefa_modal" id="data_tarefa_modal" name="data_tarefa_modal" value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="tarefa" class="col-sm-10 col-form-label text-right"></label>
                                <div class="col-sm-2">
                                    <button type="button" class="btn btn-success" id="adicionar_tarefa_projetos" data-dismiss="modal">Adicionar</button>
                                </div>
                            </div>
                            <div class="form-group">

                                <div class="col-sm-12" id="tarefas_list">

                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">

                        </div>
                    </div>
                </div>
            </div>

            <form id="filtro" action="projetos"  data-parsley-validate="" novalidate="">
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
                        <label for="departamento_id" class="col-sm-4 col-form-label text-right">Status do pedido</label>
                        <div class="col-sm-7" style="overflow-y: auto; height: 175px; border:1px solid #ced4da; border-radius: .25rem;">
                            @foreach ($AllStatus as $status)
                                <div class="form-check">
                                    <input class="form-check-input " name="departamento_id[]" id="{{ $status->id }}" type="checkbox" @if(in_array($status->id, [1,2,3,4,5,6,7,])) checked @endif value="{{ $status->id }}">
                                <div class="form-check">
                                    <label class="form-check-label " style="white-space:nowrap" for="{{ $status->id }}">{{ $status->nome }}</label>
                                </div>

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
                            @php
                             $palheta_cores = ['Expedição' => '#ff003d', 'Projetos' => '#ee7e4c', 'Reunião' => '#8f639f', 'Vendas' => '#94c5a5', 'Em Preparação' => '#ead56c', 'Entregue' => '#0fbab7', 'Expedição' => '#f7c41f', 'Cancelado' => '#898b75', 'Desenvolvimento' => '#c1d9d0', 'Vendas' => '#da8f72', 11 => '#00caf8', 12 => '#ffe792', 13 => '#9a5071', 14 => '#4a8583'];
                             $count = 0;
                            @endphp
                            @if(isset($dados['departamentos']))
                                @foreach ($dados['departamentos'] as $status_nome => $projetos)
                                    @php
                                        $count ++;
                                        $somado_tempo_projeto = $somado_tempo_programacao = '00:00:00';
                                        $pedidos_Controller = new PedidosController();

                                    @endphp
                                    <h3>{{ $status_nome }} ({{ count($projetos) }})</h3>
                                    <table class="table table-striped text-center col-md-12" >
                                        <thead>
                                            @if(!empty($permissoes_liberadas) && (in_array(1, $permissoes_liberadas)))
                                                <tr style="background-color: {{$palheta_cores[trim($status_nome)]}}">
                                                    <th style="min-width: 50px;">ID</th>
                                                    <th style="min-width: 50px;">Muda Alerta</th>
                                                    <th style="min-width: 150px;">Cliente</th>
                                                    <th style="min-width: 100px;">EP</th>
                                                    <th style="min-width: 150px;">Data solicitação</th>
                                                    <th style="min-width: 50px;">Qtde</th>
                                                    <th style="min-width: 100px;">Até/Urg</th>
                                                    <th style="min-width: 150px;">Novo/Alteração</th>
                                                    <th style="min-width: 150px;">Etapa Projeto</th>
                                                    <th style="min-width: 150px;">Colaborador</th>
                                                    <th style="min-width: 150px;">Tempo Projeto</th>
                                                    <th style="min-width: 200px;">Tempo Programação</th>
                                                    <th style="min-width: 150px;">Prazo Entrega</th>
                                                    <th style="min-width: 150px;">Alerta dias</th>
                                                    <th style="min-width: 150px;">Data Status</th>
                                                    <th style="min-width: 150px;">Data Tarefa</th>
                                                    <th style="min-width: 50px;">Apontamento</th>
                                                </tr>
                                            @endif
                                            @if(!empty($permissoes_liberadas) && (in_array(2, $permissoes_liberadas)))
                                                <tr style="background-color: {{$palheta_cores[trim($status_nome)]}}">
                                                    <th style="min-width: 50px;">ID</th>
                                                    <th style="min-width: 50px;">Muda Alerta</th>
                                                    <th style="min-width: 150px;">Cliente</th>
                                                    <th style="min-width: 100px;">EP</th>
                                                    <th style="min-width: 150px;">Data solicitação</th>
                                                    <th style="min-width: 50px;">Qtde</th>
                                                    <th style="min-width: 100px;">Até/Urg</th>
                                                    <th style="min-width: 150px;">Pedido</th>
                                                    <th style="min-width: 100px;">Valor</th>
                                                    <th style="min-width: 150px;">Novo/Alteração</th>
                                                    <th style="min-width: 150px;">Etapa Projeto</th>
                                                    <th style="min-width: 150px;">Prazo Entrega</th>
                                                    <th style="min-width: 150px;">Alerta dias</th>
                                                </tr>
                                            @endif
                                            @if(!empty($permissoes_liberadas) && (in_array(3, $permissoes_liberadas)))
                                                <tr style="background-color: {{$palheta_cores[trim($status_nome)]}}">
                                                    <th style="min-width: 50px;">ID</th>
                                                    <th style="min-width: 50px;">Muda Alerta</th>
                                                    <th style="min-width: 150px;">Cliente</th>
                                                    <th style="min-width: 100px;">EP</th>
                                                    <th style="min-width: 150px;">Data solicitação</th>
                                                    <th style="min-width: 50px;">Qtde</th>
                                                    <th style="min-width: 50px;">Blank</th>
                                                    <th style="min-width: 100px;">Até/Urg</th>
                                                    <th style="min-width: 150px;">Pedido</th>
                                                    <th style="min-width: 100px;">Valor</th>
                                                    <th style="min-width: 150px;">Cliente Ativo</th>
                                                    <th style="min-width: 150px;">Novo/Alteração</th>
                                                    <th style="min-width: 150px;">Etapa Projeto</th>
                                                    <th style="min-width: 150px;">Colaborador</th>
                                                    <th style="min-width: 150px;">Tempo Projeto</th>
                                                    <th style="min-width: 200px;">Tempo Programação</th>
                                                    <th style="min-width: 150px;">Prazo Entrega</th>
                                                    <th style="min-width: 150px;">Alerta dias</th>
                                                    <th style="min-width: 250px;">Status</th>
                                                    <th style="min-width: 250px;">Transporte</th>
                                                    <th style="min-width: 220px;">Tempo Desenvolvimento</th>
                                                    <th style="min-width: 150px;">Data Status</th>
                                                    <th style="min-width: 150px;">Observação</th>
                                                    <th style="min-width: 150px;">Data Tarefa</th>
                                                    <th style="min-width: 50px;">Apontamento</th>
                                                </tr>
                                            @endif

                                        </thead>
                                        <tbody>
                                            @foreach($projetos as $key => $projeto)
                                                @php

                                                    $pedidos_Controller = new PedidosController();
                                                    $tempo_projeto = $projeto['tempo_projetos'] ?? '00:00:00';
                                                    $tempo_programacao = $projeto['tempo_programacao'] ?? '00:00:00';
                                                    if($projeto['status_projetos_id'] == 4) {
                                                        if( in_array($projeto['etapas_projetos_id'], [1,2]) ){

                                                            $somado_tempo_projeto = $pedidos_Controller->somarHoras($somado_tempo_projeto, $tempo_projeto);
                                                        }
                                                        if( in_array($projeto['etapas_projetos_id'], [1,2,3]) ){

                                                            $somado_tempo_programacao = $pedidos_Controller->somarHoras($somado_tempo_programacao, $tempo_programacao);
                                                        }

                                                    }

                                                    $tempo_desenvolvimento_em_dias = '';
                                                    if( in_array($projeto['etapas_projetos_id'], [1,2,3,4]) ){

                                                        $data_gerado = $projeto['data_gerado'];
                                                        $tempo_desenvolvimento_em_dias = \Carbon\Carbon::createFromDate($data_gerado)->diffInDays(\Carbon\Carbon::now());
                                                    }else {

                                                        $data_historico = $projeto['data_historico'];
                                                        $tempo_desenvolvimento_em_dias = \Carbon\Carbon::createFromDate($data_historico)->diffInDays(\Carbon\Carbon::now());
                                                    }


                                                @endphp
                                                @if(isset($projeto['id']))
                                                    @if(!empty($permissoes_liberadas) && @(in_array(1, $permissoes_liberadas)))
                                                        <tr class="linha_{{ $projeto['id'] }}" @if(isset($projeto['em_alerta']) && $projeto['em_alerta'] == 1) style="background-color: #F2C807" @endif >
                                                            <th scope="row"><a href={{ URL::route($rotaAlterar, ['id' => $projeto['id']]) }}>{{ $projeto['id'] }}</a></th>
                                                            <td>
                                                                <i data-projeto_id="{{ $projeto['id'] }}" style="cursor:pointer;
                                                                @if (isset($projeto['em_alerta']) && $projeto['em_alerta'] == 1) color:#d9534f @else color:#12ad04 @endif"
                                                                class="fas fa-bell toggle_alerta_projetos"></i>
                                                            </td>
                                                            <td title="{{ trim($projeto['nome_cliente']) }}">{{ strlen(trim($projeto['nome_cliente'])) > 9 ? substr(trim($projeto['nome_cliente']), 0, 9) . '...' : trim($projeto['nome_cliente']) }}</td>
                                                            <td>{{ $projeto['ep'] }}</td>
                                                            <td>{{ \Carbon\Carbon::parse($projeto['data_gerado'])->format('d/m/Y') }}</td>
                                                            <td>{{ $projeto['qtde'] }}</td>
                                                            <td>{{ isset($projeto['prioridade_nome']) ? $projeto['prioridade_nome'] : '' }}</td>
                                                            <td>{{ $projeto['novo_alteracao'] == 0 ? 'NOVO' : ($projeto['novo_alteracao'] == 1 ? 'ALTERAÇÃO' : '') }}</td>
                                                            <td>
                                                                <select class="form-control pesquisa_etapas_projetos"
                                                                @if($projeto['sub_status_projetos_codigo'] != 1) disabled="disabled" @endif
                                                                data-projeto="{{ $projeto['id'] }}" data-sub_status_projetos_codigo="{{ $projeto['sub_status_projetos_codigo'] }}" id="pesquisa_etapas_projetos" name="pesquisa_etapas_projetos">
                                                                    <option @if(empty($projeto['etapas_projetos_id']) ) selected="selected" @endif value=""></option>
                                                                    @if (isset($AllEtapasProjetos))
                                                                        @foreach ($AllEtapasProjetos as $EtapasProjeto)
                                                                            <option value="{{ $EtapasProjeto->id }}"
                                                                                @if (!empty($projeto['etapas_projetos_id']) && $projeto['etapas_projetos_id'] === $EtapasProjeto->id) selected="selected" @endif>{{  $EtapasProjeto->nome }}</option>
                                                                        @endforeach
                                                                    @endif
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <i data-funcionariomontagem="" title="{{isset($projeto['nome_funcionario']) ? $projeto['nome_funcionario'] : ''}}"
                                                                    data-projeto_id="{{ $projeto['id'] }}" style="cursor:pointer;
                                                                @if ($projeto['nome_funcionario'] !=  '') color:#044f04 @else color:#cacaca @endif"
                                                                class="fas fa-users add_funcionarios_projetos"></i>
                                                            <td>{{ isset($projeto['tempo_projetos']) ? $projeto['tempo_projetos'] : '' }}</td>
                                                            <td>{{ isset($projeto['tempo_programacao']) ? $projeto['tempo_programacao'] : '' }}</td>
                                                            <td>{{ !empty($projeto['data_prazo_entrega']) ? $projeto['data_prazo_entrega'] : '' }}</td>
                                                            <td style="color: {{ $projeto['cor_alerta'] }};">{{ !empty($projeto['alerta_dias']) ? abs($projeto['alerta_dias']) : '' }}</td>
                                                            <td>
                                                                {{ !empty($projeto['data_historico']) ? Carbon\Carbon::parse($projeto['data_historico'])->format('d/m/Y') : '' }}
                                                            </td>
                                                            <td nowrap >
                                                                @if($projeto['compromisso'] == 1)
                                                                    <i class="fas fa-exclamation-triangle" style="color:#d9534f" title="Possui compromisso"></i>
                                                                @endif
                                                                {{ isset($projeto['data_tarefa']) ? $projeto['data_tarefa'].' '  : '' }}
                                                                <i data-projeto_id="{{ $projeto['id'] }}" style="cursor:pointer; color:#12ad04"
                                                                    class="fas fa-plus add_tarefa_projetos"></i>
                                                            </td>
                                                            <td>
                                                                @if($projeto['status_projetos_id'] == 4)
                                                                    <i data-projeto_id="{{ $projeto['id'] }}" style="cursor:pointer; color:#004ad3"
                                                                    class="fas fa-edit add_apontamentos_projetos"></i>
                                                                @endif

                                                            </td>
                                                        </tr>
                                                    @endif
                                                    @if(!empty($permissoes_liberadas) && @(in_array(2, $permissoes_liberadas)))
                                                        <tr class="linha_{{ $projeto['id'] }}" @if(isset($projeto['em_alerta']) && $projeto['em_alerta'] == 1) style="background-color: #F2C807" @endif >
                                                            <th scope="row"><a href={{ URL::route($rotaAlterar, ['id' => $projeto['id']]) }}>{{ $projeto['id'] }}</a></th>
                                                            <td>
                                                                <i data-projeto_id="{{ $projeto['id'] }}" style="cursor:pointer;
                                                                @if (isset($projeto['em_alerta']) && $projeto['em_alerta'] == 1) color:#d9534f @else color:#12ad04 @endif"
                                                                class="fas fa-bell toggle_alerta_projetos"></i>
                                                            </td>
                                                            <td title="{{ trim($projeto['nome_cliente']) }}">{{ strlen(trim($projeto['nome_cliente'])) > 9 ? substr(trim($projeto['nome_cliente']), 0, 9) . '...' : trim($projeto['nome_cliente']) }}</td>
                                                            <td>{{ $projeto['ep'] }}</td>
                                                            <td>{{ \Carbon\Carbon::parse($projeto['data_gerado'])->format('d/m/Y') }}</td>
                                                            <td>{{ $projeto['qtde'] }}</td>
                                                            <td>{{ isset($projeto['prioridade_nome']) ? $projeto['prioridade_nome'] : '' }}</td>
                                                            <td>{{ $projeto['com_pedido'] == 0 ? 'SEM PEDIDO' : ($projeto['com_pedido'] == 1 ? 'COM PEDIDO' : '') }}</td>
                                                            <td>{{ number_format($projeto['valor_unitario_adv'] * $projeto['qtde'], 2, ',', '.') }}</td>
                                                            <td>{{ $projeto['novo_alteracao'] == 0 ? 'NOVO' : ($projeto['novo_alteracao'] == 1 ? 'ALTERAÇÃO' : '') }}</td>
                                                            <td>

                                                                <select class="form-control pesquisa_etapas_projetos"
                                                                @if($projeto['sub_status_projetos_codigo'] != 1) disabled="disabled" @endif
                                                                data-projeto="{{ $projeto['id'] }}" data-sub_status_projetos_codigo="{{ $projeto['sub_status_projetos_codigo'] }}" id="pesquisa_etapas_projetos" name="pesquisa_etapas_projetos">
                                                                    <option @if(empty($projeto['etapas_projetos_id']) ) selected="selected" @endif value=""></option>
                                                                    @if (isset($AllEtapasProjetos))
                                                                        @foreach ($AllEtapasProjetos as $EtapasProjeto)
                                                                            <option value="{{ $EtapasProjeto->id }}"
                                                                                @if (!empty($projeto['etapas_projetos_id']) && $projeto['etapas_projetos_id'] === $EtapasProjeto->id) selected="selected" @endif>{{  $EtapasProjeto->nome }}</option>
                                                                        @endforeach
                                                                    @endif
                                                                </select>
                                                            </td>
                                                            <td>{{ !empty($projeto['data_prazo_entrega']) ? $projeto['data_prazo_entrega'] : '' }}</td>
                                                            <td style="color: {{ $projeto['cor_alerta'] }};">{{ !empty($projeto['alerta_dias']) ? abs($projeto['alerta_dias']) : '' }}</td>
                                                        </tr>
                                                    @endif
                                                    @if(!empty($permissoes_liberadas) && @(in_array(3, $permissoes_liberadas)))
                                                        <tr class="linha_{{ $projeto['id'] }}" @if(isset($projeto['em_alerta']) && $projeto['em_alerta'] == 1) style="background-color: #F2C807" @endif >
                                                            <th scope="row"><a href={{ URL::route($rotaAlterar, ['id' => $projeto['id']]) }}>{{ $projeto['id'] }}</a></th>
                                                            <td>
                                                                <i data-projeto_id="{{ $projeto['id'] }}" style="cursor:pointer;
                                                                @if (isset($projeto['em_alerta']) && $projeto['em_alerta'] == 1) color:#d9534f @else color:#12ad04 @endif"
                                                                class="fas fa-bell toggle_alerta_projetos"></i>
                                                            </td>
                                                            <td title="{{ trim($projeto['nome_cliente']) }}">{{ strlen(trim($projeto['nome_cliente'])) > 9 ? substr(trim($projeto['nome_cliente']), 0, 9) . '...' : trim($projeto['nome_cliente']) }}</td>

                                                            <td>{{ $projeto['ep'] }}</td>
                                                            <td>{{ \Carbon\Carbon::parse($projeto['data_gerado'])->format('d/m/Y') }}</td>
                                                            <td>{{ $projeto['qtde'] }}</td>
                                                            <td>{{ $projeto['blank'] }}</td>
                                                            <td>{{ isset($projeto['prioridade_nome']) ? $projeto['prioridade_nome'] : '' }}</td>
                                                            <td>{{ $projeto['com_pedido'] == 0 ? 'SEM PEDIDO' : ($projeto['com_pedido'] == 1 ? 'COM PEDIDO' : '') }}</td>
                                                            <td>{{ number_format($projeto['valor_unitario_adv'] * $projeto['qtde'], 2, ',', '.') }}</td>
                                                            <td>{{ !isset($projeto['cliente_ativo']) ? '' : ($projeto['cliente_ativo'] == 0 ? 'NÃO' : 'SIM') }}</td>
                                                            <td>{{ $projeto['novo_alteracao'] == 0 ? 'NOVO' : ($projeto['novo_alteracao'] == 1 ? 'ALTERAÇÃO' : '') }}</td>
                                                            <td>

                                                                <select class="form-control pesquisa_etapas_projetos"
                                                                @if($projeto['sub_status_projetos_codigo'] != 1) disabled="disabled" @endif
                                                                data-projeto="{{ $projeto['id'] }}" data-sub_status_projetos_codigo="{{ $projeto['sub_status_projetos_codigo'] }}" id="pesquisa_etapas_projetos" name="pesquisa_etapas_projetos">
                                                                    <option @if(empty($projeto['etapas_projetos_id']) ) selected="selected" @endif value=""></option>
                                                                    @if (isset($AllEtapasProjetos))
                                                                        @foreach ($AllEtapasProjetos as $EtapasProjeto)
                                                                            <option value="{{ $EtapasProjeto->id }}"
                                                                                @if (!empty($projeto['etapas_projetos_id']) && $projeto['etapas_projetos_id'] === $EtapasProjeto->id) selected="selected" @endif>{{  $EtapasProjeto->nome }}</option>
                                                                        @endforeach
                                                                    @endif
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <i data-funcionariomontagem="" title="{{isset($projeto['nome_funcionario']) ? $projeto['nome_funcionario'] : ''}}"
                                                                    data-projeto_id="{{ $projeto['id'] }}" style="cursor:pointer;
                                                                @if ($projeto['nome_funcionario'] !=  '') color:#044f04 @else color:#cacaca @endif"
                                                                class="fas fa-users add_funcionarios_projetos"></i>
                                                            <td>{{ isset($projeto['tempo_projetos']) ? $projeto['tempo_projetos'] : '' }}</td>
                                                            <td>{{ isset($projeto['tempo_programacao']) ? $projeto['tempo_programacao'] : '' }}</td>
                                                            <td>{{ !empty($projeto['data_prazo_entrega']) ? $projeto['data_prazo_entrega'] : '' }}</td>
                                                            <td style="color: {{ $projeto['cor_alerta'] }};">{{ !empty($projeto['alerta_dias']) ? abs($projeto['alerta_dias']) : '' }}</td>
                                                            <td>
                                                                @if (isset($AllSubStatus))

                                                                    <select class="form-control pesquisa_status_id" id="pesquisa_status_id" name="pesquisa_status_id" data-projeto="{{ $projeto['id'] }}">
                                                                        <option value=""></option>
                                                                        @php
                                                                            $grouped = $AllSubStatus->groupBy('status_projeto_nome');
                                                                        @endphp

                                                                        @foreach ($grouped as $groupName => $subStatuses)
                                                                            <optgroup label="{{ $groupName }}">
                                                                                @foreach ($subStatuses as $stats)
                                                                                    <option value="{{ $stats->codigo }}"
                                                                                        @if ((isset($projeto['sub_status_projetos_codigo']) && $projeto['sub_status_projetos_codigo'] == $stats->codigo)) selected="selected" @endif>
                                                                                        {{ $stats->nome }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </optgroup>
                                                                        @endforeach
                                                                    </select>
                                                                @endif
                                                            </td>
                                                            <td title="{{ trim($projeto['transporte']) }}">{{ strlen(trim($projeto['transporte'])) > 20 ? substr(trim($projeto['transporte']), 0, 20) . '...' : trim($projeto['transporte']) }}</td>

                                                            <td>{{ $tempo_desenvolvimento_em_dias }}</td>
                                                            <td>
                                                                {{ !empty($projeto['data_historico']) ? Carbon\Carbon::parse($projeto['data_historico'])->format('d/m/Y') : '' }}
                                                            </td>
                                                            <td title="{{ trim($projeto['mensagem']) }}">{{ strlen(trim($projeto['mensagem'])) > 10 ? substr(trim($projeto['mensagem']), 0, 10) . '...' : trim($projeto['mensagem']) }}</td>
                                                            <td nowrap >
                                                                @if($projeto['compromisso'] == 1)
                                                                    <i class="fas fa-exclamation-triangle" style="color:#d9534f" title="Possui compromisso"></i>
                                                                @endif
                                                                {{ isset($projeto['data_tarefa']) ? $projeto['data_tarefa'].' '  : '' }}
                                                                <i data-projeto_id="{{ $projeto['id'] }}" style="cursor:pointer; color:#12ad04"
                                                                    class="fas fa-plus add_tarefa_projetos"></i>
                                                            </td>
                                                            <td>
                                                                @if($projeto['status_projetos_id'] == 4)
                                                                    <i data-projeto_id="{{ $projeto['id'] }}" style="cursor:pointer; color:#004ad3"
                                                                    class="fas fa-edit add_apontamentos_projetos"></i>
                                                                @endif

                                                            </td>
                                                        </tr>
                                                    @endif

                                                @endif
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            @if(!empty($permissoes_liberadas) && ((in_array(1, $permissoes_liberadas))))
                                            <tr>
                                                <th style="min-width: 50px;"></th>
                                                <th style="min-width: 150px;"></th>
                                                <th style="min-width: 150px;"></th>
                                                <th style="min-width: 100px;"></th>
                                                <th style="min-width: 150px;"></th>
                                                <th style="min-width: 50px;"></th>
                                                <th style="min-width: 100px;"></th>
                                                <th style="min-width: 100px;"></th>
                                                <th style="min-width: 100px;"></th>
                                                <th style="min-width: 150px;"></th>
                                                <th style="min-width: 150px;">{{ $somado_tempo_projeto ?? '' }}</th>
                                                <th style="min-width: 200px;">{{ $somado_tempo_programacao ?? '' }}</th>
                                                <th style="min-width: 150px;"></th>
                                                <th style="min-width: 150px;"></th>
                                                <th style="min-width: 250px;"></th>
                                                <th style="min-width: 250px;"></th>
                                                <th style="min-width: 220px;"></th>
                                                <th style="min-width: 150px;"></th>
                                                <th style="min-width: 150px;"></th>
                                                <th style="min-width: 150px;"></th>
                                                <th style="min-width: 50px;"></th>
                                            </tr>
                                            @endif
                                            @if(!empty($permissoes_liberadas) && ((in_array(3, $permissoes_liberadas))))
                                            <tr>
                                                <th style="min-width: 50px;"></th>
                                                <th style="min-width: 150px;"></th>
                                                <th style="min-width: 150px;"></th>
                                                <th style="min-width: 100px;"></th>
                                                <th style="min-width: 150px;"></th>
                                                <th style="min-width: 50px;"></th>
                                                <th style="min-width: 100px;"></th>
                                                <th style="min-width: 100px;"></th>
                                                <th style="min-width: 100px;"></th>
                                                <th style="min-width: 150px;"></th>
                                                <th style="min-width: 150px;"></th>
                                                <th style="min-width: 150px;"></th>
                                                <th style="min-width: 150px;"></th>
                                                <th style="min-width: 150px;"></th>
                                                <th style="min-width: 150px;">{{ $somado_tempo_projeto ?? '' }}</th>
                                                <th style="min-width: 200px;">{{ $somado_tempo_programacao ?? '' }}</th>
                                                <th style="min-width: 150px;"></th>
                                                <th style="min-width: 150px;"></th>
                                                <th style="min-width: 250px;"></th>
                                                <th style="min-width: 250px;"></th>
                                                <th style="min-width: 220px;"></th>
                                                <th style="min-width: 150px;"></th>
                                                <th style="min-width: 150px;"></th>
                                                <th style="min-width: 150px;"></th>
                                                <th style="min-width: 50px;"></th>
                                            </tr>
                                            @endif
                                        </tfoot>
                                    </table>
                                @endforeach
                            @endif

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
                <form id="form_projetos" action="{{ $rotaAlterar }}" data-parsley-validate=""
                    class="form-horizontal form-label-left" method="post">
                    <div class="form-group row">
                        <label for="codigo" class="col-sm-2 col-form-label">Id</label>
                        <div class="col-sm-2">
                            <input type="text" id="id" name="id" class="form-control col-md-7 col-xs-12"
                                readonly="true"
                                value="@if (isset($projetos[0]->id)) {{ $projetos[0]->id }} @else{{ '' }} @endif">
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
                        value="@if (isset($projetos[0]->os) && $projetos[0]->os) {{ $projetos[0]->os }} @else{{ '' }} @endif">
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
                                    @if (isset($projetos[0]->pessoas_id) && $projetos[0]->pessoas_id == $clientes->id) selected="selected" @else{{ '' }} @endif>
                                    {{ $clientes->codigo_cliente . ' - ' . $clientes->nome_cliente }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="fichatecnica" class="col-sm-2 col-form-label">EP</label>
                <div class="col-sm-2">
                    <input type="text" class="form-control" id="ep" name="ep"
                        value="@if (isset($projetos[0]->ep) && $projetos[0]->ep) {{ $projetos[0]->ep }} @else{{ '' }} @endif">
                </div>
            </div>
            <div class="form-group row">
                <label for="data_gerado" class="col-sm-2 col-form-label">Data Solicitação</label>
                <div class="col-sm-2">
                    <input type="text" class="form-control mask_date" id="data_gerado" name="data_gerado"
                    @if ($tela == 'alterar') readonly='readonly' @else {{''}} @endif
                        value="@if (isset($projetos[0]->data_gerado)) {{ Carbon\Carbon::parse($projetos[0]->data_gerado)->format('d/m/Y') }}@else{{ Carbon\Carbon::now()->format('d/m/Y') }} @endif">
                </div>
            </div>
            <div class="form-group row">
                <label for="qtde" class="col-sm-2 col-form-label">Qtde</label>
                <div class="col-sm-1">
                    <input type="text" class="form-control sonumeros" id="qtde" name="qtde"
                        value="@if (isset($projetos[0]->qtde)) {{ $projetos[0]->qtde }} @else{{ '' }} @endif">
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
                                    @if (isset($projetos[0]->prioridade_id) && $projetos[0]->prioridade_id == $prioridade->id) selected="selected" @else{{ '' }} @endif>
                                    {{ $prioridade->nome }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="com_pedido" class="col-sm-2 col-form-label">Pedido</label>
                <div class="col-sm-10">
                    <select class="form-control col-md-2" id="com_pedido" name="com_pedido">
                        <option value="" @if (isset($projetos[0]->com_pedido) && $projetos[0]->com_pedido == '') {{ ' selected ' }}@else @endif></option>
                        <option value="0" @if (isset($projetos[0]->com_pedido) && $projetos[0]->com_pedido == '0') {{ ' selected ' }}@else @endif>Sem pedido</option>
                        <option value="1" @if (isset($projetos[0]->com_pedido) && $projetos[0]->com_pedido == '1') {{ ' selected ' }}@else @endif>Com pedido</option>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label for="com_pedido" class="col-sm-2 col-form-label">Valor</label>
                <div class="col-sm-2">
                    <input type="text" class="form-control mask_money" id="valor_unitario_adv" name="valor_unitario_adv"
                        value="@if (isset($projetos[0]->valor_unitario_adv)) {{ number_format($projetos[0]->valor_unitario_adv, 2, ',', '.') }} @else{{ '' }} @endif">
                </div>
            </div>

            <div class="form-group row">
                <label for="cliente_ativo" class="col-sm-2 col-form-label">Cliente Ativo</label>
                <div class="col-sm-10">
                    <select class="form-control col-md-2" id="cliente_ativo" name="cliente_ativo">
                        <option value="" @if (isset($projetos[0]->cliente_ativo) && $projetos[0]->cliente_ativo == '') {{ ' selected ' }}@else @endif></option>
                        <option value="0" @if (isset($projetos[0]->cliente_ativo) && $projetos[0]->cliente_ativo == '0') {{ ' selected ' }}@else @endif>NÃO</option>
                        <option value="1" @if (isset($projetos[0]->cliente_ativo) && $projetos[0]->cliente_ativo == '1') {{ ' selected ' }}@else @endif>SIM</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="novo_alteracao" class="col-sm-2 col-form-label">Novo/Alteração</label>
                <div class="col-sm-10">
                    <select class="form-control col-md-2" id="novo_alteracao" name="novo_alteracao">
                        <option value="" @if (isset($projetos[0]->novo_alteracao) && $projetos[0]->novo_alteracao == '') {{ ' selected ' }}@else @endif></option>
                        <option value="0" @if (isset($projetos[0]->novo_alteracao) && $projetos[0]->novo_alteracao == '0') {{ ' selected ' }}@else @endif>NOVO</option>
                        <option value="1" @if (isset($projetos[0]->novo_alteracao) && $projetos[0]->novo_alteracao == '1') {{ ' selected ' }}@else @endif>ALTERAÇÃO</option>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label for="funcionarios_id" class="col-sm-2 col-form-label">Colaborador</label>
                <div class="col-sm-2">
                    <select class="form-control" id="funcionarios_id" name="funcionarios_id">
                        <option value=""></option>
                        @if (isset($AllFuncionarios))
                            @foreach ($AllFuncionarios as $funcionario)
                                <option value="{{ $funcionario->id }}"
                                    @if ((isset($projetos[0]->funcionarios_id) && $projetos[0]->funcionarios_id == $funcionario->id) || ($tela == 'incluir' && $funcionario->id == 1)) selected="selected" @else{{ '' }} @endif>
                                    {{ $funcionario->nome }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="tempo_projetos" class="col-sm-2 col-form-label">Tempo de projeto</label>
                <div class="col-sm-2">
                    <input type="text" class="form-control mask_horas" id="tempo_projetos" name="tempo_projetos" placeholder="HH:MM:SS"
                        value="@if (isset($projetos[0]->tempo_projetos)) {{ $projetos[0]->tempo_projetos }} @else {{ '' }} @endif">
                </div>
            </div>

            <div class="form-group row">
                <label for="tempo_programacao" class="col-sm-2 col-form-label">Tempo de programação</label>
                <div class="col-sm-2">
                    <input type="text" class="form-control mask_horas" id="tempo_programacao" name="tempo_programacao" placeholder="HH:MM:SS"
                        value="@if (isset($projetos[0]->tempo_programacao)) {{ $projetos[0]->tempo_programacao }} @else {{ '' }} @endif">
                </div>
            </div>

            <div class="form-group row">
                <label for="blank" class="col-sm-2 col-form-label">Blank</label>
                <div class="col-sm-2">
                    <input type="text" class="form-control" id="blank" name="blank"
                        value="@if (isset($projetos[0]->blank)) {{ $projetos[0]->blank }} @else {{ '' }} @endif">
                </div>
            </div>

            <div class="form-group row">
                <label for="data_entrega" class="col-sm-2 col-form-label">Prazo entrega</label>
                <div class="col-sm-2">
                    <input type="text" class="form-control mask_date" id="data_entrega" name="data_entrega"
                        value="@if (isset($projetos[0]->data_entrega)) {{ Carbon\Carbon::parse($projetos[0]->data_entrega)->format('d/m/Y') }} @else {{ '' }} @endif">
                </div>
            </div>
            <div class="form-group row">
                <label for="alerta_dias" class="col-sm-2 col-form-label">Alerta dias</label>
                <div class="col-sm-1">
                    <input type="text" readonly class="form-control mask_date" id="alerta_dias" name="alerta_dias">
                </div>
            </div>
            <div class="form-group row">
                <label for="status_id" class="col-sm-2 col-form-label">Status do projeto</label>
                <div class="col-sm-2">
                    <select class="form-control" id="status_id" name="status_id">
                        <option value=""></option>
                        @if (isset($AllSubStatus))
                            @php
                                $grouped = $AllSubStatus->groupBy('status_projeto_nome');
                            @endphp

                            @foreach ($grouped as $groupName => $subStatuses)
                                <optgroup label="{{ $groupName }}">
                                    @foreach ($subStatuses as $stats)
                                        <option value="{{ $stats->codigo }}"
                                            @if ((isset($projetos[0]->sub_status_projetos_codigo) && $projetos[0]->sub_status_projetos_codigo == $stats->codigo) || ($tela == 'incluir' && $stats->codigo == 1)) selected="selected" @endif>
                                            {{ $stats->nome }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="etapa_projeto_id" class="col-sm-2 col-form-label">Etapa do projeto</label>
                <div class="col-sm-2">
                    <select class="form-control" id="etapa_projeto_id" name="etapa_projeto_id">
                        <option value=""></option>
                        @if (isset($AllEtapas))
                            @foreach ($AllEtapas as $etapas)
                                <option value="{{ $etapas->id }}"
                                    @if ((isset($projetos[0]->etapa_projeto_id) && $projetos[0]->etapa_projeto_id == $etapas->id) || ($tela == 'incluir' && $etapas->id == 1)) selected="selected" @else{{ '' }} @endif>
                                    {{ $etapas->nome }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="transporte_id" class="col-sm-2 col-form-label">Transporte</label>
                <div class="col-sm-4">
                    <select class="form-control" id="transporte_id" name="transporte_id">
                        <option value=""></option>
                        @if (isset($transportes))
                            @foreach ($transportes as $transporte)
                                <option value="{{ $transporte->id }}"
                                    @if (isset($projetos[0]->transporte_id) && $projetos[0]->transporte_id == $transporte->id) selected="selected" @else{{ '' }} @endif>
                                    {{ $transporte->nome }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="form-group row ">
                <label for="em_alerta" class="col-sm-2 col-form-label">Alerta</label>
                <select class="form-control custom-select col-md-2 " id="em_alerta" name="em_alerta">
                    <option value="0" @if (isset($projetos[0]->em_alerta) && $projetos[0]->em_alerta == '0'){{ ' selected '}}@else @endif>Sem alerta</option>
                    <option value="1" @if (isset($projetos[0]->em_alerta) && $projetos[0]->em_alerta =='1'){{ ' selected '}}@else @endif>Em alerta</option>
                </select>
            </div>
            <div class="form-group row">
                <label for="status" class="col-sm-2 col-form-label"></label>
                <select class="form-control col-md-1" id="status" name="status">
                    <option value="A" @if (isset($projetos[0]->projeto) && $projetos[0]->projeto == 'A') {{ ' selected ' }}@else @endif>Ativo</option>
                    <option value="I" @if (isset($projetos[0]->projeto) && $projetos[0]->projeto == 'I') {{ ' selected ' }}@else @endif>Inativo</option>
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


@endswitch


