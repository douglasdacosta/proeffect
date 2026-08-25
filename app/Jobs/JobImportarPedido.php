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

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        info("Dentro do job instance");
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

                // info(json_encode($venda));
                
                foreach($venda['itens'] as $itens) {
                    

                    $pedidos = new Pedidos();
                    $pessoas = new Pessoas();
                    $fichatecnica = new Fichastecnicas();
                    $transportes = new transportes();
                    $ep = $itens['codigo'];
                    $quantidade = $itens['quantidade'];
                    $valor_unitario = $itens['unitario']+$itens['ipi']-$itens['desconto'];
                    info("EP: ". $itens['codigo']. '; OS:'.$venda['numeroOS']. '; IdOs:'.$idOs . ' valorunitario: '.$valor_unitario . ' descricao: '.$itens['descricao']);
                    $numeroOs = $venda['numeroOS'];
                    $dataOS = $venda['dataOS'];
                    $prevEntrega = $venda['prevEntrega'];
                    $cliente_id = $venda['numCli'];
                    $cliente = $venda['idCliente'];
                    $idVendedor = $venda['idVendedor'];
                    $numero_nota = $venda['dadosAdicionais'] ?? null;
                    $cliente= $ApiERPController->getClienteById($cliente);
                    $Vendedor= $ApiERPController->getVendedorById($idVendedor);
                    $transportadora= $ApiERPController->getTransportadoraById($venda['idTransportadora']);
                    $pessoa = $pessoas->where('codigo_cliente', '=', $cliente_id)->first();
                    $fichatecnica = $fichatecnica->where('ep', '=', $ep)->where('status', '=', 'A')->get();

                    if(empty($fichatecnica[0]->id)){
                        info("EP ".$ep." não existente no CRM");
                        continue;
                    }

                    $fichatecnicaId = $fichatecnica[0]->id;

                    $transportes = $transportes->where('nome','=', $transportadora['nome'])->first();

                    $pessoa_id = $this->savePessoas($pessoa, $cliente, $Vendedor);

                    $transporte_id = $this->saveTrasportes($transportes, $transportadora);

                    $pedido = $pedidos->where('os', '=', $numeroOs)->where('fichatecnica_id', '=', $fichatecnicaId)->first();

                    $dados = [
                        'numeroOs' => $numeroOs,
                        'fichatecnicaId' => $fichatecnicaId,
                        'pessoa_id' => $pessoa_id,
                        'transporte_id' => $transporte_id,
                        'dataOS' => $dataOS,
                        'prevEntrega' => $prevEntrega,
                        'quantidade' => $itens['quantidade'],
                        'descricao' => $itens['descricao'],
                        'valor_unitario_adv' => $valor_unitario,
                        'numero_nota' => $numero_nota
                    ];

                    $this->savePedidos($pedido, $dados);

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
            $descricao = substr($dados['descricao'], 0, 255);            
            $pedidos->descricao = $descricao;
            $pedidos->transporte_id = $dados['transporte_id'];
            $pedidos->qtde =$dados['quantidade'];
            $pedidos->data_gerado = substr($dados['dataOS'], 0, 10);
            $pedidos->data_entrega = substr($dados['prevEntrega'], 0, 10);
            $pedidos->valor_unitario_adv = $dados['valor_unitario_adv'];
            if (!empty($dados['numero_nota'])) {
                $pedidos->numero_nota = $dados['numero_nota'];
            }
            $pedidos->status ='A';
            $pedidos->save();

            $this->historicosPedidos($pedidos->id, 1);
            $this->salvaAlerta($pedidos->id);
            return $pedidos->id;
        } else {
            if(empty($pedido->descricao) || (empty($pedido->numero_nota) && !empty($dados['numero_nota']))) {
                //alterar o pedido pelo id    
                $pedidos = Pedidos::find($pedido->id);
                $pedidos->descricao = substr($dados['descricao'], 0, 255);
                if (!empty($dados['numero_nota'])) {
                    $pedidos->numero_nota = $dados['numero_nota'];
                }
                $pedidos->save();
                info("Descricao e numero nota atualizada para o pedido: ".$pedidos->id);
            }
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
