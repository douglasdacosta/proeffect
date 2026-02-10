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

        // Query base para buscar dados na tabela de leads
        $query = DB::table('ia_qualidade_leads')
            ->select(
                'ia_qualidade_leads.pedido_id as id',
                'ia_qualidade_leads.data_entrega',
                'ia_qualidade_leads.os',
                'ia_qualidade_leads.ep',
                'ia_qualidade_leads.qtde',
                'ia_qualidade_leads.pessoas_id',
                'ia_qualidade_leads.datahora_envio_ultimo_lead',
                'ia_qualidade_leads.contato_pos_venda',
                'ia_qualidade_leads.numero_whatsapp_pos_venda',
                'ia_qualidade_leads.responsavel_qualidade'
            );


        if(!empty($dataEntregaDe) && !empty($dataEntregaAte )) {
            $query = $query->whereBetween('ia_qualidade_leads.data_entrega', [DateHelpers::formatDate_dmY($dataEntregaDe), DateHelpers::formatDate_dmY($dataEntregaAte)]);
        }
        if(!empty($dataEntregaDe) && empty($dataEntregaAte )) {
            $query = $query->where('ia_qualidade_leads.data_entrega', '>=', DateHelpers::formatDate_dmY($dataEntregaDe));
        }
        if(empty($dataEntregaDe) && !empty($dataEntregaAte )) {
            $query = $query->where('ia_qualidade_leads.data_entrega', '<=', DateHelpers::formatDate_dmY($dataEntregaAte));
        }

        if ($os) {
            $query->where('ia_qualidade_leads.os', 'like', '%' . $os . '%');
        }

        if ($ep) {
            $query->where('ia_qualidade_leads.ep', 'like', '%' . $ep . '%');
        }

        if ($quantidade) {
            $query->where('ia_qualidade_leads.qtde', '=', $quantidade);
        }

        if ($responsavelQualidade) {
            $query->where('ia_qualidade_leads.responsavel_qualidade', 'like', '%' . $responsavelQualidade . '%');
        }

        $query->limit(100);

        // Filtrar por status do lead
        if ($statusLead === 'pendente') {
            $query->where('ia_qualidade_leads.status_lead', '=', 'pendente');
        } elseif ($statusLead === 'removido') {
            $query->where('ia_qualidade_leads.status_lead', '=', 'removido');
        } elseif ($statusLead === 'finalizado') {
            $query->where('ia_qualidade_leads.status_lead', '=', 'finalizado');
        }

        $pedidos = $query->get();

        // Agrupar por OS
        $pedidosAgrupados = [];
        foreach ($pedidos as $pedido) {
            $os = $pedido->os;

            if (!isset($pedidosAgrupados[$os])) {
                $pedidosAgrupados[$os] = [
                    'id' => $pedido->id,
                    'data_entrega' => $pedido->data_entrega,
                    'os' => $pedido->os,
                    'ep' => [],
                    'qtde_total' => 0,
                    'pessoas_id' => $pedido->pessoas_id,
                    'datahora_envio_ultimo_lead' => $pedido->datahora_envio_ultimo_lead,
                    'contato_pos_venda' => $pedido->contato_pos_venda,
                    'numero_whatsapp_pos_venda' => $pedido->numero_whatsapp_pos_venda,
                    'responsavel_qualidade' => $pedido->responsavel_qualidade,
                    'pedidos_ids' => []
                ];
            }

            // Adicionar EP e quantidade
            if (!in_array($pedido->ep, $pedidosAgrupados[$os]['ep'])) {
                $pedidosAgrupados[$os]['ep'][] = $pedido->ep;
            }
            $pedidosAgrupados[$os]['qtde_total'] += $pedido->qtde;
            $pedidosAgrupados[$os]['pedidos_ids'][] = $pedido->id;
        }

        // Converter para array de objetos para manter compatibilidade com view
        $pedidosAgrupados = array_map(function ($grupo) {
            return (object) [
                'id' => implode(',', $grupo['pedidos_ids']),
                'data_entrega' => $grupo['data_entrega'],
                'os' => $grupo['os'],
                'ep' => implode(',', $grupo['ep']),
                'qtde' => $grupo['qtde_total'],
                'pessoas_id' => $grupo['pessoas_id'],
                'datahora_envio_ultimo_lead' => $grupo['datahora_envio_ultimo_lead'],
                'contato_pos_venda' => $grupo['contato_pos_venda'],
                'numero_whatsapp_pos_venda' => $grupo['numero_whatsapp_pos_venda'],
                'responsavel_qualidade' => $grupo['responsavel_qualidade'],
            ];
        }, $pedidosAgrupados);

        $tela = 'pesquisa';
        $data = array(
            'tela' => $tela,
            'nome_tela' => 'IA Qualidade',
            'pedidos' => $pedidosAgrupados,
            'request' => $request,
        );
        return view('iaqualidade', $data);
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

        DB::table('ia_qualidade_leads')
            ->whereIn('pedido_id', $ids)
            ->update([
                'status_lead' => 'removido',
                'datahora_envio_ultimo_lead' => now()
            ]);

        return redirect()->back()->with('success', 'Lead(s) removido(s) com sucesso');
    }

    /**
     * Finalizar lead - marca como finalizado
     */
    public function enviarLead(Request $request)
    {
        try {
            $ids = $request->input('ids');

            if (empty($ids)) {
                return redirect()->back()->with('error', 'Nenhum pedido selecionado');
            }

            if (is_string($ids)) {
                $ids = explode(',', $ids);
            }

            $retorno = DB::transaction(function () use ($request, $ids) {
                DB::table('ia_qualidade_leads')
                    ->whereIn('pedido_id', $ids)
                    ->update([
                        'status_lead' => 'finalizado',
                        'datahora_envio_ultimo_lead' => now()
                    ]);

                //enviar para a API de que envia leads em formato Json
                //ID 	Data de Entrega 	OS 	EP 	Quantidade 	Responsável Qualidade 	Whats do Cliente
                $pedidosComWhatsapp = DB::table('ia_qualidade_leads')
                    ->select(
                        'ia_qualidade_leads.pedido_id as id',
                        'ia_qualidade_leads.data_entrega',
                        'ia_qualidade_leads.os',
                        'ia_qualidade_leads.ep',
                        'ia_qualidade_leads.qtde',
                        'ia_qualidade_leads.contato_pos_venda',
                        'ia_qualidade_leads.numero_whatsapp_pos_venda',
                        'ia_qualidade_leads.responsavel_qualidade'
                    )
                    ->whereIn('ia_qualidade_leads.pedido_id', $ids)
                    ->whereNotNull('ia_qualidade_leads.numero_whatsapp_pos_venda')
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
                        'responsavel_qualidade' => $pedido->responsavel_qualidade ?? $request->input('responsavel_qualidade'),
                        'whats_cliente' => $pedido->numero_whatsapp_pos_venda
                    ];
                }

                $url = env('API_ENVIAR_LEADS_URL');
                \Log::info($dataToSend);
                \Log::info($url);
                if (!empty($dataToSend) && !empty($url)) {
                    $jsonData = json_encode($dataToSend);

                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                        'Content-Type: application/json',
                        'Authorization: Bearer ' . env('API_ENVIAR_LEADS_TOKEN')
                    ]);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

                    $response = curl_exec($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);

                    if ($httpCode !== 200) {
                          \Log::info($httpCode);
                        if ($response === false) {
                            $error = curl_error($ch);
                            $errno = curl_errno($ch);
                            \Log::info([
                                'curl_error' => $error,
                                'curl_errno' => $errno,
                            ]);
                        }

                        throw new \Exception('Erro ao enviar dados para a API: ' . $response);
                    }

                    return ['success' => true, 'message' => 'Lead(s) finalizado(s) com sucesso'];
                } else {
                    return ['success' => true, 'message' => 'Simulação finalizada com sucesso'];
                }
            });

            return redirect()->back()->with('success', $retorno['message']);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao finalizar lead: ' . $e->getMessage());
        }
    }
}
