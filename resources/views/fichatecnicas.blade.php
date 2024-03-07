@extends('adminlte::page')

@section('title', 'Pro Effect')
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="js/jquery.mask.js"></script>
<script src="js/main_custom.js"></script>
<script src="js/fichatecnica.js"></script>

@if (isset($tela) and $tela == 'pesquisa')
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

            <form id="filtro" action="fichatecnica" method="get" data-parsley-validate=""
                class="form-horizontal form-label-left" novalidate="">
                <div class="form-group row">
                    <label for="ep" class="col-sm-2 col-form-label">EP</label>
                    <div class="col-sm-2">
                        <input type="text" id="ep" name="ep" class="form-control col-md-7 col-xs-12"
                            value="@if (isset($request) && $request->input('ep') != '') {{ $request->input('ep') }}@else @endif">
                    </div>
                    <label for="status" class="col-sm-1 col-form-label"></label>
                    <select class="form-control col-md-1" id="status" name="status">
                        <option value="A" @if (isset($request) && $request->input('status') == 'A') {{ ' selected ' }}@else @endif>Ativo
                        </option>
                        <option value="I" @if (isset($request) && $request->input('status') == 'I') {{ ' selected ' }}@else @endif>Inativo
                        </option>
                    </select>
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
                        <h4>Encontrados</h4>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <table class="table table-striped  text-center">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>EP</th>
                                    <th scope="col">Total usinagem </th>
                                    <th scope="col">Total acabamento</th>
                                    <th scope="col">Total montagem</th>
                                    <th scope="col">Total inspeção</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($fichatecnicas))
                                    @foreach ($fichatecnicas as $fichatecnica)
                                        <tr>
                                            <th scope="row"><a
                                                    href={{ URL::route($rotaAlterar, ['id' => $fichatecnica->id]) }}>{{ $fichatecnica->id }}</a>
                                            </th>
                                            <td>{{ $fichatecnica->ep }}</td>
                                            <td class="@if($fichatecnica->tempo_usinagem == '00:00:00') {{'text-danger'}} @else {{''}} @endif">{{ $fichatecnica->tempo_usinagem }}</td>
                                            <td class="@if($fichatecnica->tempo_acabamento == '00:00:00') {{'text-danger'}} @else {{''}} @endif">{{ $fichatecnica->tempo_acabamento }}</td>
                                            <td class="@if($fichatecnica->tempo_montagem == '00:00:00') {{'text-danger'}} @else {{''}} @endif">{{ $fichatecnica->tempo_montagem }}</td>
                                            <td class="@if($fichatecnica->tempo_inspecao == '00:00:00') {{'text-danger'}} @else {{''}} @endif"  >{{ $fichatecnica->tempo_inspecao }}</td>
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
            <div class="toast bg-danger fade show" role="alert" style="width: 350px" aria-live="assertive"
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
                <input type="text" id="ep" name="ep" class="form-control col-md-13"
                    value="@if (isset($fichatecnicas[0]->ep)) {{ $fichatecnicas[0]->ep }} @else{{ '' }} @endif">
            </div>
            <label for="blank" class="col-sm-2 col-form-label text-right text-sm-end">Material*</label>
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
            <label for="blank" class="col-sm-2 col-form-label text-right">Blank</label>
            <div class="col-sm-1">
                <input type="text" id="blank" name="blank" class="form-control col-md-13 text-uppercase"
                    value="">
            </div>
            <div class="overlay" style="display: none;">
                <i class="fas fa-2x fa-sync-alt fa-spin"></i>
            </div>
        </div>
        <div class="form-group row">
            <label for="qtde" class="col-sm-2 col-form-label text-right">Qtde*</label>
            <div class="col-sm-1">
                <input type="text" id="qtde" name="qtde" class="form-control col-md-13 sonumeros"
                    value="">
            </div>
            <label for="medidax" class="col-sm-2 col-form-label text-right ">Medida X</label>
            <div class="col-sm-1">
                <input type="text" id="medidax" name="medidax" class="form-control col-md-13 sonumeros"
                    value="">
            </div>
            <label for="mediday" class="col-sm-2 col-form-label text-right">Medida Y</label>
            <div class="col-sm-1">
                <input type="text" id="mediday" name="mediday" class="form-control col-md-13 sonumeros"
                    value="">
            </div>


        </div>
        <div class="form-group row">
            <label for="tempo_usinagem" class="col-sm-2 col-form-label text-right">Tmp usinagem</label>
            <div class="col-sm-1">
                <input type="text" id="tempo_usinagem" name="tempo_usinagem"
                    class="form-control col-md-13 mask_minutos" value="">
            </div>
            <label for="tempo_acabamento" class="col-sm-2 col-form-label text-right">Tmp acabamento</label>
            <div class="col-sm-1">
                <input type="text" id="tempo_acabamento" name="tempo_acabamento"
                    class="form-control col-md-13 mask_minutos" value="">
            </div>
            <label for="tempo_montagem" class="col-sm-2 col-form-label text-right">Tmp montagem</label>
            <div class="col-sm-1">
                <input type="text" id="tempo_montagem" name="tempo_montagem"
                    class="form-control col-md-13 mask_minutos" value="">
            </div>
            <input type="hidden" id="tempo_montagem_torre" name="tempo_montagem_torre"
                class="form-control col-md-13 mask_minutos" value="">
            <label for="tempo_inspecao" class="col-sm-2 col-form-label text-right">Tmp inspeção</label>
            <div class="col-sm-1">
                <input type="text" id="tempo_inspecao" name="tempo_inspecao"
                    class="form-control col-md-13 mask_minutos" value="">
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
            <table class="table table-sm table-striped text-center" id="table_composicao">
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

                    @if (isset($fichatecnicasitens))
                        @foreach ($fichatecnicasitens as $fichatecnicaitem)
                            <tr class="{{ 'blank_' . $fichatecnicaitem->blank }}{{ $fichatecnicaitem->materiais_id }}">
                                <td data-name="blank" class="blank" scope="row">{{ trim($fichatecnicaitem->blank) }}
                                </td>
                                <td data-name="qtde" class="qtde">{{ trim($fichatecnicaitem->qtde_blank) }}</td>
                                <td data-name="material_id" class="material_id"
                                    data-materialid="{{ trim($fichatecnicaitem->materiais_id) }}">
                                    {{ trim($fichatecnicaitem->materiais->material) }}</td>
                                <td data-name="medidax" class="medidax">{{ trim($fichatecnicaitem->medidax) }}</td>
                                <td data-name="mediday" class="mediday">{{ trim($fichatecnicaitem->mediday) }}</td>
                                <td data-name="tempo_usinagem" class="tempo_usinagem">
                                    {{ $fichatecnicaitem->tempo_usinagem }}</td>
                                <td data-name="tempo_acabamento" class="tempo_acabamento">
                                    {{ $fichatecnicaitem->tempo_acabamento }}</td>
                                <td data-name="tempo_montagem" class="tempo_montagem">
                                    {{ $fichatecnicaitem->tempo_montagem }}</td>
                                <td data-name="tempo_montagem_torre" class="tempo_montagem_torre">
                                    {{ $fichatecnicaitem->tempo_montagem_torre }}</td>
                                <td data-name="tempo_inspecao" class="tempo_inspecao">
                                    {{ $fichatecnicaitem->tempo_inspecao }}</td>
                                <th>
                                    <button type="button" class="close" aria-label="Close"
                                        data-blank="{{ $fichatecnicaitem->blank }}{{ $fichatecnicaitem->materiais_id }}"><span
                                            aria-hidden="true">&times;</span></button>
                                    <button type="button" class="close edita_composicao" style="padding-right: 20px"
                                        data-blank="{{ $fichatecnicaitem->blank }}{{ $fichatecnicaitem->materiais_id }}"><span
                                            aria-hidden="true">&#9998;</span></button>
                                </th>
                            </tr>
                        @endforeach
                    @endif




                </tbody>
            </table>
        </div>


        <hr class="my-4">

        <div class="form-group row">
            <table class="table table-sm table-striped  text-center">
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
            <label for="alerta_usinagem" class="col-sm-2 col-form-label text-right">Alerta usinagem</label>
            <div class="col-sm-10">
                <input type="text" id="alerta_usinagem1" name="alerta_usinagem1" class="form-control col-md-13"
                    value="@if (isset($fichatecnicas[0]->alerta_usinagem1)) {{ $fichatecnicas[0]->alerta_usinagem1 }} @else{{ '' }} @endif">
                <input type="text" id="alerta_usinagem2" name="alerta_usinagem2" class="form-control col-md-13"
                    value="@if (isset($fichatecnicas[0]->alerta_usinagem2)) {{ $fichatecnicas[0]->alerta_usinagem2 }} @else{{ '' }} @endif">
                <input type="text" id="alerta_usinagem3" name="alerta_usinagem3" class="form-control col-md-13"
                    value="@if (isset($fichatecnicas[0]->alerta_usinagem3)) {{ $fichatecnicas[0]->alerta_usinagem3 }} @else{{ '' }} @endif">
                <input type="text" id="alerta_usinagem4" name="alerta_usinagem4" class="form-control col-md-13"
                    value="@if (isset($fichatecnicas[0]->alerta_usinagem4)) {{ $fichatecnicas[0]->alerta_usinagem4 }} @else{{ '' }} @endif">
                <input type="text" id="alerta_usinagem5" name="alerta_usinagem5" class="form-control col-md-13"
                    value="@if (isset($fichatecnicas[0]->alerta_usinagem5)) {{ $fichatecnicas[0]->alerta_usinagem5 }} @else{{ '' }} @endif">
            </div>
        </div>
        <div class="form-group row">
            <label for="alerta_acabamento" class="col-sm-2 col-form-label text-right">Alerta acabamento</label>
            <div class="col-sm-10">
                <input type="text" id="alerta_acabamento1" name="alerta_acabamento1" class="form-control col-md-13"
                    value="@if (isset($fichatecnicas[0]->alerta_acabamento1)) {{ $fichatecnicas[0]->alerta_acabamento1 }} @else{{ '' }} @endif">
                <input type="text" id="alerta_acabamento2" name="alerta_acabamento2" class="form-control col-md-13"
                    value="@if (isset($fichatecnicas[0]->alerta_acabamento2)) {{ $fichatecnicas[0]->alerta_acabamento2 }} @else{{ '' }} @endif">
                <input type="text" id="alerta_acabamento3" name="alerta_acabamento3" class="form-control col-md-13"
                    value="@if (isset($fichatecnicas[0]->alerta_acabamento3)) {{ $fichatecnicas[0]->alerta_acabamento3 }} @else{{ '' }} @endif">
                <input type="text" id="alerta_acabamento4" name="alerta_acabamento4" class="form-control col-md-13"
                    value="@if (isset($fichatecnicas[0]->alerta_acabamento4)) {{ $fichatecnicas[0]->alerta_acabamento4 }} @else{{ '' }} @endif">
                <input type="text" id="alerta_acabamento5" name="alerta_acabamento5" class="form-control col-md-13"
                    value="@if (isset($fichatecnicas[0]->alerta_acabamento5)) {{ $fichatecnicas[0]->alerta_acabamento5 }} @else{{ '' }} @endif">
            </div>
        </div>
        <div class="form-group row">
            <label for="alerta_montagem" class="col-sm-2 col-form-label text-right">Alerta montagem</label>
            <div class="col-sm-10">
                <input type="text" id="alerta_montagem1" name="alerta_montagem1" class="form-control col-md-13"
                    value="@if (isset($fichatecnicas[0]->alerta_montagem1)) {{ $fichatecnicas[0]->alerta_montagem1 }} @else{{ '' }} @endif">
                <input type="text" id="alerta_montagem2" name="alerta_montagem2" class="form-control col-md-13"
                    value="@if (isset($fichatecnicas[0]->alerta_montagem2)) {{ $fichatecnicas[0]->alerta_montagem2 }} @else{{ '' }} @endif">
                <input type="text" id="alerta_montagem3" name="alerta_montagem3" class="form-control col-md-13"
                    value="@if (isset($fichatecnicas[0]->alerta_montagem3)) {{ $fichatecnicas[0]->alerta_montagem3 }} @else{{ '' }} @endif">
                <input type="text" id="alerta_montagem4" name="alerta_montagem4" class="form-control col-md-13"
                    value="@if (isset($fichatecnicas[0]->alerta_montagem4)) {{ $fichatecnicas[0]->alerta_montagem4 }} @else{{ '' }} @endif">
                <input type="text" id="alerta_montagem5" name="alerta_montagem5" class="form-control col-md-13"
                    value="@if (isset($fichatecnicas[0]->alerta_montagem5)) {{ $fichatecnicas[0]->alerta_montagem5 }} @else{{ '' }} @endif">
            </div>
        </div>
        <div class="form-group row">
            <label for="alerta_inspecao" class="col-sm-2 col-form-label text-right">Alerta inspeção</label>
            <div class="col-sm-10">
                <input type="text" id="alerta_inspecao1" name="alerta_inspecao1" class="form-control col-md-13"
                    value="@if (isset($fichatecnicas[0]->alerta_inspecao1)) {{ $fichatecnicas[0]->alerta_inspecao1 }} @else{{ '' }} @endif">
                <input type="text" id="alerta_inspecao2" name="alerta_inspecao2" class="form-control col-md-13"
                    value="@if (isset($fichatecnicas[0]->alerta_inspecao2)) {{ $fichatecnicas[0]->alerta_inspecao2 }} @else{{ '' }} @endif">
                <input type="text" id="alerta_inspecao3" name="alerta_inspecao3" class="form-control col-md-13"
                    value="@if (isset($fichatecnicas[0]->alerta_inspecao3)) {{ $fichatecnicas[0]->alerta_inspecao3 }} @else{{ '' }} @endif">
                <input type="text" id="alerta_inspecao4" name="alerta_inspecao4" class="form-control col-md-13"
                    value="@if (isset($fichatecnicas[0]->alerta_inspecao4)) {{ $fichatecnicas[0]->alerta_inspecao4 }} @else{{ '' }} @endif">
                <input type="text" id="alerta_inspecao5" name="alerta_inspecao5" class="form-control col-md-13"
                    value="@if (isset($fichatecnicas[0]->alerta_inspecao5)) {{ $fichatecnicas[0]->alerta_inspecao5 }} @else{{ '' }} @endif">
            </div>
        </div>
        <div class="form-group row">
            <label for="alerta_expedicao" class="col-sm-2 col-form-label text-right">Alerta expedição</label>
            <div class="col-sm-10">
                <input type="text" id="alerta_expedicao1" name="alerta_expedicao1" class="form-control col-md-13"
                    value="@if (isset($fichatecnicas[0]->alerta_expedicao1)) {{ $fichatecnicas[0]->alerta_expedicao1 }} @else{{ '' }} @endif">
                <input type="text" id="alerta_expedicao2" name="alerta_expedicao2" class="form-control col-md-13"
                    value="@if (isset($fichatecnicas[0]->alerta_expedicao2)) {{ $fichatecnicas[0]->alerta_expedicao2 }} @else{{ '' }} @endif">
                <input type="text" id="alerta_expedicao3" name="alerta_expedicao3" class="form-control col-md-13"
                    value="@if (isset($fichatecnicas[0]->alerta_expedicao3)) {{ $fichatecnicas[0]->alerta_expedicao3 }} @else{{ '' }} @endif">
                <input type="text" id="alerta_expedicao4" name="alerta_expedicao4" class="form-control col-md-13"
                    value="@if (isset($fichatecnicas[0]->alerta_expedicao4)) {{ $fichatecnicas[0]->alerta_expedicao4 }} @else{{ '' }} @endif">
                <input type="text" id="alerta_expedicao5" name="alerta_expedicao5" class="form-control col-md-13"
                    value="@if (isset($fichatecnicas[0]->alerta_expedicao5)) {{ $fichatecnicas[0]->alerta_expedicao5 }} @else{{ '' }} @endif">
            </div>
        </div>
        <input type="hidden" id='composicoes' name="composicoes" value=''>
        <div class="form-group row">
            <label for="status" class="col-sm-2 col-form-label"></label>
            <select class="form-control col-md-1" id="status" name="status">
                <option value="A" @if (isset($fichatecnicas[0]->status) && $fichatecnicas[0]->status == 'A') {{ ' selected ' }}@else @endif>Ativo</option>
                <option value="I" @if (isset($fichatecnicas[0]->status) && $fichatecnicas[0]->status == 'I') {{ ' selected ' }}@else @endif>Inativo
                </option>
            </select>
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
