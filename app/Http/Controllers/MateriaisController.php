<?php

namespace App\Http\Controllers;

use App\Models\CategoriasMateriais;
use App\Models\HistoricosMateriais;
use App\Models\MateriaisHistoricosValores;
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
        } else{
            $materiais = $materiais->where('status', '=', 'A');
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
                'categorias' => (new CategoriasMateriais())->where('status', '=', 'A')->get(),
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
                $historico = "Valor do material alterado pelo lote de ". number_format($material[0]->valor, 2, ',', '') . " para " . $request->input('valor');

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
                'categorias' => (new CategoriasMateriais())->where('status', '=', 'A')->get(),
                'historicos'=> $historicos,
				'rotaIncluir' => 'incluir-materiais',
				'rotaAlterar' => 'alterar-materiais'
			);

        return view('materiais', $data);
    }

    public function salva($request, $historico = null) {

        $id = DB::transaction(function () use ($request, $historico) {
            $salva_historico = false;
            $materiais = new Materiais();
            $tempo_torre = '00:00:00';
            if(!empty($request->input('tempo_montagem_torre'))) {
                $tempo_torre = '00:'.$request->input('tempo_montagem_torre');
            }

            if($request->input('id')) {
                $materiais = $materiais::find($request->input('id'));

                if($materiais->valor != DateHelpers::formatFloatValue($request->input('valor'))) {
                    $salva_historico = true;
                }

            }

            $materiais->codigo = $request->input('codigo');
            $materiais->material = $request->input('material');
            $materiais->espessura = $request->input('espessura');
            $materiais->unidadex = $request->input('unidadex');
            $materiais->unidadey = $request->input('unidadey');
            $materiais->peca_padrao = $request->input('peca_padrao');
            $materiais->estoque_minimo = $request->input('estoque_minimo');
            $materiais->consumo_medio_mensal = $request->input('consumo_medio_mensal');
            $materiais->categoria_id = $request->input('categoria');
            $materiais->peso = $request->input('peso');
            $materiais->tempo_montagem_torre = $tempo_torre;
            $materiais->valor = DateHelpers::formatFloatValue($request->input('valor'));
            $materiais->status = $request->input('status');
            $materiais->save();

            if(!empty($historico)) {
                $MateriaisHistoricosValores = new MateriaisHistoricosValores();
                $MateriaisHistoricosValores->materiais_id = $materiais->id;
                $MateriaisHistoricosValores->valor = DateHelpers::formatFloatValue($request->input('valor'));
                $MateriaisHistoricosValores->save();

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


    public function atualizaConsumoMedioMensal($material_id, $consumo_medio) {

        $materiais = new Materiais();
        $materiais = $materiais->where('id', '=', $material_id)->get();

        if($materiais->isEmpty()) {
            return response()->json(['error' => true, 'mensagem' => 'Material não encontrado'], 404);
        }

        $material = $materiais[0];
        $material->consumo_medio_mensal = $consumo_medio;
        $material->save();

        return response()->json(['error' => false, 'mensagem' => 'Consumo médio mensal atualizado com sucesso']);
    }

        public function atualizaEstoqueMinimo($material_id, $estoque_minimo) {

        $materiais = new Materiais();
        $materiais = $materiais->where('id', '=', $material_id)->get();

        if($materiais->isEmpty()) {
            return response()->json(['error' => true, 'mensagem' => 'Material não encontrado'], 404);
        }

        $material = $materiais[0];
        $material->estoque_minimo = $estoque_minimo;
        $material->save();

        return response()->json(['error' => false, 'mensagem' => 'Estoque mínimo atualizado com sucesso']);
    }
}
