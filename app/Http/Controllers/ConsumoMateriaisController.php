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

            if(!empty($array_material->medidax)) {
                    
                $array_pecas_necessarias[$array_material->nome_material][] = [
                        'quantidade' => $array_material->qtde_blank * $array_material->qtde, 
                        'width' => $array_material->medidax , 
                        'height' => $array_material->mediday
                    ];

                $chapa[$array_material->nome_material] = [
                    'sheetWidth' => $array_material->unidadex,
                    'sheetHeight' => $array_material->unidadey
                ];
            }    
        }        
       
        foreach ($array_pecas_necessarias as $nome_material => $pecas_necessarias) {
            $quantidade_chapas = 0;
            while(count($pecas_necessarias)>0) {
                $quantidade_chapas ++;
                $empacotamento = new Empacotamento($chapa[$nome_material], $pecas_necessarias);
                $empacotamento->organizarPecas();
                $resultado = $empacotamento->getChapaOrganizada();

                foreach($resultado['pecasUsadas'] as $tamanho => $dados){                
                    list($w, $h) = explode('x', $tamanho);
                    foreach ($pecas_necessarias as $key => &$value) {
                        if($value['width'] == $w && $value['height']==$h) {
                            if($value['quantidade'] == count($dados)) {
                                unset($pecas_necessarias[$key]);
                            }
                            $value['quantidade'] = $value['quantidade']- count($dados);                    
                        }
                    }
                }
            }

            $dados_calculados[$nome_material] = [
                'quantidade_chapas' => $quantidade_chapas
            ];
        }

        \Log::info(print_r($dados_calculados, true));

        // $totais['total'] = number_format($totais['total'],2,',','.');
        // $totais['Totaltotal'] = number_format($totais['Totaltotal'],2,',','.');

        $tela = 'detalhes';
    	$data = array(
				'tela' => $tela,
                 'nome_tela' => 'consumo de materiais',
                 'pedidos' => $pedidos,
        //         'materiais' => $dados_materiais,
        //         'calculos' => [],
        //         'totais' => $totais,
        //         'request' => $request,
        //         'imprimir' => $imprimir,
        //         'AllStatus' => (new PedidosController)->getAllStatus(),
		// 		'rotaAlterar' => 'consumo-materiais-detalhes'
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
  

}

class Empacotamento {
    private $chapa;
    private $pecas;
    private $chapaOrganizada;

    public function __construct($chapa, $pecas) {
        $this->chapa = $chapa;
        $this->pecas = $this->ordenaPecas($pecas);
        $this->chapaOrganizada = [
            'pecas' => [],
            'width' => $chapa['sheetWidth'],
            'height' => $chapa['sheetHeight'],
            'pecasUsadas' => [],
        ];        
    }

    public static function ordenaPecas($pecas) {
        // Ordena as peças em ordem decrescente de largura (width) e altura (height)
        // sort($pecas);
        // usort($pecas, function($a, $b) {
        //     $areaA = $a['width'] * $a['height'];
        //     $areaB = $b['width'] * $b['height'];
        //     return $areaB - $areaA;
        // });

        return $pecas;
    }
    public function organizarPecas() {
        foreach ($this->pecas as $peca) {
            for ($i = 0; $i < $peca['quantidade']; $i++) {
                $pecaInserida = false;
                
                // Tenta inserir a peça em diferentes orientações (horizontal e vertical)
                for ($j = 0; $j < 2; $j++) {
                    $width = $j == 0 ? $peca['width'] : $peca['height'];
                    $height = $j == 0 ? $peca['height'] : $peca['width'];
                    // Procura uma posição na chapa onde a peça possa ser inserida
                    for ($x = 0; $x <= $this->chapaOrganizada['width'] - $width; $x++) {
                        for ($y = 0; $y <= $this->chapaOrganizada['height'] - $height; $y++) {
                            $posicaoOcupada = false;

                            // Verifica se a posição está ocupada por outra peça
                            foreach ($this->chapaOrganizada['pecas'] as $pecaNaChapa) {
                                if (!(
                                    $x + $width <= $pecaNaChapa['x'] ||
                                    $y + $height <= $pecaNaChapa['y'] ||
                                    $x >= $pecaNaChapa['x'] + $pecaNaChapa['width'] ||
                                    $y >= $pecaNaChapa['y'] + $pecaNaChapa['height']
                                )) {
                                    $posicaoOcupada = true;
                                    break;
                                }
                            }

                            // Se a posição estiver livre, adiciona a peça na chapa
                            if (!$posicaoOcupada) {

                               
                                $size = $width.'x'.$height;
                                $this->chapaOrganizada['pecasUsadas'][$size][] = $size;

                                $this->chapaOrganizada['pecas'][] = [
                                    'x' => $x,
                                    'y' => $y,
                                    'width' => $width,
                                    'height' => $height
                                ];
                                $pecaInserida = true;
                                break 3;
                            }
                        }
                    }
                }

                // Se não for possível inserir a peça em nenhuma orientação, encerra o loop
                if (!$pecaInserida) {
                    break;
                }
            }
        }
    }

    public function getChapaOrganizada() {
        return $this->chapaOrganizada;
    }
}
