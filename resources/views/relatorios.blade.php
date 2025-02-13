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
                            @if (!empty($materiais))
                                <table id="table_material" class="table table-striped  text-center">
                                    <thead>
                                        <tr>
                                            <th>Material</th>
                                            @if ($request->input('tipo_consulta') == 'V')
                                                {{-- <th>Estoque na data</th>
                                                <th>Valor estoque</th> --}}
                                                <th>Entradas</th>
                                                <th>Valor entradas</th>
                                            @endif

                                            @if ($request->input('tipo_consulta') == 'C')
                                                {{-- <th>Estoque na data</th>
                                                <th>Valor estoque</th> --}}
                                                <th>Consumo</th>
                                                <th>Valor consumido</th>
                                            @endif

                                            @if ($request->input('tipo_consulta') == 'ED')
                                                <th>Estoque na data</th>
                                                <th>Valor estoque</th>
                                            @endif

                                            @if ($request->input('tipo_consulta') == 'EEC')
                                                <th>Estoque na data</th>
                                                <th>Valor estoque</th>
                                                <th>Entradas</th>
                                                <th>Valor entradas</th>
                                                <th>Consumo</th>
                                                <th>Valor consumido</th>
                                            @endif

                                            @if(in_array($request->input('tipo_consulta'), ['P', 'E']))
                                                <th>Ver ficha técnica</th>
                                            @else
                                            <th>Ver Estoques</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>


                                        @foreach ($materiais as $material)

                                            <tr>
                                                <td data-sortable='true' >{{ $material['material'] }}</td>
                                                @if ($request->input('tipo_consulta') == 'V')
                                                    {{-- <td data-sortable='true' >{{ number_format($material['estoque_atual'], 0, '.', '.') }}</td>
                                                    <td data-sortable='true' >{{ number_format($material['valor_estoque_atual'], 2, ',', '.') }}</td> --}}
                                                    <td data-sortable='true' >{{ number_format($material['entradas'], 0, '.', '.') }}</td>
                                                    <td data-sortable='true' >{{ number_format($material['valor_entradas'], 2, ',', '.') }}</td>
                                                @endif

                                                @if ($request->input('tipo_consulta') == 'C')
                                                    {{-- <td data-sortable='true' >{{ number_format($material['estoque_atual'], 0, '.', '.') }}</td>
                                                    <td data-sortable='true' >{{ number_format($material['valor_estoque_atual'], 2, ',', '.') }}</td> --}}
                                                    <td data-sortable='true' >{{ number_format($material['consumido'], 0, '.', '.') }}</td>
                                                    <td data-sortable='true' >{{ number_format($material['valor_consumido'], 2, ',', '.') }}</td>
                                                @endif

                                                @if ($request->input('tipo_consulta') == 'ED')
                                                    <td data-sortable='true' >{{ number_format($material['estoque_atual'], 0, '.', '.') }}</td>
                                                    <td data-sortable='true' >{{ number_format($material['valor_estoque_atual'], 2, ',', '.') }}</td>
                                                @endif

                                                @if ($request->input('tipo_consulta') == 'EEC')
                                                    <td data-sortable='true' >{{ number_format($material['estoque_atual'], 0, '.', '.') }}</td>
                                                    <td data-sortable='true' >{{ number_format($material['valor_estoque_atual'], 2, ',', '.') }}</td>
                                                    <td data-sortable='true' >{{ number_format($material['entradas'], 0, '.', '.') }}</td>
                                                    <td data-sortable='true' >{{ number_format($material['valor_entradas'], 2, ',', '.') }}</td>
                                                    <td data-sortable='true' >{{ number_format($material['consumido'], 0, '.', '.') }}</td>
                                                    <td data-sortable='true' >{{ number_format($material['valor_consumido'], 2, ',', '.') }}</td>
                                                @endif
                                                <td scope="row">
                                                    <div id='modal_ver_materiais_{{ $material['material_id'] }}'  class="modal" tabindex="-1" role="dialog">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content" style="width: 100%; height: 85%">
                                                                <div class="modal-header">
                                                                    @if(in_array($request->input('tipo_consulta'), ['P', 'E']))
                                                                        <h5 class="modal-title" id='texto'>Fichas técnicas</h5>
                                                                    @else
                                                                        <h5 class="modal-title" id='texto'>Estoques</h5>
                                                                    @endif

                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <p>{{ $material['material'] }}</p>
                                                                <div class="modal-body text-Left" style="overflow-y: auto">
                                                                    <label for="" class="col-sm-6 col-form-label "></label>
                                                                    @if (isset($material['os']))

                                                                        @if(in_array($request->input('tipo_consulta'), ['P', 'E']))

                                                                            @foreach ($material['os'] as $tipo => $pedido_id)
                                                                                <p>
                                                                                    O.S: <a href={{ URL::route('consumo-materiais-detalhes', ['id' => $pedido_id['pedidos_ids']]) }}>{{$pedido_id['os']}}</a>
                                                                                    &nbsp;Qtde: <a href={{ URL::route('consumo-materiais-detalhes', ['id' => $pedido_id['pedidos_ids']]) }}>{{$pedido_id['qtde_itens']}}</a>
                                                                                    &nbsp;EP: <a href={{ URL::route('consumo-materiais-detalhes', ['id' => $pedido_id['pedidos_ids']]) }}>{{$pedido_id['ep']}}</a>
                                                                                    &nbsp;Qtde pedido: <a href={{ URL::route('consumo-materiais-detalhes', ['id' => $pedido_id['pedidos_ids']]) }}>{{$pedido_id['qtde']}}</a>
                                                                                </p>
                                                                            @endforeach

                                                                        @else

                                                                            @foreach ($material['os'] as $tipo => $ids)

                                                                                {{-- regra para não mostrar estoques diferente do tipo de consulta --}}
                                                                                @if($request->input('tipo_consulta') == 'ED' && $tipo != 'Estoque atual')
                                                                                    @continue
                                                                                @endif

                                                                                @if(($request->input('tipo_consulta') == 'V' ) && $tipo != 'Entradas')
                                                                                    @continue
                                                                                @endif
                                                                                @if(($request->input('tipo_consulta') == 'C') && $tipo != 'Consumido')
                                                                                    @continue
                                                                                @endif
                                                                                {{-- regra para não mostrar estoques diferente do tipo de consulta --}}

                                                                                @if(!empty($ids))
                                                                                    <p>
                                                                                        <b>
                                                                                            &rArr; {{$tipo}}
                                                                                        </b>
                                                                                    </p>
                                                                                @endif
                                                                                @foreach ($ids as $id)
                                                                                    <p>
                                                                                         Estoque: <a href={{ URL::route('alterar-estoque', ['id' => $id]) }}>{{$id}}</a>
                                                                                    </p>
                                                                                @endforeach
                                                                                <br>

                                                                            @endforeach
                                                                        @endif
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
                                    </tbody>
                                    @if (!empty($totalizadores))
                                        <tfoot>
                                            <tr>
                                                <th></th>
                                                @if ($request->input('tipo_consulta') == 'V')
                                                    {{-- <th>{{number_format($totalizadores['total_estoque_atual'], 0 , '.','.')}}</th>
                                                    <th>{{number_format($totalizadores['total_valor_estoque_atual'], 2 , ',','.')}}</th> --}}
                                                    <th>{{number_format($totalizadores['total_entradas'], 0 , '.','.')}}</th>
                                                    <th>{{number_format($totalizadores['total_valor_entradas'], 2 , ',','.')}}</th>
                                                @endif
                                                @if ($request->input('tipo_consulta') == 'C')
                                                    {{-- <th>{{number_format($totalizadores['total_estoque_atual'], 0 , '.','.')}}</th>
                                                    <th>{{number_format($totalizadores['total_valor_estoque_atual'], 2 , ',','.')}}</th> --}}
                                                    <th>{{number_format($totalizadores['total_consumido'], 0 , '.','.')}}</th>
                                                    <th>{{number_format($totalizadores['total_valor_consumido'], 2 , ',','.')}}</th>
                                                @endif
                                                @if ($request->input('tipo_consulta') == 'ED')
                                                    <th>{{number_format($totalizadores['total_estoque_atual'], 0 , '.','.')}}</th>
                                                    <th>{{number_format($totalizadores['total_valor_estoque_atual'], 2 , ',','.')}}</th>
                                                @endif
                                                @if ($request->input('tipo_consulta') == 'EEC')
                                                    <th>{{number_format($totalizadores['total_estoque_atual'], 0 , '.','.')}}</th>
                                                    <th>{{number_format($totalizadores['total_valor_estoque_atual'], 2 , ',','.')}}</th>
                                                    <th>{{number_format($totalizadores['total_entradas'], 0 , '.','.')}}</th>
                                                    <th>{{number_format($totalizadores['total_valor_entradas'], 2 , ',','.')}}</th>
                                                    <th>{{number_format($totalizadores['total_consumido'], 0 , '.','.')}}</th>
                                                    <th>{{number_format($totalizadores['total_valor_consumido'], 2 , ',','.')}}</th>
                                                @endif
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    @endif
                                @else
                                <h4>Nenhum registro encontrado</h4>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @stop
    @break

    @case('consumo_realizado_ficha')
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
                            @if (!empty($materiais))
                                <table id="table_material" class="table table-striped  text-center">
                                    <thead>
                                        <tr>
                                            <th>Material</th>
                                                <th>Consumo</th>
                                                <th>Peso</th>
                                                <th>Valor consumido</th>
                                                <th>Realizado</th>
                                                <th>Peso</th>
                                                <th>Valor estoque</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                                $totalizadores['consumido'] =0;
                                                $totalizadores['peso_consumido'] =0;
                                                $totalizadores['valor_consumido'] =0;
                                                $totalizadores['realizado'] =0;
                                                $totalizadores['peso_realizado'] =0;
                                                $totalizadores['valor_realizado'] =0;
                                            @endphp

                                        @foreach ($materiais as $material)

                                            <tr>
                                                <td data-sortable='true' >{{ $material['material'] }}</td>
                                                <td data-sortable='true' >{{ number_format($material['consumido'], 0, '.', '.') }}</td>
                                                <td data-sortable='true' >{{ number_format($material['peso_consumido'], 3, '.', '.') }}</td>
                                                <td data-sortable='true' >{{ number_format($material['valor_consumido'], 2, ',', '.') }}</td>
                                                <td data-sortable='true' >{{ number_format($material['realizado'], 0, '.', '.') }}</td>
                                                <td data-sortable='true' >{{ number_format($material['peso_realizado'], 3, '.', '.') }}</td>
                                                <td data-sortable='true' >{{ number_format($material['valor_realizado'], 2, ',', '.') }}</td>
                                            </tr>

                                            @php
                                                $totalizadores['consumido'] += $material['consumido'];
                                                $totalizadores['peso_consumido'] += $material['peso_consumido'];
                                                $totalizadores['valor_consumido'] += $material['valor_consumido'];
                                                $totalizadores['realizado'] += $material['realizado'];
                                                $totalizadores['peso_realizado'] += $material['peso_realizado'];
                                                $totalizadores['valor_realizado'] += $material['valor_realizado'];
                                            @endphp
                                        @endforeach
                                    </tbody>
                                    @if (!empty($totalizadores))
                                        <tfoot>
                                            <tr>
                                                <th></th>
                                                <td data-sortable='true' >{{ number_format($totalizadores['consumido'], 0, '.', '.') }}</td>
                                                <td data-sortable='true' >{{ number_format($totalizadores['peso_consumido'], 0, '.', '.') }}</td>
                                                <td data-sortable='true' >{{ number_format($totalizadores['valor_consumido'], 2, ',', '.') }}</td>
                                                <td data-sortable='true' >{{ number_format($totalizadores['realizado'], 0, '.', '.') }}</td>
                                                <td data-sortable='true' >{{ number_format($totalizadores['peso_realizado'], 0, '.', '.') }}</td>
                                                <td data-sortable='true' >{{ number_format($totalizadores['valor_realizado'], 2, ',', '.') }}</td>
                                            </tr>
                                        </tfoot>
                                    @endif
                                @else
                                    <h4>Nenhum registro encontrado</h4>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @stop
    @break

@endswitch
