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
        <h1 class="m-0 text-dark col-sm-11 col-form-label">Pesquisa de {{ $nome_tela }}</h1>
        <div class="col-sm-1">
            @include('layouts.nav-open-incluir', ['rotaIncluir => $rotaIncluir'])
        </div>
    </div>
    @stop
    @section('content')
    <div class="right_col" role="main">

        <form id="filtro" action="tarefas" method="get" data-parsley-validate="" class="form-horizontal form-label-left" novalidate="">
            <div class="form-group row">
                <label for="id" class="col-sm-2 col-form-label">Código</label>
                <div class="col-sm-2">
                    <input type="text" id="id" name="id" class="form-control col-md-7 col-xs-12" value="@if (isset($request) && $request->input('id') != ''){{$request->input('id')}}@else @endif">
                </div>
                <label for="mensagem" class="col-sm-1 col-form-label">Tarefa</label>
                <div class="col-sm-3">
                    <input type="text" id="mensagem" name="mensagem" class="form-control col-md-7 col-xs-12" value="@if (isset($request) && trim($request->input('mensagem')) != ''){{$request->input('mensagem')}}@else @endif">
                </div>
                <label for="funcionario" class="col-sm-2 col-form-label">Colaboradores</label>
                <div class="col-sm-2">
                    <select class="form-control" id="funcionario" name="funcionario">
                        <option value="" @if (isset($request) && $request->input('funcionario') == 0){{ ' selected '}}@else @endif></option>
                        @if (isset($funcionarios))
                            @foreach ($funcionarios as $funcionario)

                                @if ($perfil != 1 && $funcionario->id != $usuario)
                                    @continue
                                @endif

                                <option value="{{$funcionario->id}}" @if (isset($request) && $request->input('funcionario') == $funcionario->id){{ ' selected '}}@else @endif>{{$funcionario->nome}}</option>

                                    @if (isset($request) && $request->input('funcionario') == $funcionario->id)
                                        {{ ' selected '}}
                                    @elseif (isset($usuario) && $usuario == $funcionario->id)
                                        {{ ' selected '}}
                                    @else
                                        {{ '' }}
                                    @endif>{{$funcionario->nome }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>

            <div class="form-group row">
                    <label for="status" class="col-sm-2 col-form-label">Status atividade</label>
                    <div class="col-sm-4">
                        <select class="form-control col-md-4" id="status_atividade" name="status_atividade">
                            <option value="0" @if (empty($tarefas[0]->finalizado)) {{ ' selected '}}@else @endif>Não</option>
                            <option value="1" @if (!empty($tarefas[0]->finalizado)) {{ ' selected '}}@else @endif>Sim</option>
                        </select>
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
                      <th>Data</th>
                      <th>Data atividade</th>
                      <th>Colaborador</th>
                      <th>Criador</th>
                      <th>Mensagem</th>
                      <th>Mensagem lida</th>
                      <th>Finalizada</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                  @if(isset($tarefas))
                        @foreach ($tarefas as $tarefa)
                            <tr>
                                <th scope="row"><a href={{ URL::route($rotaAlterar, array('id' => $tarefa->id )) }}>{{$tarefa->id}}</a></th>
                                <td>{{ \Carbon\Carbon::parse($tarefa->data)->format('d/m/Y') }}</td>
                                <td>@if($tarefa->data_atividade) {{ \Carbon\Carbon::parse($tarefa->data_atividade)->format('d/m/Y') }} @else {{''}} @endif</td>
                                <td>{{$tarefa->funcionario}}</td>
                                <td>{{$tarefa->criador}}</td>
                                <td title="{{$tarefa->mensagem}}">{{ substr($tarefa->mensagem, 0, 25) . '...' }}</td>
                                <td>@if (!empty($tarefa->data_hora_lido))
                                        <i class="far fa-check-circle text-success"
                                        title="{{ \Carbon\Carbon::parse($tarefa->data_hora_lido)->format('d/m/Y H:i:s') }}"></i>
                                    @else
                                    <i class="far fa-check-circle text-danger" ></i>
                                    @endif
                                </td>
                                <td>@if ($tarefa->finalizado == 0)
                                        <span class="badge badge-danger">Não</span>
                                    @else
                                        <span class="badge badge-success">Sim</span>
                                    @endif
                                </td>
                                <td>@if ($tarefa->status == 'A')
                                        {{'Ativo'}}
                                    @else
                                        {{'Inativo'}}
                                    @endif
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
                {{-- <label for="codigo" class="col-sm-2 col-form-label">Id</label> --}}
                <div class="col-sm-2">
                <input type="hidden" id="id" name="id" class="form-control col-md-7 col-xs-12" value="@if (isset($tarefas[0]->id)){{$tarefas[0]->id}}@else{{''}}@endif">
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
                <label for="funcionario" class="col-sm-2 col-form-label">Colaboradores</label>
                <div class="col-sm-2">
                    <select class="form-control" id="funcionario" name="funcionario">
                        <option value="" @if (isset($request) && $request->input('funcionario') == 0){{ ' selected '}}@else @endif></option>
                        @if (isset($funcionarios))
                            @foreach ($funcionarios as $funcionario)
                                <option value="{{$funcionario->id}}" @if (isset($tarefas[0]->funcionario_id) && $tarefas[0]->funcionario_id == $funcionario->id){{ ' selected '}}@else @endif>{{$funcionario->nome}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label  class="col-sm-2 col-form-label">Data</label>
                <div class="col-sm-2">
                    <input type="text" class="form-control mask_date" required id="data_hora" name="data_hora"
                        value="@if (isset($tarefas[0]->data_hora)) {{ Carbon\Carbon::parse($tarefas[0]->data_hora)->format('d/m/Y H:i:s') }} @else {{ date('d/m/Y H:i:s') }} @endif">
                </div>
            </div>
            <div class="form-group row">
                <label  class="col-sm-2 col-form-label">Data atividade</label>
                <div class="col-sm-2">
                    <input type="text" class="form-control mask_date" required id="data_atividade" name="data_atividade"
                        value="@if (isset($tarefas[0]->data_atividade)) {{ Carbon\Carbon::parse($tarefas[0]->data_atividade)->format('d/m/Y H:i:s') }} @else {{ date('d/m/Y H:i:s') }} @endif">

                </div>
            </div>
            <div class="form-group row">
                <label  class="col-sm-2 col-form-label">Data lido</label>
                <div class="col-sm-2">
                    <input type="text" class="form-control mask_date_time" readonly id="data_lido" name="data_lido"
                        value="@if (isset($tarefas[0]->data_hora_lido)) {{ Carbon\Carbon::parse($tarefas[0]->data_hora_lido)->format('d/m/Y H:i:s') }} @else {{ '' }} @endif">
                </div>
            </div>
            <div class="form-group row">
                <label for="nome" class="col-sm-2 col-form-label">Mensagem</label>
                <div class="col-sm-8">
                    <textarea class="form-control" id="mensagem" name="mensagem" rows="6">@if (isset($tarefas[0]->mensagem)){{$tarefas[0]->mensagem}}@else{{''}}@endif</textarea>
                </div>
            </div>
            <div class="form-group row">
                <label for="status" class="col-sm-2 col-form-label">Status atividade</label>
                <div class="col-sm-4">
                    <select class="form-control col-md-4" id="status_atividade" name="status_atividade">
                        <option value="0" @if (empty($tarefas[0]->finalizado)) {{ ' selected '}}@else @endif>Pendente</option>
                        <option value="1" @if (!empty($tarefas[0]->finalizado)) {{ ' selected '}}@else @endif>Finalizado</option>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label for="status" class="col-sm-2 col-form-label"></label>
                <select class="form-control col-md-1" id="status" name="status">
                    <option value="A" @if (isset($tarefas[0]->status) && $tarefas[0]->status == 'A'){{ ' selected '}}@else @endif>Ativo</option>
                    <option value="I" @if (isset($tarefas[0]->status) && $tarefas[0]->status =='I'){{ ' selected '}}@else @endif>Inativo</option>
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
