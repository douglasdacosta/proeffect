<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PainelController;
use App\Models\CaixasPedidos;
use App\Models\Funcionarios;
use App\Models\HistoricosEtapas;
use App\Models\Materiais;
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

    public function ajaxAlterarPedidoCaixa(Request $request) {

        try {

            $texto = DB::transaction(function () use ($request) {
                $pedido_id = $request->input('id');
                $dados_caixas = json_decode($request->input('dados_caixa'));
                foreach ($dados_caixas as $dados_caixa) {
                    $pedidos_caixas = new CaixasPedidos();
                    $pedidos_caixas->pedidos_id = $pedido_id;
                    $pedidos_caixas->material_id = $dados_caixa->material;
                    $pedidos_caixas->quantidade = $dados_caixa->cx_quantidade;
                    $pedidos_caixas->a = $dados_caixa->cx_a;
                    $pedidos_caixas->l = $dados_caixa->cx_b;
                    $pedidos_caixas->c = $dados_caixa->cx_c;
                    $pedidos_caixas->peso  = $dados_caixa->cx_peso;
                    $pedidos_caixas->save();
                }



                return response('Pedido alterado com sucesso!', 200);

            });

        } catch (\Throwable $th) {
            info($th);
            return response($th->getMessage(), 501);
        }

        return response($texto, 200);
    }

    public function ajaxAlterarPedido(Request $request) {

        try {

        $texto = DB::transaction(function () use ($request) {
            $finalizar_etapa = false;
            $HistoricosEtapas = new HistoricosEtapas();
            $funcionarios = new Funcionarios();
            $pedidos = new Pedidos();
            $pedidosController = new PedidosController();

            if($request->input('id')) {
                $pedidos= $pedidos::find($request->input('id'));
                $status_anterior = $pedidos->status_id;
                $select_tipo_manutencao = $request->input('select_tipo_manutencao');
                $select_etapa_manutencao= $request->input('select_etapa_manutencao');
                $select_motivo_pausas= $request->input('select_motivo_pausas');
                $texto_quantidade= $request->input('texto_quantidade');

                //verifica se é termino
                if($select_etapa_manutencao == 4){

                    #verifica se falta algum início sem término para poder mudar os status
                    $etapasIniciadas = DB::table('historicos_etapas')
                    ->select('historicos_etapas.id')
                    ->where('historicos_etapas.pedidos_id','=',$request->input('id'))
                    ->where('historicos_etapas.etapas_pedidos_id','=',1)
                    ->get()->toArray();

                    $etapasFinalizadas = DB::table('historicos_etapas')
                    ->select('historicos_etapas.id')
                    ->where('historicos_etapas.pedidos_id','=',$request->input('id'))
                    ->where('historicos_etapas.etapas_pedidos_id','=',4)
                    ->get()->toArray();

                    #verifica se falta apenas 1 finalização para mudar o status
                    if((count($etapasIniciadas) - count($etapasFinalizadas)) <= 1) {

                        $finalizar_etapa = true;
                        $pedidos->status_id = $request->input('status');

                     }

                }
                $pedidos->save();

                $senha= $request->input('senha');
                $funcionarios = $funcionarios->where('senha', '=', $senha)->get();

                $HistoricosEtapas->pedidos_id = $request->input('id');
                $HistoricosEtapas->status_id = $request->input('atualStatus');
                $HistoricosEtapas->etapas_pedidos_id =$select_etapa_manutencao;
                $HistoricosEtapas->funcionarios_id = $funcionarios[0]->id;
                $HistoricosEtapas->select_tipo_manutencao = $select_tipo_manutencao;
                $HistoricosEtapas->select_motivo_pausas = $select_motivo_pausas;
                $HistoricosEtapas->texto_quantidade = $texto_quantidade;

                $HistoricosEtapas->save();

                $mostrar_caixa = 0;
                if($finalizar_etapa) {
                    $pedidosController->historicosPedidos($request->input('id'), $request->input('status'));
                    $pedidosController->filaAlerta($request->input('id'),$status_anterior,$request->input('status'));
                    if($request->input('status') ==9  ) {
                        $mostrar_caixa = 1;
                    }
                }

                $retorno = json_encode([
                    'mensagem' => 'Pedido alterado com sucesso!',
                    'mostrar_caixa' => $mostrar_caixa
                ]);

                return response($retorno, 200);
            }

            return response('Erro para salvar', 501);
        });

        return $texto;
        } catch (\Throwable $th) {
            info($th);
            return response('Erro para salvar', 501);
        }

    }

    public function pesquisar(Request $request){
        $os = $request->input('os');
        $senha = $request->input('senha');
        $mensagem='';
        $dados_historicos_etapas = [];
        $pedidos=$dados_status=[];

        $painelController = new PainelController();
        $materiais = new Materiais();
        $funcionarios = new Funcionarios();
        $funcionarios = $funcionarios->where('status', '=', 'A');
        $funcionarios = $funcionarios->get();

        foreach($funcionarios as $funcionario) {
            $array_senha_producao[] = $funcionario->senha;
        }

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

            if(!empty($pedidos)) {
                $status = new Status();
                $status = $status->orderby('id')->get();

                foreach($status as  $stat) {
                    $id = $stat->id;
                    $dados_status[$id]['id'] = $stat->id;
                    $dados_status[$id]['nome'] = $stat->nome;
                }

                $status = new Status();
                $status = $status->orderby('id')->get();

                $pedidos =$painelController->buscaDadosEtapa($pedidos);
            }

        }

        $materiais = $materiais->where('material','LIKE','%CX%')->get();
        $dados = [
            'pedidos' => $pedidos,
            'status' => $dados_status,
            'senha' => $senha,
            'mensagem'=>'',
            'materiais' => $materiais,
            'motivosPausa' => $painelController->getMotivosPausa(),
        ];

        return view('manutencao_producao', $dados);

    }

    public function buscaEtapas($pedidos){
        $dados_historicos_etapas = [];
        foreach($pedidos as  $pedido) {
            $historicos_etapas = DB::table('historicos_etapas')
            ->select('historicos_etapas.*', 'pedidos.status_id', 'etapas_pedidos.nome as nome_etapa')
            ->join('pedidos', 'pedidos.id', '=', 'historicos_etapas.pedidos_id')
            ->join('etapas_pedidos', 'etapas_pedidos.id', '=', 'historicos_etapas.etapas_pedidos_id')
            ->where('pedidos_id','=',$pedido->id)
            ->orderBy('historicos_etapas.created_at', 'desc')
            ->limit(1)
            ->get();

            foreach($historicos_etapas as  $historicos_etapa) {
                 $dados_historicos_etapas[$pedido->id][$historicos_etapa->status_id][] = [
                    "nome_etapa" => $historicos_etapa->nome_etapa,
                    "etapas_pedidos_id" => $historicos_etapa->etapas_pedidos_id,
                    "funcionarios_id" => $historicos_etapa->funcionarios_id,
                ];
            }
        }
        return $dados_historicos_etapas;
    }
}
