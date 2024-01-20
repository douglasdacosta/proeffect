@extends('adminlte::page')

@section('title', 'Pro Effect')
{{-- <script src="js/jquery_v3.1.1.js"></script> --}}
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="js/jquery.mask.js"></script>
<script src="js/main_custom.js"></script>

@if(isset($tela) and $tela == 'pesquisa')
    @section('content_header')
    <div class="form-group row">
        <h1 class="m-0 text-dark col-sm-6 col-form-label">Pesquisa de {{ $nome_tela }}</h1>
        <div class="col-sm-5">
            @include('layouts.nav-open-incluir', ['rotaIncluir => $rotaIncluir']) 
        </div>
    </div>
    @stop
    @section('content')
    <div class="right_col" role="main">
        
        <form id="filtro" action="materiais" method="get" data-parsley-validate="" class="form-horizontal form-label-left" novalidate="">
            <div class="form-group row">
                <label for="codigo" class="col-sm-2 col-form-label">Código</label>
                <div class="col-sm-2">
                <input type="text" id="codigo" name="codigo" class="form-control col-md-7 col-xs-12" value="@if (isset($request) && $request->input('codigo') != ''){{$request->input('codigo')}}@else @endif">
                </div>
                <label for="codigo" class="col-sm-1 col-form-label">Nome</label>
                <div class="col-sm-5">
                <input type="text" id="nome" name="nome" class="form-control col-md-7 col-xs-12" value="@if (isset($request) && trim($request->input('nome')) != ''){{$request->input('nome')}}@else @endif">
                </div>
            </div>            
            <div class="form-group row">                            
                <div class="col-sm-5">                    
                </div>
                <div class="col-sm-5">
                    <button type="submit" class="btn btn-primary">Pesquisar</button>
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
                      <th>Código</th>
                      <th>Material</th>
                      <th>Situação</th>
                    </tr>
                  </thead>
                  <tbody>
                  @if(isset($materiais))
                        @foreach ($materiais as $material)
                            <tr>
                            <th scope="row"><a href={{ URL::route($rotaAlterar, array('id' => $material->id )) }}>{{$material->id}}</a></th>
                              <td>{{$material->codigo}}</td>
                              <td>{{$material->material}}</td>
                              <td>@if ( $material->status == 1 ) <span class='label label-success' >Ativo</span> @else  <span class='label label-danger' >Inativo</span> @endif </td>
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
            <form id="alterar" action="{{$rotaAlterar}}" data-parsley-validate="" class="form-horizontal form-label-left" method="post">
            <div class="form-group row">
                <label for="codigo" class="col-sm-2 col-form-label">Id</label>
                <div class="col-sm-2">
                <input type="text" id="id" name="id" class="form-control col-md-7 col-xs-12" readonly="true" value="@if (isset($materiais[0]->id)){{$materiais[0]->id}}@else{{''}}@endif">
                </div>
            </div>
        @else
            @section('content_header')
                <h1 class="m-0 text-dark">Inclusão de {{ $nome_tela }}</h1>
            @stop
            <form id="incluir" action="{{$rotaIncluir}}" data-parsley-validate="" class="form-horizontal form-label-left" method="post">
        @endif
            @csrf <!--{{ csrf_field() }}-->
            <div class="form-group row">
                <label for="codigo" class="col-sm-2 col-form-label">Código</label>
                <div class="col-sm-2">
                <input type="text" class="form-control is-invalid" id="codigo" name="codigo" required value="@if (isset($materiais[0]->codigo)){{$materiais[0]->codigo}}@else{{''}}@endif">
                </div>
            </div>
            <div class="form-group row">
                <label for="Material" class="col-sm-2 col-form-label">Material</label>
                <div class="col-sm-6">
                <input type="text" class="form-control is-invalid" required id="material"  name="material"  value="@if (isset($materiais[0]->material)){{$materiais[0]->material}}@else{{''}}@endif">
                </div>
            </div>  
            
            <div class="form-group row">
                <label for="espessura" class="col-sm-2 col-form-label">Espessura (mm)</label>
                <div class="col-sm-2">
                <input type="text" class="form-control" id="espessura" name="espessura" value="@if (isset($materiais[0]->espessura)){{$materiais[0]->espessura}}@else{{''}}@endif">
                </div>
            </div>  
            <div class="form-group row">
                <label for="unidadex" class="col-sm-2 col-form-label">Tamanho placa X (mm)</label>
                <div class="col-sm-2">
                <input type="text" class="form-control" id="unidadex" name="unidadex" value="@if (isset($materiais[0]->unidadex)){{$materiais[0]->unidadex}}@else{{''}}@endif">
                </div>
            </div>  
            <div class="form-group row">
                <label for="unidadey" class="col-sm-2 col-form-label">Tamanho placa Y (mm)</label>
                <div class="col-sm-2">
                <input type="text" class="form-control" id="unidadey" name="unidadey" value="@if (isset($materiais[0]->unidadey)){{$materiais[0]->unidadey}}@else{{''}}@endif">
                </div>
            </div> 
            <div class="form-group row">
                <label for="tempo_montagem_torre" class="col-sm-2 col-form-label">Tempo montagem torre</label>
                <div class="col-sm-2">
                <input type="text" class="form-control mask_minutos" id="tempo_montagem_torre" name="tempo_montagem_torre" placeholder="00:00" value="@if (isset($materiais[0]->tempo_montagem_torre)){{$materiais[0]->tempo_montagem_torre}}@else{{''}}@endif">
                </div>
            </div> 
            <div class="form-group row">
                <label for="valor" class="col-sm-2 col-form-label">Valor peça</label>
                <div class="col-sm-2">
                <input type="text" class="form-control mask_valor" id="valor" name="valor" data-mask="0,00"  value="@if (isset($materiais[0]->valor)){{$materiais[0]->valor}}@else{{''}}@endif">
                </div>
            </div>
            <div class="form-group row">
                <label  class="col-sm-2 col-form-label"> </label>
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="status" name="status" @if (!isset($materiais[0]->status) || $materiais[0]->status == 1) checked @else{{''}}@endif>
                    <label class="custom-control-label" for="status">Ativo/Inativo</label>
                </div>
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