<?php

namespace App\Http\Controllers;

use App\Models\Fichastecnicas;
use Illuminate\Http\Request;
use App\Models\Status;
use App\Providers\DateHelpers;
use Illuminate\Support\Facades\DB;

class AtualizacaoTemposController extends Controller
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

        $status_id = $request->input('status_id');
        $filtrado = 0;
        $pedidos = DB::table('pedidos')
            ->distinct()
            ->join('status', 'pedidos.status_id', '=', 'status.id')
            ->join('ficha_tecnica', 'ficha_tecnica.id', '=', 'pedidos.fichatecnica_id')
            ->join('pessoas', 'pessoas.id', '=', 'pedidos.pessoas_id')
            ->orderby('status_id', 'desc')
            ->orderby('data_entrega');
        $pedidos->select('pedidos.*',
            'ficha_tecnica.ep as ep',
            'ficha_tecnica.tempo_montagem as tempo_montagem',
            'ficha_tecnica.tempo_inspecao as tempo_inspecao',
            'status.id as id_status');

        $pedidos = $pedidos->orderBy('ficha_tecnica.ep', 'asc');

        $pedidos->whereBetween('data_gerado', [
            \Carbon\Carbon::now()->sub('6 months')->format('Y-m-d'),
            \Carbon\Carbon::now()->format('Y-m-d'),
        ]);

        $pedidos = $pedidos->where('pedidos.status', '=', 'A');
        $pedidos = $pedidos->get();

        foreach ($pedidos as $pedido) {

            $retorno_tempo = DB::select(DB::raw('
                select (
                        (
                            SELECT created_at FROM historicos_etapas
                            WHERE pedidos_id = '.$pedido->id.' AND status_id = 7 AND etapas_pedidos_id = 4
                            ORDER BY created_at desc LIMIT 1
                        )-
                        (
                            SELECT created_at FROM historicos_etapas
                            WHERE pedidos_id = '.$pedido->id.' AND status_id = 7 AND etapas_pedidos_id = 1
                            ORDER BY created_at asc LIMIT 1
                        )) as tempo_montagem,
                        (
                        (
                            SELECT created_at FROM historicos_etapas
                            WHERE pedidos_id = '.$pedido->id.' AND status_id = 6 AND etapas_pedidos_id = 4
                            ORDER BY created_at desc LIMIT 1
                        )-
                        (
                            SELECT created_at FROM historicos_etapas
                            WHERE pedidos_id = '.$pedido->id.' AND status_id = 6 AND etapas_pedidos_id = 1
                            ORDER BY created_at asc LIMIT 1
                        )) as tempo_acabamento;
            '));

            $tempo_montagem = $retorno_tempo[0]->tempo_montagem/$pedido->qtde;
            $pedido->tempo_somado_montagem = !empty($tempo_montagem) ? $tempo_montagem : '0';
            $pedido->tempo_somado_montagem = $this->formatSeconds($pedido->tempo_somado_montagem);

            $tempo_acabamento = $retorno_tempo[0]->tempo_acabamento/$pedido->qtde;
            $pedido->tempo_somado_acabamento = !empty($tempo_acabamento) ? $tempo_acabamento : '0';
            $pedido->tempo_somado_acabamento = $this->formatSeconds($pedido->tempo_somado_acabamento);
        }

        $tela = 'pesquisa';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'Atualização de Tempos',
				'pedidos'=> $pedidos,
				'request' => $request,
				'rotaIncluir' => 'incluir-atualizacao_tempo',
				'rotaAlterar' => 'alterar-atualizacao_tempo'
			);

        return view('atualizacao_tempo', $data);
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

    		$fichaTecnica_id = $this->salva($request);

	    	return redirect()->route('atualizacao_tempo', [ 'id' => $fichaTecnica_id ] );

    	}
        $tela = 'incluir';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'Atualização de Tempos',
				'request' => $request,
				'rotaIncluir' => 'incluir-atualizacao_tempo',
				'rotaAlterar' => 'alterar-atualizacao_tempo'
			);

        return view('atualizacao_tempo', $data);
    }

     /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function alterar(Request $request)
    {

        $fichaTecnica = new Status();


        $fichaTecnica= $fichaTecnica->where('id', '=', $request->input('id'))->get();

		$metodo = $request->method();
		if ($metodo == 'POST') {

    		$fichaTecnica_id = $this->salva($request);

	    	return redirect()->route('status', [ 'id' => $fichaTecnica_id ] );

    	}
        $tela = 'alterar';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'Atualização de Tempos',
				'atualizacao_tempos'=> $fichaTecnica,
				'request' => $request,
				'rotaIncluir' => 'incluir-atualizacao_tempo',
				'rotaAlterar' => 'alterar-atualizacao_tempo'
			);

        return view('atualizacao_tempo', $data);
    }

    public function salva($requests) {

        return false;

    }
    /**
     * Formata os segundos em horas, minutos e segundos.
     *
     * @param int $segundos
     * @return string
     */
    function formatSeconds($segundos)
    {
        $segundos = floor($segundos); // Remove casas decimais

        $horas = floor($segundos / 3600);
        $minutos = floor(($segundos % 3600) / 60);
        $segundos_restantes = $segundos % 60;

        return sprintf("%02d:%02d:%02d", $horas, $minutos, $segundos_restantes);
    }
}
