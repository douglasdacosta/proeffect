<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\CaixasPedidos;
use Illuminate\Support\Facades\DB;

class CaixasController extends Controller
{
    public function index()
    {
        // return view('');
    }

    public function buscarCaixas(Request $request)
    {
        try {

                $caixas_pedidos = DB::table('caixas_pedidos')
                    ->join('materiais', 'materiais.id', '=', 'caixas_pedidos.material_id')
                    ->select('caixas_pedidos.quantidade', 'caixas_pedidos.a', 'caixas_pedidos.l', 'caixas_pedidos.c', 'caixas_pedidos.peso', 'materiais.material')
                    ->where('pedidos_id','=',$request->input('id'));
                $caixas_pedidos = $caixas_pedidos->get();

                return response($caixas_pedidos, 200);

            } catch (\Throwable $th) {
                info($th);
                return response($th, 501);
            }
    }
}
