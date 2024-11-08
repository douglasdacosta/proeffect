<?php

namespace App\Http\Controllers;

use App\Models\Fichastecnicasitens;
use Illuminate\Support\Facades\DB;

class PaineisController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('afterAuth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('painel');
    }

    private function busca_dados_pedidos($status){

        $Fichastecnicasitens = new Fichastecnicasitens();

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
