<?php

namespace App\Http\Controllers;

use App\Models\CategoriasMateriais;
use Illuminate\Http\Request;
class CategoriasMateriaisController extends Controller
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
        $categorias_materiais = new CategoriasMateriais();

        $id = !empty($request->input('id')) ? ($request->input('id')) : ( !empty($id) ? $id : false );

        if ($id) {
            $categorias_materiais = $categorias_materiais->where('id', '=', $id);
        }

        if (!empty($request->input('status'))){
            $categorias_materiais = $categorias_materiais->where('status', '=', $request->input('status'));
        } else {
            $categorias_materiais = $categorias_materiais->where('status', '=', 'A');
        }

        if ($request->input('nome') != '') {
        	$categorias_materiais = $categorias_materiais->where('nome', 'like', '%'.$request->input('nome').'%');
        }

        $categorias_materiais = $categorias_materiais->get();
        $tela = 'pesquisa';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'categorias de materiais',
				'categorias_materiais'=> $categorias_materiais,
				'request' => $request,
				'rotaIncluir' => 'incluir-categorias-materiais',
				'rotaAlterar' => 'alterar-categorias-materiais'
			);

        return view('categorias_materiais', $data);
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

    		$categorias_materiais_id = $this->salva($request);

	    	return redirect()->route('categorias-materiais', [ 'id' => $categorias_materiais_id ] );

    	}
        $tela = 'incluir';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'categorias de materiais',
				'request' => $request,
				'rotaIncluir' => 'incluir-categorias-materiais',
				'rotaAlterar' => 'alterar-categorias-materiais'
			);

        return view('categorias_materiais', $data);
    }

     /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function alterar(Request $request)
    {

        $categorias_materiais = new CategoriasMateriais();


        $categorias_materiais= $categorias_materiais->where('id', '=', $request->input('id'))->get();

		$metodo = $request->method();
		if ($metodo == 'POST') {

    		$categorias_materiais_id = $this->salva($request);

	    	return redirect()->route('categorias-materiais', [ 'id' => $categorias_materiais_id ] );

    	}
        $tela = 'alterar';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'categorias de materiais',
				'categorias_materiais'=> $categorias_materiais,
				'request' => $request,
				'rotaIncluir' => 'incluir-categorias-materiais',
				'rotaAlterar' => 'alterar-categorias-materiais'
			);

        return view('categorias_materiais', $data);
    }

    public function salva($request) {
        $categorias_materiais = new CategoriasMateriais();

        if($request->input('id')) {
            $categorias_materiais = $categorias_materiais::find($request->input('id'));
        } else {
            $categorias_materiais->nome = $request->input('nome');
        }

        $categorias_materiais->status = $request->input('status');
        $categorias_materiais->save();

        return $categorias_materiais->id;

}
}
