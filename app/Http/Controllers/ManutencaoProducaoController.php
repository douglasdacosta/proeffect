<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Pedidos;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManutencaoProducaoController extends Controller
{

    /*
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    public function index() {

        $dados = [
            'pedidos' => '',
            'status' => '',
            'mensagem' => ''
        ];
        return view('manutencao_producao', $dados);
    }

    public function ajaxAlterarPedido(Request $request) {
        $pedidos = new Pedidos();
        $pedidosController = new PedidosController();

        if($request->input('id')) {
            $pedidos= $pedidos::find($request->input('id'));
            $status_anterior = $pedidos->status_id;
            $pedidos->status_id = $request->input('status');
            $pedidos->save();

            $pedidosController->historicosPedidos($request->input('id'), $request->input('status'));

            $pedidosController->filaAlerta($request->input('id'),$status_anterior,$request->input('status'));

            return response('Pedido alterado com sucesso!', 200);
        }

        return response('Erro para salvar', 501);
    }

    public function pesquisar(Request $request){
        $os = $request->input('os');
        $senha = $request->input('senha');
        $mensagem='';
        $pedidos=$dados_status=[];
        $array_senha_producao = explode(',',env('SENHA_PRODUCAO'));
        if(!empty($request->input()) && !in_array($senha, $array_senha_producao)) {
            $dados = [
                'pedidos' => '',
                'status' => '',
                'mensagem' => 'OS não encontrada/senha incorreta'
            ];

            return view('manutencao_producao', $dados);

        }
        if(!empty($os)) {

            $pedidos = DB::table('pedidos')
            ->join('status', 'pedidos.status_id', '=', 'status.id')
            ->join('ficha_tecnica', 'ficha_tecnica.id', '=', 'pedidos.fichatecnica_id')
            ->join('pessoas', 'pessoas.id', '=', 'pedidos.pessoas_id')
            ->select('pedidos.*', 'ficha_tecnica.ep', 'pessoas.nome_cliente', 'status.nome as nomeStatus' , 'status.id as id_status')
            ->orderby('status_id', 'desc')
            ->orderby('data_entrega');

            $pedidos = $pedidos->where('os', '=', $os)->get();
            info($pedidos);
            if(!empty($pedidos)) {
                $status = new Status();
                $status = $status->orderby('id')->get();

                foreach($status as  $stat) {
                    $id = $stat->id;
                    $dados_status[$id]['id'] = $stat->id;
                    $dados_status[$id]['nome'] = $stat->nome;
                }
            }

        }
        info($dados_status);
        $dados = [
            'pedidos' => $pedidos,
            'status' => $dados_status,
            'mensagem'=>''
        ];

        return view('manutencao_producao', $dados);

    }
}
