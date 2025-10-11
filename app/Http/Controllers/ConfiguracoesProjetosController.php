<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Models\ConfiguracoesProjetos;


class ConfiguracoesProjetosController extends Controller
{
    private $configuracoes_projetos;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');

        $this->configuracoes_projetos = new ConfiguracoesProjetos();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $configuracoes_projetos = new ConfiguracoesProjetos();

        $id = !empty($request->input('id')) ? ($request->input('id')) : ( !empty($id) ? $id : false );

        if ($id) {
            $configuracoes_projetos = $configuracoes_projetos->where('id', '=', $id);
        }


        if ($request->input('nome') != '') {
        	$configuracoes_projetos = $configuracoes_projetos->where('nome', 'like', '%'.$request->input('nome').'%');
        }



        $configuracoes_projetos = $configuracoes_projetos->first();

        $configuracoes_projetos = json_decode($configuracoes_projetos->dados, true);


        $tela = 'configuracoes_projetos';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'configurações',
				'configuracoes'=> $configuracoes_projetos,
        		'request' => $request,
				'rotaIncluir' => 'incluir-configuracoes_projetos',
				'rotaAlterar' => 'alterar-configuracoes_projetos'
			);

        return view('configuracoes_projetos', $data);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function alterar(Request $request)
    {


        $configuracoes_projetos = new ConfiguracoesProjetos();

        $configuracoes_projetos= $configuracoes_projetos->where('id', '=', 1)->get();

		$metodo = $request->method();


		if ($metodo == 'POST') {

    		$configuracoes_projetos_id = $this->salva($request);


	    	return redirect()->route('configuracoes-projetos', [ 'id' => $configuracoes_projetos_id ] );

    	}

        $tela = 'configuracoes_projetos';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'configurações',
				'configuracoes'=> $configuracoes_projetos,
				'request' => $request,
				'rotaIncluir' => 'incluir-configuracoes_projetos',
				'rotaAlterar' => 'alterar-configuracoes_projetos'
			);

        return view('configuracoes-projetos', $data);
    }

    public function salva($request) {
        $configuracoes_projetos = new ConfiguracoesProjetos();

        $array_dados = Arr::except($request->input(), ['id', '_token' ]);

        $json = json_encode($array_dados);

        $configuracoes_projetos = $configuracoes_projetos::find(1);

        $configuracoes_projetos->dados = $json;

        $configuracoes_projetos->save();

        return $configuracoes_projetos->id;

    }

}
