<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PainelController;
use App\Models\CaixasPedidos;
use App\Models\EtapasAlteracao;
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
            // info($request->input());


            $texto = DB::transaction(function () use ($request) {
                $finalizar_etapa = false;
                $HistoricosEtapas = new HistoricosEtapas();
                $funcionarios = new Funcionarios();
                $pedidos = new Pedidos();
                $ModelEtapasAlteracao = new EtapasAlteracao();
                $pedidosController = new PedidosController();

                if($request->input('id')) {
                    $pedidos= $pedidos::find($request->input('id'));
                    $status_anterior = $pedidos->status_id;
                    $etapasalteracao = $request->input('etapasalteracao');
                    $select_tipo_manutencao = $request->input('select_tipo_manutencao');
                    $select_etapa_manutencao= $request->input('select_etapa_manutencao');
                    $select_motivo_pausas= $request->input('select_motivo_pausas');
                    $texto_quantidade= $request->input('texto_quantidade');
                    $necessitaMontagemExtra= $request->input('necessitaMontagemExtra');
                    $numero_maquina_iniciando= $request->input('numero_maquina_iniciando');

                    $etapasIniciadas = DB::table('historicos_etapas')
                        ->select('historicos_etapas.id')
                        ->where('historicos_etapas.pedidos_id','=',$request->input('id'))
                        ->where('historicos_etapas.etapas_pedidos_id','=',1)
                        ->where('historicos_etapas.status_id' ,'=', $request->input('atualStatus'))
                        ->get()->toArray();

                    //verifica se não encontrou nenhuma etapa iniciada
                    if(empty($etapasIniciadas) && $select_etapa_manutencao != 1) {
                        throw new \Exception('A etapa não foi iniciada, entre em contato com a administração.');
                    }

                    $senha= $request->input('senha');
                    $funcionarios = $funcionarios->where('senha', '=', $senha)->get();

                    $etapas = DB::table('historicos_etapas')
                        ->select(
                            'historicos_etapas.etapas_pedidos_id'
                        )
                        ->join('pedidos', 'historicos_etapas.pedidos_id', '=', 'pedidos.id')
                        ->where('pedidos.id','=',$request->input('id'))
                        ->where('historicos_etapas.funcionarios_id','=',$funcionarios[0]->id)
                        ->where('historicos_etapas.status_id' ,'=', $request->input('atualStatus'))
                        ->orderBy('historicos_etapas.pedidos_id', 'asc')
                        ->orderBy('historicos_etapas.status_id', 'asc')
                        ->orderBy('historicos_etapas.created_at', 'desc')
                        ->limit(1)
                        ->get()
                        ->toArray();

                    $ultima_etapa = !empty($etapas[0]->etapas_pedidos_id) ? $etapas[0]->etapas_pedidos_id : 0;

                    if($ultima_etapa == 1) {
                        $array_liberar=[2,4];

                        if(!in_array($select_etapa_manutencao, $array_liberar)) {
                                throw new \Exception(' A próxima etapa tem que ser PAUSA ou FINALIZADO');
                        }
                    }

                    if($ultima_etapa == 2) {
                        $array_liberar=[3];

                        if(!in_array($select_etapa_manutencao, $array_liberar)) {
                                throw new \Exception(' A próxima etapa tem que ser CONTINUAR');
                        }
                    }

                    if($ultima_etapa == 3) {
                        $array_liberar=[2,4];

                        if(!in_array($select_etapa_manutencao, $array_liberar)) {
                                throw new \Exception(' A próxima etapa deve ser PAUSA ou FINALIZADO');
                        }
                    }

                    if($select_etapa_manutencao == 4 ){



                        $etapasFinalizadas = DB::table('historicos_etapas')
                        ->select('historicos_etapas.id')
                        ->where('historicos_etapas.pedidos_id','=',$request->input('id'))
                        ->where('historicos_etapas.etapas_pedidos_id','=',4)
                        ->get()->toArray();

                        #verifica se falta apenas 1 finalização para mudar o status

                        if(((count($etapasIniciadas) - count($etapasFinalizadas)) <= 1) && $necessitaMontagemExtra == 0) {
                            $finalizar_etapa = true;
                            $pedidos->status_id = $request->input('status');

                        }

                    }
                    $pedidos->save();
                    $numero_etapas_alteracao=$etapasalteracao;
                    if($select_etapa_manutencao == 1){
                        $quantidade_iniciada =  $ModelEtapasAlteracao->where('pedido_id','=', $request->input('id'))->get()->count();
                        $numero_etapas_alteracao =  $quantidade_iniciada + 1;
                        $MetapasAlteracao = new EtapasAlteracao();
                        $MetapasAlteracao->pedido_id = $request->input('id');
                        $MetapasAlteracao->save();
                    }



                    $HistoricosEtapas->pedidos_id = $request->input('id');
                    $HistoricosEtapas->etapas_alteracao_id = $numero_etapas_alteracao;
                    $HistoricosEtapas->status_id = $request->input('atualStatus');
                    $HistoricosEtapas->etapas_pedidos_id =$select_etapa_manutencao;
                    $HistoricosEtapas->funcionarios_id = $funcionarios[0]->id;
                    $HistoricosEtapas->select_tipo_manutencao = $select_tipo_manutencao;
                    $HistoricosEtapas->select_motivo_pausas = $select_motivo_pausas;
                    $HistoricosEtapas->texto_quantidade = $texto_quantidade;
                    $HistoricosEtapas->numero_maquina = $numero_maquina_iniciando;

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
        } catch (\Exception $th) {

            return response($th->getMessage(), 501);
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
        $materiais = $materiais->where('material','LIKE','%CX%')->get();
        if(!empty($request->input()) && !in_array($senha, $array_senha_producao)) {
            $dados = [
                'pedidos' => '',
                'status' => '',
                'mensagem' => 'OS não encontrada/senha incorreta',
                'materiais' => $materiais,
                'motivosPausa' => $painelController->getMotivosPausa(),
            ];

            return view('manutencao_producao', $dados);

        }

        if(!empty($os)) {
            $pedidos = DB::table('pedidos')
            ->join('status', 'pedidos.status_id', '=', 'status.id')
            ->join('ficha_tecnica', 'ficha_tecnica.id', '=', 'pedidos.fichatecnica_id')
            ->join('pessoas', 'pessoas.id', '=', 'pedidos.pessoas_id')
            ->select('pedidos.*', 'ficha_tecnica.ep', 'pessoas.nome_cliente', 'status.nome as nomeStatus' , 'status.id as id_status')
            ->where('pedidos.status_id' ,'<','9')
            ->orderby('status_id', 'desc')
            ->orderby('data_entrega');

            $pedidos = $pedidos->where('os', '=', $os)->where('pedidos.status', '=', 'A')->get();

            $mensagem = 'OS não encontrada';
            if(!empty($pedidos) && count($pedidos) > 0) {
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
                $mensagem = '';
            }

        } else {
            if (!empty($request->input('os'))) {
                $mensagem = 'OS não encontrada';
            }
        }


        $dados = [
            'pedidos' => $pedidos,
            'status' => $dados_status,
            'senha' => $senha,
            'mensagem'=>$mensagem,
            'materiais' => $materiais,
            'motivosPausa' => $painelController->getMotivosPausa(),
        ];

        return view('manutencao_producao', $dados);

    }

}
