<?php

namespace App\Http\Controllers;

use App\Models\Pessoas;
use Illuminate\Http\Request;

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
        info($token);
        info($tokenValido);
        if (!$token || $token !== $tokenValido) {
            return response()->json([
                'success' => false,
                'message' => 'Token de integração inválido'
            ], 401);
        }

        try {
            $pessoa = Pessoas::where('hash_consulta', $hash)->firstOrFail();
            $pedidos = $pessoa->pedidos()
                ->with('tabelaFichastecnicas')
                ->get();

            if ($pedidos->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'pedidos' => []
                    ]
                ]);
            }

            $pedidosFormatados = $pedidos->map(function($pedido) {
                $status = [];
                foreach (self::STATUS_PEDIDOS as $id => $descricao) {
                    $status[$id] = [
                        'descricao' => $descricao,
                        'atual' => ($pedido->status_id == $id)
                    ];
                }

                return [
                    'codigo' => $pedido->id,
                    'ep' => $pedido->tabelaFichastecnicas ? $pedido->tabelaFichastecnicas->ep : null,
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
            return response()->json([
                'success' => false,
                'message' => 'Hash inválido ou não encontrado'
            ], 404);
        }
    }
}
