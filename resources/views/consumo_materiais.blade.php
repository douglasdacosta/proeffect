<?php use \App\Http\Controllers\PedidosController; ?>
@extends('adminlte::page')

@section('title', 'Pro Effect')
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="js/jquery.mask.js"></script>
<script src="js/main_custom.js"></script>
<link rel="stylesheet" href="{{asset('css/main_style.css')}}" />
@switch($tela)
    @case('pesquisar')
        @section('content_header')
            <div class="form-group row">
                <h1 class="m-0 text-dark col-sm-11 col-form-label">Pesquisa de {{ $nome_tela }}</h1>
                <div class="col-sm-1">
                </div>
            </div>
        @stop
        @section('content')
        <div id="blocker">
                <div class="d-flex justify-content-center" style="position: fixed;  margin-top: 17%;  margin-left: 41%;">
                    <span class="visually-hidden">Calculando Materiais...    </span>
                    <div class="spinner-border" role="status">
                    </div>
                  </div>
        </div>        
        <div id="toastsContainerTopRight" class="toasts-top-right fixed">
            <div class="toast fade show" role="alert" style="width: 350px" aria-live="assertive"
                aria-atomic="true">
                <div class="toast-header">
                    <strong class="mr-auto">Alerta!</strong>
                    <small></small>
                    <button data-dismiss="toast" type="button" class="ml-2 mb-1 close" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="toast-body textoAlerta"
                    style="text-decoration-style: solid; font-weight: bold; font-size: larger;"></div>
            </div>
        </div>
        <div class="right_col" role="main">

            <form id="filtro" action="consumo-materiais" method="get" data-parsley-validate="" class="form-horizontal form-label-left"
                novalidate="">
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
                    <label  for="data_entrega" class="col-sm-2 col-form-label text-right">Data entrega: de</label>
                    <div class="col-sm-1">
                        <input type="text" class="form-control mask_date" id="data_entrega" name="data_entrega"
                            placeholder="DD/MM/AAAA">
                    </div>
                    <label for="data_entrega_fim" class=" col-form-label text-right">até</label>
                    <div class="col-sm-1">
                        <input type="text" class="form-control mask_date" id="data_entrega_fim" name="data_entrega_fim"
                            placeholder="DD/MM/AAAA">
                    </div>
                    <label for="status" class="col-sm-1 col-form-label">&nbsp;</label>
                    <div class="col-sm-2">
                    <select class="form-control col-md-5" id="status" name="status">
                        <option value="A" @if (isset($request) && $request->input('status') == 'A'){{ ' selected '}}@else @endif>Ativo</option>
                        <option value="I" @if (isset($request) && $request->input('status')  == 'I'){{ ' selected '}}@else @endif>Inativo</option>
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
                                        <th>Cliente</th>
                                        <th>Status do pedido</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- {{dd($pedidos)}} --}}
                                    @if (isset($pedidos))
                                        @foreach ($pedidos as $pedido)
                                            <tr>
                                                <th scope="row"><a class="pesquisar_materiais"
                                                        href={{ URL::route($rotaAlterar, ['id' => $pedido->id]) }}>{{ $pedido->id }}</a>
                                                </th>
                                                <td>{{ $pedido->os }}</td>
                                                <td>{{ $pedido->ep }}</td>
                                                <td>{{ $pedido->nome_cliente }}</td>
                                                <td>{{ $pedido->nome_status}}</td>
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

    @case('detalhes')
        @section('content_header')
            <div class="form-group row">
                <h1 class="m-0 text-dark col-sm-6 col-form-label">Detalhes de consumo de materiais</h1>

            </div>
        @stop
        @section('content')
            @if (isset($pedidos))
            @foreach ($pedidos as $key => $pedido)
                <div class="form-group row">
                    <table class="table table-sm table-striped text-center" id="table_composicao">
                        <thead class="">
                            <tr>
                                <th scope="col">OS</th>
                                <th scope="col">EP</th>
                                <th scope="col">Qtde do pedido</th>
                                <th scope="col">Data entrega</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="col">{{$pedido->os}}</th>
                                <th scope="col">{{$pedido->ep}}</th>
                                <th scope="col">{{$pedido->qtde}}</th>
                                <th scope="col">{{ Carbon\Carbon::parse($pedido->data_entrega)->format('d/m/Y') }}</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @endforeach
        @endif

        @if (isset($materiais))
            <div class="form-group row">
                <table class="table table-sm table-striped " id="table_composicao">
                    <thead class="">
                        <tr>
                            <th scope="col">Material</th>
                            <th scope="col">Tamanho placa</th>
                            <th scope="col">Blank</th>
                            <th scope="col">Qtde Blank</th>
                            <th scope="col">Medida Blank</th>
                            <th scope="col">Espessura</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($materiais as $key => $materiai)
                        <tr>
                            <td scope="col">{{$materiai->nome_material}}</td>
                            <td scope="col">@if ($materiai->unidadex != '') {{$materiai->unidadex . 'x'. $materiai->unidadey}}  @else {{''}} @endif</td>
                            <td scope="col">{{$materiai->blank}}</td>
                            <td scope="col">{{$materiai->qtde_blank}}</td>
                            <td scope="col">@if ($materiai->medidax != '') {{$materiai->medidax . 'x'. $materiai->mediday}} @else {{''}} @endif</td>
                            <td scope="col">{{$materiai->espessura}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif


            
                    <div class="form-group row">
                        <h3 class="text-dark ">Total de materiais</h1>
                    </div>
                        @if (!empty($totais_calculados))
                            <table class="table table-sm table-striped  text-center" id="table_composicao">
                                <thead class="">
                                    <tr>
                                        <th scope="col">Material</th>
                                        <th scope="col">Medida placa</th>
                                        <th scope="col">Espessura</th>
                                        <th scope="col">Valor unitário</th>
                                        <th scope="col" title="Placas necessárias">Qtde/Placas</th>
                                        <th scope="col">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach ($totais_calculados as $total_calculado)
                                    <tr>
                                        <td scope="col">{{$total_calculado['nome_material']}}</td>
                                        <td scope="col">@if(!empty($total_calculado['tamanho_chapa'])) {{$total_calculado['tamanho_chapa'].'mm'}} @else {{''}} @endif</td>
                                        <td scope="col">{{$total_calculado['espessura']}}</td>
                                        <td scope="col">{{$total_calculado['valor_unitario']}}</td>
                                        <td scope="col">{{$total_calculado['quantidade_chapas']}}</td>
                                        <td scope="col">{{$total_calculado['valor_total']}}</td>
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                        <th scope="col">{{'R$ ' .$total_somado}}</th>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="form-group row">
                                <input type="hidden" value="{{$imprimir}}" id="imprimir" >
                            </div>
            @endif
        @stop
    @break
@endswitch
