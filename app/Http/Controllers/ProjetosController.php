<?php

namespace App\Http\Controllers;

use App\Models\Funcionarios;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Projetos;
use App\Providers\DateHelpers;
use App\Models\StatusProjetos;
use App\Models\SubStatusProjetos;
use App\Http\Controllers\PedidosController;
use App\Models\EtapasProjetos;
use App\Models\HistoricosEtapasProjetos;
use App\Models\TarefasProjetos;
use DateTime;
use Illuminate\Support\Facades\Auth;

class ProjetosController extends Controller
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
    public function index(Request $request)
    {

        // dd($request->all());
        $funcionarios = new Funcionarios();
        $funcionarios = $funcionarios->where('status','=','A')->orderby('nome')->get();

        $id = !empty($request->input('id')) ? ($request->input('id')) : (!empty($id) ? $id : false);
        $codigo_cliente = !empty($request->input('codigo_cliente')) ? ($request->input('codigo_cliente')) : (!empty($codigo_cliente) ? $codigo_cliente : false);
        $nome_cliente = !empty($request->input('nome_cliente')) ? ($request->input('nome_cliente')) : (!empty($nome_cliente) ? $nome_cliente : false);
        $os = !empty($request->input('os')) ? ($request->input('os')) : (!empty($os) ? $os : false);

        $ep = !empty($request->input('ep')) ? ($request->input('ep')) : (!empty($ep) ? $ep : false);




        $projetos = DB::table('projetos')
            ->leftJoin('status_projetos', 'projetos.status_projetos_id', '=', 'status_projetos.id')
            ->leftJoin('sub_status_projetos', 'projetos.sub_status_projetos_codigo', '=', 'sub_status_projetos.codigo')
            ->leftJoin('pessoas', 'pessoas.id', '=', 'projetos.pessoas_id')
            ->leftJoin('transportes', 'transportes.id', '=', 'projetos.transporte_id')
            ->leftJoin('funcionarios', 'funcionarios.id', '=', 'projetos.funcionarios_id')
            ->leftJoin('prioridades', 'prioridades.id', '=', 'projetos.prioridade_id')
            ->leftJoin('etapas_projetos', 'etapas_projetos.id', '=', 'projetos.etapa_projeto_id')
            ->select('projetos.*',
            'projetos.ep',
            'pessoas.nome_cliente',
            'pessoas.id as id_pessoa',
            'pessoas.telefone',
            'status_projetos.nome as status_nome',
            'status_projetos.id as id_status',
            'sub_status_projetos.nome as sub_status_projetos_nome',
            'sub_status_projetos.codigo as sub_status_projetos_codigo',
            'transportes.nome as transporte',
            'funcionarios.nome as nome_funcionario',
            'prioridades.nome as prioridade_nome',
            'etapas_projetos.nome as etapas_projetos_nome',
            'etapas_projetos.id as etapas_projetos_id'
            )
            ->orderby('status_projetos.ordem', 'asc')
            ->orderby('projetos.data_gerado' , 'DESC');





        if (!empty($request->input('status'))){
            $projetos = $projetos->where('projetos.status', '=', $request->input('status'));
        } else {
            $projetos = $projetos->where('projetos.status', '=', 'A');
        }


        if ($ep) {
            $projetos = $projetos->where('projetos.ep', 'like', '%' . $ep);
        }

        if ($id) {
            $projetos = $projetos->where('projetos.id', '=', $id);
        }

        $departamento_id = [1,2,3,4,5,6,7];
        if(!empty($request->input('departamento_id'))) {
            $departamento_id = $request->input('departamento_id');
        }

        if($projetos) {
            $projetos =$projetos->whereIn('projetos.status_projetos_id', $departamento_id);
        }

        if ($os) {
            $projetos = $projetos->where('projetos.os',  '=', $os);
        }

        if(!empty($request->input('data_entrega')) && !empty($request->input('data_entrega_fim') )) {
            $projetos = $projetos->whereBetween('projetos.data_entrega', [DateHelpers::formatDate_dmY($request->input('data_entrega')), DateHelpers::formatDate_dmY($request->input('data_entrega_fim'))]);
        }
        if(!empty($request->input('data_entrega')) && empty($request->input('data_entrega_fim') )) {
            $projetos = $projetos->where('projetos.data_entrega', '>=', DateHelpers::formatDate_dmY($request->input('data_entrega')));
        }
        if(empty($request->input('data_entrega')) && !empty($request->input('data_entrega_fim') )) {
            $projetos = $projetos->where('projetos.data_entrega', '<=', DateHelpers::formatDate_dmY($request->input('data_entrega_fim')));
        }

        if ($codigo_cliente) {
            $projetos = $projetos->where('pessoas.codigo_cliente', '=', $codigo_cliente);
        }

        if ($nome_cliente) {
            $projetos = $projetos->where('pessoas.nome_cliente', 'like', '%'.$nome_cliente.'%' );
        }

        $projetos = $projetos->get();

        foreach ($projetos as $key => &$value) {

            $tarefas_projetos = new TarefasProjetos();
            $tarefas_projetos = $tarefas_projetos->where('projetos_id', '=', $value->id)->orderby('created_at', 'DESC')->first();

            $value->mensagem = !empty($tarefas_projetos->mensagem) ? $tarefas_projetos->mensagem : '';
            $value->data_tarefa = !empty($tarefas_projetos->data_hora) ? (new DateTime($tarefas_projetos->data_hora))->format('d/m/Y') : '';
            $value->compromisso = !empty($tarefas_projetos->funcionario_id) ? 1 : 0;


            $HistoricosEtapasProjetos = new HistoricosEtapasProjetos();
            $HistoricosEtapasProjetos = $HistoricosEtapasProjetos->where('projetos_id', '=', $value->id)->orderby('created_at', 'DESC')->first();

            $value->data_historico = !empty($HistoricosEtapasProjetos->created_at) ? (new DateTime($HistoricosEtapasProjetos->created_at))->format('d/m/Y') : '';


        }

        $dados = [];
        foreach ($projetos as $projeto) {
            $dados['departamentos'][$projeto->status_nome][] = array(
                'id' => $projeto->id,
                'os' => $projeto->os,
                'ep' => $projeto->ep,
                'qtde' => $projeto->qtde,
                'nome_cliente' => $projeto->nome_cliente,
                'telefone' => $projeto->telefone,
                'data_gerado' => $projeto->data_gerado ? (new DateTime($projeto->data_gerado))->format('d/m/Y') : '',
                'data_entrega' => $projeto->data_entrega ? (new DateTime($projeto->data_entrega))->format('d/m/Y') : '',
                'status_nome' => $projeto->status_nome,
                'sub_status_projetos_nome' => $projeto->sub_status_projetos_nome,
                'status_projetos_id' => $projeto->id_status,
                'prioridade_nome' => $projeto->prioridade_nome,
                'novo_alteracao' => $projeto->novo_alteracao,
                'valor_unitario_adv' => $projeto->valor_unitario_adv,
                'cliente_ativo' => $projeto->cliente_ativo,
                'funcionarios_id' => $projeto->funcionarios_id,
                'transporte' => $projeto->transporte,
                'tempo_programacao' => $projeto->tempo_programacao,
                'tempo_projetos' => $projeto->tempo_projetos,
                'nome_funcionario' => $projeto->nome_funcionario,
                'mensagem' => $projeto->mensagem,
                'data_tarefa' => $projeto->data_tarefa,
                'data_historico' => $projeto->data_historico,
                'compromisso' => $projeto->compromisso,
                'em_alerta' => $projeto->em_alerta,
                'com_pedido' => $projeto->com_pedido,
                'etapas_projetos_nome' => $projeto->etapas_projetos_nome,
                'etapas_projetos_id' => $projeto->etapas_projetos_id
            );
        }


        // dd($projetos);

        $data = array(
            'tela' => 'pesquisar',
            'nome_tela' => 'projetos',
            'dados' => $dados,
            'request' => $request,
            'AllFuncionarios' => $this->getAllFuncionarios(),
            'AllStatus' => $this->getAllStatus(),
            'rotaIncluir' => 'incluir-projetos',
            'rotaAlterar' => 'alterar-projetos'
        );

        return view('projetos', $data);
    }


    function ordenarProjetosPorEtapaEData(array &$dados): void
    {
        if (!isset($dados['departamentos'])) {
            return;
        }

        foreach ($dados['departamentos'] as $status => &$projetos) {
            usort($projetos, function ($a, $b) {
                // Ordena primeiro pelo etapas_projetos_id
                if ($a['etapas_projetos_id'] == $b['etapas_projetos_id']) {
                    // Se forem iguais, ordena pela data_gerado
                    $dataA = DateTime::createFromFormat('d/m/Y', $a['data_gerado']);
                    $dataB = DateTime::createFromFormat('d/m/Y', $b['data_gerado']);

                    $timeA = $dataA ? $dataA->getTimestamp() : 0;
                    $timeB = $dataB ? $dataB->getTimestamp() : 0;

                    return $timeA <=> $timeB;
                }

                return $a['etapas_projetos_id'] <=> $b['etapas_projetos_id'];
            });
        }
    }

    /**
    * Show the application dashboard.
    *
    * @return \Illuminate\Contracts\Support\Renderable
    */
    public function incluir(Request $request)
    {

    }

    /**
    * Show the application dashboard.
    *
    * @return \Illuminate\Contracts\Support\Renderable
    */
    public function alterar(Request $request)
    {

        $projetos = new projetos();

        $projetos = $projetos->where('id', '=', $request->input('id'))->get();

        $metodo = $request->method();
        if ($metodo == 'POST') {

            $projetos_id = $this->salva($request);

            return redirect()->route('projetos', ['id' => $projetos_id]);
        }

        $data = array(
            'tela' => 'alterar',
            'nome_tela' => 'projetos',
            'projetos' => $projetos,
            'request' => $request,
            'clientes' => (new PedidosController())->getAllClientes(),
            'prioridades' => (new PedidosController())->getAllprioridades(),
            'transportes' => (new PedidosController())->getAlltransportes(),
            'AllStatus' => $this->getAllStatus(),
            'AllSubStatus' => $this->getAllSubStatus(),
            'AllFuncionarios' => $this->getAllFuncionarios(),
            'AllEtapas' => $this->getAllEtapasProjetos(),
            'rotaIncluir' => 'incluir-projetos',
            'rotaAlterar' => 'alterar-projetos'
        );
        return view('projetos', $data);
    }

    public function salva($request)
    {
        $id = DB::transaction(function () use ($request) {
            $projeto = new Projetos();

            if ($request->input('id')) {
                $projeto = $projeto::find($request->input('id'));
            }

            $status_projetos_id = $this->getSubStatus($request->input('status_id'));
            $sub_status_projetos_id = $status_projetos_id[0]['id'];
            $status_projetos_id = $status_projetos_id[0]['status_projetos_id'];

            if($projeto->sub_status_projetos_codigo != $request->input('status_id')){
                $projeto->data_status = date('Y-m-d');
                $projeto->em_alerta = 1;

                $HistoricosEtapasProjetos = new HistoricosEtapasProjetos();
                $HistoricosEtapasProjetos->projetos_id = $projeto->id;
                $HistoricosEtapasProjetos->status_projetos_id = $status_projetos_id;
                $HistoricosEtapasProjetos->sub_status_projetos_id = $sub_status_projetos_id;
                $HistoricosEtapasProjetos->funcionarios_id = Auth::user()->id;
                $HistoricosEtapasProjetos->etapas_pedidos_id = $request->input('etapa_projeto_id');
                $HistoricosEtapasProjetos->save();

            } else {

                $projeto->em_alerta = $request->input('em_alerta');
            }

            if($request->input('status_id') == '2' || $request->input('status_id') == '36'){
                $projeto->em_alerta = 0;
            }


            $projeto->os = $request->input('os');
            $projeto->ep = $request->input('ep');
            $projeto->qtde = $request->input('qtde');
            $projeto->pessoas_id = $request->input('clientes_id');
            $projeto->data_gerado = !empty($request->input('data_gerado')) ? DateHelpers::formatDate_dmY($request->input('data_gerado')) : null;
            $projeto->status_projetos_id = $status_projetos_id;
            $projeto->sub_status_projetos_codigo = $request->input('status_id');
            $projeto->etapa_projeto_id = $request->input('etapa_projeto_id');
            $projeto->prioridade_id = $request->input('prioridade_id');
            $projeto->transporte_id = $request->input('transporte_id');
            $projeto->cliente_ativo = $request->input('cliente_ativo');
            $projeto->novo_alteracao = $request->input('novo_alteracao');
            $projeto->tempo_projetos = $request->input('tempo_projetos');
            $projeto->tempo_programacao = $request->input('tempo_programacao');
            $projeto->com_pedido = $request->input('com_pedido');
            $projeto->valor_unitario_adv = DateHelpers::formatFloatValue($request->input('valor_unitario_adv'));
            $projeto->funcionarios_id = $request->input('funcionarios_id');
            $projeto->data_entrega = !empty($request->input('data_entrega')) ? DateHelpers::formatDate_dmY($request->input('data_entrega')) : null;
            $projeto->observacao = trim($request->input('observacao'));
            $projeto->status = $request->input('status');
            $projeto->save();

            return $projeto->id;
        });

        return $id;
    }


    /**
    * Busca todos os status_projetos
    *
    * @return \Illuminate\Contracts\Support\Renderable
    */
    public function getAllStatus()
    {
        $Status = new StatusProjetos();
        return $Status->where('status', '=', 'A')
        ->orderby('status_projetos.nome', 'asc')
        ->get();
    }


    /**
    * Busca todos os sub_status_projetos
    *
    * @return \Illuminate\Contracts\Support\Renderable
    */
    public function getAllSubStatus()
    {
        $Status = new SubStatusProjetos();

        $Status = $Status->join('status_projetos', 'sub_status_projetos.status_projetos_id', '=', 'status_projetos.id');

        $Status = $Status->select('sub_status_projetos.*', 'status_projetos.nome as status_projeto_nome');
        $Retorno = $Status->where('sub_status_projetos.status', '=', 'A')
        ->orderby('status_projetos.nome', 'asc')
        ->orderby('sub_status_projetos.nome', 'asc')
        ->get();

        return $Retorno;
    }

    /**
    * Busca todos os sub_status_projetos
    *
    * @return \Illuminate\Contracts\Support\Renderable
    */
    public function getSubStatus($codigo)
    {
                $Status = new SubStatusProjetos();

        $Status = $Status->join('status_projetos', 'sub_status_projetos.status_projetos_id', '=', 'status_projetos.id');

        $Status = $Status->select('sub_status_projetos.*', 'status_projetos.nome as status_projeto_nome');
        $Retorno = $Status->where('sub_status_projetos.codigo', '=', $codigo)
        ->orderby('sub_status_projetos.nome', 'asc')
        ->get()->toArray();

        return $Retorno;
    }

    /**
    * Busca todos os funcionarios ativos
    *
    * @return \Illuminate\Contracts\Support\Renderable
    */
    public function getAllFuncionarios()
    {
        $Funcionarios = new Funcionarios();
        return $Funcionarios->where('perfil', '=', '6')->where('status', '=', 'A')
        ->orderby('funcionarios.nome', 'asc')
        ->get();
    }

    /**
    * Busca todos os funcionarios ativos
    *
    * @return \Illuminate\Contracts\Support\Renderable
    */
    public function getAllEtapasProjetos()
    {
        $etapas_projetos = new EtapasProjetos();
        return $etapas_projetos->where('status', '=', 'A')
        ->orderby('etapas_projetos.nome', 'asc')
        ->get();
    }

}


