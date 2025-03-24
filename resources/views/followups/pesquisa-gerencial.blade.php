<?php
use App\Http\Controllers\PedidosController;
use App\Http\Controllers\AjaxOrcamentosController;
use App\Providers\DateHelpers;

?>
<link rel="stylesheet" href="{{ asset('css/followups.css') }}" />
        @section('content_header')
            <div class="form-group row">
                <h1 class="m-0 text-dark col-sm-11 col-form-label">Pesquisa de {{ $nome_tela }}</h1>
                <div class="col-sm-1">
                    @include('layouts.nav-open-incluir', ['rotaIncluir => $rotaIncluir'])
                </div>
            </div>
        @stop
        @section('content')

            <form id="filtro" action="followup-gerencial" method="get" data-parsley-validate="" class="form-horizontal form-label-left" novalidate="">
                <div class="form-group row ">
                    <div class="col-md-9 themed-grid-col row">
                        <div class="form-group row">
                            <label for="os" class="col-sm-1 col-form-label text-right">OS</label>
                            <div class="col-sm-1">
                                <input type="text" id="os" name="os" class="form-control col-md-13" value="">
                            </div>
                            <label for="ep" class="col-sm-1 col-form-label text-right">EP</label>
                            <div class="col-sm-1">
                                <input type="text" id="ep" name="ep" class="form-control col-md-13" value="">
                            </div>
                            <label for="lote" class="col-sm-3 col-form-label text-right tipo_consulta ">Tipo de consulta</label>
                            <div class="col-sm-3">
                                <select class="form-control col-sm-12 tipo_consulta_followup" id="tipo_consulta" name="tipo_consulta">
                                    <option value="F" @if($request->input('tipo_consulta') == 'F'){{ ' selected '}}@else @endif>Followup</option>
                                    <option value="R" @if($request->input('tipo_consulta') == 'R'){{ ' selected '}}@else @endif>Realizado</option>
                                    <option value="G" @if($request->input('tipo_consulta') == 'G'){{ ' selected '}}@else @endif>Gerêncial</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row campos_followup">
                            <label for="data_entrega" class="col-sm-4 col-form-label text-right ">Data entrega: de</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control mask_date" id="data_entrega" name="data_entrega"
                                    placeholder="DD/MM/AAAA">
                            </div>
                            <label for="data_entrega_fim" class=" col-form-label text-right">até</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control mask_date" id="data_entrega_fim" name="data_entrega_fim"
                                    placeholder="DD/MM/AAAA">
                            </div>
                        </div>
                        <div class="form-group row campos_followup">
                            <label for="data_gerado" class="col-sm-4 col-form-label text-right">Data pedido: de</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control mask_date" id="data_gerado" name="data_gerado"
                                    placeholder="DD/MM/AAAA">
                            </div>
                            <label for="data_gerado_fim" class=" col-form-label text-right">até</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control mask_date" id="data_gerado_fim" name="data_gerado_fim"
                                    placeholder="DD/MM/AAAA">
                            </div>
                        </div>
                        <div class="form-group row campos_ciclo_producao">
                            <label for="data_apontamento" class="col-sm-4 col-form-label text-right">Data apontamento: de</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control mask_date" id="data_apontamento" name="data_apontamento"
                                    placeholder="DD/MM/AAAA">
                            </div>
                            <label for="data_apontamento_fim" class=" col-form-label text-right">até</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control mask_date" id="data_apontamento_fim" name="data_apontamento_fim"
                                    placeholder="DD/MM/AAAA">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 themed-grid-col row" >
                        <div class="row">
                            <label for="ep" class="col-sm-4 col-form-label text-right">Status do pedido</label>
                            <div class="col-sm-8" style="overflow-y: auto; height: 175px; border:1px solid #ced4da; border-radius: .25rem;">
                                <div class="right_col col-sm-6" role="main">
                                        @foreach ($status as $status)
                                            <div class="col-sm-6 form-check">
                                                <input class="form-check-input col-sm-4 status_pedido"  name="status_id[]" id="{{$status->id}}" type="checkbox"
                                                @if(in_array($status->id, [1,2,3,4,5,6,7,8,9,10])) {{'checked'}} @else {{''}}@endif value="{{$status->id}}">
                                                <label class="form-check-label col-sm-6" style="white-space:nowrap" for="{{$status->id}}">{{$status->nome}}</label>
                                            </div>
                                        @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-5">
                            <button type="submit" class="btn btn-primary">Pesquisar</button>
                        </div>
                    </div>
                </div>
            </form>

            <div class="right_col" role="main">
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for=""></label>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="x_panel">
                            <div class="x_title">
                                @if (!empty($pedidos_encontrados))
                                    <h4>Encontrados {{ count($pedidos_encontrados) }} ordens de serviço</h4>
                                    @if($request->input('tipo_consulta') == 'G')
                                        <div class="form-group row">
                                            <div class="col-sm-5">
                                                <form id="filtro" action="followup-gerencial-dados" method="post"
                                                    data-parsley-validate="" class="form-horizontal form-label-left" novalidate="">
                                                    @csrf <!--{{ csrf_field() }}-->
                                                    <input type="hidden" id="pedidos_encontrados" name="pedidos_encontrados"
                                                        value="{{ json_encode($pedidos_encontrados) }}">
                                                    <input type="hidden" id="" name="nome_tela"
                                                        value="{{ 'gerencial' }}">
                                                    <button type="submit" class="btn btn-primary"><span
                                                            class="far fa-fw fa-calendar"></span> Visualizar followups Gerêncial</button>
                                                    <div class="clearfix"></div>
                                                </form>
                                            </div>
                                        </div>
                                    @endif
                                    @if($request->input('tipo_consulta') == 'F')
                                        <div class="form-group row">
                                            <div class="col-sm-5">
                                                <form id="filtro" action="followup-detalhes" method="post"
                                                    data-parsley-validate="" class="form-horizontal form-label-left" novalidate="">
                                                    @csrf <!--{{ csrf_field() }}-->
                                                    <input type="hidden" id="pedidos_encontrados" name="pedidos_encontrados"
                                                        value="{{ json_encode($pedidos_encontrados) }}">
                                                    <input type="hidden" id="" name="nome_tela"
                                                        value="{{ 'tempos' }}">
                                                    <button type="submit" class="btn btn-primary"><span
                                                            class="far fa-fw fa-calendar"></span> Visualizar followups tempos</button>
                                                    <div class="clearfix"></div>
                                                </form>
                                            </div>

                                            <div class="col-sm-5">
                                                <form id="filtro" action="followup-detalhes" method="post"
                                                    data-parsley-validate="" class="form-horizontal form-label-left" novalidate="">
                                                    @csrf <!--{{ csrf_field() }}-->
                                                    <input type="hidden" id="pedidos_encontrados" name="pedidos_encontrados"
                                                        value="{{ json_encode($pedidos_encontrados) }}">
                                                    <input type="hidden" id="" name="nome_tela" value="{{ 'geral' }}">
                                                    <button type="submit" class="btn btn-primary"><span
                                                            class="far fa-fw fa-calendar"></span> Visualizar followups geral</button>
                                                    <div class="clearfix"></div>
                                                </form>
                                            </div>
                                        </div>
                                        @endif
                                        @if($request->input('tipo_consulta') == 'R')
                                            <div class="col-sm-5">
                                                <form id="filtro" action="followup-realizado" method="post"
                                                    data-parsley-validate="" class="form-horizontal form-label-left" novalidate="">
                                                    @csrf <!--{{ csrf_field() }}-->
                                                    <input type="hidden" id="pedidos_encontrados" name="pedidos_encontrados"
                                                        value="{{ json_encode($pedidos_encontrados) }}">
                                                    <input type="hidden" id="" name="nome_tela" value="{{ 'realizados' }}">
                                                    <input type="hidden" id="data_inicio" name="data_apontamento" value="{{$request->input('data_apontamento')}}">
                                                    <input type="hidden" id="data_fim" name="data_apontamento_fim" value="{{$request->input('data_apontamento_fim')}}">
                                                    <input type="hidden" id="status_id" name="status_id" value="{{ json_encode($request->input('status_id')) }}">
                                                    <button type="submit" class="btn btn-primary"><span
                                                            class="far fa-fw fa-calendar"></span> Visualizar followups realizados</button>
                                                    <div class="clearfix"></div>
                                                </form>
                                            </div>
                                        @endif
                                        @if($request->input('tipo_consulta') == 'C')
                                            <div class="col-sm-5">
                                                <form id="filtro" action="followup-ciclo-producao" method="post"
                                                    data-parsley-validate="" class="form-horizontal form-label-left" novalidate="">
                                                    @csrf <!--{{ csrf_field() }}-->
                                                    <input type="hidden" id="pedidos_encontrados" name="pedidos_encontrados"
                                                        value="{{ json_encode($pedidos_encontrados) }}">
                                                    <input type="hidden" id="" name="nome_tela" value="{{ 'ciclo_producao' }}">
                                                    <button type="submit" class="btn btn-primary"><span
                                                            class="far fa-fw fa-calendar"></span> Visualizar followups ciclo de produção</button>
                                                    <div class="clearfix"></div>
                                                </form>
                                            </div>
                                        @endif
                                @else
                                    <h4>Nenhum registro encontrado</h4>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @stop