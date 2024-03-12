<?php

namespace App\Console\Commands;

use App\Jobs\JobImportarPedido;
use Illuminate\Console\Command;

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
        JobImportarPedido::dispatch();

        return true;
    }
}
