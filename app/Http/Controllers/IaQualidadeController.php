<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Providers\DateHelpers;

use function Psy\info;

class IaQualidadeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the IA Qualidade dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {

        $dataEntregaDe = !empty($request->input('data_entrega_de')) ? $request->input('data_entrega_de') : null;
        $dataEntregaAte = !empty($request->input('data_entrega_ate')) ? $request->input('data_entrega_ate') : null;
        $os = !empty($request->input('os')) ? $request->input('os') : null;
        $ep = !empty($request->input('ep')) ? $request->input('ep') : null;
        $quantidade = !empty($request->input('quantidade')) ? $request->input('quantidade') : null;
        $responsavelQualidade = !empty($request->input('responsavel_qualidade')) ? $request->input('responsavel_qualidade') : null;
        $statusLead = !empty($request->input('status_lead')) ? $request->input('status_lead') : 'pendente';

        // Query base para buscar dados
        $query = DB::table('pedidos')
            ->distinct()
            ->select(
                'pedidos.id',
                'hp.created_at as data_entrega',
                'pedidos.os',
                'ficha_tecnica.ep',
                'pedidos.qtde',
                'pessoas.contato_pos_venda',
                'pessoas.numero_whatsapp_pos_venda'
            )
            ->join('historicos_pedidos as hp', function ($join) {
                $join->on('hp.pedidos_id', '=', 'pedidos.id');
                // pega somente o último histórico via subquery por pedido
                $join->whereRaw('hp.id = (select max(h2.id) from historicos_pedidos h2 where h2.pedidos_id = pedidos.id)');
            })
            ->join('pessoas', 'pedidos.pessoas_id', '=', 'pessoas.id')
            ->join('ficha_tecnica', 'pedidos.fichatecnica_id', '=', 'ficha_tecnica.id');


        $query->where('pedidos.status_id', 11); // Filtrar por entregues

        // Bustar da tabela de configuracoes_ia
        $param_entrega_qualidade = DB::table('configuracoes_ia')->value('tempo_entrega_dias');

        //diferença entre a data entregue e a data do período param_entrega_qualidade
        $query->whereRaw(
            "DATEDIFF(CURRENT_DATE, hp.created_at) >= ?",
            [$param_entrega_qualidade]
        );


        if(!empty($dataEntregaDe) && !empty($dataEntregaAte )) {
            $query = $query->whereBetween('hp.created_at', [DateHelpers::formatDate_dmY($dataEntregaDe), DateHelpers::formatDate_dmY($dataEntregaAte)]);
        }
        if(!empty($dataEntregaDe) && empty($dataEntregaAte )) {
            $query = $query->where('hp.created_at', '>=', DateHelpers::formatDate_dmY($dataEntregaDe));
        }
        if(empty($dataEntregaDe) && !empty($dataEntregaAte )) {
            $query = $query->where('hp.created_at', '<=', DateHelpers::formatDate_dmY($dataEntregaAte));
        }

        if ($os) {
            $query->where('pedidos.os', 'like', '%' . $os . '%');
        }

        if ($ep) {
            $query->where('ficha_tecnica.ep', 'like', '%' . $ep . '%');
        }

        if ($quantidade) {
            $query->where('pedidos.qtde', '=', $quantidade);
        }

        if ($responsavelQualidade) {
            $query->where('pedidos.responsavel_qualidade', 'like', '%' . $responsavelQualidade . '%');
        }

        $query->limit(100);

        // Filtrar por status do lead
        if ($statusLead === 'pendente') {
            $query->where('pedidos.status_lead', '=', 'pendente');
        } elseif ($statusLead === 'removido') {
            $query->where('pedidos.status_lead', '=', 'removido');
        } elseif ($statusLead === 'finalizado') {
            $query->where('pedidos.status_lead', '=', 'finalizado');
        }

        $pedidos = $query->get();

