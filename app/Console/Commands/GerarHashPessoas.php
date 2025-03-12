<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pessoas;
use Illuminate\Support\Str;

class GerarHashPessoas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pessoas:gerar-hash';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gera hash de consulta para todas as pessoas';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $pessoas = Pessoas::whereNull('hash_consulta')->get();
        $count = 0;

        foreach ($pessoas as $pessoa) {
            $hash = md5($pessoa->id . $pessoa->documento);
            $pessoa->update(['hash_consulta' => $hash]);
            $count++;
        }

        $this->info("Hashes gerados com sucesso! Total: {$count} pessoas atualizadas.");
    }
}
