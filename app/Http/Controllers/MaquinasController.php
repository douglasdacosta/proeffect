<?php

namespace App\Http\Controllers;

use App\Models\Maquinas;
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
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
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
}
