<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\PedidosController;
use App\Providers\DateHelpers;

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

}


