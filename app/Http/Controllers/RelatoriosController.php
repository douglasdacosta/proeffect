<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\PedidosController;
use App\Models\Materiais;
use App\Providers\DateHelpers;
use DateTime;

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
        $pedidos = [];

        $intervalo_dias = 0;
        if(!empty($data_inicio) && !empty($data_fim)) {

            $formato = 'd/m/Y';
            $inicio = DateTime::createFromFormat($formato, $data_inicio);
            $fim = DateTime::createFromFormat($formato, $data_fim);
            $intervalo_dias = $inicio->diff($fim)->days;

            $data_inicio = DateHelpers::formatDate_dmY($data_inicio);
            $data_fim = DateHelpers::formatDate_dmY($data_fim);
            if (!empty($data_inicio) && !empty($data_fim)){

                $where[] = "A.data_entrega between '$data_inicio 00:00:01' and '$data_fim 23:59:59'";
            }
            if (empty($data_inicio) && !empty($data_fim)){
                $where[] = "A.data_entrega <= '$data_fim 23:59:59'";

            }
            if (!empty($data_inicio) && empty($data_fim)){
                $where[] = "A.data_entrega >= '$data_inicio 00:00:01'" ;
            }

            $status_pedido = "A.status = 'A'";
            $where[] = $status_pedido;

            if(count($where)) {
                $condicao = ' WHERE '.implode(' AND ', $where);
            }

            $pedidos = DB::select(DB::raw("SELECT
                                                A.id,
                                                A.os,
                                                D.material,
                                                D.id as material_id,
                                                D.consumo_medio_mensal,
                                                B.ep,
                                                C.qtde_blank
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
                                        "));
            }
        $array_materiais=$arr_pedidos=[];

        foreach ($pedidos as $key => &$pedido) {
            $arr_pedidos[$pedido->material_id] = [
                'id' => $pedido->id,
                'material' => $pedido->material,
                'material_id' => $pedido->material_id,
                'consumo_medio_mensal' =>$pedido->consumo_medio_mensal
            ];
        }

        foreach ($arr_pedidos as $key => $pedido) {

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

            $media_uso_mensal_dia = $pedido['consumo_medio_mensal']/30;

            $estoque_atual = !empty($estoque[0]->estoque_atual) ? $estoque[0]->estoque_atual : 0;
            $consumo_previsto = number_format($media_uso_mensal_dia * $intervalo_dias, 0, '.', '');
            $array_materiais[$pedido['material_id']] = [
                'id' => $pedido['id'],
                'material_id' => $pedido['material_id'],
                'material' => $pedido['material'],
                'estoque_atual' => $estoque_atual,
                'consumo_medio_mensal' => $pedido['consumo_medio_mensal'],
                'media_uso_mensal_dia' => $media_uso_mensal_dia,
                'consumo_previsto' => $consumo_previsto,
                'diferenca' => $estoque_atual - $consumo_previsto,
                'alerta' => $estoque_atual < $consumo_previsto ? '<i class="text-danger fas fa-arrow-down"></i>' : '<i class="text-success fas fa-arrow-up"></i>'
            ];
        }
        $data = array(
            'tela' => 'relatorio-previsao-material',
            'nome_tela' => 'previsão de materiais',
            'materiais' => $array_materiais,
            'request' => $request,
            'status' => (new PedidosController)->getAllStatus(),
            'rotaIncluir' => '',
            'rotaAlterar' => ''
        );

        return view('relatorios', $data);
    }
}
