<?php

namespace App\Http\Controllers;

use App\Models\Pedidos;
use Illuminate\Http\Response;

class HomeController extends Controller
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function getProducao()
    {
        $pedidos = new Pedidos();
        $pedidos = $pedidos::with('tabelaStatus')->where('status', '=', '1')->get();
        $totais =[];
        $total_pedidos = 0;
        foreach ($pedidos as $key => $pedido) {
            $total_pedidos++;
            $totais[$pedido->tabelaStatus->nome]['qtde'] = (isset($totais[$pedido->tabelaStatus->nome]['qtde']) ? $totais[$pedido->tabelaStatus->nome]['qtde'] : 0) + 1;
        }

        $data  = [
            'total_pedidos' => $total_pedidos,
            'totais' => $totais,
        ];
        return response($data);
    }


}
