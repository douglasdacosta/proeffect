<?php

namespace App\Jobs;

use App\Http\Controllers\Auth\ApiERPController;
use App\Models\Alertas;
use App\Models\Projetos;
use App\Models\HistoricosPedidos;
use App\Models\Pessoas;
use App\Models\Transportes;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class JobImportarPedidoProjetos implements ShouldQueue
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

            $projetos = new Projetos();
            $pessoas = new Pessoas();
            $transportes = new Transportes();


            $ApiERPController = new ApiERPController();
            $ApiERPController->getToken();
            $vendas = $ApiERPController->getVendasByStatus(7492);
            // info('retorno da api de vendas');
            // info($vendas);
            // dd('fim');
            foreach ($vendas['os'] as $key => $venda) {

                $idOs = $venda['idOs'];
                $idTipoSaida = $venda['idTipoSaida'];

                 //verifica se a OS já existe

                if(!$venda) {
                    info(" OS não encontrada ".$venda['numeroOS'] . " ou sem data de prevEntrega");
                    continue;
                }

                foreach($venda['itens'] as $itens) {
                    info("EP: ". $itens['codigo']. '; OS:'.$venda['numeroOS']. '; IdOs:'.$idOs);

                    $projetos = new Projetos();
                    $pessoas = new Pessoas();
                    $transportes = new Transportes();
                    $ep = $itens['codigo'];

                    $valor_unitario = $itens['unitario']+$itens['ipi']-$itens['desconto'];

                    $numeroOs = $venda['numeroOS'];
                    $dataOS = $venda['dataOS'];
                    $prevEntrega = $venda['prevEntrega'];
                    $cliente_id = $venda['numCli'];
                    $cliente = $venda['idCliente'];
                    $idVendedor = $venda['idVendedor'];

                    if($idTipoSaida == 27) {
                        $novo_alteracao = 1; // 1 = Alteração
                    } else {
                        $novo_alteracao = 0; // 0 = Novo
                    }

                    $cliente= $ApiERPController->getClienteById($cliente);
                    $Vendedor= $ApiERPController->getVendedorById($idVendedor);
                    $transportadora= $ApiERPController->getTransportadoraById($venda['idTransportadora']);
                    $pessoa = $pessoas->where('codigo_cliente', '=', $cliente_id)->first();

                    $transportes = $transportes->where('nome','=', $transportadora['nome'])->first();

                    $pessoa_id = $this->savePessoas($pessoa, $cliente, $Vendedor);

                    $transporte_id = $this->saveTrasportes($transportes, $transportadora);

                    $projetos = $projetos->where('os', '=', $numeroOs)->where('ep', '=', $ep)->first();

                    $dados = [
                        'numeroOs' => $numeroOs,
                        'ep' => $ep,
                        'pessoa_id' => $pessoa_id,
                        'transporte_id' => $transporte_id,
                        'dataOS' => $dataOS,
                        'prevEntrega' => $prevEntrega,
                        'quantidade' => $itens['quantidade'],
                        'valor_unitario_adv' => $valor_unitario,
                        'novo_alteracao' => $novo_alteracao
                    ];

                    $this->saveProjetos($projetos, $dados);

                }
            }
        });

        return $id;
    }

    /**
     * Salva dados do projeto
     */
    private function saveProjetos($projetos, $dados){

        if(empty($projetos->id)) {
            $projetos = new Projetos();
            $projetos->os = $dados['numeroOs'];
            $projetos->ep = $dados['ep'];
            $projetos->pessoas_id = $dados['pessoa_id'];
            $projetos->status_projetos_id = 7;
            $projetos->sub_status_projetos_codigo = 50;
            $projetos->transporte_id = $dados['transporte_id'];
            $projetos->qtde = $dados['quantidade'];
            $projetos->data_gerado = substr($dados['dataOS'], 0, 10);
            $projetos->data_entrega = null;
            $projetos->valor_unitario_adv = $dados['valor_unitario_adv'];
            $projetos->novo_alteracao = $dados['novo_alteracao'];
            $projetos->status = 'A';
            $projetos->save();

            return $projetos->id;
        }

        return $projetos->id;
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
