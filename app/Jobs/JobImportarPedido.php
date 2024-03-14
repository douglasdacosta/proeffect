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

class JobImportarPedido implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        set_time_limit(360);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
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

                $venda_completa= $ApiERPController->getVendasByOS($idOs);

                if(!$venda_completa || empty($venda_completa['os']['prevEntrega'])) {

                    continue;
                }

                foreach($venda_completa['os']['itens'] as $itens) {

                    $pedidos = new Pedidos();
                    $pessoas = new Pessoas();
                    $fichatecnica = new Fichastecnicas();
                    $transportes = new transportes();

                    if($venda_completa) {
                        $numeroOs = $venda_completa['os']['numeroOS'];
                        $dataOS = $venda_completa['os']['dataOS'];
                        $prevEntrega = $venda_completa['os']['prevEntrega'];
                        $ep = $itens['codigo'];
                        $cliente_id = $venda_completa['cliente']['id'];
                        $cliente = $venda_completa['cliente'];
                        $transportadora = $venda_completa['transportadora'];
                        $idVendedor = $venda_completa['os']['idVendedor'];

                        $Vendedor= $ApiERPController->getVendedorById($idVendedor);
                        $pessoa = $pessoas->where('codigo_cliente', '=', $cliente_id)->first();

                        $fichatecnica = $fichatecnica->where('ep', '=', $ep)->get();
                        if(empty($fichatecnica[0]->id)){
                            info("EP ".$ep." não existente no CRM");
                            continue;
                        }
                        $fichatecnicaId = $fichatecnica[0]->id;
                        $transportes = $transportes->where('nome','=', $transportadora['nome'])->first();

                        $pessoa_id = $this->savePessoas($pessoa, $cliente, $Vendedor);

                        $transporte_id = $this->saveTrasportes($transportes, $transportadora);

                        $pedido = $pedidos->where('os', '=', $numeroOs)->first();
                        $dados = [
                            'numeroOs' => $numeroOs,
                            'fichatecnicaId' => $fichatecnicaId,
                            'pessoa_id' => $pessoa_id,
                            'transporte_id' => $transporte_id,
                            'dataOS' => $dataOS,
                            'prevEntrega' => $prevEntrega,
                            'quantidade' => $itens['quantidade']
                        ];
                        $this->savePedidos($pedido, $dados);
                    }
                }
            }
        });

        return $id;
    }

    /**
     * Salva dados do pedido
     */
    private function savePedidos($pedido, $dados){
        info($dados);
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
        $pessoas->codigo_cliente =  $cliente['id'];
        $pessoas->nome_cliente = $cliente['razao'];
        $pessoas->nome_contato = $cliente['fantasia'];
        $pessoas->nome_assistente = $Vendedor['nome'];
        $pessoas->telefone = preg_replace('/\D/', '', $cliente['telefone']);
        $pessoas->email = $cliente['email'];
        $pessoas->status = 'A';
        $pessoas->save();

        return $pessoas->id;
    }
}