        $tela = 'pesquisa';
        $data = array(
            'tela' => $tela,
            'nome_tela' => 'IA Qualidade',
            'pedidos' => $pedidos,
            'request' => $request,
        );
        return view('iaqualidade', $data);
    }


    /**
     * Enviar lead - atualiza status do pedido
     */
    public function enviarLead(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids)) {
            return redirect()->back()->with('error', 'Nenhum pedido selecionado');
        }

        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }

        DB::table('pedidos')
            ->whereIn('id', $ids)
            ->update([
                'status_lead' => 'finalizado',
                'datahora_envio_ultimo_lead' => now()
            ]);

        return redirect()->back()->with('success', 'Lead(s) enviado(s) com sucesso');
    }

    /**
     * Remover lead - marca como removido
     */
    public function removerLead(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids)) {
            return redirect()->back()->with('error', 'Nenhum pedido selecionado');
        }

        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }

        DB::table('pedidos')
            ->whereIn('id', $ids)
            ->update([
                'status_lead' => 'removido',
                'datahora_envio_ultimo_lead' => now()
            ]);

        return redirect()->back()->with('success', 'Lead(s) removido(s) com sucesso');
    }

    /**
     * Finalizar lead - marca como finalizado
     */
    public function finalizarLead(Request $request)
    {
        try {

            $retorno = DB::transaction(function () use ($request) {
                $ids = $request->input('ids');

                if (empty($ids)) {
                    return redirect()->back()->with('error', 'Nenhum pedido selecionado');
                }

                if (is_string($ids)) {
                    $ids = explode(',', $ids);
                }

                DB::table('pedidos')
                    ->whereIn('id', $ids)
                    ->update([
                        'status_lead' => 'finalizado',
                        'data_envio_ultimo_lead' => now()
                    ]);

                //enviar para a API de que envia leads em formato Json
                //ID 	Data de Entrega 	OS 	EP 	Quantidade 	Responsável Qualidade 	Whats do Cliente
                $pedidosComWhatsapp = DB::table('pedidos')
                    ->select(
                        'pedidos.id',
                        'hp.created_at as data_entrega',
                        'pedidos.os',
                        'ficha_tecnica.ep',
                        'pedidos.qtde',
                        'pessoas.contato_pos_venda',
                        'pessoas.numero_whatsapp_pos_venda'
                    )
                    ->join('historicos_pedidos as hp', function ($join) {
                        $join->on('hp.pedidos_id', '=', 'pedidos.id');
                        $join->whereRaw('hp.id = (select max(h2.id) from historicos_pedidos h2 where h2.pedidos_id = pedidos.id)');
                    })
                    ->join('pessoas', 'pedidos.pessoas_id', '=', 'pessoas.id')
                    ->join('ficha_tecnica', 'pedidos.fichatecnica_id', '=', 'ficha_tecnica.id')
                    ->whereIn('pedidos.id', $ids)
                    ->whereNotNull('pessoas.numero_whatsapp_pos_venda')
                    ->get();

                //transforma dados em json e envia para API
                $dataToSend = [];
                foreach ($pedidosComWhatsapp as $pedido) {
                    $dataToSend[] = [
                        'id' => $pedido->id,
                        'data_entrega' => $pedido->data_entrega,
                        'os' => $pedido->os,
                        'ep' => $pedido->ep,
                        'quantidade' => $pedido->qtde,
                        'responsavel_qualidade' => $request->input('responsavel_qualidade'),
                        'whats_cliente' => $pedido->numero_whatsapp_pos_venda
                    ];
                }

                $url = env('API_ENVIAR_LEADS_URL');

                if (!empty($dataToSend) && !empty($url)) {
                    $jsonData = json_encode($dataToSend);

                    $ch = curl_init($url); // Substitua pela URL correta da API
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                        'Content-Type: application/json',
                        'Authorization: Bearer ' . env('API_ENVIAR_LEADS_TOKEN') // Substitua pelo token correto se necessário
                    ]);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

                    $response = curl_exec($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                    if ($httpCode !== 200) {
                        DB::rollBack();
                        return redirect()->back()->with('error', 'Erro ao enviar dados para a API: ' . $response);
                    }

                } else {
                    return redirect()->back()->with('success', 'Simulação finalizada com sucesso');
                }

                return redirect()->back()->with('success', 'Lead(s) finalizado(s) com sucesso');

            });
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erro ao finalizar lead: ' . $e->getMessage());
        }
    }
}
