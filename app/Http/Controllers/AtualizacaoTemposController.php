<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Status;
use App\Providers\DateHelpers;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AjaxController;
use App\Models\Funcionarios;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;

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
                $pedidos = $pedidos->where('funcionarios.nome', 'like', '%'.strtolower($request->input('responsavel')).'%');
            }

            $pedidos->select('pedidos.*',
                'ficha_tecnica.ep as ep',
                'ficha_tecnica.tempo_montagem as pedido_tempo_montagem',
                'ficha_tecnica.tempo_montagem_torre as pedido_tempo_montagem_torre',
                'ficha_tecnica.tempo_inspecao as pedido_tempo_inspecao',
                'status.id as id_status');


            $pedidos = $pedidos->orderBy('ficha_tecnica.ep', 'asc');

            $pedidos->whereBetween('data_gerado', [
                \Carbon\Carbon::now()->sub('10 months')->format('Y-m-d'),
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


        $funcionarios = new Funcionarios();
        $funcionarios = $funcionarios->where(column: 'perfil', operator: '=', value: '5');

        $funcionarios = $funcionarios->get();

        $status = new Status();
        $status = $status->where('status', '=', 'A')->get();


        $tela = 'pesquisa';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'Atualização de Tempos',
				'pedidos'=> $pedidos,
                'status' => $status,
                'funcionarios' => $funcionarios,
				'request' => $request,
				'rotaIncluir' => 'incluir-atualizacao_tempo',
				'rotaAlterar' => 'alterar-atualizacao_tempo'
			);

        return view('atualizacao_tempo', $data);
    }

    function organizarIntervalos(array $eventos): array
    {
        $resultado = [];
        $buffer = [];

        foreach ($eventos as $evento) {
            if (isset($evento['Início'])) {
                if (!empty($buffer)) {
                    $resultado = array_merge($resultado, $buffer);
                    $buffer = [];
                }
                $buffer[] = ['Início' => $evento['Início']];
            } elseif (isset($evento['Pausa'])) {
                $buffer[] = ['Pausa' => $evento['Pausa']];
            } elseif (isset($evento['Continuar'])) {
                $buffer[] = ['Continuar' => $evento['Continuar']];
            } elseif (isset($evento['Término'])) {
                $buffer[] = ['Término' => $evento['Término']];
                // Finaliza o intervalo após término
                $resultado = array_merge($resultado, $buffer);
                $buffer = [];
            }
        }

        // Caso sobre algum buffer não finalizado
        if (!empty($buffer)) {
            $resultado = array_merge($resultado, $buffer);
        }

        return $resultado;
    }


    /**
 * Calcula o tempo total "ativo" considerando os eventos:
 * Início -> (Pausa <-> Continuar)* -> Término
 *
 * - Soma somente os períodos em que estava "rodando" (não pausado).
 * - Ignora durações negativas.
 * - Se houver um Início sem Término e você quiser contar até "agora",
 *   passe $agora. Caso contrário, o trecho será ignorado.
 *
 * @param array $eventos  Array de eventos cronológicos.
 * @param DateTimeInterface|null $agora Opcional: momento atual para sessões abertas.
 * @return string Tempo total em H:i:s
 */
public function calcularTempoTotal(array $eventos, ?DateTimeInterface $agora = null): string
{
    $totalSegundos = 0;

    $rodando = false;   // existe uma sessão aberta (entre Início e Término)?
    $pausado = false;   // sessão atual está pausada?
    $inicioAtivo = null; // timestamp do início do trecho ativo atual

    $agora = $agora ?? null; // só usa se precisar

    foreach ($eventos as $evento) {
        if (isset($evento['Início'])) {
            // Se já estava rodando e sem término, fechamos a sessão anterior (melhor ignorar/validar)
            if ($rodando && !$pausado && $inicioAtivo !== null) {
                // não somamos nada aqui porque não houve Término, a não ser que queira até "agora"
                if ($agora instanceof DateTimeInterface) {
                    $totalSegundos += max(0, $agora->getTimestamp() - $inicioAtivo->getTimestamp());
                }
            }

            $rodando = true;
            $pausado = false;
            $inicioAtivo = new DateTimeImmutable($evento['Início']);

        } elseif (isset($evento['Pausa'])) {
            // Só faz sentido pausar se está rodando e não está pausado
            if ($rodando && !$pausado && $inicioAtivo !== null) {
                $pausa = new DateTimeImmutable($evento['Pausa']);
                $diff = $pausa->getTimestamp() - $inicioAtivo->getTimestamp();
                if ($diff > 0) {
                    $totalSegundos += $diff;
                }
                $pausado = true;
                $inicioAtivo = null;
            }

        } elseif (isset($evento['Continuar'])) {
            // Só continua se está rodando e está pausado
            if ($rodando && $pausado) {
                $inicioAtivo = new DateTimeImmutable($evento['Continuar']);
                $pausado = false;
            }

        } elseif (isset($evento['Término'])) {
            if ($rodando) {
                // Se não está pausado, fechar o último trecho ativo
                if (!$pausado && $inicioAtivo !== null) {
                    $fim = new DateTimeImmutable($evento['Término']);
                    $diff = $fim->getTimestamp() - $inicioAtivo->getTimestamp();
                    if ($diff > 0) {
                        $totalSegundos += $diff;
                    }
                }
                // encerra a sessão
                $rodando = false;
                $pausado = false;
                $inicioAtivo = null;
            }
        }
    }

    // Se terminar o loop ainda rodando e não pausado, decidir se conta até "agora"
    if ($rodando && !$pausado && $inicioAtivo !== null && $agora instanceof DateTimeInterface) {
        $totalSegundos += max(0, $agora->getTimestamp() - $inicioAtivo->getTimestamp());
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
