<?php

namespace App\Jobs;

use App\Models\HistoricosEtapas;
use App\Models\Maquinas;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class JobCorrecaoTempoApontamentosInicios implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        info("Dentro do job instance JobCorrecaoTempoApontamentos");


        $historicos_etapas = DB::table('historicos_etapas')
            ->select(
                'historicos_etapas.id',
                'historicos_etapas.pedidos_id',
                'historicos_etapas.numero_maquina',
                'historicos_etapas.etapas_alteracao_id',
                'historicos_etapas.status_id',
                'historicos_etapas.etapas_pedidos_id',
                'historicos_etapas.funcionarios_id',
                'historicos_etapas.select_tipo_manutencao',
                'historicos_etapas.select_motivo_pausas',
                'historicos_etapas.texto_quantidade',
                'historicos_etapas.created_at',
                'pedidos.os'
            )
            ->join('pedidos', 'historicos_etapas.pedidos_id', '=', 'pedidos.id')
            ->where('pedidos.os', '=', 18953)
            ->orderBy('historicos_etapas.pedidos_id', 'asc')
            ->orderBy('historicos_etapas.status_id', 'asc')
            ->orderBy('historicos_etapas.created_at', 'asc')
            ->get();


/*
SELECT
	pedidos.pedidos_id,
    historicos_etapas.status_id,
	historicos_etapas.etapas_pedidos_id,
    historicos_etapas.created_at
FROM
	historicos_etapas
inner join
	pedidos
ON
	pedidos.id = historicos_etapas.pedidos_id
WHERE
	pedidos.os=18953

    2817
*/


        foreach ($historicos_etapas as $historicos_etapa) {
            $pedidos_id = $historicos_etapa->pedidos_id;
            $status_id = $historicos_etapa->status_id;

                $array_apontamentos[$pedidos_id][$status_id][] = [
                    'pedidos_id' => $pedidos_id,
                    'numero_maquina' => $historicos_etapa->numero_maquina,
                    'etapas_alteracao_id' => $historicos_etapa->etapas_alteracao_id,
                    'status_id' => $status_id,
                    'etapas_pedidos_id' => $historicos_etapa->etapas_pedidos_id,
                    'funcionarios_id' => $historicos_etapa->funcionarios_id,
                    'select_tipo_manutencao' => $historicos_etapa->select_tipo_manutencao,
                    'select_motivo_pausas' => $historicos_etapa->select_motivo_pausas,
                    'texto_quantidade' => $historicos_etapa->texto_quantidade,
                    'created_at' => $historicos_etapa->created_at,
                    'os' => $historicos_etapa->os
                ];


        }

        $array_dias_semana = [ 0 => 'domingo',1 => 'segunda',2 => 'terca',3 => 'quarta',4 => 'quinta',5 => 'sexta',6 => 'sabado'];

        // info($array_apontamentos);
        $status_do_loop = null;
        foreach($array_apontamentos as $key1 => $arr_apontamentos) {
            foreach ($arr_apontamentos as $status => $apontamento) {

                info("status_id: " . $status);
                // info($apontamento[0]['pedidos_id']);


                foreach ($apontamento as $posicao => $etapas) {

                    if($posicao == 0 && $etapas['etapas_pedidos_id'] != 1) {

                        //pega o dia da semana
                        $dia_semana = date('w', strtotime($etapas['created_at']));
                        //pega hora
                        $hora_etapa = date('H:i', strtotime($etapas['created_at']));
                        $texto_dia_semana = $array_dias_semana[$dia_semana];

                        $Maquinas = new Maquinas();
                        $maquina = $Maquinas->get()->toArray();

                        foreach ($maquina[0] as $key => $hora_minuto) {

                            $hora_etapa = date('H:i', strtotime($hora_etapa));
                            if($key == $texto_dia_semana.'_inicio' && !empty($hora_minuto) &&  $this->horaMaiorQue($hora_minuto, $hora_etapa)) {
                                $nova_horario = $hora_minuto;
                                break;
                            } elseif ($key == $texto_dia_semana.'_almoco_inicio' && !empty($hora_minuto) && $this->horaMaiorQue($hora_minuto, $hora_etapa)) {
                                $nova_horario = $hora_minuto;
                                break;
                            } elseif ($key == $texto_dia_semana.'_almoco_fim' && !empty($hora_minuto) && $this->horaMaiorQue($hora_minuto, $hora_etapa)) {
                                $nova_horario = $hora_minuto;
                                break;
                            } elseif ($key == $texto_dia_semana.'_fim' && !empty($hora_minuto) && $this->horaMaiorQue($hora_minuto, $hora_etapa)) {
                                $nova_horario = $hora_minuto;
                                break;
                            }
                        }
                        $historicos_etapas = new HistoricosEtapas();
                        $historicos_etapas->pedidos_id = $etapas['pedidos_id'];
                        $historicos_etapas->etapas_pedidos_id = 1; //adiciona a etapa Iniciar que é 1
                        $historicos_etapas->status_id = $etapas['status_id'];
                        $historicos_etapas->numero_maquina = $etapas['numero_maquina'];
                        $historicos_etapas->etapas_alteracao_id = $etapas['etapas_alteracao_id'];
                        $historicos_etapas->funcionarios_id = 15; //adiciona 15(eplax) pois não tem funcionário
                        $historicos_etapas->select_tipo_manutencao = $etapas['select_tipo_manutencao'];
                        $historicos_etapas->select_motivo_pausas = $etapas['select_motivo_pausas'];
                        $historicos_etapas->texto_quantidade = $etapas['texto_quantidade'];
                        $historicos_etapas->created_at = $hora_etapa = date('Y-m-d', strtotime($etapas['created_at'])) . ' ' . $nova_horario; //pega a data do apontamento e hora corrigida
                        $historicos_etapas->save();

                    }

                }

            }



        }



        return '1';
    }

    public function horaMaiorQue($hora1, $hora2) {
        // Converte para timestamp usando strtotime
        $t1 = strtotime($hora1);
        $t2 = strtotime($hora2);

        if ($t1 === false || $t2 === false) {
            return null; // erro de formato
        }

        return $t1 < $t2;
    }

    /**
     * Salva histórico do pedido
     */
    public function historicosPedidos($pedido_id, $status_id) {
        $historicosPedidos = new HistoricosPedidos();
        $historicosPedidos->pedidos_id = $pedido_id;
        $historicosPedidos->status_id = $status_id;
        $historicosPedidos->save();
    }


}
