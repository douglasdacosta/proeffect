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
                <h1 class="m-0 text-dark col-sm-11 col-form-label">Relatório de produção</h1>
                <div class="col-sm-1">
                </div>
            </div>
        @stop
        @section('content')
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

            <form id="filtro" action="relatorio-producao" method="get" data-parsley-validate="" class="form-horizontal form-label-left"
                novalidate="">
                <div class="form-group row">

                    <label  for="data_gerado" class="col-sm-2 col-form-label text-right">Data gerado inicial</label>
                    <div class="col-sm-2">
                        <input type="text" class="form-control mask_date" id="data_gerado" name="data_gerado" value="@if (isset($request) && $request->input('data_gerado') != ''){{$request->input('data_gerado')}}@else @endif"
                            placeholder="DD/MM/AAAA">
                    </div>
                    <label for="data_gerado_fim" class="col-sm-2 col-form-label text-right">Data gerado final</label>
                    <div class="col-sm-2">
                        <input type="text" class="form-control mask_date" id="data_gerado_fim" name="data_gerado_fim" value="@if (isset($request) && $request->input('data_gerado_fim') != ''){{$request->input('data_gerado_fim')}}@else @endif"
                            placeholder="DD/MM/AAAA">
                    </div>
                </div>

                {{-- <div class="form-group row">
                    <label for="os" class="col-sm-1 col-form-label text-right">OS</label>
                    <div class="col-sm-1">
                        <input type="text" id="os" name="os" class="form-control" value="@if (isset($request) && $request->input('os') != ''){{$request->input('os')}}@else @endif">
                    </div>
                    <label  for="data_entrega" class="col-sm-2 col-form-label text-right">Data entrega inicial</label>
                    <div class="col-sm-2">
                        <input type="text" class="form-control mask_date" id="data_entrega" name="data_entrega" value="@if (isset($request) && $request->input('data_entrega') != ''){{$request->input('data_entrega')}}@else @endif"
                            placeholder="DD/MM/AAAA">
                    </div>
                    <label for="data_entrega_fim" class="col-sm-2 col-form-label text-right">Data entrega final</label>
                    <div class="col-sm-2">
                        <input type="text" class="form-control mask_date" id="data_entrega_fim" name="data_entrega_fim" value="@if (isset($request) && $request->input('data_entrega_fim') != ''){{$request->input('data_entrega_fim')}}@else @endif"
                            placeholder="DD/MM/AAAA">
                    </div>
                    <label for="status" class="col-sm-1 col-form-label">&nbsp;</label>
                    <div class="col-sm-2">
                    </div>
                </div> --}}
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
                        <div class="x_content">
                            <table class="table table-striped  text-center">
                                <thead>
                                    @if (isset($arr_status))
                                        <tr>
                                            @foreach ($arr_status as $key => $arr_statu)
                                                    <th>{{$key}}</td>
                                            @endforeach
                                        </tr>
                                    @endif
                                </thead>
                                <tbody>

                                    @if (isset($arr_status))
                                        <tr>
                                            @foreach ($arr_status as $arr_statu)
                                                    <td>{{$arr_statu['horas']}}</td>
                                            @endforeach
                                        </tr>
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

    @case('relatorio-producao')
        @section('content_header')
            <div class="form-group row">
                <h1 class="m-0 text-dark col-sm-6 col-form-label">Relatório de produção</h1>
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

        @if (isset($pedidos))
            <div class="form-group row">
                <table class="table table-sm table-striped " id="table_composicao">
                    <thead class="">
                        <tr>
                            <th scope="col">ID</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pedidos as $key => $pedido)
                        <tr>
                            <td scope="col">{{$pedido->id}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @stop
    @break
@endswitch
