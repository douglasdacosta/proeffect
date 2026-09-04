<?php

namespace App\Console\Commands;

use App\Http\Controllers\Auth\ApiERPController;
use App\Models\Pedidos;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AtualizarNumeroNotaPedidosFinalizados extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:atualizarNumeroNotaPedidosFinalizados';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Atualiza o numero_nota dos pedidos finalizados (status 7973) alterados nos ultimos 10 dias';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            if (env('habilita_busca') != 1) {
                $this->info('Busca desabilitada por ambiente (habilita_busca != 1).');
                return 0;
            }

            $apiERPController = new ApiERPController();
            $apiERPController->getToken();
            $dataAlteracao = Carbon::now()->subDays(360)->format('Y-m-d');
            $vendas = $apiERPController->getVendasByStatus(7973, $dataAlteracao);

            if (empty($vendas['os']) || !is_array($vendas['os'])) {
                $this->info('Nenhuma venda finalizada encontrada no retorno da API.');
                return 0;
            }

            $totalVendasAnalisadas = 0;
            $totalOSAtualizadas = 0;
            $totalPedidosAtualizados = 0;

            foreach ($vendas['os'] as $venda) {
                $totalVendasAnalisadas++;

                $numeroOs = $venda['numeroOS'] ?? null;
                $numeroNota = $venda['dadosAdicionais'] ?? null;
                if (empty($numeroOs) || empty($numeroNota)) {
                    continue;
                }

                $pedidosDaOs = Pedidos::where('os', '=', $numeroOs)->get();

                if ($pedidosDaOs->isEmpty()) {
                    continue;
                }

                $osFoiAtualizada = false;

                foreach ($pedidosDaOs as $pedido) {
                    if ($pedido->numero_nota !== $numeroNota) {
                        $pedido->numero_nota = $numeroNota;
                        $pedido->save();
                        $totalPedidosAtualizados++;
                        $osFoiAtualizada = true;
                    }
                }

                if ($osFoiAtualizada) {
                    $totalOSAtualizadas++;
                }
            }

            $this->info('Vendas analisadas: '.$totalVendasAnalisadas);
            $this->info('OS atualizadas: '.$totalOSAtualizadas);
            $this->info('Pedidos atualizados: '.$totalPedidosAtualizados);

            return 0;
        } catch (\Throwable $th) {
            info($th->getMessage());
            $this->error('Erro ao atualizar numero_nota: '.$th->getMessage());
            return 1;
        }
    }
}