<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\PedidosController;
use App\Providers\DateHelpers;

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

        if(!empty($request->input('ep'))) {
            $pedidos = $pedidos->where('ficha_tecnica.ep', '=', $request->input('ep'));
        }

        if(!empty($request->input('os'))) {
            $pedidos = $pedidos->where('pedidos.os', '=', $request->input('os'));
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

    /**
     *
     */
    public function detalhes(Request $request, $imprimir = 0) {


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



// // Exemplo de uso:
// $pecas = [
//     // ['quantidade' => 9, 'width' => 350, 'height' => 250],
//     ['quantidade' => 2, 'width' => 1200, 'height' => 200],
//     // ['quantidade' => 1, 'width' => 150, 'height' => 610],
// ];
// $chapa = [
//     'sheetWidth' => 1200,
//     'sheetHeight' => 960,
// ];

// $resultado = SheetCuttingCalculator::calculateSheets($pecas, $chapa);

        $totais['Totaltotal'] = $totais['total'] = 0;
        $percentualPerda = 10;
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
                        // \Log::info(print_r($medida_placax.'x'.$medida_placay, true));
                        // $medida_placax = $medida_placax * (1 - ($percentualPerda / 100));
                        // $medida_placay = $medida_placay * (1 - ($percentualPerda / 100));
                        
                        // \Log::info(print_r($medida_placax.'x'.$medida_placay, true));

                        $valor = (float)$valueD['valor'];
                        $qtde_pedido = (float)$valueD['qtde_pedido'];

                        if(!empty($medidax)) {

                            $larguraPrincipal = (float)$medida_placax;
                            $alturaPrincipal = (float)$medida_placay;
                            $larguraPeca = (float)$medidax;
                            $alturaPeca = (float)$mediday;
                            $totalPecasPequenas = $quantidade*$qtde_pedido;
                            
                            $retorno = $this->calculaPecas($larguraPrincipal, $alturaPrincipal, $larguraPeca, $alturaPeca, $totalPecasPequenas);
                            $placas = $retorno['quantidadeChapas'];

                            $custo=  $placas * (float)$valor;

                            $placas_utilizadas = [
                                'placas' => $placas,
                                'custo' => number_format($custo, 2, ',','.'),
                                'valor_unitario' => number_format((float)$valor, 2, ',','.'),
                                'custo_noformat' => $custo,
                                'Totalplacas' => $placas,
                                'Totalcusto' => number_format($custo, 2, ',','.'),
                                'Totalvalor_unitario' => number_format((float)$valor, 2, ',','.'),
                                'Totalcusto_noformat' => $custo
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
        
        
        // \Log::info(print_r($materiais, true));
        // foreach ($materiais as $key => $material) {
        //    if($material['material'] == $materiais[$key -1]['material'] &&  $material['espessura']) {
        // $materiais_agrupado[$key] = [            
        //         'nome_material' => PSAI Cinza Claro
        //         'medida_placax' => 1200
        //         'medida_placay' => 960
        //         'espessura' => 3
        //         'quantidade' => 2
        //         'placas_utilizadas' => [                    
        //                 'placas' => 167
        //                 'custo' => 2.588,50
        //                 'valor_unitario' => 15,50
        //                 'custo_noformat' => 2588.5
        //                 'Totalplacas' => 167
        //                 'Totalcusto' => 2.588,50
        //                 'Totalvalor_unitario' => 15,50
        //                 'Totalcusto_noformat' => 2588.5                    
        //         ]
        // ]         
        // }
        
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
                'imprimir' => $imprimir,
                'AllStatus' => (new PedidosController)->getAllStatus(),
				'rotaAlterar' => 'consumo-materiais-detalhes'
			);

        return view('consumo_materiais', $data);

    }

    function calculaPecas($alturaChapa, $larguraChapa, $alturaPeca, $larguraPeca, $qtdePecas) {
        
        $calculo_horizontalx = floor($alturaChapa/$alturaPeca);
        $calculo_horizontaly = floor($larguraChapa/$larguraPeca);

        $qtde_na_horizontal = ($calculo_horizontalx * $calculo_horizontaly);

        $sobra_na_horizontalx = $alturaChapa - ($alturaPeca*$calculo_horizontalx);
        $sobra_na_horizontaly = $alturaChapa - ($larguraPeca*$calculo_horizontaly);


        $calculo_verticalx = floor($larguraChapa/$larguraPeca);
        $calculo_verticaly = floor($alturaChapa/$alturaPeca);

        $qtde_na_vertical = ($calculo_verticalx * $calculo_verticaly);

        $sobra_na_verticalx = $alturaChapa - ($alturaPeca*$calculo_verticalx);
        $sobra_na_verticaly = $alturaChapa - ($larguraPeca*$calculo_verticaly);

        $pecas_po_placa = ($qtde_na_horizontal > $qtde_na_vertical ? $qtde_na_horizontal : $qtde_na_vertical);

        $quantidadeChapas = ceil($qtdePecas/$pecas_po_placa);
        return [
            'quantidadeChapas' => $quantidadeChapas,
            'sobra_na_horizontal' => [
                'x' => $sobra_na_horizontalx,
                'y' => $sobra_na_horizontaly
            ],
            'sobra_na_vertical' => [
                'x' => $sobra_na_verticalx,
                'y' => $sobra_na_verticaly
            ],

        ];
            


    }

    public static function calculateSheets($pieces, $sheet)
    {
        $sheet_area = $sheet['sheetWidth'] * $sheet['sheetHeight'];

        // Calcula a área total ocupada pelas peças sem considerar a quantidade
        $total_pieces_area = 0;
        foreach ($pieces as $piece) {
            $total_pieces_area += $piece['width'] * $piece['height'];
        }

        // Calcula o número de chapas necessárias
        $total_sheets = 0;
        $remaining_area = $sheet_area;
        foreach ($pieces as $piece) {
            $piece_area = $piece['width'] * $piece['height'];
            $piece_quantity = $piece['quantidade'];

            while ($piece_quantity > 0 && $remaining_area >= $piece_area) {
                $remaining_area -= $piece_area;
                $piece_quantity--;
            }

            // Se não couber nenhuma peça, passa para a próxima chapa
            if ($piece_quantity == $piece['quantidade']) {
                continue;
            }

            $total_sheets++;
            $remaining_area = $sheet_area;
        }

        return $total_sheets;
    }


}

class SheetCuttingCalculator2
{
    public static function calculateSheets($pieces, $sheet)
    {
        $sheet_area = $sheet['sheetWidth'] * $sheet['sheetHeight'];
        $total_pieces_area = 0;

        foreach ($pieces as $piece) {
            $total_pieces_area += $piece['width'] * $piece['height'] * $piece['quantidade'];
        }

        $total_sheets = ceil($total_pieces_area / $sheet_area);
        $unused_area = $total_sheets * $sheet_area - $total_pieces_area;

        return ['total_sheets' => $total_sheets, 'unused_area' => $unused_area];
    }
}


class SheetCuttingCalculator
{
    public static function calculateSheets($pieces, $sheet)
    {
        $total_sheets = 0;
        $unused_width = $sheet['sheetWidth'];
        $unused_height = $sheet['sheetHeight'];

        foreach ($pieces as $piece) {
            $piece_width = $piece['width'];
            $piece_height = $piece['height'];
            $piece_quantity = $piece['quantidade'];

            while ($piece_quantity > 0) {
                if ($piece_width <= $unused_width && $piece_height <= $unused_height) {
                    $unused_width -= $piece_width;
                    $unused_height -= $piece_height;
                    $piece_quantity--;
                } else {
                    $total_sheets++;
                    $unused_width = $sheet['sheetWidth'];
                    $unused_height = $sheet['sheetHeight'];
                }
            }
        }

        return ['total_sheets' => $total_sheets, 'unused_width' => $unused_width, 'unused_height' => $unused_height];
    }
}