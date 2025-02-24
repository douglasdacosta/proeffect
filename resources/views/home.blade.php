@extends('adminlte::page')

@section('title', 'Pro Effect')

@section('adminlte_css')

    <link rel="stylesheet" href="{{ asset('css/home.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/home.css') }}" />

@stop

<script src="../vendor/jquery/jquery.min.js"></script>
<script src="js/funcoes.js"></script>

@section('content')

    <div class="container dark-mode">
        @if(in_array('1', $perfis_dashboards))
            <div class="right_col" role="main">

                <div class="form-group row col-md-12 vendas">
                    <div class="col-6 col-sm-6 col-md-3 ">
                        <div class="info-box mb-3 fundo-escuro fundo-escuro">
                            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-arrow-circle-up"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Vendas do Dia - {{$vendas['qtde_vendas_dia']}} OS</span>
                                <span class="info-box-number">{{$vendas['vendas_dia']}}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-sm-6 col-md-3">
                        <div class="info-box mb-3 fundo-escuro">
                            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-arrow-circle-down"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Entregas do Dia - {{$vendas['qtde_entregas_dia']}} OS</span>
                                <span class="info-box-number">{{$vendas['entregas_dia']}}</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-sm-6 col-md-3">
                        <div class="info-box mb-3 fundo-escuro">
                            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-arrow-circle-up"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Vendas Mensal - {{$vendas['qtde_vendas_mes']}} OS</span>
                                <span class="info-box-number">{{$vendas['vendas_mensal']}}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-sm-6 col-md-3">
                        <div class="info-box mb-3 fundo-escuro">
                            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-arrow-circle-down"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Entregas Mensal - {{$vendas['qtde_entregas_mensal']}} OS</span>
                                <span class="info-box-number">{{$vendas['entregas_mensal']}}</span>
                            </div>
                        </div>
                    </div>

                </div>


            </div>
        @endif
        @if(in_array('2', $perfis_dashboards) || in_array('3', $perfis_dashboards))
            <div class="row">
                <div class="col-md-12">
                    <div class="card-body">
                        <div class="row">
                            @if(in_array('2', $perfis_dashboards))
                                <div class="col-sm-6 col-6">
                                    <!-- small card -->
                                    <div class="small-box bg-danger text-center">
                                        <div class="inner">
                                            <h4>Você tem <span class="counter">{{ $os_atraso }}</span> OS em atraso</h4>
                                        </div>
                                        <div class="icon">
                                            <i class="fas fa-chart-pie"></i>
                                        </div>
                                        <a href="/followup?os=&ep=&tipo_consulta=F&data_entrega=&data_entrega_fim={{$data_atraso}}&status_id[]=1&status_id[]=2&status_id[]=3&status_id[]=4&status_id[]=5&status_id[]=6&status_id[]=7&status_id[]=8&status_id[]=9&status_id[]=10" class="small-box-footer">
                                            Ver mais <i class="fas fa-arrow-circle-right"></i>
                                        </a>
                                    </div>
                                </div>
                            @endif
                            @if(in_array('3', $perfis_dashboards))
                                <div class="col-sm-6 col-6">
                                    <div class="description-block ">
                                        @if ($comparativo_percentual < 0)
                                            <span class="description-percentage text-danger text-bold">
                                                <i class="fas fa-caret-down"></i>
                                                {{$comparativo_percentual}}%
                                            </span>
                                        @else
                                            <span class="description-percentage text-success text-bold">
                                                <i class="fas fa-caret-up"></i>
                                                {{$comparativo_percentual}}%
                                            </span>
                                        @endif

                                        <h5 class="description-header">{{ $comparativo_valor }}</h5>
                                        <span class="description-text">Comparativo mês anterior</span>
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if(in_array('4', $perfis_dashboards))
            <div class="form-group row col-md-12">
                <div class="form-group col-md-6">
                    <div class="card">
                        <div class="card-header border-transparent text-center">
                        <p>
                                <span class="texto-previsto"><i class="fas fa-caret-right text-warning"></i> MP Previsto para 30 dias</span>
                                <a href="/relatorio-previsao-material?data=&data_fim={{$data_30}}&tipo_consulta=P&categorias=&status_id[]=1&status_id[]=2&status_id[]=3&status_id[]=4&status_id[]=5&status_id[]=6&status_id[]=7&status_id[]=8" class="small-box-footer col-md-2">
                                    Ver mais <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </p>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body p-0 previstos" >
                            <div class="table-responsive">
                                <table class="table m-0">
                                    <thead>
                                    </thead>
                                    <tbody>

                                        @foreach ($array_material_alerta_30 as $key => $previsto_30)
                                            <tr>
                                                <td>{{$previsto_30['material']}} | {{ $previsto_30['diferenca']}}</td>
                                            </tr>
                                        @endforeach


                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group col-md-6">
                    <div class="card fundo-escuro ">
                        <div class="card-header border-transparent text-center">
                            <p>
                                <span class="texto-previsto"><i class="fas fa-caret-right text-warning"></i> MP Previsto para 60 dias</span>
                                <a href="/relatorio-previsao-material?data=&data_fim={{$data_60}}&tipo_consulta=P&categorias=&status_id[]=1&status_id[]=2&status_id[]=3&status_id[]=4&status_id[]=5&status_id[]=6&status_id[]=7&status_id[]=8" class="small-box-footer col-md-2">
                                    Ver mais <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </p>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body p-0 previstos">
                            <div class="table-responsive">
                                <table class="table m-0">
                                    <thead>
                                    </thead>
                                    <tbody>
                                        @foreach ($array_material_alerta_60 as $key => $previsto_60)
                                            <tr>
                                                <td>{{$previsto_60['material']}} | {{ $previsto_60['diferenca']}}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if(in_array('5', $perfis_dashboards))
            <div class="form-group col-md-12">
                <div class="card">
                    <div class="card-header border-transparent text-center">
                        <h4 class="">
                            <i class="fas fa-caret-right text-danger"></i> Tarefas
                        </h4>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body p-0 previstos">
                        <div class="table-responsive">
                            <table class="table m-0">

                                <tbody>
                                    @if($tarefas)
                                        @foreach ($tarefas as $tarefa)                                            
                                            <tr id="tarefa_{{$tarefa->id}}">
                                                <td><i class="fas fa-caret-right text-danger"></i> {{' Data da tarefa: ' . \Carbon\Carbon::parse($tarefa->data_hora)->format('d/m/Y')}}</td>
                                                <td title="{{ $tarefa->mensagem }}"> {{' Tarefa: ' . substr($tarefa->mensagem, 0, 50) . '...' }}</td>
                                                <td class="marcar_lido text-center" style="cursor: pointer;" data-id="{{$tarefa->id}}" > {{ 'Marcar como lida ' }} <i class="far fa-check-circle text-success"></i> </td>
                                            </tr>
                                        @endforeach
                                    @endif

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
        @endif
        </div>
    </div>
@stop
