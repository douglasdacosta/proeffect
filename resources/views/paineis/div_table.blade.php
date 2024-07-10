<div class="w-auto"  style="display: contents; overflow: hidden;">
    <h2><b>Concluídos</h2>
    <table style="background-color: #68c570;" class="table table-sm table-striped text-center concluidos" id="table_composicao">
        <thead >
            <tr>
                <th scope="col">EP</th>
                <th scope="col">OS</th>
                <th scope="col">Qtde</th>
                <th scope="col">Blanks</th>
                <th scope="col">Conj.</th>
                <th scope="col">Entrega</th>
                <th scope="col">Alerta</th>
                <th scope="col">Etapa</th>
                @if(isset($usinagem) && $usinagem == true)
                    <th scope="col">Nº máquina</th>
                @endif
                <th scope="col">Motivo Pausa</th>
                <th scope="col">Qtde</th>
                @if(isset($montagem) && $montagem == true)
                    <th scope="col">Responsável</th>
                @endif
                <th scope="col">Colaborador</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pedidosCompletos as $pedido)
                @if(isset($pedido->colaboradores[$pedido->id]))
                    <?php  $contador =0 ?>
                    @foreach ($pedido->colaboradores[$pedido->id] as $colaborador)
                        <tr>
                            @if($contador==0)
                                <td scope="col">{{$pedido->ep}}</td>
                                <td scope="col">{{$pedido->os}}</td>
                                <td scope="col">{{$pedido->qtde}}</td>
                                <td scope="col">{{$pedido->qtde_blank}}</td>
                                <td scope="col">{{$pedido->conjuntos}}</td>
                                <td scope="col">{{ Carbon\Carbon::parse($pedido->data_entrega)->format('d/m/Y') }} </td>
                                <td scope="col"class="{{ $pedido->class_dias_alerta }}">{{ $pedido->dias_alerta }}</td>
                            @else
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            @endif
                            <td scope="col">{{$colaborador['nome_etapa']}}</td>
                            @if(isset($usinagem) && $usinagem == true)
                                <td scope="col">{{$colaborador['numero_maquina']}}</td>
                            @endif
                            <td scope="col">{{$colaborador['select_motivo_pausas']}}</td>
                            <td scope="col">{{$colaborador['texto_quantidade']}}</td>
                            @if(isset($montagem) && $montagem == true)
                                <td scope="col">{{$pedido->funcionario}}</td>
                            @endif
                            <td scope="col">{{$colaborador['nome']}}</td>
                        </tr>
                        <?php $contador++ ?>
                    @endforeach
                @else
                    <tr>
                        <td scope="col">{{$pedido->ep}}</td>
                        <td scope="col">{{$pedido->os}}</td>
                        <td scope="col">{{$pedido->qtde}}</td>
                        <td scope="col">{{$pedido->qtde_blank}}</td>
                        <td scope="col">{{$pedido->conjuntos}}</td>
                        <td scope="col">{{ Carbon\Carbon::parse($pedido->data_entrega)->format('d/m/Y') }} </td>
                        <td scope="col"class="{{ $pedido->class_dias_alerta }}">{{ $pedido->dias_alerta }}</td>
                        <td scope="col"></td>
                        <td scope="col"></td>
                        <td scope="col"></td>
                        @if(isset($usinagem) && $usinagem == true)
                            <td scope="col"></td>
                        @endif
                        @if(isset($montagem) && $montagem == true)
                            <td scope="col"></td>
                        @endif
                        <td scope="col"></td>
                        <td scope="col"></td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</div>
<div class="w-auto"  style=" height: 85%; overflow: hidden; background-color: #e9aa4c;">
    <h2><b>Pendentes</b></h2>
    <table class="table table-sm table-striped text-center" id="table_composicao">
        <thead >
            <tr>
                <th scope="col">EP</th>
                <th scope="col">OS</th>
                <th scope="col">Qtde</th>
                <th scope="col">Blanks</th>
                <th scope="col">Conj.</th>
                <th scope="col">Entrega</th>
                <th scope="col">Alerta</th>
                <th scope="col">Etapa</th>
                @if(isset($usinagem) && $usinagem == true)
                    <th scope="col">Nº máquina</th>
                @endif
                <th scope="col">Motivo Pausa</th>
                <th scope="col">Qtde</th>
                @if(isset($montagem) && $montagem == true)
                    <th scope="col">Responsável</th>
                @endif
                <th scope="col">Colaborador</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pedidosPendentes as $pedido)
                @if(isset($pedido->colaboradores[$pedido->id]))
                    <?php $contador =0 ?>
                    @foreach ($pedido->colaboradores[$pedido->id] as $colaborador)
                        <tr>
                            @if($contador==0)
                                <td scope="col">{{$pedido->ep}}</td>
                                <td scope="col">{{$pedido->os}}</td>
                                <td scope="col">{{$pedido->qtde}}</td>
                                <td scope="col">{{$pedido->qtde_blank}}</td>
                                <td scope="col">{{$pedido->conjuntos}}</td>
                                <td scope="col">{{ Carbon\Carbon::parse($pedido->data_entrega)->format('d/m/Y') }} </td>
                                <td scope="col"class="{{ $pedido->class_dias_alerta }}">{{ $pedido->dias_alerta }}</td>
                            @else
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            @endif
                            <td scope="col">{{$colaborador['nome_etapa']}}</td>
                            @if(isset($usinagem) && $usinagem == true)
                                <td scope="col">{{$colaborador['numero_maquina']}}</td>
                            @endif
                            <td scope="col">{{$colaborador['select_motivo_pausas']}}</td>
                            <td scope="col">{{$colaborador['texto_quantidade']}}</td>
                            @if(isset($montagem) && $montagem == true)
                                <td scope="col">{{$pedido->funcionario}}</td>
                            @endif
                            <td scope="col">{{$colaborador['nome']}}</td>
                        </tr>
                        <?php $contador++ ?>
                    @endforeach
                @else
                    <tr>
                        <td scope="col">{{$pedido->ep}}</td>
                        <td scope="col">{{$pedido->os}}</td>
                        <td scope="col">{{$pedido->qtde}}</td>
                        <td scope="col">{{$pedido->qtde_blank}}</td>
                        <td scope="col">{{$pedido->conjuntos}}</td>
                        <td scope="col">{{ Carbon\Carbon::parse($pedido->data_entrega)->format('d/m/Y') }} </td>
                        <td scope="col"class="{{ $pedido->class_dias_alerta }}">{{ $pedido->dias_alerta }}</td>
                        @if(isset($usinagem) && $usinagem == true)
                            <td scope="col"></td>
                        @endif
                        <td scope="col"></td>
                        <td scope="col"></td>
                        <td scope="col"></td>
                        @if(isset($montagem) && $montagem == true)
                            <td scope="col"></td>
                        @endif
                        <td scope="col"></td>
                    </tr>
                @endif
            @endforeach

        </tbody>
    </table>
</div>
