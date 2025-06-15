<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\ConfiguracoesController;

class AtualisarTempos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'configuracoes:atualisar-tempos';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $ConfiguracoesController = new ConfiguracoesController();
        $ConfiguracoesController->ProcessaAtualizacoesTemposConfiguracoes();

        $this->info('Tempos atualizados com sucesso!');
        return 0;
    }
}
