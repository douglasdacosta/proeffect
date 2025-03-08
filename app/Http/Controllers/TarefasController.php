<?php

namespace App\Http\Controllers;

use App\Models\Funcionarios;
use App\Models\Tarefas;
use App\Providers\DateHelpers;
use Illuminate\Http\Request;

class TarefasController extends Controller
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {

        $user = \Auth::user();
        $users = new Funcionarios();
        $users = $users->where('id', '=', $user->id)->first();
        $perfil = $users->perfil;


        $tarefas = new Tarefas();
        $tarefas = $tarefas->select('tarefas.*', 'funcionarios.nome as funcionario', 'funcionarios_criador.nome as criador');
        $tarefas = $tarefas->join('funcionarios', 'funcionarios.id', '=', 'tarefas.funcionario_id');
        $tarefas = $tarefas->join('funcionarios AS funcionarios_criador', 'funcionarios_criador.id', '=', 'tarefas.funcionario_criador_id');
        $tarefas = $tarefas->where('tarefas.status', '=', 'A');
        $tarefas = $tarefas->orderBy('tarefas.finalizado', 'asc');
        $tarefas = $tarefas->orderBy('tarefas.data_atividade', 'asc');


        if (!empty($request->input('id'))) {
            $tarefas = $tarefas->where('tarefas.id', '=', $request->input('id'));
        }

        if (!empty($request->input('funcionario'))) {
            $tarefas = $tarefas->where(function ($query) use ($request) {
                $query->where('funcionario_id', $request->input('funcionario'))
                    ->orWhere('funcionario_criador_id', $request->input('funcionario'));
            });
        } else {
            $tarefas = $tarefas->where(function ($query) use ($user) {
                $query->where('funcionario_id', $user->id)
                    ->orWhere('funcionario_criador_id', $user->id);
            });
        }

        if (!empty($request->input('status_atividade'))) {
            $tarefas = $tarefas->whereNotNull('tarefas.finalizado');
        } else {
            $tarefas = $tarefas->whereNull('tarefas.finalizado');
        }

        if (!empty($request->input('status'))) {
            $tarefas = $tarefas->where('tarefas.status', '=', $request->input('status'));
        } else {
            $tarefas = $tarefas->where('tarefas.status', '=', 'A');
        }

        if ($request->input('mensagem') != '') {
            $tarefas = $tarefas->where('mensagem', 'like', '%' . $request->input('mensagem') . '%');
        }
        
        $tarefas = $tarefas->get();

        $tela = 'pesquisa';
        $data = array(
            'tela' => $tela,
            'nome_tela' => 'tarefas',
            'tarefas' => $tarefas,
            'funcionarios' => $this->getFuncionarios(),
            'usuario' => $user->id,
            'perfil' => $perfil,
            'request' => $request,
            'rotaIncluir' => 'incluir-tarefas',
            'rotaAlterar' => 'alterar-tarefas'
        );

        return view('tarefas', $data);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function incluir(Request $request)
    {
        $metodo = $request->method();

        if ($metodo == 'POST') {

            $tarefas_id = $this->salva($request);

            return redirect()->route('tarefas', ['id' => $tarefas_id]);

        }

        $tela = 'incluir';
        $data = array(
            'tela' => $tela,
            'nome_tela' => 'tarefas',
            'request' => $request,
            'funcionarios' => $this->getFuncionarios(),
            'rotaIncluir' => 'incluir-tarefas',
            'rotaAlterar' => 'alterar-tarefas'
        );

        return view('tarefas', $data);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function alterar(Request $request)
    {

        $tarefas = new Tarefas();
        $tarefas = $tarefas->select('tarefas.*', 'funcionarios.nome as funcionario', 'funcionarios_criador.nome as criador');
        $tarefas = $tarefas->join('funcionarios', 'funcionarios.id', '=', 'tarefas.funcionario_id');
        $tarefas = $tarefas->join('funcionarios AS funcionarios_criador', 'funcionarios_criador.id', '=', 'tarefas.funcionario_criador_id');
        $tarefas = $tarefas->where('tarefas.status', '=', 'A');
        $tarefas = $tarefas->where('tarefas.id', '=', $request->input('id'));
        $tarefas = $tarefas->get();


        $metodo = $request->method();
        if ($metodo == 'POST') {

            $tarefas_id = $this->salva($request);

            return redirect()->route('tarefas', ['id' => $tarefas_id]);

        }

        $tela = 'alterar';
        $data = array(
            'tela' => $tela,
            'nome_tela' => 'tarefas',
            'tarefas' => $tarefas,
            'funcionarios' => $this->getFuncionarios(),
            'request' => $request,
            'rotaIncluir' => 'incluir-tarefas',
            'rotaAlterar' => 'alterar-tarefas'
        );

        return view('tarefas', $data);
    }

    public function salva($request)
    {
        $tarefas = new Tarefas();

        if ($request->input('id')) {
            $tarefas = $tarefas::find($request->input('id'));
        }

        $tarefas->data_hora = !empty($request->input('data_hora')) ? DateHelpers::formatDate_dmY($request->input('data_hora')) : date('Y-m-d H:i:s');
        $tarefas->data_atividade = !empty($request->input('data_atividade')) ? DateHelpers::formatDate_dmY($request->input('data_atividade')) : date('Y-m-d H:i:s');
        $tarefas->funcionario_id = $request->input('funcionario');
        $tarefas->funcionario_criador_id = \Auth::user()->id;
        if($tarefas->finalizado == null && $request->input('status_atividade') == 1){
            $tarefas->finalizado = date('Y-m-d H:i:s');
        }

        if($request->input('status_atividade') == 0 ){
            $tarefas->finalizado = null;
        }

        $tarefas->mensagem = $request->input('mensagem');
        $tarefas->status = $request->input('status');
        $tarefas->save();

        return $tarefas->id;

    }

    function getFuncionarios() {

        $funcionarios = new Funcionarios();
        $funcionarios = $funcionarios->where('status', '=', 'A')->orderBy('nome', 'asc')->get();

        return $funcionarios;
    }

    public function marcarTarefaLida(Request $request)
    {
        $tarefa = Tarefas::find($request->input('tarefa'));
        $tarefa->data_hora_lido = date('Y-m-d H:i:s');
        $tarefa->save();
    }
}
