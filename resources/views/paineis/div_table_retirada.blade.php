<script>
    var ligado = false;
    function toggleBackgroundByClass(className) {

        setInterval(() => {

            let elements = document.querySelectorAll(`.${className}`);
            // Se data_antecipacao e data_retirada são classes, use "."
            let elements_dtr = document.querySelectorAll('.data_antecipacao');
            let elements_dt = document.querySelectorAll('.data_retirada');

            if(!ligado){
                // Esconder data_antecipacao e mostrar data_retirada
                elements.forEach(el => el.style.backgroundColor = "red");

                elements_dtr.forEach(el => el.style.display = 'none');
                elements_dt.forEach(el => el.style.display = 'block');

                ligado = true;
            } else {

                elements.forEach(el => el.style.backgroundColor = "");

                elements_dtr.forEach(el => el.style.display = 'block');
                elements_dt.forEach(el => el.style.display = 'none');

                ligado = false;
            }

    }, 5000); // Alterna a cada 5 segundos
}

toggleBackgroundByClass('retirada');

</script>
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
                <th scope="col">Prioridade</th>
                <th scope="col">Entrega</th>
                <th scope="col">Retirada</th>
                <th scope="col">Alerta Depart</th>
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
            @php 
            // dd($pedido);
            @endphp
                    <tr>
                        <td scope="col">{{$pedido->ep}}</td>
                        <td scope="col">{{$pedido->os}}</td>
                        <td scope="col">{{$pedido->qtde}}</td>
                        <td scope="col">{{$pedido->blanks}}</td>
                        <td scope="col">{{$pedido->conjuntos}}</td>
                        <td scope="col">{{$pedido->prioridade}}</td>
                        <td scope="col">@if($pedido->data_entrega !='') {{Carbon\Carbon::parse($pedido->data_entrega)->format('d/m/Y')}} @else {{''}} @endif</td>
                        <td scope="col">{{ $pedido->hora_antecipacao }}</td>
                        <td scope="col" class="{{ $pedido->dias_alerta_departamento}}">{{ $pedido->diasSobrando }}</td>
                        <td scope="col"class="{{ $pedido->class_dias_alerta }}">{{ $pedido->alerta }}</td>
                        <td scope="col">Finalizado</td>
                        @if(isset($usinagem) && $usinagem == true)
                        <td scope="col">{{$pedido->numero_maquina}}</td>
                        @endif
                        <td scope="col"></td>
                        <td scope="col"></td>
                        @if(isset($montagem) && $montagem == true)
                            <td scope="col"></td>
                        @endif
                        <td scope="col">{{$pedido->colaborador}}</td>
                    </tr>
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
                <th scope="col">Prioridade</th>
                <th scope="col">Entrega</th>
                <th scope="col">Retirada</th>
                <th scope="col">Alerta Depart</th>
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
                    <tr>
                        <td scope="col">{{$pedido->ep}}</td>
                        <td scope="col">{{$pedido->os}}</td>
                        <td scope="col">{{$pedido->qtde}}</td>
                        <td scope="col">{{$pedido->blanks}}</td>
                        <td scope="col">{{$pedido->conjuntos}}</td>
                        <td scope="col">{{$pedido->prioridade}}</td>
                        <td scope="col" @if(!empty($pedido->hora_antecipacao))  class='retirada' @else {{ '' }} @endif >
                            @if($pedido->data_entrega !='')
                                @if(!empty($pedido->hora_antecipacao))
                                    <span class='data_antecipacao'> {{Carbon\Carbon::parse($pedido->data_entrega)->format('d/m/Y')}} </span>
                                    <span class='data_retirada' style="display: none;"> {{Carbon\Carbon::parse($pedido->data_antecipacao)->format('d/m/Y')}} </span>
                                @else
                                    {{Carbon\Carbon::parse($pedido->data_entrega)->format('d/m/Y')}}
                                @endif
                            @else
                                {{''}}
                            @endif</td>

                        <td scope="col" @if(!empty($pedido->hora_antecipacao))  class='retirada' @else {{ '' }} @endif >{{ $pedido->hora_antecipacao }}</td>
                        <td scope="col" class="{{ $pedido->dias_alerta_departamento}}">{{ $pedido->diasSobrando }}</td>
                        <td scope="col" class="{{ $pedido->class_dias_alerta }}">{{ $pedido->alerta }}</td>
                        <td scope="col">{{$pedido->etapa}}</td>
                        @if(isset($usinagem) && $usinagem == true)
                            <td scope="col">{{$pedido->numero_maquina}}</td>
                        @endif
                        <td scope="col">{{$pedido->motivo_pausa}}</td>
                        <td scope="col">{{$pedido->qtde_pausa}}</td>
                        @if(isset($montagem) && $montagem == true)
                            <td scope="col">{{$pedido->responsavel}}</td>
                        @endif
                        <td scope="col">{{$pedido->colaborador}}</td>
                    </tr>
            @endforeach

        </tbody>
    </table>
</div>

</div>
