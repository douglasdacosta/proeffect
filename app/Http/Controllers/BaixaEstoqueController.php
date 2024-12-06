<?php

namespace App\Http\Controllers;

use App\Models\Estoque;
use App\Models\Funcionarios;
use App\Models\HistoricosEstoque;
use App\Models\LoteEstoqueBaixados;
use App\Models\Pessoas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $nome = '';
        $funcionarios = new Funcionarios();
        $funcionarios = $funcionarios->where(column: 'status', operator: '=', value: 'A');

        $funcionarios = $funcionarios->where(column: 'senha', operator: '=', value: $senha);

        $funcionarios = $funcionarios->get();

        if(!empty($funcionarios[0]->senha))  {
            $array_senha_producao = $funcionarios[0]->senha;
            $nome = $funcionarios[0]->nome;
        }
        $mensagem_alerta_estoque =[
            "mensagem" => "",
            "alerta" => false
        ];

        if(!empty($request->input('senha')) && empty($array_senha_producao)) {
            $dados = [
                'pedidos' => '',
                'status' => '',
                'mensagem' => 'Estoque não encontrado/senha incorreta',
                'usuario' => '',
                'materiais' => [],
                'mensagem_alerta_estoque' => $mensagem_alerta_estoque,
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

            $estoque = DB::select(DB::raw("SELECT
                                            *
                                            FROM
                                                estoque A
                                            WHERE
                                                A.status = 'A'
                                                AND A.lote = '$id'
                                                AND (select
                                                    count(1)
                                                    from
                                                        lote_estoque_baixados
                                                    where
                                                        estoque_id = A.id) < (A.qtde_por_pacote+A.qtde_por_pacote_mo) "
            ));


            $mensagem_alerta_estoque =[
                "mensagem" => "",
                "alerta" => false
            ];

            if(!empty($estoque)) {

                $estoqueTodos = new Estoque();
                $estoqueTodos = $estoqueTodos->where('material_id', '=', $estoque[0]->material_id)
                ->where('status_estoque', '=', 'A')
                ->where('status', '=', 'A');
                $estoqueTodos = $estoqueTodos->orderBy('data', 'asc')->get();



                if($estoque[0]->lote != $estoqueTodos[0]->lote) {

                    $mensagem_alerta_estoque =[
                        "mensagem" => "Atenção: existe outro lote ".$estoqueTodos[0]->lote." anterior com pacotes pendentes de baixa.",
                        "alerta" => true
                    ];

                }
            }


            $mensagem = '';
            if (empty($estoque) && !empty($request->input('id'))) {
                $mensagem = 'Estoque não encontrado/senha incorreta';
            }

            $dados = [
                'pedidos' => '',
                'status' => '',
                'mensagem' => $mensagem ,
                'materiais' => $array_materiais,
                'usuario' => $nome,
                'mensagem_alerta_estoque' => $mensagem_alerta_estoque,
                'fornecedores' => $array_fornecedores,
                'estoque' =>$estoque
            ];

        return view('tela_baixa_estoque', $dados);
    }


    public function baixarEstoque(Request $request) {

        try {
            $id = DB::transaction(function () use ($request) {
                $estoque = new Estoque();
                $estoque = $estoque->where('id', '=', $request->input('id'))->get();


                $estoqueTodos = new Estoque();
                $estoqueTodos = $estoqueTodos->where('material_id', '=', $estoque[0]->material_id)
                                    ->where('status_estoque', '=', 'A')
                                    ->where('status', '=', 'A');
                $estoqueTodos = $estoqueTodos->orderBy('data', 'asc')->get();

                if($estoque[0]->lote != $estoqueTodos[0]->lote) {

                    $estoqueAltera = new Estoque();
                    $estoqueAltera = $estoqueAltera->where('id', '=', $estoque[0]->id)
                                    ->update(['alerta_baixa_errada' => 1]);
                }

                $LoteEstoqueBaixados = new  LoteEstoqueBaixados();
                $LoteEstoqueBaixados->estoque_id=$request->input('id');
                $LoteEstoqueBaixados->data_baixa = now();
                $LoteEstoqueBaixados->save();

                $usuario = $request->input('usuario');

                $historico = "Retirada de 1 de pacote do estoque - tela de baixa de estoque - por $usuario";
                $historico_estoque = new HistoricosEstoque();
                $historico_estoque->estoque_id = $request->input('id');
                $historico_estoque->historico = $historico;
                $historico_estoque->status = 'A';
                $historico_estoque->save();

            });

            return true;

        } catch (\Exception $e) {
            info('erro para Baixar -> '.$e);
            return false;
        }

    }

    public function alterarEstoque(Request $request) {
        try{

            $id = DB::transaction(function () use ($request) {
                $qtde = $request->input('qtde');

                $name = auth()->user()->name;
                if($request->input('acao_estoque') == 'adicionar') {
                    $historico = "Devolução de $qtde de pacotes para estoque - por $name";
                } else {
                    $historico = "Retirada de $qtde de pacotes do estoque - por $name";
                }

                $historico_estoque = new HistoricosEstoque();
                $historico_estoque->estoque_id = $request->input('id');
                $historico_estoque->historico = $historico;
                $historico_estoque->status = 'A';
                $historico_estoque->save();

                for ($i=0; $i < $qtde; $i++) {
                    if($request->input('acao_estoque') == 'adicionar') {
                        $LoteEstoqueBaixados = new  LoteEstoqueBaixados();
                        $LoteEstoqueBaixados->where('estoque_id', '=', $request->input('id'))->orderBy('id')->first()->delete();
                    } else {
                        $LoteEstoqueBaixados = new  LoteEstoqueBaixados();
                        $LoteEstoqueBaixados->estoque_id=$request->input('id');
                        $LoteEstoqueBaixados->data_baixa = now();
                        $LoteEstoqueBaixados->save();
                    }
                }
            });

            return true;

        } catch (\Exception $e) {
            info('erro para Baixar -> '.$e);
            return false;
        }




    }
}
