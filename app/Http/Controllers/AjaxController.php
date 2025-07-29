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
        $torre = false;
        if($status_id =='MA' ) {
            $status_id = 6; // Montagem
        } elseif($status_id == 'MT') {
            $status_id = 6; // Montagem
            $torre = true; // Montagem Torre
        } elseif($status_id == 'I') {
            $status_id = 7; // Inspeção
        }

        $pedidos = $this->consultarResponsaveis($id, $status_id, $torre);

        if($pedidos->count() > 0){
            return response()->json($pedidos);
        }else{
            return response()->json([]);
        }
    }

    public function consultarResponsaveis($id, $status_id, $torre = false)
    {
        $historicos = DB::table('pedidos')
            ->distinct()
            ->select(
                'funcionarios.id',
                'funcionarios.nome as responsavel',
                'historicos_etapas.created_at as data',
                'historicos_etapas.id as historico_id',
                'historicos_etapas.select_tipo_manutencao as tipo_manutencao',
                'etapas_pedidos.nome as etapa'
            )
            ->join('historicos_etapas', 'historicos_etapas.pedidos_id', '=', 'pedidos.id')
            ->join('etapas_pedidos', 'etapas_pedidos.id', '=', 'historicos_etapas.etapas_pedidos_id')
            ->join('funcionarios', 'funcionarios.id', '=', 'historicos_etapas.funcionarios_id')
            ->where('pedidos.id', '=', $id)
            ->where('historicos_etapas.status_id', '=', $status_id)
            ->orderby('historicos_etapas.created_at');

        if($torre) {
            $historicos = $historicos->where('historicos_etapas.select_tipo_manutencao', '=', 'T');
        }
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

}
