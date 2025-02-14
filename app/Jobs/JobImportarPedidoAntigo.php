<?php

namespace App\Jobs;

use App\Http\Controllers\Auth\ApiERPController;
use App\Models\Alertas;
use App\Models\Fichastecnicas;
use App\Models\HistoricosPedidos;
use App\Models\Pedidos;
use App\Models\Pessoas;
use App\Models\Transportes;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class JobImportarPedidoAntigo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        return true;;
        info("Dentro do job instance JobImportarPedidoAntigo");
        $dados_pedidos = [];
        $id = DB::transaction(function () {

            $pedidos = new Pedidos();
            $pessoas = new Pessoas();
            $fichatecnica = new Fichastecnicas();
            $transportes = new transportes();


            $ApiERPController = new ApiERPController();
            $ApiERPController->getToken();
            $vendas = $ApiERPController->getVendasByStatus();

            foreach ($vendas['os'] as $key => $venda) {

                $idOs = $venda['idOs'];

                if(!$venda || empty($venda['prevEntrega'])) {
                    info(" OS não encontrada ".$venda['numeroOS'] . " ou sem data de prevEntrega");
                    continue;
                }

                foreach($venda['itens'] as $itens) {
                    // info("EP: ". $itens['codigo']. '; OS:'.$venda['numeroOS']. '; IdOs:'.$idOs);


                    $pedidos = new Pedidos();
                    $pessoas = new Pessoas();
                    $fichatecnica = new Fichastecnicas();
                    $transportes = new transportes();
                    $ep = $itens['codigo'];
                    $valor_unitario = $itens['unitario'];

                    $numeroOs = $venda['numeroOS'];
                    $dataOS = $venda['dataOS'];
                    $prevEntrega = $venda['prevEntrega'];
                    $cliente_id = $venda['numCli'];
                    $cliente = $venda['idCliente'];
                    $idVendedor = $venda['idVendedor'];

                    $fichatecnica = $fichatecnica->where('ep', '=', $ep)->where('status', '=', 'A')->get();

                    if(empty($fichatecnica[0]->id)){
                        info("EP ".$ep." não existente no CRM");
                        continue;
                    }
                    info("update pedidos set valor_unitario_adv = '".$itens['unitario']."' where os = '".$venda['numeroOS']."' and fichatecnica_id = '".$fichatecnica[0]->id."'");



                }
            }
        });

        return $id;
    }

    /**
     * Salva dados do pedido
     */
    private function savePedidos($pedido, $dados){

        if(empty($pedido->id)) {
            $pedidos = new Pedidos();
            $pedidos->os = $dados['numeroOs'];
            $pedidos->fichatecnica_id = $dados['fichatecnicaId'];
            $pedidos->pessoas_id = $dados['pessoa_id'];
            $pedidos->status_id = 1;
            $pedidos->transporte_id = $dados['transporte_id'];
            $pedidos->qtde =$dados['quantidade'];
            $pedidos->data_gerado = substr($dados['dataOS'], 0, 10);
            $pedidos->data_entrega = substr($dados['prevEntrega'], 0, 10);
            $pedidos->valor_unitario_adv = $dados['valor_unitario_adv'];
            $pedidos->status ='A';
            $pedidos->save();

            $this->historicosPedidos($pedidos->id, 1);
            $this->salvaAlerta($pedidos->id);
            return $pedidos->id;
        }

        return $pedido->id;
    }

    /**
     * Salva dados de transporte do pedido
     */
    private function saveTrasportes($transportes, $transportadora){
        if(empty($transportes->id)) {
            $transportes = new transportes();
            $transportes->id = $transportadora['id'];
            $transportes->nome = $transportadora['nome'];
            $transportes->status ='A';
            $transportes->save();
        }
        return $transportes->id;

    }

    /**
     * Salva Alerta do pedido
     */
    public function salvaAlerta($pedido_id) {
        $file = new Alertas();
        $file->pedidos_id = $pedido_id;
        $file->enviado = false;
        $file->save();
    }

    /**
     * Salva histórico do pedido
     */
    public function historicosPedidos($pedido_id, $status_id) {
        $historicosPedidos = new HistoricosPedidos();
        $historicosPedidos->pedidos_id = $pedido_id;
        $historicosPedidos->status_id = $status_id;
        $historicosPedidos->save();
    }

    /**
     * salva dados de clientes
     */
    private function savePessoas($pessoa, $cliente, $Vendedor){
        $pessoas = new Pessoas();
        if(!empty($pessoa->id)) {
            $pessoas = $pessoas::find($pessoa->id);
        }
        $pessoas->codigo_cliente = $cliente['numcli'];
        $pessoas->nome_cliente = $cliente['fantasia'];
        $pessoas->nome_contato = $cliente['contato1'];
        $pessoas->nome_assistente = $Vendedor['nome'];
        $pessoas->telefone = preg_replace('/\D/', '', $cliente['telefone']);
        $pessoas->email = $cliente['email'];
        $pessoas->status = 'A';
        $pessoas->save();

        return $pessoas->id;
    }
}
