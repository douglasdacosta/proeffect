@extends('layouts.app')

@section('content')

@foreach ($folhas as $key => $folha)
    <div id="imprimir" class="contenedor">
        <table class="table text-center">
            <tbody>
            <tr>
                <td colspan="3" class="font-weight-bold">Eplax</td>
                <td colspan="6" class="font-weight-bold">{{'Ordem de serviço'}}</td>
                <td colspan="2">RF-11</td>
            </tr>
            <tr>
                <td colspan="2" class="font-weight-bold">Processo</td>
                <td>{{$folha['status']}}</td>
                <td class="font-weight-bold">Nº O.S</td>
                <td>{{$pedidos[0]->os}}</td>
                <td colspan="3" class="font-weight-bold">Código do produto(EP)</td>
                <td>{{$pedidos[0]->tabelaFichastecnicas->ep}}</td>
                <td class="font-weight-bold">Data</td>
                <td >{{ Carbon\Carbon::createFromDate($pedidos[0]->data_gerado)->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td colspan="2" class="font-weight-bold">Quantidade</td>
                <td >{{$pedidos[0]->qtde}}</td>
                <td  colspan="2" class="font-weight-bold">Quantidade blanks</td>
                <td >{{$qtde_blank}}</td>
                <td colspan="2" class="font-weight-bold">Quantidade de conjuntos</td>
                <td >{{$qtde_conjuntos}}</td>
                <td class="font-weight-bold">Data entrega</td>
                <td >{{Carbon\Carbon::createFromDate($pedidos[0]->data_entrega)->format('d/m/Y')}}</td>
            </tr>
            <tr>
                <td colspan="11" class="font-weight-bold ">Materia prima</td>
            </tr>
            <tr>
                <td >Lote</td>
                <td colspan="3">Descricao</td>
                <td >Quantidade</td>
                <td >Lote</td>
                <td colspan="4">Descricao</td>
                <td >Quantidade</td>
            </tr>
            @for ($i = 0; $i < 3; $i++)
                <tr>
                    <td >&nbsp;</td>
                    <td colspan="3"></td>
                    <td ></td>
                    <td ></td>
                    <td colspan="4"></td>
                    <td ></td>
                </tr>
            @endfor
            <tr>
                <td colspan="2" rowspan="5" class="font-weight-bold ">
                    <p class='top_texto_qualidade'>Alertas de qualidade</p>
                </td>
                <td colspan="9">@if ($folha['alerta1']) {{$folha['alerta1']}} @else &nbsp; @endif</td>
            </tr>
            <tr>
                <td colspan="9">@if ($folha['alerta2']) {{$folha['alerta2']}} @else &nbsp; @endif</td>
            </tr>
            <tr>
                <td colspan="9">@if ($folha['alerta3']) {{$folha['alerta3']}} @else &nbsp; @endif</td>
            </tr>
            <tr>
                <td colspan="9">@if ($folha['alerta4']) {{$folha['alerta4']}} @else &nbsp; @endif</td>
            </tr>
            <tr>
                <td colspan="9">@if ($folha['alerta5']) {{$folha['alerta5']}} @else &nbsp; @endif</td>
            </tr>
            <tr>
                <td  rowspan="5" class="font-weight-bold "><p class="top_texto_itens">Itens</p></td>
                <td >Data</td>
                <td >Data</td>
                <td >Data</td>
                <td >Data</td>
                <td >Data</td>
                <td >Data</td>
                <td >Data</td>
                <td >Data</td>
                <td >Data</td>
                <td >Data</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>Operador</td>
                <td>Operador</td>
                <td>Operador</td>
                <td>Operador</td>
                <td>Operador</td>
                <td>Operador</td>
                <td>Operador</td>
                <td>Operador</td>
                <td>Operador</td>
                <td>Operador</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>Processo</td>
                <td>Processo</td>
                <td>Processo</td>
                <td>Processo</td>
                <td>Processo</td>
                <td>Processo</td>
                <td>Processo</td>
                <td>Processo</td>
                <td>Processo</td>
                <td>Processo</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @for ($i = 1; $i < 5; $i++)
            <tr>
                <td>{{$i}}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @endfor
            </tbody>
        </table>
    </div>
    <div class="row mt-3"></div>

    @endforeach
@stop
