<?php

namespace App\Http\Controllers;

use App\Models\Fichastecnicas;
use Illuminate\Http\Request;
use App\Models\Pedidos;
use App\Models\Status;
use App\Providers\DateHelpers;

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

        $id = !empty($request->input('id')) ? ($request->input('id')) : (!empty($id) ? $id : false);
        $status_id = !empty($request->input('status_id')) ? ($request->input('status_id')) : (!empty($status_id) ? $status_id : false);
        $os = !empty($request->input('os')) ? ($request->input('os')) : (!empty($os) ? $os : false);
        $ep = !empty($request->input('ep')) ? ($request->input('ep')) : (!empty($ep) ? $ep : false);
        $data_gerado = !empty($request->input('data_gerado')) ? ($request->input('data_gerado')) : (!empty($data_gerado) ? $data_gerado : false);
        $data_entrega = !empty($request->input('data_entrega')) ? ($request->input('data_entrega')) : (!empty($data_entrega) ? $data_entrega : false);

        $pedidos = new Pedidos();
        $pedidos::with('tabelaStatus','tabelaFichastecnicas');
        if ($id) { $pedidos = $pedidos->where('id', '=', $id); }        
        if ($status_id) { $pedidos = $pedidos->where('status_id', '=', $status_id); }
        if ($os) { $pedidos = $pedidos->where('os', '=', $os); }
        if ($ep) { $pedidos = $pedidos->where('ep', '=', $ep); }
        if ($data_gerado) { $pedidos = $pedidos->where('data_gerado', '=', $data_gerado); }
        if ($data_entrega) { $pedidos = $pedidos->where('data_entrega', '=', $data_entrega); }


        $pedidos = $pedidos->get();

        $data = array(
            'tela' => 'pesquisar',
            'nome_tela' => 'pedidos',
            'pedidos' => $pedidos,
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
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function incluir(Request $request)
    {
        $metodo = $request->method();

        if ($metodo == 'POST') {

            $pedidos_id = $this->salva($request);

            return redirect()->route('pedidos', ['id' => $pedidos_id]);
        }

        $data = array(
            'tela' => 'incluir',
            'nome_tela' => 'pedidos',
            'request' => $request,
            'status' => $this->getAllStatus(),
            'fichastecnicas' =>$this->getAllfichastecnicas(),
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


        $pedidos = $pedidos->where('id', '=', $request->input('id'))->get();

        $metodo = $request->method();
        if ($metodo == 'POST') {

            $pedidos_id = $this->salva($request);

            return redirect()->route('pedidos', ['id' => $pedidos_id]);
        }
        
        $data = array(
            'tela' =>'alterar',
            'nome_tela' => 'pedidos',
            'pedidos' => $pedidos,
            'request' => $request,
            'status' => $this->getAllStatus(),
            'fichastecnicas' =>$this->getAllfichastecnicas(),
            'rotaIncluir' => 'incluir-pedidos',
            'rotaAlterar' => 'alterar-pedidos'
        );

        return view('pedidos', $data);
    }

    public function salva($request)
    {
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
        $pedidos->observacao = trim($request->input('observacao'));
        $pedidos->valor = !empty($request->input('valor')) ? DateHelpers::formatFloatValue($request->input('valor')) : null;
        $pedidos->status = $request->input('status') == 'on' ? 1 : 0;
        $pedidos->save();

        return $pedidos->id;
    }
    
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function followup(Request $request)
    {
        $filtrado = 0;
        $pedidos = new Pedidos();

         if(!empty($request->input('status_id'))) {
            $pedidos = $pedidos->where('status_id', '=', $request->input('status_id'));
            $filtrado++;
         } 
         if(!empty($request->input('os'))) {
            $pedidos = $pedidos->where('os', '=', $request->input('os'));
            $filtrado++;
         } 
         if(!empty($request->input('ep'))) {
            $pedidos = $pedidos->where('ep', '=', $request->input('ep'));
            $filtrado++;
         } 
         if(!empty($request->input('id'))) {
            $pedidos = $pedidos->where('id', '=', $request->input('id'));
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

        $pedidos_encontrados = [];
        if ($filtrado > 0) {

            $pedidos = $pedidos->get();


            foreach ($pedidos as $key => $value) {
                $pedidos_encontrados[] = $value->id;
            }
        }

        $data = array(
            'tela' => 'pesquisa-followup',
            'nome_tela' => 'followup',
            'pedidos_encontrados' => $pedidos_encontrados,
            'pedidos' => $pedidos,
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
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function followupDetalhes(Request $request)
    {
         $pedidos = new Pedidos();
        
        if(empty($request->input('pedidos_encontrados'))) {
            return redirect()->route('followup');
        }
        $pedidos_encontrados = json_decode($request->input('pedidos_encontrados'));
        
        $pedidos = $pedidos::with('tabelaStatus', 'tabelaFichastecnicas')->wherein('id', $pedidos_encontrados)->get();
        
        $total_tempo_usinagem=$total_tempo_acabamento=$total_tempo_montagem=$total_tempo_inspecao='00:00:00';
        $dados_pedido_status=[];

        foreach ($pedidos as $pedido) {
            $dados_pedido_status[$pedido->tabelaStatus->nome]['class'][] = $pedido;
        }

        foreach ($dados_pedido_status as $status => $classes) {

            $total_tempo_usinagem=$total_tempo_acabamento=$total_tempo_montagem=$total_tempo_inspecao='00:00:00';
            
            foreach ($classes['class'] as $classe) {
                $total_tempo_usinagem = $this->somarHoras($total_tempo_usinagem , $classe->tabelaFichastecnicas->tempo_usinagem);
                $total_tempo_acabamento = $this->somarHoras($total_tempo_acabamento , $classe->tabelaFichastecnicas->tempo_acabamento);
                $total_tempo_montagem = $this->somarHoras($total_tempo_montagem , $classe->tabelaFichastecnicas->tempo_montagem);
                $total_tempo_inspecao = $this->somarHoras($total_tempo_inspecao , $classe->tabelaFichastecnicas->tempo_inspecao);

            }

            $dados_pedido_status[$status]['totais']['total_tempo_usinagem'] =$total_tempo_usinagem;
            $dados_pedido_status[$status]['totais']['total_tempo_acabamento'] =$total_tempo_acabamento;
            $dados_pedido_status[$status]['totais']['total_tempo_montagem'] =$total_tempo_montagem;
            $dados_pedido_status[$status]['totais']['total_tempo_inspecao'] =$total_tempo_inspecao;
            
        }

        $data = array(
            'tela' => 'followup-detalhes',
            'nome_tela' => 'followup',
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
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getAllStatus()
    {
        $Status = new Status();
        return $Status->where('status', '=', 1)->get();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getAllfichastecnicas() {
        $Status = new Fichastecnicas();
        return $Status->where('status', '=', 1)->get();

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
}