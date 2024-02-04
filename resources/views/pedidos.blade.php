@extends('adminlte::page')

@section('title', 'Pro Effect')
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="js/jquery.mask.js"></script>
<script src="js/main_custom.js"></script>

@switch($tela)
    @case('pesquisar')
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

                <form id="filtro" action="pedidos" method="get" data-parsley-validate="" class="form-horizontal form-label-left"
                    novalidate="">
                    <div class="form-group row">
                        <label for="codigo_cliente" class="col-sm-2 col-form-label text-right">Código cliente</label>
                        <div class="col-sm-1">
                            <input type="text" id="codigo_cliente" name="codigo_cliente" class="form-control col-md-13"
                                value="">
                        </div>

                        <label for="nome_cliente" class="col-sm-2 col-form-label text-right">Nome cliente</label>
                        <div class="col-sm-1">
                            <input type="text" id="nome_cliente" name="nome_cliente" class="form-control col-md-13"
                                value="">
                        </div>

                        <label for="blank" class="col-sm-2 col-form-label text-right text-sm-end">Status do pedido</label>
                        <div class="col-sm-4">
                            <select class="form-control" id="status_id" name="status_id">
                                <option value=""></option>
                                @if (isset($status))
                                    @foreach ($status as $status)
                                        <option value="{{ $status->id }}">{{ $status->nome }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="os" class="col-sm-2 col-form-label text-right">OS</label>
                        <div class="col-sm-1">
                            <input type="text" id="os" name="os" class="form-control col-md-13" value="">
                        </div>

                        <label for="ep" class="col-sm-2 col-form-label text-right">EP</label>
                        <div class="col-sm-1">
                            <input type="text" id="ep" name="ep" class="form-control col-md-13" value="">
                        </div>
                        <label for="status" class="col-sm-1 col-form-label"></label>
                        <select class="form-control col-md-1" id="status" name="status">
                            <option value="A" @if (isset($request) && $request->input('status') == 'A'){{ ' selected '}}@else @endif>Ativo</option>
                            <option value="I" @if (isset($request) && $request->input('status')  == 'I'){{ ' selected '}}@else @endif>Inativo</option>
                        </select>
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
                                <h4>Encontrados</h4>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>OS</th>
                                            <th>EP</th>
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
                                        {{-- {{dd($pedidos)}} --}}
                                        @if (isset($pedidos))
                                            @foreach ($pedidos as $pedido)
                                                <tr>
                                                    <th scope="row"><a
                                                            href={{ URL::route($rotaAlterar, ['id' => $pedido->id]) }}>{{ $pedido->id }}</a>
                                                    </th>
                                                    <td>{{ $pedido->os }}</td>
                                                    <td>{{ $pedido->ep }}</td>
                                                    <td>{{ $pedido->nome_cliente }}</td>
                                                    <td>{{ $pedido->nome }}</td>
                                                    <td>{{ Carbon\Carbon::parse($pedido->data_gerado)->format('d/m/Y') }}</td>
                                                    <td>{{ Carbon\Carbon::parse($pedido->data_entrega)->format('d/m/Y') }}</td>
                                                    <td class="@if (\Carbon\Carbon::createFromDate(date('Y-m-d'))->diffInDays(\Carbon\Carbon::createFromDate($pedido->data_entrega)) < 5 ) text-danger @else text-primary @endif" >
                                                        {{ \Carbon\Carbon::createFromDate(date('Y-m-d'))->diffInDays(\Carbon\Carbon::createFromDate($pedido->data_entrega)) }}
                                                   </td>
                                                    <th scope="row" title="Imprimir ordem de serviço">
                                                        <a  onclick="createPopupWin('{{ URL::route('imprimirOS', ['id' => $pedido->id]) }}', 'Impressão de OS', 1200, 650)">
                                                        <span class="fa fa-print"></span></a>
                                                    </th>
                                                    <th scope="row">
                                                        <a  onclick="createPopupWin('{{ URL::route('imprimirMP', ['id' => $pedido->id]) }}', 'Impressão de OS', 1200, 650)">
                                                        <span class="fa fa-print"></span></a>
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
        <form id="alterar" action="{{ $rotaAlterar }}" data-parsley-validate="" class="form-horizontal form-label-left"
        novalidate="" method="post">
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
                        class="form-horizontal form-label-left" novalidate="" method="post">
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
                    <select class="form-control" id="fichatecnica" name="fichatecnica">
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
                    <input type="text" class="form-control" id="qtde" name="qtde"
                        value="@if (isset($pedidos[0]->qtde)) {{ $pedidos[0]->qtde }}@else{{ '' }} @endif">
                </div>
            </div>
            <div class="form-group row">
                <label for="data_gerado" class="col-sm-2 col-form-label">Data gerado</label>
                <div class="col-sm-2">
                    <input type="text" class="form-control mask_date" id="data_gerado" name="data_gerado"
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
                            @foreach ($status as $status)
                                <option value="{{ $status->id }}"
                                    @if ((isset($pedidos[0]->status_id) && $pedidos[0]->status_id == $status->id) || (($tela == 'incluir') && $status->id == 1)) selected="selected" @else{{ '' }} @endif>
                                    {{ $status->nome }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="observacao" class="col-sm-2 col-form-label">Observações</label>
                <div class="col-sm-6">
                    <textarea class="form-control" id="observacao" name="observacao">@if (isset($pedidos[0]->observacao)){{trim($pedidos[0]->observacao)}}@else{{''}}@endif</textarea>
                </div>
            </div>
            @if(!empty($historicos))
            <div class="form-group row">
                <label for="observacao" class="col-sm-2 col-form-label">Historico</label>
                <div class="col-sm-8">
                    <div class="d-flex p-2 bd-highlight overflow-auto">
                        @foreach ($historicos as $historico)
                            {{ '[' . \Carbon\Carbon::parse($historico->created_at)->format('d/m/Y h:i:s') .'] ' . $historico->historico}}</br>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
            <div class="form-group row">
                <label for="status" class="col-sm-2 col-form-label"></label>
                <select class="form-control col-md-1" id="status" name="status">
                    <option value="A" @if (isset($pedidos[0]->status) && $pedidos[0]->status == 'A'){{ ' selected '}}@else @endif>Ativo</option>
                    <option value="I" @if (isset($pedidos[0]->status) && $pedidos[0]->status =='I'){{ ' selected '}}@else @endif>Inativo</option>
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

                        <label for="blank" class="col-sm-2 col-form-label text-right text-sm-end">Status do pedido</label>
                        <div class="col-sm-4">
                            <select class="form-control" id="status_id" name="status_id">
                                <option value=""></option>
                                @if (isset($status))
                                    @foreach ($status as $status)
                                        <option value="{{ $status->id }}">{{ $status->nome }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-2 text-center">
                            <label for="data_gerado">Data gerado inicial</label>
                            <input type="text" class="form-control mask_date" id="data_gerado" name="data_gerado"
                                placeholder="DD/MM/AAAA">
                        </div>
                        <div class="form-group col-md-2 text-center">
                            <label for="data_gerado_fim">Data gerado final</label>
                            <input type="text" class="form-control mask_date" id="data_gerado_fim" name="data_gerado_fim"
                                placeholder="DD/MM/AAAA">
                        </div>
                        <div class="form-group col-md-2 text-center">
                        </div>
                        <div class="form-group col-md-2 text-center">
                            <label for="data_entrega">Data gerado inicial</label>
                            <input type="text" class="form-control mask_date" id="data_entrega" name="data_entrega"
                                placeholder="DD/MM/AAAA">
                        </div>
                        <div class="form-group col-md-2 text-center">
                            <label for="data_entrega_fim">Data gerado final</label>
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
                                    <form id="filtro" action="followup-detalhes" method="post" data-parsley-validate=""
                                        class="form-horizontal form-label-left" novalidate="">
                                        @csrf <!--{{ csrf_field() }}-->
                                        <input type="hidden" id="pedidos_encontrados" name="pedidos_encontrados"
                                            value="{{ json_encode($pedidos_encontrados) }}">
                                        <div class="col-sm-5">
                                            <button type="submit" class="btn btn-primary"><span
                                                    class="far fa-fw fa-calendar"></span> Visualizar followups</button>
                                        </div>
                                        <div class="clearfix"></div>
                                    </form>
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
                <h1 class="m-0 text-dark col-sm-6 col-form-label">Tela de followup</h1>
            </div>
        @stop
        @section('content')
            @if (isset($dados_pedido_status))
                @foreach ($dados_pedido_status as $key => $dado_pedido_status)
                    <label for="codigo" class="col-sm-10 col-form-label">Status do Pedido: {{ Str::upper($key) }} </label>
                    <div class="form-group row">
                        <table class="table table-sm table-striped " id="table_composicao">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col">OS</th>
                                    <th scope="col">Usinagem</th>
                                    <th scope="col">Acabamento</th>
                                    <th scope="col">Montagem</th>
                                    <th scope="col">Inspeção</th>
                                    <th scope="col">Data entrega</th>
                                    <th scope="col">Alerta de dias</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- {{dd($dado_pedido_status)}} --}}
                               @foreach ($dado_pedido_status['classe'] as $pedido)
                                    <tr>
                                        <td>{{ $pedido->os }}</td>
                                        <td>{{ $dado_pedido_status['pedido'][$pedido->id]['usinagem'] }}</td>
                                        <td>{{ $dado_pedido_status['pedido'][$pedido->id]['acabamento']  }}</td>
                                        <td>{{ $dado_pedido_status['pedido'][$pedido->id]['montagem']  }}</td>
                                        <td>{{ $dado_pedido_status['pedido'][$pedido->id]['inspecao']  }}</td>
                                        <td>{{ \Carbon\Carbon::parse($pedido->data_entrega)->format('d/m/Y')}}</td>
                                        <td class="@if (\Carbon\Carbon::createFromDate(date('Y-m-d'))->diffInDays(\Carbon\Carbon::createFromDate($pedido->data_entrega)) < 5 ) text-danger @else text-primary @endif" >
                                             {{ \Carbon\Carbon::createFromDate(date('Y-m-d'))->diffInDays(\Carbon\Carbon::createFromDate($pedido->data_entrega)) }}
                                        </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col">{{$dado_pedido_status['totais']['total_tempo_usinagem']}}</th>
                                    <th scope="col">{{$dado_pedido_status['totais']['total_tempo_acabamento']}}</th>
                                    <th scope="col">{{$dado_pedido_status['totais']['total_tempo_montagem']}}</th>
                                    <th scope="col">{{$dado_pedido_status['totais']['total_tempo_inspecao']}}</th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                </tr>
                                <tr>
                                    <th scope="col">Maquinas/pessoas</th>
                                    <th scope="col">{{$dado_pedido_status['maquinas_usinagens']}}</th>
                                    <th scope="col">{{$dado_pedido_status['pessoas_acabamento']}}</th>
                                    <th scope="col">{{$dado_pedido_status['pessoas_montagem']}}</th>
                                    <th scope="col">{{$dado_pedido_status['pessoas_inspecao']}}</th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
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

@endswitch
