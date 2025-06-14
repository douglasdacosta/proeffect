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
    <div class="right_col" role="main">
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
                      <th>Tmp Montagem</th>
                      <th>Tmp Inspeção</th>
                      <th>Tmp Montagem calc</th>
                      <th>Tmp Inspeção calc</th>
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
                              <td>{{$pedido->tempo_montagem}}</td>
                              <td>{{$pedido->tempo_inspecao}}</td>
                              <td>{{$pedido->tempo_somado_montagem}}</td>
                              <td>{{$pedido->tempo_somado_inspecao}}</td>
                              <td>
                                <a class="btn btn-success btn-sm">APLICAR</a>
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
