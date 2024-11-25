<?php
use App\Http\Controllers\PedidosController;
?>

@extends('adminlte::page')

@section('title', 'Pro Effect')
<script src="../vendor/jquery/jquery.min.js?cache={{time()}}"></script>
<script src="js/bootstrap.4.6.2.js?cache={{time()}}"></script>
<script src="js/main_custom.js?cache={{time()}}"></script>
<script src="js/jquery.mask.js?cache={{time()}}"></script>
<link rel="stylesheet" href="{{ asset('css/main_style.css') }}" />
<script src="DataTables/datatables.min.js"></script>
<link  rel="stylesheet" src="DataTables/datatables.min.css"></link>
@switch($tela)

    @case('relatorio-previsao-material')
        @section('content_header')
        <div class="form-group row">
            <h1 class="m-0 text-dark col-sm-11 col-form-label">Relatório de {{ $nome_tela }}</h1>
        </div>
        @stop

        @section('content')

        <form id="filtro" action="relatorio-previsao-material" method="get" data-parsley-validate="" class="form-horizontal form-label-left" novalidate="">
            <div class="row" role="main">
                <div class="form-group row col-sm-12">
                    <label for="data" class="col-sm-2 col-form-label text-right">Data: de</label>
                    <div class="col-sm-2">
                        <input type="text" class="form-control mask_date" id="data" name="data" value="{{$request->input('data', '')}}"
                            placeholder="DD/MM/AAAA">
                    </div>
                    <label for="data_fim" class="col-form-label text-right">até</label>
                    <div class="col-sm-2">
                        <input type="text" class="form-control mask_date" id="data_fim" name="data_fim" value="{{$request->input('data_fim', '')}}"
                            placeholder="DD/MM/AAAA">
                    </div>
                    <div class="col-md-5 themed-grid-col " >
                        <div class="row">
                            <label for="ep" class="col-sm-3 col-form-label text-right">Status do pedido</label>
                            <div class="col-sm-8" style="overflow-y: auto; height: 175px; border:1px solid #ced4da; border-radius: .25rem;">
                                <div class="right_col col-sm-6" role="main">
                                    @foreach ($status as $status)
                                    <div class="col-sm-6 form-check">
                                        <input class="form-check-input col-sm-4"  name="status_id[]" id="{{$status->id}}" type="checkbox"
                                        @if($status->id > 4) {{''}} @else {{ 'checked'}}@endif value="{{$status->id}}">
                                        <label class="form-check-label col-sm-6" style="white-space:nowrap" for="{{$status->id}}">{{$status->nome}}</label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row col-sm-12">
                        <label for="lote" class="col-sm-2 col-form-label text-right tipo_consulta ">Tipo de consulta</label>
                        <select class="form-control col-sm-3 tipo_consulta" id="tipo_consulta" name="tipo_consulta">
                            <option value="P" @if($request->input('tipo_consulta') == 'P'){{ ' selected '}}@else @endif>Prevista</option>
                            <option value="E" @if($request->input('tipo_consulta') == 'E'){{ ' selected '}}@else @endif>Executada</option>
                            <option value="V" @if($request->input('tipo_consulta') == 'V'){{ ' selected '}}@else @endif>Valor retroativos</option>
                        </select>
                        <label for="categorias" class="col-sm-2 col-form-label text-right campo_categorias">Categorias</label>
                        <select class="form-control col-sm-3 campo_categorias" id="categorias" name="categorias">
                        @if(!empty($CategoriasMateriais))
                            <option value=""></option>
                            @foreach ($CategoriasMateriais as $categoria)
                                <option value="{{$categoria->id}}" @if($request->input('categorias') == $categoria->id){{ ' selected '}}@else @endif>{{$categoria->nome}}</option>
                            @endforeach
                        @endif
                    </select>
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
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for=""></label>
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_content">
                            <table id="table_material" class="table table-striped  text-center">
                                <thead>
                                    <tr>
                                        <th>Material</th>
                                        <th>Consumo @if($request->input('tipo_consulta') == 'P') previsto @endif unidade</th>
                                        @if($request->input('tipo_consulta') == 'P')
                                            <th>Estoque atual unidade</th>
                                            <th>Diferença unidade</th>
                                            <th>Valor previsto</th>
                                            <th>Alerta</th>
                                        @else
                                            <th>Valor</th>
                                        @endif
                                        <th>Ver ficha técnica</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (isset($materiais))
                                        @foreach ($materiais as $material)
                                            <tr>
                                                <td data-sortable='true' >{{ $material['material'] }}</td>
                                                <td data-sortable='true' >{{ $material['consumo_previsto'] }}</td>
                                                @if($request->input('tipo_consulta') == 'P')
                                                    <td data-sortable='true' >{{ $material['estoque_atual'] }}</td>
                                                    <td data-sortable='true' >{{ $material['diferenca'] }}</td>
                                                    <td data-sortable='true' >{{ $material['valor_previsto'] }}</td>
                                                    <td >{!! $material['alerta'] !!}</td>
                                                @else
                                                    <td data-sortable='true' >{{ $material['valor_previsto'] }}</td>
                                                @endif
                                                <td scope="row">

                                                    <div id='modal_ver_materiais_{{ $material['material_id'] }}'  class="modal" tabindex="-1" role="dialog">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content" style="width: 100%; height: 85%">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id='texto'>Fichas técnicas</h5>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <p>{{ $material['material'] }}</p>
                                                                <div class="modal-body text-Left" style="overflow-y: auto">
                                                                    <label for="" class="col-sm-6 col-form-label "></label>
                                                                    @if (isset($material['os']))
                                                                        @foreach ($material['os'] as $pedido_id)
                                                                            <p>
                                                                                O.S: <a href={{ URL::route('consumo-materiais-detalhes', ['id' => $pedido_id['pedidos_ids']]) }}>{{$pedido_id['os']}}</a>
                                                                                &nbsp;Qtde: <a href={{ URL::route('consumo-materiais-detalhes', ['id' => $pedido_id['pedidos_ids']]) }}>{{$pedido_id['qtde_itens']}}</a>
                                                                                &nbsp;EP: <a href={{ URL::route('consumo-materiais-detalhes', ['id' => $pedido_id['pedidos_ids']]) }}>{{$pedido_id['ep']}}</a>
                                                                                &nbsp;Qtde pedido: <a href={{ URL::route('consumo-materiais-detalhes', ['id' => $pedido_id['pedidos_ids']]) }}>{{$pedido_id['qtde']}}</a>
                                                                            </p>
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                                <div class="modal-footer">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <a class="ver_materiais" data-pedidoid="modal_ver_materiais_{{ $material['material_id'] }}">
                                                        <i style="cursor:pointer;" class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                                @if (!empty($totalizadores))
                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th>{{$totalizadores['consumo_previsto']}}</th>
                                            @if($request->input('tipo_consulta') == 'P')
                                                <th>{{$totalizadores['estoque_atual']}}</th>
                                                <th>{{$totalizadores['diferenca']}}</th>
                                                <th>{{$totalizadores['valor_previsto']}}</th>
                                            @else
                                                <th>{{$totalizadores['valor_previsto']}}</th>
                                            @endif
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @stop

    @break

    @case('valores_retroativos')
    @section('content_header')
    <div class="form-group row">
        <h1 class="m-0 text-dark col-sm-11 col-form-label">Relatório de {{ $nome_tela }}</h1>
    </div>
    @stop

    @section('content')

    <form id="filtro" action="relatorio-previsao-material" method="get" data-parsley-validate="" class="form-horizontal form-label-left" novalidate="">
        <div class="row" role="main">
            <div class="form-group row col-sm-12">
                <label for="data" class="col-sm-2 col-form-label text-right">Data: de</label>
                <div class="col-sm-2">
                    <input type="text" class="form-control mask_date" id="data" name="data" value="{{$request->input('data', '')}}"
                        placeholder="DD/MM/AAAA">
                </div>
                <label for="data_fim" class="col-form-label text-right">até</label>
                <div class="col-sm-2">
                    <input type="text" class="form-control mask_date" id="data_fim" name="data_fim" value="{{$request->input('data_fim', '')}}"
                        placeholder="DD/MM/AAAA">
                </div>
                <div class="col-md-5 themed-grid-col " >
                    <div class="row">
                        <label for="ep" class="col-sm-3 col-form-label text-right">Status do pedido</label>
                        <div class="col-sm-8" style="overflow-y: auto; height: 175px; border:1px solid #ced4da; border-radius: .25rem;">
                            <div class="right_col col-sm-6" role="main">
                                @foreach ($status as $status)
                                <div class="col-sm-6 form-check">
                                    <input class="form-check-input col-sm-4"  name="status_id[]" id="{{$status->id}}" type="checkbox"
                                    @if($status->id > 4) {{''}} @else {{ 'checked'}}@endif value="{{$status->id}}">
                                    <label class="form-check-label col-sm-6" style="white-space:nowrap" for="{{$status->id}}">{{$status->nome}}</label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group row col-sm-12">
                    <label for="tipo_consulta" class="col-sm-2 col-form-label text-right tipo_consulta">Tipo de consulta</label>
                    <select class="form-control col-sm-3 tipo_consulta" id="tipo_consulta" name="tipo_consulta">
                        <option value="P" @if($request->input('tipo_consulta') == 'P'){{ ' selected '}}@else @endif>Prevista</option>
                        <option value="E" @if($request->input('tipo_consulta') == 'E'){{ ' selected '}}@else @endif>Executada</option>
                        <option value="V" @if($request->input('tipo_consulta') == 'V'){{ ' selected '}}@else @endif>Valor retroativos</option>
                    </select>
                    <label for="categorias" class="col-sm-2 col-form-label text-right campo_categorias">Categorias</label>
                    <select class="form-control col-sm-3 campo_categorias" id="categorias" name="categorias">
                        @if(!empty($CategoriasMateriais))
                            <option value=""></option>
                            @foreach ($CategoriasMateriais as $categoria)
                                <option value="{{$categoria->id}}" @if($request->input('categorias') == $categoria->id){{ ' selected '}}@else @endif>{{$categoria->nome}}</option>
                            @endforeach
                        @endif
                    </select>
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
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for=""></label>
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_content">
                        <table id="table_material" class="table table-striped  text-center">
                            <thead>
                                <tr>
                                    <th>Material</th>
                                    <th>Estoque atual unidade</th>
                                    <th>Valor estoque</th>
                                    <th>Entradas</th>
                                    <th>Valor entradas</th>
                                    <th>Consumo</th>
                                    <th>Valor consumido</th>
                                    <th>Ver ficha técnica</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (!empty($dadosMaterialRetroativo))

                                    @foreach ($dadosMaterialRetroativo as $material)

                                        <tr>

                                            <td data-sortable='true' >{{ $material['material'] }}</td>
                                            <td data-sortable='true' >{{ number_format($material['estoque_atual'], 0, '.', '.') }}</td>
                                            <td data-sortable='true' >{{ number_format($material['valor_estoque_atual'], 2, '.', '.') }}</td>
                                            <td data-sortable='true' >{{ number_format($material['entradas'], 0, '.', '.') }}</td>
                                            <td data-sortable='true' >{{ number_format($material['valor_entradas'], 2, '.', '.') }}</td>
                                            <td data-sortable='true' >{{ number_format($material['consumido'], 0, '.', '.') }}</td>
                                            <td data-sortable='true' >{{ number_format($material['valor_consumido'], 2, '.', '.') }}</td>

                                            <td scope="row">
                                                <div id='modal_ver_materiais_{{ $material['material_id'] }}'  class="modal" tabindex="-1" role="dialog">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content" style="width: 100%; height: 85%">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id='texto'>Fichas técnicas</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <p>{{ $material['material'] }}</p>
                                                            <div class="modal-body text-Left" style="overflow-y: auto">
                                                                <label for="" class="col-sm-6 col-form-label "></label>
                                                                @if (isset($material['os']))
                                                                    @foreach ($material['os'] as $pedido_id)
                                                                        <p>
                                                                            O.S: <a href={{ URL::route('consumo-materiais-detalhes', ['id' => $pedido_id['pedidos_ids']]) }}>{{$pedido_id['os']}}</a>
                                                                            &nbsp;Qtde: <a href={{ URL::route('consumo-materiais-detalhes', ['id' => $pedido_id['pedidos_ids']]) }}>{{$pedido_id['qtde_itens']}}</a>
                                                                            &nbsp;EP: <a href={{ URL::route('consumo-materiais-detalhes', ['id' => $pedido_id['pedidos_ids']]) }}>{{$pedido_id['ep']}}</a>
                                                                            &nbsp;Qtde pedido: <a href={{ URL::route('consumo-materiais-detalhes', ['id' => $pedido_id['pedidos_ids']]) }}>{{$pedido_id['qtde']}}</a>
                                                                        </p>
                                                                    @endforeach
                                                                @endif
                                                            </div>
                                                            <div class="modal-footer">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <a class="ver_materiais" data-pedidoid="modal_ver_materiais_{{ $material['material_id'] }}">
                                                    <i style="cursor:pointer;" class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                            @if (!empty($totalizadoresRetroativo))
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th>{{number_format($totalizadoresRetroativo['estoque_atual'], 0 , '.','.')}}</th>
                                        <th>{{number_format($totalizadoresRetroativo['valor_estoque_atual'], 0 , '.','.')}}</th>
                                        <th>{{number_format($totalizadoresRetroativo['entradas'], 0 , '.','.')}}</th>
                                        <th>{{number_format($totalizadoresRetroativo['valor_entradas'], 0 , '.','.')}}</th>
                                        <th>{{number_format($totalizadoresRetroativo['consumido'], 0 , '.','.')}}</th>
                                        <th>{{number_format($totalizadoresRetroativo['valor_consumido'], 0 , '.','.')}}</th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @stop

@break
@endswitch
