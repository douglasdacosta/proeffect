<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pessoas;
class PessoasController extends Controller
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

        if (!empty($request->input('status'))){
            $pessoas = $pessoas->where('status', '=', $request->input('status'));
        }

        if ($request->input('codigo_cliente') != '') {
        	$pessoas = $pessoas->where('codigo_cliente', '=', $request->input('codigo_cliente'));
        }

        if ($request->input('nome_cliente') != '') {
        	$pessoas = $pessoas->where('nome_cliente', 'like', '%'.$request->input('nome_cliente').'%');
        }

        if ($request->input('nome_contato') != '') {
        	$pessoas = $pessoas->where('nome_contato', 'like', '%'.$request->input('nome_contato').'%');
        }

        $pessoas = $pessoas->get();
        $tela = 'pesquisa';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'clientes',
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
                'nome_tela' => 'clientes',
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
                'nome_tela' => 'clientes',
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

        $pessoas->codigo_cliente = $request->input('codigo_cliente');
        $pessoas->nome_cliente = $request->input('nome_cliente');
        $pessoas->nome_contato = $request->input('nome_contato');
        $pessoas->nome_assistente = $request->input('nome_assistente');
        $pessoas->endereco = $request->input('endereco');
        $pessoas->numero = $request->input('numero');
        $pessoas->cep = $request->input('cep');
        $pessoas->bairro = $request->input('bairro');
        $pessoas->cidade = $request->input('cidade');
        $pessoas->estado = $request->input('estado');
        $pessoas->telefone = preg_replace("/[^0-9]/", "", $request->input('telefone'));
        $pessoas->email = $request->input('email');
        $pessoas->status = $request->input('status');
        $pessoas->save();

        return $pessoas->id;

}
}
