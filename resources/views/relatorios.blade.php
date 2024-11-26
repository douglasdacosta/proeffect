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

            @include('relatoriosPesquisa')

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

    @case('entrada_por_periodo')
        @section('content_header')
        <div class="form-group row">
            <h1 class="m-0 text-dark col-sm-11 col-form-label">Relatório de {{ $nome_tela }}</h1>
        </div>
        @stop

        @section('content')

            @include('relatoriosPesquisa')

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for=""></label>
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_content">
                            <table id="table_material" class="table table-striped  text-center">
                                <thead>
                                    <tr>
                                        <th>Material</th>
                                        <th>Estoque na data</th>
                                        <th>Valor estoque</th>

                                        @if ($request->input('tipo_consulta') == 'V')
                                            <th>Entradas</th>
                                            <th>Valor entradas</th>
                                        @endif

                                        @if ($request->input('tipo_consulta') == 'C')
                                            <th>Consumo</th>
                                            <th>Valor consumido</th>
                                        @endif

                                        <th>Ver ficha técnica</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (!empty($array_materiais))

                                        @foreach ($array_materiais as $material)

                                            <tr>
                                                <td data-sortable='true' >{{ $material['material'] }}</td>
                                                <td data-sortable='true' >{{ number_format($material['estoque_atual'], 0, '.', '.') }}</td>
                                                <td data-sortable='true' >{{ number_format($material['valor_estoque_atual'], 2, '.', '.') }}</td>

                                                @if ($request->input('tipo_consulta') == 'V')
                                                    <td data-sortable='true' >{{ number_format($material['entradas'], 0, '.', '.') }}</td>
                                                    <td data-sortable='true' >{{ number_format($material['valor_entradas'], 2, '.', '.') }}</td>
                                                @endif

                                                @if ($request->input('tipo_consulta') == 'C')
                                                    <td data-sortable='true' >{{ number_format($material['consumido'], 0, '.', '.') }}</td>
                                                    <td data-sortable='true' >{{ number_format($material['valor_consumido'], 2, '.', '.') }}</td>
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
