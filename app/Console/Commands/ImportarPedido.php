<?php

namespace App\Console\Commands;

use App\Jobs\JobImportarPedido;
use Illuminate\Console\Command;
ini_set('max_execution_time', 5); // Define o tempo limite de execução para 300 segundos (5 minutos)
class ImportarPedido extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:importarPedido';

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
        sleep(300);
        // JobImportarPedido::dispatch();

        return true;
    }
}
