<?php

namespace App\Http\Controllers;

use App\Models\Fichastecnicasitens;
use Illuminate\Support\Facades\DB;

class PainelController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}

    public function getMotivosPausa(){
        return [
            '1' => 'F.P – Faltando Peças',
            '2' => 'P.P – Problema na produção',
            '3' => 'P – Pausado',
            '4' => 'P.R – Protótipo',
            '5' => 'A.P – Assunto Pessoal',
            '6' => 'P.M – Problema na máquina',
            '7' => 'E.P - Esperando próxima produção'
        ];
    }
    private function busca_dados_pedidos($status, $limit = 11, $concluidos ){

        $pedidos = DB::table('pedidos')->distinct()
            ->join('status', 'pedidos.status_id', '=', 'status.id')
            ->join('ficha_tecnica', 'ficha_tecnica.id', '=', 'pedidos.fichatecnica_id')
            ->join('pessoas', 'pessoas.id', '=', 'pedidos.pessoas_id');


        if($concluidos){
            $pedidos = $pedidos->select('pedidos.*', 'ficha_tecnica.ep','historicos_etapas.created_at')
            ->join('historicos_etapas', 'historicos_etapas.pedidos_id', '=', 'pedidos.id')
            ->orderBy('historicos_etapas.created_at', 'desc')
            ->orderby('pedidos.data_entrega');
        } else {
            $pedidos = $pedidos->select('pedidos.*', 'ficha_tecnica.ep','historicos_etapas.created_at')
            ->leftJoin('historicos_etapas', 'historicos_etapas.pedidos_id', '=', 'pedidos.id')
            ->orderBy('historicos_etapas.created_at', 'desc')
            ->orderby('pedidos.data_entrega');
        }

        $pedidos=$pedidos->where('pedidos.status_id', '=', $status );

        $pedidos->paginate($limit);
        $pedidos = $pedidos->get()->toArray();

        $pedidos = $this->buscaDadosEtapa($pedidos, $concluidos);
        $pedidos = array_map("unserialize", array_unique(array_map("serialize", $pedidos)));
        if($concluidos){
            $pedidos = array_slice($pedidos, 0,3);
        }
        return $pedidos;
    }

    public function buscaDadosEtapa($pedidos, $concluidos = false) {

        $Fichastecnicasitens = new Fichastecnicasitens();
        $dados_colaboradores = [];

        foreach ($pedidos as $key => $pedido) {
            unset($pedidos[$key]->created_at);
            unset($pedidos[$key]->updated_at);
            $fichastecnicasitens = $Fichastecnicasitens->where('fichatecnica_id', '=', $pedido->fichatecnica_id)->get();
            $conjuntos['conjuntos'] = [];
            $qdte_blank = 0;
            foreach($fichastecnicasitens as $fichastecnicasitem) {
                $letra_blank = substr($fichastecnicasitem->blank, 0, 1);
                if($letra_blank != '') {
                    $qdte_blank++ ;
                    $conjuntos['conjuntos'][$letra_blank] = $letra_blank;
                }
            };

            $entrega = \Carbon\Carbon::createFromDate($pedido->data_entrega)->format('Y-m-d');
            $hoje = date('Y-m-d');
            $pedidos[$key]->dias_alerta = \Carbon\Carbon::createFromDate($hoje)->diffInDays($entrega, false);

            if ($pedidos[$key]->dias_alerta < 6) {
                $pedidos[$key]->class_dias_alerta = 'text-danger';
            } else {
                $pedidos[$key]->class_dias_alerta = 'text-primary';
            }

            $pedidos[$key]->qtde_blank = $qdte_blank;
            $pedidos[$key]->conjuntos = count($conjuntos['conjuntos']);


            $historicos_etapas = DB::table('historicos_etapas')
            ->select('historicos_etapas.*', 'funcionarios.nome', 'etapas_pedidos.nome as nome_etapa')
            ->join('funcionarios', 'funcionarios.id', '=', 'historicos_etapas.funcionarios_id')
            ->join('etapas_pedidos', 'etapas_pedidos.id', '=', 'historicos_etapas.etapas_pedidos_id')
            ->where('pedidos_id','=',$pedido->id)
            ->orderBy('historicos_etapas.created_at', 'desc')
            ->get(1);

            $dados_colaboradores = [];
            if(!empty($historicos_etapas[0])) {

                $historicos_etapas_status = DB::table('historicos_etapas')->distinct()
                    ->select(
                    'historicos_etapas.pedidos_id',
                    'historicos_etapas.status_id',
                    'historicos_etapas.etapas_pedidos_id',
                    'historicos_etapas.funcionarios_id',
                    'historicos_etapas.select_tipo_manutencao',
                    'historicos_etapas.select_motivo_pausas',
                    'historicos_etapas.texto_quantidade',
                    'historicos_etapas.created_at',
                    'funcionarios.nome',
                    'etapas_pedidos.nome as nome_etapa')
                    ->join('funcionarios', 'funcionarios.id', '=', 'historicos_etapas.funcionarios_id')
                    ->join('etapas_pedidos', 'etapas_pedidos.id', '=', 'historicos_etapas.etapas_pedidos_id')
                    ->where('historicos_etapas.pedidos_id','=',$pedido->id);


                if($concluidos == true) {
                    $historicos_etapas_status = $historicos_etapas_status
                    ->where('historicos_etapas.status_id','=',$pedido->status_id - 1)
                    ->where('historicos_etapas.etapas_pedidos_id','=',4);
                } else {
                    $historicos_etapas_status = $historicos_etapas_status
                    ->where('historicos_etapas.status_id','=',$pedido->status_id);
                }


                $historicos_etapas_status = $historicos_etapas_status
                    ->orderBy('historicos_etapas.created_at', 'asc')
                    ->get()->toArray();


                $dados_colaboradores = [];

                foreach ($historicos_etapas_status as $hestatus) {

                    $etapa = $hestatus->nome_etapa;

                    if($hestatus->status_id == 6 ){
                        $etapa = ($hestatus->select_tipo_manutencao =='T' ) ? $etapa . " - Torre" : $etapa . " - Agulha" ;
                    }
                    $dados_colaboradores[$hestatus->pedidos_id][$hestatus->funcionarios_id]= [
                        'pedidos_id' => $hestatus->pedidos_id   ,
                        'status_id' => $hestatus->status_id     ,
                        'etapas_pedidos_id' => $hestatus->etapas_pedidos_id     ,
                        'funcionarios_id' => $hestatus->funcionarios_id     ,
                        'select_tipo_manutencao' => $hestatus->select_tipo_manutencao   ,
                        'select_motivo_pausas' => $hestatus->select_motivo_pausas   ,
                        'texto_quantidade' => $hestatus->texto_quantidade   ,
                        'created_at' => $hestatus->created_at   ,
                        'nome' => $hestatus->nome   ,
                        'nome_etapa' => $etapa  ,
                    ];

                }
            }





            $pedidos[$key]->colaboradores =$dados_colaboradores;


            $funcionarios_montagens = DB::table('pedidos_funcionarios_montagens')
                ->join('funcionarios', 'funcionarios.id', '=', 'pedidos_funcionarios_montagens.funcionario_id')
                ->select('funcionarios.nome', 'funcionarios.id')
                ->where('pedidos_funcionarios_montagens.pedido_id', '=', $pedido->id)
                ->orderby('funcionarios.nome')->get();
                $nome_funcionarios = [];

            foreach($funcionarios_montagens as $funcionario_montagem){
                $nome_funcionarios[] = $funcionario_montagem->nome;
            }

            $pedidos[$key]->funcionario = implode(',',$nome_funcionarios);
        }
        return $pedidos;
    }


    private function carrega_dados($status_pendente, $status_concluido){

        $pedidosCompletos = $this->busca_dados_pedidos($status_concluido, 12, true);

        $pedidosPendentes = $this->busca_dados_pedidos($status_pendente, 50, false);

        return  [
            'pedidosCompletos'=> $pedidosCompletos,
            'pedidosPendentes'=> $pedidosPendentes
        ];
    }

    public function paineisUsinagem(){

        $data = $this->carrega_dados(4,5);

        return view('paineis.painel_usinagem', $data);
    }

    public function paineisAcabamento(){
        $data = $this->carrega_dados(5,6);

        return view('paineis.painel_acabamento', $data);
    }

    public function paineisMontagem(){
        $data = $this->carrega_dados(6,7);
        return view('paineis.painel_montagem', $data);
    }

    public function paineisInspecao(){
        $data = $this->carrega_dados(7,8);
        return view('paineis.painel_inspecao', $data);
    }

    public function paineisEmbalar(){
        $data = $this->carrega_dados(8,9);
        return view('paineis.painel_embalar', $data);
    }

}
