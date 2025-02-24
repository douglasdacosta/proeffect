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
        $tarefas = new Tarefas();
        $tarefas = $tarefas->select('tarefas.*', 'funcionarios.nome as funcionario');
        $tarefas = $tarefas->join('funcionarios', 'funcionarios.id', '=', 'tarefas.funcionario_id');
        $tarefas = $tarefas->where('tarefas.status', '=', 'A');
        $tarefas = $tarefas->orderBy('tarefas.data_hora', 'desc');


        if (!empty($request->input('id'))) {
            $tarefas = $tarefas->where('tarefas.id', '=', $request->input('id'));
        }
        
        if (!empty($request->input('funcionario'))) {
            $tarefas = $tarefas->where('tarefas.funcionario_id', '=', $request->input('funcionario'));
        }

        if (!empty($request->input('status'))) {
            $tarefas = $tarefas->where('tarefas.status', '=', $request->input('status'));
        } else {
            $tarefas = $tarefas->where('tarefas.status', '=', 'A');
        }

        if ($request->input('mensagem') != '') {
            $tarefas = $tarefas->where('mensagem', 'like', '%' . $request->input('mensagem') . '%');
        }

        $funcionarios = new Funcionarios();
        $funcionarios = $funcionarios->where('status', '=', 'A')->get();

        $tarefas = $tarefas->get();
        $tela = 'pesquisa';
        $data = array(
            'tela' => $tela,
            'nome_tela' => 'tarefas',
            'tarefas' => $tarefas,
            'funcionarios' => $funcionarios,
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

        $funcionarios = new Funcionarios();
        $funcionarios = $funcionarios->where('status', '=', 'A')->get();

        $tela = 'incluir';
        $data = array(
            'tela' => $tela,
            'nome_tela' => 'tarefas',
            'request' => $request,
            'funcionarios' => $funcionarios,
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
        $tarefas = $tarefas->select('tarefas.*', 'funcionarios.nome as funcionario');
        $tarefas = $tarefas->join('funcionarios', 'funcionarios.id', '=', 'tarefas.funcionario_id');
        $tarefas = $tarefas->where('tarefas.status', '=', 'A');
        $tarefas = $tarefas->where('tarefas.id', '=', $request->input('id'));
        $tarefas = $tarefas->orderBy('tarefas.data_hora', 'desc');
        $tarefas = $tarefas->get();


        $metodo = $request->method();
        if ($metodo == 'POST') {

            $tarefas_id = $this->salva($request);

            return redirect()->route('tarefas', ['id' => $tarefas_id]);

        }
        
        $funcionarios = new Funcionarios();
        $funcionarios = $funcionarios->where('status', '=', 'A')->get();

        $tela = 'alterar';
        $data = array(
            'tela' => $tela,
            'nome_tela' => 'tarefas',
            'tarefas' => $tarefas,
            'funcionarios' => $funcionarios,
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
        $tarefas->funcionario_id = $request->input('funcionario');
        $tarefas->funcionario_criador_id = \Auth::user()->id;
        
        $tarefas->mensagem = $request->input('mensagem');
        $tarefas->status = $request->input('status');
        $tarefas->save();

        return $tarefas->id;

    }

    public function marcarTarefaLida(Request $request)
    {
        $tarefa = Tarefas::find($request->input('tarefa'));
        $tarefa->data_hora_lido = date('Y-m-d H:i:s');
        $tarefa->save();
    }
}
