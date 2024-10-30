<?php

namespace App\Http\Controllers;

class CalculadoraPlacasController extends Controller{
    private $pecas_necessarias;
    private $chapa;
    private $perda;
    private $adicao_w;
    private $adicao_h;

    public function __construct($pecas_necessarias, $chapa) {

        $this->pecas_necessarias = $pecas_necessarias;
        $this->chapa = $chapa;
        $this->perda = env('PERCENTUAL_PERDA_CHAPA');
        $this->adicao_w = env('PERCENTUAL_x');
        $this->adicao_h = env('PERCENTUAL_h');
    }

    public function calcularNumeroPlacas() {
        $total_area_peca = 0;
        foreach ($this->pecas_necessarias as $peca) {

            $total_area_peca += $peca['quantidade'] * ($peca['width'] + $this->adicao_w ) * ($peca['height'] + $this->adicao_h ) ;
        }

        $area_chapa_util = ($this->chapa['sheetWidth'] * $this->chapa['sheetHeight']) * (1 - $this->perda);

        if($total_area_peca == 0 || $area_chapa_util == 0) {
            return 0;
        }
        $numero_placas = number_format(round($total_area_peca / $area_chapa_util, 3, PHP_ROUND_HALF_UP), '3', '.', '');
        //$numero_placas = round($total_area_peca / $area_chapa_util, 3, PHP_ROUND_HALF_UP);

        return $numero_placas;
    }
}
