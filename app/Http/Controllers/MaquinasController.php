<?php

namespace App\Http\Controllers;

use App\Models\Maquinas;
use App\Models\ProducaoMaquinas;
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
    {

    }

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

    public function getHorasTurno(Request $request){
        try {
            $array = $request->input();

            if(!$this->checkAcesso($request)) {
                return response('Erro na identificação', 401);
            }

            $numero_cnc = $array['NUMERO_CNC'];
            $data_atual = Carbon::now()->format('Y-m-d');

            $hora_inicio = $data_atual." 14:00:00";

            $hora_fim =  $data_atual." 22:00:00";

            if($this->turno_mamnha()) {
                $hora_inicio = $data_atual." 08:00:00";
                $hora_fim =  $data_atual." 13:59:59";
            }

            $dados = [
                'textoHorasTrabalhadas' => "Horas Trabalhadas: 00:00:00",
                'textoHorasUsinadas' => "Horas Usinadas: 00:00:00",
                'horasTrabalhadasq'=>"00",
                'horasUsinadas'=>"0",

            ];

            $producaoMaquinas = new ProducaoMaquinas();
            $datahora_atual = new DateTime();
            $datahora_atual = $datahora_atual->format('Y-m-d H:i:s');
            $producaoMaquinas = $producaoMaquinas->where('numero_cnc', '=', $numero_cnc);
            $producaoMaquinas->whereBetween('created_at', [$hora_inicio, $datahora_atual]);
            $producaoMaquinas->orderby('created_at', 'asc');
            $producaoMaquinas = $producaoMaquinas->get();

            $total_horas_usinadas = $total_horas_trabalhadas = "00:00:00";
            $hora_atual = new DateTime();
            $hora_atual = $hora_atual->format('H:i:s');
            info('---------------------------------------------------------------------------');
            $hora = new DateTime();
            $hora = $hora->format('H');
            if($hora > 8 && count($producaoMaquinas)) {

                foreach ($producaoMaquinas as $key => $producaoMaquina) {
                    $total_horas_usinadas = PedidosController::somarHoras($total_horas_usinadas, $producaoMaquina['HorasServico']);
                }


                $total_horas_trabalhadas = $this->diff_hours_between_dates($hora_inicio, $hora_atual);

                $horasTrabalhadasq = $this->calcularPorcentagemTrabalhada($hora_inicio, $hora_fim, $hora_atual);
                $horasUsinadas = $this->calcularPorcentagemUsinada($total_horas_usinadas,$total_horas_trabalhadas);

                $dados = [
                    'textoHorasTrabalhadas' => "Horas Trabalhadas: ". substr($total_horas_trabalhadas, 0, 5),
                    'textoHorasUsinadas' => "Horas Usinadas: ". substr($total_horas_usinadas, 0, 5),
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

            $resultado['numero_cnc'] = $array['NUMERO_CNC'];
            $resultado['HorasServico'] = $array['stshUsageTimeHoursService'];
            $resultado['metrosPercorridos'] = $array['stsTraveledDistMetersService'];
            $resultado['qtdeServico'] = $array['stsNumJobsDoneService'];

            $producaoMaquinas = new ProducaoMaquinas();
            $producaoMaquinas->numero_cnc = $resultado['numero_cnc'];
            $producaoMaquinas->HorasServico = $this->converterParaHoras($resultado['HorasServico']);
            $producaoMaquinas->metrosPercorridos = $resultado['metrosPercorridos'];
            $producaoMaquinas->qtdeServico = $resultado['qtdeServico'];
            $producaoMaquinas->save();

            return response('sucesso', 200);
        } catch (\Throwable $th) {
            info($th);
            return response('erro', 200);
        }


    }
    function calcularPorcentagemUsinada($horasTrabalhadas, $periodoTotal) {
        // Convertendo as horas trabalhadas e o período total para segundos
        $horasTrabalhadasSegundos = strtotime($horasTrabalhadas) - strtotime('TODAY');
        $periodoTotalSegundos = strtotime($periodoTotal) - strtotime('TODAY');

        // Calculando a porcentagem
        $porcentagem = ($horasTrabalhadasSegundos / $periodoTotalSegundos) * 100;
        return (float)number_format($porcentagem, 0);
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

    private function converterParaHoras($valor_em_horas) {
        // Separar a parte inteira e decimal do valor em horas
        $partes = explode('.', $valor_em_horas);
        $horas = (int)$partes[0]; // parte inteira
        $minutos_decimais = isset($partes[1]) ? $partes[1] : 0; // parte decimal

        // Converter os minutos decimais em minutos reais (0.1 = 6 minutos, 0.2 = 12 minutos, ...)
        $minutos = round($minutos_decimais * 60);

        // Formatar a saída no formato HH:MM:SS
        return sprintf('%02d:%02d:00', $horas, $minutos);
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
        $maquinas->pessoas_inspecao = $request->input('pessoas_inspecao');
        $maquinas->horas_dia = $request->input('horas_dia');
        $maquinas->save();

        return $maquinas->id;
    }


    function horasParaDias($horas)
    {
        // Divide a string da hora em horas, minutos e segundos
        list($horas, $minutos, $segundos) = explode(':', $horas);

        // Converte horas, minutos e segundos para segundos
        $totalSegundos = $horas * 3600 + $minutos * 60 + $segundos;

        // Calcula o número de dias
        $dias = $totalSegundos / (24 * 3600);

        return $dias;
    }

    function horasParaDiasTrabalhados($horas)
    {
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

    function multiplicarHoras($horas, $multiplicador)
    {
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

}
