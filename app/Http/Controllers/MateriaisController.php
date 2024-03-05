<?php

namespace App\Http\Controllers;

use App\Models\HistoricosMateriais;
use Illuminate\Http\Request;
use App\Models\Materiais;
use App\Providers\DateHelpers;
use Illuminate\Support\Facades\DB;

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
        	$materiais =$materiais->where('id', '=', $id);
        }
        if ($codigo) {
        	$materiais = $materiais->where('codigo', '=', $codigo);
        }

        if (!empty($request->input('nome'))) {
        	$materiais = $materiais->where('material', 'like', '%'.$request->input('nome').'%');
        }

        if (!empty($request->input('status'))){
            $materiais = $materiais = $materiais->where('status', '=', $request->input('status'));
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
        $historico = '';

        $material= $materiais->where('id', '=', $request->input('id'))->get();

		$metodo = $request->method();
		if ($metodo == 'POST') {

            if($material[0]->valor != DateHelpers::formatFloatValue($request->input('valor'))) {
                DateHelpers::formatDate_dmY($request->input("data_entrega"));
                $historico = "Valor do material alterado  de ". number_format($material[0]->valor, 2, ',', '') . " para " . $request->input('valor');

            }
    		$material_id = $this->salva($request, $historico);

	    	return redirect()->route('materiais', [ 'id' => $material_id ] );

    	}

        $historicos = HistoricosMateriais::where('materiais_id','=', $material[0]->id)->get();

        $tela = 'alterar';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'materiais',
				'materiais'=> $material,
				'request' => $request,
                'historicos'=> $historicos,
				'rotaIncluir' => 'incluir-materiais',
				'rotaAlterar' => 'alterar-materiais'
			);

        return view('materiais', $data);
    }

    public function salva($request, $historico = null) {

        $id = DB::transaction(function () use ($request, $historico) {

            $materiais = new Materiais();
            $tempo_torre = '00:00:00';
            if(!empty($request->input('tempo_montagem_torre'))) {
                $tempo_torre = '00:'.$request->input('tempo_montagem_torre');
            }

            if($request->input('id')) {
                $materiais = $materiais::find($request->input('id'));
            }
            $materiais->codigo = $request->input('codigo');
            $materiais->material = $request->input('material');
            $materiais->espessura = $request->input('espessura');
            $materiais->unidadex = $request->input('unidadex');
            $materiais->unidadey = $request->input('unidadey');
            $materiais->peca_padrao = $request->input('peca_padrao');
            $materiais->tempo_montagem_torre = $tempo_torre;
            $materiais->valor = DateHelpers::formatFloatValue($request->input('valor'));
            $materiais->status = $request->input('status');
            $materiais->save();

            if(!empty($historico)) {
                $historicos = new HistoricosMateriais();
                $historicos->materiais_id = $materiais->id;
                $historicos->historico = $historico;
                $historicos->status = 'A';
                $historicos->save();
            }

        return $materiais->id;
    });

    return $id;

}
}
