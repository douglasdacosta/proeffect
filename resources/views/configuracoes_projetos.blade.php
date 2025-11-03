@extends('adminlte::page')

@section('title', 'Pro Effect')
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="js/bootstrap.4.6.2.js?cache={{time()}}"></script>
<script src="js/jquery.mask.js"></script>
<script src="js/main_custom.js"></script>
<script src="js/configuracoes.js"></script>
@switch($tela)
    @case('configuracoes_projetos')
        @section('content_header')
            <div class="form-group row">
                <h1 class="m-0 text-dark col-sm-6 col-form-label">Configurações de projetos</h1>

            </div>
        @stop
        @section('content')
        <form id="alterar_configuracoes" action="alterar-configuracoes-projetos" data-parsley-validate="" class="form-horizontal form-label-left" novalidate="" method="post">
                @csrf <!--{{ csrf_field() }}-->

                <h6>Horas para status Liberado para Projetos</h6>

                <input type="hidden" id="id" name="id"  value="@if (isset($configuracoes[0]->id)){{$configuracoes[0]->id}}@else{{''}}@endif">
                <div class="form-group row">
                    <label for="0_2_horas" class="col-sm-1 col-form-label text-right" title=''>0-2 Dias</label>
                    <div class="col-sm-1">
                        <input type="text" class="form-control" id="0_2_horas" placeholder="XX dias úteis" name="0_2_horas" value="@if (isset($configuracoes['0_2_horas'])){{$configuracoes['0_2_horas']}}@else{{''}}@endif">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="2_6_horas" class="col-sm-1 col-form-label text-right" title=''>2-6 Dias</label>
                    <div class="col-sm-1">
                        <input type="text" class="form-control" id="2_6_horas" placeholder="XX dias úteis" name="2_6_horas" value="@if (isset($configuracoes['2_6_horas'])){{$configuracoes['2_6_horas']}}@else{{''}}@endif">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="6_10_horas" class="col-sm-1 col-form-label text-right" title=''>6-10 Dias</label>
                    <div class="col-sm-1">
                        <input type="text" class="form-control" id="6_10_horas" placeholder="XX dias úteis" name="6_10_horas" value="@if (isset($configuracoes['6_10_horas'])){{$configuracoes['6_10_horas']}}@else{{''}}@endif">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="10_ou_mais_horas" class="col-sm-1 col-form-label text-right" title=''>10 ou mais Dias</label>
                    <div class="col-sm-1">
                        <input type="text" class="form-control" id="10_ou_mais_horas" placeholder="XX dias úteis" name="10_ou_mais_horas" value="@if (isset($configuracoes['10_ou_mais_horas'])){{$configuracoes['10_ou_mais_horas']}}@else{{''}}@endif">
                    </div>
                </div>

                <h6>Horas para status Desenvolvimento</h6>

                <div class="form-group row">
                    <label for="em_avaliacao" class="col-sm-1 col-form-label text-right" title=''>Em Avaliação</label>
                    <div class="col-sm-1">
                        <input type="text" class="form-control" id="em_avaliacao" placeholder="XX dias úteis" name="em_avaliacao" value="@if (isset($configuracoes['em_avaliacao'])){{$configuracoes['em_avaliacao']}}@else{{''}}@endif">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="elaboracao_design" class="col-sm-1 col-form-label text-right" title=''>Elaboração Design</label>
                    <div class="col-sm-1">
                        <input type="text" class="form-control" id="elaboracao_design" placeholder="XX dias úteis" name="elaboracao_design" value="@if (isset($configuracoes['elaboracao_design'])){{$configuracoes['elaboracao_design']}}@else{{''}}@endif">
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-2">
                        <button class="btn btn-danger" onclick="window.history.back();" type="button">Cancelar</button>
                    </div>
                    <div class="col-sm-5">
                        <button type="submit" class="btn btn-primary">Salvar </button>
                    </div>
                </div>
        </form>
        @stop
    @break

@endswitch
