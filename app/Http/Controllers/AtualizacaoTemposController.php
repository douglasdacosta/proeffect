<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Status;
use App\Providers\DateHelpers;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AjaxController;
use DateTime;

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

        if(!empty($request->input('departamento'))) {
            $status_id = $request->input('status_id');

            if($request->input('departamento') =='MA' ) {
                $status_id = 6; // Montagem
            } elseif($request->input('departamento') == 'MT') {
                $status_id = 6; // Montagem
            } elseif($request->input('departamento') == 'I') {
                $status_id = 7; // Inspeção
            }


            $pedidos = DB::table('pedidos')
                ->distinct()
                ->join('status', 'pedidos.status_id', '=', 'status.id')
                ->join('ficha_tecnica', 'ficha_tecnica.id', '=', 'pedidos.fichatecnica_id')
                ->join('pessoas', 'pessoas.id', '=', 'pedidos.pessoas_id')
                ->join('historicos_etapas', 'historicos_etapas.pedidos_id', '=', 'pedidos.id')
                ->join('funcionarios', 'funcionarios.id', '=', 'historicos_etapas.funcionarios_id')
                ->orderby('status_id', 'desc')
                ->orderby('data_entrega');

                if(!empty($status_id)) {
                    $pedidos = $pedidos->where('historicos_etapas.status_id', '=', $status_id);
                }

            if(!empty($request->input('os'))) {
                $pedidos = $pedidos->where('pedidos.os', '=', $request->input('os'));
            }

            if(!empty($request->input('ep'))) {
                $pedidos = $pedidos->where('ficha_tecnica.ep', '=', $request->input('ep'));
            }

            if(!empty($request->input('data_apontamento')) && !empty($request->input('data_apontamento_fim') )) {
                $pedidos = $pedidos->whereBetween('historicos_etapas.created_at', [DateHelpers::formatDate_dmY($request->input('data_apontamento')).' 00:00:01', DateHelpers::formatDate_dmY($request->input('data_apontamento_fim')).' 23:59:59']);
            }
            if(!empty($request->input('data_apontamento')) && empty($request->input('data_apontamento_fim') )) {
                $pedidos = $pedidos->where('historicos_etapas.created_at', '>=', DateHelpers::formatDate_dmY($request->input('data_apontamento')).' 00:00:01');
            }
            if(empty($request->input('data_apontamento')) && !empty($request->input('data_apontamento_fim') )) {
                $pedidos = $pedidos->where('historicos_etapas.created_at', '<=', DateHelpers::formatDate_dmY($request->input('data_apontamento_fim')).' 23:59:59');
            }

            if(!empty($request->input('responsavel'))) {
                $pedidos = $pedidos->where('funcionarios.nome', 'like', '%'.$request->input('responsavel').'%');
            }

            $pedidos->select('pedidos.*',
                'ficha_tecnica.ep as ep',
                'ficha_tecnica.tempo_montagem as pedido_tempo_montagem',
                'ficha_tecnica.tempo_montagem_torre as pedido_tempo_montagem_torre',
                'ficha_tecnica.tempo_inspecao as pedido_tempo_inspecao',
                'status.id as id_status');


            $pedidos = $pedidos->orderBy('ficha_tecnica.ep', 'asc');

            $pedidos->whereBetween('data_gerado', [
                \Carbon\Carbon::now()->sub('6 months')->format('Y-m-d'),
                \Carbon\Carbon::now()->format('Y-m-d'),
            ]);

            $pedidos = $pedidos->where('pedidos.status', '=', 'A');
            $pedidos = $pedidos->get();
            foreach ($pedidos as $pedido) {


                $ajaxController = new AjaxController();
                $status_id = $request->input('departamento');
                $torre = false;
                if($status_id =='MA' ) {
                    $status_id = 6; // Montagem
                } elseif($status_id == 'MT') {
                    $status_id = 6; // Montagem
                    $torre = true; // Montagem Torre
                } elseif($status_id == 'I') {
                    $status_id = 7; // Inspeção
                }
                $retorno_tempo = $ajaxController->consultarResponsaveis($pedido->id, $status_id, $torre);
                $array = [];
                foreach ($retorno_tempo as $tempo) {
                    $array[] = [
                        $tempo->etapa => $tempo->data,
                    ];
                }
                $array = $this->organizarIntervalos($array);
                $pedido->tempo_somado = $this->calcularTempoTotal($array);

                $pedido->tempo_default = '00:00:00';

                if($request->input('departamento') == 'MA') {

                    $pedido->tempo_default = $pedido->pedido_tempo_montagem;

                } elseif($request->input('departamento') == 'MT') {

                    $pedido->tempo_default = $pedido->pedido_tempo_montagem_torre;

                } else {

                    $pedido->tempo_default = $pedido->pedido_tempo_inspecao;

                 }



            }
        } else {
            $pedidos = [];
        }


        $status = new Status();
        $status = $status->where('status', '=', 'A')->get();


        $tela = 'pesquisa';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'Atualização de Tempos',
				'pedidos'=> $pedidos,
                'status' => $status,
				'request' => $request,
				'rotaIncluir' => 'incluir-atualizacao_tempo',
				'rotaAlterar' => 'alterar-atualizacao_tempo'
			);

        return view('atualizacao_tempo', $data);
    }

    function organizarIntervalos(array $eventos): array
    {
        $inicios = [];
        $terminos = [];

        // Separa os inícios e términos
        foreach ($eventos as $evento) {
            if (isset($evento['Início'])) {
                $inicios[] = $evento['Início'];
            } elseif (isset($evento['Término'])) {
                $terminos[] = $evento['Término'];
            }
        }

        $resultado = [];
        $quantidade = min(count($inicios), count($terminos));

        // Intercala os pares mantendo a estrutura original
        for ($i = 0; $i < $quantidade; $i++) {
            $resultado[] = ['Início' => $inicios[$i]];
            $resultado[] = ['Término' => $terminos[$i]];
        }

        return $resultado;
    }

    /**
     * Calcula o tempo total a partir de um array de eventos.
     *
     * @param array $eventos
     * @return string
     */
    public function calcularTempoTotal(array $eventos): string
    {
        $totalSegundos = 0;

        for ($i = 0; $i < count($eventos) - 1; $i += 2) {
            if (isset($eventos[$i]['Início']) && isset($eventos[$i + 1]['Término'])) {
                $inicio = new DateTime($eventos[$i]['Início']);
                $fim = new DateTime($eventos[$i + 1]['Término']);

                $diff = $fim->getTimestamp() - $inicio->getTimestamp();

                if ($diff > 0) {
                    $totalSegundos += $diff;
                }
            }
        }

        return gmdate('H:i:s', $totalSegundos);
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

    /**
     * Converte o formato HH:MM:SS para um inteiro de segundos.
     *
     * @param string $tempo
     * @return int
     */
    function converteTempoParaInteiro($tempo)
    {
        $partes = explode(':', $tempo);
        return ($partes[0] * 3600) + ($partes[1] * 60) + $partes[2];
    }


}
