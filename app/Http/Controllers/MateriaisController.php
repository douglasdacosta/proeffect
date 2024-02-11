<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Materiais;
use App\Providers\DateHelpers;

class MateriaisController extends Controller
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
        $codigo = !empty($request->input('codigo')) ? ($request->input('codigo')) : ( !empty($codigo) ? $codigo : false );


        $materiais = new Materiais();

        if ($id) {
        	$materiais->where('id', '=', $id);
        }
        if ($codigo) {
        	$materiais->where('codigo', '=', $codigo);
        }

        if ($request->input('nome') != '') {
        	$materiais->where('material', 'like', '%'.$request->input('nome').'%');
        }

        if (!empty($request->input('status'))){
            $materiais = $materiais->where('status', '=', $request->input('status'));
        }

        $materiais = $materiais->get();
        $tela = 'pesquisa';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'materiais',
				'materiais'=> $materiais,
				'request' => $request,
				'rotaIncluir' => 'incluir-materiais',
				'rotaAlterar' => 'alterar-materiais'
			);

        return view('materiais', $data);
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

    		$material_id = $this->salva($request);

	    	return redirect()->route('materiais', [ 'id' => $material_id ] );

    	}
        $tela = 'incluir';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'materiais',
				'request' => $request,
				'rotaIncluir' => 'incluir-materiais',
				'rotaAlterar' => 'alterar-materiais'
			);

        return view('materiais', $data);
    }

     /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function alterar(Request $request)
    {

        $materiais = new Materiais();


        $material= $materiais->where('id', '=', $request->input('id'))->get();

		$metodo = $request->method();
		if ($metodo == 'POST') {

    		$material_id = $this->salva($request);

	    	return redirect()->route('materiais', [ 'id' => $material_id ] );

    	}

        $tela = 'alterar';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'materiais',
				'materiais'=> $material,
				'request' => $request,
				'rotaIncluir' => 'incluir-materiais',
				'rotaAlterar' => 'alterar-materiais'
			);

        return view('materiais', $data);
    }

    public function salva($request) {
        $materiais = new Materiais();

        if($request->input('id')) {
            $materiais = $materiais::find($request->input('id'));
        }
        $materiais->codigo = $request->input('codigo');
        $materiais->material = $request->input('material');
        $materiais->espessura = $request->input('espessura');
        $materiais->unidadex = $request->input('unidadex');
        $materiais->unidadey = $request->input('unidadey');
        $materiais->tempo_montagem_torre = $request->input('tempo_montagem_torre');
        $materiais->valor = DateHelpers::formatFloatValue($request->input('valor'));
        $materiais->status = $request->input('status');
        $materiais->save();

        return $materiais->id;

}
}
