<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\PedidosController;
use App\Models\Fichastecnicas;
use App\Models\Fichastecnicasitens;
use App\Models\Pedidos;
use App\Models\Status;
use App\Models\Historicos;
use App\Models\Pessoas;
use App\Models\Prioridades;
use App\Models\Transportes;
use App\Providers\DateHelpers;
use App\Http\Controllers\MaquinasController;
use App\Http\Controllers\ContatosController;
use App\Http\Controllers\PDFController;
use App\Mail\Contatos;
use App\Models\Maquinas;

class ConsumoMateriaisController extends Controller
{
    public function index(Request $request)
    {

        $id = !empty($request->input('id')) ? ($request->input('id')) : (!empty($id) ? $id : false);
        $status_id = !empty($request->input('status_id')) ? ($request->input('status_id')) : (!empty($status_id) ? $status_id : false);
        $codigo_cliente = !empty($request->input('codigo_cliente')) ? ($request->input('codigo_cliente')) : (!empty($codigo_cliente) ? $codigo_cliente : false);
        $nome_cliente = !empty($request->input('nome_cliente')) ? ($request->input('nome_cliente')) : (!empty($nome_cliente) ? $nome_cliente : false);



        $pedidos = DB::table('pedidos')
            ->join('status', 'pedidos.status_id', '=', 'status.id')
            ->join('ficha_tecnica', 'ficha_tecnica.id', '=', 'pedidos.fichatecnica_id')
            ->join('pessoas', 'pessoas.id', '=', 'pedidos.pessoas_id')
            ->select('pedidos.*', 'ficha_tecnica.ep', 'pessoas.nome_cliente', 'status.nome as nome_status' , 'status.id as id_status');

        if (!empty($request->input('status'))){
            $pedidos = $pedidos->where('pedidos.status', '=', $request->input('status'));
        }

        if ($id) {
            $pedidos = $pedidos->where('pedidos.id', '=', $id);
        }

        if ($status_id) {
            $pedidos = $pedidos->where('pedidos.status_id', '=', $status_id);
        }

        if(!empty($request->input('data_entrega')) && !empty($request->input('data_entrega_fim') )) {
            $pedidos = $pedidos->whereBetween('pedidos.data_entrega', [DateHelpers::formatDate_dmY($request->input('data_entrega')), DateHelpers::formatDate_dmY($request->input('data_entrega_fim'))]);
        }
        if(!empty($request->input('data_entrega')) && empty($request->input('data_entrega_fim') )) {
            $pedidos = $pedidos->where('pedidos.data_entrega', '>=', DateHelpers::formatDate_dmY($request->input('data_entrega')));
        }
        if(empty($request->input('data_entrega')) && !empty($request->input('data_entrega_fim') )) {
            $pedidos = $pedidos->where('pedidos.data_entrega', '<=', DateHelpers::formatDate_dmY($request->input('data_entrega_fim')));
        }

        if ($codigo_cliente) {
            $pedidos = $pedidos->where('pessoas.codigo_cliente', '=', $codigo_cliente);
        }

        if ($nome_cliente) {
            $pedidos = $pedidos->where('pessoas.nome_cliente', 'like', '%'.$nome_cliente.'%' );
        }


        $pedidos = $pedidos->get();

        $tela = 'pesquisar';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'consumo de materiais',
                'pedidos' => $pedidos,
                'request' => $request,
                'AllStatus' => (new PedidosController)->getAllStatus(),
				'rotaIncluir' => '',
				'rotaAlterar' => 'consumo-materiais-detalhes'
			);

