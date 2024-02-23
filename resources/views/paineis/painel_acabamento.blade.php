@extends('layouts.app')

<style type="text/css">
    .container_default {
        width: 100%;
        height: 100%;
        padding: 10px;
    }

    .container_default .table {
        font-size: 2em;
    }
</style>
<script type="text/javascript" > 

setTimeout(function () {
    location.reload();
    }, 3000);
</script>
@section('content')


    <div class="container_default" style="background-color: #ffffff;">   
        <div class="w-auto text-center">
            <h1>Painel de Acabamento</h1>
        </div>
            
            <div class="w-auto"  style="height: 34%; overflow: hidden; background-color: #68c570;">
                <h1><b>Concluídos</h1> 
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
                        </tr>
                    </thead>
                    <tbody> 
                        
                        @foreach ($pedidosCompletos as $pedido)                        
                        <tr>
                            <td scope="col">{{$pedido->ep}}</td>
                            <td scope="col">{{$pedido->os}}</td>
                            <td scope="col">{{$pedido->qtde}}</td>
                            <td scope="col">{{$pedido->qtde_blank}}</td>
                            <td scope="col">{{$pedido->conjuntos}}</td>
                            <td scope="col">{{ Carbon\Carbon::parse($pedido->data_entrega)->format('d/m/Y') }} </td>
                            <td scope="col"class="{{ $pedido->class_dias_alerta }}">{{ $pedido->dias_alerta }}</td>
                            <td scope="col">{{'iniciado'}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>


            </div>
            <div class="w-auto"  style="height: 60%; overflow: hidden; background-color: #e9aa4c;">
                <h1><b>Pendentes</b></h1> 
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
                        </tr>
                    </thead>
                    <tbody>                        
                        @foreach ($pedidosPendentes as $pedido)                        
                        <tr>
                            <td scope="col">{{$pedido->ep}}</td>
                            <td scope="col">{{$pedido->os}}</td>
                            <td scope="col">{{$pedido->qtde}}</td>
                            <td scope="col">{{$pedido->qtde_blank}}</td>
                            <td scope="col">{{$pedido->conjuntos}}</td>
                            <td scope="col">{{ Carbon\Carbon::parse($pedido->data_entrega)->format('d/m/Y') }} </td>
                            <td scope="col"class="{{ $pedido->class_dias_alerta }}">{{ $pedido->dias_alerta }}</td>
                            <td scope="col">{{'iniciado'}}</td>
                        </tr>
                        @endforeach
                        
                    </tbody>
                </table>
            </div>
    </div>

@stop
