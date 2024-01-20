@extends('adminlte::page')

@section('title', 'Pro Effect')



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
        
        <form id="filtro" action="pessoas" method="get" data-parsley-validate="" class="form-horizontal form-label-left" novalidate="">
            <div class="form-group row">
                <label for="id" class="col-sm-2 col-form-label">Código</label>
                <div class="col-sm-2">
                <input type="text" id="id" name="id" class="form-control col-md-7 col-xs-12" value="@if (isset($request) && $request->input('id') != ''){{$request->input('id')}}@else @endif">
                </div>
                <label for="nome" class="col-sm-1 col-form-label">Nome</label>
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
                      <th>Nome</th>
                      <th>Situação</th>
                    </tr>
                  </thead>
                  <tbody>
                  @if(isset($pessoas))
                        @foreach ($pessoas as $pessoa)
                            <tr>
                            <th scope="row"><a href={{ URL::route($rotaAlterar, array('id' => $pessoa->id )) }}>{{$pessoa->id}}</a></th>                              
                              <td>{{$pessoa->nome}}</td>
                              <td>@if ( $pessoa->status == 1 ) <span class='label label-success' >Ativo</span> @else  <span class='label label-danger' >Inativo</span> @endif </td>
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
                <input type="text" id="id" name="id" class="form-control col-md-7 col-xs-12" readonly="true" value="@if (isset($pessoas[0]->id)){{$pessoas[0]->id}}@else{{''}}@endif">
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
                <input type="text" class="form-control" id="nome"  name="nome" value="@if (isset($pessoas[0]->nome)){{$pessoas[0]->nome}}@else{{''}}@endif">
                </div>
            </div>  
            
            <div class="form-group row">
                <label for="documento" class="col-sm-2 col-form-label">Documento</label>
                <div class="col-sm-2">
                <input type="text" class="form-control" id="documento" name="documento" value="@if (isset($pessoas[0]->documento)){{$pessoas[0]->documento}}@else{{''}}@endif">
                </div>
            </div>
            <div class="form-group row">
                <label for="endereco" class="col-sm-2 col-form-label">Endereço</label>
                <div class="col-sm-7">
                <input type="text" class="form-control" id="endereco" name="endereco" value="@if (isset($pessoas[0]->endereco)){{$pessoas[0]->endereco}}@else{{''}}@endif">
                </div>
            </div>            
            <div class="form-group row">
                <label for="numero" class="col-sm-2 col-form-label">Numero</label>
                <div class="col-sm-1">
                <input type="text" class="form-control" id="numero" name="numero" value="@if (isset($pessoas[0]->numero)){{$pessoas[0]->numero}}@else{{''}}@endif">
                </div>
            </div>            
            <div class="form-group row">
                <label for="bairro" class="col-sm-2 col-form-label">Bairro</label>
                <div class="col-sm-4">
                <input type="text" class="form-control" id="bairro" name="bairro" value="@if (isset($pessoas[0]->bairro)){{$pessoas[0]->bairro}}@else{{''}}@endif">
                </div>
            </div>  
            <div class="form-group row">
                <label for="cidade" class="col-sm-2 col-form-label">Cidade</label>
                <div class="col-sm-2">
                <input type="text" class="form-control" id="cidade" name="cidade" value="@if (isset($pessoas[0]->cidade)){{$pessoas[0]->cidade}}@else{{''}}@endif">
                </div>
            </div>  
            <div class="form-group row">
                <label for="estado" class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-2">
                <input type="text" class="form-control" id="estado" name="estado" value="@if (isset($pessoas[0]->estado)){{$pessoas[0]->estado}}@else{{''}}@endif">
                </div>
            </div>  
            <div class="form-group row">
                <label for="cep" class="col-sm-2 col-form-label">Cep</label>
                <div class="col-sm-2">
                <input type="text" class="form-control" id="cep" name="cep" value="@if (isset($pessoas[0]->cep)){{$pessoas[0]->cep}}@else{{''}}@endif">
                </div>
            </div>  
            <div class="form-group row">
                <label for="telefone" class="col-sm-2 col-form-label">Telefone</label>
                <div class="col-sm-2">
                <input type="text" class="form-control" id="telefone" name="telefone" value="@if (isset($pessoas[0]->telefone)){{$pessoas[0]->telefone}}@else{{''}}@endif">
                </div>
            </div>  
            <div class="form-group row">
                <label for="email" class="col-sm-2 col-form-label">Email</label>
                <div class="col-sm-4">
                <input type="text" class="form-control" id="email" name="email" value="@if (isset($pessoas[0]->email)){{$pessoas[0]->email}}@else{{''}}@endif">
                </div>
            </div>  
            
            <div class="form-group row">
                <label  class="col-sm-2 col-form-label"> </label>
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="status" name="status" @if (!isset($pessoas[0]->status) || $pessoas[0]->status == 1) checked @else{{''}}@endif>
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