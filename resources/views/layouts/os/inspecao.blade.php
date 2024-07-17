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
            <td colspan="1" class="font-weight-bold">Quantidade</td>
            <td >{{$pedidos[0]->qtde}}</td>
            <td  colspan="2" class="font-weight-bold">Quantidade blanks</td>
            <td >{{$qtde_blank}}</td>
            <td colspan="2" class="font-weight-bold">Quantidade de conjuntos</td>
            <td >{{$qtde_conjuntos}}</td>
            <td class="font-weight-bold">Data entrega</td>
            <td >{{Carbon\Carbon::createFromDate($pedidos[0]->data_entrega)->format('d/m/Y')}}</td>
        </tr>
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
            <td  colspan="4" rowspan="2" class="font-weight-bold "><p style="margin-top: 25px;">Processo Operacionais</p></td>
            <td colspan="7">Inspeção liberação Produto</td>
        </tr>
        <tr>
            <td>Conforme?</td>
            <td>Não conforme?</td>
            <td colspan="2">Observação</td>
            <td>Data</td>
            <td colspan="2">Nome do responsável</td>
        </tr>

        <tr>
            <td>01</td>
            <td colspan="3"  style="padding-top: 25px;">Foi efetuada a verificação dos Alertas de Qualidade?</td>
            <td></td>
            <td></td>
            <td colspan="2"></td>
            <td></td>
            <td colspan="2"></td>
        </tr>
        <tr>
            <td>02</td>
            <td colspan="3"  style="padding-top: 25px;">Inspecionar lotes de 5 peças e por etapas.</td>
            <td></td>
            <td></td>
            <td colspan="2"></td>
            <td></td>
            <td colspan="2"></td>
        </tr>
        <tr>
            <td>03</td>
            <td colspan="3"  style="padding-top: 25px;">Iniciar montagem e ajustes dos Conjuntos</td>
            <td></td>
            <td></td>
            <td colspan="2"></td>
            <td></td>
            <td colspan="2"></td>
        </tr>
        <tr>
            <td>04</td>
            <td colspan="3" style="padding-top: 25px;">Quantidade Correta?</td>
            <td></td>
            <td></td>
            <td colspan="2"></td>
            <td></td>
            <td colspan="2"></td>
        </tr>
        </tbody>
    </table>
</div>
<div class="row mt-4"></div>
<div class="row mt-4"></div>
<div class="row mt-4"></div>
<div class="row mt-4"></div>
<div class="row mt-4"></div>
<div class="row mt-4"></div>
<div class="row mt-4"></div>
