<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\ConfiguracoesController;

class AtualisarMateriais extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'configuracoes:atualisar-materiais';

   
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $ConfiguracoesController = new ConfiguracoesController();
        $ConfiguracoesController->ProcessaAtualizacoesConfiguracoes();
        
        $this->info('Materiais atualizados com sucesso!');
        return 0;
    }
}
