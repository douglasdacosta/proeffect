<?php

namespace App\Http\Controllers;

use App\Models\MateriaisHistoricosValores;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\PedidosController;
use App\Models\HistoricosEtapas;
use App\Models\Materiais;
use App\Providers\DateHelpers;
use DateTime;

use App\Http\Controllers\CalculadoraPlacasController;
use App\Models\CategoriasMateriais;
use App\Models\HistoricosPedidos;

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


    public function consulta_previsao_material($data_inicio, $data_fim, $status_id) {

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
        return $where;
    }

    public function consulta_executados($data_inicio, $data_fim, $status_id, $request) {
        // Usinagem
        // Acabamento
        // Montagem
        // Inspeção
        // Embalar
        $status_com_inicio_fim = ["4", "5", "6", "7", "8"];
        $where = [];
        // Verifica se há interseção entre os arrays
        if (!empty($status_id) && empty(array_intersect($status_id, $status_com_inicio_fim))) {

            $historicos_pedidos = new HistoricosPedidos();
            $historicos_pedidos = $historicos_pedidos->select('pedidos_id');
            $historicos_pedidos = $historicos_pedidos->whereIn('status_id', $request->input('status_id'));

            if (!empty($data_inicio) && !empty($data_fim)){
                $historicos_pedidos = $historicos_pedidos->whereBetween('created_at', [$data_inicio . ' 00:00:01' , $data_fim . ' 23:59:59']);
            }
            if (empty($data_inicio) && !empty($data_fim)){
                $historicos_pedidos = $historicos_pedidos->where('created_at', '<=', $data_fim . ' 23:59:59');

            }
            if (!empty($data_inicio) && empty($data_fim)){
                $historicos_pedidos = $historicos_pedidos->where('created_at', '>=', $data_inicio . ' 00:00:01');
            }

            $categoria_id = $request->input('categorias');
            if(!empty($categoria_id)) {
                $where[] = "D.categoria_id = $categoria_id";
            }

            $historicos_pedidos = $historicos_pedidos->get();

            $array_pedidos = $historicos_pedidos->pluck('pedidos_id')->toArray();

            $busca_id = "A.id in (0)";

            if(!empty($array_pedidos)) {
                $busca_id= "A.id in (".implode(',', $array_pedidos).")";
            }
            $where[] = $busca_id;


        } else {
            if(!empty($request->input('status_id'))){
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

                $categoria_id = $request->input('categorias');

                if(!empty($categoria_id)) {
                    $where[] = "D.categoria_id = $categoria_id";
                }

                $busca_id = "A.id in (0)";

                if(!empty($array_pedidos)) {
                    $busca_id= "A.id in (".implode(',', $array_pedidos).")";
                }
                $where[] = $busca_id;
            }
        }

        return $where;
    }



    public function relatorioPrevisaoMaterial(Request $request) {
        $data_inicio = !empty($request->input('data')) ? $request->input('data') : '';
        $data_fim = !empty($request->input('data_fim')) ? $request->input('data_fim') : '';
        $status_id = !empty($request->input('status_id')) ? $request->input('status_id') : '';
        $pedidos = [];

        $intervalo_dias = 0;
        $tela = 'relatorio-previsao-material';
        if(empty($data_inicio) && empty($data_fim)) {
            $data_inicio= \Carbon\Carbon::now()->startOfMonth()->format('d/m/Y');
            $data_fim = \Carbon\Carbon::now()->endOfMonth()->format('d/m/Y');

        }

        $formato = 'd/m/Y';
        $inicio = DateTime::createFromFormat($formato, $data_inicio);
        $fim = DateTime::createFromFormat($formato, $data_fim);
        $intervalo_dias = $inicio->diff($fim)->days;

        $data_inicio = DateHelpers::formatDate_dmY($data_inicio);
        $data_fim = DateHelpers::formatDate_dmY($data_fim);

        $categoria = $request->input('categorias');

        $coluna = 'A.data_gerado';
        switch ($request->input('tipo_consulta')) {

            //prevista
            case 'P':

            $where = $this->consulta_previsao_material($data_inicio, $data_fim, $status_id);

            break;

            //executada
            case 'E':

                $where = $this->consulta_executados($data_inicio, $data_fim, $status_id, $request);

            break;
            //valores_retroativos
            case 'V':

                $where = $this->consulta_executados($data_inicio, $data_fim, $status_id, $request);
                $tela = 'valores_retroativos';
            break;

            default:
                $where = $this->consulta_executados($data_inicio, $data_fim, $status_id, $request);

            break;
        }
        $status_pedido = "A.status = 'A'";
        $where[] = $status_pedido;

        if(count($where)) {
            $condicao = ' WHERE '.implode(' AND ', $where);
        }

        $pedidos = $this->getDadosPedidosPorCondicao($condicao);


        $array_materiais=$arr_pedidos=[];
        if(!empty($pedidos)) {

            $arr_pedidos = $this->calculaDadosMaterial($pedidos);
        }

        $totalizadores = $totalizadoresRetroativo = [];

        $dadosMaterialRetroativo = [];
        foreach ($arr_pedidos as $key => $pedido) {

            $estoque = $this->getEstoqueByMaterial($pedido['material_id']);
            $estoque_atual = $this->CalculaEstoqueAtual($estoque);
            switch ($request->input('tipo_consulta')) {


                case 'V':

                    $entradas=$estoque_baixado=0;


                    $estoques = $this->getEstoqueByMaterialDataCategoria($pedido['material_id'], $data_inicio, $data_fim, $categoria);

                    if(!empty($estoques)) {
                        foreach ($estoques as $key => $value) {
                            $entradas += $value->estoque_total;
                            $estoque_baixado += $value->estoque_baixado;
                        }


                        $valorMaterialRetroativo = $this->buscaValorMaterialPorData($pedido['material_id'], $data_inicio, $data_fim);

                        $valor_estoque_atual = $estoque_atual * $pedido['valor_material'];

                        $valor_entradas = $entradas * $valorMaterialRetroativo;

                        $valor_consumido = $estoque_baixado * $valorMaterialRetroativo;

                        $dadosMaterialRetroativo[] = [
                            'material' => $pedido['material'],
                            'material_id' => $pedido['material_id'],
                            'estoque_atual' => $estoque_atual,
                            'valor_estoque_atual' =>$valor_estoque_atual,
                            'entradas' => $entradas,
                            'valor_entradas' => $valor_entradas,
                            'consumido' => $estoque_baixado,
                            'valor_consumido' => $valor_consumido
                        ];

                        $estoque_atual_retroativo =  isset($totalizadoresRetroativo['estoque_atual']) ? $totalizadoresRetroativo['estoque_atual'] + $estoque_atual : $estoque_atual;
                        $consumo_previsto_retroativo =   isset($totalizadoresRetroativo['valor_estoque_atual']) ? $totalizadoresRetroativo['valor_estoque_atual'] + $valor_estoque_atual : $valor_estoque_atual;
                        $entradas_retroativo =   isset($totalizadoresRetroativo['entradas']) ? $totalizadoresRetroativo['entradas'] + $entradas : $entradas;
                        $valor_entradas_retroativo =   isset($totalizadoresRetroativo['valor_entradas']) ? $totalizadoresRetroativo['valor_entradas'] + $valor_entradas : $valor_entradas;
                        $consumido_retroativo =   isset($totalizadoresRetroativo['consumido']) ? $totalizadoresRetroativo['consumido'] + $estoque_baixado : $estoque_baixado;
                        $valor_consumido_retroativo =   isset($totalizadoresRetroativo['valor_consumido']) ? $totalizadoresRetroativo['valor_consumido'] + $valor_consumido : $valor_consumido;

                        $totalizadoresRetroativo = [
                            'estoque_atual' => $estoque_atual_retroativo,
                            'valor_estoque_atual' => $consumo_previsto_retroativo,
                            'entradas' => $entradas_retroativo,
                            'valor_entradas' => $valor_entradas_retroativo,
                            'consumido' => $consumido_retroativo,
                            'valor_consumido' => $valor_consumido_retroativo,
                            'os' => $pedido['fichas']
                        ];

                    }

                break;

                default:


                break;
            }



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
            'tela' => $tela,
            'nome_tela' => 'previsão de materiais',
            'materiais' => $array_materiais,
            'dadosMaterialRetroativo' => $dadosMaterialRetroativo,
            'request' => $request,
            'status' => (new PedidosController)->getAllStatus(),
            'CategoriasMateriais' => (new CategoriasMateriais)->get(),
            'rotaIncluir' => '',
            'rotaAlterar' => '',
            'totalizadores' => $totalizadores,
            'totalizadoresRetroativo' => $totalizadoresRetroativo
        );

        return view('relatorios', $data);
    }


    public function buscaValorMaterialPorData($material_id, $data_inicio, $data_fim) {

            $valorMaterial = new MateriaisHistoricosValores();
            $valorMaterial = $valorMaterial->where('materiais_id', '=', $material_id)
                ->select('valor')
                ->whereBetween('created_at', [$data_inicio . ' 00:00:01' , $data_fim . ' 23:59:59'])
                ->orderBy('created_at', 'desc')
                ->get();

            return !empty($valorMaterial[0]->valor) ? $valorMaterial[0]->valor : '0';
    }

    /**
     * Summary of calculaDadosMaterial
     * @param mixed $pedidos
     * @return array
     */
    public function calculaDadosMaterial($pedidos) {
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
            $arr_pedidos[$pedido->material_id]['valor_material'] = $pedido->valor_material;
            $material_calculado[$pedido->id][$pedido->material_id] = true;
            $arr_pedidos[$pedido->material_id]['fichas'][] = [
                'os' => $pedido->os,
                'ep' => $pedido->ep,
                'material' => $pedido->material,
                'pedidos_ids' => $pedido->id,
                'qtde_itens' => $quantidade_chapas,
                'qtde' => $pedido->qtde
            ];

        }

        return $arr_pedidos;
    }

    /**
     * Summary of getDadosPedidosPorCondicao
     * @param mixed $condicao
     * @return array
     */
    public function getDadosPedidosPorCondicao($condicao) {
        return DB::select(DB::raw("SELECT DISTINCT
                                                A.id,
                                                A.os,
                                                D.material,
                                                D.id as material_id,
                                                B.ep,
                                                D.valor as valor_material,
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
                                            left join
                                                historicos E
                                            on
                                                E.pedidos_id = A.id
                                            $condicao
                                                  -- AND D.material = 'PSAI Preto 3mm'
                                            group by
                                                A.id,
                                                A.os,
                                                D.material,
                                                D.id,
                                                B.ep,
                                                D.valor,
                                                A.qtde,
                                                D.peca_padrao
                                            order by
                                                D.material, A.os, B.ep
                                        "));
    }

    /**
     * Summary of CalculaEstoqueAtual
     * @param mixed $estoque
     * @return float
     */
    public function CalculaEstoqueAtual($estoque) {

        $estoque_total = $gasto_total  = 0.00;
        foreach ($estoque as $key => $value) {

            $qtde_baixa = $this->getEstoqueById($value->id);

            $gasto_total += ($qtde_baixa[0]->qtde_baixa * ($value->qtde_chapa_peca));

            $estoque_total += ($value->qtde_chapa_peca * $value->qtde_por_pacote) + ($value->qtde_chapa_peca_mo * $value->qtde_por_pacote_mo);

        }

        $estoque_atual = $estoque_total - $gasto_total;

        return $estoque_atual;
    }

    /**
     * Summary of getEstoqueById
     * @param mixed $id
     * @return array
     */
    public function getEstoqueById($id) {

        return DB::select(DB::raw("SELECT
                                        count(1) as qtde_baixa
                                    FROM
                                        lote_estoque_baixados A
                                    INNER JOIN
                                        estoque C
                                    on
                                        C.id = A.estoque_id
                                        and C.status = 'A'
                                    INNER JOIN
                                        materiais B
                                        ON B.id = C.material_id
                                    WHERE
                                        A.estoque_id = $id
                                "));
    }

    /**
     * Summary of getEstoqueByMaterial
     * @param mixed $material_id
     * @return array
     */
    public function getEstoqueByMaterial($material_id) {

        return DB::select(DB::raw("SELECT
                                            A.data,
                                            A.id,
                                            A.qtde_chapa_peca,
                                            A.qtde_chapa_peca_mo,
                                            A.qtde_por_pacote,
                                            A.qtde_por_pacote_mo,
                                            B.estoque_minimo,
                                            A.lote,
                                            C.nome_cliente as fornecedor,
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
                                                    X.estoque_id = A.id)) as estoque_pacote_atual,
                                            B.material,
                                            A.material_id,
                                            B.consumo_medio_mensal,
                                            A.alerta_baixa_errada
                                        FROM
                                            estoque A
                                        INNER JOIN
                                            materiais B
                                            ON B.id = A.material_id
                                        INNER JOIN
                                            pessoas C
                                        ON
                                            C.id = A.fornecedor_id
                                        WHERE A.status = 'A' AND B.id = $material_id"
                                            ));
    }

    /**
     * Summary of getEstoqueByMaterialData
     * @param mixed $material_id
     * @param mixed $data_inicial
     * @param mixed $data_final
     * @return array
     */
    public function getEstoqueByMaterialDataCategoria($material_id, $data_inicial, $data_final, $categoria) {

        $filtro_categoria = '';
        if(!empty($categoria)) {
            $filtro_categoria = "AND B.categoria_id = $categoria";
        }
        return DB::select(DB::raw("SELECT
                                            ((A.qtde_chapa_peca_mo * A.qtde_por_pacote_mo) + (A.qtde_chapa_peca * A.qtde_por_pacote))  as estoque_total,
                                            ((select
                                                count(1)
                                            from
                                                lote_estoque_baixados X
                                            where
                                                X.estoque_id = A.id) * (A.qtde_chapa_peca + A.qtde_chapa_peca_mo)) as estoque_baixado
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
                                            A.status = 'A'
                                            AND B.id = $material_id
                                            AND A.data between '$data_inicial' and '$data_final'
                                            $filtro_categoria"
                                        ));
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
