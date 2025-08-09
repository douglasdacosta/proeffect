
<link rel="stylesheet" href="{{ asset('css/followups.css') }}" />
        @section('content_header')
            <div class="form-group row">
                <h1 class="m-0 text-dark col-sm-6 col-form-label">Tela de Followup ciclo de produção</h1>
            </div>
        @stop
        @section('content')
            @if (isset($dados_pedido_status))
            <div class="form-group row overflow-followup" style="overflow-x:auto;  ">
                <table class="table table-sm table-striped text-center" id="table_composicao">
                    <thead>
                        <tr>
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col" title="Data do pedido"></th>
                            <th scope="col" title="Data da entrega"></th>
                            <th scope="col" title="Data da entrega"></th>
                            <th scope="col" colspan="2" style="background-color: {{ $palheta_cores[4] }}">Usinagem</th>

                            <th scope="col" colspan="2" style="background-color: {{ $palheta_cores[5] }}">Acabamento</th>

                            <th scope="col" colspan="2" style="background-color: {{ $palheta_cores[6] }}">Montagem</th>

                            <th scope="col" colspan="2" style="background-color: {{ $palheta_cores[8] }}">Inspeção</th>

                            <th scope="col" colspan="2" style="background-color: {{ $palheta_cores[9] }}">Embalar</th>

                            {{-- <th scope="col" colspan="2" style="background-color: {{ $palheta_cores[10] }}">Expedição</th> --}}

                            <th scope="col" colspan="2" style="background-color: {{ $palheta_cores[11] }}">Entregue</th>

                            <th scope="col"></th>

                        </tr>
                        {{-- style="background-color: {{ $palheta_cores[$dado_pedido_status['id_status'][0]] }} --}}
                        <tr>
                            <th scope="col" style="min-width: 100px">EP</th>
                            <th scope="col" style="min-width: 100px">OS</th>
                            <th scope="col" style="min-width: 100px">Qtde</th>
                            <th scope="col" style="min-width: 100px">Prioridade</th>
                            <th scope="col" style="min-width: 100px" title="Data do pedido">Data</th>
                            <th scope="col" style="min-width: 100px" title="Data da entrega">Entrega</th>
                            <th scope="col" style="min-width: 100px" title="Data da entrega">Data de contagem</th>
                            <th scope="col" style="min-width: 100px; background-color: {{ $palheta_cores[4] }}">Apontamento</th>
                            <th scope="col" style="min-width: 100px; background-color: {{ $palheta_cores[4] }}">Dias Parados</th>
                            <th scope="col" style="min-width: 100px; background-color: {{ $palheta_cores[5] }}">Apontamento</th>
                            <th scope="col" style="min-width: 100px; background-color: {{ $palheta_cores[5] }}">Dias Parados</th>
                            <th scope="col" style="min-width: 100px; background-color: {{ $palheta_cores[6] }}">Apontamento</th>
                            <th scope="col" style="min-width: 100px; background-color: {{ $palheta_cores[6] }}">Dias Parados</th>
                            <th scope="col" style="min-width: 100px; background-color: {{ $palheta_cores[8] }}">Apontamento</th>
                            <th scope="col" style="min-width: 100px; background-color: {{ $palheta_cores[8] }}">Dias Parados</th>
                            <th scope="col" style="min-width: 100px; background-color: {{ $palheta_cores[9] }}">Apontamento</th>
                            <th scope="col" style="min-width: 100px; background-color: {{ $palheta_cores[9] }}">Dias Parados</th>
                            {{-- <th scope="col" style="min-width: 100px; background-color: {{ $palheta_cores[10] }}">Apontamento</th> --}}
                            {{-- <th scope="col" style="min-width: 100px; background-color: {{ $palheta_cores[10] }}">Dias Parados</th> --}}
                            <th scope="col" style="min-width: 100px; background-color: {{ $palheta_cores[11] }}">Apontamento</th>
                            <th scope="col" style="min-width: 100px; background-color: {{ $palheta_cores[11] }}">Dias Parados</th>
                            <th scope="col" title="Alerta de dias">Tempo da produção</th>
                            <th scope="col" title="Alerta de dias">Tempo de entrega</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php
                                    $contador=0;
                                    $dias_totais_usinagem=0;
                                    $dias_totais_acabamento=0;
                                    $dias_totais_montagem=0;
                                    $dias_totais_inspecao=0;
                                    $dias_totais_embalagem=0;
                                    $dias_totais_expedicao=0;
                                    $dias_totais_entregue=0;
                                    $dias_totais_producao=0;
                                ?>
                         @foreach ($dados_pedido_status as $key => $dado_pedido_status)
                            @foreach ($dado_pedido_status['classe'] as $pedido)
                                @php
                                    $contador++;
                                    $tempo_producao=0;

                                    $entrega = \Carbon\Carbon::createFromDate($pedido->data_entrega)->format('Y-m-d');
                                    $hoje = date('Y-m-d');

                                @endphp
                                <tr>
                                    <td>{{ $pedido->tabelaFichastecnicas->ep }}</td>
                                    <td>{{ $pedido->os }}</td>
                                    <td>{{ $pedido->qtde }}</td>
                                    <td>@if(!empty($pedido->tabelaPrioridades->nome)){{ $pedido->tabelaPrioridades->nome }}@else{{''}}@endif</td>
                                    <td>{{ \Carbon\Carbon::parse($pedido->data_gerado)->format('d/m/Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($pedido->data_entrega)->format('d/m/Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($dado_pedido_status['pedido'][$pedido->id]['data_prazo'])->format('d/m/Y') }}</td>
                                    <td style="background-color: {{ $palheta_cores[4] }}">{{ !empty($pedido->apontamento_usinagem) ? \Carbon\Carbon::parse($pedido->apontamento_usinagem)->format('d/m/Y') : '' }}</td>
                                    <td style="background-color: {{ $palheta_cores[4] }}">{{ !empty($pedido->apontamento_usinagem) ? \Carbon\Carbon::parse($pedido->apontamento_usinagem)->startOfDay()->diffInDays(\Carbon\Carbon::parse($dado_pedido_status['pedido'][$pedido->id]['data_prazo'])->startOfDay()) : ''  }}</td>
                                    @php
                                        $soma = !empty($pedido->apontamento_usinagem) ? \Carbon\Carbon::parse($pedido->apontamento_usinagem)->startOfDay()->diffInDays(\Carbon\Carbon::parse(\Carbon\Carbon::parse($dado_pedido_status['pedido'][$pedido->id]['data_prazo'])->startOfDay())->startOfDay()) : 0;
                                        $dias_totais_usinagem += $soma;
                                        $tempo_producao += $soma;
                                        if(!empty($pedido->apontamento_usinagem)) {
                                            $proxima_data=$pedido->apontamento_usinagem;
                                        } else {
                                            $proxima_data=$dado_pedido_status['pedido'][$pedido->id]['data_prazo'];
                                        }

                                     @endphp
                                    <td style="background-color: {{ $palheta_cores[5] }}">{{  !empty($pedido->apontamento_acabamento) ? \Carbon\Carbon::parse($pedido->apontamento_acabamento)->format('d/m/Y') : '' }}</td>
                                    <td style="background-color: {{ $palheta_cores[5] }}">{{ !empty($pedido->apontamento_acabamento) ? \Carbon\Carbon::parse($pedido->apontamento_acabamento)->startOfDay()->diffInDays(\Carbon\Carbon::parse($proxima_data)->startOfDay()) : ''  }}</td>
                                    @php
                                        $soma = !empty($pedido->apontamento_acabamento) ? \Carbon\Carbon::parse($pedido->apontamento_acabamento)->startOfDay()->diffInDays(\Carbon\Carbon::parse($proxima_data)->startOfDay()) : 0;
                                        $dias_totais_acabamento += $soma;
                                        $tempo_producao += $soma;
                                        if(!empty($pedido->apontamento_acabamento)) {
                                            $proxima_data =$pedido->apontamento_acabamento;
                                        }

                                     @endphp
                                    <td style="background-color: {{ $palheta_cores[6] }}">{{ !empty($pedido->apontamento_montagem) ? \Carbon\Carbon::parse($pedido->apontamento_montagem)->format('d/m/Y') : '' }}</td>
                                    <td style="background-color: {{ $palheta_cores[6] }}">{{ !empty($pedido->apontamento_montagem) ? \Carbon\Carbon::parse($pedido->apontamento_montagem)->startOfDay()->diffInDays(\Carbon\Carbon::parse($proxima_data)->startOfDay()) : ''  }}</td>
                                    @php
                                        $soma = !empty($pedido->apontamento_montagem) ? \Carbon\Carbon::parse($pedido->apontamento_montagem)->startOfDay()->diffInDays(\Carbon\Carbon::parse($proxima_data)->startOfDay()) : 0;
                                        $dias_totais_montagem += $soma;
                                        $tempo_producao += $soma;
                                        if(!empty($pedido->apontamento_montagem)) {
                                            $proxima_data =$pedido->apontamento_montagem;
                                        }

                                     @endphp
                                    <td style="background-color: {{ $palheta_cores[8] }}">{{ !empty($pedido->apontamento_inspecao) ? \Carbon\Carbon::parse($pedido->apontamento_inspecao)->format('d/m/Y') : '' }}</td>
                                    <td style="background-color: {{ $palheta_cores[8] }}">{{ !empty($pedido->apontamento_inspecao) ? \Carbon\Carbon::parse($pedido->apontamento_inspecao)->startOfDay()->diffInDays(\Carbon\Carbon::parse($proxima_data)->startOfDay()) : ''  }}</td>
                                    @php
                                        $soma = !empty($pedido->apontamento_inspecao) ? \Carbon\Carbon::parse($pedido->apontamento_inspecao)->startOfDay()->diffInDays(\Carbon\Carbon::parse($proxima_data)->startOfDay()) : 0;
                                        $dias_totais_inspecao += $soma;
                                        $tempo_producao += $soma;
                                        if(!empty($pedido->apontamento_inspecao)) {
                                            $proxima_data =$pedido->apontamento_inspecao;
                                        }

                                     @endphp

                                    <td style="background-color: {{ $palheta_cores[9] }}">{{ !empty($pedido->apontamento_embalagem) ? \Carbon\Carbon::parse($pedido->apontamento_embalagem)->format('d/m/Y') : '' }}</td>
                                    <td style="background-color: {{ $palheta_cores[9] }}">{{ !empty($pedido->apontamento_embalagem) ? \Carbon\Carbon::parse($pedido->apontamento_embalagem)->startOfDay()->diffInDays(\Carbon\Carbon::parse($proxima_data)->startOfDay()) : ''  }}</td>
                                    @php
                                        $soma = !empty($pedido->apontamento_embalagem) ? \Carbon\Carbon::parse($pedido->apontamento_embalagem)->startOfDay()->diffInDays(\Carbon\Carbon::parse($proxima_data)->startOfDay()) : 0;
                                        $dias_totais_embalagem += $soma;
                                        $tempo_producao += $soma;
                                        if(!empty($pedido->apontamento_embalagem)) {
                                            $proxima_data =$pedido->apontamento_embalagem;
                                        }

                                     @endphp
                                    {{-- <td style="background-color: {{ $palheta_cores[10] }}">{{ !empty($pedido->apontamento_expedicao) ? \Carbon\Carbon::parse($pedido->apontamento_expedicao)->format('d/m/Y') : '' }}</td> --}}
                                    {{-- <td style="background-color: {{ $palheta_cores[10] }}">{{ !empty($pedido->apontamento_expedicao) ? \Carbon\Carbon::parse($pedido->apontamento_expedicao)->startOfDay()->diffInDays(\Carbon\Carbon::parse($proxima_data)->startOfDay()) : ''  }}</td> --}}
                                    {{-- @php
                                        $soma = !empty($pedido->apontamento_expedicao) ? \Carbon\Carbon::parse($pedido->apontamento_expedicao)->startOfDay()->diffInDays(\Carbon\Carbon::parse($proxima_data)->startOfDay()) : 0;
                                        $dias_totais_expedicao += $soma;
                                        $tempo_producao += $soma;
                                        if(!empty($pedido->apontamento_expedicao)) {
                                            $proxima_data =$pedido->apontamento_expedicao;
                                        }

                                     @endphp --}}
                                    <td style="background-color: {{ $palheta_cores[11] }}">{{ !empty($pedido->apontamento_entregue) ? \Carbon\Carbon::parse($pedido->apontamento_entregue)->format('d/m/Y') : '' }}</td>
                                    <td style="background-color: {{ $palheta_cores[11] }}">{{ !empty($pedido->apontamento_entregue) ? \Carbon\Carbon::parse($pedido->apontamento_entregue)->startOfDay()->diffInDays(\Carbon\Carbon::parse($proxima_data)->startOfDay()) : ''  }}</td>
                                    @php
                                        $soma = !empty($pedido->apontamento_entregue) ? \Carbon\Carbon::parse($pedido->apontamento_entregue)->startOfDay()->diffInDays(\Carbon\Carbon::parse($proxima_data)->startOfDay()) : 0;
                                        $dias_totais_entregue += $soma;
                                        $tempo_producao += $soma;
                                        if(!empty($pedido->apontamento_entregue)) {
                                            $proxima_data =$pedido->apontamento_entregue;
                                        }

                                        $tempo_entrega = \Carbon\Carbon::parse($pedido->apontamento_entregue)
                                                            ->startOfDay()
                                                            ->diffInDays(\Carbon\Carbon::parse($pedido->data_entrega)->startOfDay(), false);
                                        if ($tempo_entrega < 0) {
                                            $class_dias_alerta = 'text-danger';
                                        } else {
                                            $class_dias_alerta = 'text-primary';
                                        }

                                     @endphp
                                    <td >{{ $tempo_producao }}</td>
                                    <td class="{{$class_dias_alerta}}"> {{ $tempo_entrega }}</td>
                                    @php $dias_totais_producao += $tempo_producao; @endphp
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
                            <th></th>
                            <th></th>
                            <th style="background-color: {{ $palheta_cores[4] }}"></th>
                            <th style="background-color: {{ $palheta_cores[4] }}">{{ number_format($dias_totais_usinagem/$contador, 2, '.','.') }}</th>
                            <th style="background-color: {{ $palheta_cores[5] }}"></th>
                            <th style="background-color: {{ $palheta_cores[5] }}">{{ number_format($dias_totais_acabamento/$contador, 2, '.','.') }}</th>
                            <th style="background-color: {{ $palheta_cores[6] }}"></th>
                            <th style="background-color: {{ $palheta_cores[6] }}">{{ number_format($dias_totais_montagem/$contador, 2, '.','.') }}</th>
                            <th style="background-color: {{ $palheta_cores[8] }}"></th>
                            <th style="background-color: {{ $palheta_cores[8] }}">{{ number_format($dias_totais_inspecao/$contador, 2, '.','.') }}</th>
                            <th style="background-color: {{ $palheta_cores[9] }}"></th>
                            <th style="background-color: {{ $palheta_cores[9] }}">{{ number_format($dias_totais_embalagem/$contador, 2, '.','.') }}</th>
                            {{-- <th style="background-color: {{ $palheta_cores[10] }}"></th> --}}
                            {{-- <th style="background-color: {{ $palheta_cores[10] }}">{{ number_format($dias_totais_expedicao/$contador, 2, '.','.') }}</th> --}}
                            <th style="background-color: {{ $palheta_cores[11] }}"></th>
                            <th style="background-color: {{ $palheta_cores[11] }}">{{ number_format($dias_totais_entregue/$contador, 2, '.','.') }}</th>
                            <th>{{ number_format($dias_totais_producao/$contador, 2, '.','.') }}</th>
                            <th>{{ number_format( \Carbon\Carbon::parse($pedido->data_entrega)->startOfDay()->diffInDays(\Carbon\Carbon::parse($pedido->apontamento_entregue)->startOfDay()) /$contador , 2, '.','.') }}</th>
                        </tr>
                    </tfoot>

                </table>
                </div>
                    <hr class="my-4">

            @endif
            </div>

        @stop
