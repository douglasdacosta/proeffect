<?php

namespace App\Console\Commands;

use App\Jobs\JobImportarPedidoProjetos;
use Illuminate\Console\Command;
ini_set('max_execution_time', 600); // Define o tempo limite de execução para 300 segundos (5 minutos)
class importarPedidoProjetos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:importarPedidoProjetos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa os pedidos/vendas do ERP';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        try {
            if(env('habilita_busca_projetos') == 1){
                info('Chamando o JobImportarPedidoProjetos');
                 JobImportarPedidoProjetos::dispatch();
            }

            return true;
        } catch (\Exception $th) {
            info($th);
        }

    }
}
