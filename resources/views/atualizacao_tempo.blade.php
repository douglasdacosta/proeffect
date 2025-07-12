@extends('adminlte::page')

@section('title', 'Pro Effect')

<script src="../vendor/jquery/jquery.min.js"></script>
<script src="js/jquery.mask.js"></script>
<script src="js/bootstrap.4.6.2.js"></script>
<script src="js/select2.min.js"></script>
<script src="js/main_custom.js"></script>

@if(isset($tela) and $tela == 'pesquisa')
    @section('content_header')
    <div class="form-group row">
        <h1 class="m-0 text-dark col-sm-11 col-form-label">{{ $nome_tela }}</h1>
    </div>
    @stop
    @section('content')

    <div id='modal_detalhes'  class="modal" tabindex="-1" role="dialog" style="width: 100%;">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-80w" role="document" >
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title" id='texto_status_caixas'>Detalhes de lançamentos</h5>
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
                                            <th>Data e hora</th>
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
                    <label for="ep" class="col-sm-1 col-form-label">EP</label>
                    <div class="col-sm-2">
                        <input type="text" id="ep" name="ep" class="form-control col-md-7 col-xs-12"
                            value="@if (isset($request) && $request->input('ep') != '') {{ $request->input('ep') }}@else @endif">
                    </div>
                    <label for="os" class="col-sm-1 col-form-label">OS</label>
                    <div class="col-sm-2">
                        <input type="text" id="os" name="os" class="form-control col-md-7 col-xs-12"
                            value="@if (isset($request) && $request->input('os') != '') {{ $request->input('os') }}@else @endif">
                    </div>
                    <label for="responsavel" class="col-sm-2 col-form-label">Responsável</label>
                    <div class="col-sm-3">
                        <input type="text" id="responsavel" name="responsavel" class="form-control col-md-7 col-xs-12"
                            value="@if (isset($request) && $request->input('responsavel') != '') {{ $request->input('responsavel') }}@else @endif">
                    </div>
                </div>
                    <div class="form-group row">
                        <label for="data_apontamento" class="col-sm-2 col-form-label text-right">Data apontamento: de</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control mask_date" id="data_apontamento" name="data_apontamento"
                                placeholder="DD/MM/AAAA">
                        </div>
                        <label for="data_apontamento_fim" class=" col-form-label text-right">até</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control mask_date" id="data_apontamento_fim" name="data_apontamento_fim"
                                placeholder="DD/MM/AAAA">
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
          <label class="control-label col-md-3 col-sm-3 col-xs-12" for=""></label>
          <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
              <div class="x_title">
                <div class="clearfix"></div>
              </div>
              <div class="x_content">
                <table class="table table-striped text-center">
                  <thead>
                    <tr>
                      <th>EP</th>
                      <th>OS</th>
                      <th>Qtde</th>
                      <th>Tempo</th>
                      <th>Tempo calculado</th>
                      <th>Detalhes</th>
                      <th>Ação</th>
                    </tr>
                  </thead>
                  <tbody>
                  @if(isset($pedidos))
                        @foreach ($pedidos as $pedido)
                            <tr>
                            <td scope="row">{{$pedido->ep}}</td>
                            <td>{{$pedido->os}}</td>
                            <td>{{$pedido->qtde}}</td>
                            <td>{{$pedido->tempo_default}}</td>
                            <td>{{$pedido->tempo_somado}}</td>
                            <td>
                                <a class="btn btn-info btn-sm ver-detalhes" data-id="{{$pedido->id}}" data-status_id="{{$request->input('departamento')}}" >Detalhes</a>
                            </td>
                            <td>
                                <a class="btn btn-success btn-sm aplicar-valores" data-ep='{{$pedido->ep}}' data-status_id="{{$request->input('departamento')}}" data-tempo_aplicar='{{$pedido->tempo_somado}}'>APLICAR</a>
                            </td>
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
