<?php

namespace App\Http\Controllers;

use App\Models\Estoque;
use App\Models\Pedidos;
use App\Models\Pessoas;
use Illuminate\Http\Request;

class AjaxController extends Controller
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

    function ajaxInventario(Request $request){
        try{
            $estoque = new Estoque();
            $estoque = Estoque::find($request->input('id'));

            // se inventario for igual a true, então o valor de inventário é 1
            $estoque->inventario = ($request->input('inventario') == 'true' ? 1 : 0);
            $estoque->save();
            return true;
        }catch(\Throwable $th){
            info(print_r($th->getMessage(), true));
            return false;
        }
    }

    function ajaxLimparInventario(){
        try{
            $estoque = new Estoque();
            $estoque->where('inventario', '!=', null)->update(['inventario' => null]);
            $estoque->save();
            return true;
        }catch(\Throwable $th){
            info(print_r($th->getMessage(), true));
            return false;
        }
    }
    

    function ajaxFaturado(Request $request){
        try{
            $pedidos = new Pedidos();
            $pedidos = Pedidos::find($request->input('id'));

            // se faturado for igual a true, então o valor de inventário é 1
            $pedidos->faturado = ($request->input('status_faturado') == '1' ? 0 : 1);
            $pedidos->save();
            return true;
        }catch(\Throwable $th){
            info(print_r($th->getMessage(), true));
            return false;
        }
    }

    function ajaxLimparFaturado(){
        try{
            $pedidos = new Pedidos();
            $pedidos->where('faturado', '!=', null)->update(['faturado' => null]);
            $pedidos->save();
            return true;
        }catch(\Throwable $th){
            info(print_r($th->getMessage(), true));
            return false;
        }
    }


    function ajaxWhatsappStatus(Request $request){
        try{
            $pessoas = new Pessoas();
            $pessoas = Pessoas::find($request->input('id'));

            // se faturado for igual a true, então o valor de inventário é 1
            $pessoas->whatsapp_status = ($request->input('whatsapp_status') == '1' ? 0 : 1);
            $pessoas->save();
            return true;
        }catch(\Throwable $th){
            info(print_r($th->getMessage(), true));
            return false;
        }
    }
}
