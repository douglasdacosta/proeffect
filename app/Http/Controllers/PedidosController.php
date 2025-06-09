<?php

namespace App\Http\Controllers;

use App\Models\Alertas;
use App\Models\Funcionarios;
use App\Models\HistoricosEtapas;
use App\Models\HistoricosPedidos;
use App\Models\PedidosFuncionariosMontagens;
use Illuminate\Support\Facades\DB;
use App\Models\Fichastecnicas;
use App\Models\Fichastecnicasitens;
use Illuminate\Http\Request;
use App\Models\Pedidos;
use App\Models\Status;
use App\Models\Historicos;
use App\Models\Pessoas;
use App\Models\Prioridades;
use App\Models\Transportes;
use App\Providers\DateHelpers;
use App\Http\Controllers\MaquinasController;
use App\Http\Controllers\ContatosController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\ConsumoMateriaisController;
use App\Models\Maquinas;
use DateTime;

class PedidosController extends Controller
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

        $funcionarios = new Funcionarios();
        $funcionarios = $funcionarios->where('status','=','A')->orderby('nome')->get();

        $id = !empty($request->input('id')) ? ($request->input('id')) : (!empty($id) ? $id : false);
        $codigo_cliente = !empty($request->input('codigo_cliente')) ? ($request->input('codigo_cliente')) : (!empty($codigo_cliente) ? $codigo_cliente : false);
        $nome_cliente = !empty($request->input('nome_cliente')) ? ($request->input('nome_cliente')) : (!empty($nome_cliente) ? $nome_cliente : false);
        $os = !empty($request->input('os')) ? ($request->input('os')) : (!empty($os) ? $os : false);
        $ep = !empty($request->input('ep')) ? ($request->input('ep')) : (!empty($ep) ? $ep : false);




        $pedidos = DB::table('pedidos')
            ->join('status', 'pedidos.status_id', '=', 'status.id')
            ->join('ficha_tecnica', 'ficha_tecnica.id', '=', 'pedidos.fichatecnica_id')
            ->join('pessoas', 'pessoas.id', '=', 'pedidos.pessoas_id')
            ->select('pedidos.*',
            'ficha_tecnica.ep',
            'pessoas.nome_cliente',
            'pessoas.whatsapp_status as whatsapp_status',
            'pessoas.id as id_pessoa',
            'pessoas.telefone',
            'status.nome' ,
            'status.id as id_status'
            )
            ->addSelect(DB::raw('(SELECT COUNT(1) FROM caixas_pedidos where caixas_pedidos.pedidos_id = pedidos.id) as caixas'))
            ->orderby('pedidos.data_entrega');



        if (!empty($request->input('status'))){
            $pedidos = $pedidos->where('pedidos.status', '=', $request->input('status'));
        } else {
            $pedidos = $pedidos->where('pedidos.status', '=', 'A');
        }


        if ($ep) {
            $pedidos = $pedidos->where('ficha_tecnica.ep', 'like', '%'.$ep.'%');
        }

        if ($id) {
            $pedidos = $pedidos->where('pedidos.id', '=', $id);
        }

        if ($os) {
            $pedidos = $pedidos->where('pedidos.os',  'like', '%'.$os.'%');
        }

        if( null === $request->input('status_id')) {
             $status_id = [
                    0 => 1,
                    1 => 2,
                    2 => 3,
                    3 => 4,
                    4 => 5,
                    5 => 6,
                    6 => 7,
                    7 => 8,
                    8 => 9,
                    9 => 10
             ];

        } else {
            $status_id = $this->getAllStatusExcept([11,12,13]);
            if(!empty($request->input('status_id'))) {
                $status_id = $request->input('status_id');
            }

        }

        if($status_id) {
            $pedidos =$pedidos->whereIn('status_id', $status_id);
        }

        if(!empty($request->input('data_entrega')) && !empty($request->input('data_entrega_fim') )) {
            $pedidos = $pedidos->whereBetween('pedidos.data_entrega', [DateHelpers::formatDate_dmY($request->input('data_entrega')), DateHelpers::formatDate_dmY($request->input('data_entrega_fim'))]);
        }
        if(!empty($request->input('data_entrega')) && empty($request->input('data_entrega_fim') )) {
            $pedidos = $pedidos->where('pedidos.data_entrega', '>=', DateHelpers::formatDate_dmY($request->input('data_entrega')));
        }
        if(empty($request->input('data_entrega')) && !empty($request->input('data_entrega_fim') )) {
            $pedidos = $pedidos->where('pedidos.data_entrega', '<=', DateHelpers::formatDate_dmY($request->input('data_entrega_fim')));
        }

        if ($codigo_cliente) {
            $pedidos = $pedidos->where('pessoas.codigo_cliente', '=', $codigo_cliente);
        }

        if ($nome_cliente) {
            $pedidos = $pedidos->where('pessoas.nome_cliente', 'like', '%'.$nome_cliente.'%' );
        }

        $pedidos = $pedidos->get();
        $funcionarios_vinculdaos = [];

        foreach ($pedidos as $key => $pedido) {
            $funcionarios_montagens = DB::table('pedidos_funcionarios_montagens')
                ->join('funcionarios', 'funcionarios.id', '=', 'pedidos_funcionarios_montagens.funcionario_id')
                ->select('funcionarios.nome', 'funcionarios.id')
                ->where('pedidos_funcionarios_montagens.pedido_id', '=', $pedido->id)
                ->orderby('funcionarios.nome')->get();

            $funcionarios_vinculdaos[$pedido->id] = [
                'funcionarios_montagens' => $funcionarios_montagens
            ];
        }

        $alertasPendentes = DB::table('alertas')->where('enviado', '=', 0)->count();
        $data = array(
            'tela' => 'pesquisar',
            'nome_tela' => 'pedidos',
            'pedidos' => $pedidos,
            'request' => $request,
            'funcionarios' => $funcionarios,
            'funcionarios_vinculados' => $funcionarios_vinculdaos,
            'alertasPendentes' => $alertasPendentes,
            'AllStatus' => $this->getAllStatus(),
            'rotaIncluir' => 'incluir-pedidos',
            'rotaAlterar' => 'alterar-pedidos'
        );

        return view('pedidos', $data);
    }

    /**
    * Show the application dashboard.
    *
    * @return \Illuminate\Contracts\Support\Renderable
    */
    public function incluir(Request $request)
    {
        $metodo = $request->method();

        if ($metodo == 'POST') {


            $pedidos_id = $this->salva($request);
            // dd($pedidos_id);
            $this->historicosPedidos($pedidos_id, $request->input('status_id'));
            return redirect()->route('pedidos', ['id' => $pedidos_id]);
        }

        $data = array(
            'tela' => 'incluir',
            'nome_tela' => 'pedidos',
            'request' => $request,
            'status' => $this->getAllStatus(),
            'clientes' =>$this->getAllClientes(),
            'fichastecnicas' =>$this->getAllfichastecnicas(),
            'prioridades' =>$this->getAllprioridades(),
            'transportes' =>$this->getAlltransportes(),
            'rotaIncluir' => 'incluir-pedidos',
            'rotaAlterar' => 'alterar-pedidos'
        );

        return view('pedidos', $data);
    }

    /**
    * Show the application dashboard.
    *
    * @return \Illuminate\Contracts\Support\Renderable
    */
    public function alterar(Request $request)
    {

        $pedidos = new Pedidos();

        $historico = '';
        $pedidos = $pedidos->where('id', '=', $request->input('id'))->get();

        $metodo = $request->method();
        if ($metodo == 'POST') {

            if(DateHelpers::formatDate_dmY($pedidos[0]->data_entrega) != DateHelpers::formatDate_dmY($request->input('data_entrega'))) {
                DateHelpers::formatDate_dmY($request->input("data_entrega"));
                $historico = "Data de entrega do pedido alterado de ".  DateHelpers::formatDate_ddmmYYYY(DateHelpers::formatDate_dmY($pedidos[0]->data_entrega)) . " para " . $request->input("data_entrega");

            }

            if(!empty($request->input('status_id')) && $pedidos[0]->status_id != $request->input('status_id')) {

                $this->historicosPedidos($request->input('id'), $request->input('status_id'));

                $this->filaAlerta($request->input('id'),$pedidos[0]->status, $request->input('status_id'));

            }

            $pedidos_id = $this->salva($request, $historico);
            // $this->enviaEmail($request);

            return redirect()->route('pedidos', ['id' => $pedidos_id]);
        }

        $historicos = Historicos::where('pedidos_id','=', $pedidos[0]->id)->get();
        $data = array(
            'tela' =>'alterar',
            'nome_tela' => 'pedidos',
            'pedidos' => $pedidos,
            'request' => $request,
            'historicos' => $historicos,
            'status' => $this->getAllStatus(),
            'fichastecnicas' =>$this->getAllfichastecnicas(),
            'clientes' =>$this->getAllClientes(),
            'prioridades' =>$this->getAllprioridades(),
            'transportes' =>$this->getAlltransportes(),
            'rotaIncluir' => 'incluir-pedidos',
            'rotaAlterar' => 'alterar-pedidos'
        );

        return view('pedidos', $data);
    }

    public function ajaxIncluirFuncionariosMontagens(Request $request) {

        $montadores = json_decode($request->input('montadores'));

        $funcionarioMontagens = new PedidosFuncionariosMontagens();
        $funcionarioMontagens->where('pedido_id', '=', $request->input('pedido_id'))->delete();

        foreach($montadores as $montador) {
            $funcionarioMontagens = new PedidosFuncionariosMontagens();

            $funcionarioMontagens->pedido_id = $request->input('pedido_id');
            $funcionarioMontagens->funcionario_id = $montador;
            $funcionarioMontagens->save();
        }

        return response('Alterado com sucesso!', 200);


    }

    public function ajaxAlterar(Request $request) {
        $pedidos = new Pedidos();

        if($request->input('id')) {
            $pedidos= $pedidos::find($request->input('id'));
            $status_anterior = $pedidos->status_id;
            $pedidos->status_id = $request->input('status');
            $pedidos->save();

            $this->historicosPedidos($request->input('id'), $request->input('status'));

            $this->filaAlerta($request->input('id'),$status_anterior,$request->input('status'));

            return response('Pedido alterado com sucesso!', 200);
        }

        return response('Erro para salvar', 501);
    }

    public function historicosPedidos($pedido_id, $status_id) {
        $historicosPedidos = new HistoricosPedidos();
        $historicosPedidos->pedidos_id = $pedido_id;
        $historicosPedidos->status_id = $status_id;
        $historicosPedidos->save();
    }

    public function filaAlerta($pedido_id, $status_id_anterior, $novo_status_id) {

        $count = DB::table('alertas')->where('pedidos_id', '=', $pedido_id)->where('enviado', '=', 0)->count();

        if($count == 0) {
            $file = new Alertas();
            $file->pedidos_id = $pedido_id;
            $file->enviado = false;
            $file->save();
        }
    }

    public function enviaEmail($pedido) {

        $contatos = new ContatosController();
        $pedidos = DB::table('pedidos')
        ->join('status', 'pedidos.status_id', '=', 'status.id')
        ->join('ficha_tecnica', 'ficha_tecnica.id', '=', 'pedidos.fichatecnica_id')
        ->join('pessoas', 'pessoas.id', '=', 'pedidos.pessoas_id')
        ->select('pedidos.os', 'pedidos.id', 'pedidos.data_entrega', 'ficha_tecnica.ep', 'pessoas.nome_contato', 'pessoas.email','status.nome', 'status.id as status_id' );

        $pedidos->where('pedidos.id', '=', $pedido);
        $pedidos = $pedidos->get();


        $dados_texto = [
            'pedidos' => $pedidos,
            'statusEnvio' => [
                1 => [
                    'status_contenedor'=> [1,2,3],
                    'descricao' => 'Pedido',
                    'imagem' => base64_encode(file_get_contents(public_path().'/images/status1.png'))
                ],
                2 => [
                    'status_contenedor'=> [4],
                    'descricao' => 'Usinagem',
                    'imagem' => base64_encode(file_get_contents(public_path().'/images/status2.png'))
                ],
                3 => [
                    'status_contenedor'=> [5],
                    'descricao' => 'Acabamento',
                    'imagem' => base64_encode(file_get_contents(public_path().'/images/status3.png'))
                ],
                4 => [
                    'status_contenedor'=> [6],
                    'descricao' => 'Montagem',
                    'imagem' => base64_encode(file_get_contents(public_path().'/images/status4.png'))
                ],
                5 => [
                    'status_contenedor'=> [7],
                    'descricao' => 'Inspeção',
                    'imagem' => base64_encode(file_get_contents(public_path().'/images/status5.png'))
                ],
                6 => [
                    'status_contenedor'=> [8,9,10],
                    'descricao' => 'Expedição',
                    'imagem' => base64_encode(file_get_contents(public_path().'/images/status6.png'))
                ],
                7 => [
                    'status_contenedor'=> [11],
                    'descricao' => 'Entregue',
                    'imagem' => base64_encode(file_get_contents(public_path().'/images/status7.png'))
                    ]
                ]
        ];

        $dados = [
            'fromName' => 'Eplax',
            'fromEmail' => env('MAIL_CC'),
            'assunto' => 'Status de produção '.$pedidos[0]->ep.' Eplax',
            'texto' => view('layouts.emailAlerta', $dados_texto),
            'nome_cliente' => $pedidos[0]->nome_contato,
            'email_cliente' => $pedidos[0]->email,
        ];

        $retorno = $contatos->store($dados);
    }

    public function salva($request, $historico='')
    {
        $id = DB::transaction(function () use ($request, $historico) {
            $pedidos = new Pedidos();

            if ($request->input('id')) {
                $pedidos = $pedidos::find($request->input('id'));
            }

            $pedidos->os = $request->input('os');
            $pedidos->fichatecnica_id = $request->input('fichatecnica');
            $pedidos->qtde = $request->input('qtde');
            $pedidos->data_gerado = !empty($request->input('data_gerado')) ? DateHelpers::formatDate_dmY($request->input('data_gerado')) : null;
            $pedidos->data_entrega = !empty($request->input('data_entrega')) ? DateHelpers::formatDate_dmY($request->input('data_entrega')) : null;
            $pedidos->status_id = $request->input('status_id');
            $pedidos->pessoas_id = $request->input('clientes_id');
            $pedidos->prioridade_id = $request->input('prioridade_id');
            $pedidos->transporte_id = $request->input('transporte_id');
            $pedidos->data_antecipacao = !empty($request->input('data_antecipacao')) ? DateHelpers::formatDate_dmY($request->input('data_antecipacao')) : null;
            $pedidos->hora_antecipacao = !empty($request->input('hora_antecipacao')) ? $request->input('hora_antecipacao') : null;
            $pedidos->observacao = trim($request->input('observacao'));
            $pedidos->status = $request->input('status');
            $pedidos->save();

            if(!empty($historico)) {
                $historicos = new Historicos();
                $historicos->pedidos_id = $pedidos->id;
                $historicos->historico = $historico;
                $historicos->status = 'A';
                $historicos->save();
            }

            return $pedidos->id;
        });

        return $id;
    }

    /**
    * Show the application dashboard.
    *
    * @return \Illuminate\Contracts\Support\Renderable
    */
    public function followup(Request $request)
    {
        $status_id = $request->input('status_id');
        $filtrado = 0;
        $pedidos = DB::table('pedidos')
            ->distinct()
            ->join('status', 'pedidos.status_id', '=', 'status.id')
            ->join('ficha_tecnica', 'ficha_tecnica.id', '=', 'pedidos.fichatecnica_id')
            ->join('pessoas', 'pessoas.id', '=', 'pedidos.pessoas_id')
            ->orderby('status_id', 'desc')
            ->orderby('data_entrega');

        if(!empty($request->input('data_apontamento'))) {
            $pedidos = $pedidos->select('pedidos.*',
            'ficha_tecnica.ep',
            'pessoas.nome_cliente',
            'status.nome',
            'historicos_etapas.created_at as historicos_etapas_created_at',
            'historicos_pedidos.created_at as historicos_pedidos_created_at',
            'status.id as id_status');

            $pedidos = $pedidos->leftJoin('historicos_etapas', function ($join) use ($status_id, $request) {
                $join = $join->on('pedidos.id', '=', 'historicos_etapas.pedidos_id');
                $join = $join->where('historicos_etapas.etapas_pedidos_id', '=', 4)
                     ->whereIn('historicos_etapas.status_id', $status_id);
                if(!empty($request->input('data_apontamento')) && !empty($request->input('data_apontamento_fim') )) {
                    $join = $join->whereBetween('historicos_etapas.created_at', [DateHelpers::formatDate_dmY($request->input('data_apontamento')) . ' 00:00:01', DateHelpers::formatDate_dmY($request->input('data_apontamento_fim')) . ' 23:59:59']);

                }
                if(!empty($request->input('data_apontamento')) && empty($request->input('data_apontamento_fim') )) {
                    $join = $join->where('historicos_etapas.created_at', '>=', DateHelpers::formatDate_dmY($request->input('data_apontamento')) .' 00:00:01');
                }
                if(empty($request->input('data_apontamento')) && !empty($request->input('data_apontamento_fim') )) {
                    $join = $join->where('historicos_etapas.created_at', '<=', DateHelpers::formatDate_dmY($request->input('data_apontamento_fim')) .' 00:00:01');

                }
            });



            $pedidos  = $pedidos->leftJoin('historicos_pedidos', function ($join) use ($status_id, $request) {
                $join = $join->on('pedidos.id', '=', 'historicos_pedidos.pedidos_id');

                $join = $join->whereIn('historicos_pedidos.status_id', $status_id);
                if(!empty($request->input('data_apontamento')) && !empty($request->input('data_apontamento_fim') )) {
                    $join = $join->whereBetween('historicos_pedidos.created_at', [DateHelpers::formatDate_dmY($request->input('data_apontamento')) . ' 00:00:00', DateHelpers::formatDate_dmY($request->input('data_apontamento_fim')) . ' 23:59:59']);
                }
                if(!empty($request->input('data_apontamento')) && empty($request->input('data_apontamento_fim') )) {
                    $join = $join->where('historicos_pedidos.created_at', '>=', DateHelpers::formatDate_dmY($request->input('data_apontamento')) . ' 00:00:00');
                }
                if(empty($request->input('data_apontamento')) && !empty($request->input('data_apontamento_fim') )) {
                    $join = $join->where('historicos_pedidos.created_at', '<=', DateHelpers::formatDate_dmY($request->input('data_apontamento_fim')) . ' 00:00:00');
                }
            });


            $filtrado++;
        } else {
            $pedidos = $pedidos->select('pedidos.*',
            'ficha_tecnica.ep',
            'pessoas.nome_cliente',
            'status.nome',
            'status.id as id_status');
        }

        if(!empty($request->input('os'))) {
            $pedidos = $pedidos->where('os', '=', $request->input('os'));
            $filtrado++;
        }
        if(!empty($request->input('ep'))) {
            $pedidos = $pedidos->where('ficha_tecnica.ep', '=', $request->input('ep'));
            $filtrado++;
        }
        if(!empty($request->input('id'))) {
            $pedidos = $pedidos->where('id', '=', $request->input('id'));
            $filtrado++;
        }

        if($request->input('tipo_consulta') == 'F') {


            if(!empty($request->input('status_id'))) {
                $pedidos = $pedidos->whereIn('pedidos.status_id', $status_id);
                $filtrado++;
            }

            if(!empty($request->input('data_gerado')) && !empty($request->input('data_gerado_fim') )) {
                $pedidos = $pedidos->whereBetween('data_gerado', [DateHelpers::formatDate_dmY($request->input('data_gerado')), DateHelpers::formatDate_dmY($request->input('data_gerado_fim'))]);
                $filtrado++;
            }
            if(!empty($request->input('data_gerado')) && empty($request->input('data_gerado_fim') )) {
                $pedidos = $pedidos->where('data_gerado', '>=', DateHelpers::formatDate_dmY($request->input('data_gerado')));
                $filtrado++;
            }
            if(empty($request->input('data_gerado')) && !empty($request->input('data_gerado_fim') )) {
                $pedidos = $pedidos->where('data_gerado', '<=', DateHelpers::formatDate_dmY($request->input('data_gerado_fim')));
                $filtrado++;
            }

            if(!empty($request->input('data_entrega')) && !empty($request->input('data_entrega_fim') )) {
                $pedidos = $pedidos->whereBetween('data_entrega', [DateHelpers::formatDate_dmY($request->input('data_entrega')), DateHelpers::formatDate_dmY($request->input('data_entrega_fim'))]);
                $filtrado++;
            }
            if(!empty($request->input('data_entrega')) && empty($request->input('data_entrega_fim') )) {
                $pedidos = $pedidos->where('data_entrega', '>=', DateHelpers::formatDate_dmY($request->input('data_entrega')));
                $filtrado++;
            }
            if(empty($request->input('data_entrega')) && !empty($request->input('data_entrega_fim') )) {
                $pedidos = $pedidos->where('data_entrega', '<=', DateHelpers::formatDate_dmY($request->input('data_entrega_fim')));
                $filtrado++;
            }
        }

        $pedidos = $pedidos->where('pedidos.status', '=', 'A');


        $pedidos_encontrados = [];


        if ($filtrado > 0) {

            $pedidos = $pedidos->get();


            foreach ($pedidos as $key => $value) {

                if($request->input('tipo_consulta') == 'R' || $request->input('tipo_consulta') == 'C') {

                    if(empty($value->historicos_etapas_created_at) && empty($value->historicos_pedidos_created_at)) {
                        continue;
                    };

                }


                $pedidos_encontrados[] = $value->id;
            }
        }

        
        $tela = 'pesquisa-followup';
        $nome_tela = 'followup tempos';
        $data = array(
            'tela' => $tela,
            'nome_tela' =>$nome_tela,
            'pedidos_encontrados' => $pedidos_encontrados,
            'pedidos' => $pedidos,
            'request' => $request,
            'maquinas' => $this->getMaquinas(),
            'status' => $this->getAllStatus(),
            'rotaIncluir' => 'incluir-pedidos',
            'rotaAlterar' => 'alterar-pedidos'
        );


        return view('pedidos', $data);
    }

    /**
    * Show the application dashboard.
    *
    * @return \Illuminate\Contracts\Support\Renderable
    */
    public function followupDetalhes(Request $request)
    {
        $pedidos = new Pedidos();

        $nome_tela = !empty($request->input('nome_tela')) ? $request->input('nome_tela') : 'tempos' ;

        if(empty($request->input('pedidos_encontrados'))) {
            return redirect()->route('followup');
        }
        $pedidos_encontrados = json_decode($request->input('pedidos_encontrados'));

        $pedidos = $pedidos::with('tabelaStatus', 'tabelaFichastecnicas', 'tabelaPessoas')
        ->wherein('id', $pedidos_encontrados)
        ->orderby('status_id', 'desc')
        ->orderby('data_entrega')->get();

        $total_tempo_usinagem=$total_tempo_acabamento=$total_tempo_montagem=$total_tempo_inspecao='00:00:00';
        $dados_pedido_status=[];

        foreach ($pedidos as $pedido) {
            $dados_pedido_status[$pedido->tabelaStatus->nome]['classe'][] = $pedido;
            $dados_pedido_status[$pedido->tabelaStatus->nome]['id_status'][] = $pedido->tabelaStatus->id;
        }

        $MaquinasController = new MaquinasController();

        $Maquinas = new Maquinas();

        $maquinas = $Maquinas->get();

        $qtde_maquinas =$maquinas[0]->qtde_maquinas;
        $horas_maquinas =$maquinas[0]->horas_maquinas;
        $pessoas_acabamento =$maquinas[0]->pessoas_acabamento;
        $pessoas_montagem =$maquinas[0]->pessoas_montagem;
        $pessoas_montagem_torres =$maquinas[0]->pessoas_montagem_torres;
        $pessoas_inspecao =$maquinas[0]->pessoas_inspecao;
        $horas_dia =$maquinas[0]->horas_dia;
        $total_horas_usinagem_maquinas_dia = $this->multiplyTimeByInteger($horas_maquinas, $qtde_maquinas);
        $total_horas_pessoas_acabamento_dia = $this->multiplyTimeByInteger($horas_dia, $pessoas_acabamento);
        $total_horas_pessoas_pessoas_montagem_dia = $this->multiplyTimeByInteger($horas_dia, $pessoas_montagem);
        $total_horas_pessoas_pessoas_montagem_torres_dia = $this->multiplyTimeByInteger($horas_dia, $pessoas_montagem_torres);
        $total_horas_pessoas_inspecao_dia = $this->multiplyTimeByInteger($horas_dia, $pessoas_inspecao);
        $totalGeral = [];
        foreach ($dados_pedido_status as $status => $pedidos) {


            foreach ($pedidos['classe'] as $chave =>  $pedido) {

                $total_tempo_usinagem=$total_tempo_acabamento=$total_tempo_montagem_torre=$total_tempo_montagem=$total_tempo_inspecao='00:00:00';

                $total_tempo_usinagem = $this->somarHoras($total_tempo_usinagem , $pedido->tabelaFichastecnicas->tempo_usinagem);
                $total_tempo_usinagem = $MaquinasController->multiplicarHoras($total_tempo_usinagem,$pedido->qtde);
                $dados_pedido_status[$status]['pedido'][$pedido->id]['usinagem'] = $total_tempo_usinagem;

                $total_tempo_acabamento = $this->somarHoras($total_tempo_acabamento , $pedido->tabelaFichastecnicas->tempo_acabamento);
                $total_tempo_acabamento = $MaquinasController->multiplicarHoras($total_tempo_acabamento,$pedido->qtde);
                $dados_pedido_status[$status]['pedido'][$pedido->id]['acabamento'] = $total_tempo_acabamento;

                $total_tempo_montagem_torre = $this->somarHoras($total_tempo_montagem_torre , $pedido->tabelaFichastecnicas->tempo_montagem_torre);
                $total_tempo_montagem_torre = $MaquinasController->multiplicarHoras($total_tempo_montagem_torre,$pedido->qtde);

                $dados_pedido_status[$status]['pedido'][$pedido->id]['montagem_torre'] = $total_tempo_montagem_torre;
                $total_tempo_montagem = $this->somarHoras($total_tempo_montagem , $pedido->tabelaFichastecnicas->tempo_montagem);
                $total_tempo_montagem = $MaquinasController->multiplicarHoras($total_tempo_montagem,$pedido->qtde);

                if($nome_tela == 'geral') {
                    $total_tempo_montagem = $this->somarHoras($total_tempo_montagem, $total_tempo_montagem_torre) ;
                }

                $dados_pedido_status[$status]['pedido'][$pedido->id]['montagem'] = $total_tempo_montagem;

                $total_tempo_inspecao = $this->somarHoras($total_tempo_inspecao , $pedido->tabelaFichastecnicas->tempo_inspecao);
                $total_tempo_inspecao = $MaquinasController->multiplicarHoras($total_tempo_inspecao,$pedido->qtde);
                $dados_pedido_status[$status]['pedido'][$pedido->id]['inspecao'] = $total_tempo_inspecao;

                $dados_pedido_status[$status]['totais']['total_tempo_usinagem'] = $this->somarHoras(!empty($dados_pedido_status[$status]['totais']['total_tempo_usinagem']) ? $dados_pedido_status[$status]['totais']['total_tempo_usinagem']: '00:00:00' , $total_tempo_usinagem);
                $dados_pedido_status[$status]['totais']['total_tempo_acabamento'] = $this->somarHoras(!empty($dados_pedido_status[$status]['totais']['total_tempo_acabamento']) ? $dados_pedido_status[$status]['totais']['total_tempo_acabamento'] : "00:00:00", $total_tempo_acabamento);
                $dados_pedido_status[$status]['totais']['total_tempo_montagem_torre'] = $this->somarHoras(!empty($dados_pedido_status[$status]['totais']['total_tempo_montagem_torre']) ? $dados_pedido_status[$status]['totais']['total_tempo_montagem_torre'] : "00:00:00", $total_tempo_montagem_torre);
                $dados_pedido_status[$status]['totais']['total_tempo_montagem'] = $this->somarHoras(!empty($dados_pedido_status[$status]['totais']['total_tempo_montagem']) ? $dados_pedido_status[$status]['totais']['total_tempo_montagem'] : "00:00:00", $total_tempo_montagem);
                $dados_pedido_status[$status]['totais']['total_tempo_inspecao'] = $this->somarHoras(!empty($dados_pedido_status[$status]['totais']['total_tempo_inspecao']) ? $dados_pedido_status[$status]['totais']['total_tempo_inspecao'] : "00:00:00", $total_tempo_inspecao);

                $historicos_pedidos_datas = new HistoricosPedidos();
                $historicos_pedidos_datas = $historicos_pedidos_datas->where('pedidos_id', '=', $pedido->id );
                $historicos_pedidos_datas = $historicos_pedidos_datas->orderBy('created_at', 'desc')->limit(1)->get();
                $dados_pedido_status[$status]['pedido'][$pedido->id]['data_alteracao_status'] = !empty($historicos_pedidos_datas[0]->created_at) ? \Carbon\Carbon::parse($historicos_pedidos_datas[0]->created_at)->format('d/m/Y') : null;

            }

            $dados_pedido_status[$status]['maquinas_usinagens'] = $this->divideHoursIntoDays($dados_pedido_status[$status]['totais']['total_tempo_usinagem'], $total_horas_usinagem_maquinas_dia);
            $dados_pedido_status[$status]['pessoas_acabamento'] = $this->divideHoursAndReturnWorkDays($dados_pedido_status[$status]['totais']['total_tempo_acabamento'], $total_horas_pessoas_acabamento_dia);


            $dados_pedido_status[$status]['pessoas_montagem_torre'] = $this->divideHoursAndReturnWorkDays($dados_pedido_status[$status]['totais']['total_tempo_montagem_torre'], $total_horas_pessoas_pessoas_montagem_torres_dia);

            $dados_pedido_status[$status]['pessoas_montagem'] = $this->divideHoursAndReturnWorkDays($dados_pedido_status[$status]['totais']['total_tempo_montagem'], $total_horas_pessoas_pessoas_montagem_dia);
            $dados_pedido_status[$status]['pessoas_inspecao'] =$this->divideHoursAndReturnWorkDays($dados_pedido_status[$status]['totais']['total_tempo_inspecao'], $total_horas_pessoas_inspecao_dia);

            if($pedidos['id_status'][$chave] <= 4 ){
                $totalGeral['totalGeralusinagens'] = ((!empty($totalGeral['totalGeralusinagens']) ? $totalGeral['totalGeralusinagens'] : '0') + preg_replace('/[^0-9.]/', '', $dados_pedido_status[$status]['maquinas_usinagens']) );
                $totalGeral['totalGeralacabamento'] = ((!empty($totalGeral['totalGeralacabamento']) ? $totalGeral['totalGeralacabamento'] : '0') + preg_replace('/[^0-9.]/', '', $dados_pedido_status[$status]['pessoas_acabamento']) );
                $totalGeral['totalGeralmontagem_torre'] = ((!empty($totalGeral['totalGeralmontagem_torre']) ? $totalGeral['totalGeralmontagem_torre'] : '0') + preg_replace('/[^0-9.]/', '', $dados_pedido_status[$status]['pessoas_montagem_torre']) );
                $totalGeral['totalGeralmontagem'] = ((!empty($totalGeral['totalGeralmontagem']) ? $totalGeral['totalGeralmontagem'] : '0') + preg_replace('/[^0-9.]/', '', $dados_pedido_status[$status]['pessoas_montagem']) );
                $totalGeral['totalGeralinspecao'] = ((!empty($totalGeral['totalGeralinspecao']) ? $totalGeral['totalGeralinspecao'] : '0') + preg_replace('/[^0-9.]/', '', $dados_pedido_status[$status]['pessoas_inspecao']) );
            }
            if($pedidos['id_status'][$chave] == 5 ){
                $totalGeral['totalGeralacabamento'] = ((!empty($totalGeral['totalGeralacabamento']) ? $totalGeral['totalGeralacabamento'] : '0') + preg_replace('/[^0-9.]/', '', $dados_pedido_status[$status]['pessoas_acabamento']) );
                $totalGeral['totalGeralmontagem_torre'] = ((!empty($totalGeral['totalGeralmontagem_torre']) ? $totalGeral['totalGeralmontagem_torre'] : '0') + preg_replace('/[^0-9.]/', '', $dados_pedido_status[$status]['pessoas_montagem_torre']) );
                $totalGeral['totalGeralmontagem'] = ((!empty($totalGeral['totalGeralmontagem']) ? $totalGeral['totalGeralmontagem'] : '0') + preg_replace('/[^0-9.]/', '', $dados_pedido_status[$status]['pessoas_montagem']) );
                $totalGeral['totalGeralinspecao'] = ((!empty($totalGeral['totalGeralinspecao']) ? $totalGeral['totalGeralinspecao'] : '0') + preg_replace('/[^0-9.]/', '', $dados_pedido_status[$status]['pessoas_inspecao']) );
            }
            if($pedidos['id_status'][$chave] == 6 ){
                $totalGeral['totalGeralmontagem_torre'] = ((!empty($totalGeral['totalGeralmontagem_torre']) ? $totalGeral['totalGeralmontagem_torre'] : '0') + preg_replace('/[^0-9.]/', '', $dados_pedido_status[$status]['pessoas_montagem_torre']) );
                $totalGeral['totalGeralmontagem'] = ((!empty($totalGeral['totalGeralmontagem']) ? $totalGeral['totalGeralmontagem'] : '0') + preg_replace('/[^0-9.]/', '', $dados_pedido_status[$status]['pessoas_montagem']) );
                $totalGeral['totalGeralinspecao'] = ((!empty($totalGeral['totalGeralinspecao']) ? $totalGeral['totalGeralinspecao'] : '0') + preg_replace('/[^0-9.]/', '', $dados_pedido_status[$status]['pessoas_inspecao']) );
            }
            if($pedidos['id_status'][$chave] == 7 ){
                $totalGeral['totalGeralinspecao'] = ((!empty($totalGeral['totalGeralinspecao']) ? $totalGeral['totalGeralinspecao'] : '0') + preg_replace('/[^0-9.]/', '', $dados_pedido_status[$status]['pessoas_inspecao']) );
            }


        }

        $tela = 'followup-detalhes';
        $nome_da_tela ='followup tempos';
        if($nome_tela == 'geral') {
            $tela = 'followup-detalhes-geral';
            $nome_da_tela ='followup geral';
        }

        $dias_alerta_maquinas = $this->getMaquinas();

        $data = array(
            'tela' => $tela,
            'nome_tela' => $nome_da_tela,
            'dados_pedido_status' => $dados_pedido_status,
            'totalGeral' => $totalGeral,
            'request' => $request,
            'status' => $this->getAllStatus(),
            'maquinas' => $dias_alerta_maquinas,
            'rotaIncluir' => 'incluir-pedidos',
            'rotaAlterar' => 'alterar-pedidos'
        );


        return view('pedidos', $data);
    }


    /**
    * Show the application dashboard.
    *
    * @return \Illuminate\Contracts\Support\Renderable
    */
    public function followupRealizado(Request $request)
    {
        $pedidos = new Pedidos();

        $nome_tela = !empty($request->input('nome_tela')) ? $request->input('nome_tela') : 'tempos' ;

        $data_apontamento = $request->input('data_apontamento', 0);
        $data_apontamento_fim = $request->input('data_apontamento_fim', 0);

        if(empty($request->input('pedidos_encontrados'))) {
            return redirect()->route('followup');
        }
        $pedidos_encontrados = json_decode($request->input('pedidos_encontrados'));

        $pedidos = $pedidos::with('tabelaStatus', 'tabelaFichastecnicas', 'tabelaPessoas')
        ->wherein('id', $pedidos_encontrados)
        ->orderby('status_id', 'desc')
        ->orderby('data_entrega')->get();

        $dados_pedido_status=[];

        foreach ($pedidos as &$pedido) {

            $historicos_etapas = new HistoricosEtapas();

            $historicos_etapas = $historicos_etapas->whereIn('status_id', [4,5,6,7,8]);
            $historicos_etapas = $historicos_etapas->where('etapas_pedidos_id', '=', 4 );
            $historicos_etapas = $historicos_etapas->where('pedidos_id', '=', $pedido->id);

            if(!empty($request->input('data_apontamento')) && !empty($request->input('data_apontamento_fim') )) {
                $historicos_etapas = $historicos_etapas->whereBetween('created_at', [DateHelpers::formatDate_dmY($request->input('data_apontamento')).' 00:00:01' , DateHelpers::formatDate_dmY($request->input('data_apontamento_fim')).' 23:59:59']);
            }
            if(!empty($request->input('data_apontamento')) && empty($request->input('data_apontamento_fim') )) {
                $historicos_etapas = $historicos_etapas->where('created_at', '>=', DateHelpers::formatDate_dmY($request->input('data_apontamento')).' 00:00:01');
            }
            if(empty($request->input('data_apontamento')) && !empty($request->input('data_apontamento_fim') )) {
                $historicos_etapas = $historicos_etapas->where('created_at', '<=', DateHelpers::formatDate_dmY($request->input('data_apontamento_fim')).' 00:00:01');
            }

            $historicos_etapas = $historicos_etapas->get();

            $historicos_apontamentos = [];

            foreach ($historicos_etapas as $key => $historico_etapa) {
                $historicos_apontamentos[$historico_etapa['status_id']] = [
                    'data_apontamento' => \Carbon\Carbon::parse($historico_etapa['created_at'])->format('Y-m-d'),
                    'torre' => ($historico_etapa['select_tipo_manutencao'] == 'A' ? 0 : 1)
                ];
            }
            $pedido->apontamento_usinagem = !empty($historicos_apontamentos[4]['data_apontamento']) ? $historicos_apontamentos[4]['data_apontamento'] : null;
            $pedido->apontamento_acabamento = !empty($historicos_apontamentos[5]['data_apontamento']) ? $historicos_apontamentos[5]['data_apontamento'] : null;

            $pedido->apontamento_montagem = '';
            $pedido->apontamento_montagem_torre = '';

            if(!empty($historicos_apontamentos[6]['data_apontamento'])){
                if ($historicos_apontamentos[6]['torre'] == 0 ) {
                    $pedido->apontamento_montagem = !empty($historicos_apontamentos[6]['data_apontamento']) ? $historicos_apontamentos[6]['data_apontamento'] : null;
                } else {
                    $pedido->apontamento_montagem_torre = !empty($historicos_apontamentos[6]['data_apontamento']) ? $historicos_apontamentos[6]['data_apontamento'] : null;

                }
            }

            $pedido->apontamento_inspecao = !empty($historicos_apontamentos[7]['data_apontamento']) ? $historicos_apontamentos[7]['data_apontamento'] : null;
            $pedido->apontamento_embalagem = !empty($historicos_apontamentos[8]['data_apontamento']) ? $historicos_apontamentos[8]['data_apontamento'] : null;



            $historicos_pedidos = new HistoricosPedidos();
            $historicos_pedidos = $historicos_pedidos->whereIn('status_id', [1,2,3,9,10,11,12,13]);
            $historicos_pedidos = $historicos_pedidos->where('pedidos_id', '=', $pedido->id );
            if(!empty($request->input('data_apontamento')) && !empty($request->input('data_apontamento_fim') )) {
                $historicos_pedidos = $historicos_pedidos->whereBetween('created_at', [DateHelpers::formatDate_dmY($request->input('data_apontamento')) . ' 00:00:00', DateHelpers::formatDate_dmY($request->input('data_apontamento_fim')). ' 23:59:59']);
            }
            if(!empty($request->input('data_apontamento')) && empty($request->input('data_apontamento_fim') )) {
                $historicos_pedidos = $historicos_pedidos->where('created_at', '>=', DateHelpers::formatDate_dmY($request->input('data_apontamento')) . ' 00:00:00');
            }
            if(empty($request->input('data_apontamento')) && !empty($request->input('data_apontamento_fim') )) {
                $historicos_pedidos = $historicos_pedidos->where('created_at', '<=', DateHelpers::formatDate_dmY($request->input('data_apontamento_fim')) . ' 00:00:00');
            }

            $historicos_pedidos = $historicos_pedidos->get();
            $historicos_pedidos = $historicos_pedidos->toArray();


            $historicos_apontamentos = [];
            foreach ($historicos_pedidos as $key => $historicos_pedido) {
                $historicos_apontamentos[$historicos_pedido['status_id']] = [
                    'data_apontamento' => \Carbon\Carbon::parse($historicos_pedido['created_at'])->format('Y-m-d'),
                ];
            }

            $pedido->apontamento_expedicao = !empty($historicos_apontamentos[9]['data_apontamento']) ? $historicos_apontamentos[9]['data_apontamento'] : null;
            $pedido->apontamento_estoque = !empty($historicos_apontamentos[10]['data_apontamento']) ? $historicos_apontamentos[10]['data_apontamento'] : null;
            $pedido->apontamento_entregue = !empty($historicos_apontamentos[11]['data_apontamento']) ? $historicos_apontamentos[11]['data_apontamento'] : null;


            $dados_pedido_status[$pedido->tabelaStatus->nome]['classe'][] = $pedido;
            $dados_pedido_status[$pedido->tabelaStatus->nome]['id_status'][] = $pedido->tabelaStatus->id;
        }

        $MaquinasController = new MaquinasController();

        $Maquinas = new Maquinas();

        $maquinas = $Maquinas->get();

        $qtde_maquinas =$maquinas[0]->qtde_maquinas;
        $horas_maquinas =$maquinas[0]->horas_maquinas;
        $pessoas_acabamento =$maquinas[0]->pessoas_acabamento;
        $pessoas_montagem =$maquinas[0]->pessoas_montagem;
        $pessoas_montagem_torres =$maquinas[0]->pessoas_montagem_torres;
        $pessoas_inspecao =$maquinas[0]->pessoas_inspecao;
        $horas_dia =$maquinas[0]->horas_dia;
        $total_horas_usinagem_maquinas_dia = $this->multiplyTimeByInteger($horas_maquinas, $qtde_maquinas);
        $total_horas_pessoas_acabamento_dia = $this->multiplyTimeByInteger($horas_dia, $pessoas_acabamento);
        $total_horas_pessoas_pessoas_montagem_dia = $this->multiplyTimeByInteger($horas_dia, $pessoas_montagem);
        $total_horas_pessoas_pessoas_montagem_torres_dia = $this->multiplyTimeByInteger($horas_dia, $pessoas_montagem_torres);
        $total_horas_pessoas_inspecao_dia = $this->multiplyTimeByInteger($horas_dia, $pessoas_inspecao);
        $totalGeral = [];

        $status_id = json_decode($request->input('status_id'));

        foreach ($dados_pedido_status as $status => &$pedidos) {

            foreach ($pedidos['classe'] as $chave =>  &$pedido) {


                $array_status = [
                    '4' => 'apontamento_usinagem',
                    '5' => 'apontamento_acabamento',
                    '6' => 'apontamento_montagem',
                    '7' => 'apontamento_inspecao',
                    '8' => 'apontamento_embalagem',
                    '9' => 'apontamento_expedicao',
                    '10' => 'apontamento_estoque',
                    '11' => 'apontamento_entregue',
                ];

                foreach(range(1,11) as $status_s){

                    if(!in_array($status_s, $status_id)){
                        if(isset($array_status[$status_s])) {

                            $pedido->{$array_status[$status_s]} = '';
                            if($status_s == 6) {
                                $pedido->apontamento_montagem_torre = '';
                            }

                        }
                    }
                }


                if(empty($pedido->apontamento_usinagem) && empty($pedido->apontamento_acabamento) &&  empty($pedido->apontamento_montagem) &&
                    empty($pedido->apontamento_montagem_torre) && empty($pedido->apontamento_inspecao) && empty($pedido->apontamento_embalagem) &&
                    empty($pedido->apontamento_expedicao) && empty($pedido->apontamento_estoque) && empty($pedido->apontamento_entregue)) {
                        unset($pedidos['classe'][$chave ]);
                }

                $total_tempo_usinagem=$total_tempo_acabamento=$total_tempo_montagem_torre=$total_tempo_montagem=$total_tempo_inspecao=$total_tempo_expedicao='00:00:00';

                $total_tempo_usinagem = $this->somarHoras($total_tempo_usinagem , $pedido->tabelaFichastecnicas->tempo_usinagem);
                $total_tempo_usinagem = $MaquinasController->multiplicarHoras($total_tempo_usinagem,$pedido->qtde);
                $dados_pedido_status[$status]['pedido'][$pedido->id]['usinagem'] = ($pedido->apontamento_usinagem != '') ? $total_tempo_usinagem : '00:00:00';

                $total_tempo_acabamento = $this->somarHoras($total_tempo_acabamento , $pedido->tabelaFichastecnicas->tempo_acabamento);
                $total_tempo_acabamento = $MaquinasController->multiplicarHoras($total_tempo_acabamento,$pedido->qtde);
                $dados_pedido_status[$status]['pedido'][$pedido->id]['acabamento'] = ($pedido->apontamento_acabamento != '') ? $total_tempo_acabamento : '00:00:00';

                $total_tempo_montagem_torre = $this->somarHoras($total_tempo_montagem_torre , $pedido->tabelaFichastecnicas->tempo_montagem_torre);
                $total_tempo_montagem_torre = $MaquinasController->multiplicarHoras($total_tempo_montagem_torre,$pedido->qtde);
                $dados_pedido_status[$status]['pedido'][$pedido->id]['montagem_torre'] = ($pedido->apontamento_montagem_torre != '') ? $total_tempo_montagem_torre : '00:00:00';

                $total_tempo_montagem = $this->somarHoras($total_tempo_montagem , $pedido->tabelaFichastecnicas->tempo_montagem);
                $total_tempo_montagem = $MaquinasController->multiplicarHoras($total_tempo_montagem,$pedido->qtde);

                if($nome_tela == 'geral') {
                    $total_tempo_montagem = $this->somarHoras($total_tempo_montagem, $total_tempo_montagem_torre) ;
                }

                $dados_pedido_status[$status]['pedido'][$pedido->id]['montagem'] = ($pedido->apontamento_montagem != '') ? $total_tempo_montagem : '00:00:00';

                $total_tempo_inspecao = $this->somarHoras($total_tempo_inspecao , $pedido->tabelaFichastecnicas->tempo_inspecao);
                $total_tempo_inspecao = $MaquinasController->multiplicarHoras($total_tempo_inspecao,$pedido->qtde);
                $dados_pedido_status[$status]['pedido'][$pedido->id]['inspecao'] = ($pedido->apontamento_inspecao != '') ? $total_tempo_inspecao : '00:00:00';

                $dados_pedido_status[$status]['totais']['total_tempo_usinagem'] = $this->somarHoras(!empty($dados_pedido_status[$status]['totais']['total_tempo_usinagem']) ? $dados_pedido_status[$status]['totais']['total_tempo_usinagem']: '00:00:00' , $dados_pedido_status[$status]['pedido'][$pedido->id]['usinagem']);
                $dados_pedido_status[$status]['totais']['total_tempo_acabamento'] = $this->somarHoras(!empty($dados_pedido_status[$status]['totais']['total_tempo_acabamento']) ? $dados_pedido_status[$status]['totais']['total_tempo_acabamento'] : "00:00:00", $dados_pedido_status[$status]['pedido'][$pedido->id]['acabamento']);
                $dados_pedido_status[$status]['totais']['total_tempo_montagem_torre'] = $this->somarHoras(!empty($dados_pedido_status[$status]['totais']['total_tempo_montagem_torre']) ? $dados_pedido_status[$status]['totais']['total_tempo_montagem_torre'] : "00:00:00", $dados_pedido_status[$status]['pedido'][$pedido->id]['montagem_torre']);
                $dados_pedido_status[$status]['totais']['total_tempo_montagem'] = $this->somarHoras(!empty($dados_pedido_status[$status]['totais']['total_tempo_montagem']) ? $dados_pedido_status[$status]['totais']['total_tempo_montagem'] : "00:00:00", $dados_pedido_status[$status]['pedido'][$pedido->id]['montagem']);
                $dados_pedido_status[$status]['totais']['total_tempo_inspecao'] = $this->somarHoras(!empty($dados_pedido_status[$status]['totais']['total_tempo_inspecao']) ? $dados_pedido_status[$status]['totais']['total_tempo_inspecao'] : "00:00:00", $dados_pedido_status[$status]['pedido'][$pedido->id]['inspecao']);

                $historicos_pedidos_datas = new HistoricosPedidos();
                $historicos_pedidos_datas = $historicos_pedidos_datas->where('pedidos_id', '=', $pedido->id );
                $historicos_pedidos_datas = $historicos_pedidos_datas->orderBy('created_at', 'desc')->limit(1)->get();
                $dados_pedido_status[$status]['pedido'][$pedido->id]['data_alteracao_status'] = !empty($historicos_pedidos_datas[0]->created_at) ? \Carbon\Carbon::parse($historicos_pedidos_datas[0]->created_at)->format('d/m/Y') : null;

            }

            $dados_pedido_status[$status]['maquinas_usinagens'] = $this->divideHoursIntoDays($dados_pedido_status[$status]['totais']['total_tempo_usinagem'], $total_horas_usinagem_maquinas_dia);
            $dados_pedido_status[$status]['pessoas_acabamento'] = $this->divideHoursAndReturnWorkDays($dados_pedido_status[$status]['totais']['total_tempo_acabamento'], $total_horas_pessoas_acabamento_dia);


            $dados_pedido_status[$status]['pessoas_montagem_torre'] = $this->divideHoursAndReturnWorkDays($dados_pedido_status[$status]['totais']['total_tempo_montagem_torre'], $total_horas_pessoas_pessoas_montagem_torres_dia);

            $dados_pedido_status[$status]['pessoas_montagem'] = $this->divideHoursAndReturnWorkDays($dados_pedido_status[$status]['totais']['total_tempo_montagem'], $total_horas_pessoas_pessoas_montagem_dia);
            $dados_pedido_status[$status]['pessoas_inspecao'] =$this->divideHoursAndReturnWorkDays($dados_pedido_status[$status]['totais']['total_tempo_inspecao'], $total_horas_pessoas_inspecao_dia);

            if($pedidos['id_status'][$chave] <= 4 ){
                $totalGeral['totalGeralusinagens'] = ((!empty($totalGeral['totalGeralusinagens']) ? $totalGeral['totalGeralusinagens'] : '0') + preg_replace('/[^0-9.]/', '', $dados_pedido_status[$status]['maquinas_usinagens']) );
                $totalGeral['totalGeralacabamento'] = ((!empty($totalGeral['totalGeralacabamento']) ? $totalGeral['totalGeralacabamento'] : '0') + preg_replace('/[^0-9.]/', '', $dados_pedido_status[$status]['pessoas_acabamento']) );
                $totalGeral['totalGeralmontagem_torre'] = ((!empty($totalGeral['totalGeralmontagem_torre']) ? $totalGeral['totalGeralmontagem_torre'] : '0') + preg_replace('/[^0-9.]/', '', $dados_pedido_status[$status]['pessoas_montagem_torre']) );
                $totalGeral['totalGeralmontagem'] = ((!empty($totalGeral['totalGeralmontagem']) ? $totalGeral['totalGeralmontagem'] : '0') + preg_replace('/[^0-9.]/', '', $dados_pedido_status[$status]['pessoas_montagem']) );
                $totalGeral['totalGeralinspecao'] = ((!empty($totalGeral['totalGeralinspecao']) ? $totalGeral['totalGeralinspecao'] : '0') + preg_replace('/[^0-9.]/', '', $dados_pedido_status[$status]['pessoas_inspecao']) );
            }
            if($pedidos['id_status'][$chave] == 5 ){
                $totalGeral['totalGeralacabamento'] = ((!empty($totalGeral['totalGeralacabamento']) ? $totalGeral['totalGeralacabamento'] : '0') + preg_replace('/[^0-9.]/', '', $dados_pedido_status[$status]['pessoas_acabamento']) );
                $totalGeral['totalGeralmontagem_torre'] = ((!empty($totalGeral['totalGeralmontagem_torre']) ? $totalGeral['totalGeralmontagem_torre'] : '0') + preg_replace('/[^0-9.]/', '', $dados_pedido_status[$status]['pessoas_montagem_torre']) );
                $totalGeral['totalGeralmontagem'] = ((!empty($totalGeral['totalGeralmontagem']) ? $totalGeral['totalGeralmontagem'] : '0') + preg_replace('/[^0-9.]/', '', $dados_pedido_status[$status]['pessoas_montagem']) );
                $totalGeral['totalGeralinspecao'] = ((!empty($totalGeral['totalGeralinspecao']) ? $totalGeral['totalGeralinspecao'] : '0') + preg_replace('/[^0-9.]/', '', $dados_pedido_status[$status]['pessoas_inspecao']) );
            }
            if($pedidos['id_status'][$chave] == 6 ){
                $totalGeral['totalGeralmontagem_torre'] = ((!empty($totalGeral['totalGeralmontagem_torre']) ? $totalGeral['totalGeralmontagem_torre'] : '0') + preg_replace('/[^0-9.]/', '', $dados_pedido_status[$status]['pessoas_montagem_torre']) );
                $totalGeral['totalGeralmontagem'] = ((!empty($totalGeral['totalGeralmontagem']) ? $totalGeral['totalGeralmontagem'] : '0') + preg_replace('/[^0-9.]/', '', $dados_pedido_status[$status]['pessoas_montagem']) );
                $totalGeral['totalGeralinspecao'] = ((!empty($totalGeral['totalGeralinspecao']) ? $totalGeral['totalGeralinspecao'] : '0') + preg_replace('/[^0-9.]/', '', $dados_pedido_status[$status]['pessoas_inspecao']) );
            }
            if($pedidos['id_status'][$chave] == 7 ){
                $totalGeral['totalGeralinspecao'] = ((!empty($totalGeral['totalGeralinspecao']) ? $totalGeral['totalGeralinspecao'] : '0') + preg_replace('/[^0-9.]/', '', $dados_pedido_status[$status]['pessoas_inspecao']) );
            }
        }

        $tela = 'followup-realizado';
        $nome_da_tela ='followup realizado';

        $data = array(
            'tela' => $tela,
            'nome_tela' => $nome_da_tela,
            'dados_pedido_status' => $dados_pedido_status,
            'request' => $request,
            'status' => $this->getAllStatus(),
            'rotaIncluir' => 'incluir-pedidos',
            'rotaAlterar' => 'alterar-pedidos'
        );


        return view('pedidos', $data);
    }

    /**
    * Show the application dashboard.
    *
    * @return \Illuminate\Contracts\Support\Renderable OR json
    */
    public function followupgerencial(Request $request) {

        $status_id = $request->input('status_id');
        $filtrado = 0;
        $pedidos = DB::table('pedidos')
            ->distinct()
            ->join('status', 'pedidos.status_id', '=', 'status.id')
            ->join('ficha_tecnica', 'ficha_tecnica.id', '=', 'pedidos.fichatecnica_id')
            ->join('pessoas', 'pessoas.id', '=', 'pedidos.pessoas_id')
            ->orderby('status_id', 'desc')
            ->orderby('data_entrega');

        if(!empty($request->input('data_apontamento'))) {
            $pedidos = $pedidos->select('pedidos.*',
            'ficha_tecnica.ep',
            'pessoas.nome_cliente',
            'status.nome',
            'historicos_etapas.created_at as historicos_etapas_created_at',
            'historicos_pedidos.created_at as historicos_pedidos_created_at',
            'status.id as id_status');

            $pedidos = $pedidos->leftJoin('historicos_etapas', function ($join) use ($status_id, $request) {
                $join = $join->on('pedidos.id', '=', 'historicos_etapas.pedidos_id');
                $join = $join->where('historicos_etapas.etapas_pedidos_id', '=', 4)
                     ->whereIn('historicos_etapas.status_id', $status_id);
                if(!empty($request->input('data_apontamento')) && !empty($request->input('data_apontamento_fim') )) {
                    $join = $join->whereBetween('historicos_etapas.created_at', [DateHelpers::formatDate_dmY($request->input('data_apontamento')) . ' 00:00:01', DateHelpers::formatDate_dmY($request->input('data_apontamento_fim')) . ' 23:59:59']);

                }
                if(!empty($request->input('data_apontamento')) && empty($request->input('data_apontamento_fim') )) {
                    $join = $join->where('historicos_etapas.created_at', '>=', DateHelpers::formatDate_dmY($request->input('data_apontamento')) .' 00:00:01');
                }
                if(empty($request->input('data_apontamento')) && !empty($request->input('data_apontamento_fim') )) {
                    $join = $join->where('historicos_etapas.created_at', '<=', DateHelpers::formatDate_dmY($request->input('data_apontamento_fim')) .' 00:00:01');

                }
            });



            $pedidos  = $pedidos->leftJoin('historicos_pedidos', function ($join) use ($status_id, $request) {
                $join = $join->on('pedidos.id', '=', 'historicos_pedidos.pedidos_id');

                $join = $join->whereIn('historicos_pedidos.status_id', $status_id);
                if(!empty($request->input('data_apontamento')) && !empty($request->input('data_apontamento_fim') )) {
                    $join = $join->whereBetween('historicos_pedidos.created_at', [DateHelpers::formatDate_dmY($request->input('data_apontamento')) . ' 00:00:00', DateHelpers::formatDate_dmY($request->input('data_apontamento_fim')) . ' 23:59:59']);
                }
                if(!empty($request->input('data_apontamento')) && empty($request->input('data_apontamento_fim') )) {
                    $join = $join->where('historicos_pedidos.created_at', '>=', DateHelpers::formatDate_dmY($request->input('data_apontamento')) . ' 00:00:00');
                }
                if(empty($request->input('data_apontamento')) && !empty($request->input('data_apontamento_fim') )) {
                    $join = $join->where('historicos_pedidos.created_at', '<=', DateHelpers::formatDate_dmY($request->input('data_apontamento_fim')) . ' 00:00:00');
                }
            });


            $filtrado++;
        } else {
            $pedidos = $pedidos->select('pedidos.*',
            'ficha_tecnica.ep',
            'pessoas.nome_cliente',
            'status.nome',
            'status.id as id_status');
        }

        if(!empty($request->input('os'))) {
            $pedidos = $pedidos->where('os', '=', $request->input('os'));
            $filtrado++;
        }
        if(!empty($request->input('ep'))) {
            $pedidos = $pedidos->where('ficha_tecnica.ep', '=', $request->input('ep'));
            $filtrado++;
        }
        if(!empty($request->input('id'))) {
            $pedidos = $pedidos->where('id', '=', $request->input('id'));
            $filtrado++;
        }

        // if($request->input('tipo_consulta') == 'G') {


            if(!empty($request->input('status_id'))) {
                $pedidos = $pedidos->whereIn('pedidos.status_id', $status_id);
                $filtrado++;
            }

            if(!empty($request->input('data_gerado')) && !empty($request->input('data_gerado_fim') )) {
                $pedidos = $pedidos->whereBetween('data_gerado', [DateHelpers::formatDate_dmY($request->input('data_gerado')), DateHelpers::formatDate_dmY($request->input('data_gerado_fim'))]);
                $filtrado++;
            }
            if(!empty($request->input('data_gerado')) && empty($request->input('data_gerado_fim') )) {
                $pedidos = $pedidos->where('data_gerado', '>=', DateHelpers::formatDate_dmY($request->input('data_gerado')));
                $filtrado++;
            }
            if(empty($request->input('data_gerado')) && !empty($request->input('data_gerado_fim') )) {
                $pedidos = $pedidos->where('data_gerado', '<=', DateHelpers::formatDate_dmY($request->input('data_gerado_fim')));
                $filtrado++;
            }

            if(!empty($request->input('data_entrega')) && !empty($request->input('data_entrega_fim') )) {
                $pedidos = $pedidos->whereBetween('data_entrega', [DateHelpers::formatDate_dmY($request->input('data_entrega')), DateHelpers::formatDate_dmY($request->input('data_entrega_fim'))]);
                $filtrado++;
            }
            if(!empty($request->input('data_entrega')) && empty($request->input('data_entrega_fim') )) {
                $pedidos = $pedidos->where('data_entrega', '>=', DateHelpers::formatDate_dmY($request->input('data_entrega')));
                $filtrado++;
            }
            if(empty($request->input('data_entrega')) && !empty($request->input('data_entrega_fim') )) {
                $pedidos = $pedidos->where('data_entrega', '<=', DateHelpers::formatDate_dmY($request->input('data_entrega_fim')));
                $filtrado++;
            }
        // }

        $pedidos = $pedidos->where('pedidos.status', '=', 'A');


        $pedidos_encontrados = [];


        if ($filtrado > 0) {

            $pedidos = $pedidos->get();


            foreach ($pedidos as $key => $value) {

                if($request->input('tipo_consulta') == 'R' || $request->input('tipo_consulta') == 'C') {

                    if(empty($value->historicos_etapas_created_at) && empty($value->historicos_pedidos_created_at)) {
                        continue;
                    };

                }


                $pedidos_encontrados[] = $value->id;
            }
        }

        if($request->input('somente_dados')) {
            return $pedidos_encontrados;
        }

        $request->merge(['tipo_consulta' => 'G']);
        $tela = 'pesquisa-gerencial';
        $nome_tela = 'pesquisa gerêncial';
        $data = array(
            'tela' => $tela,
            'nome_tela' =>$nome_tela,
            'pedidos_encontrados' => $pedidos_encontrados,
            'pedidos' => $pedidos,
            'request' => $request,
            'status' => $this->getAllStatus(),
            'rotaIncluir' => 'incluir-pedidos',
            'rotaAlterar' => 'alterar-pedidos'
        );


        return view('pedidos', $data);
    }

    public function followupgerencialDados(Request $request)
    {
        $pedidos = new Pedidos();
        $AjaxOrcamentosController = new AjaxOrcamentosController();
        $nome_tela = !empty($request->input('nome_tela')) ? $request->input('nome_tela') : 'tempos' ;

        if(empty($request->input('pedidos_encontrados'))) {
            return redirect()->route('followup');
        }
        $pedidos_encontrados = json_decode($request->input('pedidos_encontrados'));

        $pedidos = $pedidos::with('tabelaStatus', 'tabelaFichastecnicas', 'tabelaPessoas')
        ->wherein('id', $pedidos_encontrados)
        ->orderby('status_id', 'desc')
        ->orderby('data_entrega')->get();

        $total_tempo_usinagem=$total_tempo_acabamento=$total_tempo_montagem=$total_tempo_inspecao='00:00:00';
        $dados_pedido_status=[];

        foreach ($pedidos as $pedido) {
            $dados_pedido_status[$pedido->tabelaStatus->nome]['classe'][] = $pedido;
            $dados_pedido_status[$pedido->tabelaStatus->nome]['id_status'][] = $pedido->tabelaStatus->id;
        }

        $MaquinasController = new MaquinasController();

        $Maquinas = new Maquinas();

        $maquinas = $Maquinas->get();

        $qtde_maquinas =$maquinas[0]->qtde_maquinas;
        $horas_maquinas =$maquinas[0]->horas_maquinas;
        $pessoas_acabamento =$maquinas[0]->pessoas_acabamento;
        $pessoas_montagem =$maquinas[0]->pessoas_montagem;
        $pessoas_montagem_torres =$maquinas[0]->pessoas_montagem_torres;
        $pessoas_inspecao =$maquinas[0]->pessoas_inspecao;
        $horas_dia =$maquinas[0]->horas_dia;
        $total_horas_usinagem_maquinas_dia = $this->multiplyTimeByInteger($horas_maquinas, $qtde_maquinas);
        $total_horas_pessoas_acabamento_dia = $this->multiplyTimeByInteger($horas_dia, $pessoas_acabamento);
        $total_horas_pessoas_pessoas_montagem_dia = $this->multiplyTimeByInteger($horas_dia, $pessoas_montagem);
        $total_horas_pessoas_pessoas_montagem_torres_dia = $this->multiplyTimeByInteger($horas_dia, $pessoas_montagem_torres);
        $total_horas_pessoas_inspecao_dia = $this->multiplyTimeByInteger($horas_dia, $pessoas_inspecao);
        $totalGeral = [];

        // $fichatecnicas = new Fichastecnicas();

        foreach ($dados_pedido_status as $status => $pedidos) {

            $totais = [];
            foreach ($pedidos['classe'] as $chave =>  $pedido) {


                // calcula dados chapa e valores

                $percentuais =[];

                // $fichatecnica= $fichatecnicas->where('id', '=', $pedido->ep)->get();

                $consumoMateriais = new ConsumoMateriaisController();
                $fichatecnicasitens = new Fichastecnicasitens();
                $fichatecnicasitens= $fichatecnicasitens::with('tabelaMateriais')->where('fichatecnica_id', '=', $pedido->fichatecnica_id)->orderByRaw("CASE WHEN blank='' THEN 1 ELSE 0 END ASC")->orderBy('blank','ASC')->get();

                $historicos_pedidos_datas = new HistoricosPedidos();
                $historicos_pedidos_datas = $historicos_pedidos_datas->where('pedidos_id', '=', $pedido->id );
                $historicos_pedidos_datas = $historicos_pedidos_datas->orderBy('created_at', 'desc')->limit(1)->get();
                $dados_pedido_status[$status]['pedido'][$pedido->id]['data_alteracao_status'] = !empty($historicos_pedidos_datas[0]->created_at) ? \Carbon\Carbon::parse($historicos_pedidos_datas[0]->created_at)->format('d/m/Y') : null;

                $tempo_fresa_total = '00:00:00';

                foreach ($fichatecnicasitens as $key => $fichatecnicasitem) {
                    $tempo_usinagem = $fichatecnicasitem->tempo_usinagem;
                    $tempo_usinagem = $this->multiplyTimeByInteger($tempo_usinagem,$fichatecnicasitem->qtde_blank);
                    $tempo_fresa_total = $this->somarHoras($tempo_fresa_total, $tempo_usinagem);
                }

                $Total_mo=$Total_mp=$Total_ci=0;

                foreach ($fichatecnicasitens as $key => $fichatecnicasitem) {

                    $tempo_usinagem = $fichatecnicasitem->tempo_usinagem;
                    $tempo_usinagem = $this->multiplyTimeByInteger($tempo_usinagem,$fichatecnicasitem->qtde_blank);

                    $percentuais[$key]['percentual']=round($this::calcularPorcentagemEntreMinutos($tempo_usinagem, $tempo_fresa_total));


                    $pecas = [
                        'width' => $fichatecnicasitem->medidax + 2,
                        'height'=> $fichatecnicasitem->mediday + 10,
                    ];

                    $chapa = [
                        'sheetWidth' => $fichatecnicasitem->tabelaMateriais->unidadex - 20,
                        'sheetHeight'=> $fichatecnicasitem->tabelaMateriais->unidadey - 20
                    ];

                    if($fichatecnicasitem->tabelaMateriais->peca_padrao == 2){

                        $blank_por_chapa = $consumoMateriais->calculaPecas($pecas, $chapa);
                    } else {

                        $blank_por_chapa = $fichatecnicasitem->qtde_blank;
                    }

                        $blank = $fichatecnicasitem->blank;
                        $tmp = $fichatecnicasitem->tempo_usinagem;
                        $val_chapa = $fichatecnicasitem->tabelaMateriais->valor;
                        $qtde_CH = $blank_por_chapa;
                       
                        $qtde_ = $fichatecnicasitem->qtde_blank;
                        $MP = '';
                        if($blank != '') {

                            if(!empty($val_chapa) && !empty($qtde_CH)){

                                $MP = $val_chapa/$qtde_CH*$qtde_;

                                $tempo = $this->multiplyTimeByInteger($tmp,  $qtde_);
                                $MO = $AjaxOrcamentosController->calcularValor('480.00', $tempo);
                                $Total_mo = $Total_mo + DateHelpers::formatFloatValue($MO);
                            } else {
                                $Total_mo = 0;
                            }


                        }
                        else {
                            $MP = $val_chapa*$qtde_CH;
                            $MO = 0;
                        }
                        
                        try{
                            $Total_mp = $Total_mp + (($MP !='') ? $MP : 0);
                        } catch(\Exception $e){
                            dd($pedido);                           
                        }
                            

                        


                    $Total_ci = $Total_mp + $Total_mo;
                    $Total_mp_2 = $Total_mp * 0.37;
                    $desc_10_1 = $Total_ci * 1.66;
                    $desc_20_1 = $Total_ci * 1.50;
                    $desc_30_1 = $Total_ci * 1.35;
                    $desc_40_1 = $Total_ci * 1.25;
                    $desc_50_1 = $Total_ci * 1.16;

                    $totais = [
                        'subTotalMO' => number_format($Total_mo, 2, ',',''),
                        'subTotalMP' => number_format($Total_mp, 2, ',',''),
                        'subTotalCI'=> number_format($Total_ci, 2, ',',''),
                        'desc_10_total' => number_format($desc_10_1 + $Total_mp_2, 2, ',',''),
                        'desc_20_total' => number_format($desc_20_1 + $Total_mp_2, 2, ',',''),
                        'desc_30_total' => number_format($desc_30_1 + $Total_mp_2, 2, ',',''),
                        'desc_40_total' => number_format($desc_40_1 + $Total_mp_2, 2, ',',''),
                        'desc_50_total' => number_format($desc_50_1 + $Total_mp_2, 2, ',',''),
                    ];

                }
                $dados_pedido_status[$status]['pedido'][$pedido->id]['totais'] = $totais;
                // $valores = $calcularOrcamento->ajaxCalculaOrcamentos();

                $total_tempo_usinagem=$total_tempo_acabamento=$total_tempo_montagem_torre=$total_tempo_montagem=$total_tempo_inspecao='00:00:00';

                $total_tempo_usinagem = $this->somarHoras($total_tempo_usinagem , $pedido->tabelaFichastecnicas->tempo_usinagem);
                $total_tempo_usinagem = $MaquinasController->multiplicarHoras($total_tempo_usinagem, $pedido->qtde);
                $dados_pedido_status[$status]['pedido'][$pedido->id]['usinagem'] = $total_tempo_usinagem;
                $dados_pedido_status[$status]['pedido'][$pedido->id]['valor_unitario'] = $pedido->valor_unitario_adv ;

                $total_tempo_acabamento = $this->somarHoras($total_tempo_acabamento , $pedido->tabelaFichastecnicas->tempo_acabamento);
                $total_tempo_acabamento = $MaquinasController->multiplicarHoras($total_tempo_acabamento,$pedido->qtde);
                $dados_pedido_status[$status]['pedido'][$pedido->id]['acabamento'] = $total_tempo_acabamento;

                $total_tempo_montagem_torre = $this->somarHoras($total_tempo_montagem_torre , $pedido->tabelaFichastecnicas->tempo_montagem_torre);
                $total_tempo_montagem_torre = $MaquinasController->multiplicarHoras($total_tempo_montagem_torre,$pedido->qtde);

                $dados_pedido_status[$status]['pedido'][$pedido->id]['montagem_torre'] = $total_tempo_montagem_torre;
                $total_tempo_montagem = $this->somarHoras($total_tempo_montagem , $pedido->tabelaFichastecnicas->tempo_montagem);
                $total_tempo_montagem = $MaquinasController->multiplicarHoras($total_tempo_montagem,$pedido->qtde);

                if($nome_tela == 'geral') {
                    $total_tempo_montagem = $this->somarHoras($total_tempo_montagem, $total_tempo_montagem_torre) ;
                }

                $dados_pedido_status[$status]['pedido'][$pedido->id]['montagem'] = $total_tempo_montagem;

                $total_tempo_inspecao = $this->somarHoras($total_tempo_inspecao , $pedido->tabelaFichastecnicas->tempo_inspecao);
                $total_tempo_inspecao = $MaquinasController->multiplicarHoras($total_tempo_inspecao,$pedido->qtde);
                $dados_pedido_status[$status]['pedido'][$pedido->id]['inspecao'] = $total_tempo_inspecao;

                $dados_pedido_status[$status]['totais']['total_tempo_usinagem'] = $this->somarHoras(!empty($dados_pedido_status[$status]['totais']['total_tempo_usinagem']) ? $dados_pedido_status[$status]['totais']['total_tempo_usinagem']: '00:00:00' , $total_tempo_usinagem);
                $dados_pedido_status[$status]['totais']['total_tempo_acabamento'] = $this->somarHoras(!empty($dados_pedido_status[$status]['totais']['total_tempo_acabamento']) ? $dados_pedido_status[$status]['totais']['total_tempo_acabamento'] : "00:00:00", $total_tempo_acabamento);
                $dados_pedido_status[$status]['totais']['total_tempo_montagem_torre'] = $this->somarHoras(!empty($dados_pedido_status[$status]['totais']['total_tempo_montagem_torre']) ? $dados_pedido_status[$status]['totais']['total_tempo_montagem_torre'] : "00:00:00", $total_tempo_montagem_torre);
                $dados_pedido_status[$status]['totais']['total_tempo_montagem'] = $this->somarHoras(!empty($dados_pedido_status[$status]['totais']['total_tempo_montagem']) ? $dados_pedido_status[$status]['totais']['total_tempo_montagem'] : "00:00:00", $total_tempo_montagem);
                $dados_pedido_status[$status]['totais']['total_tempo_inspecao'] = $this->somarHoras(!empty($dados_pedido_status[$status]['totais']['total_tempo_inspecao']) ? $dados_pedido_status[$status]['totais']['total_tempo_inspecao'] : "00:00:00", $total_tempo_inspecao);
            }

            $dados_pedido_status[$status]['maquinas_usinagens'] = $this->divideHoursIntoDays($dados_pedido_status[$status]['totais']['total_tempo_usinagem'], $total_horas_usinagem_maquinas_dia);
            $dados_pedido_status[$status]['pessoas_acabamento'] = $this->divideHoursAndReturnWorkDays($dados_pedido_status[$status]['totais']['total_tempo_acabamento'], $total_horas_pessoas_acabamento_dia);


            $dados_pedido_status[$status]['pessoas_montagem_torre'] = $this->divideHoursAndReturnWorkDays($dados_pedido_status[$status]['totais']['total_tempo_montagem_torre'], $total_horas_pessoas_pessoas_montagem_torres_dia);

            $dados_pedido_status[$status]['pessoas_montagem'] = $this->divideHoursAndReturnWorkDays($dados_pedido_status[$status]['totais']['total_tempo_montagem'], $total_horas_pessoas_pessoas_montagem_dia);
            $dados_pedido_status[$status]['pessoas_inspecao'] =$this->divideHoursAndReturnWorkDays($dados_pedido_status[$status]['totais']['total_tempo_inspecao'], $total_horas_pessoas_inspecao_dia);

            if($pedidos['id_status'][$chave] <= 4 ){
                $totalGeral['totalGeralusinagens'] = ((!empty($totalGeral['totalGeralusinagens']) ? $totalGeral['totalGeralusinagens'] : '0') + preg_replace('/[^0-9.]/', '', $dados_pedido_status[$status]['maquinas_usinagens']) );
                $totalGeral['totalGeralacabamento'] = ((!empty($totalGeral['totalGeralacabamento']) ? $totalGeral['totalGeralacabamento'] : '0') + preg_replace('/[^0-9.]/', '', $dados_pedido_status[$status]['pessoas_acabamento']) );
                $totalGeral['totalGeralmontagem_torre'] = ((!empty($totalGeral['totalGeralmontagem_torre']) ? $totalGeral['totalGeralmontagem_torre'] : '0') + preg_replace('/[^0-9.]/', '', $dados_pedido_status[$status]['pessoas_montagem_torre']) );
                $totalGeral['totalGeralmontagem'] = ((!empty($totalGeral['totalGeralmontagem']) ? $totalGeral['totalGeralmontagem'] : '0') + preg_replace('/[^0-9.]/', '', $dados_pedido_status[$status]['pessoas_montagem']) );
                $totalGeral['totalGeralinspecao'] = ((!empty($totalGeral['totalGeralinspecao']) ? $totalGeral['totalGeralinspecao'] : '0') + preg_replace('/[^0-9.]/', '', $dados_pedido_status[$status]['pessoas_inspecao']) );
            }
            if($pedidos['id_status'][$chave] == 5 ){
                $totalGeral['totalGeralacabamento'] = ((!empty($totalGeral['totalGeralacabamento']) ? $totalGeral['totalGeralacabamento'] : '0') + preg_replace('/[^0-9.]/', '', $dados_pedido_status[$status]['pessoas_acabamento']) );
                $totalGeral['totalGeralmontagem_torre'] = ((!empty($totalGeral['totalGeralmontagem_torre']) ? $totalGeral['totalGeralmontagem_torre'] : '0') + preg_replace('/[^0-9.]/', '', $dados_pedido_status[$status]['pessoas_montagem_torre']) );
                $totalGeral['totalGeralmontagem'] = ((!empty($totalGeral['totalGeralmontagem']) ? $totalGeral['totalGeralmontagem'] : '0') + preg_replace('/[^0-9.]/', '', $dados_pedido_status[$status]['pessoas_montagem']) );
                $totalGeral['totalGeralinspecao'] = ((!empty($totalGeral['totalGeralinspecao']) ? $totalGeral['totalGeralinspecao'] : '0') + preg_replace('/[^0-9.]/', '', $dados_pedido_status[$status]['pessoas_inspecao']) );
            }
            if($pedidos['id_status'][$chave] == 6 ){
                $totalGeral['totalGeralmontagem_torre'] = ((!empty($totalGeral['totalGeralmontagem_torre']) ? $totalGeral['totalGeralmontagem_torre'] : '0') + preg_replace('/[^0-9.]/', '', $dados_pedido_status[$status]['pessoas_montagem_torre']) );
                $totalGeral['totalGeralmontagem'] = ((!empty($totalGeral['totalGeralmontagem']) ? $totalGeral['totalGeralmontagem'] : '0') + preg_replace('/[^0-9.]/', '', $dados_pedido_status[$status]['pessoas_montagem']) );
                $totalGeral['totalGeralinspecao'] = ((!empty($totalGeral['totalGeralinspecao']) ? $totalGeral['totalGeralinspecao'] : '0') + preg_replace('/[^0-9.]/', '', $dados_pedido_status[$status]['pessoas_inspecao']) );
            }
            if($pedidos['id_status'][$chave] == 7 ){
                $totalGeral['totalGeralinspecao'] = ((!empty($totalGeral['totalGeralinspecao']) ? $totalGeral['totalGeralinspecao'] : '0') + preg_replace('/[^0-9.]/', '', $dados_pedido_status[$status]['pessoas_inspecao']) );
            }



        }

        $tela = 'followup-gerencial';
        $nome_da_tela ='followup gerencial';
        if($nome_tela == 'geral') {
            $tela = 'followup-detalhes-geral';
            $nome_da_tela ='followup geral';
        }

        $dias_alerta_maquinas = $this->getMaquinas();
        
        $data = array(
            'tela' => $tela,
            'nome_tela' => $nome_da_tela,
            'dados_pedido_status' => $dados_pedido_status,
            'totalGeral' => $totalGeral,
            'request' => $request,
            'maquinas' => $dias_alerta_maquinas,
            'status' => $this->getAllStatus(),
            'rotaIncluir' => 'incluir-pedidos',
            'rotaAlterar' => 'alterar-pedidos'
        );


        return view('pedidos', $data);
    }


    public function followupCicloProducao(Request $request)
    {
        $pedidos = new Pedidos();

        $nome_tela = !empty($request->input('nome_tela')) ? $request->input('nome_tela') : 'tempos' ;

        $data_apontamento = $request->input('data_apontamento', 0);
        $data_apontamento_fim = $request->input('data_apontamento_fim', 0);

        if(empty($request->input('pedidos_encontrados'))) {
            return redirect()->route('followup');
        }
        $pedidos_encontrados = json_decode($request->input('pedidos_encontrados'));

        $pedidos = $pedidos::with('tabelaStatus', 'tabelaFichastecnicas', 'tabelaPessoas')
        ->wherein('id', $pedidos_encontrados)
        ->orderby('status_id', 'desc')
        ->orderby('data_entrega')->get();

        $dados_pedido_status=[];

        foreach ($pedidos as &$pedido) {

            $historicos_etapas = new HistoricosEtapas();

            $historicos_etapas = $historicos_etapas->whereIn('status_id', [4,5,6,7,8]);
            $historicos_etapas = $historicos_etapas->where('etapas_pedidos_id', '=', 4 );
            $historicos_etapas = $historicos_etapas->where('pedidos_id', '=', $pedido->id);

            if(!empty($request->input('data_apontamento')) && !empty($request->input('data_apontamento_fim') )) {
                $historicos_etapas = $historicos_etapas->whereBetween('created_at', [DateHelpers::formatDate_dmY($request->input('data_apontamento')).' 00:00:01' , DateHelpers::formatDate_dmY($request->input('data_apontamento_fim')).' 23:59:59']);
            }
            if(!empty($request->input('data_apontamento')) && empty($request->input('data_apontamento_fim') )) {
                $historicos_etapas = $historicos_etapas->where('created_at', '>=', DateHelpers::formatDate_dmY($request->input('data_apontamento')).' 00:00:01');
            }
            if(empty($request->input('data_apontamento')) && !empty($request->input('data_apontamento_fim') )) {
                $historicos_etapas = $historicos_etapas->where('created_at', '<=', DateHelpers::formatDate_dmY($request->input('data_apontamento_fim')).' 00:00:01');
            }
            $historicos_etapas = $historicos_etapas->get();

            $historicos_etapas = $historicos_etapas->toArray();

            // dd($historicos_etapas);
            $historicos_apontamentos = [];
            // $historicos_etapas
            foreach ($historicos_etapas as $key => $historico_etapa) {
                $historicos_apontamentos[$historico_etapa['status_id']] = [
                    'data_apontamento' => \Carbon\Carbon::parse($historico_etapa['created_at'])->format('Y-m-d'),
                    'torre' => ($historico_etapa['select_tipo_manutencao'] == 'A' ? 0 : 1)
                ];
            }

            $pedido->apontamento_usinagem = !empty($historicos_apontamentos[4]['data_apontamento']) ? $historicos_apontamentos[4]['data_apontamento'] : null;
            $pedido->apontamento_acabamento = !empty($historicos_apontamentos[5]['data_apontamento']) ? $historicos_apontamentos[5]['data_apontamento'] : null;

            $pedido->apontamento_montagem = !empty($historicos_apontamentos[6]['data_apontamento']) ? $historicos_apontamentos[6]['data_apontamento'] : null;

            $pedido->apontamento_inspecao = !empty($historicos_apontamentos[7]['data_apontamento']) ? $historicos_apontamentos[7]['data_apontamento'] : null;
            $pedido->apontamento_embalagem = !empty($historicos_apontamentos[8]['data_apontamento']) ? $historicos_apontamentos[8]['data_apontamento'] : null;

            $historicos_pedidos_datas = new HistoricosPedidos();
            $historicos_pedidos_datas = $historicos_pedidos_datas->where('pedidos_id', '=', $pedido->id );
            $historicos_pedidos_datas = $historicos_pedidos_datas->orderBy('created_at', 'desc')->limit(1)->get();
            $pedido->data_alteracao_status = !empty($historicos_pedidos_datas[0]->created_at) ? \Carbon\Carbon::parse($historicos_pedidos_datas[0]->created_at)->format('d/m/Y') : null;

            $historicos_pedidos = new HistoricosPedidos();
            $historicos_pedidos = $historicos_pedidos->whereIn('status_id', [9,10,11]);
            $historicos_pedidos = $historicos_pedidos->where('pedidos_id', '=', $pedido->id );

            if(!empty($request->input('data_apontamento')) && !empty($request->input('data_apontamento_fim') )) {
                $historicos_pedidos = $historicos_pedidos->whereBetween('created_at', [DateHelpers::formatDate_dmY($request->input('data_apontamento')) . ' 00:00:00', DateHelpers::formatDate_dmY($request->input('data_apontamento_fim')). ' 23:59:59']);
            }
            if(!empty($request->input('data_apontamento')) && empty($request->input('data_apontamento_fim') )) {
                $historicos_pedidos = $historicos_pedidos->where('created_at', '>=', DateHelpers::formatDate_dmY($request->input('data_apontamento')) . ' 00:00:00');
            }
            if(empty($request->input('data_apontamento')) && !empty($request->input('data_apontamento_fim') )) {
                $historicos_pedidos = $historicos_pedidos->where('created_at', '<=', DateHelpers::formatDate_dmY($request->input('data_apontamento_fim')) . ' 00:00:00');
            }
            $historicos_pedidos = $historicos_pedidos->get();
            $historicos_pedidos = $historicos_pedidos->toArray();


            $historicos_apontamentos = [];
            foreach ($historicos_pedidos as $key => $historicos_pedido) {
                $historicos_apontamentos[$historicos_pedido['status_id']] = [
                    'data_apontamento' =>  \Carbon\Carbon::parse($historicos_pedido['created_at'])->format('Y-m-d'),
                ];
            }

            $pedido->apontamento_expedicao = !empty($historicos_apontamentos[9]['data_apontamento']) ? $historicos_apontamentos[9]['data_apontamento'] : null;
            $pedido->apontamento_estoque = !empty($historicos_apontamentos[10]['data_apontamento']) ? $historicos_apontamentos[10]['data_apontamento'] : null;
            $pedido->apontamento_entregue = !empty($historicos_apontamentos[11]['data_apontamento']) ? $historicos_apontamentos[11]['data_apontamento'] : null;


            $dados_pedido_status[$pedido->tabelaStatus->nome]['classe'][] = $pedido;
            $dados_pedido_status[$pedido->tabelaStatus->nome]['id_status'][] = $pedido->tabelaStatus->id;
        }

        $MaquinasController = new MaquinasController();

        $Maquinas = new Maquinas();

        $maquinas = $Maquinas->get();

        $qtde_maquinas =$maquinas[0]->qtde_maquinas;
        $horas_maquinas =$maquinas[0]->horas_maquinas;
        $pessoas_acabamento =$maquinas[0]->pessoas_acabamento;
        $pessoas_montagem =$maquinas[0]->pessoas_montagem;
        $pessoas_montagem_torres =$maquinas[0]->pessoas_montagem_torres;
        $pessoas_inspecao =$maquinas[0]->pessoas_inspecao;
        $prazo_entrega =$maquinas[0]->prazo_entrega;
        $horas_dia =$maquinas[0]->horas_dia;
        $total_horas_usinagem_maquinas_dia = $this->multiplyTimeByInteger($horas_maquinas, $qtde_maquinas);
        $total_horas_pessoas_acabamento_dia = $this->multiplyTimeByInteger($horas_dia, $pessoas_acabamento);
        $total_horas_pessoas_pessoas_montagem_dia = $this->multiplyTimeByInteger($horas_dia, $pessoas_montagem);
        $total_horas_pessoas_pessoas_montagem_torres_dia = $this->multiplyTimeByInteger($horas_dia, $pessoas_montagem_torres);
        $total_horas_pessoas_inspecao_dia = $this->multiplyTimeByInteger($horas_dia, $pessoas_inspecao);
        $totalGeral = [];
        foreach ($dados_pedido_status as $status => $pedidos) {


            foreach ($pedidos['classe'] as $chave =>  $pedido) {


                $dataEntrega = \Carbon\Carbon::parse($pedido->data_entrega); // Converte a data de entrega para Carbon
                $prazoEntrega = (int) $prazo_entrega; // Certifica-se que o prazo é um número de dias
                $dataFinal = $dataEntrega->subDays($prazoEntrega);

                $dados_pedido_status[$status]['pedido'][$pedido->id]['data_prazo'] = $dataFinal;

                $total_tempo_usinagem=$total_tempo_acabamento=$total_tempo_montagem_torre=$total_tempo_montagem=$total_tempo_inspecao='00:00:00';

                $total_tempo_usinagem = $this->somarHoras($total_tempo_usinagem , $pedido->tabelaFichastecnicas->tempo_usinagem);
                $total_tempo_usinagem = $MaquinasController->multiplicarHoras($total_tempo_usinagem,$pedido->qtde);
                $dados_pedido_status[$status]['pedido'][$pedido->id]['usinagem'] = $total_tempo_usinagem;

                $total_tempo_acabamento = $this->somarHoras($total_tempo_acabamento , $pedido->tabelaFichastecnicas->tempo_acabamento);
                $total_tempo_acabamento = $MaquinasController->multiplicarHoras($total_tempo_acabamento,$pedido->qtde);
                $dados_pedido_status[$status]['pedido'][$pedido->id]['acabamento'] = $total_tempo_acabamento;

                $total_tempo_montagem_torre = $this->somarHoras($total_tempo_montagem_torre , $pedido->tabelaFichastecnicas->tempo_montagem_torre);
                $total_tempo_montagem_torre = $MaquinasController->multiplicarHoras($total_tempo_montagem_torre,$pedido->qtde);

                $dados_pedido_status[$status]['pedido'][$pedido->id]['montagem_torre'] = $total_tempo_montagem_torre;
                $total_tempo_montagem = $this->somarHoras($total_tempo_montagem , $pedido->tabelaFichastecnicas->tempo_montagem);
                $total_tempo_montagem = $MaquinasController->multiplicarHoras($total_tempo_montagem,$pedido->qtde);

                if($nome_tela == 'geral') {
                    $total_tempo_montagem = $this->somarHoras($total_tempo_montagem, $total_tempo_montagem_torre) ;
                }

                $dados_pedido_status[$status]['pedido'][$pedido->id]['montagem'] = $total_tempo_montagem;

                $total_tempo_inspecao = $this->somarHoras($total_tempo_inspecao , $pedido->tabelaFichastecnicas->tempo_inspecao);
                $total_tempo_inspecao = $MaquinasController->multiplicarHoras($total_tempo_inspecao,$pedido->qtde);
                $dados_pedido_status[$status]['pedido'][$pedido->id]['inspecao'] = $total_tempo_inspecao;

                $dados_pedido_status[$status]['totais']['total_tempo_usinagem'] = $this->somarHoras(!empty($dados_pedido_status[$status]['totais']['total_tempo_usinagem']) ? $dados_pedido_status[$status]['totais']['total_tempo_usinagem']: '00:00:00' , $total_tempo_usinagem);
                $dados_pedido_status[$status]['totais']['total_tempo_acabamento'] = $this->somarHoras(!empty($dados_pedido_status[$status]['totais']['total_tempo_acabamento']) ? $dados_pedido_status[$status]['totais']['total_tempo_acabamento'] : "00:00:00", $total_tempo_acabamento);
                $dados_pedido_status[$status]['totais']['total_tempo_montagem_torre'] = $this->somarHoras(!empty($dados_pedido_status[$status]['totais']['total_tempo_montagem_torre']) ? $dados_pedido_status[$status]['totais']['total_tempo_montagem_torre'] : "00:00:00", $total_tempo_montagem_torre);
                $dados_pedido_status[$status]['totais']['total_tempo_montagem'] = $this->somarHoras(!empty($dados_pedido_status[$status]['totais']['total_tempo_montagem']) ? $dados_pedido_status[$status]['totais']['total_tempo_montagem'] : "00:00:00", $total_tempo_montagem);
                $dados_pedido_status[$status]['totais']['total_tempo_inspecao'] = $this->somarHoras(!empty($dados_pedido_status[$status]['totais']['total_tempo_inspecao']) ? $dados_pedido_status[$status]['totais']['total_tempo_inspecao'] : "00:00:00", $total_tempo_inspecao);
            }

            $dados_pedido_status[$status]['maquinas_usinagens'] = $this->divideHoursIntoDays($dados_pedido_status[$status]['totais']['total_tempo_usinagem'], $total_horas_usinagem_maquinas_dia);
            $dados_pedido_status[$status]['pessoas_acabamento'] = $this->divideHoursAndReturnWorkDays($dados_pedido_status[$status]['totais']['total_tempo_acabamento'], $total_horas_pessoas_acabamento_dia);


            $dados_pedido_status[$status]['pessoas_montagem_torre'] = $this->divideHoursAndReturnWorkDays($dados_pedido_status[$status]['totais']['total_tempo_montagem_torre'], $total_horas_pessoas_pessoas_montagem_torres_dia);

            $dados_pedido_status[$status]['pessoas_montagem'] = $this->divideHoursAndReturnWorkDays($dados_pedido_status[$status]['totais']['total_tempo_montagem'], $total_horas_pessoas_pessoas_montagem_dia);
            $dados_pedido_status[$status]['pessoas_inspecao'] =$this->divideHoursAndReturnWorkDays($dados_pedido_status[$status]['totais']['total_tempo_inspecao'], $total_horas_pessoas_inspecao_dia);

            if($pedidos['id_status'][$chave] <= 4 ){
                $totalGeral['totalGeralusinagens'] = ((!empty($totalGeral['totalGeralusinagens']) ? $totalGeral['totalGeralusinagens'] : '0') + preg_replace('/[^0-9.]/', '', $dados_pedido_status[$status]['maquinas_usinagens']) );
                $totalGeral['totalGeralacabamento'] = ((!empty($totalGeral['totalGeralacabamento']) ? $totalGeral['totalGeralacabamento'] : '0') + preg_replace('/[^0-9.]/', '', $dados_pedido_status[$status]['pessoas_acabamento']) );
                $totalGeral['totalGeralmontagem_torre'] = ((!empty($totalGeral['totalGeralmontagem_torre']) ? $totalGeral['totalGeralmontagem_torre'] : '0') + preg_replace('/[^0-9.]/', '', $dados_pedido_status[$status]['pessoas_montagem_torre']) );
                $totalGeral['totalGeralmontagem'] = ((!empty($totalGeral['totalGeralmontagem']) ? $totalGeral['totalGeralmontagem'] : '0') + preg_replace('/[^0-9.]/', '', $dados_pedido_status[$status]['pessoas_montagem']) );
                $totalGeral['totalGeralinspecao'] = ((!empty($totalGeral['totalGeralinspecao']) ? $totalGeral['totalGeralinspecao'] : '0') + preg_replace('/[^0-9.]/', '', $dados_pedido_status[$status]['pessoas_inspecao']) );
            }
            if($pedidos['id_status'][$chave] == 5 ){
                $totalGeral['totalGeralacabamento'] = ((!empty($totalGeral['totalGeralacabamento']) ? $totalGeral['totalGeralacabamento'] : '0') + preg_replace('/[^0-9.]/', '', $dados_pedido_status[$status]['pessoas_acabamento']) );
                $totalGeral['totalGeralmontagem_torre'] = ((!empty($totalGeral['totalGeralmontagem_torre']) ? $totalGeral['totalGeralmontagem_torre'] : '0') + preg_replace('/[^0-9.]/', '', $dados_pedido_status[$status]['pessoas_montagem_torre']) );
                $totalGeral['totalGeralmontagem'] = ((!empty($totalGeral['totalGeralmontagem']) ? $totalGeral['totalGeralmontagem'] : '0') + preg_replace('/[^0-9.]/', '', $dados_pedido_status[$status]['pessoas_montagem']) );
                $totalGeral['totalGeralinspecao'] = ((!empty($totalGeral['totalGeralinspecao']) ? $totalGeral['totalGeralinspecao'] : '0') + preg_replace('/[^0-9.]/', '', $dados_pedido_status[$status]['pessoas_inspecao']) );
            }
            if($pedidos['id_status'][$chave] == 6 ){
                $totalGeral['totalGeralmontagem_torre'] = ((!empty($totalGeral['totalGeralmontagem_torre']) ? $totalGeral['totalGeralmontagem_torre'] : '0') + preg_replace('/[^0-9.]/', '', $dados_pedido_status[$status]['pessoas_montagem_torre']) );
                $totalGeral['totalGeralmontagem'] = ((!empty($totalGeral['totalGeralmontagem']) ? $totalGeral['totalGeralmontagem'] : '0') + preg_replace('/[^0-9.]/', '', $dados_pedido_status[$status]['pessoas_montagem']) );
                $totalGeral['totalGeralinspecao'] = ((!empty($totalGeral['totalGeralinspecao']) ? $totalGeral['totalGeralinspecao'] : '0') + preg_replace('/[^0-9.]/', '', $dados_pedido_status[$status]['pessoas_inspecao']) );
            }
            if($pedidos['id_status'][$chave] == 7 ){
                $totalGeral['totalGeralinspecao'] = ((!empty($totalGeral['totalGeralinspecao']) ? $totalGeral['totalGeralinspecao'] : '0') + preg_replace('/[^0-9.]/', '', $dados_pedido_status[$status]['pessoas_inspecao']) );
            }
        }

        $tela = 'ciclo-producao';
        $nome_da_tela ='followup realizado';

        $data = array(
            'tela' => $tela,
            'nome_tela' => $nome_da_tela,
            'dados_pedido_status' => $dados_pedido_status,
            'request' => $request,
            'status' => $this->getAllStatus(),
            'rotaIncluir' => 'incluir-pedidos',
            'rotaAlterar' => 'alterar-pedidos'
        );


        return view('pedidos', $data);
    }

    public function alertasPedidos(Request $request){

        if($request->input('enviar')){
            foreach ($request->input('enviar') as $key => $pedido) {

                $alertasPedido = DB::table('pedidos')
                    ->select('pedidos.id','status.alertacliente', 'status.nome as nome_status', 'alertas.id as id_alerta')
                    ->join('alertas', 'pedidos.id', '=', 'alertas.pedidos_id')
                    ->join('status', 'pedidos.status_id', '=', 'status.id')
                    ->where('alertas.enviado', '=', 0)
                    ->where('pedidos.id', '=', $pedido)->get();

                if($alertasPedido[0]->alertacliente == 1){
                    $this->enviaEmail($alertasPedido[0]->id);
                }
            }
        }

        if($request->input('emails')){
            foreach ($request->input('emails') as $key => $pedido) {

                $alertasPedido = DB::table('pedidos')
                    ->select('pedidos.id', 'status.alertacliente', 'alertas.id as id_alerta')
                    ->join('alertas', 'pedidos.id', '=', 'alertas.pedidos_id')
                    ->join('status', 'pedidos.status_id', '=', 'status.id')
                    ->where('alertas.enviado', '=', 0)
                    ->where('pedidos.id', '=', $pedido)->get();

                $Alertas = new Alertas();
                $Alertas = $Alertas::find($alertasPedido[0]->id_alerta);
                $Alertas->enviado = 1;
                $Alertas->save();
            }
        }

        $alertasPedido = DB::table('pedidos')
        ->join('alertas', 'pedidos.id', '=', 'alertas.pedidos_id')
        ->join('status', 'pedidos.status_id', '=', 'status.id')
        ->join('ficha_tecnica', 'ficha_tecnica.id', '=', 'pedidos.fichatecnica_id')
        ->join('pessoas', 'pessoas.id', '=', 'pedidos.pessoas_id')
        ->select('pedidos.*', 'ficha_tecnica.ep', 'pessoas.nome_cliente','pessoas.nome_contato', 'pessoas.email', 'status.nome as nome_status')
        ->where('enviado', '=', 0)
        ->distinct()->get();

        $data = array(
            'tela' =>'alerta-pedidos',
            'nome_tela' => 'alerta de pedidos',
            'pedidos' => $alertasPedido
        );

        return view('pedidos', $data);
    }


    public function imprimirOS(Request $request)
    {
        $pedidos = new Pedidos();
        $Fichastecnicasitens = new Fichastecnicasitens();

        $pedidos = $pedidos::with('tabelaStatus', 'tabelaFichastecnicas')->where('id', $request->input('id'))->get();

        $fichastecnicasitens = $Fichastecnicasitens->where('fichatecnica_id', '=', $pedidos[0]->tabelaFichastecnicas->id)->get();

        $conjuntos['conjuntos'] = [];
        $qdte_blank = 0;
        foreach($fichastecnicasitens as $fichastecnicasitem) {
            $letra_blank = substr($fichastecnicasitem->blank, 0, 1);
            if($letra_blank != '') {
                $qdte_blank++ ;
                $conjuntos['conjuntos'][$letra_blank] = $letra_blank;
            }
        };

        $data = [
            'pedidos' => $pedidos,
            'folhas' => [
                0 => [
                    'status' => 'Usinagem',
                    'indicador_status' => 'usinagem',
                    'alerta1' => $pedidos[0]->tabelaFichastecnicas->alerta_usinagem1,
                    'alerta2' => $pedidos[0]->tabelaFichastecnicas->alerta_usinagem2,
                    'alerta3' => $pedidos[0]->tabelaFichastecnicas->alerta_usinagem3,
                    'alerta4' => $pedidos[0]->tabelaFichastecnicas->alerta_usinagem4,
                    'alerta5' => $pedidos[0]->tabelaFichastecnicas->alerta_usinagem5,
                ],
                1 => [
                    'status' => 'Acabamento',
                    'indicador_status' => 'acabamento',
                    'alerta1' => $pedidos[0]->tabelaFichastecnicas->alerta_acabamento1,
                    'alerta2' => $pedidos[0]->tabelaFichastecnicas->alerta_acabamento2,
                    'alerta3' => $pedidos[0]->tabelaFichastecnicas->alerta_acabamento3,
                    'alerta4' => $pedidos[0]->tabelaFichastecnicas->alerta_acabamento4,
                    'alerta5' => $pedidos[0]->tabelaFichastecnicas->alerta_acabamento5,
                ],
                2 => [
                    'status' => 'Montagem',
                    'indicador_status' => 'montagem',
                    'alerta1' => $pedidos[0]->tabelaFichastecnicas->alerta_montagem1,
                    'alerta2' => $pedidos[0]->tabelaFichastecnicas->alerta_montagem2,
                    'alerta3' => $pedidos[0]->tabelaFichastecnicas->alerta_montagem3,
                    'alerta4' => $pedidos[0]->tabelaFichastecnicas->alerta_montagem4,
                    'alerta5' => $pedidos[0]->tabelaFichastecnicas->alerta_montagem5,
                ],
                3 => [
                    'status' => 'Inspeção',
                    'indicador_status' => 'inspecao',
                    'alerta1' => $pedidos[0]->tabelaFichastecnicas->alerta_inspecao1,
                    'alerta2' => $pedidos[0]->tabelaFichastecnicas->alerta_inspecao2,
                    'alerta3' => $pedidos[0]->tabelaFichastecnicas->alerta_inspecao3,
                    'alerta4' => $pedidos[0]->tabelaFichastecnicas->alerta_inspecao4,
                    'alerta5' => $pedidos[0]->tabelaFichastecnicas->alerta_inspecao5,
                ],
                4 => [
                    'status' => 'Embalagem',
                    'indicador_status' => 'embalagem',
                    'alerta1' => $pedidos[0]->tabelaFichastecnicas->alerta_expedicao1,
                    'alerta2' => $pedidos[0]->tabelaFichastecnicas->alerta_expedicao2,
                    'alerta3' => $pedidos[0]->tabelaFichastecnicas->alerta_expedicao3,
                    'alerta4' => $pedidos[0]->tabelaFichastecnicas->alerta_expedicao4,
                    'alerta5' => $pedidos[0]->tabelaFichastecnicas->alerta_expedicao5,
                ],
            ],
            'fichastecnicasitens'=>$fichastecnicasitens,
            'qtde_blank' => $qdte_blank,
            'qtde_conjuntos' => count($conjuntos['conjuntos'])
        ];
        $imprimirPDF = new PDFController();

        return $imprimirPDF->generatePDF($data, 'imprimir_os');

        // return view('imprimir_os', $data);
    }

    public function imprimirMP(Request $request)
    {
        $ConsumoMateriais = new ConsumoMateriaisController();

        $dados = $ConsumoMateriais->detalhes($request, 1);

        return $dados;
    }

    function divideHoursAndReturnWorkDays($totalHours, $smallerHours) {
        // Extrair as horas, minutos e segundos do total
        list($totalHours, $totalMinutes, $totalSeconds) = explode(':', $totalHours);

        // Calcular o total de segundos
        $totalSeconds = $totalHours * 3600 + $totalMinutes * 60 + $totalSeconds;

        // Calcular o valor menor em segundos
        list($tHours, $tMinutes, $tSeconds) = explode(':', $smallerHours);
        $smallerseconds = $tHours * 3600 + $tMinutes * 60 + $tSeconds;

        // Dividir o total de segundos pelo valor menor
        $resultDays = $totalSeconds / $smallerseconds ;

        // Formatar o resultado
        $resultTime = sprintf("%.1f dias", $resultDays);

        return $resultTime;
    }

    static function formatarHoraMinuto($hora) {
        // Separando as partes da hora
        $partes = explode(":", $hora);

        // Se houver mais de duas partes, mantenha apenas as duas primeiras
        if (count($partes) > 2) {
            return $partes[0] . ":" . $partes[1];
        } else {
            return $hora; // Já está no formato desejado
        }
    }


    function divideHoursIntoDays($tempoTotal, $tempoDiario) {
        /// Convertendo os tempos para segundos
        list($horasTotal, $minutosTotal, $segundosTotal) = explode(":", $tempoTotal);
        $tempoTotalSegundos = $horasTotal * 3600 + $minutosTotal * 60 + $segundosTotal;

        list($horasDiario, $minutosDiario, $segundosDiario) = explode(":", $tempoDiario);
        $tempoDiarioSegundos = $horasDiario * 3600 + $minutosDiario * 60 + $segundosDiario;

        // Calculando o resultado em dias
        $resultadoDias = $tempoTotalSegundos / $tempoDiarioSegundos;

        // Formatando o resultado
        $mensagem = sprintf("%.1f dias", $resultadoDias);

        return $mensagem;
    }

    /**
     * Exemplo de uso
     * $tempo = "90:00";
     * $valor = 3;
     * $resultado = dividirTempoPorValor($tempo, $valor);
     */
    function dividirTempoPorValor($tempo, $valor) {
        // Separar as partes do tempo
        list($horas, $minutos) = explode(':', $tempo);

        // Converter o tempo para minutos totais
        $total_minutos = $horas * 60 + $minutos;

        // Dividir o total de minutos pelo valor
        $resultado_minutos = $total_minutos / $valor;

        // Calcular as novas horas e minutos do resultado
        $horas_resultado = floor($resultado_minutos / 60);
        $minutos_resultado = $resultado_minutos % 60;

        // Formatando a saída com zeros à esquerda, se necessário
        $hora_formatada = str_pad($horas_resultado, 2, '0', STR_PAD_LEFT);
        $minuto_formatado = str_pad($minutos_resultado, 2, '0', STR_PAD_LEFT);

        // Retornar o tempo formatado
        return $hora_formatada . ':' . $minuto_formatado;
    }


    /**
    * Show the application dashboard.
    *
    * @return \Illuminate\Contracts\Support\Renderable
    */
    public function getAllStatus()
    {
        $Status = new Status();
        return $Status->where('status', '=', 'A')->get();
    }

    /**
    * Show the application dashboard.
    *
    * @return \Illuminate\Contracts\Support\Renderable
    */
    public function getAllStatusExcept($status_id)
    {
        $Status = new Status();
        $datas = $Status->select('id')->whereNotIn('id', $status_id)->where('status', '=', 'A')->get();
        $array_data = [];
        foreach ($datas as $key => $data) {
            $array_data[] = $data->id;
        }

        return $array_data;
    }

    /**
    * Show the application dashboard.
    *
    * @return \Illuminate\Contracts\Support\Renderable
    */
    public function getAllprioridades()
    {
        $Status = new Prioridades();
        return $Status->where('status', '=', 'A')->orderBy('nome', 'ASC')->get();
    }

    /**
    * Show the application dashboard.
    *
    * @return \Illuminate\Contracts\Support\Renderable
    */
    public function getAllTransportes()
    {
        $Status = new Transportes();
        return $Status->where('status', '=', 'A')->orderBy('nome', 'ASC')->get();
    }


    /**
    * Show the application dashboard.
    *
    *
    */
    public function getAllfichastecnicas() {
        $Status = new Fichastecnicas();
        return $Status->where('status', '=', 'A')->orderBy('ep', 'ASC')->get();

    }
    /**
    * Show the application dashboard.
    *
    *
    */
    public function getAllClientes() {
        $pessoa = new Pessoas();
        return $pessoa->where('status', '=', 'A')->orderBy('nome_cliente', 'ASC')->get();

    }

        /**
    * Soma dois valores de horas Ex: 00:00:10 + 00:00:10 = 00:00:20
    * @param string $hora1
    * @param string $hora2
    * @return string
    */
    public static function somarHoras($hora1, $hora2) {

        // Dividir as horas, minutos e segundos
        list($h1, $m1, $s1) = array_map('intval', explode(':', $hora1));
        list($h2, $m2, $s2) = array_map('intval', explode(':', $hora2));

        // Somar as horas, minutos e segundos
        $totalSegundos = ($h1 * 3600 + $m1 * 60 + $s1) + ($h2 * 3600 + $m2 * 60 + $s2);

        // Converter de volta para o formato de horas
        $novoHoras = floor($totalSegundos / 3600);
        $novoMinutos = floor(($totalSegundos % 3600) / 60);
        $novoSegundos = $totalSegundos % 60;

        // Formatar e retornar o resultado
        $resultado = sprintf('%02d:%02d:%02d', $novoHoras, $novoMinutos, $novoSegundos);
        return $resultado;
    }

    public function subtrairHoras($hora1, $hora2) {
        // Convertendo as strings de horas para segundos
        list($hours1, $minutes1, $seconds1) = explode(':', $hora1);
        $totalSeconds1 = $hours1 * 3600 + $minutes1 * 60 + $seconds1;

        list($hours2, $minutes2, $seconds2) = explode(':', $hora2);
        $totalSeconds2 = $hours2 * 3600 + $minutes2 * 60 + $seconds2;

        // Subtraindo os segundos e convertendo de volta para o formato de horas
        $differenceSeconds = $totalSeconds1 - $totalSeconds2;
        $hoursDiff = floor($differenceSeconds / 3600);
        $minutesDiff = floor(($differenceSeconds % 3600) / 60);
        $secondsDiff = $differenceSeconds % 60;

        // Formatando a diferença de tempo de volta para a string original
        $resultTime = sprintf("%02d:%02d:%02d", $hoursDiff, $minutesDiff, $secondsDiff);

        return $resultTime;
    }

    function multiplyTime($time1, $time2) {
        $seconds1 = strtotime($time1);
        $seconds2 = strtotime($time2);

        $result = $seconds1 * $seconds2;

        return gmdate("H:i:s", $result);
    }


    function multiplyTimeByInteger($time, $factor) {
        $parts = explode(':', $time);
        $hours = $parts[0];
        $minutes = $parts[1];
        $seconds = $parts[2];

        $totalSeconds = $hours * 3600 + $minutes * 60 + $seconds;
        $resultSeconds = $totalSeconds * $factor;

        $resultHours = floor($resultSeconds / 3600);
        $resultMinutes = floor(($resultSeconds % 3600) / 60);
        $resultSeconds = $resultSeconds % 60;

        return sprintf("%02d:%02d:%02d", $resultHours, $resultMinutes, $resultSeconds);
    }

    function diferencaDatasEmHoras($data1, $data2) {
        // Convertendo as strings de data para objetos DateTime
        $data1_obj = new DateTime($data1);
        $data2_obj = new DateTime($data2);

        // Calculando a diferença entre as datas
        $diferenca = $data1_obj->diff($data2_obj);

        // Calculando a diferença total em horas
        $horas = $diferenca->days * 24 + $diferenca->h;

        // Formatando o resultado para o formato desejado
        return sprintf("%02d:%02d:%02d", $horas, $diferenca->i, $diferenca->s);
    }

    static function formatarMinutoSegundo($hora) {
        // Separando as partes da hora
        $partes = explode(":", $hora);

        // Se houver mais de duas partes, mantenha apenas as duas ultimas
        if (count($partes) > 2) {
            return $partes[1] . ":" . $partes[2];
        } else {
            return $hora; // Já está no formato desejado
        }
    }

    static function calcularPorcentagemEntreMinutos($tempoA, $tempoB) {
        // Convertendo os tempos para minutos
        list($horasA, $minutosA, $segundosA) = explode(':', $tempoA);
        $totalMinutosA = ($horasA * 60 * 60 + $minutosA * 60 + $segundosA) / 60;

        list($horasB, $minutosB, $segundosB) = explode(':', $tempoB);
        $totalMinutosB = ($horasB * 60 * 60 + $minutosB * 60 + $segundosB) / 60;

        // Calculando a porcentagem de tempoA em relação a tempoB
        if($totalMinutosA ==0 || $totalMinutosB == 0) {
            return 0;
        }
        $percentual = ($totalMinutosA / $totalMinutosB) * 100;

       return $percentual;
    }

    static function getMaquinas() {
        
        $maquinas = Maquinas::all();
        
        return [
            'usinagem' => $maquinas[0]->prazo_usinagem + $maquinas[0]->prazo_acabamento + $maquinas[0]->prazo_montagem + $maquinas[0]->prazo_inspecao + $maquinas[0]->prazo_embalar ,
            'acabamento' => $maquinas[0]->prazo_acabamento + $maquinas[0]->prazo_montagem + $maquinas[0]->prazo_inspecao + $maquinas[0]->prazo_embalar ,
            'montagem' => $maquinas[0]->prazo_montagem + $maquinas[0]->prazo_inspecao + $maquinas[0]->prazo_embalar ,
            'inspeção' => $maquinas[0]->prazo_inspecao + $maquinas[0]->prazo_embalar ,
            'embalar' => $maquinas[0]->prazo_embalar ,           
            'expedição' => 0,
            'usinagem_original' => $maquinas[0]->prazo_usinagem ,
            'acabamento_original' => $maquinas[0]->prazo_acabamento ,
            'montagem_original' => $maquinas[0]->prazo_montagem ,
            'inspeção_original' => $maquinas[0]->prazo_inspecao ,
            'embalar_original' => $maquinas[0]->prazo_embalar,
            'expedição_original' => 0,
        ];
        
    }

    static function calculaDiasSobrando($maquinas, $status, $pedido){
        
        $hoje = date('Y-m-d');
        $dias_alerta_departamento = 'text-primary';
        $dias_prazo  = $maquinas[$status];
        $original = $maquinas[$status . '_original'];

        $data_minima = \Carbon\Carbon::createFromDate($pedido->data_entrega)->subWeekdays($dias_prazo-$original)->format('Y-m-d');

        $diasSobrando = \Carbon\Carbon::createFromDate($hoje)->diffInWeekdays($data_minima, false); 
        if($diasSobrando >= 0 ) {
            $diasSobrando = $diasSobrando-1;
        }
        if($diasSobrando < $maquinas[$status . '_original']/2){
            $dias_alerta_departamento = 'text-danger';
        }      

        return ['dias_alerta_departamento' => $dias_alerta_departamento, 'diasSobrando' => $diasSobrando];
    }

}

