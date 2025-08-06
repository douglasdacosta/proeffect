<?php

namespace App\Http\Controllers;

use App\Models\Estoque;
use App\Models\Fichastecnicas;
use App\Models\Pedidos;
use App\Models\Pessoas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AjaxController extends Controller
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

    function ajaxInventario(Request $request){
        try{
            $estoque = new Estoque();
            $estoque = Estoque::find($request->input('id'));

            // se inventario for igual a true, então o valor de inventário é 1
            $estoque->inventario = ($request->input('inventario') == 'true' ? 1 : 0);
            $estoque->save();
            return true;
        }catch(\Throwable $th){
            info(print_r($th->getMessage(), true));
            return false;
        }
    }

    function ajaxLimparInventario(){
        try{
            $estoque = new Estoque();
            $estoque->where('inventario', '!=', null)->update(['inventario' => null]);
            $estoque->save();
            return true;
        }catch(\Throwable $th){
            info(print_r($th->getMessage(), true));
            return false;
        }
    }


    function ajaxFaturado(Request $request){
        try{
            $pedidos = new Pedidos();
            $pedidos = Pedidos::find($request->input('id'));

            // se faturado for igual a true, então o valor de inventário é 1
            $pedidos->faturado = ($request->input('status_faturado') == '1' ? 0 : 1);
            $pedidos->save();
            return true;
        }catch(\Throwable $th){
            info(print_r($th->getMessage(), true));
            return false;
        }
    }

    function ajaxLimparFaturado(){
        try{
            $pedidos = new Pedidos();
            $pedidos->where('faturado', '!=', null)->update(['faturado' => null]);
            $pedidos->save();
            return true;
        }catch(\Throwable $th){
            info(print_r($th->getMessage(), true));
            return false;
        }
    }


    function ajaxWhatsappStatus(Request $request){
        try{
            $pessoas = new Pessoas();
            $pessoas = Pessoas::find($request->input('id'));

            // se faturado for igual a true, então o valor de inventário é 1
            $pessoas->whatsapp_status = (!empty($request->input('whatsapp_status')) && $request->input('whatsapp_status') == '1' ? 0 : 1);
            $pessoas->save();
            return true;
        }catch(\Throwable $th){
            info(print_r($th->getMessage(), true));
            return false;
        }
    }


    function ajaxBuscaResponsveis(Request $request){
        $id = $request->input('id');
        $status_id = $request->input('status_id');
        $torre = $tipo_manutencao = false;
        if($status_id =='MA' ) {
            $status_id = 6; // Montagem
            $tipo_manutencao = 'A';
        } elseif($status_id == 'MT') {
            $status_id = 6; // Montagem
            $torre = true; // Montagem Torre
            $tipo_manutencao = 'T'; // Montagem Torre
        } elseif($status_id == 'I') {
            $status_id = 7; // Inspeção
        }

        $pedidos = $this->consultarResponsaveis($id, $status_id, $torre, $request->input('responsavel'), $tipo_manutencao);

        if($pedidos->count() > 0){
            return response()->json($pedidos);
        }else{
            return response()->json([]);
        }
    }

    public function consultarResponsaveis($id, $status_id, $torre = false, $responsavel = null, $tipo_manutencao = false)
    {
        $historicos = DB::table('pedidos')
            ->distinct()
            ->select(
                'funcionarios.id',
                'funcionarios.nome as responsavel',
                'historicos_etapas.created_at as data',
                'historicos_etapas.id as historico_id',
                'status.nome as departamento',
                'historicos_etapas.select_tipo_manutencao as tipo_manutencao',
                DB::raw("
                    CASE
                        WHEN historicos_etapas.select_motivo_pausas = '1' THEN 'F.P – Faltando Peças'
                        WHEN historicos_etapas.select_motivo_pausas = '2' THEN 'P.P – Problema na produção'
                        WHEN historicos_etapas.select_motivo_pausas = '3' THEN 'P – Pausado'
                        WHEN historicos_etapas.select_motivo_pausas = '4' THEN 'P.R – Protótipo'
                        WHEN historicos_etapas.select_motivo_pausas = '5' THEN 'A.P – Assunto Pessoal'
                        WHEN historicos_etapas.select_motivo_pausas = '6' THEN 'P.M – Problema na máquina'
                        WHEN historicos_etapas.select_motivo_pausas = '7' THEN 'E.P - Esperando próxima produção'
                        WHEN historicos_etapas.select_motivo_pausas = '8' THEN 'F.M - Faltando Material'
                    END AS motivo_pausa
                "),
                'historicos_etapas.select_motivo_pausas as motivo_pausa_id',
                'etapas_pedidos.nome as etapa',
                'status.id as departamento_id',
            )
            ->join('historicos_etapas', 'historicos_etapas.pedidos_id', '=', 'pedidos.id')
            ->join('status', 'status.id', '=', 'historicos_etapas.status_id')
            ->join('etapas_pedidos', 'etapas_pedidos.id', '=', 'historicos_etapas.etapas_pedidos_id')
            ->join('funcionarios', 'funcionarios.id', '=', 'historicos_etapas.funcionarios_id')
            ->where('pedidos.id', '=', $id);

        if ($responsavel) {
            $historicos->where('funcionarios.nome', '=', $responsavel);
        }

        if (is_array($status_id)) {
            $historicos->whereIn('historicos_etapas.status_id', $status_id);
        } else {
            $historicos->where('historicos_etapas.status_id', '=', $status_id);
            if($status_id == 6){
                $historicos = $historicos->where('historicos_etapas.select_tipo_manutencao', '=', $tipo_manutencao);
            }
        }

        $historicos->orderBy('historicos_etapas.created_at');
        $historicos = $historicos->get();

        return $historicos;
    }

    function ajaxAplicaValoresFichatecnica(){

        $tempo_aplicar = request()->input('tempo_aplicar');
        $ep = request()->input('ep');
        $status_id = request()->input('status_id');

        if(empty($ep) || empty($tempo_aplicar) || empty($status_id)) {
            return response()->json(['error' => 'Dados incompletos para aplicar valores.'], 400);
        }

        try {
            $Fichastecnicas = Fichastecnicas::where('ep', $ep)->first();

            $torre = false;
            if($status_id =='MA' ) {
                $Fichastecnicas->tempo_montagem = $tempo_aplicar;
            } elseif($status_id == 'MT') {
                $Fichastecnicas->tempo_montagem_torre = $tempo_aplicar;
            } elseif($status_id == 'I') {
                $Fichastecnicas->tempo_inspecao = $tempo_aplicar;
            }

            $Fichastecnicas->save();

            return response()->json(['success' => 'Valores aplicados com sucesso.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao aplicar valores: ' . $e->getMessage()], 500);
        }
    }

    function ajaxSalvaNovoApontamento(Request $request){
        $id = $request->input('id');
        $responsavel = $request->input('responsavel');
        $etapa = $request->input('etapa');
        $data_hora = $request->input('data_hora');
        $motivo_pausa = $request->input('motivo_pausa');

        //converte data_hora "16/01/2025+12:26:04" para o formato correto "2025-01-16 12:26:04"

        $data_hora = \DateTime::createFromFormat('d/m/Y H:i:s', $data_hora);
        $data_hora = $data_hora ? $data_hora->format('Y-m-d H:i:s') : null;

        if(empty($id) || empty($responsavel) || empty($etapa  ) || empty($data_hora)) {
            return response()->json(['error' => 'Dados incompletos para salvar novo apontamento.'], 400);
        }

        $funcionario = DB::table('funcionarios')
            ->where('nome', $responsavel)
            ->first();

        $status_id = request()->input('status_id');
        $select_tipo_manutencao = null;
        if($status_id =='MA' ) {
            $status_id = '6'; // Montagem
            $select_tipo_manutencao = 'A';
        } elseif($status_id == 'MT') {
            $status_id = '6'; // Montagem
            $select_tipo_manutencao = 'T'; // Montagem Torre
        } elseif($status_id == 'I') {
            $status_id = '7'; // Inspeção
            $select_tipo_manutencao = 'I';
        }

        try {
            if($request->has('historico_id') && !empty($request->input('historico_id'))) {
                // Atualiza o histórico existente
                DB::table('historicos_etapas')
                    ->where('id', $request->input('historico_id'))
                    ->update([
                        'pedidos_id' => $id,
                        'status_id' => $status_id,
                        'etapas_pedidos_id' => $etapa,
                        'etapas_alteracao_id'=> 1,
                        'funcionarios_id' => $funcionario->id,
                        'created_at' => $data_hora,
                        'updated_at' => $data_hora,
                        'select_motivo_pausas' => $motivo_pausa,
                        'select_tipo_manutencao' => $select_tipo_manutencao,
                    ]);
                return response()->json(['success' => 'Apontamento atualizado com sucesso.']);
            }

            DB::table('historicos_etapas')->insert([
                'pedidos_id' => $id,
                'status_id' => $status_id,
                'etapas_pedidos_id' => $etapa,
                'etapas_alteracao_id'=> 1,
                'funcionarios_id' => $funcionario->id,
                'created_at' => $data_hora,
                'updated_at' => $data_hora,
                'select_tipo_manutencao' => $select_tipo_manutencao,
            ]);

            return response()->json(['success' => 'Novo apontamento salvo com sucesso.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao salvar novo apontamento: ' . $e->getMessage()], 500);
        }
    }

    function ajaxExcluiNovoApontamento(Request $request){
        $historico_id = $request->input('historico_id');

        if(empty($historico_id)) {
            return response()->json(['error' => 'Dados incompletos para excluir apontamento.'], 400);
        }

        try {
            DB::table('historicos_etapas')
                ->where('id', $historico_id)
                ->delete();

            return response()->json(['success' => 'Apontamento excluído com sucesso.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao excluir apontamento: ' . $e->getMessage()], 500);
        }
    }
}
