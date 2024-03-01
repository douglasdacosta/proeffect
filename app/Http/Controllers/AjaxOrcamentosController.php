<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Materiais;
use App\Providers\DateHelpers;
use App\Http\Controllers\PedidosController;
use PhpParser\Node\Expr\Cast\Array_;
use PhpParser\Node\Expr\Cast\Object_;

class AjaxOrcamentosController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Create a new controller instance.
     *
     *
     */
    public function ajaxCalculaOrcamentos(Request $request) {

        try {
            $dados = $request->input('dados');
            $calculo_hora_fresa = DateHelpers::formatFloatValue($request->input('calculo_hora_fresa'));

            $pedidos = new PedidosController();
            $Total_mo=$Total_mp=$Total_ci=0;
            foreach ($dados as $key => &$dado) {

                $blank = json_decode($dado[0]);
                $tmp = json_decode($dado[1]);
                $qtde = json_decode($dado[3]);
                $qtdeCH = json_decode($dado[5]);
                $valor_chapa = json_decode($dado[8]);
                $MO = json_decode($dado[9]);
                $MP = json_decode($dado[10]);
                
                $val_chapa = DateHelpers::formatFloatValue($valor_chapa->{"valor_chapa_$key"});
                $qtde_CH = $qtdeCH->{"qtdeCH_$key"};
                $qtde_ = $qtde->{"qtde_$key"};

                if($blank->{"blank_$key"} != '') {
                    
                    $MP->{"valorMP_$key"} = $val_chapa/$qtde_CH*$qtde_;                
                    $dado[10] = json_encode(["valorMP_$key" => number_format($MP->{"valorMP_$key"}, 2, ',','')]);
                    
                    $tempo = $pedidos->multiplyTimeByInteger('00:'.$tmp->{"tmp_$key"},  $qtde_);

                    $MO->{"valorMO_$key"} = $this->calcularValor($calculo_hora_fresa, $tempo);
                    

                    $dado[9] = json_encode(["valorMO_$key" => $MO->{"valorMO_$key"}]);
                    $Total_mo = $Total_mo + DateHelpers::formatFloatValue($MO->{"valorMO_$key"});
                    
                } 
                else {
                    $MP->{"valorMP_$key"} = $val_chapa*$qtde_CH;
                    $dado[10] = json_encode(["valorMP_$key" => number_format($MP->{"valorMP_$key"}, 2, ',','')]);
                    $dado[9] =   json_encode(["valorMO_$key" => '']);
                }

                $Total_mp = $Total_mp + (($MP->{"valorMP_$key"} !='') ? $MP->{"valorMP_$key"} : 0);
                
                
            }
            
            $Total_ci = $Total_mp + $Total_mo;
            $Total_mp_2 = $Total_mp * 0.37;
            $desc_10_1 = $Total_ci * 1.66;
            $desc_20_1 = $Total_ci * 1.50;
            $desc_30_1 = $Total_ci * 1.35;
            $desc_40_1 = $Total_ci * 1.25;
            $desc_50_1 = $Total_ci * 1.16;    

            $totais = [
                'subTotalMO' => number_format($Total_mo, 2, ',',''),
                'subTotalMP' => number_format($Total_mp, 2, ',',''),
                'subTotalCI'=> number_format($Total_ci, 2, ',',''),
                'desc_10_1' => number_format($desc_10_1, 2, ',',''),
                'desc_20_1' => number_format($desc_20_1, 2, ',',''),
                'desc_30_1' => number_format($desc_30_1, 2, ',',''),
                'desc_40_1' => number_format($desc_40_1, 2, ',',''),
                'desc_50_1' => number_format($desc_50_1, 2, ',',''),
                'desc_10_2' => number_format($Total_mp_2, 2, ',',''),
                'desc_20_2' => number_format($Total_mp_2, 2, ',',''),
                'desc_30_2' => number_format($Total_mp_2, 2, ',',''),
                'desc_40_2' => number_format($Total_mp_2, 2, ',',''),
                'desc_50_2' => number_format($Total_mp_2, 2, ',',''),
                'desc_10_total' => number_format($desc_10_1 + $Total_mp_2, 2, ',',''),
                'desc_20_total' => number_format($desc_20_1 + $Total_mp_2, 2, ',',''),
                'desc_30_total' => number_format($desc_30_1 + $Total_mp_2, 2, ',',''),
                'desc_40_total' => number_format($desc_40_1 + $Total_mp_2, 2, ',',''),
                'desc_50_total' => number_format($desc_50_1 + $Total_mp_2, 2, ',',''),
            ];
            return response ([0 => $dados, 1 => $totais]);

        } catch (\Throwable $th) {
            return response($th);
        }


    }


    function calcularValor($valorHora, $tempoTrabalhado) {
        // Convertendo o tempo trabalhado para minutos
        $tempoTrabalhado = '00:'.$tempoTrabalhado;
        list($horas, $minutos, $segundos) = explode(':', $tempoTrabalhado);
        $tempoTotalMinutos = ($horas * 60 * 60) + ($minutos * 60) + $segundos;

        // Calculando o custo total
        $custoTotal = ($tempoTotalMinutos / 60) / 60 * $valorHora;

        return number_format($custoTotal, 2, ',','');
    }

}
