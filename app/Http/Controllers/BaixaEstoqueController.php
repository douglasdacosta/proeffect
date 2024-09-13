<?php

namespace App\Http\Controllers;

use App\Models\Estoque;
use App\Models\Funcionarios;
use App\Models\Pessoas;
use Illuminate\Http\Request;

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

    public function telaBaixaEstoque(Request $request) {


        $id = $request->input('id');
        $senha = $request->input('senha');

        $funcionarios = new Funcionarios();
        $funcionarios = $funcionarios->where(column: 'status', operator: '=', value: 'A');
        $funcionarios = $funcionarios->get();

        foreach($funcionarios as $funcionario) {
            $array_senha_producao[] = $funcionario->senha;
        }

        if(!empty($request->input()) && !in_array($senha, $array_senha_producao)) {
            $dados = [
                'pedidos' => '',
                'status' => '',
                'mensagem' => 'Estoque não encontrado/senha incorreta',
                'materiais' => [],
                'fornecedores' => [],
                'estoque' =>[]
            ];
            return view('tela_baixa_estoque', $dados);
        }
            $estoque = new Estoque();

            $estoqueController = new EstoqueController();
            $fornecedores = $estoqueController->getFornecedores(true);
            $array_fornecedores = [];
            foreach ($fornecedores as $key => $fornecedor) {
                $array_fornecedores[$fornecedor['id']]['nome_cliente'] = $fornecedor['nome_cliente'];
            }


            $fichaTecnicasController = new FichatecnicaController();
            $Materiais = $fichaTecnicasController->getAllMateriais();
            $array_materiais=[];
            foreach ($Materiais as $key => $material) {
                $array_materiais[$material->id]['material'] = $material->material;
            }

            $estoque = $estoque->where(column: 'status', operator: '=', value: 'A')
                        ->where(column: 'data_baixa', operator: '=', value: null)
                        ->where(column: 'id', operator: '=', value: $id)
                        ->get();
            $mensagem = '';

            if(($estoque->isEmpty())) {
                $mensagem = 'Estoque não encontrado/senha incorreta';
            }
            $dados = [
                'pedidos' => '',
                'status' => '',
                'mensagem' => $mensagem ,
                'materiais' => $array_materiais,
                'fornecedores' => $array_fornecedores,
                'estoque' =>$estoque
            ];

        return view('tela_baixa_estoque', $dados);
    }


    public function baixarEstoque(Request $request) {

        $id = $request->input('id');

        $estoque = Estoque::find($id);

        if ($estoque) {
            $estoque->data_baixa = now();
            $estoque->save();
        } else {
            return redirect()->back()->with('error', 'Estoque não encontrado');
        }

        return true;
    }
}
