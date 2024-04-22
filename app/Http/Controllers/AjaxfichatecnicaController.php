<?php

namespace App\Http\Controllers;

use App\Models\Fichastecnicas;
use Illuminate\Http\Request;
use App\Models\Materiais;
use PhpParser\Node\Expr\Cast\Array_;
use PhpParser\Node\Expr\Cast\Object_;

class AjaxfichatecnicaController extends Controller
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


    public function checkEpExistente(Request $request) {

        $fichaTecnica = new Fichastecnicas();

        $ep = (new FichatecnicaController)->trataEP($request);

        $fichaTecnica = $fichaTecnica->where('ep', '=', $ep)->get();

        if(!empty($fichaTecnica[0]->ep)){
            return response('O EP ('.$ep.') já existe, tente novamente!');
        }
        return response(null);
    }

    public function calculaUsinagem(Request $request) {
        $total = '00:00';
        $arra_type = ['+','/'];

        $calctype = $request->input('calc-type');

        if(!in_array($calctype, $arra_type)) {
            return response($total);
        }

        $Pedidos = New PedidosController();



        if($calctype == '+'){
            $calcval1 = '00:'.$request->input('calc-val1');
            $calcval2= '00:'.$request->input('calc-val2');
            $total = $Pedidos->somarHoras($calcval1, $calcval2);
            $total =  $this->converterParaMinutosFormatado($total);
        }

        if($calctype == '/'){
            $calcval1 = $request->input('calc-val1');
            $calcval2= $request->input('calc-val2');
            info($calcval1);
            info($calcval2);
            $total = $Pedidos->dividirTempoPorValor($calcval1, $calcval2);
            info($total);
        }


        return response($total);
    }

    function converterParaMinutosFormatado($tempo) {
        // Separar as partes do tempo
        list($horas, $minutos, $segundos) = explode(':', $tempo);

        // Converter horas e minutos para minutos totais
        $total_minutos = $horas * 60 + $minutos;

        // Retornar o tempo formatado como minutos:segundos
        return sprintf('%d:%02d', $total_minutos, $segundos);
    }


    /**
     * Create a new controller instance.
     *
     *
     */
    public function buscarMateriais(Request $request) {

        $materiais = new Materiais();

        if($request->input('id')) {
            $material= $materiais->where('id', '=', $request->input('id'))->get();
            return response($material);
        }

        return response(null);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getAllMateriais() {
        $Materiais = new Materiais();
        return $Materiais->where('status', '=', 'A')->get();

    }
}
