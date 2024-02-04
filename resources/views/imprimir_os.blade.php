@extends('layouts.app')

@section('content')

@foreach ($folhas as $key => $folha)
    <div class="container" style="@if ($key >0 ) {{'margin-top: 620px'}} @else {{''}} @endif">
        <table class="table text-center" style="font-size: 75%;  border-collapse: collapse; border: 2px solid black">
            <tbody>
            <tr>
                <td colspan="3" class="font-weight-bold">Eplax</td>
                <td colspan="6" class="font-weight-bold">{{'Ordem de serviço'}}</td>
                <td colspan="2">RF-11</td>
            </tr>
            <tr>
                <td colspan="2" class="font-weight-bold">Processo</td>
                <td >{{$folha['status']}}</td>
                <td  class="font-weight-bold">Nº O.S</td>
                <td >{{$pedidos[0]->os}}</td>
                <td colspan="2" class="font-weight-bold">Código do produto(EP)</td>
                <td >{{$pedidos[0]->tabelaFichastecnicas->ep}}</td>
                <td class="font-weight-bold">Data </td>
                <td >{{ Carbon\Carbon::createFromDate(date('Y-m-d'))->format('d/m/Y') }}</td>
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
                <td colspan="2" rowspan="5" class="font-weight-bold "><p class='top_texto_qualidade'>Alertas de qualidade</p></td>
                <td colspan="9">{{$folha['alerta1']}}</td>
            </tr>
            <tr>
                <td colspan="9">{{$folha['alerta2']}}</td>
            </tr>
            <tr>
                <td colspan="9">{{$folha['alerta3']}}</td>
            </tr>
            <tr>
                <td colspan="9">{{$folha['alerta4']}}</td>
            </tr>
            <tr>
                <td colspan="9">{{$folha['alerta5']}}</td>
            </tr>
            <tr>
                <td rowspan="6" class="font-weight-bold "><div class="top_texto_itens">Itens</p></td>
            </tr>
            <tr>
                <td>Data</td>
                <td>Data</td>
                <td>Data</td>
                <td>Data</td>
                <td>Data</td>
                <td>Data</td>
                <td>Data</td>
                <td>Data</td>
                <td>Data</td>
                <td>Data</td>
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
