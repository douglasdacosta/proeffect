<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\ValidaPermissaoAcessoController;
use App\Models\Funcionarios;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\Projetos;
use App\Providers\DateHelpers;
use App\Models\StatusProjetos;
use App\Models\SubStatusProjetos;
use App\Http\Controllers\PedidosController;
use App\Models\ConfiguracoesProjetos;
use App\Models\EtapasProjetos;
use App\Models\HistoricosEtapasProjetos;
use App\Models\ProjetosLogs;
use App\Models\TarefasProjetos;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ProjetosController extends Controller
{
    public $permissoes_liberadas = [];

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

        $this->permissoes_liberadas = (new ValidaPermissaoAcessoController())->validaAcaoLiberada(22, (new ValidaPermissaoAcessoController())->retornaPerfil());

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
            ->leftJoin('historicos_etapas_projetos', 'historicos_etapas_projetos.projetos_id', '=', 'projetos.id')
            ->select('projetos.*',
            'projetos.ep',
            'pessoas.nome_cliente',
            'pessoas.id as id_pessoa',
            'pessoas.telefone',
            'status_projetos.nome as status_nome',
            'status_projetos.id as id_status',
            'sub_status_projetos.nome as sub_status_projetos_nome',
            'sub_status_projetos.id as sub_status_projetos_id',
            'sub_status_projetos.codigo as sub_status_projetos_codigo',
            'transportes.nome as transporte',
            'funcionarios.nome as nome_funcionario',
            'prioridades.nome as prioridade_nome',
            'etapas_projetos.nome as etapas_projetos_nome',
            'etapas_projetos.id as etapas_projetos_id',
            'status_projetos.ordem as ordem',
            'projetos.data_gerado',
            'projetos.data_antecipacao'
            )
            ->distinct()
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

            $data_1 = DateHelpers::formatDate_dmY($request->input('data_entrega')).' 00:00:01';
            $data_2 = DateHelpers::formatDate_dmY($request->input('data_entrega_fim')).' 23:59:59';

            $projetos = $projetos->whereBetween('historicos_etapas_projetos.created_at', [$data_1, $data_2]);
        }
        if(!empty($request->input('data_entrega')) && empty($request->input('data_entrega_fim') )) {

            $projetos = $projetos->where('historicos_etapas_projetos.created_at', '>=', DateHelpers::formatDate_dmY($request->input('data_entrega').' 00:00:01'));
        }
        if(empty($request->input('data_entrega')) && !empty($request->input('data_entrega_fim') )) {
            $projetos = $projetos->where('historicos_etapas_projetos.created_at', '<=', DateHelpers::formatDate_dmY($request->input('data_entrega_fim')).' 23:59:59');
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

            $value->data_historico = !empty($HistoricosEtapasProjetos->created_at) ? $HistoricosEtapasProjetos->created_at : '';


        }

        $configuracaoProjetos = new ConfiguracoesProjetos();
        $configuracaoProjetos = $configuracaoProjetos->where('id', '=', 1)->first();

        $configuracaoProjetos = json_decode($configuracaoProjetos->dados, true);

        // dd($configuracaoProjetos);

        $dados = [];
        foreach ($projetos as $projeto) {

            $prazo_entrega = '';
            if (!empty($projeto->data_entrega_congelada) || $projeto->alerta_dias_congelado !== null) {
                $projeto->data_prazo_entrega = !empty($projeto->data_entrega_congelada)
                    ? (new DateTime($projeto->data_entrega_congelada))->format('d/m/Y')
                    : '';
                $projeto->alerta_dias = $projeto->alerta_dias_congelado ?? '';
                $projeto->cor_alerta = ($projeto->alerta_dias !== '' && $projeto->alerta_dias < 0) ? 'red' : 'green';
            } elseif(!empty($projeto->tempo_projetos)) {
                $t = explode(':', $projeto->tempo_projetos);
                $horas = (int)$t[0];
                $minutos = isset($t[1]) ? (int)$t[1] : 0;
                $segundos = isset($t[2]) ? (int)$t[2] : 0;
                $tempo_projeto = number_format($horas + ($minutos / 60) + ($segundos / 3600), 2);

                if($tempo_projeto <= 2 && !empty($configuracaoProjetos['0_2_horas'])) {
                    $prazo_entrega = $configuracaoProjetos['0_2_horas'];
                } elseif($tempo_projeto > 2 && $tempo_projeto <= 6 && !empty($configuracaoProjetos['2_6_horas'])) {
                    $prazo_entrega = $configuracaoProjetos['2_6_horas'];
                } elseif($tempo_projeto > 6 && $tempo_projeto <= 10 && !empty($configuracaoProjetos['6_10_horas'])) {
                    $prazo_entrega = $configuracaoProjetos['6_10_horas'];
                } elseif($tempo_projeto > 10 && !empty($configuracaoProjetos['10_ou_mais_horas'])) {
                    $prazo_entrega = $configuracaoProjetos['10_ou_mais_horas'];
                }

                if(!empty($prazo_entrega) and $projeto->id_status == 4) {

                    $data_historico= new DateTime($projeto->data_historico);
                    $data_prazo_entrega = clone $data_historico;

                    $data_prazo_entrega = Carbon::parse($data_prazo_entrega);
                    $data_prazo_entrega->addWeekdays($prazo_entrega);
                    $projeto->data_prazo_entrega = $data_prazo_entrega->format('d/m/Y');

                    $hoje = Carbon::today();
                    $diferenca = Carbon::parse($data_prazo_entrega)->diffInDays($hoje, false);
                    $projeto->cor_alerta = 'green';
                    if($diferenca>0) {
                        $diferenca = $diferenca * -1;
                        $projeto->cor_alerta = 'red';
                    }
                    $projeto->alerta_dias = $diferenca;
                    if($data_prazo_entrega->format('d/m/Y') == $hoje->format('d/m/Y')) {
                        $projeto->cor_alerta = 'green';
                        $projeto->alerta_dias = 0;
                    }


                } else {
                    $projeto->data_prazo_entrega = $projeto->alerta_dias = '';
                }
            }

            if(empty($projeto->data_entrega_congelada) && $projeto->alerta_dias_congelado === null && $projeto->id_status == 3) { //EM AVALIAÇÃO

                if($projeto->sub_status_projetos_id == 3) {
                    $prazo_entrega = $configuracaoProjetos['em_avaliacao'];
                } elseif($projeto->sub_status_projetos_id == 4) {
                    $prazo_entrega = $configuracaoProjetos['elaboracao_design'];
                }

                $data_historico= new DateTime($projeto->data_historico);
                //A DATA DO PRAZO ENTREGA É A SOMA DA DATA GERADO + PRAZO ENTREGA
                $data_prazo_entrega = clone $data_historico;

                $data_prazo_entrega = Carbon::parse($data_prazo_entrega);
                $data_prazo_entrega->addWeekdays($prazo_entrega);
                $projeto->data_prazo_entrega = $data_prazo_entrega->format('d/m/Y');
                $hoje = new DateTime();
                // Calculando a diferença entre as datas
                $diferenca = Carbon::parse($data_prazo_entrega)->diffInDays(Carbon::now(), false);
                $projeto->cor_alerta = 'green';
                //A DIFERENÇA ENTRE A DATA ATUAL E A DATA DO PRAZO DE ENTREGA, se for negativa, já passou do prazo e mostra numero negativo
                if($diferenca > 0) {
                    $diferenca = $diferenca * -1;
                    $projeto->cor_alerta = 'red';
                }
                $projeto->alerta_dias = $diferenca;
                if($data_prazo_entrega->format('d/m/Y') == $hoje->format('d/m/Y')) {
                    $projeto->cor_alerta = 'green';
                    $projeto->alerta_dias = 0;
                }
                $projeto->alerta_dias = $diferenca;

            }

            $dados['departamentos'][$projeto->status_nome][] = array(
                'id' => $projeto->id,
                'os' => $projeto->os,
                'ep' => $projeto->ep,
                'qtde' => $projeto->qtde,
                'blank' => $projeto->blank,
                'nome_cliente' => $projeto->nome_cliente,
                'telefone' => $projeto->telefone,
                'data_gerado' => $projeto->data_gerado ? $projeto->data_gerado : '',
                'data_entrega' => $projeto->data_entrega ? (new DateTime($projeto->data_entrega))->format('d/m/Y') : '',
                'status_nome' => $projeto->status_nome,
                'sub_status_projetos_nome' => $projeto->sub_status_projetos_nome,
                'sub_status_projetos_id' => $projeto->sub_status_projetos_id,
                'sub_status_projetos_codigo' => $projeto->sub_status_projetos_codigo,
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
                'etapas_projetos_id' => $projeto->etapas_projetos_id,
                'data_prazo_entrega' => $projeto->data_prazo_entrega ?? '',
                'data_antecipacao' => $projeto->data_antecipacao ? (new DateTime($projeto->data_antecipacao))->format('d/m/Y') : '',
                'alerta_dias' => $projeto->alerta_dias ?? '',
                'cor_alerta' => $projeto->cor_alerta ?? '',
            );
        }

        $this->ordenarProjetosPorEtapaEData($dados);

        $data = array(
            'tela' => 'pesquisar',
            'nome_tela' => 'projetos',
            'dados' => $dados,
            'configuracaoProjetos' => $configuracaoProjetos,
            'request' => $request,
            'permissoes_liberadas' => $this->permissoes_liberadas,
            'AllEtapasProjetos' => $this->getAllEtapasProjetos(),
            'AllFuncionarios' => $this->getAllFuncionarios(),
            'AllStatus' => $this->getAllStatus(),
            'AllSubStatus' => $this->getAllSubStatus(),
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

                    return $timeB <=> $timeA ;
                }

                return $b['etapas_projetos_id'] <=> $a['etapas_projetos_id'];
            });
        }
    }

    public function consultaDetalhes(Request $request, $id)
    {
        $projeto = Projetos::find($id);

        if (!$projeto) {
            return response()->json(['error' => 'Projeto não encontrado'], 404);
        }

        // Retorne os detalhes do projeto como JSON
        return response()->json(['projeto' => $projeto]);
    }

    public function ativaDesativaAlerta(Request $request, $id)
    {
        $projeto = Projetos::find($id);

        if (!$projeto) {
            return response()->json(['error' => 'Projeto não encontrado'], 404);
        }

        // Alterna o valor de em_alerta
        $projeto->em_alerta = !$projeto->em_alerta;
        $projeto->save();

        return response()->json(['success' => true, 'em_alerta' => $projeto->em_alerta]);
    }

    /**
    * Show the application dashboard - Create new project.
    *
    * @return \Illuminate\Contracts\Support\Renderable
    */
    public function incluir(Request $request)
    {
        // Validar permissões
        $this->permissoes_liberadas = (new ValidaPermissaoAcessoController())
            ->validaAcaoLiberada(22, (new ValidaPermissaoAcessoController())->retornaPerfil());

        $metodo = $request->method();

        // Tratamento para POST - Salvar novo projeto
        if ($metodo == 'POST') {
            return $this->processarInclusao($request);
        }

        // GET Request - Exibir formulário vazio
        $data = [
            'tela' => 'incluir',
            'nome_tela' => 'projetos',
            'projetos' => collect([new Projetos()]),
            'request' => $request,
            'clientes' => (new PedidosController())->getAllClientes(),
            'prioridades' => (new PedidosController())->getAllprioridades(),
            'transportes' => (new PedidosController())->getAlltransportes(),
            'AllStatus' => $this->getAllStatus(),
            'AllSubStatus' => $this->getAllSubStatus(),
            'AllFuncionarios' => $this->getAllFuncionarios(),
            'AllEtapas' => $this->getAllEtapasProjetos(),
            'rotaIncluir' => 'incluir-projetos',
            'rotaAlterar' => 'alterar-projetos',
            'permissoes_liberadas' => $this->permissoes_liberadas,
            'historicos' => collect([])
        ];

        return view('projetos', $data);
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

            $configuracaoProjetos = new ConfiguracoesProjetos();
        $configuracaoProjetos = $configuracaoProjetos->where('id', '=', 1)->first();

        $configuracaoProjetos = json_decode($configuracaoProjetos->dados, true);

        $HistoricosEtapasProjetos = new HistoricosEtapasProjetos();
        $HistoricosEtapasProjetos = $HistoricosEtapasProjetos->where('projetos_id', '=', $request->input('id'))->orderby('created_at', 'DESC')->first();

        $data_historico = !empty($HistoricosEtapasProjetos->created_at) ? $HistoricosEtapasProjetos->created_at : '';

        $status_projetos_id = $this->getSubStatus($request->input('status_id'));
        $sub_status_projetos_id = $status_projetos_id[0]['id'];
        $status_projetos_id = $status_projetos_id[0]['status_projetos_id'];

        if(!empty($request->input('tempo_projetos'))) {

                $t = explode(':', $request->input('tempo_projetos'));
                $horas = (int)$t[0];
                $minutos = isset($t[1]) ? (int)$t[1] : 0;
                $segundos = isset($t[2]) ? (int)$t[2] : 0;
                $tempo_projeto = number_format($horas + ($minutos / 60) + ($segundos / 3600), 2);

                if($tempo_projeto <= 2 && !empty($configuracaoProjetos['0_2_horas'])) {
                    $prazo_entrega = $configuracaoProjetos['0_2_horas'];
                } elseif($tempo_projeto > 2 && $tempo_projeto <= 6 && !empty($configuracaoProjetos['2_6_horas'])) {
                    $prazo_entrega = $configuracaoProjetos['2_6_horas'];
                } elseif($tempo_projeto > 6 && $tempo_projeto <= 10 && !empty($configuracaoProjetos['6_10_horas'])) {
                    $prazo_entrega = $configuracaoProjetos['6_10_horas'];
                } elseif($tempo_projeto > 10 && !empty($configuracaoProjetos['10_ou_mais_horas'])) {
                    $prazo_entrega = $configuracaoProjetos['10_ou_mais_horas'];
                }

                if(!empty($prazo_entrega) && $status_projetos_id == 4) {

                    // dd($data_historico, $prazo_entrega);
                    $data_historico= new DateTime($data_historico);
                    $data_prazo_entrega = clone $data_historico;

                    $data_prazo_entrega = Carbon::parse($data_prazo_entrega);
                    $data_prazo_entrega->addWeekdays($prazo_entrega);

                    $request->merge([
                        'data_entrega' => $data_prazo_entrega->format('Y-m-d H:i:s')
                    ]);

                } else {
                    $request->merge([
                        'data_entrega' => null
                    ]);
                }
            }


            if($status_projetos_id == 3) { //EM AVALIAÇÃO

                if($sub_status_projetos_id == 3) {
                    $prazo_entrega = $configuracaoProjetos['em_avaliacao'];
                } elseif($sub_status_projetos_id == 4) {
                    $prazo_entrega = $configuracaoProjetos['elaboracao_design'];
                }

                $data_historico= new DateTime($data_historico);
                //A DATA DO PRAZO ENTREGA É A SOMA DA DATA GERADO + PRAZO ENTREGA
                $data_prazo_entrega = clone $data_historico;

                $data_prazo_entrega = Carbon::parse($data_prazo_entrega);
                $data_prazo_entrega->addWeekdays($prazo_entrega);

                $request->merge([
                    'data_entrega' => $data_prazo_entrega->format('Y-m-d H:i:s')
                ]);

            }

            $projetos_id = $this->salva($request);



            return redirect()->route('projetos', ['id' => $projetos_id]);
        }

        $historicos = new ProjetosLogs();
        $historicos = $historicos->where('projetos_id', '=', $request->input('id'))->orderby('created_at', 'DESC')->get();

        $data = array(
            'tela' => 'alterar',
            'nome_tela' => 'projetos',
            'projetos' => $projetos,
            'request' => $request,
            'historicos' => $historicos,
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

    /**
     * Resolve a descrição de campos relacionais para exibição no histórico
     *
     * @param string $campo Nome do campo
     * @param mixed $valor Valor do campo (geralmente um ID)
     * @return string|mixed Descrição legível ou o valor original
     */
    private function resolverDescricao($campo, $valor)
    {
        if ($valor === null) {
            return null;
        }

        try {
            switch ($campo) {
                case 'status_projetos_id':
                    $status = StatusProjetos::find($valor);
                    return $status ? $status->nome : $valor;

                case 'sub_status_projetos_codigo':
                    $subStatus = SubStatusProjetos::where('codigo', $valor)->first();
                    return $subStatus ? $subStatus->nome : $valor;

                case 'etapa_projeto_id':
                    $etapa = EtapasProjetos::find($valor);
                    return $etapa ? $etapa->nome : $valor;

                case 'pessoas_id':
                    $pessoa = DB::table('pessoas')->find($valor);
                    return $pessoa ? $pessoa->nome_cliente : $valor;

                case 'transporte_id':
                    $transporte = DB::table('transportes')->find($valor);
                    return $transporte ? $transporte->nome : $valor;

                case 'prioridade_id':
                    $prioridade = DB::table('prioridades')->find($valor);
                    return $prioridade ? $prioridade->nome : $valor;

                case 'funcionarios_id':
                    $funcionario = Funcionarios::find($valor);
                    return $funcionario ? $funcionario->nome : $valor;

                case 'cliente_ativo':
                    return $valor == 1 ? 'Sim' : 'Não';

                case 'status':
                    return $valor == 'A' ? 'Ativo' : 'Inativo';

                case 'novo_alteracao':
                    return $valor == 'N' ? 'Novo' : 'Alteração';

                case 'com_pedido':
                    return $valor == 1 ? 'Sim' : 'Não';

                default:
                    return $valor;
            }
        } catch (\Exception $e) {
            // Em caso de erro, retorna o valor original
            return $valor;
        }
    }

    /**
     * Normaliza valores para comparação no histórico
     * Converte datas e resolve descrições de campos relacionais
     *
     * @param string $campo Nome do campo
     * @param mixed $valor Valor do campo
     * @return string|null Valor normalizado
     */
    public function normalizarValor($campo, $valor)
    {
        if ($valor === null) {
            return null;
        }

        // Normalizar datas para formato brasileiro
        if (in_array($campo, ['data_gerado', 'data_antecipacao', 'data_entrega', 'data_entrega_congelada', 'data_tarefa', 'data_status'])) {
            try {
                return \Carbon\Carbon::parse($valor)->format('d/m/Y');
            } catch (\Exception $e) {
                return $valor;
            }
        }

        // Normalizar valores monetários
        if ($campo === 'valor_unitario_adv') {
            return 'R$ ' . number_format($valor, 2, ',', '.');
        }

        // Normalizar campos de tempo
        if (in_array($campo, ['tempo_projetos', 'tempo_programacao'])) {
            if (!empty($valor)) {
                return $valor; // Mantém formato HH:MM:SS
            }
            return null;
        }

        // Resolver descrições para campos relacionais
        return $this->resolverDescricao($campo, $valor);
    }

    private function calcularPrazoCongelado(?string $tempoProjetos, array $configuracaoProjetos, DateTime $dataBase): array
    {
        if (empty($tempoProjetos)) {
            return [null, null];
        }

        $t = explode(':', $tempoProjetos);
        $horas = (int)$t[0];
        $minutos = isset($t[1]) ? (int)$t[1] : 0;
        $segundos = isset($t[2]) ? (int)$t[2] : 0;
        $tempo_projeto = number_format($horas + ($minutos / 60) + ($segundos / 3600), 2);

        $prazo_entrega = null;
        if ($tempo_projeto <= 2 && !empty($configuracaoProjetos['0_2_horas'])) {
            $prazo_entrega = $configuracaoProjetos['0_2_horas'];
        } elseif ($tempo_projeto > 2 && $tempo_projeto <= 6 && !empty($configuracaoProjetos['2_6_horas'])) {
            $prazo_entrega = $configuracaoProjetos['2_6_horas'];
        } elseif ($tempo_projeto > 6 && $tempo_projeto <= 10 && !empty($configuracaoProjetos['6_10_horas'])) {
            $prazo_entrega = $configuracaoProjetos['6_10_horas'];
        } elseif ($tempo_projeto > 10 && !empty($configuracaoProjetos['10_ou_mais_horas'])) {
            $prazo_entrega = $configuracaoProjetos['10_ou_mais_horas'];
        }

        if (empty($prazo_entrega)) {
            return [null, null];
        }

        $data_prazo_entrega = Carbon::parse($dataBase)->addWeekdays($prazo_entrega);
        $hoje = Carbon::today();
        $diferenca = $data_prazo_entrega->diffInDays($hoje, false);

        if ($diferenca > 0) {
            $diferenca = $diferenca * -1;
        }

        if ($data_prazo_entrega->format('d/m/Y') == $hoje->format('d/m/Y')) {
            $diferenca = 0;
        }

        return [$data_prazo_entrega->format('Y-m-d H:i:s'), $diferenca];
    }

    public function salva($request)
    {
        $id = DB::transaction(function () use ($request) {
            $projeto = new Projetos();
            $projeto_logs = new ProjetosLogs();

            if ($request->input('id')) {
                $projeto = $projeto::find($request->input('id'));
            }
            // dd($request->all());
            $status_id = $request->input('status_id');
            $etapa_projeto_id = $request->input('etapa_projeto_id');

            if($etapa_projeto_id == 5  && $status_id != 36) {
                $status_id = 2;
            }

            $status_projetos_id = $this->getSubStatus($status_id);
            // dd($status_projetos_id);
            $sub_status_projetos_codigo = $status_projetos_id[0]['codigo'];
            $sub_status_projetos_id = $status_projetos_id[0]['id'];
            $status_projetos_id = $status_projetos_id[0]['status_projetos_id'];

            // Verificar se é novo projeto ou se houve mudança de status
            $ehNovoProjeto = !$request->input('id');
            $houveMudancaStatus = !$ehNovoProjeto && $projeto->sub_status_projetos_codigo != $status_id;

            // Se houver mudança de status, marcar para criar histórico DEPOIS de salvar
            $criarHistorico = $houveMudancaStatus || $ehNovoProjeto;

            if($criarHistorico){
                $projeto->data_status = date('Y-m-d');
                $projeto->em_alerta = 1;
            } else {
                $projeto->em_alerta = $request->input('em_alerta');
            }

            if($etapa_projeto_id == 5  && $status_id == 36) {
                $projeto->em_alerta = 0;
            }

            // Labels atualizados para melhor legibilidade
            $labels = [
                'valor_unitario_adv' => 'Valor Unitário',
                'os' => 'OS',
                'ep' => 'EP',
                'qtde' => 'Quantidade',
                'blank' => 'Blank',
                'pessoas_id' => 'Cliente',
                'data_gerado' => 'Data de Geração',
                'data_antecipacao' => 'Data de Antecipação',
                'data_entrega' => 'Data de Entrega',
                'data_entrega_congelada' => 'Prazo Entrega (Congelado)',
                'alerta_dias_congelado' => 'Alerta Dias (Congelado)',
                'observacao' => 'Observação',
                'status' => 'Situação',
                'status_projetos_id' => 'Status do Projeto',
                'sub_status_projetos_codigo' => 'Sub-Status',
                'etapa_projeto_id' => 'Etapa do Projeto',
                'transporte_id' => 'Transporte',
                'prioridade_id' => 'Prioridade',
                'cliente_ativo' => 'Cliente Ativo',
                'novo_alteracao' => 'Tipo',
                'tempo_projetos' => 'Tempo de Projeto',
                'tempo_programacao' => 'Tempo de Programação',
                'funcionarios_id' => 'Responsável',
                'com_pedido' => 'Com Pedido',
            ];


            $projeto->os = $request->input('os');
            $projeto->ep = $request->input('ep');
            $projeto->qtde = $request->input('qtde');
            $projeto->blank = $request->input('blank') && is_numeric($request->input('blank')) ? (int)$request->input('blank') : null;
            $projeto->pessoas_id = $request->input('clientes_id');
            $projeto->data_gerado = !empty($request->input('data_gerado')) ? DateHelpers::formatDate_dmY($request->input('data_gerado')) : null;
            $projeto->data_antecipacao = !empty($request->input('data_antecipacao')) ? DateHelpers::formatDate_dmY($request->input('data_antecipacao')) : null;
            $projeto->status_projetos_id = $status_projetos_id;
            $projeto->sub_status_projetos_codigo = $sub_status_projetos_codigo;
            $projeto->etapa_projeto_id = $etapa_projeto_id;
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

            $deveCongelarPrazo = $criarHistorico && ($status_projetos_id == 4 || (int)$status_id === 36);
            if ($deveCongelarPrazo && empty($projeto->data_entrega_congelada) && $projeto->alerta_dias_congelado === null) {
                $configuracaoProjetos = ConfiguracoesProjetos::where('id', '=', 1)->first();
                $configuracaoProjetos = $configuracaoProjetos ? json_decode($configuracaoProjetos->dados, true) : [];

                [$data_entrega_congelada, $alerta_dias_congelado] = $this->calcularPrazoCongelado(
                    $projeto->tempo_projetos,
                    $configuracaoProjetos,
                    new DateTime()
                );

                if (!empty($data_entrega_congelada) || $alerta_dias_congelado !== null) {
                    $projeto->data_entrega_congelada = $data_entrega_congelada;
                    $projeto->alerta_dias_congelado = $alerta_dias_congelado;
                }
            }

            $alteracoes = [];
            $usuarioLogado = auth()->user()->name;

            foreach ($labels as $campo => $label) {
                if ($projeto->isDirty($campo)) {
                    $antes = $projeto->getOriginal($campo);
                    $depois = $projeto->$campo;

                    // Normaliza os valores (converte datas e resolve descrições)
                    $antesNormalizado = $this->normalizarValor($campo, $antes);
                    $depoisNormalizado = $this->normalizarValor($campo, $depois);

                    // Só registra se realmente mudou
                    if ($antesNormalizado != $depoisNormalizado) {
                        $antesTexto = $antesNormalizado ?? 'vazio';
                        $depoisTexto = $depoisNormalizado ?? 'vazio';

                        $alteracoes[] = "Campo \"{$label}\" alterado de \"{$antesTexto}\" para \"{$depoisTexto}\" por \"{$usuarioLogado}\"";
                    }
                }
            }

            // SALVAR o projeto ANTES de criar histórico
            $projeto->save();

            // AGORA criar histórico com o ID do projeto já salvo
            if($criarHistorico) {
                $HistoricosEtapasProjetos = new HistoricosEtapasProjetos();
                $HistoricosEtapasProjetos->projetos_id = $projeto->id; // Agora tem ID!
                $HistoricosEtapasProjetos->status_projetos_id = $status_projetos_id;
                $HistoricosEtapasProjetos->sub_status_projetos_id = $sub_status_projetos_id;
                $HistoricosEtapasProjetos->funcionarios_id = Auth::user()->id;
                $HistoricosEtapasProjetos->etapas_pedidos_id = $etapa_projeto_id;
                $HistoricosEtapasProjetos->save();
            }

            if(count($alteracoes) > 0) {
                $projeto_logs = new ProjetosLogs();
                $projeto_logs->projetos_id = $projeto->id;
                $projeto_logs->historico = implode("<br>", $alteracoes);
                $projeto_logs->save();
            }

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
        $Status = $Status->where('sub_status_projetos.codigo', '=', $codigo)
        ->orderby('sub_status_projetos.nome', 'asc')
        ->get()->toArray();

        return $Status;
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
        ->orderby('etapas_projetos.id', 'asc')
        ->get();
    }

    /**
     * Processamento de inclusão de novo projeto (POST)
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    private function processarInclusao(Request $request)
    {
        try {
            // 1. Validar dados
            $this->validarDadosInclusao($request);

            // 2. Preparar dados para salvamento
            $this->prepararDadosParaSalvamento($request);

            // 3. Salvar via método existente
            $projetos_id = $this->salva($request);

            // 4. Redirecionar com sucesso
            return redirect()->route('projetos', ['id' => $projetos_id])
                ->with('success', 'Projeto criado com sucesso!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Retornar com erros de validação
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            // Log do erro
            Log::error('Erro ao criar projeto: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Erro ao salvar projeto: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Validar dados de inclusão de novo projeto
     *
     * @param Request $request
     * @return void
     */
    private function validarDadosInclusao(Request $request)
    {
        $request->validate([
            // Campos opcionais - Cliente
            'clientes_id' => 'nullable|integer|exists:pessoas,id',

            // Campos opcionais - OS
            'os' => 'nullable|string|max:50',

            // Campos opcionais - EP
            'ep' => 'nullable|string|max:100',

            // Campos básicos
            'qtde' => 'nullable|integer|min:0',
            'blank' => 'nullable|integer',

            // Datas
            'data_gerado' => 'nullable|date_format:d/m/Y',
            'data_antecipacao' => 'nullable|date_format:d/m/Y',

            // Valores monetários
            'valor_unitario_adv' => 'nullable|regex:/^[\d.,]+$/|min:0',

            // Tempos (formato HH:MM:SS)
            'tempo_projetos' => 'nullable|regex:/^([0-1][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/',
            'tempo_programacao' => 'nullable|regex:/^([0-1][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/',

            // Status e relacionamentos
            'status_id' => 'nullable|integer',
            'etapa_projeto_id' => 'nullable|integer|exists:etapas_projetos,id',
            'prioridade_id' => 'nullable|integer|exists:prioridades,id',
            'transporte_id' => 'nullable|integer|exists:transportes,id',
            'funcionarios_id' => 'nullable|integer|exists:funcionarios,id',

            // Booleanos
            'cliente_ativo' => 'nullable|in:0,1',
            'com_pedido' => 'nullable|in:0,1',
            'novo_alteracao' => 'nullable|in:0,1',
            'em_alerta' => 'nullable|in:0,1',
            'status' => 'nullable|in:A,I',

            // Observações
            'observacao' => 'nullable|string',
        ], [
            'clientes_id.exists' => 'Cliente selecionado não existe',
            'valor_unitario_adv.regex' => 'Valor deve ser um número válido (ex: 2342,34 ou 2342.34)',
            'valor_unitario_adv.min' => 'Valor deve ser maior ou igual a 0',
            'data_gerado.date_format' => 'Data de solicitação deve estar no formato DD/MM/AAAA',
            'data_antecipacao.date_format' => 'Data de antecipação deve estar no formato DD/MM/AAAA',
            'tempo_projetos.regex' => 'Tempo de projeto deve estar no formato HH:MM:SS',
            'tempo_programacao.regex' => 'Tempo de programação deve estar no formato HH:MM:SS',
            'etapa_projeto_id.exists' => 'Etapa selecionada não existe',
            'prioridade_id.exists' => 'Prioridade selecionada não existe',
            'transporte_id.exists' => 'Transporte selecionado não existe',
            'funcionarios_id.exists' => 'Funcionário selecionado não existe',
            'novo_alteracao.in' => 'Campo Novo/Alteração deve ser NOVO ou ALTERAÇÃO',
            'cliente_ativo.in' => 'Campo Cliente Ativo deve ser SIM ou NÃO',
            'com_pedido.in' => 'Campo Pedido deve ser Com Pedido ou Sem Pedido',
            'em_alerta.in' => 'Campo Alerta deve ser Em Alerta ou Sem Alerta',
            'status.in' => 'Campo Status deve ser Ativo ou Inativo',
        ]);
    }

    /**
     * Preparar dados para salvamento na inclusão
     *
     * @param Request $request
     * @return void
     */
    private function prepararDadosParaSalvamento(Request $request)
    {
        // 1. Definir status padrão se não informado (1 = Solicitado)
        if (empty($request->input('status_id'))) {
            $request->merge(['status_id' => 1]);
        }

        // 2. Definir data de geração se vazia (data atual)
        if (empty($request->input('data_gerado'))) {
            $request->merge(['data_gerado' => date('d/m/Y')]);
        }

        // 3. Definir tipo como "Novo"
        $request->merge(['novo_alteracao' => 0]);

        // 4. Ativar novo projeto
        $request->merge(['status' => 'A']);

        // 5. Ativar alerta
        $request->merge(['em_alerta' => 1]);

        // 6. Definir cliente ativo padrão
        if (empty($request->input('cliente_ativo'))) {
            $request->merge(['cliente_ativo' => 1]);
        }

        // 7. Definir com_pedido padrão
        if (empty($request->input('com_pedido'))) {
            $request->merge(['com_pedido' => 0]);
        }
    }

}


