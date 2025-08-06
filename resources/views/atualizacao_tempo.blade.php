<!DOCTYPE html>
<?php
    use \App\Http\Controllers\AtualizacaoTemposController;
    use \App\Http\Controllers\MaquinasController;
    use \App\Http\Controllers\PedidosController;
    use \App\Models\Maquinas;

?>

@extends('adminlte::page')

@section('title', 'Pro Effect')
@section('adminlte_css')
    <link  rel="stylesheet" src="DataTables/datatables.min.css"></link>
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}" />
    <link rel="stylesheet" href="{{asset('css/main_style.css')}}" />

@stop

<script src="../vendor/jquery/jquery.min.js"></script>
<script src="js/jquery.mask.js"></script>
<script src="js/main_custom.js"></script>
<script src="js/bootstrap.4.6.2.js"></script>
<script src="js/select2.min.js"></script>
<script src="DataTables/datatables.min.js"></script>


@if(isset($tela) and $tela == 'pesquisa')
    @section('content_header')
    <div class="form-group row">
        <h1 class="m-0 text-dark col-sm-11 col-form-label">{{ $nome_tela }}</h1>
    </div>
    @stop
    @section('content')

    <div id='modal_detalhes'  class="modal" tabindex="-1" role="dialog" style="width: 70%;">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-80w" role="document" >
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title" id='texto_status_caixas'>Detalhes de lançamentos <span id="texto_ep"></span> OS <span id="texto_os"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>
                    <div class="modal-body" >
                        <div class="row">
                            <div class="col-sm-12">
                                <table id="tabela_responsaveis" class="table table-striped text-center ">
                                    <thead>
                                        <tr>
                                            <th>Responsável</th>
                                            <th>Status</th>
                                            <th>Detalhes</th>
                                            <th>Data e hora</th>
                                            <th>Editar linha</th>
                                            <th>Adicionar</th>
                                            <th>Excluir</th>
                                            <th>Salvar</th>

                                        </tr>
                                    </thead>
                                    <tbody id="detalhes_lancamentos">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="salva_fila_impressao" data-dismiss="modal" >Salvar</button>
                    </div> --}}
                </div>
            </div>
        </div>

    <div class="right_col" role="main">
        <form id="filtro" action="atualizacao_tempo" method="get" data-parsley-validate=""
                class="form-horizontal form-label-left" novalidate="">
                <div class="form-group row">
                    <label for="ep" class="col-sm-2 col-form-label text-right">EP</label>
                    <div class="col-sm-2">
                        <input type="text" id="ep" name="ep" class="form-control col-md-7 col-xs-12"
                            value="@if (isset($request) && $request->input('ep') != '') {{ $request->input('ep') }}@else @endif">
                    </div>
                    <label for="os" class="col-sm-2 col-form-label text-right">OS</label>
                    <div class="col-sm-2">
                        <input type="text" id="os" name="os" class="form-control col-md-7 col-xs-12"
                            value="@if (isset($request) && $request->input('os') != '') {{ $request->input('os') }}@else @endif">
                    </div>
                    <label for="responsavel" class="col-sm-1 col-form-label">Responsável</label>
                    <select class="form-control col-sm-2 default-select2" id="responsavel" name="responsavel">
                        <option value=" ">Selecione</option>
                        @foreach ($funcionarios as $funcionario)
                            @php $array_funcionarios[] = $funcionario->nome; @endphp
                            @if (isset($request) && $request->input('responsavel') == $funcionario->nome)
                                <option value="{{ $funcionario->nome }}" selected>{{ $funcionario->nome }}</option>
                            @else
                                <option value="{{ $funcionario->nome }}">{{ $funcionario->nome }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <input type="hidden" name="array_funcionarios" id="array_funcionarios" value="{{ json_encode($array_funcionarios) }}">

                    <div class="form-group row">
                        <label for="data_apontamento" class="col-sm-2 col-form-label text-right">Data apontamento: de</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control mask_date" id="data_apontamento" name="data_apontamento"
                                placeholder="DD/MM/AAAA" value="@if (isset($request) && $request->input('data_apontamento') != '') {{ $request->input('data_apontamento') }}@else @endif">
                        </div>
                        <label for="data_apontamento_fim" class=" col-form-label text-right">até</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control mask_date" id="data_apontamento_fim" name="data_apontamento_fim"
                                placeholder="DD/MM/AAAA" value="@if (isset($request) && $request->input('data_apontamento_fim') != '') {{ $request->input('data_apontamento_fim') }}@else @endif">
                        </div>


                        <div class="row col-sm-4">
                            <label for="ep" class="col-sm-3 col-form-label text-right status_pedido">Departamento</label>
                            <div class="col-sm-7 status_pedido" style="overflow-y: auto; height: 75px; border:1px solid #ced4da; border-radius: .25rem;">
                                <div class="right_col col-sm-6" role="main">

                                        <div class="col-sm-6 form-check">
                                            <input class="form-check-input col-sm-3" @if(isset($request) && $request->input('departamento') == 'MA') {{' checked="checked" '}} @endif name="departamento" id="montagem_agulha"  type="radio" value="MA">
                                            <label class="form-check-label col-sm-1"  style="white-space:nowrap" for="montagem_agulha">Montagem Agulha</label>
                                        </div>
                                        <div class="col-sm-6 form-check">
                                            <input class="form-check-input col-sm-3" @if(isset($request) && $request->input('departamento') == 'MT') {{' checked="checked" '}} @endif name="departamento" id="montagem_torre" type="radio" value="MT">
                                            <label class="form-check-label col-sm-1"  style="white-space:nowrap" for="montagem_torre">Montagem Torre</label>
                                        </div>
                                        <div class="col-sm-6 form-check">
                                            <input class="form-check-input col-sm-3" @if(isset($request) && $request->input('departamento') == 'I') {{' checked="checked" '}} @endif name="departamento" id="inspecao" type="radio" value="I">
                                            <label class="form-check-label col-sm-1"  style="white-space:nowrap" for="inspecao">Inspeção</label>
                                        </div>
                                </div>
                            </div>
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

          <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
              <div class="x_title">
                <div class="clearfix"></div>
              </div>
              <div class="x_content">
                <table id="table_atualizacao_tempo" class="table table-striped text-center">
                  <thead>
                    <tr>
                      <th data-sortable='true'>EP</th>
                      <th data-sortable='true'>OS</th>
                      <th data-sortable='true'>Qtde</th>
                      <th data-sortable='true'>Data início</th>
                      <th data-sortable='true'>Data término</th>
                      <th data-sortable='true'>Colaborador</th>
                      <th data-sortable='true'>Departamento</th>
                      <th data-sortable='true'>Tempo cadastrado</th>
                      <th data-sortable='true'>Tempo total Apont.</th>
                      <th data-sortable='true'>Tempo peça Apont.</th>
                      <th data-sortable='true'>Alerta</th>
                      <th >Detalhes</th>
                      <th >Ação</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php
                        $tempo_somado_total = 0;
                        $tempo_default_total = 0;
                        $tempoTotalApontamentoSomado = 0;
                        $AtualizacaoTempoController = new AtualizacaoTemposController();
                    @endphp
                  @if(isset($pedidos))

                        @foreach ($pedidos as $pedido)
                            @if($pedido->quantidade_responsavel==0)
                                @continue
                            @endif
                            <tr>
                            <td data-field="ep" scope="row">{{$pedido->ep}}</td>
                            <td data-field="os">{{$pedido->os}}</td>
                            <td data-field="qtde">{{$pedido->qtde}}</td>
                            <td data-field="data_inicio">{{$pedido->data_inicio}}</td>
                            <td data-field="data_fim">{{$pedido->data_fim}}</td>
                            {{-- //somente exibe 25 caracteres --}}
                            <td data-field="colaborador" title="{{$pedido->colaborador}}"
                                @if($pedido->quantidade_responsavel > 1) class="bg-warning" @endif
                            >{{ Str::limit($pedido->colaborador, 20) }}</td>
                            @if($pedido->id_status == 6 && $pedido->select_tipo_manutencao == 'T')
                                <td data-field="departamento">{{$pedido->departamento . ' - Torre' }}</td>
                            @elseif($pedido->id_status == 6 && ($pedido->select_tipo_manutencao == 'A' || empty($pedido->select_tipo_manutencao)))
                                <td data-field="departamento">{{$pedido->departamento . ' - Agulha' }}</td>
                            @else
                                <td data-field="departamento">{{$pedido->departamento}}</td>
                            @endif
                            <td data-field="tempo_default">{{$pedido->tempo_default}}</td>
                                @php


                                    $segundos_somado = $AtualizacaoTempoController->converteTempoParaInteiro($pedido->tempo_somado) / $pedido->qtde;
                                    $tempo_somado = $AtualizacaoTempoController->formatSeconds($segundos_somado);

                                    $tempoTotalApontamento = $pedido->tempo_somado;
                                    $tempoTotalApontamentoSomado += $AtualizacaoTempoController->converteTempoParaInteiro($pedido->tempo_somado);
                                    $pedido->tempo_somado = $tempo_somado
                                @endphp
                            <td data-field="tempo_total_apontamento">{{$tempoTotalApontamento}}</td>
                            <td data-field="tempo_somado">{{$pedido->tempo_somado}}</td>
                            <td >
                                @php
                                    $tempo_default = $AtualizacaoTempoController->converteTempoParaInteiro($pedido->tempo_default);
                                    $tempo_default_total += $tempo_default;
                                    $tempo_somado = $AtualizacaoTempoController->converteTempoParaInteiro($pedido->tempo_somado);
                                    $tempo_somado_total += $segundos_somado;
                                @endphp
                                @if($tempo_somado < $tempo_default)
                                    <i class="text-success fas fa-arrow-up"></i>
                                @else
                                    <i class="text-danger fas fa-arrow-down"></i>
                                @endif
                            </td>
                            <td>
                                <a class="btn btn-info btn-sm ver-detalhes" data-id="{{$pedido->id}}" data-ep="{{$pedido->ep}}" data-os="{{$pedido->os}}" data-responsavel="{{$request->input('responsavel')}}" data-status_id="{{$pedido->id_status}}" >Detalhes</a>
                            </td>
                            <td>
                                <a class="btn btn-success btn-sm aplicar-valores" data-ep='{{$pedido->ep}}' data-status_id="{{$request->input('departamento')}}" data-tempo_aplicar='{{$pedido->tempo_somado}}'>APLICAR</a>
                            </td>
                            </tr>
                        @endforeach
                    @endif
                  </tbody>
                  <tfoot>
                    <tr>
                      <th></th><th></th><th></th><th></th><th></th><th></th><th></th>
                      <th>{{$AtualizacaoTempoController->formatSeconds($tempo_default_total)}}</th>
                      <th>{{$AtualizacaoTempoController->formatSeconds($tempoTotalApontamentoSomado)}}</th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                    </tr>
                    <tr>
@php
                $PedidosController = new PedidosController();

                $Maquinas = new Maquinas();
                $maquinas = $Maquinas->get();
                $horas_dia =$maquinas[0]->horas_dia;
                $tempo_default_total = $AtualizacaoTempoController->formatSeconds($tempo_default_total);
                $dias_default_total = $PedidosController->divideHoursAndReturnWorkDays($tempo_default_total, $horas_dia);

                $tempoTotalApontamentoSomado = $AtualizacaoTempoController->formatSeconds($tempoTotalApontamentoSomado);
                $dias_total_apontamento = $PedidosController->divideHoursAndReturnWorkDays($tempoTotalApontamentoSomado, $horas_dia);
@endphp

                      <th></th><th></th><th></th><th></th><th></th><th></th><th></th>
                      <th>{{$dias_default_total}}</th>
                      <th>{{$dias_total_apontamento}}</th>
                      <th>{{''}}</th>
                      <th></th>
                      <th></th>
                      <th></th>
                    </tr>
                </table>
              </div>
            </div>
          </div>
        </div>
    </div>

    @stop
@else
@section('content')
        @if($tela == 'alterar')
            @section('content_header')
                <h1 class="m-0 text-dark">Alteração de {{ $nome_tela }}</h1>
            @stop
            <form id="alterar" action="{{$rotaAlterar}}" data-parsley-validate="" class="form-horizontal form-label-left" novalidate="" method="post">
            <div class="form-group row">
                <label for="codigo" class="col-sm-2 col-form-label">Id</label>
                <div class="col-sm-2">
                <input type="text" id="id" name="id" class="form-control col-md-7 col-xs-12" readonly="true" value="@if (isset($status[0]->id)){{$status[0]->id}}@else{{''}}@endif">
                </div>
            </div>
        @else
            @section('content_header')
                <h1 class="m-0 text-dark">Inclusão de {{ $nome_tela }}</h1>
            @stop
            <form id="incluir" action="{{$rotaIncluir}}" data-parsley-validate="" class="form-horizontal form-label-left" novalidate="" method="post">
        @endif
            @csrf <!--{{ csrf_field() }}-->
            <div class="form-group row">
                <label for="nome" class="col-sm-2 col-form-label">Nome</label>
                <div class="col-sm-6">
                <input type="text" class="form-control" id="nome"  name="nome" value="@if (isset($status[0]->nome)){{$status[0]->nome}}@else{{''}}@endif">
                </div>
            </div>
            <div class="form-group row">
                <label  class="col-sm-2 col-form-label"> </label>
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="alertacliente" name="alertacliente" @if (!isset($status[0]->alertacliente) || $status[0]->alertacliente == 1) checked @else{{''}}@endif>
                    <label class="custom-control-label" for="alertacliente">Alerta cliente (ao mudar o status do pedido)</label>
                </div>
            </div>
            <div class="form-group row">
                <label for="status" class="col-sm-2 col-form-label"></label>
                <select class="form-control col-md-1" id="status" name="status">
                    <option value="A" @if (isset($status[0]->status) && $status[0]->status == 'A'){{ ' selected '}}@else @endif>Ativo</option>
                    <option value="I" @if (isset($status[0]->status) && $status[0]->status =='I'){{ ' selected '}}@else @endif>Inativo</option>
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
@endif
