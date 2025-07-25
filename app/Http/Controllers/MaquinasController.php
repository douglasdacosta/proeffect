<?php

namespace App\Http\Controllers;

use App\Models\Maquinas;
use App\Models\ProducaoMaquinas;
use App\Providers\DateHelpers;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;

class MaquinasController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {}

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $this->middleware('auth');
        $Maquinas = new Maquinas();

        if(count($request->input()) > 0) {

            $this->salva($request);
        }
        $Maquinas = $Maquinas->get();
        $data = array(
            'tela' => 'maquinas',
            'maquinas' => $Maquinas
        );


        return view('maquinas', $data);
    }

    public function producaoMaquinas(Request $request)
    {
        $this->middleware('auth');
        $ProducaoMaquinas = new ProducaoMaquinas();
        $colors = ['#FE6F5E','#FDBCB4','#F88379','#FFC0CB','#FFD700','#FF6347','#FF8200','#DAA06D','#CC7F3B','#DDD06A','#E48400','#E5AA70','#E4D96F','#7BA05B','#ACE1AF','#03C03C','#00CCCC','#93C572','#BCB88A','#76FF7A','#2E8B57','#40826D','#00BFFF','#008B8B','#007BA7','#99FFFF','#81D8D0','#5D8AA8','#0095B6','#6F00FF','#BF00FF','#FF00FF','#DDA0DD','#E0B0FF','#AA98A9','#EE82EE','#FBAED2','#FFCBA4','#F0EAD6','#EFDFBB','#F1E9D2'];
        $data = date('Y-m-d');
        $data_fim = date('Y-m-d');
        $hora = '01:00:01';
        $hora_fim = '23:59:59';

        if(!empty($request->input('numero_cnc'))) {
            $numero_cnc = $request->input('numero_cnc');
            $ProducaoMaquinas = $ProducaoMaquinas->where('numero_cnc', '=', $numero_cnc);
        }


        if(!empty($request->input('hora')) && !empty($request->input('hora_fim') )) {
            $hora = $request->input('hora');
            $hora_fim = $request->input('hora_fim');
        }

        if(!empty($request->input('hora')) && empty($request->input('hora_fim') )) {
            $hora = $request->input('hora');
        }

        if(empty($request->input('hora')) && !empty($request->input('hora_fim') )) {
            $hora_fim = $request->input('hora_fim');
        }

        if(!empty($request->input('created_at')) && !empty($request->input('created_at_fim') )) {
            $data = DateHelpers::formatDate_dmY($request->input('created_at'));
            $data_fim = DateHelpers::formatDate_dmY($request->input('created_at_fim'));
        }

        if(!empty($request->input('created_at')) && empty($request->input('created_at_fim') )) {
            $data = DateHelpers::formatDate_dmY($request->input('created_at'));
        }

        if(empty($request->input('created_at')) && !empty($request->input('created_at_fim') )) {
            $data_fim = DateHelpers::formatDate_dmY($request->input('created_at_fim'));
        }

        $ProducaoMaquinas = $ProducaoMaquinas->whereBetween('created_at', [$data. ' ' . $hora, $data_fim. ' ' . $hora_fim])
                                                ->orderby('numero_cnc','asc')
                                                ->orderby('created_at','asc')
                                                ->get();


        $horas_filtradas = $this->diferencaHoras($hora, $hora_fim);
        $hora_servico_periodo_manha = $horas_filtradas;
        $hora_servico_periodo_tarde = $horas_filtradas;
        $qtdeServico_manha = $qtdeServico_tarde =   0;

        $dados=[];
        $chave_antes = $chave = '';

        $horas_usinagem_tarde =$horas_usinagem_manha = [];
        $pedidosController = new PedidosController();
        if ($request->input('listarpor') == 1) { #0  por hora
            $ProdMaq = new ProducaoMaquinas();
            foreach ($ProducaoMaquinas as $key => $producaoMaquina) {
                $created_at = DateTime::createFromFormat('Y-m-d H:i:s', $producaoMaquina['created_at']);

                $numero_maquina = $producaoMaquina['numero_cnc'];
                $hora_loop = $created_at->format('H');
                $chave = $created_at->format('d/m/Y').$producaoMaquina['numero_cnc'].$hora_loop;
                $turno = ($hora_loop < 14 ) ? 'Manhã' : 'Tarde';
                $turno_chave = ($hora_loop < 14 ) ? 'manha' : 'tarde';

                $data_hora_pesquisa = $created_at->format('Y-m-d H');

                $ProdMaqAntes = $ProdMaq->where('created_at', '<', $data_hora_pesquisa.':00:00')
                                    ->where('numero_cnc', '=', $numero_maquina)
                                    ->orderby('created_at', 'desc')->limit(1)->get()->toArray();

                if(empty($ProdMaqAntes[0]['HorasServico'])) {
                    $ProdMaq = new ProducaoMaquinas();
                    $ProdMaqAntes = $ProdMaq->where('created_at', '=', $created_at)
                                    ->where('numero_cnc', '=', $numero_maquina)
                                    ->orderby('created_at', 'desc')->limit(1)->get()->toArray();
                }
                $usinados[$chave][$turno_chave]['horas_usinadas'] = $producaoMaquina['HorasServico'] - $ProdMaqAntes[0]['HorasServico'];
                $usinados[$chave][$turno_chave]['metrosPercorridos'] = $producaoMaquina['metrosPercorridos'] - $ProdMaqAntes[0]['metrosPercorridos'];
                $usinados[$chave][$turno_chave]['qtdeServico'] = $producaoMaquina['qtdeServico'] - $ProdMaqAntes[0]['qtdeServico'];
            }

            $total_horas_tabalhadas=$total_horas_usinadas='00:00:00';
            $tempo_temporario = $sobra_tempo = '00:00:00';
            $chave_anterior = '';
            foreach ($ProducaoMaquinas as $key => $producaoMaquina) {
                $created_at = DateTime::createFromFormat('Y-m-d H:i:s', $producaoMaquina['created_at']);

                $hora = $created_at ->format('H');
                $chave = $created_at->format('d/m/Y').$producaoMaquina['numero_cnc'].$hora;
                $hora_loop = $created_at->format('H');
                $turno = ($hora_loop < 14 ) ? 'Manhã' : 'Tarde';
                $turno_chave = ($hora_loop < 14 ) ? 'manha' : 'tarde';
                $horas_usinadas =$usinados[$chave][$turno_chave]['horas_usinadas'];

                $horas_usinadas = $this->converterParaHoras($horas_usinadas);



                if($chave_anterior != $chave) {

                    if($this->horaMaior($sobra_tempo, '00:00:00')) {
                        $tempo_temporario = $pedidosController->somarHoras($tempo_temporario, $sobra_tempo);
                        $sobra_tempo = '00:00:00';
                    }

                    if($horas_usinadas == '00:00:00') {

                        $tempo_temporario = $pedidosController->somarHoras($tempo_temporario, '01:00:00');

                        $horas_usinadas = '01:00:00';

                    } else {
                        if($this->horaMaior($horas_usinadas, '01:00:00')) {
                            $sobra_tempo = $pedidosController->subtrairHoras($horas_usinadas, '01:00:00');
                            $horas_usinadas  = '01:00:00';
                        }else {
                            if($this->horaMaior($horas_usinadas, '01:00:00')) {
                                $horas_usinadas = $pedidosController->subtrairHoras($horas_usinadas, $tempo_temporario);
                            }
                        }
                        $tempo_temporario = '00:00:00';

                    }

                    $chave_anterior = $chave;

                    $horas_tabalhadas = '01:00:00';
                    $dados[$chave][$turno_chave] = [
                        'maquina_cnc' =>  $producaoMaquina['numero_cnc'],
                        'turno' => $turno,
                        'data' =>  $created_at->format('d/m/Y'). ' ' . $hora_loop .':00:00',
                        'horasTrabalho' => $horas_tabalhadas,
                        'total_horas_usinadas' => $horas_usinadas,
                        'metrosPercorridos' => $usinados[$chave][$turno_chave]['metrosPercorridos'],
                        'qtdeServico' => $usinados[$chave][$turno_chave]['qtdeServico'],
                        'percentual' => $this->calcularPorcentagemUsinada($horas_usinadas,$horas_tabalhadas)
                    ];
            }
                $data = $created_at->format('d/m/Y');

            }
            foreach ($dados as $chave => $dado) {
                foreach ($dado as $data => $dados_dia) {
                        $total_horas_tabalhadas = $pedidosController->somarHoras($dados_dia['horasTrabalho'], $total_horas_tabalhadas);
                        $total_horas_usinadas = $pedidosController->somarHoras($dados_dia['total_horas_usinadas'], $total_horas_usinadas);
                }

            }

        } else { #0 = por data

            $data_agora = (New DateTime);
            $total_horas_tabalhadas=$total_horas_usinadas='00:00:00';
            foreach ($ProducaoMaquinas as $key => $producaoMaquina) {
                $created_at = DateTime::createFromFormat('Y-m-d H:i:s', $producaoMaquina['created_at']);
                $hora_loop = $created_at->format('H');
                $chave = $created_at->format('d/m/Y').$producaoMaquina['numero_cnc'];
                $data = $created_at->format('d/m/Y');

                if($chave_antes != $chave) {
                    $chave_antes = $chave;
                    $hora_servico_periodo_manha = $horas_filtradas;
                    $hora_servico_periodo_tarde = $horas_filtradas;
                    $qtdeServico_manha = $qtdeServico_tarde =  0;

                }


                if($hora_loop < '14') {
                    if ($hora_loop < 6) {
                        continue;
                    }
                    $periodo_horas_manha[$chave][]= $created_at->format('H:i:s');
                    $horas_usinagem_manha[$chave][]= $producaoMaquina['HorasServico'];
                } else {
                    if ($hora_loop > 22) {
                        continue;
                    }
                    $periodo_horas_tarde[$chave][]= $created_at->format('H:i:s');
                    $horas_usinagem_tarde[$chave][]= $producaoMaquina['HorasServico'];
                }
            }



            foreach($horas_usinagem_manha as $chave => $valor){
                $horas_usinagem_manha_anterior[$chave] = '0';
                $ProdMaq = new ProducaoMaquinas();

                $data_inicio = DateHelpers::formatDate_dmY(substr($chave, 0,10));
                $hora_inicio = $periodo_horas_manha[$chave][0];
                $maquina = substr($chave, 10, 1);

                $ProdMaq = $ProdMaq->where('data', '<=', $data_inicio)
                                    ->where('hora', '<=', $hora_inicio)
                                    ->where('numero_cnc', '=', $maquina)
                                    ->orderby('created_at', 'desc')->limit(1)->get()->toArray();

                if(empty($ProdMaq[0]['HorasServico'])) {
                    $ProdMaq = new ProducaoMaquinas();
                    $ProdMaq = $ProdMaq->where('data', '=', $data_inicio)
                                    ->where('numero_cnc', '=', $maquina)
                                    ->orderby('created_at', 'desc')->limit(1)->get()->toArray();
                }

                $horas_usinagem_manha_anterior[$chave]=number_format($ProdMaq[0]['HorasServico'], 3, '.', '');

                $distancia_manha_antes[$chave] = $ProdMaq[0]['metrosPercorridos'];
                $numero_trabalho_manha_antes[$chave] = $ProdMaq[0]['qtdeServico'];

            }

            foreach($horas_usinagem_tarde as $chave => $valor){
                $ProdMaq = new ProducaoMaquinas();
                $data_inicio =   DateHelpers::formatDate_dmY(substr($chave, 0,10));
                $hora_inicio =  $periodo_horas_tarde[$chave][0];
                $maquina =   substr($chave, 10, 1);
                $ProdMaq = $ProdMaq->where('data', '<=', $data_inicio)
                                    ->where('hora', '<=', $hora_inicio)
                                    ->where('numero_cnc', '=', $maquina)
                                    ->orderby('created_at', 'desc')->limit(1)->get()->toArray();

                if(empty($ProdMaq[0]['HorasServico'])) {
                    $ProdMaq = new ProducaoMaquinas();
                    $ProdMaq = $ProdMaq->where('data', '=', $data_inicio)
                                    ->where('numero_cnc', '=', $maquina)
                                    ->orderby('created_at', 'desc')->limit(1)->get()->toArray();
                }
                $horas_usinagem_tarde_anterior[$chave]= number_format($ProdMaq[0]['HorasServico'], 3, '.', '');
                $distancia_tarde_antes[$chave] = $ProdMaq[0]['metrosPercorridos'];
                $numero_trabalho_tarde_antes[$chave] = $ProdMaq[0]['qtdeServico'];
            }



            foreach ($ProducaoMaquinas as $key => $producaoMaquina) {

                $created_at = DateTime::createFromFormat('Y-m-d H:i:s', $producaoMaquina['created_at']);
                $chave = $created_at->format('d/m/Y').$producaoMaquina['numero_cnc'];
                $data = $created_at->format('d/m/Y');

                if($chave_antes != $chave) {
                    $chave_antes = $chave;
                    $hora_servico_periodo_tarde = $horas_filtradas;
                    $qtdeServico_manha = $qtdeServico_tarde = 0;

                }

                if($created_at->format('H') < '14') {

                    $total_horas_usinadas_manha = $this->converterParaHoras($producaoMaquina['HorasServico'] - $horas_usinagem_manha_anterior[$chave]);

                    if($request->input('hora') && $this->horaMaior($request->input('hora'), '06:00:00')) {
                        $inicio_periodo = $request->input('hora');
                    } else {
                        $inicio_periodo = '06:00:00';
                    }


                    if($request->input('hora_fim') && $this->horaMaior('14:00:00', $request->input('hora_fim'))) {
                        $final_periodo = $request->input('hora_fim');
                    } else {
                        $final_periodo = '14:00:00';
                        if ($data_agora->format('H') > 6 && $data_agora->format('H') < 14){
                            $final_periodo = $data_agora->format('H:i:s');
                        }
                    }

                    if($this->horaMaior(end($periodo_horas_manha[$chave]), '14:00:00') ) {
                        $final_periodo = end($periodo_horas_manha[$chave]);
                        $final_periodo = '14:00:00';
                    }

                    $hora_servico_periodo_manha =  $this->diferencaHoras($inicio_periodo, $final_periodo);
                    $qtdeServico_manha = $qtdeServico_manha + $producaoMaquina['qtdeServico'];

                    $dados[$chave]['manha']['maquina_cnc'] = $producaoMaquina['numero_cnc'];
                    $dados[$chave]['manha']['turno'] = 'Manhã';
                    $dados[$chave]['manha']['data'] = $data;
                    $dados[$chave]['manha']['horasTrabalho'] = $hora_servico_periodo_manha;
                    $dados[$chave]['manha']['total_horas_usinadas'] = $total_horas_usinadas_manha;
                    $dados[$chave]['manha']['metrosPercorridos'] = $producaoMaquina['metrosPercorridos'] - $distancia_manha_antes[$chave] ;
                    $dados[$chave]['manha']['qtdeServico'] = $producaoMaquina['qtdeServico'] - $numero_trabalho_manha_antes[$chave];

                } else {

                    $total_horas_usinadas_tarde = $this->converterParaHoras($producaoMaquina['HorasServico'] - $horas_usinagem_tarde_anterior[$chave]);

                    if($request->input('hora_fim') && $this->horaMaior('22:00:00', $request->input('hora_fim'))) {
                        $final_periodo = $request->input('hora_fim');
                    } else {
                        $final_periodo = '22:00:00';
                        if ($data_agora->format('H') > 14 && $data_agora->format('H') < 22){
                            $final_periodo = $data_agora->format('H:i:s');
                        }
                    }

                    if($this->horaMaior(end($periodo_horas_tarde[$chave]), '22:00:00') ) {
                        $final_periodo = end($periodo_horas_tarde[$chave]);
                        $final_periodo = '22:00:00';
                    }

                    $hora_servico_periodo_tarde =  $this->diferencaHoras('14:00:00', $final_periodo);
                    $qtdeServico_tarde = $qtdeServico_tarde + $producaoMaquina['qtdeServico'];
                    $dados[$chave]['tarde']['maquina_cnc'] = $producaoMaquina['numero_cnc'];
                    $dados[$chave]['tarde']['turno'] = 'Tarde';
                    $dados[$chave]['tarde']['data'] = $data;
                    $dados[$chave]['tarde']['horasTrabalho'] = $hora_servico_periodo_tarde;
                    $dados[$chave]['tarde']['total_horas_usinadas'] = $total_horas_usinadas_tarde;
                    $dados[$chave]['tarde']['metrosPercorridos'] = $producaoMaquina['metrosPercorridos'] - $distancia_tarde_antes[$chave] ;
                    $dados[$chave]['tarde']['qtdeServico'] = $producaoMaquina['qtdeServico'] - $numero_trabalho_tarde_antes[$chave];
                }

            }
            foreach ($dados as $turno => $dado) {
                foreach ($dado as $data => $dados_dia) {

                    $dados[$turno][$data]['percentual'] = '0';
                    if($dados_dia['total_horas_usinadas'] != '00:00:00' && $dados_dia['horasTrabalho'] != '00:00:00') {
                        $total_horas_tabalhadas = $pedidosController->somarHoras($dados_dia['horasTrabalho'], $total_horas_tabalhadas);
                        $total_horas_usinadas = $pedidosController->somarHoras($dados_dia['total_horas_usinadas'], $total_horas_usinadas);
                        $dados[$turno][$data]['percentual'] = $this->calcularPorcentagemUsinada($dados_dia['total_horas_usinadas'],$dados_dia['horasTrabalho']);
                    }
                }

            }
        }

        $dados_array = [];
        $total_pecentual = $this->calcularPorcentagemUsinada($total_horas_usinadas,$total_horas_tabalhadas);
        foreach($dados as $dados2) {
            foreach($dados2 as $dado) {
                $dados_array[] = $dado;
            }
        }
        if(!empty($dados_array)) {

            $this->ordenarPorTurno($dados_array);
        }

        $data = array(
            'tela' => 'pesquisa',
            'nome_tela' => 'Produção Máquinas',
            'producao_maquinas' => $dados_array,
            'totais' => ['total_horas_tabalhadas' => $total_horas_tabalhadas, 'total_horas_usinadas' => $total_horas_usinadas, 'total_pecentual' => $total_pecentual],
            'request' => $request,
            'colors' => $colors
        );

        return view('producao_maquinas', $data);
    }

    function ordenarPorTurno(&$array) {
        usort($array, function($a, $b) {
            $dataComparison = strcmp($a['data'], $b['data']);

            if ($dataComparison === 0) {
                $turnoComparison = strcmp($a['turno'], $b['turno']);

                if ($turnoComparison === 0) {
                    return $a['maquina_cnc'] - $b['maquina_cnc'];
                }

                return $turnoComparison;
            }

            return $dataComparison;
        });
    }

    public function getHorasTurno(Request $request){
        try {
            $array = $request->input();

            if(!$this->checkAcesso($request)) {
                return response('Erro na identificação', 401);
            }

            $numero_cnc = $array['NUMERO_CNC'];
            $data_atual = Carbon::now()->format('Y-m-d');

            $data_hora_inicio = $data_atual." 14:00:00";
            $hora_inicio = "14:00:00";
            $hora_fim = "22:00:00";
            $turno = "Turno 14:00 as 22:00";
            if($this->turno_mamnha()) {
                $data_hora_inicio = $data_atual." 06:00:00";
                $hora_inicio = "06:00:00";
                $hora_fim =  "13:59:59";
                $turno = "Turno 06:00 as 14:00";
            }

            $producaoMaquinas = new ProducaoMaquinas();
            $datahora_atual = new DateTime();
            $datahora_atual = $datahora_atual->format('Y-m-d H:i:s');
            $producaoMaquinas = $producaoMaquinas->where('numero_cnc', '=', $numero_cnc);
            $producaoMaquinas->whereBetween('created_at', [$data_hora_inicio, $datahora_atual]);
            $producaoMaquinas->orderby('created_at', 'desc')->limit('1');
            $producaoMaquinas = $producaoMaquinas->get();

            $total_horas_usinadas = "00:00:00";
            $total_horas_trabalhadas = "00:00:00";
            $hora_atual = new DateTime();
            $hora_atual = $hora_atual->format('H:i:s');
            $hora = new DateTime();
            $hora = $hora->format('H');

            $producaoMaquinas_antes = new ProducaoMaquinas();
            $producaoMaquinas_antes = $producaoMaquinas_antes->where('created_at', '<=', $data_hora_inicio)
                                ->where('numero_cnc', '=', $numero_cnc)
                                ->orderby('created_at', 'desc')->limit(1)->get()->toArray();

            $usinadas_anterior_turno=$producaoMaquinas_antes[0]['HorasServico'];
            $dados = [
                'turno' => 'Nenhum dado do turno',
                'textoHorasTrabalhadas' => "Horas Trabalhadas: 0",
                'textoHorasUsinadas' => "Horas Usinadas: 0",
                'horasTrabalhadasq'=>"0",
                'horasUsinadas'=>"0",
            ];
            if($hora > 5 && count($producaoMaquinas)) {

                foreach ($producaoMaquinas as $key => $producaoMaquina) {
                    $HorasServico = $producaoMaquina['HorasServico'] -$usinadas_anterior_turno;
                }

                $total_horas_trabalhadas = $this->diff_hours_between_dates($hora_inicio, $hora_atual);
                $total_horas_usinadas = $this->converterParaHoras($HorasServico);
                $horasTrabalhadasq = $this->calcularPorcentagemTrabalhada($hora_inicio, $hora_fim, $hora_atual);
                $horasUsinadas = $this->calcularPorcentagemUsinada($total_horas_usinadas,$total_horas_trabalhadas);
                $total_horas_usinadas = explode(':', $total_horas_usinadas);

                $dados = [
                    'turno' => $turno,
                    'textoHorasTrabalhadas' => "Horas Trabalhadas: ". substr($total_horas_trabalhadas, 0, 5),
                    'textoHorasUsinadas' => "Horas Usinadas: ". $total_horas_usinadas[0].":".$total_horas_usinadas[1],
                    'horasTrabalhadasq'=>"$horasTrabalhadasq",
                    'horasUsinadas'=>"$horasUsinadas",
                ];
            }
                return response($dados, 200);
            } catch (\Throwable $th) {
                info($th);
                return response('erro', 200);
            }

        if(!$this->checkAcesso($request)) {
            return response('Erro na identificação', 401);
        }



    }

    public function salvaDadosMaquina(Request $request){

        try {
            $array = $request->input();

            if(!$this->checkAcesso($request)) {
                return response('Erro na identificação', 401);
            }

            $hora_atual = new DateTime();
            $hora_atual = $hora_atual->format('H:i:s');

            $data_atual = new DateTime();
            $data_atual = $data_atual->format('Y-m-d');

            $resultado['numero_cnc'] = $array['NUMERO_CNC'];
            $resultado['HorasServico'] = $array['stshUsageTimeHoursService'];
            $resultado['metrosPercorridos'] = $array['stsTraveledDistMetersService'];
            $resultado['qtdeServico'] = $array['stsNumJobsDoneService'];

            $producaoMaquinas = new ProducaoMaquinas();
            $producaoMaquinas->numero_cnc = $resultado['numero_cnc'];
            $producaoMaquinas->HorasServico = $resultado['HorasServico'];
            $producaoMaquinas->metrosPercorridos = $resultado['metrosPercorridos'];
            $producaoMaquinas->qtdeServico = $resultado['qtdeServico'];
            $producaoMaquinas->data = $data_atual;
            $producaoMaquinas->hora = $hora_atual;

            $producaoMaquinas->save();

            return response('sucesso', 200);
        } catch (\Throwable $th) {
            info($th);
            return response('erro', 200);
        }


    }

    function calcularPorcentagemUsinada($horasTrabalhadas,$periodoTotal) {
        list($h, $m, $s) = explode(':', $periodoTotal);
        $segundos_total = $h * 3600 + $m * 60 + $s;

        list($h, $m, $s) = explode(':', $horasTrabalhadas);
        $segundos_trabalhados = $h * 3600 + $m * 60 + $s;

        if($segundos_trabalhados == 0 || $segundos_total == 0) {
            return 0;
        }

        $percentual = ($segundos_trabalhados / $segundos_total) * 100;
        return (float) number_format($percentual, 0); // Arredondando para 2 casas decimais
    }

    function calcularPorcentagemTrabalhada($inicioPeriodo, $fimPeriodo, $trabalhadas) {
        $inicioSegundos = strtotime($inicioPeriodo);
        $fimSegundos = strtotime($fimPeriodo);
        $trabalhadasSegundos = strtotime($trabalhadas);

        // Calculando o intervalo de tempo total do período em segundos
        $intervaloTotalSegundos = $fimSegundos - $inicioSegundos;

        // Calculando o intervalo de tempo trabalhado em segundos
        $intervaloTrabalhadoSegundos = $trabalhadasSegundos - $inicioSegundos;

        // Calculando a porcentagem de horas trabalhadas em relação ao período total
        $percentualTrabalhado = ($intervaloTrabalhadoSegundos / $intervaloTotalSegundos) * 100;
        return (float)number_format($percentualTrabalhado, 0);
    }

    public function turno_mamnha() {
        $hora_atual = new DateTime();
        $hora_atual = $hora_atual->format('H:i:s');
        $hora = DateTime::createFromFormat('H:i', '14:00');
        return $hora_atual < $hora->format('H:i:s');
    }

    private function converterParaHoras($floatHours) {
    // Extrair as horas e os minutos do valor float
    $hours = floor($floatHours);
    $minutes = ($floatHours - $hours) * 60;

    // Formatar as horas e os minutos para o formato de tempo
    $timeFormat = sprintf('%02d:%02d:00', $hours, $minutes);

    return $timeFormat;
    }

    private function checkAcesso(Request $request){
        $dados = $request->input();
        if($dados['TOKEN'] != env('TOKEN_API')){
            return false;
        }
        if($dados['LOGIN'] != env('LOGIN_API')){
            return false;
        }
        if($dados['SENHA'] != env('SENHA_API')){
            return false;
        }
        return true;

    }

    public function salva($request) {
        $maquinas = new Maquinas();

        if($request->input('id')) {
            $maquinas = $maquinas::find($request->input('id'));
        }
        $maquinas->qtde_maquinas = $request->input('qtde_maquinas');
        $maquinas->horas_maquinas = $request->input('horas_maquinas');
        $maquinas->pessoas_acabamento = $request->input('pessoas_acabamento');
        $maquinas->pessoas_montagem = $request->input('pessoas_montagem');
        $maquinas->pessoas_montagem_torres = $request->input('pessoas_montagem_torres');
        $maquinas->pessoas_inspecao = $request->input('pessoas_inspecao');
        $maquinas->horas_dia = $request->input('horas_dia');
        $maquinas->prazo_entrega = $request->input('prazo_entrega');
        $maquinas->prazo_usinagem = $request->input('prazo_usinagem');
        $maquinas->prazo_acabamento = $request->input('prazo_acabamento');
        $maquinas->prazo_montagem = $request->input('prazo_montagem');
        $maquinas->prazo_inspecao = $request->input('prazo_inspecao');
        $maquinas->prazo_embalar = $request->input('prazo_embalar');
        $maquinas->prazo_expedicao = $request->input('prazo_expedicao');

        $maquinas->save();

        return $maquinas->id;
    }

    function horasParaDias($horas) {
        // Divide a string da hora em horas, minutos e segundos
        list($horas, $minutos, $segundos) = explode(':', $horas);

        // Converte horas, minutos e segundos para segundos
        $totalSegundos = $horas * 3600 + $minutos * 60 + $segundos;

        // Calcula o número de dias
        $dias = $totalSegundos / (24 * 3600);

        return $dias;
    }

    function horasParaDiasTrabalhados($horas) {
        // Divide a string da hora em horas, minutos e segundos
        list($horas, $minutos, $segundos) = explode(':', $horas);

        // Converte horas, minutos e segundos para segundos
        $totalSegundos = $horas * 3600 + $minutos * 60 + $segundos;

        // Define o número de horas de trabalho por dia
        $horasPorDia = 8;

        // Calcula o número de dias de trabalho
        $diasTrabalhados = $totalSegundos / ($horasPorDia * 3600);

        return $diasTrabalhados;
    }

    function multiplicarHoras($horas, $multiplicador) {
        // Divide a string da hora em horas, minutos e segundos
        list($horas, $minutos, $segundos) = explode(':', $horas);

        // Converte horas, minutos e segundos para segundos
        $totalSegundos = $horas * 3600 + $minutos * 60 + $segundos;

        // Multiplica o total de segundos pelo multiplicador
        $novoTotalSegundos = $totalSegundos * $multiplicador;

        // Converte o novo total de segundos de volta para horas, minutos e segundos
        $novasHoras = floor($novoTotalSegundos / 3600);
        $novosMinutos = floor(($novoTotalSegundos % 3600) / 60);
        $novosSegundos = $novoTotalSegundos % 60;

        // Formata a string do resultado
        $resultadoFormatado = sprintf("%02d:%02d:%02d", $novasHoras, $novosMinutos, $novosSegundos);

        return $resultadoFormatado;
    }

    function diff_hours_between_dates($dataInicio, $dataFim) {
        // Converte as datas para timestamps
    $inicioTimestamp = strtotime($dataInicio);
    $fimTimestamp = strtotime($dataFim);

    // Calcula a diferença em segundos
    $diferencaSegundos = $fimTimestamp - $inicioTimestamp;

    // Calcula o intervalo de horas, minutos e segundos
    $horas = floor($diferencaSegundos / 3600); // 1 hora = 3600 segundos
    $minutos = floor(($diferencaSegundos % 3600) / 60); // 1 minuto = 60 segundos
    $segundos = $diferencaSegundos % 60;

    // Formata o intervalo de horas
    $intervalo = sprintf('%02d:%02d:%02d', $horas, $minutos, $segundos);

    return $intervalo;
    }

    function horaMaior($hora1, $hora2) {
        $dataHora1 = DateTime::createFromFormat('H:i:s', $hora1);
        $dataHora2 = DateTime::createFromFormat('H:i:s', $hora2);

        if ($dataHora1 > $dataHora2) {
            return true;
        } else {
            return false;
        }
    }

    function diferencaHoras($hora1, $hora2) {

        $dataHora1 = DateTime::createFromFormat('H:i:s', $hora1);
        $dataHora2 = DateTime::createFromFormat('H:i:s', $hora2);

        $diferenca = $dataHora1->diff($dataHora2);

        return $diferenca->format('%H:%I:%S');
    }

}
