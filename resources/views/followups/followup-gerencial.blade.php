<?php
use App\Http\Controllers\PedidosController;
use App\Http\Controllers\AjaxOrcamentosController;
use App\Providers\DateHelpers;

?>

<link rel="stylesheet" href="{{ asset('css/followups.css') }}" />
        @section('content_header')
            <div class="form-group row">
                <h1 class="m-0 text-dark col-sm-6 col-form-label">Tela de Followup Gerêncial</h1>
            </div>
        @stop
        @section('content')
            @if (isset($dados_pedido_status))
                @foreach ($dados_pedido_status as $key => $dado_pedido_status)
                    @php
                    $total_mo = $total_mp = $total_soma = 0.00;
                    @endphp
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
                                    <th scope="col">Prioridade</th>
                                    <th scope="col" title="Observações">Obs</th>
                                    <th scope="col">Valor Unit</th>
                                    <th scope="col">Total</th>
                                    <th scope="col">Total MO</th>
                                    <th scope="col">Total MP</th>
                                    <th scope="col">$MP</th>
                                    <th scope="col">%MP</th>
                                    <th scope="col">$MO</th>
                                    <th scope="col">%MO</th>
                                    <th scope="col">$HM</th>
                                    <th scope="col">Transporte</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Data status</th>
                                    <th scope="col">Usinagem</th>
                                    <th scope="col">Acabamento</th>
                                    <th scope="col">Montagem</th>
                                    <th scope="col">Inspeção</th>
                                    <th scope="col">Alerta departamento</th>
                                    <th scope="col" title="Alerta de dias">Alerta</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($dado_pedido_status['classe'] as $pedido)
                                    @php
                                        $entrega = \Carbon\Carbon::createFromDate($pedido->data_entrega)->format('Y-m-d');
                                        $hoje = date('Y-m-d');
                                        $dias_alerta = \Carbon\Carbon::createFromDate($hoje)->diffInDays($entrega, false);

                                        $diasSobrando=0;
                                        $dias_alerta_departamento = 'text-primary';
                                        $status = strtolower($key);
                                        
                                        if(!empty($maquinas[$status])) {
                                            
                                            $retorno = PedidosController::calculaDiasSobrando($maquinas, $status, $pedido);
                                            $dias_alerta_departamento = $retorno['dias_alerta_departamento'];
                                            $diasSobrando = $retorno['diasSobrando'];

                                        }

                                        
                                        
                                        if ($dias_alerta < 6) {
                                            $class_dias_alerta = 'text-danger';
                                        } else {
                                            $class_dias_alerta = 'text-primary';
                                        }
                                        $mp = DateHelpers::formatFloatValue($dado_pedido_status['pedido'][$pedido->id]['totais']['subTotalMP']);
                                        $mp_numerico = $dado_pedido_status['pedido'][$pedido->id]['totais']['subTotalMP'];
                                        $mo = $pedido->valor_unitario_adv - $mp;


                                        list($horas, $minutos, $segundos) = explode(':', $dado_pedido_status['pedido'][$pedido->id]['usinagem']);

                                        $tempoEmDias = ($horas / 24) + ($minutos / 1440) + ($segundos / 86400);
                                        $resultado = (($tempoEmDias / $pedido->qtde) * 24) * 60;
                                        $tempo_usinagem = number_format($resultado, 2, '.', '');

                                        $total = $pedido->valor_unitario_adv * $pedido->qtde;
                                        $total_soma = isset($total_soma) ? $total_soma + $total : $total;
                                        $total_soma_geral = isset($total_soma_geral) ? $total_soma_geral + $total : $total;
                                        $valor_mo = $mo * $pedido->qtde;
                                        $valor_mp = $mp * $pedido->qtde;

                                        $total_mo = isset($total_mo) ? $total_mo + $valor_mo : $valor_mo ;
                                        $total_mp = isset($total_mp) ? $total_mp + $valor_mp : $valor_mp ;

                                        $percentual_mo = ($mo == 0 || $pedido->valor_unitario_adv == 0) ? 0 : ($mo/$pedido->valor_unitario_adv)*100;
                                        $percentual_mp = ($mp == 0 || $pedido->valor_unitario_adv == 0) ? 0 : ($mp/$pedido->valor_unitario_adv)*100;

                                    @endphp

                                    <tr>
                                        <td>{{ $pedido->tabelaPessoas->codigo_cliente }}</td>
                                        <td>{{ $pedido->tabelaPessoas->nome_assistente }}</td>
                                        <td>{{ $pedido->tabelaFichastecnicas->ep }}</td>
                                        <td>{{ $pedido->os }}</td>
                                        <td>{{ $pedido->qtde }}</td>
                                        <td>{{ \Carbon\Carbon::parse($pedido->data_gerado)->format('d/m/Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($pedido->data_entrega)->format('d/m/Y') }}</td>
                                        <td>@if(!empty($pedido->tabelaPrioridades->nome)){{ $pedido->tabelaPrioridades->nome }}@else{{''}}@endif</td>
                                        <td title="{{ $pedido->observacao }}">{!! Str::words($pedido->observacao, 1, '...') !!}</td>
                                        <td>{{ number_format($pedido->valor_unitario_adv, 2, ',', '.')  }}</td> <!--valor_unitário-->
                                        <td style="background-color: #d9edf7">{{ number_format($total, 2, ',', '.')  }}</td> <!--total-->
                                        <td style="background-color: #d9edf7">{{ number_format($valor_mo, 2, ',', '.')  }}</td> <!--total mo-->
                                        <td style="background-color: #d9edf7">{{ number_format($valor_mp, 2, ',', '.')  }}</td> <!--total mp-->
                                        <td style="background-color: #f3f2b6">{{ number_format(($mp), 2, ',', '.')  }}</td> <!--mp-->
                                        <td style="background-color: #f3f2b6">{{ number_format($percentual_mp, 2, ',', '.') }}%</td> <!--%mp-->
                                        <td style="background-color: #b2eeaa">{{ number_format(($mo), 2, ',', '.')  }}</td> <!--mp-->
                                        <td style="background-color: #b2eeaa">{{ number_format($percentual_mo, 2, ',', '.') }}%</td> <!--%MO-->

                                        @php
                                        $valotHM = ($mp ==0 || $tempo_usinagem == 0) ? '0,00' : number_format($pedido->valor_unitario_adv - ((($mp*1.53))/(($tempo_usinagem/60)*1.16)), 2, ',', '.');

                                        if($mp ==0 || $tempo_usinagem == 0){
                                            $valotHM_float = 0.00;
                                        } else {
                                            $valotHM_float = ($pedido->valor_unitario_adv - ($mp*1.53))/(($tempo_usinagem/60)*1.16);
                                        }


                                        @endphp
                                        <td @if($valotHM_float <300 ) style="background-color: #e25050; color: #FFFFFF" @endif >{{  number_format($valotHM_float, 2, ',', '.') }}</td> <!-- HM  unitario - ((MP*1,53))/((Tempo_usinagem/60)*1,16) -->

                                        <td title="{{$pedido->tabelaTransportes->nome}}">{!! Str::words($pedido->tabelaTransportes->nome, 2, '...') !!}</td>
                                        <td>{{ $pedido->tabelaStatus->nome }} </td>
                                        <td>{{ $dado_pedido_status['pedido'][$pedido->id]['data_alteracao_status'] }} </td>
                                        <td>{{ PedidosController::formatarHoraMinuto($dado_pedido_status['pedido'][$pedido->id]['usinagem']) }}
                                        </td>
                                        <td>{{ PedidosController::formatarHoraMinuto($dado_pedido_status['pedido'][$pedido->id]['acabamento']) }}
                                        </td>
                                        <td>{{ PedidosController::formatarHoraMinuto($dado_pedido_status['pedido'][$pedido->id]['montagem']) }}
                                        </td>
                                        <td>{{ PedidosController::formatarHoraMinuto($dado_pedido_status['pedido'][$pedido->id]['inspecao']) }}
                                        </td>
                                        <td class="{{ $dias_alerta_departamento }}">{{ ($diasSobrando) }}</td>
                                        <td class="{{ $class_dias_alerta }}">{{ $dias_alerta }}</td>
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
                                    <th scope="col" style="background-color: #d9edf7">{{number_format($total_soma, 2, ',', '.') }}</th>
                                    <th scope="col" style="background-color: #d9edf7">{{ number_format($total_mo, 2, ',', '.') }}</th>
                                    <th scope="col" style="background-color: #d9edf7">{{number_format($total_mp, 2, ',', '.')}}</th>
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
                                    <th scope="col"></th>
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
                                    @php
                                        if($total_soma == 0 || $total_mo == 0 ){
                                            $totalMO = 0;
                                        } else {
                                            $totalMO = $total_mo/$total_soma;
                                        }

                                        if($total_soma == 0 || $total_mo == 0 ){
                                            $totalMP = 0;
                                        } else {
                                            $totalMP = $total_mp/$total_soma;
                                        }
                                    @endphp
                                    <th scope="col" style="background-color: #d9edf7">{{ number_format(($totalMO) * 100, 2, ',', '.') }}%</th>
                                    <th scope="col" style="background-color: #d9edf7">{{ number_format(($totalMP) * 100, 2, ',', '.') }}%</th>
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
                                    <th scope="col"></th>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <hr class="my-4">
                @endforeach
                <table>
                    <thead style="background-color: {{ $palheta_cores[8] }}">
                        <tr>
                            <th style="padding-left: 65px;"> </th>
                            <th style="padding-left: 65px;"> </th>
                            <th style="padding-left: 65px;"> </th>
                            <th style="padding-left: 65px;"> </th>
                            <th style="padding-left: 65px;"> </th>
                            <th style="padding-left: 65px;"> </th>
                            <th style="padding-left: 65px;"> </th>
                            <th style="padding-left: 65px;"> </th>
                            <th style="padding-left: 65px;"> </th>
                            <th style="padding-left: 65px;"> </th>
                            <th style="padding-left: 35px;"></th>
                            <th style="padding-left: 0px;">Total</th>
                            <th style="padding-left: 65px;"></th>
                            <th style="padding-left: 65px;"></th>
                            <th style="padding-left: 65px;"></th>
                            <th style="padding-left: 65px;"></th>
                            <th style="padding-left: 65px;"></th>
                            <th style="padding-left: 65px;"></th>
                            <th style="padding-left: 65px;"></th>

                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="col">&nbsp;</th>
                            <th scope="col">&nbsp;</th>
                            <th scope="col">&nbsp;</th>
                            <th scope="col">&nbsp;</th>
                            <th scope="col">&nbsp;</th>
                            <th scope="col">&nbsp;</th>
                            <th scope="col">&nbsp;</th>
                            <th scope="col">&nbsp;</th>
                            <th scope="col">&nbsp;</th>
                            <th scope="col">&nbsp;</th>
                            <th scope="col">&nbsp;</th>
                            <th scope="col">{{ number_format($total_soma_geral, 2, ',', '.') }}</th>
                            <th scope="col">&nbsp;</th>
                            <th scope="col">&nbsp;</th>
                        </tr>
                    </tbody>
                </table>
            @endif
        @stop