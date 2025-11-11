<?php

namespace App\Http\Controllers;

use App\Models\Fichastecnicasitens;
use Illuminate\Support\Facades\DB;

class PainelController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}


    public function getMotivosPausa(){
        return [
            '1' => 'F.P – Faltando Peças',
            '2' => 'P.P – Problema na produção',
            '3' => 'P – Pausado',
            '4' => 'P.R – Protótipo',
            '5' => 'A.P – Assunto Pessoal',
            '6' => 'P.M – Problema na máquina',
            '7' => 'E.P - Esperando próxima produção',
            '8' => 'F.M - Faltando Material',
            '9' => 'HH - Chapelona'
        ];
    }
    private function busca_dados_pedidos($status, $limit = 11, $concluidos ){
        $etapas_pedidos_id = '';

        if($concluidos){
            $etapas_pedidos_id = " AND C.etapas_pedidos_id = 4 ";


            $pedidos = DB::select(DB::raw("SELECT distinct
                                A.id,
                                H.nome,
                                B.ep,
                                A.os,
                                B.ep as ep_validar,
                                A.os as os_validar,
                                A.qtde,
                                A.data_antecipacao,
                                A.hora_antecipacao,
                                (SELECT COUNT(id)
                                FROM ficha_tecnica_itens X
                                WHERE X.fichatecnica_id = A.fichatecnica_id
                                AND X.blank != '') AS total_itens,
                                (
                                    select
                                        GROUP_CONCAT(X.blank ORDER BY X.blank ASC SEPARATOR ',') AS blanks
                                    from
                                        ficha_tecnica_itens X
                                    WHERE
                                        X.fichatecnica_id = A.fichatecnica_id
                                        AND X.blank != ''
                                    GROUP BY A.fichatecnica_id
                                ) as  blanks,
                                A.data_entrega,
                                DATEDIFF(A.data_entrega, NOW()) AS alerta,
                                C.numero_maquina,
                                D.nome as etapa,
                                case
                                    when C.select_motivo_pausas ='1' then 'F.P – Faltando Peças'
                                    when C.select_motivo_pausas ='2' then 'P.P – Problema na produção'
                                    when C.select_motivo_pausas ='3' then 'P – Pausado'
                                    when C.select_motivo_pausas ='4' then 'P.R – Protótipo'
                                    when C.select_motivo_pausas ='5' then 'A.P – Assunto Pessoal'
                                    when C.select_motivo_pausas ='6' then 'P.M – Problema na máquina'
                                    when C.select_motivo_pausas ='7' then 'E.P - Esperando próxima produção'
                                    when C.select_motivo_pausas ='8' then 'F.M - Faltando Material'
                                END as  motivo_pausa,
                                C.etapas_pedidos_id as etapa_codigo,
                                C.texto_quantidade as  qtde_pausa,
                                (
                                    select
                                        GROUP_CONCAT(G.nome ORDER BY G.nome ASC SEPARATOR ',') AS nome
                                    FROM
                                        pedidos_funcionarios_montagens F
                                    left join
                                        funcionarios G
                                    on
                                        G.id=F.funcionario_id
                                    WHERE
                                        F.pedido_id=A.id
                                )  AS responsavel,
                                E.nome as colaborador,
                                C.created_at,
                                C.select_tipo_manutencao as select_tipo_manutencao,
                                A.status_id,
                                I.nome as prioridade
                            FROM
                                pedidos A
                            INNER JOIN
                                ficha_tecnica B
                            ON
                                B.id = A.fichatecnica_id
                            LEFT JOIN
                                historicos_etapas C
                            ON
                                C.pedidos_id=A.id
                                And C.status_id=$status-1
                                AND C.etapas_pedidos_id = 4
                            left join
                                etapas_pedidos D
                            on
                                D.id=4
                            left join
                                funcionarios E
                            on
                                E.id=C.funcionarios_id
                            LEFT JOIN
                                status H
                            on
                                H.id=A.status_id
                            LEFT JOIN
                                prioridades I
                            ON
                                I.id=A.prioridade_id
                            WHERE
                                A.status_id = $status
                            ORDER BY
                                A.data_antecipacao,
                                C.created_at desc,
                                A.os, B.ep ASC
                            limit $limit"
                        ));
                    } else {

                        $pedidos = DB::select(DB::raw("SELECT
                                                            distinct
                                                            A.id,
                                                            H.nome,
                                                            B.ep,
                                                            A.os,
                                                            B.ep as ep_validar,
                                                            A.os as os_validar,
                                                            A.qtde,
                                                            A.data_antecipacao,
                                                            A.hora_antecipacao,
                                                            (SELECT COUNT(id)
                                                            FROM ficha_tecnica_itens X
                                                            WHERE X.fichatecnica_id = A.fichatecnica_id
                                                            AND X.blank != '') AS total_itens,
                                                            (
                                                                select
                                                                    GROUP_CONCAT(X.blank ORDER BY X.blank ASC SEPARATOR ',') AS blanks
                                                                from
                                                                    ficha_tecnica_itens X
                                                                WHERE
                                                                    X.fichatecnica_id = A.fichatecnica_id
                                                                    AND X.blank != ''
                                                                GROUP BY A.fichatecnica_id
                                                            ) as  blanks,
                                                            A.data_entrega,
                                                            DATEDIFF(A.data_entrega, NOW()) AS alerta,
                                                            C.numero_maquina,
                                                            D.nome as etapa,
                                                            case
                                                                when C.select_motivo_pausas ='1' then 'F.P – Faltando Peças'
                                                                when C.select_motivo_pausas ='2' then 'P.P – Problema na produção'
                                                                when C.select_motivo_pausas ='3' then 'P – Pausado'
                                                                when C.select_motivo_pausas ='4' then 'P.R – Protótipo'
                                                                when C.select_motivo_pausas ='5' then 'A.P – Assunto Pessoal'
                                                                when C.select_motivo_pausas ='6' then 'P.M – Problema na máquina'
                                                                when C.select_motivo_pausas ='7' then 'E.P - Esperando próxima produção'
                                                                when C.select_motivo_pausas ='8' then 'F.M - Faltando Material'
                                                            END as  motivo_pausa,
                                                            C.etapas_pedidos_id as etapa_codigo,
                                                            C.texto_quantidade as  qtde_pausa,
                                                            (
                                                                select
                                                                    GROUP_CONCAT(G.nome ORDER BY G.nome ASC SEPARATOR ',') AS nome
                                                                FROM
                                                                    pedidos_funcionarios_montagens F
                                                                left join
                                                                    funcionarios G
                                                                on
                                                                    G.id=F.funcionario_id
                                                                WHERE
                                                                    F.pedido_id=A.id
                                                            )  AS responsavel,
                                                            E.nome as colaborador,
                                                            C.select_tipo_manutencao as select_tipo_manutencao,
                                                            A.status_id,
                                                            C.created_at,
                                                            I.nome as prioridade
                                                        FROM
                                                            pedidos A
                                                        INNER JOIN
                                                            ficha_tecnica B
                                                        ON
                                                            B.id = A.fichatecnica_id
                                                        LEFT JOIN
                                                            historicos_etapas C
                                                        ON
                                                            C.pedidos_id=A.id
                                                            And C.status_id=A.status_id
                                                            and C.etapas_pedidos_id = (select
                                                                                            X.etapas_pedidos_id
                                                                                        from
                                                                                            historicos_etapas X
                                                                                        WHERE
                                                                                            X.pedidos_id=A.id
                                                                                            and X.status_id=A.status_id
                                                                                            and X.funcionarios_id=C.funcionarios_id
                                                                                        order by
                                                                                            X.created_at desc
                                                                                        limit 1
                                                                                        )
                                                            and C.id = (select
                                                                                            X.id
                                                                                        from
                                                                                            historicos_etapas X
                                                                                        WHERE
                                                                                            X.pedidos_id=A.id
                                                                                            and X.status_id=A.status_id
                                                                                            and X.funcionarios_id=C.funcionarios_id
                                                                                        order by
                                                                                            X.created_at desc
                                                                                        limit 1
                                                                                        )
                                                        left join
                                                            etapas_pedidos D
                                                        on
                                                            D.id=C.etapas_pedidos_id
                                                        left join
                                                            funcionarios E
                                                        on
                                                            E.id=C.funcionarios_id
                                                        LEFT JOIN
                                                            status H
                                                        on
                                                            H.id=A.status_id
                                                        LEFT JOIN
                                                            prioridades I
                                                        ON
                                                            I.id=A.prioridade_id
                                                        WHERE
                                                            A.status_id = $status
                                                            -- AND A.os =15493
                                                            -- and B.ep ='EP4177'
                                                         ORDER BY
                                                            A.data_antecipacao desc,
                                                            CASE WHEN D.nome IS NOT NULL THEN 0 ELSE 1 END ASC,
                                                            A.data_entrega,
                                                            A.os,
                                                            B.ep ASC
                                                        limit $limit"
                        ));
                    }


        foreach ($pedidos as $key => $pedido) {

            $blanck_exploded = explode(',', $pedido->blanks);
            $qdte_blank = 0;
            $conjuntos = array();

            $pedidosController = new PedidosController();
            $maquinas = $pedidosController->getMaquinas();
            $retorno = $pedidosController->calculaDiasSobrando($maquinas, strtolower($pedidos[$key]->nome), $pedido);

            $pedidos[$key]->dias_alerta_departamento = $retorno['dias_alerta_departamento'];
            $pedidos[$key]->diasSobrando = $retorno['diasSobrando'];

            if ($pedidos[$key]->alerta < 6) {
                $pedidos[$key]->class_dias_alerta = 'text-danger';
            } else {
                $pedidos[$key]->class_dias_alerta = 'text-primary';
            }

            foreach($blanck_exploded as $blancks) {
                $letra_blank = substr($blancks, 0, 1);
                if($letra_blank != '') {
                    $qdte_blank++ ;
                    $conjuntos['conjuntos'][$letra_blank] = $letra_blank;
                }
            };

            if($pedido->status_id == 6 ){

                switch (($pedidos[$key]->select_tipo_manutencao)) {
                    case 'T':
                        $pedidos[$key]->etapa = $pedido->etapa . " - Torre";
                        break;
                    case 'A':
                        $pedidos[$key]->etapa = $pedido->etapa . " - Agulha";
                        break;

                    default:
                        $pedidos[$key]->etapa = '';
                        break;
                }

            }

            $pedidos[$key]->blanks = $qdte_blank;
            $pedidos[$key]->conjuntos = !empty($conjuntos['conjuntos']) ? count($conjuntos['conjuntos']) : 0;

            if(isset($pedidos[$key-1]) && $pedidos[$key-1]->os_validar == $pedidos[$key]->os_validar && $pedidos[$key-1]->ep_validar == $pedidos[$key]->ep_validar) {
                $pedidos[$key]->os = '';
                $pedidos[$key]->ep = '';
                $pedidos[$key]->qtde = '';
                $pedidos[$key]->blanks = '';
                $pedidos[$key]->conjuntos = '';
                $pedidos[$key]->data_entrega = '';
                $pedidos[$key]->alerta = '';
            }
        }

        if($concluidos){
            $pedidos = array_slice($pedidos, 0,3);
        }

        return $pedidos;
    }

    public function higienizaDatas($pedidos) {
        foreach ($pedidos as $key => $pedido) {
            unset($pedidos[$key]->created_at);
            unset($pedidos[$key]->updated_at);
        }
        return $pedidos;
    }
    public function ordenarPedidosDesc($array) {
        usort($array, array($this, 'compararPedidosDesc'));
        return $array;
    }
    public function ordenarPedidos($array, ) {
        usort($array, array($this, 'compararPedidos'));
        return $array;
    }

    public function compararPedidosDesc($a, $b) {
        // Verifica se ambos têm colaboradores
        $colab_a = !empty($a->colaboradores);
        $colab_b = !empty($b->colaboradores);

        // Compara baseado na presença de colaboradores e na data de criação
        if ($colab_a && !$colab_b) {
            return -1; // $a vem antes de $b
        } elseif (!$colab_a && $colab_b) {
            return 1; // $b vem antes de $a
        } else {
            return strcmp($a->created_at, $b->created_at);
        }
    }

    public function compararPedidos($a, $b) {
        // Verifica se ambos têm colaboradores
        $colab_a = !empty($a->colaboradores);
        $colab_b = !empty($b->colaboradores);

        // Compara baseado na presença de colaboradores e na data de criação
        if ($colab_a && !$colab_b) {
            return -1; // $a vem antes de $b
        } elseif (!$colab_a && $colab_b) {
            return 1; // $b vem antes de $a
        } else {
            return strcmp($a->data_entrega, $b->data_entrega);
        }
    }

    public function buscaDadosEtapa($pedidos, $concluidos = false) {

        $Fichastecnicasitens = new Fichastecnicasitens();
        $dados_colaboradores = [];

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


            $historicos_etapas = DB::table('historicos_etapas')
            ->select('historicos_etapas.*', 'funcionarios.nome', 'etapas_pedidos.nome as nome_etapa')
            ->join('funcionarios', 'funcionarios.id', '=', 'historicos_etapas.funcionarios_id')
            ->join('etapas_pedidos', 'etapas_pedidos.id', '=', 'historicos_etapas.etapas_pedidos_id')
            ->where('pedidos_id','=',$pedido->id)
            ->orderBy('historicos_etapas.created_at', 'desc')
            ->get(1);

            $dados_colaboradores = [];
            if(!empty($historicos_etapas[0])) {

                $historicos_etapas_status = DB::table('historicos_etapas')->distinct()
                    ->select(
                    'historicos_etapas.pedidos_id',
                    'historicos_etapas.status_id',
                    'historicos_etapas.etapas_pedidos_id',
                    'historicos_etapas.funcionarios_id',
                    'historicos_etapas.select_tipo_manutencao',
                    'historicos_etapas.select_motivo_pausas',
                    'historicos_etapas.texto_quantidade',
                    'historicos_etapas.created_at',
                    'historicos_etapas.etapas_alteracao_id',
                    'historicos_etapas.numero_maquina',
                    'funcionarios.nome',
                    'etapas_pedidos.nome as nome_etapa')
                    ->join('funcionarios', 'funcionarios.id', '=', 'historicos_etapas.funcionarios_id')
                    ->leftjoin('etapas_alteracao', 'etapas_alteracao.id', '=', 'historicos_etapas.etapas_alteracao_id')
                    ->join('etapas_pedidos', 'etapas_pedidos.id', '=', 'historicos_etapas.etapas_pedidos_id')
                    ->where('historicos_etapas.pedidos_id','=',$pedido->id);


                if($concluidos == true) {
                    $historicos_etapas_status = $historicos_etapas_status
                    ->where('historicos_etapas.status_id','=',$pedido->status_id - 1)
                    ->where('historicos_etapas.etapas_pedidos_id','=',4);
                } else {
                    $historicos_etapas_status = $historicos_etapas_status
                    ->where('historicos_etapas.status_id','=',$pedido->status_id);
                }


                $historicos_etapas_status = $historicos_etapas_status
                    ->orderBy('historicos_etapas.created_at', 'asc')
                    ->get()->toArray();


                $dados_colaboradores = [];

                $motivosPausa = $this->getMotivosPausa();
                foreach ($historicos_etapas_status as $hestatus) {

                    $etapa = $hestatus->nome_etapa;

                    if($hestatus->status_id == 6 ){
                        $etapa = ($hestatus->select_tipo_manutencao =='T' ) ? $etapa . " - Torre" : $etapa . " - Agulha" ;
                    }
                    $dados_colaboradores[$hestatus->pedidos_id][$hestatus->etapas_alteracao_id]= [
                        'pedidos_id' => $hestatus->pedidos_id   ,
                        'status_id' => $hestatus->status_id     ,
                        'etapas_alteracao_id' => $hestatus->etapas_alteracao_id     ,
                        'etapas_pedidos_id' => $hestatus->etapas_pedidos_id     ,
                        'funcionarios_id' => $hestatus->funcionarios_id     ,
                        'select_tipo_manutencao' => $hestatus->select_tipo_manutencao   ,
                        'select_motivo_pausas' => ($hestatus->etapas_pedidos_id == 2 && !empty($hestatus->select_motivo_pausas)) ? $motivosPausa[$hestatus->select_motivo_pausas] : '' ,
                        'texto_quantidade' => $hestatus->texto_quantidade   ,
                        'numero_maquina' => $hestatus->numero_maquina   ,
                        'created_at' => $hestatus->created_at   ,
                        'nome' => $hestatus->nome   ,
                        'nome_etapa' => $etapa  ,
                    ];

                }
            }





            $pedidos[$key]->colaboradores =$dados_colaboradores;


            $funcionarios_montagens = DB::table('pedidos_funcionarios_montagens')
                ->join('funcionarios', 'funcionarios.id', '=', 'pedidos_funcionarios_montagens.funcionario_id')
                ->select('funcionarios.nome', 'funcionarios.id')
                ->where('pedidos_funcionarios_montagens.pedido_id', '=', $pedido->id)
                ->orderby('funcionarios.nome')->get();
                $nome_funcionarios = [];

            foreach($funcionarios_montagens as $funcionario_montagem){
                $nome_funcionarios[] = $funcionario_montagem->nome;
            }

            $pedidos[$key]->funcionario = implode(',',$nome_funcionarios);
        }
        return $pedidos;
    }


    private function carrega_dados($status_pendente, $status_concluido){


        $pedidosCompletos = $this->busca_dados_pedidos($status_concluido, 3, true);
        $pedidosPendentes = $this->busca_dados_pedidos($status_pendente, 50, false);

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
