@extends('adminlte::page')

@section('title', 'Pro Effect')
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="js/jquery.mask.js"></script>
<script src="js/main_custom.js"></script>
<script src="js/fichatecnica.js"></script>

@if (isset($tela) and $tela == 'pesquisa')
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

            <form id="filtro" action="fichatecnicas" method="get" data-parsley-validate=""
                class="form-horizontal form-label-left" novalidate="">
                <div class="form-group row">
                    <label for="codigo" class="col-sm-2 col-form-label">Código</label>
                    <div class="col-sm-2">
                        <input type="text" id="codigo" name="codigo" class="form-control col-md-7 col-xs-12"
                            value="@if (isset($request) && $request->input('codigo') != '') {{ $request->input('codigo') }}@else @endif">
                    </div>
                    <label for="codigo" class="col-sm-1 col-form-label">Nome</label>
                    <div class="col-sm-5">
                        <input type="text" id="nome" name="nome" class="form-control col-md-7 col-xs-12"
                            value="@if (isset($request) && trim($request->input('nome')) != '') {{ $request->input('nome') }}@else @endif">
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
                                        <th>EP</th>
                                        <th>Situação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (isset($fichatecnicas))
                                        @foreach ($fichatecnicas as $fichatecnica)
                                            <tr>
                                                <th scope="row"><a
                                                        href={{ URL::route($rotaAlterar, ['id' => $fichatecnica->id]) }}>{{ $fichatecnica->id }}</a>
                                                </th>
                                                <td>{{ $fichatecnica->EP }}</td>
                                                <td>
                                                    @if ($fichatecnica->status == 1)
                                                        <span class='label label-success'>Ativo</span>
                                                    @else
                                                        <span class='label label-danger'>Inativo</span>
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
        <div id="toastsContainerTopRight" class="toasts-top-right fixed">
            <div class="toast bg-danger fade show" role="alert" style="width: 350px" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <strong class="mr-auto">Alerta!</strong>
                    <small></small>
                    <button data-dismiss="toast" type="button" class="ml-2 mb-1 close" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="toast-body textoAlerta" style="text-decoration-style: solid; font-weight: bold; font-size: larger;"></div>
            </div>
        </div>
        @if ($tela == 'alterar')
            @section('content_header')
                <h4 class="m-0 text-dark">Alteração de {{ $nome_tela }}</h4>
            @stop
            <form id="alterar" class="form_ficha" action="{{ $rotaAlterar }}" data-parsley-validate=""
                class="form-horizontal form-label-left" novalidate="" method="post">
                <div class="form-group row">
                    <label for="codigo" class="col-sm-2 col-form-label">Id</label>
                    <div class="col-sm-2">
                        <input type="text" id="id" name="id" class="form-control col-md-7 col-xs-12"
                            readonly="true"
                            value="@if (isset($fichatecnicas[0]->id)) {{ $fichatecnicas[0]->id }}@else{{ '' }} @endif">
                    </div>
                </div>
            @else
                @section('content_header')
                    <h1 class="m-0 text-dark">Inclusão de {{ $nome_tela }}</h1>
                @stop
                <form id="incluir" class="form_ficha" action="{{ $rotaIncluir }}" data-parsley-validate=""
                    class="form-horizontal form-label-left" novalidate="" method="post">
        @endif
        @csrf <!--{{ csrf_field() }}-->
        <div class="form-group row">
            <label for="ep" class="col-sm-2 col-form-label text-right">EP*</label>
            <div class="col-sm-1">
                <input type="text" id="ep" name="ep" class="form-control col-md-13" value="">
            </div>
            <label for="blank" class="col-sm-2 col-form-label text-right">Blank</label>
            <div class="col-sm-1">
                <input type="text" id="blank" name="blank" class="form-control col-md-13" value="">
            </div>
            <label for="blank" class="col-sm-1 col-form-label text-right text-sm-end">Material*</label>
            <div class="col-sm-4">
                <select class="form-control" id="material_id" name="material_id">
                    <option value=""></option>
                    @if (isset($materiais))
                        @foreach ($materiais as $material)
                            <option value="{{ $material->id }}">{{ $material->codigo . ' - ' . $material->material }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="overlay" style="display: none;">
                <i class="fas fa-3x fa-sync-alt fa-spin"></i>
            </div>
        </div>
        <div class="form-group row">
            <label for="qtde" class="col-sm-2 col-form-label text-right">Qtde*</label>
            <div class="col-sm-1">
                <input type="text" id="qtde" name="qtde" class="form-control col-md-13" value="">
            </div>
            <label for="medidax" class="col-sm-2 col-form-label text-right">Medida X</label>
            <div class="col-sm-1">
                <input type="text" id="medidax" name="medidax" class="form-control col-md-13" value="">
            </div>
            <label for="mediday" class="col-sm-2 col-form-label text-right">Medida Y</label>
            <div class="col-sm-1">
                <input type="text" id="mediday" name="mediday" class="form-control col-md-13" value="">
            </div>
            <label for="tempo_usinagem" class="col-sm-2 col-form-label text-right">Tmp usinagem</label>
            <div class="col-sm-1">
                <input type="text" id="tempo_usinagem" name="tempo_usinagem" class="form-control col-md-13 mask_minutos"
                    value="">
            </div>

        </div>
        <div class="form-group row">
            <label for="tempo_acabamento" class="col-sm-2 col-form-label text-right">Tmp acabamento</label>
            <div class="col-sm-1">
                <input type="text" id="tempo_acabamento" name="tempo_acabamento" class="form-control col-md-13 mask_minutos"
                    value="">
            </div>
            <label for="tempo_montagem" class="col-sm-2 col-form-label text-right">Tmp montagem</label>
            <div class="col-sm-1">
                <input type="text" id="tempo_montagem" name="tempo_montagem" class="form-control col-md-13 mask_minutos"
                    value="">
            </div>
            <label for="tempo_montagem" class="col-sm-2 col-form-label text-right">Tmp montagem torre</label>
            <div class="col-sm-1">
                <input type="text" id="tempo_montagem_torre" name="tempo_montagem_torre"
                    class="form-control col-md-13" value="">
            </div>
            <label for="tempo_inspecao" class="col-sm-2 col-form-label text-right">Tmp inspeção</label>
            <div class="col-sm-1">
                <input type="text" id="tempo_inspecao" name="tempo_inspecao" class="form-control col-md-13 mask_minutos"
                    value="">
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-10">
            </div>
            <div class="col-sm-2">
                <button type="button" id="addComposicao" class="btn btn-success">Adicionar</button>
            </div>
        </div>
        <hr class="my-3">
        <label for="codigo" class="col-sm-10 col-form-label">Tabela de composição do EP</label>
        <div class="form-group row">
            <table class="table table-sm table-striped " id="table_composicao">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">Blank</th>
                        <th scope="col">Qtde</th>
                        <th scope="col">Material</th>
                        <th scope="col">Medida X</th>
                        <th scope="col">Medida Y</th>
                        <th scope="col">Tmp usinagem</th>
                        <th scope="col">Tmp Acabamento</th>
                        <th scope="col">Tmp montagem</th>
                        <th scope="col">Tmp montagem torre</th>
                        <th scope="col">Tmp inspeção</th>
                        <th scope="col">Ação</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>


        <hr class="my-4">

        <div class="form-group row">
            <table class="table table-sm table-striped">
                <thead>
                    <tr>
                        <th scope="col">Total usinagem </th>
                        <th scope="col">Total acabamento</th>
                        <th scope="col">Total montagem</th>
                        <th scope="col">Total montagem torre</th>
                        <th scope="col">Total inspeção</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th><input type="text" id="soma_tempo_usinagem" name="soma_tempo_usinagem"
                                class="form-control col-md-13" value="" readonly></th>
                        <td><input type="text" id="soma_tempo_acabamento" name="soma_tempo_acabamento"
                                class="form-control col-md-13" value="" readonly></td>
                        <td><input type="text" id="soma_tempo_montagem" name="soma_tempo_montagem"
                                class="form-control col-md-13" value="" readonly></td>
                        <td><input type="text" id="soma_tempo_montagem_torre" name="soma_tempo_montagem_torre"
                                class="form-control col-md-13" value="" readonly></td>
                        <td><input type="text" id="soma_tempo_inspecao" name="soma_tempo_inspecao"
                                class="form-control col-md-13" value="" readonly></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="form-group row">
            <input type="hidden" id='composicoes' name="composicoes" value=''>
            <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" id="status" name="status"
                    @if (!isset($materiais[0]->status) || $materiais[0]->status == 1) checked @else{{ '' }} @endif>
                <label class="custom-control-label" for="status">Ativo/Inativo</label>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-10">
                <button class="btn btn-danger" onclick="window.history.back();" type="button">Cancelar</button>
            </div>
            <div class="col-sm-2">
                <button type="button" id="salvar_ficha" class="btn btn-primary">Salvar</button>
            </div>
        </div>
        </form>
    @stop
@endif
