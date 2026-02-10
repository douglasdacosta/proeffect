<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GerarLeadsIaQualidade extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:gerarLeadsIaQualidade';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gera e atualiza a fila de leads da IA Qualidade';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $paramEntregaQualidade = DB::table('configuracoes_ia')->value('tempo_entrega_dias');
            $paramEntregaQualidade = $paramEntregaQualidade ?? 0;

            $query = DB::table('pedidos')
                ->distinct()
                ->select(
                    'pedidos.id',
                    'hp.created_at as data_entrega',
                    'pedidos.os',
                    'ficha_tecnica.ep',
                    'pedidos.qtde',
                    'pedidos.pessoas_id',
                    'pessoas.contato_pos_venda',
                    'pessoas.numero_whatsapp_pos_venda'
                )
                ->join('historicos_pedidos as hp', function ($join) {
                    $join->on('hp.pedidos_id', '=', 'pedidos.id');
                    $join->whereRaw('hp.id = (select max(h2.id) from historicos_pedidos h2 where h2.pedidos_id = pedidos.id)');
                })
                ->join('pessoas', 'pedidos.pessoas_id', '=', 'pessoas.id')
                ->join('ficha_tecnica', 'pedidos.fichatecnica_id', '=', 'ficha_tecnica.id')
                ->where('pedidos.status_id', 11)
                ->whereRaw(
                    "hp.created_at >= DATE_SUB(CURRENT_DATE, INTERVAL ? DAY)",
                    [$paramEntregaQualidade]
                );

            $pedidos = $query->get();

            if ($pedidos->isEmpty()) {
                $this->info('Nenhum pedido encontrado para geração de leads.');
                return 0;
            }

            $now = now();
            $rows = [];
            foreach ($pedidos as $pedido) {
                $rows[] = [
                    'pedido_id' => $pedido->id,
                    'data_entrega' => $pedido->data_entrega,
                    'os' => $pedido->os,
                    'ep' => $pedido->ep,
                    'qtde' => $pedido->qtde,
                    'pessoas_id' => $pedido->pessoas_id,
                    'contato_pos_venda' => $pedido->contato_pos_venda,
                    'numero_whatsapp_pos_venda' => $pedido->numero_whatsapp_pos_venda,
                    'responsavel_qualidade' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::table('ia_qualidade_leads')->upsert(
                $rows,
                ['pedido_id'],
                [
                    'data_entrega',
                    'os',
                    'ep',
                    'qtde',
                    'pessoas_id',
                    'contato_pos_venda',
                    'numero_whatsapp_pos_venda',
                    'updated_at'
                ]
            );

            $this->info('Leads da IA Qualidade gerados/atualizados com sucesso.');
            return 0;
        } catch (\Exception $th) {
            info($th);
            $this->error('Erro ao gerar leads da IA Qualidade: ' . $th->getMessage());
            $this->error('Trace: ' . $th->getTraceAsString());
            return 1;
        }
    }
}
