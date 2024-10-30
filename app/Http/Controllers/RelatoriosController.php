<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\PedidosController;
use App\Models\HistoricosEtapas;
use App\Models\Materiais;
use App\Providers\DateHelpers;
use DateTime;

use App\Http\Controllers\CalculadoraPlacasController;

class RelatoriosController extends Controller
{
    public function index(Request $request)
    {

        $id = !empty($request->input('id')) ? ($request->input('id')) : (!empty($id) ? $id : false);
        $status_id = !empty($request->input('status_id')) ? ($request->input('status_id')) : (!empty($status_id) ? $status_id : false);
        $codigo_cliente = !empty($request->input('codigo_cliente')) ? ($request->input('codigo_cliente')) : (!empty($codigo_cliente) ? $codigo_cliente : false);
        $nome_cliente = !empty($request->input('nome_cliente')) ? ($request->input('nome_cliente')) : (!empty($nome_cliente) ? $nome_cliente : false);

        $filtrado = 0;
        $pedidos = DB::table('pedidos')
            ->join('historicos_pedidos', 'historicos_pedidos.pedidos_id', '=', 'pedidos.id')
            ->join('status', 'historicos_pedidos.status_id', '=', 'status.id')
            ->join('ficha_tecnica', 'ficha_tecnica.id', '=', 'pedidos.fichatecnica_id')
            ->join('pessoas', 'pessoas.id', '=', 'pedidos.pessoas_id')
            ->select(
                'pedidos.*',
                'status.nome as nome_status',
                'historicos_pedidos.status_id',
                'historicos_pedidos.created_at'
            )
            ->distinct()
            ->orderby('pedidos.status_id', 'asc');

        if (!empty($request->input('status'))){
            $pedidos = $pedidos->where('pedidos.status', '=', $request->input('status'));
            $filtrado++;
        } else {
            $pedidos = $pedidos->where('pedidos.status', '=', 'A');
        }


        if ($id) {
            $pedidos = $pedidos->where('pedidos.id', '=', $id);
            $filtrado++;
        }

        if(!empty($request->input('ep'))) {
            $pedidos = $pedidos->where('ficha_tecnica.ep', '=', $request->input('ep'));
            $filtrado++;
        }

        if(!empty($request->input('os'))) {
            $pedidos = $pedidos->where('pedidos.os', '=', $request->input('os'));
            $filtrado++;
        }

        if ($status_id) {
            $pedidos = $pedidos->where('pedidos.status_id', '=', $status_id);
            $filtrado++;
        }

        if(!empty($request->input('data_entrega')) && !empty($request->input('data_entrega_fim') )) {
            $pedidos = $pedidos->whereBetween('pedidos.data_entrega', [DateHelpers::formatDate_dmY($request->input('data_entrega')), DateHelpers::formatDate_dmY($request->input('data_entrega_fim'))]);
            $filtrado++;
        }
        if(!empty($request->input('data_entrega')) && empty($request->input('data_entrega_fim') )) {
            $pedidos = $pedidos->where('pedidos.data_entrega', '>=', DateHelpers::formatDate_dmY($request->input('data_entrega')));
            $filtrado++;
        }
        if(empty($request->input('data_entrega')) && !empty($request->input('data_entrega_fim') )) {
            $pedidos = $pedidos->where('pedidos.data_entrega', '<=', DateHelpers::formatDate_dmY($request->input('data_entrega_fim')));
            $filtrado++;
        }
        if(!empty($request->input('data_gerado')) && !empty($request->input('data_gerado_fim') )) {
            $pedidos = $pedidos->whereBetween('pedidos.data_gerado', [DateHelpers::formatDate_dmY($request->input('data_gerado')), DateHelpers::formatDate_dmY($request->input('data_gerado_fim'))]);
            $filtrado++;
        }
        if(!empty($request->input('data_gerado')) && empty($request->input('data_gerado_fim') )) {
            $pedidos = $pedidos->where('pedidos.data_gerado', '>=', DateHelpers::formatDate_dmY($request->input('data_gerado')));
            $filtrado++;
        }
        if(empty($request->input('data_gerado')) && !empty($request->input('data_gerado_fim') )) {
            $pedidos = $pedidos->where('pedidos.data_gerado', '<=', DateHelpers::formatDate_dmY($request->input('data_gerado_fim')));
            $filtrado++;
        }

        if ($codigo_cliente) {
            $pedidos = $pedidos->where('pessoas.codigo_cliente', '=', $codigo_cliente);
            $filtrado++;
        }

        if ($nome_cliente) {
            $pedidos = $pedidos->where('pessoas.nome_cliente', 'like', '%'.$nome_cliente.'%' );
            $filtrado++;
        }
        $arr_status = [];
        if($filtrado>0) {

            $pedidos = $pedidos->get();


            $dados_pedidos = [];

            foreach ($pedidos as $key => $pedido) {

                $dados_pedidos[$pedido->id][] = [
                    'data_status_alterado' => $pedido->created_at,
                    'status' => $pedido->nome_status,
                ];

            }
            $arr_status = [];
            foreach ($dados_pedidos as $pedido_id => &$dados_pedido) {

                foreach ($dados_pedido as $key => &$pedido) {

                    $horas = !empty($arr_status[$pedido['status']]['horas'])? $arr_status[$pedido['status']]['horas'] : '00:00:00' ;

                    if(isset($dados_pedido[$key+1])){
                        $hora_proximo_status = $dados_pedido[$key+1]['data_status_alterado'];
                        $dados_pedido[$key]['diferenca_horas'] = (new PedidosController)->diferencaDatasEmHoras($pedido['data_status_alterado'], $hora_proximo_status);


                        $arr_status[$pedido['status']]['horas'] = (new PedidosController)->somarHoras($horas, $dados_pedido[$key]['diferenca_horas']);
                    } else {
                        $dados_pedido[$key]['diferenca_horas'] = (new PedidosController)->diferencaDatasEmHoras($pedido['data_status_alterado'], date('Y-m-d H:i:s'));

                        $arr_status[$pedido['status']]['horas'] = (new PedidosController)->somarHoras($horas, $dados_pedido[$key]['diferenca_horas']);
                    }
                }
            }
        }

        $tela = 'pesquisar';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'consumo de materiais',
                'arr_status' => $arr_status,
                'request' => $request,
                'AllStatus' => (new PedidosController)->getAllStatus(),
				'rotaIncluir' => '',
				'rotaAlterar' => 'consumo-materiais-detalhes'
			);

