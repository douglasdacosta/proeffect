<?php
    use App\Http\Controllers\PedidosController;
?>
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
            <div class="toast fade show" role="alert" style="width: 350px" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <strong class="mr-auto">Alerta!</strong>
                    <small></small>
                    <button data-dismiss="toast" type="button" class="ml-2 mb-1 close" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="toast-body textoAlerta" style="text-decoration-style: solid; font-weight: bold; font-size: larger;">
                </div>
            </div>
        </div>
        @if ($tela == 'alterar')
            @section('content_header')
                <h4 class="m-0 text-dark">Orçamentos</h4>
            @stop
            <form id="alterar" class="form_ficha" action="{{ $rotaAlterar }}" data-parsley-validate=""
                class="form-horizontal form-label-left" novalidate="" method="post">
                <div class="form-group row">
                    <div class="col-sm-2">
                        <input type="hidden" id="id" name="id" class="form-control col-md-7 col-xs-12"
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
        </div>
        <div class="form-group row">
            <table class="table table-sm table-striped text-center" id="table_composicao_orcamento">
                <thead style="background-color: #b6b3b3">
                    <tr>
                        <th scope="col">Blank</th>
                        <th scope="col">tmp Usin</th>
                        <th scope="col">Uso%</th>
                        <th scope="col">BL/CJ</th>
                        <th scope="col">Material</th>
                        <th scope="col">BL/CH</th>
                        <th scope="col">Medida X</th>
                        <th scope="col">Medida Y</th>
                        <th scope="col">Valor Chapa</th>
                        <th style="border: solid;" scope="col">MO</th>
                        <th style="border: solid;" scope="col">MP</th>
                    </tr>
                </thead>
                <tbody>

                    @if (isset($fichatecnicasitens))
                        <?php $count = 0 ?>
                        @foreach ($fichatecnicasitens as $key => $fichatecnicaitem)
                            <tr>
                                <td data-name="blank{{'_'.$count}}" class="blank{{'_'.$count}}" scope="row">@if(trim($fichatecnicaitem->blank) != '') {{trim($fichatecnicaitem->blank)}} @else {{ ''}} @endif</td>
                                <td data-name="tmp{{'_'.$count}}" class="tmp{{'_'.$count}}">@if(trim($fichatecnicaitem->blank) == '') {{ ''}} @else {{ PedidosController::formatarMinutoSegundo($fichatecnicaitem->tempo_usinagem) }}@endif</td>
                                <td data-name="uso{{'_'.$count}}" class="uso{{'_'.$count}}">@if($fichatecnicaitem->blank != '') {{ $percentuais[$key]['percentual'].'%' }} @else {{ ''}} @endif</td>
                                <td data-name="qtde{{'_'.$count}}" class="qtde{{'_'.$count}}">@if($fichatecnicaitem->blank != '') {{$fichatecnicaitem->qtde_blank}} @else {{''}} @endif</td>
                                <td data-name="material_id{{'_'.$count}}" class="material_id{{'_'.$count}}"data-materialid="{{ trim($fichatecnicaitem->materiais_id) }}">
                                    @if(trim($fichatecnicaitem->materiais->material) != ''){{ trim($fichatecnicaitem->materiais->material) }}@else {{ ''}} @endif
                                </td>
                                <td data-name="qtdeCH{{'_'.$count}}" class="qtdeCH{{'_'.$count}}">{{ $percentuais[$key]['blank_por_chapa']}}</td>
                                <td data-name="medidax{{'_'.$count}}" class="medidax{{'_'.$count}}">@if(trim($fichatecnicaitem->medidax)!='') {{ trim($fichatecnicaitem->medidax) }} @else {{ ''}} @endif</td>
                                <td data-name="mediday{{'_'.$count}}" class="mediday{{'_'.$count}}">@if(trim($fichatecnicaitem->mediday)!='') {{ trim($fichatecnicaitem->mediday) }} @else {{ ''}} @endif</td>
                                <td data-name="valor_chapa{{'_'.$count}}" class="valor_chapa{{'_'.$count}}">{{number_format($fichatecnicaitem->tabelaMateriais->valor, 2, ',', '')}} </td>
                                <td style="border-left: solid; border-right: solid;" data-name="valorMO{{'_'.$count}}" class="valorMO{{'_'.$count}}"></td>
                                <td style="border-left: solid; border-right: solid;"data-name="valorMP{{'_'.$count}}" class="valorMP{{'_'.$count}}"></td>
                            </tr>
                            <?php $count++ ?>
                        @endforeach
                    @endif
                </tbody>
                    <tfoot>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th style="border-left: solid; border-right: solid;"></th>
                        <th style="border-left: solid; border-right: solid;"></th>
                    </tr>
                    <tr>
                        <th scope="col">&nbsp;</th>
                        <th scope="col">&nbsp;</th>
                        <th scope="col">&nbsp;</th>
                        <th scope="col">&nbsp;</th>
                        <th scope="col">&nbsp;</th>
                        <th scope="col">&nbsp;</th>
                        <th scope="col">&nbsp;</th>
                        <th scope="col">&nbsp;</th>
                        <th style="border: solid;" scope="col">Sub Total</th>
                        <th style="border: solid;" scope="col" class='subTotalMO'>0</th>
                        <th style="border: solid;" scope="col" class='subTotalMP'>0</th>
                    </tr>
                    <tr>
                        <th scope="col">&nbsp;</th>
                        <th scope="col">&nbsp;</th>
                        <th scope="col">&nbsp;</th>
                        <th scope="col">&nbsp;</th>
                        <th scope="col">&nbsp;</th>
                        <th scope="col">&nbsp;</th>
                        <th scope="col">&nbsp;</th>
                        <th scope="col">&nbsp;</th>
                        <th style="border: solid;"scope="col">CI</th>
                        <th style="border: solid;" scope="col"></th>
                        <th style="border: solid;" scope="col" class='subTotalCI'>0</th>
                    </tr>
                </tfoot>

            </table>

        </div>
        <hr class="my-1">


        <div class="form-group row">
            <label for="tm_fresa_total" class="col-sm-1 col-form-label text-right">Tempo fresa total </label>
            <div class="col-sm-1">
                <input type="text" id="tm_fresa_total" readonly name="tm_fresa_total" class="form-control col-md-13" value="{{$tempo_fresa_total}}">
            </div>

            <label for="calculo_hora_fresa" class="col-sm-1 col-form-label text-right">Hora de fresa</label>
            <div class="col-sm-1">
                <input type="text" id="calculo_hora_fresa" name="calculo_hora_fresa" class="form-control col-md-13 mask_valor" value="480,00">
            </div>

            <label for="rv" class="col-sm-1 col-form-label text-right">RV.</label>
            <div class="col-sm-1">
                <input type="text" id="rv" name="rv" class="form-control col-md-13">
            </div>
            <div class="col-sm-1">
                <div class="overlay" style="display: none;">
                    <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                </div>
            </div>
            <div class="col-sm-5">
                <table class="table table-sm table-striped text-center" id="table_composicao">
                    <thead style="background-color: #b6b3b3">
                        <tr>
                            <th scope="col">10</th>
                            <th scope="col">20</th>
                            <th scope="col">30</th>
                            <th scope="col">40</th>
                            <th scope="col">50</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="col" class='desc_10_total'>0</th>
                            <th scope="col" class='desc_20_total'>0</th>
                            <th scope="col" class='desc_30_total'>0</th>
                            <th scope="col" class='desc_40_total'>0</th>
                            <th scope="col" class='desc_50_total'>0</th>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-sm-5">
            <table class="table table-sm table-striped text-center" id="tabela_rev">
                <thead style="background-color: #b6b3b3">
                    <tr>
                        <th scope="col">RV</th>
                        <th scope="col">Data</th>
                        <th scope="col">Carregar</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
        <hr class="my-4">
        <div class="form-group row">
            <div class="col-sm-10">
                <button class="btn btn-danger" onclick="window.history.back();" type="button">Cancelar</button>
            </div>
            <div class="col-sm-2">
                <button type="button" id="salvar_orcamento" class="btn btn-primary">Salvar</button>
            </div>
        </div>
        </form>
    @stop
@endif
