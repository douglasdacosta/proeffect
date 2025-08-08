<?php
    use App\Http\Controllers\PedidosController;
    use App\Providers\DateHelpers;
?>
<link rel="stylesheet" href="{{ asset('css/followups.css') }}" />
        @section('content_header')
            <div class="form-group row">
                <h1 class="m-0 text-dark col-sm-6 col-form-label">Tela de Followup Tempos</h1>
            </div>
        @stop
        @section('content')
            @if (isset($dados_pedido_status))
                @foreach ($dados_pedido_status as $key => $dado_pedido_status)
                    <label for="codigo" class="col-sm-10 col-form-label">Status do Pedido: {{ Str::upper($key) }} </label>
                    <div class="form-group row overflow-followup">
                        <table class="table table-sm table-striped text-center" id="table_composicao">
                            <thead>
                                <tr style="background-color: {{ $palheta_cores[$dado_pedido_status['id_status'][0]] }}">
                                    <th scope="col">OS</th>
                                    <th scope="col" style="width: 100px">EP</th>
                                    <th scope="col">Qtde</th>
                                    <th scope="col" style="width: 100px">Obs</th>
                                    <th scope="col">Prioridade</th>
                                    <th scope="col">Data status</th>
                                    <th scope="col">Usinagem</th>
                                    <th scope="col">Acabamento</th>
                                    <th scope="col">Montagem Torre</th>
                                    <th scope="col">Montagem</th>
                                    <th scope="col">Inspeção</th>
                                    <th scope="col">Data entrega</th>
                                    <th scope="col">Alerta departamento</th>
                                    <th scope="col">Alerta de dias</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($dado_pedido_status['classe'] as $pedido)
                                    <?php
                                        $hoje = date('Y-m-d');
                                        $diasSobrando=  0;
                                        $dias_alerta_departamento = 'text-primary';
                                        $status = strtolower($key);

                                        $entrega = \Carbon\Carbon::createFromDate($pedido->data_entrega)->format('Y-m-d');

                                        $dias_alerta = \Carbon\Carbon::createFromDate($hoje)->diffInDays($entrega, false);
                                        if ($dias_alerta < 6) {
                                            $class_dias_alerta = 'text-danger';
                                        } else {
                                            $class_dias_alerta = 'text-primary';
                                        }
                                        $dias_alerta_departamento = 'text-primary';
                                        $diasSobrando = 0;

                                        if($status == 'imprimir' || $status == 'em preparação' || $status == 'aguardando material') {
                                            $status = 'usinagem';
                                        }

                                        if(!empty($maquinas[$status])) {

                                            $retorno = PedidosController::calculaDiasSobrando($maquinas, $status, $pedido);
                                            $dias_alerta_departamento = $retorno['dias_alerta_departamento'];
                                            $diasSobrando = $retorno['diasSobrando'];

                                        }


                                    ?>
                                    <tr @if($pedido->faturado == 1) style="background-color: #3fe964" @endif>
                                        <td>{{ $pedido->os }}
                                        <td>{{ $pedido->tabelaFichastecnicas->ep }}</td>
                                        <td>{{ $pedido->qtde }}</td>
                                        <td title="{{ $pedido->observacao }}">{!! Str::words($pedido->observacao, 2, '...') !!}</td>
                                        <td>@if(!empty($pedido->tabelaPrioridades->nome)){{ $pedido->tabelaPrioridades->nome }}@else{{''}}@endif</td>
                                        <td>{{ $dado_pedido_status['pedido'][$pedido->id]['data_alteracao_status'] }}</td>
                                        <td>{{ PedidosController::formatarHoraMinuto($dado_pedido_status['pedido'][$pedido->id]['usinagem']) }}
                                        </td>
                                        <td>{{ PedidosController::formatarHoraMinuto($dado_pedido_status['pedido'][$pedido->id]['acabamento']) }}
                                        </td>
                                        <td>{{ PedidosController::formatarHoraMinuto($dado_pedido_status['pedido'][$pedido->id]['montagem_torre']) }}
                                        </td>
                                        <td>{{ PedidosController::formatarHoraMinuto($dado_pedido_status['pedido'][$pedido->id]['montagem']) }}
                                        </td>
                                        <td>{{ PedidosController::formatarHoraMinuto($dado_pedido_status['pedido'][$pedido->id]['inspecao']) }}
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($pedido->data_entrega)->format('d/m/Y') }}</td>
                                        <td class="{{ $dias_alerta_departamento }}">{{ ($diasSobrando) }}</td>
                                        <td class="{{ $class_dias_alerta }}">{{ abs($dias_alerta) }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col">
                                        {{ PedidosController::formatarHoraMinuto($dado_pedido_status['totais']['total_tempo_usinagem']) . ' horas' }}
                                    </th>
                                    <th scope="col">
                                        {{ PedidosController::formatarHoraMinuto($dado_pedido_status['totais']['total_tempo_acabamento']) . ' horas' }}
                                    </th>
                                    <th scope="col">
                                        {{ PedidosController::formatarHoraMinuto($dado_pedido_status['totais']['total_tempo_montagem_torre']) . ' horas' }}
                                    </th>
                                    <th scope="col">
                                        {{ PedidosController::formatarHoraMinuto($dado_pedido_status['totais']['total_tempo_montagem']) . ' horas' }}
                                    </th>
                                    <th scope="col">
                                        {{ PedidosController::formatarHoraMinuto($dado_pedido_status['totais']['total_tempo_inspecao']) . ' horas' }}
                                    </th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                </tr>
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col">{{ $dado_pedido_status['maquinas_usinagens'] }}</th>
                                    <th scope="col">{{ $dado_pedido_status['pessoas_acabamento'] }}</th>
                                    <th scope="col">{{ $dado_pedido_status['pessoas_montagem_torre'] }}</th>
                                    <th scope="col">{{ $dado_pedido_status['pessoas_montagem'] }}</th>
                                    <th scope="col">{{ $dado_pedido_status['pessoas_inspecao'] }}</th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                </tr>
                            </tbody>
                        </table>
                    </div>


                    <hr class="my-4">
                @endforeach
                <div class="form-group row">
                    <table class="table table-sm table-striped text-center" id="table_composicao">
                        <thead>
                            <tr style="background-color: #463a2a; color: #FFFFFF">
                                <th scope="col"></th>
                                <th scope="col"></th>
                                <th scope="col"></th>
                                <th scope="col"></th>
                                <th scope="col">Usinagem</th>
                                <th scope="col">Acabamento</th>
                                <th scope="col">Montagem Torre</th>
                                <th scope="col">Montagem</th>
                                <th scope="col">Inspeção</th>
                                <th scope="col"></th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="">
                                <th scope="col" colspan="2">Total geral</th>
                                <th scope="col">&nbsp;</th>
                                <th scope="col">&nbsp;</th>
                                <th scope="col">
                                    {{ !empty($totalGeral['totalGeralusinagens']) ? $totalGeral['totalGeralusinagens'] . ' dias' : '0' }}
                                </th>
                                <th scope="col">
                                    {{ !empty($totalGeral['totalGeralacabamento']) ? $totalGeral['totalGeralacabamento'] . ' dias' : '0 dias' }}
                                </th>
                                <th scope="col">
                                    {{ !empty($totalGeral['totalGeralmontagem_torre']) ? $totalGeral['totalGeralmontagem_torre'] . ' dias' : '0 dias' }}
                                </th>
                                <th scope="col">
                                    {{ !empty($totalGeral['totalGeralmontagem']) ? $totalGeral['totalGeralmontagem'] . ' dias' : '0 dias' }}
                                </th>
                                <th scope="col">
                                    {{ !empty($totalGeral['totalGeralinspecao']) ? $totalGeral['totalGeralinspecao'] . ' dias' : '0 dias' }}
                                </th>
                                <th scope="col"></th>
                                <th scope="col"></th>
                                <th scope="col"></th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @endif
        @stop
