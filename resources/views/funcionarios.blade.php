@extends('adminlte::page')

@section('title', 'Pro Effect')
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="js/jquery.mask.js"></script>
<script src="js/main_custom.js"></script>

@if(isset($tela) and $tela == 'pesquisa')
    @section('content_header')
    <div class="form-group row">
        <h1 class="m-0 text-dark col-sm-11 col-form-label">Pesquisa de {{ $nome_tela }}</h1>
        <div class="col-sm-1">
            @include('layouts.nav-open-incluir', ['rotaIncluir => $rotaIncluir'])
        </div>
    </div>
    @stop
    @section('content')
    <div class="right_col" role="main">

        <form id="filtro" action="funcionarios" method="get" data-parsley-validate="" class="form-horizontal form-label-left" novalidate="">
            <div class="form-group row">
                <label for="nome" class="col-sm-1 col-form-label">Nome</label>
                <div class="col-sm-5">
                    <input type="text" id="nome" name="nome" class="form-control col-md-7 col-xs-12" value="@if (isset($request) && trim($request->input('nome')) != ''){{$request->input('nome')}}@else @endif">
                </div>
                <label for="status" class="col-sm-1 col-form-label"></label>
                <select class="form-control col-md-1" id="status" name="status">
                    <option value="A" @if (isset($request) && $request->input('status') == 'A'){{ ' selected '}}@else @endif>Ativo</option>
                    <option value="I" @if (isset($request) && $request->input('status')  == 'I'){{ ' selected '}}@else @endif>Inativo</option>
                </select>
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
                <table class="table table-striped text-center">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Nome</th>
                    </tr>
                  </thead>
                  <tbody>
                  @if(isset($funcionarios))
                        @foreach ($funcionarios as $funcionario)
                            <tr>
                            <th scope="row"><a href={{ URL::route($rotaAlterar, array('id' => $funcionario->id )) }}>{{$funcionario->id}}</a></th>
                              <td>{{$funcionario->nome}}</td>
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
            <form id="alterar" action="{{$rotaAlterar}}" data-parsley-validate="" class="form-horizontal form-label-left"  method="post">
            <div class="form-group row">
                <label for="id" class="col-sm-2 col-form-label">Id</label>
                <div class="col-sm-2">
                <input type="text" id="id" name="id" class="form-control col-md-7 col-xs-12" readonly="true" value="@if (isset($funcionarios[0]->id)){{$funcionarios[0]->id}}@else{{''}}@endif">
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
                <label for="nome" class="col-sm-2 col-form-label">Nome*</label>
                <div class="col-sm-6">
                <input type="text" class="form-control is-invalid" required id="nome"  name="nome" value="@if (isset($funcionarios[0]->nome)){{$funcionarios[0]->nome}}@else{{''}}@endif">
                </div>
            </div>
            <div class="form-group row">
                <label for="email" class="col-sm-2 col-form-label">Email*</label>
                <div class="col-sm-4">
                <input type="text" class="form-control" required id="email"  name="email" value="@if (isset($funcionarios[0]->email)){{$funcionarios[0]->email}}@else{{''}}@endif" @if($tela == 'alterar'){{' readonly="true" '}}@endif>
                </div>
            </div>
            <div class="form-group row">
                <label for="funcao" class="col-sm-2 col-form-label">Cargo</label>
                <div class="col-sm-3">
                <input type="text" class="form-control" id="funcao"  name="funcao" value="@if (isset($funcionarios[0]->funcao)){{$funcionarios[0]->funcao}}@else{{''}}@endif">
                </div>
            </div>
            <div class="form-group row">
                <label for="perfil" class="col-sm-2 col-form-label">Perfil</label>
                <div class="col-sm-2">
                    <select class="form-control" id="perfil" name="perfil">
                        @foreach ($perfis as $perfil)
                            <option value="{{$perfil->id}}" @if (isset($funcionarios[0]->perfil) && $funcionarios[0]->perfil == $perfil->id){{ ' selected '}}@else @endif>{{$perfil->nome}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label for="senha" class="col-sm-2 col-form-label">Senha</label>
                <div class="col-sm-2">
                <input type="text" class="form-control" id="senha"  name="senha" value="@if (isset($funcionarios[0]->senha)){{$funcionarios[0]->senha}}@else{{''}}@endif">
                </div>
            </div>
            <div class="form-group row">
                <label for="status" class="col-sm-2 col-form-label"></label>
                <select class="form-control col-md-1" id="status" name="status">
                    <option value="A" @if (isset($funcionarios[0]->status) && $funcionarios[0]->status == 'A'){{ ' selected '}}@else @endif>Ativo</option>
                    <option value="I" @if (isset($funcionarios[0]->status) && $funcionarios[0]->status =='I'){{ ' selected '}}@else @endif>Inativo</option>
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
