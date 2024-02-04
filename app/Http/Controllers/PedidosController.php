<?php

namespace App\Http\Controllers;

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
use App\Models\Maquinas;

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
        $codigo_cliente = !empty($request->input('codigo_cliente')) ? ($request->input('codigo_cliente')) : (!empty($codigo_cliente) ? $codigo_cliente : false);
        $nome_cliente = !empty($request->input('nome_cliente')) ? ($request->input('nome_cliente')) : (!empty($nome_cliente) ? $nome_cliente : false);


        $pedidos = DB::table('pedidos')
            ->join('status', 'pedidos.status_id', '=', 'status.id')
            ->join('ficha_tecnica', 'ficha_tecnica.id', '=', 'pedidos.fichatecnica_id')
            ->join('pessoas', 'pessoas.id', '=', 'pedidos.pessoas_id')
            ->select('pedidos.*', 'ficha_tecnica.ep', 'pessoas.nome_cliente', 'status.nome');

        if (!empty($request->input('status'))){
            $pedidos = $pedidos->where('pedidos.status', '=', $request->input('status'));
        }

        if ($id) {
            $pedidos = $pedidos->where('pedidos.id', '=', $id);
        }

        if ($status_id) {
            $pedidos = $pedidos->where('pedidos.status_id', '=', $status_id);
        }


        if ($codigo_cliente) {
            $pedidos = $pedidos->where('pessoas.codigo_cliente', '=', $codigo_cliente);
        }

        if ($nome_cliente) {
            $pedidos = $pedidos->where('pessoas.nome_cliente', 'like', '%'.$nome_cliente.'%' );
        }


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

                $historico = "Data de entrega do pedido alterado de ".  DateHelpers::formatDate_ddmmYYYY($pedidos[0]->data_entrega) . " para " . DateHelpers::formatDate_ddmmYYYY($request->input("data_entrega"));

            }

            $pedidos_id = $this->salva($request, $historico);

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

    public function salva($request, $historico='')
    {
        DB::transaction(function () use ($request, $historico) {
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

        if (!empty($request->input('status'))){
            $pedidos = $pedidos->where('status', '=', $request->input('status'));
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
            $dados_pedido_status[$pedido->tabelaStatus->nome]['classe'][] = $pedido;
        }

        $MaquinasController = new MaquinasController();

        $Maquinas = new Maquinas();

        $maquinas = $Maquinas->get();

        $qtde_maquinas =$maquinas[0]->qtde_maquinas;
        $horas_maquinas =$maquinas[0]->horas_maquinas;
        $pessoas_acabamento =$maquinas[0]->pessoas_acabamento;
        $pessoas_montagem =$maquinas[0]->pessoas_montagem;
        $pessoas_inspecao =$maquinas[0]->pessoas_inspecao;
        $horas_dia =$maquinas[0]->horas_dia;

        $total_horas_usinagem_maquinas_dia = $horas_maquinas * $qtde_maquinas;

        $total_horas_pessoas_acabamento_dia = $pessoas_acabamento * $horas_dia;
        $total_horas_pessoas_pessoas_montagem_dia = $pessoas_montagem * $horas_dia;
        $total_horas_pessoas_inspecao_dia = $pessoas_inspecao * $horas_dia;

        foreach ($dados_pedido_status as $status => $pedidos) {


            foreach ($pedidos['classe'] as $pedido) {
                $total_tempo_usinagem=$total_tempo_acabamento=$total_tempo_montagem=$total_tempo_inspecao='00:00:00';

                $total_tempo_usinagem = $this->somarHoras($total_tempo_usinagem , $pedido->tabelaFichastecnicas->tempo_usinagem);
                $total_tempo_usinagem = $MaquinasController->multiplicarHoras($total_tempo_usinagem,$pedido->qtde);
                $dados_pedido_status[$status]['pedido'][$pedido->id]['usinagem'] = $total_tempo_usinagem;

                $total_tempo_acabamento = $this->somarHoras($total_tempo_acabamento , $pedido->tabelaFichastecnicas->tempo_acabamento);
                $total_tempo_acabamento = $MaquinasController->multiplicarHoras($total_tempo_acabamento,$pedido->qtde);
                $dados_pedido_status[$status]['pedido'][$pedido->id]['acabamento'] = $total_tempo_acabamento;

                $total_tempo_montagem = $this->somarHoras($total_tempo_montagem , $pedido->tabelaFichastecnicas->tempo_montagem);
                $total_tempo_montagem = $MaquinasController->multiplicarHoras($total_tempo_montagem,$pedido->qtde);
                $dados_pedido_status[$status]['pedido'][$pedido->id]['montagem'] = $total_tempo_montagem;

                $total_tempo_inspecao = $this->somarHoras($total_tempo_inspecao , $pedido->tabelaFichastecnicas->tempo_inspecao);
                $total_tempo_inspecao = $MaquinasController->multiplicarHoras($total_tempo_inspecao,$pedido->qtde);
                $dados_pedido_status[$status]['pedido'][$pedido->id]['inspecao'] = $total_tempo_inspecao;

                $dados_pedido_status[$status]['totais']['total_tempo_usinagem'] = $this->somarHoras(!empty($dados_pedido_status[$status]['totais']['total_tempo_usinagem']) ? $dados_pedido_status[$status]['totais']['total_tempo_usinagem']: '00:00:00' , $total_tempo_usinagem);
                $dados_pedido_status[$status]['totais']['total_tempo_acabamento'] = $this->somarHoras(!empty($dados_pedido_status[$status]['totais']['total_tempo_acabamento']) ? $dados_pedido_status[$status]['totais']['total_tempo_acabamento'] : "00:00:00", $total_tempo_acabamento);
                $dados_pedido_status[$status]['totais']['total_tempo_montagem'] = $this->somarHoras(!empty($dados_pedido_status[$status]['totais']['total_tempo_montagem']) ? $dados_pedido_status[$status]['totais']['total_tempo_montagem'] : "00:00:00", $total_tempo_montagem);
                $dados_pedido_status[$status]['totais']['total_tempo_inspecao'] = $this->somarHoras(!empty($dados_pedido_status[$status]['totais']['total_tempo_inspecao']) ? $dados_pedido_status[$status]['totais']['total_tempo_inspecao'] : "00:00:00", $total_tempo_inspecao);
            }



            $dados_pedido_status[$status]['maquinas_usinagens'] = $this->divideHoursAndReturnWorkDays($dados_pedido_status[$status]['totais']['total_tempo_usinagem'], $total_horas_usinagem_maquinas_dia, $horas_maquinas);
            $dados_pedido_status[$status]['pessoas_acabamento'] = $this->divideHoursAndReturnWorkDays($dados_pedido_status[$status]['totais']['total_tempo_acabamento'], $total_horas_pessoas_acabamento_dia, $horas_dia);
            $dados_pedido_status[$status]['pessoas_montagem'] = $this->divideHoursAndReturnWorkDays($dados_pedido_status[$status]['totais']['total_tempo_montagem'], $total_horas_pessoas_pessoas_montagem_dia, $horas_dia);
            $dados_pedido_status[$status]['pessoas_inspecao'] =$this->divideHoursAndReturnWorkDays($dados_pedido_status[$status]['totais']['total_tempo_inspecao'], $total_horas_pessoas_inspecao_dia, $horas_dia);
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
    public function imprimirOS(Request $request)
    {
        $pedidos = new Pedidos();
        $Fichastecnicasitens = new Fichastecnicasitens();

        $pedidos = $pedidos::with('tabelaStatus', 'tabelaFichastecnicas')->where('id', $request->input('id'))->get();

        $fichastecnicasitens = $Fichastecnicasitens->where('fichatecnica_id', $request->input('id'))->get();

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
                    'alerta1' => $pedidos[0]->tabelaFichastecnicas->alerta_usinagem1,
                    'alerta2' => $pedidos[0]->tabelaFichastecnicas->alerta_usinagem2,
                    'alerta3' => $pedidos[0]->tabelaFichastecnicas->alerta_usinagem3,
                    'alerta4' => $pedidos[0]->tabelaFichastecnicas->alerta_usinagem4,
                    'alerta5' => $pedidos[0]->tabelaFichastecnicas->alerta_usinagem5,
                ],
                1 => [
                    'status' => 'Acabamento',
                    'alerta1' => $pedidos[0]->tabelaFichastecnicas->alerta_acabamento1,
                    'alerta2' => $pedidos[0]->tabelaFichastecnicas->alerta_acabamento2,
                    'alerta3' => $pedidos[0]->tabelaFichastecnicas->alerta_acabamento3,
                    'alerta4' => $pedidos[0]->tabelaFichastecnicas->alerta_acabamento4,
                    'alerta5' => $pedidos[0]->tabelaFichastecnicas->alerta_acabamento5,
                ],
                2 => [
                    'status' => 'Montagem',
                    'alerta1' => $pedidos[0]->tabelaFichastecnicas->alerta_montagem1,
                    'alerta2' => $pedidos[0]->tabelaFichastecnicas->alerta_montagem2,
                    'alerta3' => $pedidos[0]->tabelaFichastecnicas->alerta_montagem3,
                    'alerta4' => $pedidos[0]->tabelaFichastecnicas->alerta_montagem4,
                    'alerta5' => $pedidos[0]->tabelaFichastecnicas->alerta_montagem5,
                ],
                3 => [
                    'status' => 'Inspeção',
                    'alerta1' => $pedidos[0]->tabelaFichastecnicas->alerta_inspecao1,
                    'alerta2' => $pedidos[0]->tabelaFichastecnicas->alerta_inspecao2,
                    'alerta3' => $pedidos[0]->tabelaFichastecnicas->alerta_inspecao3,
                    'alerta4' => $pedidos[0]->tabelaFichastecnicas->alerta_inspecao4,
                    'alerta5' => $pedidos[0]->tabelaFichastecnicas->alerta_inspecao5,
                ],
                4 => [
                    'status' => 'Expedição',
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
        return view('imprimir_os', $data);
    }

    public function imprimirMP(Request $request)
    {
        $Fichastecnicasitens = new Fichastecnicasitens();

        $fichastecnicasitens = $Fichastecnicasitens->where('fichatecnica_id', $request->input('id'))->get();

        $data = [
            'fichastecnicasitens' => $fichastecnicasitens,
        ];

        return view('imprimir_mp', $data);
    }
    function divideHoursAndReturnWorkDays($totalHours, $smallerHours, $horas_diarias) {
        // Extrair as horas, minutos e segundos do total
        list($totalHours, $totalMinutes, $totalSeconds) = explode(':', $totalHours);

        // Calcular o total de segundos
        $totalSeconds = $totalHours * 3600 + $totalMinutes * 60 + $totalSeconds;

        // Calcular o valor menor em segundos
        $smallerSeconds = $smallerHours * 3600;

        // Dividir o total de segundos pelo valor menor
        $resultDays = $totalSeconds / $smallerSeconds / $horas_diarias;

        // Formatar o resultado
        $resultTime = sprintf("%.2f dias de trabalho", $resultDays);

        return $resultTime;
    }



    function divideHoursIntoDays($time, $divisor) {
        // Extrair as horas, minutos e segundos
        list($hours, $minutes, $seconds) = explode(':', $time);

        // Calcular o total de segundos
        $totalSeconds = $hours * 3600 + $minutes * 60 + $seconds;

        // Dividir o total de segundos pelo divisor
        $resultSeconds = $totalSeconds / $divisor;

        // Calcular dias, horas, minutos e segundos resultantes
        $daysResult = floor($resultSeconds / (24 * 3600));
        $resultSeconds %= (24 * 3600);
        $hoursResult = floor($resultSeconds / 3600);
        $resultSeconds %= 3600;
        $minutesResult = floor($resultSeconds / 60);
        $secondsResult = $resultSeconds % 60;

        // Formatar o resultado
        $resultTime = sprintf("%d dias %02d:%02d:%02d", $daysResult, $hoursResult, $minutesResult, $secondsResult);

        return $resultTime;
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
    * @return \Illuminate\Contracts\Support\Renderable
    */
    public function getAllfichastecnicas() {
        $Status = new Fichastecnicas();
        return $Status->where('status', '=', 'A')->get();

    }
    /**
    * Show the application dashboard.
    *
    * @return \Illuminate\Contracts\Support\Renderable
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

    function subtrairHoras($hora1, $hora2) {
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
}
