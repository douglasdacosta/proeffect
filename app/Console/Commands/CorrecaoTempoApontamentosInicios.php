<?php

namespace App\Console\Commands;

use App\Jobs\JobCorrecaoTempoApontamentosInicios;
use Illuminate\Console\Command;

ini_set('max_execution_time', 600); // Define o tempo limite de execução para 300 segundos (5 minutos)

class CorrecaoTempoApontamentosInicios extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:CorrecaoTempoApontamentosInicios';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Corrige os tempos de apontamentos dos departamentos de produção';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        try {
            JobCorrecaoTempoApontamentosInicios::dispatch();

            return true;
        } catch (\Exception $th) {
            info($th);
        }

    }
}
