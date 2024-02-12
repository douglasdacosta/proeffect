<?php use \App\Http\Controllers\PedidosController; ?>

@extends('adminlte::page')

@section('title', 'Pro Effect')

<script src="../vendor/jquery/jquery.min.js"></script>
<script src="js/jquery.mask.js"></script>
<script src="js/main_custom.js"></script>

@switch($tela)
    @case('maquinas')
        @section('content_header')
            <div class="form-group row">
                <h1 class="m-0 text-dark col-sm-6 col-form-label">Cadastro máquinas/pessoas</h1>

            </div>
        @stop
        @section('content')
        <form id="incluir" action="maquinas" data-parsley-validate="" class="form-horizontal form-label-left" novalidate="" method="post">
                @csrf <!--{{ csrf_field() }}-->
                <input type="hidden" id="id" name="id"  value="@if (isset($maquinas[0]->id)){{$maquinas[0]->id}}@else{{''}}@endif">
                <div class="form-group row">
                    <label for="qtde_maquinas" class="col-sm-3 col-form-label">Qtde máquinas</label>
                    <div class="col-sm-1">
                    <input type="text" class="form-control" id="qtde_maquinas"  name="qtde_maquinas" value="@if (isset($maquinas[0]->qtde_maquinas)){{$maquinas[0]->qtde_maquinas}}@else{{''}}@endif">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="horas_maquinas" class="col-sm-3 col-form-label">Horas máquinas</label>
                    <div class="col-sm-1">
                    <input type="text" class="form-control mask_minutos" id="horas_maquinas"  name="horas_maquinas" value="@if (isset($maquinas[0]->horas_maquinas)){{PedidosController::formatarHoraMinuto($maquinas[0]->horas_maquinas)}}@else{{''}}@endif">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="pessoas_acabamento" class="col-sm-3 col-form-label">Pessoas acabamento</label>
                    <div class="col-sm-1">
                    <input type="text" class="form-control" id="pessoas_acabamento"  name="pessoas_acabamento" value="@if (isset($maquinas[0]->pessoas_acabamento)){{$maquinas[0]->pessoas_acabamento}}@else{{''}}@endif">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="pessoas_montagem" class="col-sm-3 col-form-label">Pessoas Montagem</label>
                    <div class="col-sm-1">
                    <input type="text" class="form-control" id="pessoas_montagem"  name="pessoas_montagem" value="@if (isset($maquinas[0]->pessoas_montagem)){{$maquinas[0]->pessoas_montagem}}@else{{''}}@endif">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="pessoas_inspecao" class="col-sm-3 col-form-label">Pessoas inspeção</label>
                    <div class="col-sm-1">
                    <input type="text" class="form-control" id="pessoas_inspecao"  name="pessoas_inspecao" value="@if (isset($maquinas[0]->pessoas_inspecao)){{$maquinas[0]->pessoas_inspecao}}@else{{''}}@endif">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="horas_dia" class="col-sm-3 col-form-label">Horas dias</label>
                    <div class="col-sm-1">
                    <input type="text" class="form-control mask_minutos" id="horas_dia"  name="horas_dia" value="@if (isset($maquinas[0]->horas_dia)){{PedidosController::formatarHoraMinuto($maquinas[0]->horas_dia)}}@else{{''}}@endif">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-5">
                    </div>
                    <div class="col-sm-5">
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </div>
                </div>
        </form>
        @stop
    @break

@endswitch
