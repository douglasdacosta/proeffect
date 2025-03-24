<?php
use App\Http\Controllers\PedidosController;

$palheta_cores = [1 => '#ff003d', 2 => '#ee7e4c', 3 => '#8f639f', 4 => '#94c5a5', 5 => '#ead56c', 6 => '#0fbab7', 7 => '#f7c41f', 8 => '#898b75', 9 => '#c1d9d0', 10 => '#da8f72', 11 => '#00caf8', 12 => '#ffe792', 13 => '#9a5071'];
?>
<link rel="stylesheet" href="{{ asset('css/followups.css') }}" />
        @section('content_header')
            <div class="form-group row">
                <h1 class="m-0 text-dark col-sm-6 col-form-label">Tela de Followup realizado</h1>
            </div>
        @stop
        @section('content')
            @if (isset($dados_pedido_status))
            <div class="form-group row overflow-followup" style="overflow-x:auto;  ">
                <table class="table table-sm table-striped text-center" id="table_composicao">
                    <thead>
                        {{-- style="background-color: {{ $palheta_cores[$dado_pedido_status['id_status'][0]] }} --}}
                        <tr>
                            <th scope="col">EP</th>
                            <th scope="col">OS</th>
                            <th scope="col">Qtde</th>
                            <th scope="col" title="Data do pedido">Data</th>
                            <th scope="col" title="Data da entrega">Entrega</th>
                            <th scope="col" style="background-color: {{ $palheta_cores[4] }}">Apontamento</th>
                            <th scope="col" style="background-color: {{ $palheta_cores[4] }}">Usinagem</th>
                            <th scope="col" style="background-color: {{ $palheta_cores[5] }}">Apontamento</th>
                            <th scope="col" style="background-color: {{ $palheta_cores[5] }}">Acabamento</th>
                            <th scope="col" style="background-color: {{ $palheta_cores[6] }}">Apontamento</th>
                            <th scope="col" style="background-color: {{ $palheta_cores[6] }}">Montagem</th>
                            <th scope="col" style="background-color: {{ $palheta_cores[7] }}">Apontamento</th>
                            <th scope="col" style="background-color: {{ $palheta_cores[7] }}">Montagem Torre</th>
                            <th scope="col" style="background-color: {{ $palheta_cores[8] }}">Apontamento</th>
                            <th scope="col" style="background-color: {{ $palheta_cores[8] }}">Inspeção</th>
                            <th scope="col" style="background-color: {{ $palheta_cores[9] }}">Embalar</th>
                            {{-- <th scope="col" style="background-color: {{ $palheta_cores[10] }}">Expedição</th> --}}
                            <th scope="col" style="background-color: {{ $palheta_cores[11] }}">Entregue</th>

                        </tr>
                    </thead>
                    <tbody>
                        @php
                                    $totais_usinagem='00:00:00';
                                    $totais_acabamento='00:00:00';
                                    $totais_montagem='00:00:00';
                                    $totais_montagem_torre='00:00:00';
                                    $totais_inspecao='00:00:00';
                                    $totais_embalagem='00:00:00';
                                    $totais_expedicao='00:00:00';
                                    $totais_entregue='00:00:00';
                                    $totais_producao='00:00:00';
                        @endphp
                         @foreach ($dados_pedido_status as $key => $dado_pedido_status)
                            @foreach ($dado_pedido_status['classe'] as $pedido)
                                <tr>
                                    <td>{{ $pedido->tabelaFichastecnicas->ep }}</td>
                                    <td>{{ $pedido->os }}</td>
                                    <td>{{ $pedido->qtde }}</td>
                                    <td>{{ \Carbon\Carbon::parse($pedido->data_gerado)->format('d/m/Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($pedido->data_entrega)->format('d/m/Y') }}</td>
                                    <td style="background-color: {{ $palheta_cores[4] }}">{{ !empty($pedido->apontamento_usinagem) ? \Carbon\Carbon::parse($pedido->apontamento_usinagem)->format('d/m/Y') : '' }}</td>
                                    <td style="background-color: {{ $palheta_cores[4] }}">{{ !empty($pedido->apontamento_usinagem) ? PedidosController::formatarHoraMinuto($dado_pedido_status['pedido'][$pedido->id]['usinagem']) : '' }}</td>
                                    @php
                                        $totais_usinagem = (new PedidosController)->somarHoras($dado_pedido_status['pedido'][$pedido->id]['usinagem'], $totais_usinagem);
                                    @endphp
                                    <td style="background-color: {{ $palheta_cores[5] }}">{{ !empty($pedido->apontamento_acabamento) ? \Carbon\Carbon::parse($pedido->apontamento_acabamento)->format('d/m/Y') : '' }}</td>
                                    <td style="background-color: {{ $palheta_cores[5] }}">{{ !empty($pedido->apontamento_acabamento) ? PedidosController::formatarHoraMinuto($dado_pedido_status['pedido'][$pedido->id]['acabamento']) : '' }}</td>
                                    @php
                                        $totais_acabamento = (new PedidosController)->somarHoras($dado_pedido_status['pedido'][$pedido->id]['acabamento'], $totais_acabamento);
                                    @endphp
                                    <td style="background-color: {{ $palheta_cores[6] }}">{{ !empty($pedido->apontamento_montagem) ? \Carbon\Carbon::parse($pedido->apontamento_montagem)->format('d/m/Y') : '' }}</td>
                                    <td style="background-color: {{ $palheta_cores[6] }}">{{ !empty($pedido->apontamento_montagem) ? PedidosController::formatarHoraMinuto($dado_pedido_status['pedido'][$pedido->id]['montagem']) : '' }}</td>
                                    @php
                                        $totais_montagem = (new PedidosController)->somarHoras($dado_pedido_status['pedido'][$pedido->id]['montagem'], $totais_montagem);
                                    @endphp
                                    <td style="background-color: {{ $palheta_cores[7] }}">{{ !empty($pedido->apontamento_montagem_torre) ? \Carbon\Carbon::parse($pedido->apontamento_montagem_torre)->format('d/m/Y') : '' }}</td>
                                    <td style="background-color: {{ $palheta_cores[7] }}">{{ !empty($pedido->apontamento_montagem_torre) ? PedidosController::formatarHoraMinuto($dado_pedido_status['pedido'][$pedido->id]['montagem_torre']) : '' }}</td>
                                    @php
                                        $totais_montagem_torre = (new PedidosController)->somarHoras($dado_pedido_status['pedido'][$pedido->id]['montagem_torre'], $totais_montagem_torre);
                                    @endphp
                                    <td style="background-color: {{ $palheta_cores[8] }}">{{ !empty($pedido->apontamento_inspecao) ? \Carbon\Carbon::parse($pedido->apontamento_inspecao)->format('d/m/Y') : '' }}</td>
                                    <td style="background-color: {{ $palheta_cores[8] }}">{{ !empty($pedido->apontamento_inspecao) ? PedidosController::formatarHoraMinuto($dado_pedido_status['pedido'][$pedido->id]['inspecao']) : '' }}</td>
                                    @php
                                        $totais_inspecao = (new PedidosController)->somarHoras($dado_pedido_status['pedido'][$pedido->id]['inspecao'], $totais_inspecao);
                                    @endphp
                                    <td style="background-color: {{ $palheta_cores[9] }}">{{ !empty($pedido->apontamento_embalagem) ? \Carbon\Carbon::parse($pedido->apontamento_embalagem)->format('d/m/Y') : '' }}</td>
                                    {{-- <td style="background-color: {{ $palheta_cores[10] }}">{{ !empty($pedido->apontamento_expedicao) ? \Carbon\Carbon::parse($pedido->apontamento_expedicao)->format('d/m/Y') : '' }}</td> --}}
                                    <td style="background-color: {{ $palheta_cores[11] }}">{{ !empty($pedido->apontamento_entregue) ? \Carbon\Carbon::parse($pedido->apontamento_entregue)->format('d/m/Y') : '' }}</td>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th style="background-color: {{ $palheta_cores[4] }}"></th>
                            <th style="background-color: {{ $palheta_cores[4] }}">{{ PedidosController::formatarHoraMinuto($totais_usinagem) }}</th>
                            <th style="background-color: {{ $palheta_cores[5] }}"></th>
                            <th style="background-color: {{ $palheta_cores[5] }}">{{ PedidosController::formatarHoraMinuto($totais_acabamento) }}</th>
                            <th style="background-color: {{ $palheta_cores[6] }}"></th>
                            <th style="background-color: {{ $palheta_cores[6] }}">{{ PedidosController::formatarHoraMinuto($totais_montagem) }}</th>
                            <th style="background-color: {{ $palheta_cores[7] }}"></th>
                            <th style="background-color: {{ $palheta_cores[7] }}">{{ PedidosController::formatarHoraMinuto($totais_montagem_torre) }}</th>
                            <th style="background-color: {{ $palheta_cores[8] }}"></th>
                            <th style="background-color: {{ $palheta_cores[8] }}">{{ PedidosController::formatarHoraMinuto($totais_inspecao) }}</th>
                            <th style="background-color: {{ $palheta_cores[9] }}"></th>
                            {{-- <th style="background-color: {{ $palheta_cores[10] }}"></th> --}}
                            <th style="background-color: {{ $palheta_cores[11] }}"></th>
                        </tr>
                    </tfoot>

                </table>
                </div>
                    <hr class="my-4">
                </div>
            @endif
        @stop