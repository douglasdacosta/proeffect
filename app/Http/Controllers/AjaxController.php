<?php

namespace App\Http\Controllers;

use App\Models\Estoque;
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
            \Log::info(print_r($th->getMessage(), true));
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
            \Log::info(print_r($th->getMessage(), true));
            return false;
        }
    }


}
