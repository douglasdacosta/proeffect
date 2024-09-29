<?php

namespace App\Http\Controllers;

use App\Models\Estoque;
use App\Models\FilaImpressao;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;

class FilaImpressaoController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    public function imprimirTagEstoque(Request $request) {

        if(!$this->checkAcesso($request)) {
            return response('Erro na identificação', 401);
        }

        $filas = FilaImpressao::where('impresso', '=', 0)->get();
        $array = [];
        foreach ($filas as $key => $fila) {

            $estoque = DB::table('estoque')
                ->join('materiais', 'materiais.id', '=', 'estoque.material_id')
                ->join('pessoas', 'pessoas.id', '=', 'estoque.fornecedor_id')
                ->select('estoque.data', 'estoque.lote', 'pessoas.nome_cliente', 'materiais.codigo')
                ->where('estoque.id', '=', $fila->estoque_id)
                ->orderby('estoque.data', 'desc')
                ->first();

            $nome_cliente = $estoque->nome_cliente;
            $nome_cliente = implode(' ', array_slice(explode(' ', $nome_cliente), 0, 2));

            $array[] = [
                'material' => $estoque->codigo,
                'fornecedor' => $nome_cliente,
                'estoque_id' => $estoque->lote,
                'qtde' => $fila->qtde_etiqueta,
                'data' => $fila->data_impresso ? Carbon::parse($fila->data_impresso)->format('d/m/Y') : $fila->data_impresso,
            ];

            $FilaImpressao = new FilaImpressao();
            $FilaImpressao->where('estoque_id', '=', $fila->estoque_id)
            ->update(['impresso' => 1]);

        }

        return $array;
    }

    public function incluirFilaImpressao(Request $request) {

        $fila = new FilaImpressao();
        $fila->estoque_id = $request->input('id');
        $fila->data_impresso = date('Y-m-d');
        $fila->qtde_etiqueta = $request->input('qtde_etiqueta');
        $fila->impresso = 0;
        $fila->save();

        return response('Fila incluída com sucesso!');

    }

    private function checkAcesso(Request $request){
        $dados = $request->input();
        if($dados['TOKEN'] != env('TOKEN_API')){
            return false;
        }
        if($dados['LOGIN'] != env('LOGIN_API')){
            return false;
        }
        if($dados['SENHA'] != env('SENHA_API')){
            return false;
        }
        return true;

    }

}
