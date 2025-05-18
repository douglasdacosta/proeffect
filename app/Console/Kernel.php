<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

    protected $commands = [
        Commands\ImportarPedido::class,
        Commands\ImportarPedidoAntigo::class,
        Commands\GerarHashPessoas::class,
        Commands\AtualisarMateriais::class,
    ];
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        
        try {
            $schedule->command('command:importarPedido')->everyFifteenMinutes()->withoutOverlapping()->between('8:00', '20:00');
            $schedule->command('command:ImportarPedidoAntigo')->everyFifteenMinutes()->withoutOverlapping()->between('8:00', '22:00');
            $schedule->command('pessoas:gerar-hash')->everyFiveMinutes()->withoutOverlapping()->between('8:00', '22:00');
            $schedule->command('configuracoes:atualisar-materiais')->cron('0 0 1 * *')->withoutOverlapping(); // minuto hora dia-do-mês mês dia-da-semana
        } catch (\Exception $th) {
            info($th);
        }


    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
