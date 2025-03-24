<?php
    use App\Http\Controllers\PedidosController;
?>
<link rel="stylesheet" href="{{ asset('css/followups.css') }}" />
        @section('content_header')
            <div class="form-group row">
                <h1 class="m-0 text-dark col-sm-6 col-form-label">Tela de Followup Geral</h1>
            </div>
        @stop
        @section('content')
            @if (isset($dados_pedido_status))
                @foreach ($dados_pedido_status as $key => $dado_pedido_status)
                    <label for="codigo" class="col-sm-10 col-form-label">Status do Pedido: {{ Str::upper($key) }} </label>
                    <div class="form-group row overflow-followup" style="overflow-x:auto;  ">
                        <table class="table table-sm table-striped text-center" id="table_composicao">
                            <thead style="background-color: {{ $palheta_cores[$dado_pedido_status['id_status'][0]] }}">
                                <tr>
                                    <th scope="col" title="Código do cliente">Cliente</th>
                                    <th scope="col">Assistente</th>
                                    <th scope="col">EP</th>
                                    <th scope="col">OS</th>
                                    <th scope="col">Qtde</th>
                                    <th scope="col" title="Data do pedido">Data</th>
                                    <th scope="col" title="Data da entrega">Entrega</th>
                                    <th scope="col" title="Alerta de dias">Alerta</th>
                                    <th scope="col">Prioridade</th>
                                    <th scope="col" title="Observações">Obs</th>
                                    <th scope="col">Transporte</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Data Status</th>
                                    <th scope="col">Usinagem</th>
                                    <th scope="col">Acabamento</th>
                                    <th scope="col">Montagem</th>
                                    <th scope="col">Inspeção</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($dado_pedido_status['classe'] as $pedido)
                                    <?php
                                    $entrega = \Carbon\Carbon::createFromDate($pedido->data_entrega)->format('Y-m-d');
                                    $hoje = date('Y-m-d');
                                    $dias_alerta = \Carbon\Carbon::createFromDate($hoje)->diffInDays($entrega, false);
                                    if ($dias_alerta < 6) {
                                        $class_dias_alerta = 'text-danger';
                                    } else {
                                        $class_dias_alerta = 'text-primary';
                                    }
                                    ?>
                                    <tr>
                                        <td>{{ $pedido->tabelaPessoas->codigo_cliente }}</td>
                                        <td>{{ $pedido->tabelaPessoas->nome_assistente }}</td>
                                        <td>{{ $pedido->tabelaFichastecnicas->ep }}</td>
                                        <td>{{ $pedido->os }}</td>
                                        <td>{{ $pedido->qtde }}</td>
                                        <td>{{ \Carbon\Carbon::parse($pedido->data_gerado)->format('d/m/Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($pedido->data_entrega)->format('d/m/Y') }}</td>
                                        <td class="{{ $class_dias_alerta }}">{{ $dias_alerta }}</td>
                                        <td>@if(!empty($pedido->tabelaPrioridades->nome)){{ $pedido->tabelaPrioridades->nome }}@else{{''}}@endif</td>
                                        <td title="{{ $pedido->observacao }}">{!! Str::words($pedido->observacao, 1, '...') !!}</td>
                                        <td title="{{$pedido->tabelaTransportes->nome}}">{!! Str::words($pedido->tabelaTransportes->nome, 2, '...') !!}</td>
                                        <td>{{ $pedido->tabelaStatus->nome }}</td>
                                        <td>{{ $dado_pedido_status['pedido'][$pedido->id]['data_alteracao_status'] }}</td>
                                        <td>{{ PedidosController::formatarHoraMinuto($dado_pedido_status['pedido'][$pedido->id]['usinagem']) }}
                                        </td>
                                        <td>{{ PedidosController::formatarHoraMinuto($dado_pedido_status['pedido'][$pedido->id]['acabamento']) }}
                                        </td>
                                        <td>{{ PedidosController::formatarHoraMinuto($dado_pedido_status['pedido'][$pedido->id]['montagem']) }}
                                        </td>
                                        <td>{{ PedidosController::formatarHoraMinuto($dado_pedido_status['pedido'][$pedido->id]['inspecao']) }}
                                        </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
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
                                        {{ PedidosController::formatarHoraMinuto($dado_pedido_status['totais']['total_tempo_montagem']) . ' horas' }}
                                    </th>
                                    <th scope="col">
                                        {{ PedidosController::formatarHoraMinuto($dado_pedido_status['totais']['total_tempo_inspecao']) . ' horas' }}
                                    </th>
                                </tr>
                                <tr>
                                    <th scope="col" colspan="2"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col">{!! Str::words($dado_pedido_status['maquinas_usinagens'], 2, '') !!}</th>
                                    <th scope="col">{!! Str::words($dado_pedido_status['pessoas_acabamento'], 2, '') !!}</th>
                                    <th scope="col">{!! Str::words($dado_pedido_status['pessoas_montagem'], 2, '') !!}</th>
                                    <th scope="col">{!! Str::words($dado_pedido_status['pessoas_inspecao'], 2, '') !!}</th>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <hr class="my-4">
                @endforeach
            @endif
        @stop