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

        <form id="filtro" action="clientes" method="get" data-parsley-validate="" class="form-horizontal form-label-left" novalidate="">
            <div class="form-group row">
                <label for="codigo_cliente" class="col-sm-1 col-form-label">Código cliente</label>
                <div class="col-sm-2">
                    <input type="text" id="codigo_cliente" name="codigo_cliente" class="form-control col-md-7 col-xs-12" value="@if (isset($request) && $request->input('codigo_cliente') != ''){{$request->input('codigo_cliente')}}@else @endif">
                </div>
                <label for="nome_cliente" class="col-sm-1 col-form-label">Nome cliente</label>
                <div class="col-sm-5">
                    <input type="text" id="nome_cliente" name="nome_cliente" class="form-control col-md-7 col-xs-12" value="@if (isset($request) && trim($request->input('nome_cliente')) != ''){{$request->input('nome_cliente')}}@else @endif">
                </div>
            </div>
            <div class="form-group row">
                <label for="nome_contato" class="col-sm-1 col-form-label">Nome contato</label>
                <div class="col-sm-5">
                    <input type="text" id="nome_contato" name="nome_contato" class="form-control col-md-7 col-xs-12" value="@if (isset($request) && trim($request->input('nome_contato')) != ''){{$request->input('nome_contato')}}@else @endif">
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
                      <th>Contato</th>
                      <th>Telefone</th>
                      <th>Email</th>
                    </tr>
                  </thead>
                  <tbody>
                  @if(isset($pessoas))
                        @foreach ($pessoas as $pessoa)
                            <tr>
                            <th scope="row"><a href={{ URL::route($rotaAlterar, array('id' => $pessoa->id )) }}>{{$pessoa->id}}</a></th>
                              <td>{{$pessoa->nome_cliente}}</td>
                              <td>{{$pessoa->nome_contato}}</td>
                              <td class='mask_phone'>{{$pessoa->telefone}}</td>
                              <td>{{$pessoa->email}}</td>
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
                <label for="codigo" class="col-sm-2 col-form-label">Id</label>
                <div class="col-sm-2">
                <input type="text" id="id" name="id" class="form-control col-md-7 col-xs-12" readonly="true" value="1">
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
                <label for="codigo_cliente" class="col-sm-2 col-form-label">Código cliente</label>
                <div class="col-sm-1">
                <input type="text" class="form-control is-invalid" required id="codigo_cliente"  name="codigo_cliente" value="@if (isset($pessoas[0]->codigo_cliente)){{$pessoas[0]->codigo_cliente}}@else{{''}}@endif">
                </div>
            </div>
            <div class="form-group row">
                <label for="nome_cliente" class="col-sm-2 col-form-label">Nome cliente</label>
                <div class="col-sm-6">
                <input type="text" class="form-control is-invalid" required id="nome_cliente"  name="nome_cliente" value="@if (isset($pessoas[0]->nome_cliente)){{$pessoas[0]->nome_cliente}}@else{{''}}@endif">
                </div>
            </div>
            <div class="form-group row">
                <label for="nome_contato" class="col-sm-2 col-form-label">Nome contato</label>
                <div class="col-sm-6">
                <input type="text" class="form-control is-invalid" required id="nome_contato"  name="nome_contato" value="@if (isset($pessoas[0]->nome_contato)){{$pessoas[0]->nome_contato}}@else{{''}}@endif">
                </div>
            </div>
            <div class="form-group row">
                <label for="nome_assistente" class="col-sm-2 col-form-label">Nome Assistente</label>
                <div class="col-sm-6">
                <input type="text" class="form-control" id="nome_assistente"  name="nome_assistente" value="@if (isset($pessoas[0]->nome_assistente)){{$pessoas[0]->nome_assistente}}@else{{''}}@endif">
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
                <input type="text" class="form-control sonumeros" id="numero" name="numero" value="@if (isset($pessoas[0]->numero)){{$pessoas[0]->numero}}@else{{''}}@endif">
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
                <input type="text" class="form-control cep" id="cep" name="cep" value="@if (isset($pessoas[0]->cep)){{$pessoas[0]->cep}}@else{{''}}@endif">
                </div>
            </div>
            <div class="form-group row">
                <label for="telefone" class="col-sm-2 col-form-label">Telefone</label>
                <div class="col-sm-2">
                <input type="text" class="form-control is-invalid mask_phone" required id="telefone" name="telefone" value="@if (isset($pessoas[0]->telefone)){{$pessoas[0]->telefone}}@else{{''}}@endif">
                </div>
            </div>
            <div class="form-group row">
                <label for="email" class="col-sm-2 col-form-label">Email</label>
                <div class="col-sm-4">
                <input type="text" class="form-control is-invalid" required id="email" name="email" value="@if (isset($pessoas[0]->email)){{$pessoas[0]->email}}@else{{''}}@endif">
                </div>
            </div>

            <div class="form-group row">
                <label for="status" class="col-sm-2 col-form-label"></label>
                <select class="form-control col-md-1" id="status" name="status">
                    <option value="A" @if (isset($pessoas[0]->status) && $pessoas[0]->status == 'A'){{ ' selected '}}@else @endif>Ativo</option>
                    <option value="I" @if (isset($pessoas[0]->status) && $pessoas[0]->status =='I'){{ ' selected '}}@else @endif>Inativo</option>
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
