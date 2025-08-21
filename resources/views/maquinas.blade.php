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
                <h1 class="m-0 text-dark col-sm-6 col-form-label text-right">Cadastro máquinas/pessoas</h1>

            </div>
            @stop
            @section('content')
            <form id="incluir" action="maquinas" data-parsley-validate="" class="form-horizontal form-label-left" novalidate="" method="post">
                {{-- <div class="container"> --}}
                    <div class="row">
                            @csrf <!--{{ csrf_field() }}-->
                        <div class="col-6">

                            <input type="hidden" id="id" name="id"  value="@if (isset($maquinas[0]->id)){{$maquinas[0]->id}}@else{{''}}@endif">
                            <div class="form-group row">
                                <label for="qtde_maquinas" class="col-sm-4 col-form-label text-right">Qtde máquinas</label>
                                <div class="col-sm-2">
                                <input type="text" class="form-control" id="qtde_maquinas"  name="qtde_maquinas" value="@if (isset($maquinas[0]->qtde_maquinas)){{$maquinas[0]->qtde_maquinas}}@else{{''}}@endif">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="horas_maquinas" class="col-sm-4 col-form-label text-right">Horas máquinas</label>
                                <div class="col-sm-2">
                                <input type="text" class="form-control mask_minutos" id="horas_maquinas"  name="horas_maquinas" value="@if (isset($maquinas[0]->horas_maquinas)){{PedidosController::formatarHoraMinuto($maquinas[0]->horas_maquinas)}}@else{{''}}@endif">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="pessoas_acabamento" class="col-sm-4 col-form-label text-right">Pessoas acabamento</label>
                                <div class="col-sm-2">
                                <input type="text" class="form-control" id="pessoas_acabamento"  name="pessoas_acabamento" value="@if (isset($maquinas[0]->pessoas_acabamento)){{$maquinas[0]->pessoas_acabamento}}@else{{''}}@endif">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="pessoas_montagem" class="col-sm-4 col-form-label text-right">Pessoas Montagem</label>
                                <div class="col-sm-2">
                                <input type="text" class="form-control" id="pessoas_montagem"  name="pessoas_montagem" value="@if (isset($maquinas[0]->pessoas_montagem)){{$maquinas[0]->pessoas_montagem}}@else{{''}}@endif">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="pessoas_montagem_torres" class="col-sm-4 col-form-label text-right">Pessoas Montagem torres</label>
                                <div class="col-sm-2">
                                <input type="text" class="form-control" id="pessoas_montagem_torres"  name="pessoas_montagem_torres" value="@if (isset($maquinas[0]->pessoas_montagem_torres)){{$maquinas[0]->pessoas_montagem_torres}}@else{{''}}@endif">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="pessoas_inspecao" class="col-sm-4 col-form-label text-right">Pessoas inspeção</label>
                                <div class="col-sm-2">
                                <input type="text" class="form-control" id="pessoas_inspecao"  name="pessoas_inspecao" value="@if (isset($maquinas[0]->pessoas_inspecao)){{$maquinas[0]->pessoas_inspecao}}@else{{''}}@endif">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="horas_dia" class="col-sm-4 col-form-label text-right">Horas dias</label>
                                <div class="col-sm-2">
                                <input type="text" class="form-control mask_minutos" id="horas_dia"  name="horas_dia" value="@if (isset($maquinas[0]->horas_dia)){{PedidosController::formatarHoraMinuto($maquinas[0]->horas_dia)}}@else{{''}}@endif">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="horas_dia" class="col-sm-4 col-form-label text-right">Prazo entrega (dias)</label>
                                <div class="col-sm-2">
                                <input type="text" class="form-control " id="prazo_entrega"  name="prazo_entrega" value="@if (isset($maquinas[0]->prazo_entrega)){{$maquinas[0]->prazo_entrega}}@else{{''}}@endif">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="prazo_usinagem" class="col-sm-4 col-form-label text-right">Prazo usinagem (dias)</label>
                                <div class="col-sm-2">
                                <input type="text" class="form-control " id="prazo_usinagem"  name="prazo_usinagem" value="@if (isset($maquinas[0]->prazo_usinagem)){{$maquinas[0]->prazo_usinagem}}@else{{''}}@endif">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="prazo_acabamento" class="col-sm-4 col-form-label text-right">Prazo acabamento (dias)</label>
                                <div class="col-sm-2">
                                <input type="text" class="form-control " id="prazo_acabamento"  name="prazo_acabamento" value="@if (isset($maquinas[0]->prazo_acabamento)){{$maquinas[0]->prazo_acabamento}}@else{{''}}@endif">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="prazo_montagem" class="col-sm-4 col-form-label text-right">Prazo montagem (dias)</label>
                                <div class="col-sm-2">
                                <input type="text" class="form-control " id="prazo_montagem"  name="prazo_montagem" value="@if (isset($maquinas[0]->prazo_montagem)){{$maquinas[0]->prazo_montagem}}@else{{''}}@endif">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="prazo_inspecao" class="col-sm-4 col-form-label text-right">Prazo inspecao (dias)</label>
                                <div class="col-sm-2">
                                <input type="text" class="form-control " id="prazo_inspecao"  name="prazo_inspecao" value="@if (isset($maquinas[0]->prazo_inspecao)){{$maquinas[0]->prazo_inspecao}}@else{{''}}@endif">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="prazo_embalar" class="col-sm-4 col-form-label text-right">Prazo embalar (dias)</label>
                                <div class="col-sm-2">
                                <input type="text" class="form-control " id="prazo_embalar"  name="prazo_embalar" value="@if (isset($maquinas[0]->prazo_embalar)){{$maquinas[0]->prazo_embalar}}@else{{''}}@endif">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="prazo_expedicao" class="col-sm-4 col-form-label text-right">Prazo expedicao (dias)</label>
                                <div class="col-sm-2">
                                <input type="text" class="form-control " id="prazo_expedicao"  name="prazo_expedicao" value="@if (isset($maquinas[0]->prazo_expedicao)){{$maquinas[0]->prazo_expedicao}}@else{{''}}@endif">
                                </div>
                            </div>
                        </div>
                        <div class="col-6" >
                            <div class="form-group row">
                                <label for="observacao" class="col-sm-4 col-form-label text-right">Horas de trabalho</label>
                                <div class="col-sm-2">
                                    Início expediente
                                </div>
                                <div class="col-sm-2">
                                    Horário início de almoço
                                </div>
                                <div class="col-sm-2">
                                    Horário final de almoço
                                </div>
                                <div class="col-sm-2">
                                    Final expediente
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="segunda_inicio" class="col-sm-4 col-form-label text-right">Segunda</label>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control mask_minutos" placeholder="00:00" id="segunda_inicio"  name="segunda_inicio" value="@if (isset($maquinas[0]->segunda_inicio)){{PedidosController::formatarHoraMinuto($maquinas[0]->segunda_inicio)}}@else{{''}}@endif">
                                </div>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control mask_minutos" placeholder="00:00" id="segunda_almoco_inicio"  name="segunda_almoco_inicio" value="@if (isset($maquinas[0]->segunda_almoco_inicio)){{PedidosController::formatarHoraMinuto($maquinas[0]->segunda_almoco_inicio)}}@else{{''}}@endif">
                                </div>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control mask_minutos" placeholder="00:00" id="segunda_almoco_fim"  name="segunda_almoco_fim" value="@if (isset($maquinas[0]->segunda_almoco_fim)){{PedidosController::formatarHoraMinuto($maquinas[0]->segunda_almoco_fim)}}@else{{''}}@endif">
                                </div>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control mask_minutos" placeholder="00:00" id="segunda_fim"  name="segunda_fim" value="@if (isset($maquinas[0]->segunda_fim)){{PedidosController::formatarHoraMinuto($maquinas[0]->segunda_fim)}}@else{{''}}@endif">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="segunda_inicio" class="col-sm-4 col-form-label text-right">Terça</label>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control mask_minutos" placeholder="00:00" id="terca_inicio"  name="terca_inicio" value="@if (isset($maquinas[0]->terca_inicio)){{PedidosController::formatarHoraMinuto($maquinas[0]->terca_inicio)}}@else{{''}}@endif">
                                </div>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control mask_minutos" placeholder="00:00" id="terca_almoco_inicio"  name="terca_almoco_inicio" value="@if (isset($maquinas[0]->terca_almoco_inicio)){{PedidosController::formatarHoraMinuto($maquinas[0]->terca_almoco_inicio)}}@else{{''}}@endif">
                                </div>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control mask_minutos" placeholder="00:00" id="terca_almoco_fim"  name="terca_almoco_fim" value="@if (isset($maquinas[0]->terca_almoco_fim)){{PedidosController::formatarHoraMinuto($maquinas[0]->terca_almoco_fim)}}@else{{''}}@endif">
                                </div>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control mask_minutos" placeholder="00:00" id="terca_fim"  name="terca_fim" value="@if (isset($maquinas[0]->terca_fim)){{PedidosController::formatarHoraMinuto($maquinas[0]->terca_fim)}}@else{{''}}@endif">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="quarta_inicio" class="col-sm-4 col-form-label text-right">Quarta</label>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control mask_minutos" placeholder="00:00" id="quarta_inicio"  name="quarta_inicio" value="@if (isset($maquinas[0]->quarta_inicio)){{PedidosController::formatarHoraMinuto($maquinas[0]->quarta_inicio)}}@else{{''}}@endif">
                                </div>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control mask_minutos" placeholder="00:00" id="quarta_almoco_inicio"  name="quarta_almoco_inicio" value="@if (isset($maquinas[0]->quarta_almoco_inicio)){{PedidosController::formatarHoraMinuto($maquinas[0]->quarta_almoco_inicio)}}@else{{''}}@endif">
                                </div>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control mask_minutos" placeholder="00:00" id="quarta_almoco_fim"  name="quarta_almoco_fim" value="@if (isset($maquinas[0]->quarta_almoco_fim)){{PedidosController::formatarHoraMinuto($maquinas[0]->quarta_almoco_fim)}}@else{{''}}@endif">
                                </div>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control mask_minutos" placeholder="00:00" id="quarta_fim"  name="quarta_fim" value="@if (isset($maquinas[0]->quarta_fim)){{PedidosController::formatarHoraMinuto($maquinas[0]->quarta_fim)}}@else{{''}}@endif">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="quinta_inicio" class="col-sm-4 col-form-label text-right">Quinta</label>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control mask_minutos" placeholder="00:00" id="quinta_inicio"  name="quinta_inicio" value="@if (isset($maquinas[0]->quinta_inicio)){{PedidosController::formatarHoraMinuto($maquinas[0]->quinta_inicio)}}@else{{''}}@endif">
                                </div>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control mask_minutos" placeholder="00:00" id="quinta_almoco_inicio"  name="quinta_almoco_inicio" value="@if (isset($maquinas[0]->quinta_almoco_inicio)){{PedidosController::formatarHoraMinuto($maquinas[0]->quinta_almoco_inicio)}}@else{{''}}@endif">
                                </div>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control mask_minutos" placeholder="00:00" id="quinta_almoco_fim"  name="quinta_almoco_fim" value="@if (isset($maquinas[0]->quinta_almoco_fim)){{PedidosController::formatarHoraMinuto($maquinas[0]->quinta_almoco_fim)}}@else{{''}}@endif">
                                </div>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control mask_minutos" placeholder="00:00" id="quinta_fim"  name="quinta_fim" value="@if (isset($maquinas[0]->quinta_fim)){{PedidosController::formatarHoraMinuto($maquinas[0]->quinta_fim)}}@else{{''}}@endif">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="sexta_inicio" class="col-sm-4 col-form-label text-right">Sexta</label>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control mask_minutos" placeholder="00:00" id="sexta_inicio"  name="sexta_inicio" value="@if (isset($maquinas[0]->sexta_inicio)){{PedidosController::formatarHoraMinuto($maquinas[0]->sexta_inicio)}}@else{{''}}@endif">
                                </div>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control mask_minutos" placeholder="00:00" id="sexta_almoco_inicio"  name="sexta_almoco_inicio" value="@if (isset($maquinas[0]->sexta_almoco_inicio)){{PedidosController::formatarHoraMinuto($maquinas[0]->sexta_almoco_inicio)}}@else{{''}}@endif">
                                </div>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control mask_minutos" placeholder="00:00" id="sexta_almoco_fim"  name="sexta_almoco_fim" value="@if (isset($maquinas[0]->sexta_almoco_fim)){{PedidosController::formatarHoraMinuto($maquinas[0]->sexta_almoco_fim)}}@else{{''}}@endif">
                                </div>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control mask_minutos" placeholder="00:00" id="sexta_fim"  name="sexta_fim" value="@if (isset($maquinas[0]->sexta_fim)){{PedidosController::formatarHoraMinuto($maquinas[0]->sexta_fim)}}@else{{''}}@endif">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="sabado_inicio" class="col-sm-4 col-form-label text-right">Sábado</label>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control mask_minutos" placeholder="00:00" id="sabado_inicio"  name="sabado_inicio" value="@if (isset($maquinas[0]->sabado_inicio)){{PedidosController::formatarHoraMinuto($maquinas[0]->sabado_inicio)}}@else{{''}}@endif">
                                </div>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control mask_minutos" placeholder="00:00" id="sabado_almoco_inicio"  name="sabado_almoco_inicio" value="@if (isset($maquinas[0]->sabado_almoco_inicio)){{PedidosController::formatarHoraMinuto($maquinas[0]->sabado_almoco_inicio)}}@else{{''}}@endif">
                                </div>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control mask_minutos" placeholder="00:00" id="sabado_almoco_fim"  name="sabado_almoco_fim" value="@if (isset($maquinas[0]->sabado_almoco_fim)){{PedidosController::formatarHoraMinuto($maquinas[0]->sabado_almoco_fim)}}@else{{''}}@endif">
                                </div>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control mask_minutos" placeholder="00:00" id="sabado_fim"  name="sabado_fim" value="@if (isset($maquinas[0]->sabado_fim)){{PedidosController::formatarHoraMinuto($maquinas[0]->sabado_fim)}}@else{{''}}@endif">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="domingo_inicio" class="col-sm-4 col-form-label text-right">Domingo</label>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control mask_minutos" placeholder="00:00" id="domingo_inicio"  name="domingo_inicio" value="@if (isset($maquinas[0]->domingo_inicio)){{PedidosController::formatarHoraMinuto($maquinas[0]->domingo_inicio)}}@else{{''}}@endif">
                                </div>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control mask_minutos" placeholder="00:00" id="domingo_almoco_inicio"  name="domingo_almoco_inicio" value="@if (isset($maquinas[0]->domingo_almoco_inicio)){{PedidosController::formatarHoraMinuto($maquinas[0]->domingo_almoco_inicio)}}@else{{''}}@endif">
                                </div>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control mask_minutos" placeholder="00:00" id="domingo_almoco_fim"  name="domingo_almoco_fim" value="@if (isset($maquinas[0]->domingo_almoco_fim)){{PedidosController::formatarHoraMinuto($maquinas[0]->domingo_almoco_fim)}}@else{{''}}@endif">
                                </div>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control mask_minutos" placeholder="00:00" id="domingo_fim"  name="domingo_fim" value="@if (isset($maquinas[0]->domingo_fim)){{PedidosController::formatarHoraMinuto($maquinas[0]->domingo_fim)}}@else{{''}}@endif">
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                                <div class="form-group row ">
                                    <div class="col-sm-4">
                                    </div>
                                    <div class="col-sm-4">
                                        <button type="submit" class="btn btn-primary">Salvar</button>
                                    </div>
                                </div>
                            </div>
                    </div>
                {{-- </div> --}}
            </form>
        @stop
    @break

@endswitch
