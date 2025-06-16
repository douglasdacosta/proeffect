@extends('adminlte::page')

@section('title', 'Pro Effect')
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="js/bootstrap.4.6.2.js?cache={{time()}}"></script>
<script src="js/jquery.mask.js"></script>
<script src="js/main_custom.js"></script>
<script src="js/configuracoes.js"></script>
@switch($tela)
    @case('configuracoes')
        @section('content_header')
            <div class="form-group row">
                <h1 class="m-0 text-dark col-sm-6 col-form-label">Configurações</h1>

            </div>
        @stop
        @section('content')
        <form id="alterar_configuracoes" action="alterar-configuracoes" data-parsley-validate="" class="form-horizontal form-label-left" novalidate="" method="post">
                @csrf <!--{{ csrf_field() }}-->
                <input type="hidden" id="id" name="id"  value="@if (isset($configuracoes[0]->id)){{$configuracoes[0]->id}}@else{{''}}@endif">
                <div class="form-group row">
                    <label for="consumo_medio_mensal" class="col-sm-3 col-form-label text-right" title='Tempo de cálculo de consumo médio mensal em meses'>Consumo médio mensal (meses)</label>
                    <div class="col-sm-1">
                        <input type="text" class="form-control" id="consumo_medio_mensal"  name="consumo_medio_mensal" value="@if (isset($configuracoes['consumo_medio_mensal'])){{$configuracoes['consumo_medio_mensal']}}@else{{''}}@endif">
                    </div>
                </div>

                @foreach($categorias as $categoria)
                    <div class="form-group row">
                        <label for="consumo_medio_mensal" class="col-sm-3 col-form-label text-right" title='Tempo de cálculo de consumo médio mensal em meses'>{{$categoria->nome}}  (meses)</label>
                        <div class="col-sm-1">
                            <input type="text" class="form-control" id="categorias_{{$categoria->id}}"  name="categoria_{{$categoria->id}}" value="{{ $configuracoes['categoria_'.$categoria->id] }}">
                        </div>

                    </div>
                @endforeach

                <div class="form-group row">
                    <label for="percentual_usinagem_acabamento" class="col-sm-3 col-form-label text-right" title='Percentual de acabamento baseado no tempo de usinagem'>Percentual de usinagem no acabamento(%)</label>
                    <div class="col-sm-1">
                        <input type="text" class="form-control" id="percentual_usinagem_acabamento"  name="percentual_usinagem_acabamento" value="@if (isset($configuracoes['percentual_usinagem_acabamento'])){{$configuracoes['percentual_usinagem_acabamento']}}@else{{''}}@endif">
                    </div>
                </div>

                <input type="hidden" id="tipo_atualizacao" name="tipo_atualizacao"  value="0">

                <div class="form-group row">
                    <div class="col-sm-2">
                        <button class="btn btn-danger" onclick="window.history.back();" type="button">Cancelar</button>
                    </div>
                    <div class="col-sm-2">
                            <button type="button" class="btn btn-warning" id="atualiza_dados">Salvar e aplicar atualizações</button>
                        </div>
                    <div class="col-sm-5">
                        <button type="submit" class="btn btn-primary">Salvar sem aplicar atualizações</button>
                    </div>
                </div>
        </form>
        @stop
    @break

@endswitch
