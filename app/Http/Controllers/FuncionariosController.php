<?php

namespace App\Http\Controllers;

use App\Models\Funcionarios;
use Illuminate\Http\Request;
use App\Models\Pessoas;
class FuncionariosController extends Controller
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

        $id = !empty($request->input('id')) ? ($request->input('id')) : ( !empty($id) ? $id : false );

        $funcionarios = new Funcionarios();

        if ($id) {
        	$funcionarios = $funcionarios->where('id', '=', $id);
        }

        if ($request->input('nome') != '') {
        	$funcionarios = $funcionarios->where('nome', '=', $request->input('nome'));
        }

        if ($request->input('funcao') != '') {
        	$funcionarios = $funcionarios->where('funcao', 'like', '%'.$request->input('funcao').'%');
        }

        if ($request->input('nome_contato') != '') {
        	$funcionarios = $funcionarios->where('nome_contato', 'like', '%'.$request->input('nome_contato').'%');
        }

        $funcionarios = $funcionarios->get();
        $tela = 'pesquisa';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'funcionarios',
				'funcionarios'=> $funcionarios,
				'request' => $request,
				'rotaIncluir' => 'incluir-funcionarios',
				'rotaAlterar' => 'alterar-funcionarios'
			);

        return view('funcionarios', $data);
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

    		$funcionario_id = $this->salva($request);

	    	return redirect()->route('funcionarios', [ 'id' => $funcionario_id ] );

    	}
        $tela = 'incluir';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'funcionarios',
				'request' => $request,
				'rotaIncluir' => 'incluir-funcionarios',
				'rotaAlterar' => 'alterar-funcionarios'
			);

        return view('funcionarios', $data);
    }

     /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function alterar(Request $request)
    {
        $funcionarios = new Funcionarios();


        $funcionario= $funcionarios->where('id', '=', $request->input('id'))->get();

		$metodo = $request->method();
		if ($metodo == 'POST') {

    		$funcionario_id = $this->salva($request);

	    	return redirect()->route('funcionarios', [ 'id' => $funcionario_id ] );

    	}
        $tela = 'alterar';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'funcionarios',
				'funcionarios'=> $funcionario,
				'request' => $request,
				'rotaIncluir' => 'incluir-funcionarios',
				'rotaAlterar' => 'alterar-funcionarios'
			);

        return view('funcionarios', $data);
    }

    public function salva($request) {
        $funcionarios = new Funcionarios();
        if($request->input('id')) {
            $funcionarios = $funcionarios::find($request->input('id'));
        }
        $funcionarios->nome = $request->input('nome');
        $funcionarios->funcao = $request->input('funcao');
        $funcionarios->status = $request->input('status');
        $funcionarios->senha = $request->input('senha');
        $funcionarios->save();

        return $funcionarios->id;

}
}
