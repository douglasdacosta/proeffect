<?php

namespace App\Http\Controllers;

use App\Models\Estoque;
use App\Models\Fichastecnicas;
use App\Models\HistoricosEtapasProjetos;
use App\Models\Pedidos;
use App\Models\Pessoas;
use App\Models\Projetos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $tipo_manutencao = $request->input('tipo_manutencao');
        $torre = false;
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
        $tipo_manutencao = request()->input('tipo_manutencao');

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
        if($status_id =='MA' ) {
            $status_id = '6'; // Montagem
        } elseif($status_id == 'MT') {
            $status_id = '6'; // Montagem
        } elseif($status_id == 'I') {
            $status_id = '7'; // Inspeção
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
                'select_tipo_manutencao' => $tipo_manutencao,
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


    function ajaxAdicionarTarefaProjetos(Request $request){
        info($request->all());
        $mensagem = $request->input('tarefa_modal');
        $projetos_id = $request->input('projeto_id');
        $data_tarefa = $request->input('data_tarefa');
        $funcionario_id = $request->input('funcionario_id');

        if(empty($mensagem) || empty($projetos_id)) {
            return response()->json(['error' => 'Dados incompletos para adicionar tarefa.'], 400);
        }




        try {
            DB::table('tarefas_projetos')->insert([
                'projetos_id' => $projetos_id,
                'funcionario_id' => $funcionario_id,
                'funcionario_criador_id' => auth()->user()->id,
                'data_hora' => $data_tarefa,
                'mensagem' => $mensagem,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json(['success' => 'Tarefa adicionada com sucesso.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao adicionar tarefa: ' . $e->getMessage()], 500);
        }
    }

    function ajaxBuscarTarefasProjetos(Request $request){
        $projeto_id = $request->input('projeto_id');

        if(empty($projeto_id)) {
            return response()->json(['error' => 'Dados incompletos para buscar tarefas.'], 400);
        }

        try {
            $tarefas = DB::table('tarefas_projetos')
                ->leftjoin('funcionarios', 'tarefas_projetos.funcionario_id', '=', 'funcionarios.id')
                ->leftjoin('funcionarios as criador', 'tarefas_projetos.funcionario_criador_id', '=', 'criador.id')
                ->where('tarefas_projetos.projetos_id', $projeto_id)
                ->orderBy('tarefas_projetos.created_at', 'desc')
                ->select('tarefas_projetos.*', 'funcionarios.nome as funcionario_nome' , 'criador.nome as funcionario_criador_nome')
                ->get();



            return response()->json(['success' => true, 'tarefas' => $tarefas]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao buscar tarefas: ' . $e->getMessage()], 500);
        }
    }

    function ajaxAdicionarApontamentoProjetos(Request $request){
        $apontamento = $request->input('apontamento_id');
        $projeto_id = $request->input('projeto_id');

        $projetos = Projetos::find($projeto_id);
        if(!$projetos) {
            return response()->json(['error' => 'Projeto não encontrado.'], 404);
        }

        //busca o ultimo historico de etapas do projeto
        $ultimo_historico = DB::table('historicos_etapas_projetos')
            ->where('projetos_id', $projeto_id)
            ->orderBy('created_at', 'desc')
            ->first();


        if($ultimo_historico->etapas_pedidos_id == 4 ) {
            return response()->json(['error' => "Este já foi terminado!"], 400);
        }

        if($ultimo_historico->etapas_pedidos_id == 1 && $apontamento == $ultimo_historico->etapas_pedidos_id) {
            return response()->json(['error' => "O apontamento já é INÍCIO. Favor lançar PAUSA, CONTINUAR ou TERMINAR"], 400);
        }

        if($ultimo_historico->etapas_pedidos_id == 2 && $apontamento <= 2) {
            return response()->json(['error' => "O último apontamento é PAUSA. Favor lançar CONTINUAR!"], 400);
        }

        if($ultimo_historico->etapas_pedidos_id == 3 && in_array($apontamento, [1,4]) ) {
            return response()->json(['error' => "O último apontamento é CONTINUAR. Favor lançar PAUSA ou TERMINAR!"], 400);
        }





        try {
            DB::table('historicos_etapas_projetos')->insert([
                'projetos_id' => $projeto_id,
                'etapas_pedidos_id' => $apontamento,
                'funcionarios_id' => auth()->user()->id,
                'status_projetos_id' => $projetos->status_projetos_id,
                'sub_status_projetos_id' => $projetos->sub_status_projetos_codigo,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json(['success' => 'Apontamento adicionado com sucesso.']);
        } catch (\Exception $e) {
            info($e->getMessage());
            return response()->json(['error' => 'Erro ao adicionar apontamento: ' . $e->getMessage()], 500);
        }
    }

    function ajaxAdicionarFuncionarioProjetos(Request $request){
        $funcionario_id = $request->input('funcionario_id');
        $projetos_id = $request->input('projeto_id');

        if(empty($funcionario_id) || empty($projetos_id)) {
            return response()->json(['error' => 'Dados incompletos para adicionar funcionário.'], 400);
        }

        try {
            DB::table('projetos')->where('id', $projetos_id)->update([
                'funcionarios_id' => $funcionario_id,
            ]);

            return response()->json(['success' => 'Funcionário adicionado com sucesso.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao adicionar funcionário: ' . $e->getMessage()], 500);
        }
    }


    function ajaxAlterarStatusProjetos(Request $request){
        $status_id = $request->input('status');
        $id = $request->input('projeto_id');

        $projeto = new Projetos();
        $projeto = $projeto::find($id);

        $projetosController = new ProjetosController();
        $status_projetos_id = $projetosController->getSubStatus($status_id);
        $sub_status_projetos_codigo = $status_projetos_id[0]['codigo'];
        $sub_status_projetos_id = $status_projetos_id[0]['id'];
        $status_projetos_id = $status_projetos_id[0]['status_projetos_id'];

        $em_alerta = 1;
        if($projeto->sub_status_projetos_codigo != $status_id){
            $projeto->data_status = date('Y-m-d');

            $HistoricosEtapasProjetos = new HistoricosEtapasProjetos();
            $HistoricosEtapasProjetos->projetos_id = $projeto->id;
            $HistoricosEtapasProjetos->status_projetos_id = $status_projetos_id;
            $HistoricosEtapasProjetos->sub_status_projetos_id = $sub_status_projetos_id;
            $HistoricosEtapasProjetos->funcionarios_id = Auth::user()->id;
            $HistoricosEtapasProjetos->etapas_pedidos_id = $projeto->etapa_projeto_id;
            $HistoricosEtapasProjetos->save();

        }

        if($projeto->etapa_projeto_id == 5  && $status_id == 36) {
            $em_alerta = 0;
        }




        if(empty($status_id) || empty($id)) {
            return response()->json(['error' => 'Dados incompletos para alterar status.'], 400);
        }

        try {
            DB::table('projetos')->where('id', $id)->update([
                'sub_status_projetos_codigo' => $sub_status_projetos_codigo,
                'status_projetos_id' => $status_projetos_id,
                'em_alerta' => $em_alerta,

            ]);

            return response()->json(['success' => 'Status alterado com sucesso.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao alterar status: ' . $e->getMessage()], 500);
        }
    }

    function ajaxAlterarEtapasProjetos(Request $request){

        $projeto = new Projetos();
        $projetosController = new ProjetosController();

        $sub_status_projetos_codigo = $request->input('sub_status_projetos_codigo');
        $etapa_projeto_id = $request->input('etapa');
        $projeto_id = $request->input('projeto_id');

        if(empty($sub_status_projetos_codigo) || empty($projeto_id ) || empty($etapa_projeto_id)) {
            return response()->json(['error' => 'Dados incompletos para alterar a Etapa.'], 400);
        }


        $projeto = $projeto::find($projeto_id);

        $projeto->etapa_projeto_id = $etapa_projeto_id;
        try {

            if($etapa_projeto_id == 5  && $sub_status_projetos_codigo != 36) {
                $sub_status_projetos_codigo = 2;
            }

            $status_projetos_id = $projetosController->getSubStatus($sub_status_projetos_codigo);

            $sub_status_projetos_id = $status_projetos_id[0]['id'];
            $status_projetos_id = $status_projetos_id[0]['status_projetos_id'];

            $projeto->status_projetos_id = $status_projetos_id;

            if($projeto->sub_status_projetos_codigo != $sub_status_projetos_codigo){
                $projeto->data_status = date('Y-m-d');
                $projeto->em_alerta = 1;

                $HistoricosEtapasProjetos = new HistoricosEtapasProjetos();
                $HistoricosEtapasProjetos->projetos_id = $projeto->id;
                $HistoricosEtapasProjetos->status_projetos_id = $status_projetos_id;
                $HistoricosEtapasProjetos->sub_status_projetos_id = $sub_status_projetos_id;
                $HistoricosEtapasProjetos->funcionarios_id = Auth::user()->id;
                $HistoricosEtapasProjetos->etapas_pedidos_id = $etapa_projeto_id;
                $HistoricosEtapasProjetos->save();

            }

            $projeto->sub_status_projetos_codigo = $sub_status_projetos_codigo;
            if($etapa_projeto_id == 5  && $sub_status_projetos_codigo == 36) {
                $projeto->em_alerta = 0;
            }

            $projeto->save();

            return response()->json(['success' => 'Etapa alterada com sucesso.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao alterar a Etapa: ' . $e->getMessage()], 500);
        }
    }



}