        return view('relatorios_producao', $data);
    }

    public function relatorioPrevisaoMaterial(Request $request) {

        $data_inicio = !empty($request->input('data')) ? $request->input('data') : '';
        $data_fim = !empty($request->input('data_fim')) ? $request->input('data_fim') : '';
        $status_id = !empty($request->input('status_id')) ? $request->input('status_id') : '';
        $pedidos = [];

        $intervalo_dias = 0;
        if(!empty($data_inicio) && !empty($data_fim)) {

            $formato = 'd/m/Y';
            $inicio = DateTime::createFromFormat($formato, $data_inicio);
            $fim = DateTime::createFromFormat($formato, $data_fim);
            $intervalo_dias = $inicio->diff($fim)->days;

            $data_inicio = DateHelpers::formatDate_dmY($data_inicio);
            $data_fim = DateHelpers::formatDate_dmY($data_fim);

            $coluna = 'A.data_gerado';
            if($request->input('tipo_consulta') == 'P') {
                $coluna = 'A.data_entrega';

                if (!empty($data_inicio) && !empty($data_fim)){

                    $where[] = "$coluna between '$data_inicio 00:00:01' and '$data_fim 23:59:59'";
                }
                if (empty($data_inicio) && !empty($data_fim)){
                    $where[] = "$coluna <= '$data_fim 23:59:59'";

                }
                if (!empty($data_inicio) && empty($data_fim)){
                    $where[] = "$coluna >= '$data_inicio 00:00:01'" ;
                }

                if($status_id) {
                    $where[] = "A.status_id in(".implode(',', $status_id).")";
                }

            } else {
                $historicos_etapas = new HistoricosEtapas();
                $historicos_etapas = $historicos_etapas->select('pedidos_id');
                $historicos_etapas = $historicos_etapas->whereIn('status_id', $request->input('status_id'));
                $historicos_etapas = $historicos_etapas->where('etapas_pedidos_id', '=', 4 );

                if (!empty($data_inicio) && !empty($data_fim)){
                    $historicos_etapas = $historicos_etapas->whereBetween('created_at', [$data_inicio . ' 00:00:01' , $data_fim . ' 23:59:59']);
                }
                if (empty($data_inicio) && !empty($data_fim)){
                    $historicos_etapas = $historicos_etapas->where('created_at', '<=', $data_fim . ' 23:59:59');

                }
                if (!empty($data_inicio) && empty($data_fim)){
                    $historicos_etapas = $historicos_etapas->where('created_at', '>=', $data_inicio . ' 00:00:01');
                }

                $historicos_etapas = $historicos_etapas->get();

                $array_pedidos = $historicos_etapas->pluck('pedidos_id')->toArray();

                $busca_id = "A.id in (0)";

                if(!empty($array_pedidos)) {
                    $busca_id= "A.id in (".implode(',', $array_pedidos).")";
                }
                $where[] = $busca_id;
            }

            $status_pedido = "A.status = 'A'";
            $where[] = $status_pedido;

            if(count($where)) {
                $condicao = ' WHERE '.implode(' AND ', $where);
            }

            $pedidos = DB::select(DB::raw("SELECT DISTINCT
                                                A.id,
                                                A.os,
                                                D.material,
                                                D.id as material_id,
                                                B.ep,
                                                D.valor as valor_material,
                                                C.qtde_blank,
                                                A.qtde,
                                                D.peca_padrao
                                            FROM
                                                pedidos A
                                            INNER JOIN
                                                ficha_tecnica B
                                                ON B.id = A.fichatecnica_id
                                            INNER JOIN
                                                ficha_tecnica_itens C
                                            ON
                                                C.fichatecnica_id = A.fichatecnica_id
                                            inner join
                                                materiais D
                                            on
                                                D.id = C.materiais_id
                                            $condicao
                                                 -- AND D.material = 'Inserto IM3-3'
                                            group by
                                                A.id,
                                                A.os,
                                                D.material,
                                                D.id,
                                                B.ep,
                                                D.valor,
                                                C.qtde_blank,
                                                A.qtde,
                                                D.peca_padrao
                                            order by
                                                D.material, A.os, B.ep
                                        "));
            }
        $array_materiais=$arr_pedidos=[];
        $soma=0;
        $material_calculado=[];
        foreach ($pedidos as $key => &$pedido) {

            $dados_material = $this->detalhes($pedido->id, $pedido->material_id);
            $dados_material = $dados_material[$pedido->material];


            if(isset($arr_pedidos[$pedido->material_id]['qtde_consumo'])) {
                $quantidade_chapas = $dados_material['quantidade_chapas'];
                $arr_pedidos[$pedido->material_id]['qtde_consumo'] = $arr_pedidos[$pedido->material_id]['qtde_consumo'] + $quantidade_chapas;

            } else {
                $quantidade_chapas = $dados_material['quantidade_chapas'];
                $arr_pedidos[$pedido->material_id]['qtde_consumo'] = $quantidade_chapas;

            }

            if(isset($arr_pedidos[$pedido->material_id]['valor_previsto'])) {
                $arr_pedidos[$pedido->material_id]['valor_previsto'] = $arr_pedidos[$pedido->material_id]['valor_previsto'] + $dados_material['valor_total'];

            } else {
                $arr_pedidos[$pedido->material_id]['valor_previsto'] = $dados_material['valor_total'];

            }

            $arr_pedidos[$pedido->material_id]['id'] = $pedido->id;
            $arr_pedidos[$pedido->material_id]['material'] = $pedido->material;
            $arr_pedidos[$pedido->material_id]['material_id'] = $pedido->material_id;
            $material_calculado[$pedido->id][$pedido->material_id] = true;
            $arr_pedidos[$pedido->material_id]['fichas'][] = [
                'os' => $pedido->os,
                'material' => $pedido->material,
                'pedidos_ids' => $pedido->id,
                'qtde_itens' => $quantidade_chapas,
                'qtde' => $pedido->qtde
            ];

        }
        // dd($arr_pedidos);
        $totalizadores = [];
        foreach ($arr_pedidos as $key => $pedido) {
            // dd($pedido);
            $estoque = DB::select(DB::raw("SELECT
                                            ((A.qtde_chapa_peca_mo * A.qtde_por_pacote_mo) + (A.qtde_chapa_peca * A.qtde_por_pacote)) - ((select
                                                    count(1)
                                                from
                                                    lote_estoque_baixados X
                                                where
                                                    X.estoque_id = A.id) * (A.qtde_chapa_peca + A.qtde_chapa_peca_mo)) as estoque_atual,
                                            ((A.qtde_por_pacote_mo) + (A.qtde_por_pacote)) - ((select
                                                    count(1)
                                                from
                                                    lote_estoque_baixados X
                                                where
                                                    X.estoque_id = A.id)) as estoque_pacote_atual
                                        FROM
                                            estoque A
                                        INNER JOIN
                                            materiais B
                                            ON B.id = A.material_id
                                        INNER JOIN
                                            pessoas C
                                        ON
                                            C.id = A.fornecedor_id
                                        WHERE
                                            B.id = {$pedido['material_id']}
                                        ORDER BY
                                            A.data DESC
                                    "));

            $estoque_atual = !empty($estoque[0]->estoque_atual) ? $estoque[0]->estoque_atual : 0;
            $diferenca = $estoque_atual - $pedido['qtde_consumo'];
            $array_materiais[$pedido['material_id']] = [
                'id' => $pedido['id'],
                'material_id' => $pedido['material_id'],
                'material' => $pedido['material'],
                'estoque_atual' => $estoque_atual,
                'consumo_previsto' => $pedido['qtde_consumo'],
                'valor_previsto' => number_format($pedido['valor_previsto'], 2, ',', '.'),
                'diferenca' =>  round($diferenca, 2),
                'alerta' => $estoque_atual < $pedido['qtde_consumo'] || ($estoque_atual==0 && $pedido['qtde_consumo']==0) ? '<i class="text-danger fas fa-arrow-down"></i>' : '<i class="text-success fas fa-arrow-up"></i>',
                'os' => $pedido['fichas']
            ];
            $estoque_atual =  isset($totalizadores['estoque_atual']) ? $totalizadores['estoque_atual'] + $estoque_atual : $estoque_atual;
            $consumo_previsto =   isset($totalizadores['consumo_previsto']) ? $totalizadores['consumo_previsto'] + $pedido['qtde_consumo'] : $pedido['qtde_consumo'];
            $valor_previsto =  isset($totalizadores['valor_previsto']) ? $totalizadores['valor_previsto'] + $pedido['valor_previsto'] : $pedido['valor_previsto'];
            $diferenca =  isset($totalizadores['diferenca']) ? $totalizadores['diferenca'] +  $diferenca :  $diferenca;

            $totalizadores = [
                'estoque_atual' => $estoque_atual,
                'consumo_previsto' =>  $consumo_previsto,
                'valor_previsto' => $valor_previsto,
                'diferenca' => round($diferenca, 2),
            ];
        }
        if(count($totalizadores)) {
            $totalizadores['valor_previsto'] = number_format($totalizadores['valor_previsto'], 2, ',', '.');
        }
        $data = array(
            'tela' => 'relatorio-previsao-material',
            'nome_tela' => 'previsão de materiais',
            'materiais' => $array_materiais,
            'request' => $request,
            'status' => (new PedidosController)->getAllStatus(),
            'rotaIncluir' => '',
            'rotaAlterar' => '',
            'totalizadores' => $totalizadores
        );

        return view('relatorios', $data);
    }


    public function detalhes($id, $material_id) {

        $dados_materiais = DB::table('pedidos')
        ->join('ficha_tecnica', 'ficha_tecnica.id', '=', 'pedidos.fichatecnica_id')
        ->join('ficha_tecnica_itens', 'ficha_tecnica_itens.fichatecnica_id', '=', 'ficha_tecnica.id')
        ->join('materiais', 'ficha_tecnica_itens.materiais_id', '=', 'materiais.id')
        ->select(
            'pedidos.qtde',
            'ficha_tecnica_itens.qtde_blank',
            'ficha_tecnica_itens.medidax',
            'ficha_tecnica_itens.mediday',
            'ficha_tecnica_itens.blank',
            'materiais.material as nome_material',
            'materiais.id as id_material',
            'materiais.espessura',
            'materiais.unidadex',
            'materiais.unidadey',
            'materiais.valor',
        )
        ->orderby('materiais.material', 'ASC')
        ->orderby('ficha_tecnica_itens.blank', 'ASC');
        $dados_materiais = $dados_materiais->where('pedidos.id', '=', $id);
        $dados_materiais = $dados_materiais->where('materiais.id', '=', $material_id);
        $dados_materiais = $dados_materiais->get()->toArray();
        $total_somado=0;
        foreach ($dados_materiais as $array_material) {
            $tamanho_chapa = '';
            if(!empty($array_material->medidax)) {

                $array_pecas_necessarias[$array_material->nome_material][] = [
                        'quantidade' => $array_material->qtde_blank * $array_material->qtde,
                        'width' => $array_material->medidax ,
                        'height' => $array_material->mediday
                    ];

                $chapa[$array_material->nome_material] = [
                    'sheetWidth' => $array_material->unidadex,
                    'sheetHeight' => $array_material->unidadey,
                ];
                $tamanho_chapa = $array_material->unidadex .'x'. $array_material->unidadey;
            }
            $qtde = $array_material->qtde_blank * $array_material->qtde ;

            $dados_totais[$array_material->nome_material] = [
                'nome_material' => $array_material->nome_material,
                'tamanho_chapa' => $tamanho_chapa,
                'quantidade_chapas' => $qtde,
                'espessura' => $array_material->espessura,
                'valor_unitario' => $array_material->valor,
                'valor_total' => $array_material->valor,
            ];

        }
        if(!empty($array_pecas_necessarias)) {
            foreach ($array_pecas_necessarias as $nome_material => $pecas_necessarias) {

                $calculadora = new CalculadoraPlacasController($pecas_necessarias, $chapa[$nome_material]);
                $quantidade_chapas =  $calculadora->calcularNumeroPlacas();
                $dados_totais[$nome_material]['quantidade_chapas'] = $quantidade_chapas;
            }
        } else {

            $quantidade_chapas = $dados_materiais[0]->qtde;
            $dados_totais[$dados_materiais[0]->nome_material]['quantidade_chapas'] = $quantidade_chapas;
        }

        foreach($dados_totais as $nome_material => $value) {

            $unico = $dados_totais[$nome_material]['valor_unitario'];
            $quantidade_chapas = $dados_totais[$nome_material]['quantidade_chapas'];
            $dados_totais[$nome_material]['valor_total'] = $quantidade_chapas * $unico;
            $total_somado = $total_somado + $dados_totais[$nome_material]['valor_total'];
        };
        return $dados_totais;
    }

}
