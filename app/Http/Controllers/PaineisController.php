<?php

namespace App\Http\Controllers;

use App\Models\Pedidos;
use Illuminate\Http\Response;

class PaineisController extends Controller
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
        return view('painel');
    }


    public function paineisUsinagem(){
        return view('paineis.painel_usinagem');
    }
    public function paineisMontagem(){
        return view('paineis.painel_montagem');
    }
    public function paineisAcabamento(){
        return view('paineis.painel_acabamento');
    }
    public function paineisInspecao(){
        return view('paineis.painel_inspecao');
    }
    public function paineisExpedicao(){
        return view('paineis.painel_expedicao');
    }






}
