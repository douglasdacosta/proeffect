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
            '6' => 'P.M – Problema na maquina',
        ];
    }
    private function busca_dados_pedidos($status){

        $Fichastecnicasitens = new Fichastecnicasitens();

        $controllerPedidos = new PedidosController();

        $pedidos = DB::table('pedidos')
            ->join('status', 'pedidos.status_id', '=', 'status.id')
            ->join('ficha_tecnica', 'ficha_tecnica.id', '=', 'pedidos.fichatecnica_id')
            ->join('pessoas', 'pessoas.id', '=', 'pedidos.pessoas_id')
            ->select('pedidos.*', 'ficha_tecnica.ep')
            ->orderby('pedidos.data_entrega')
            ->where('pedidos.status_id', '=', $status );
        $pedidos->paginate(5);
        $pedidos = $pedidos->get();

        foreach ($pedidos as $key => $pedido) {

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
            ->select('historicos_etapas.*', 'funcionarios.nome', 'pedidos.status_id', 'etapas_pedidos.nome as nome_etapa')
            ->join('pedidos', 'pedidos.id', '=', 'historicos_etapas.pedidos_id')
            ->join('funcionarios', 'funcionarios.id', '=', 'historicos_etapas.funcionarios_id')
            ->join('etapas_pedidos', 'etapas_pedidos.id', '=', 'historicos_etapas.etapas_pedidos_id')
            ->where('pedidos_id','=',$pedido->id)
            ->orderBy('historicos_etapas.created_at', 'desc')
            ->limit(1)
            ->get();


            $etapa = 'Pendente';
            $texto_quantidade = $motivo_pausa =  '';
            $motivos_pausas = $this->getMotivosPausa();
            if(!empty($historicos_etapas[0])) {
                $etapa = $historicos_etapas[0]->nome_etapa;
                $texto_quantidade = $historicos_etapas[0]->texto_quantidade;
                if(!empty($historicos_etapas[0]->select_motivo_pausas)) {

                    $motivo_pausa = $motivos_pausas[$historicos_etapas[0]->select_motivo_pausas];
                }
            }
            $pedidos[$key]->nome_etapa = $etapa;
            $pedidos[$key]->texto_quantidade = $texto_quantidade;
            $pedidos[$key]->motivo_pausa = $motivo_pausa;


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

        $pedidosCompletos = $this->busca_dados_pedidos($status_concluido);
        $pedidosPendentes = $this->busca_dados_pedidos($status_pendente);

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
