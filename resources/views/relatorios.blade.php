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
                <label for="data" class="col-sm-2 col-form-label text-right">Data: de</label>
                <div class="col-sm-2">
                    <input type="text" class="form-control mask_date" id="data" name="data"
                        placeholder="DD/MM/AAAA">
                </div>
                <label for="data_fim" class=" col-form-label text-right">até</label>
                <div class="col-sm-2">
                    <input type="text" class="form-control mask_date" id="data_fim" name="data_fim"
                        placeholder="DD/MM/AAAA">
                </div>

                <div class="col-md-5 themed-grid-col " >
                    <div class="row">
                        <label for="ep" class="col-sm-3 col-form-label text-right">Status do pedido</label>
                        <div class="col-sm-8" style="overflow-y: auto; height: 175px; border:1px solid #97928b">
                            <div class="right_col col-sm-6" role="main">
                                    @foreach ($status as $status)
                                        <div class="col-sm-6 form-check">
                                            <input class="form-check-input col-sm-4"  name="status_id[]" id="{{$status->id}}" type="checkbox"
                                            @if($status->id == 11 || $status->id == 12 || $status->id == 13) {{''}} @else {{ 'checked'}}@endif value="{{$status->id}}">
                                            <label class="form-check-label col-sm-6" style="white-space:nowrap" for="{{$status->id}}">{{$status->nome}}</label>
                                        </div>
                                    @endforeach
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
                                        <th>Material</th>
                                        <th>Consumo previsto</th>
                                        <th>Estoque atual</th>
                                        <th>Diferença</th>
                                        <th>Alerta</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (isset($materiais))
                                        @foreach ($materiais as $material)
                                            <tr>
                                                <td>{{ $material['material'] }}</td>
                                                <td>{{ $material['consumo_previsto'] }}</td>
                                                <td>{{ $material['estoque_atual'] }}</td>
                                                <td>{{ $material['diferenca'] }}</td>
                                                <td>{!! $material['alerta'] !!}</td>
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

@endswitch