        return view('consumo_materiais', $data);
    }

    function detalhes(Request $request) {


        $id = !empty($request->input('id')) ? ($request->input('id')) : (!empty($id) ? $id : false);

        $pedidos = DB::table('pedidos')
            ->join('status', 'pedidos.status_id', '=', 'status.id')
            ->join('ficha_tecnica', 'ficha_tecnica.id', '=', 'pedidos.fichatecnica_id')
            ->join('pessoas', 'pessoas.id', '=', 'pedidos.pessoas_id')
            ->select('pedidos.*', 'ficha_tecnica.ep', 'pessoas.nome_cliente', 'status.nome as nome_status' , 'status.id as id_status');

        $pedidos = $pedidos->where('pedidos.id', '=', $id);

        $pedidos = $pedidos->get();


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
        $dados_materiais = $dados_materiais->get()->toArray();

        $array_materiais=[];
        foreach ($dados_materiais as $array_material) {
            $array_materiais[$array_material->nome_material][$array_material->medidax][$array_material->mediday][$array_material->espessura]['qtde'][] = $array_material->qtde_blank;
            $array_materiais[$array_material->nome_material][$array_material->medidax][$array_material->mediday][$array_material->espessura]['unidadey'] = $array_material->unidadey;
            $array_materiais[$array_material->nome_material][$array_material->medidax][$array_material->mediday][$array_material->espessura]['unidadex'] = $array_material->unidadex;
            $array_materiais[$array_material->nome_material][$array_material->medidax][$array_material->mediday][$array_material->espessura]['valor'] = $array_material->valor;
            $array_materiais[$array_material->nome_material][$array_material->medidax][$array_material->mediday][$array_material->espessura]['qtde_pedido'] = $array_material->qtde;

        }

        $totais['Totaltotal'] = $totais['total'] = 0;

        foreach ($array_materiais as $nome_material => $valueA) {
            foreach ($valueA as $medidax => $valueB) {
                foreach ($valueB as $mediday => $valueC) {
                    foreach ($valueC as $espessura => $valueD) {
                        $quantidade =0;
                        $placas_utilizadas= [];
                        foreach ($valueD['qtde'] as $qtde) {
                            $quantidade = $quantidade + $qtde;
                        }

                        $medida_placax = (float)$valueD['unidadex'];
                        $medida_placay = (float)$valueD['unidadey'];
                        $valor = (float)$valueD['valor'];
                        $qtde_pedido = (float)$valueD['qtde_pedido'];

                        if(!empty($medidax)) {

                            $larguraPrincipal = (float)$medida_placax;
                            $alturaPrincipal = (float)$medida_placay;
                            $larguraPeca = (float)$medidax;
                            $alturaPeca = (float)$mediday;
                            $totalPecasPequenas = $quantidade;
                            $placas = $this->calculaPecas($larguraPrincipal, $alturaPrincipal, $larguraPeca, $alturaPeca, $totalPecasPequenas);
                            $custo=  $placas * (float)$valor;


                            $placas_utilizadas = [
                                'placas' => $placas,
                                'custo' => number_format($custo, 2, ',','.'),
                                'valor_unitario' => number_format((float)$valor, 2, ',','.'),
                                'custo_noformat' => $custo,
                                'Totalplacas' => $placas * $qtde_pedido,
                                'Totalcusto' => number_format($custo * $qtde_pedido, 2, ',','.'),
                                'Totalvalor_unitario' => number_format((float)$valor, 2, ',','.'),
                                'Totalcusto_noformat' => $custo * $qtde_pedido,
                            ];

                        } else {
                            //alimenta nas posicoes do array caso não for um blank
                            $placas_utilizadas = [
                                'placas' => $quantidade,
                                'custo' => number_format($valor * $quantidade, 2, ',','.'),
                                'valor_unitario' => number_format((float)$valor, 2, ',','.'),
                                'custo_noformat' => $valor,
                                'Totalplacas' => $quantidade * $qtde_pedido,
                                'Totalcusto' => number_format(($valor * $quantidade) * $qtde_pedido, 2, ',','.'),
                                'Totalvalor_unitario' => number_format((float)$valor, 2, ',','.'),
                                'Totalcusto_noformat' => $valor * $qtde_pedido,
                            ];
                        }
                        $totais['total'] = $totais['total'] + DateHelpers::formatFloatValue($placas_utilizadas['custo_noformat']);
                        $totais['Totaltotal'] = $totais['Totaltotal'] + DateHelpers::formatFloatValue($placas_utilizadas['Totalcusto_noformat']);
                        $materiais[] = [
                            'nome_material' => $nome_material,
                            'medida_placax' => $medida_placax,
                            'medida_placay' => $medida_placay,
                            'medidax' => $medidax,
                            'mediday' => $mediday,
                            'espessura' => $espessura,
                            'quantidade' => $quantidade,
                            'placas_utilizadas' => $placas_utilizadas
                        ];
                    }
                }
            }
        }
        $totais['total'] = number_format($totais['total'],2,',','.');
        $totais['Totaltotal'] = number_format($totais['Totaltotal'],2,',','.');

        $tela = 'detalhes';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'consumo de materiais',
                'pedidos' => $pedidos,
                'materiais' => $dados_materiais,
                'calculos' => $materiais,
                'totais' => $totais,
                'request' => $request,
                'AllStatus' => (new PedidosController)->getAllStatus(),
				'rotaAlterar' => 'consumo-materiais-detalhes'
			);

        return view('consumo_materiais', $data);
    }

    function calculaPecas($alturaChapa, $larguraChapa, $alturaPeca, $larguraPeca, $qtdePecas) {

        $calculo_horizontalx = floor($alturaChapa/$alturaPeca);
        $calculo_horizontaly = floor($larguraChapa/$larguraPeca);
        $horizontal = ($calculo_horizontalx + $calculo_horizontaly);

        $calculo_verticalx = floor($larguraChapa/$larguraPeca);
        $calculo_verticaly = floor($alturaChapa/$alturaPeca);

        $vertical = ($calculo_verticalx + $calculo_verticaly);

        $pecas_po_placa = ($horizontal > $vertical ? $horizontal : $vertical);

        $quantidadeChapas = ceil($qtdePecas/$pecas_po_placa);
        return $quantidadeChapas;


    }

}


