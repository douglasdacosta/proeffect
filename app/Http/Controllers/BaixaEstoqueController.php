<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Estoque;
use App\Providers\DateHelpers;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\OrcamentosController;
use App\Models\Pessoas;

class BaixaEstoqueController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    public function telaBaixaEstoque() {
        $estoque = new Estoque();
        $dados = [
            'pedidos' => '',
            'status' => '',
            'mensagem' => ''
        ];
        return view('manutencao_producao', $dados);
    }
}
