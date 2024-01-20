<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\pessoas;
class pessoasController extends Controller
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
        
        $pessoas = new Pessoas();

        if ($id) {
        	$pessoas = $pessoas->where('id', '=', $id);
        }

        if ($request->input('nome') != '') {
        	$pessoas = $pessoas->where('pessoa', 'like', '%'.$request->input('nome').'%');
        }

        $pessoas = $pessoas->get();
        $tela = 'pesquisa';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'pessoas',
				'pessoas'=> $pessoas,
				'request' => $request,
				'rotaIncluir' => 'incluir-clientes',
				'rotaAlterar' => 'alterar-clientes'
			);

        return view('clientes', $data);
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

    		$pessoa_id = $this->salva($request);

	    	return redirect()->route('clientes', [ 'id' => $pessoa_id ] );

    	}
        $tela = 'incluir';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'pessoas',
				'request' => $request,
				'rotaIncluir' => 'incluir-clientes',
				'rotaAlterar' => 'alterar-clientes'
			);

        return view('clientes', $data);
    }

     /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function alterar(Request $request)
    {

        $pessoas = new Pessoas();
        

        $pessoa= $pessoas->where('id', '=', $request->input('id'))->get();

		$metodo = $request->method();
		if ($metodo == 'POST') {

    		$pessoa_id = $this->salva($request);

	    	return redirect()->route('clientes', [ 'id' => $pessoa_id ] );

    	}
        $tela = 'alterar';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'pessoas',
				'pessoas'=> $pessoa,
				'request' => $request,
				'rotaIncluir' => 'incluir-clientes',
				'rotaAlterar' => 'alterar-clientes'
			);

        return view('clientes', $data);
    }

    public function salva($request) {
        $pessoas = new Pessoas();

        if($request->input('id')) {
            $pessoas = $pessoas::find($request->input('id'));
        }

        $pessoas->nome = $request->input('nome');
        $pessoas->documento = $request->input('documento');
        $pessoas->endereco = $request->input('endereco');
        $pessoas->numero = $request->input('numero');
        $pessoas->cep = $request->input('cep');
        $pessoas->bairro = $request->input('bairro');
        $pessoas->cidade = $request->input('cidade');
        $pessoas->estado = $request->input('estado');
        $pessoas->telefone = $request->input('telefone');
        $pessoas->email = $request->input('email');
        $pessoas->status = $request->input('status') == 'on' ? 1 : 0;
        $pessoas->save();

        return $pessoas->id;

}
}
