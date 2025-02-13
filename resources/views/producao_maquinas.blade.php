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
        </div>
    </div>
    @stop
    @section('content')
    <div class="right_col" role="main">

        <form id="filtro" action="producao-maquinas" method="get" class="form-horizontal form-label-left">
            <div class="form-group row">
                <label for="numero_cnc" class="col-sm-1 col-form-label text-right">Máquina</label>
                <div class="col-sm-1">
                    <input type="text" id="numero_cnc" name="numero_cnc" class="form-control col-md-13" value="@if (isset($request) && $request->input('numero_cnc') != ''){{$request->input('numero_cnc')}}@else{{''}}@endif">
                </div>

                <label  for="created_at" class="col-sm-1 col-form-label text-right">Data: de </label>
                <div class="col-sm-1">
                    <input type="text" class="form-control mask_date" id="created_at" name="created_at"
                        placeholder="DD/MM/AAAA" value="@if (isset($request) && $request->input('created_at') != ''){{$request->input('created_at')}} @else{{''}}@endif">
                </div>
                <label for="created_at_fim" class=" col-form-label text-right">até</label>
                <div class="col-sm-1">
                    <input type="text" class="form-control mask_date" id="created_at_fim" name="created_at_fim"
                        placeholder="DD/MM/AAAA" value="@if (isset($request) && $request->input('created_at_fim') != ''){{$request->input('created_at_fim')}}@else{{''}}@endif">
                </div>

                <label  for="hora" class="col-sm-1 col-form-label text-right">Hora: de</label>
                <div class="col-sm-1">
                    <input type="text" class="form-control mask_horas" id="hora" name="hora"
                        placeholder="00:00:00" value="@if (isset($request) && $request->input('hora') != ''){{$request->input('hora')}}@else{{''}}@endif">
                </div>
                <label for="hora_fim" class="col-form-label text-right">até</label>
                <div class="col-sm-1">
                    <input type="text" class="form-control mask_horas" id="hora_fim" name="hora_fim"
                        placeholder="00:00:00" value="@if (isset($request) && $request->input('hora_fim') != ''){{$request->input('hora_fim')}}@else{{''}}@endif">
                </div>
                <label for="listarpor" class="col-sm-1 col-form-label">Listar por</label>
                <select class="form-control col-md-1" id="listarpor" name="listarpor">
                    <option value="0" @if (isset($request) && $request->input('listarpor') == '0'){{ ' selected '}}@else @endif>Data</option>
                    <option value="1" @if (isset($request) && $request->input('listarpor')  == '1'){{ ' selected '}}@else @endif>Hora</option>
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
                        <th>Máquina</th>
                        <th>Turno</th>
                        <th>Data Hora</th>
                        <th>Horas de Trabalho</th>
                        <th>Horas de Usinagem</th>
                        <th>% de horas</th>
                        <th>D. de Usinagem</th>
                        <th>N. de trabalho</th>
                    </tr>
                  </thead>
                        @if(isset($producao_maquinas))
                            <?php
                                        $turno_antes = '';
                                        $cor = $colors[array_rand($colors)];
                            ?>
                            @foreach ($producao_maquinas as $producao_maquina)
                                @if ($turno_antes != '' && $producao_maquina['turno'] != $turno_antes)
                                    <?php $cor = $colors[array_rand($colors)] ?>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                @endif
                                    <tr style="background-color: {{$cor}}" >
                                        <td scope="row">{{$producao_maquina['maquina_cnc']}}</td>
                                        <td>{{$producao_maquina['turno']}}</td>
                                        <td>{{$producao_maquina['data']}}</td>
                                        <td>{{$producao_maquina['horasTrabalho']}}</td>
                                        <td>{{$producao_maquina['total_horas_usinadas']}}</td>
                                        <td>{{$producao_maquina['percentual'].'%'}}</td>
                                        <td>{{$producao_maquina['metrosPercorridos']}}</td>
                                        <td>{{$producao_maquina['qtdeServico']}}</td>
                                    </tr>

                                    <?php
                                        $turno_antes = $producao_maquina['turno']
                                    ?>
                            @endforeach
                        @endif
                        <tfoot>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th>{{$totais['total_horas_tabalhadas']}}</th>
                            <th>{{$totais['total_horas_usinadas']}}</th>
                            <th>{{$totais['total_pecentual'].'%'}}</th>
                            <th></th>
                            <th></th>
                        </tfoot>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
    </div>

    @stop

@endif
