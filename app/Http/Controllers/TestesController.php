<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Cache;

class TestesController extends BaseController
{
    public function index(){




        $pecas_necessarias = [
            ['quantidade' => 2, 'width' => 150 , 'height' => 611],
            ['quantidade' => 8, 'width' => 61 , 'height' => 61],
        ];
        $chapa = [
            'sheetWidth' => 1200,
            'sheetHeight' => 960,
        ];



        $quantidade_chapas = 0;
        while(count($pecas_necessarias)>0) {
            $quantidade_chapas ++;
            $empacotamento = new Empacotamento($chapa, $pecas_necessarias);
            $empacotamento->organizarPecas();
            $resultado = $empacotamento->getChapaOrganizada();

            $usados = [];
            foreach($resultado['pecasUsadas'] as $tamanho => $dados){
                $usados[$tamanho] = count($dados);
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
            \Log::info(print_r(' ----------------------------------------------------------------------', true));
            // \Log::info(print_r($usados, true));
            \Log::info(print_r($pecas_necessarias, true));


        }

        \Log::info(print_r('Chapas totais '. $quantidade_chapas, true));









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
