<?php

namespace App\Http\Controllers;

use App\Models\HistoricosPedidos;
use App\Models\Pedidos;
use App\Models\Pessoas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConsultaStatusController extends Controller
{
    const STATUS_PEDIDOS = [
        1 => 'Imprimir',
        2 => 'Em Preparação',
        3 => 'Aguardando Material',
        4 => 'Usinagem',
        5 => 'Acabamento',
        6 => 'Montagem',
        7 => 'Inspeção',
        8 => 'Embalar',
        9 => 'Expedição',
        10 => 'Estoque',
        11 => 'Entregue',
        12 => 'Alteração de Projeto',
        13 => 'Pedido Cancelado'
    ];

    public function consultarStatus($hash, $token)
    {
        // Validar o token de integração
        $tokenValido = env('TOKEN_INTEGRACAO_CONSULTA');
        
        
        if (!$token || $token !== $tokenValido) {
            return response()->json([
                'success' => false,
                'message' => 'Token de integração inválido'
            ], 401);
        }

        try {
            $dias_entregue = env('DIAS_ENTREGUE', 10);
            $pedidos  = DB::select(DB::raw(
                "SELECT distinct
                    pedidos.id as id,
                    pedidos.os as os,
                    pedidos.status_id as status_id ,
                    ficha_tecnica.ep as ep,
                    pedidos.qtde as quantidade
                from pedidos
                inner join pessoas on pessoas.id = pedidos.pessoas_id
                inner join ficha_tecnica on ficha_tecnica.id = pedidos.fichatecnica_id
                inner join historicos_pedidos on historicos_pedidos.pedidos_id = pedidos.id
                WHERE
                    pessoas.hash_consulta = '$hash'
                    AND 
                        (
                            (pedidos.status_id = 11 and (DATEDIFF(CURRENT_DATE, historicos_pedidos.created_at) <= $dias_entregue))
                            or (pedidos.status_id < 11)
                        )"
            ));            
            if (!$pedidos) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'pedidos' => []
                    ]
                ]);
            }

            $pedidosFormatados = collect($pedidos)->map(function($pedido) {
                $status = [];
                foreach (self::STATUS_PEDIDOS as $id => $descricao) {                  
                    $status[$id] = [
                        'descricao' => $descricao,
                        'atual' => ($pedido->status_id == $id)
                    ];
                }
    
                return [
                    'codigo' => $pedido->os,
                    'ep' => $pedido->ep,
                    'quantidade' => $pedido->quantidade,
                    'status' => $status
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'pedidos' => $pedidosFormatados
                ]
            ]);

        } catch (\Exception $e) {
            info($e);
            return response()->json([
                'success' => false,
                'message' => 'Hash inválido ou não encontrado'
            ], 404);
        }
    }
}
