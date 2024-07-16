<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Estoque;
use App\Providers\DateHelpers;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\OrcamentosController;
use App\Models\Pessoas;

class EstoqueController extends Controller
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


        $estoque = new Estoque();

        if ($id) {
        	$estoque =$estoque->where('id', '=', $id);
        }


        if (!empty($request->input('status'))){
            $estoque = $estoque = $estoque->where('status', '=', $request->input('status'));
        } else{
            $estoque = $estoque->where('status', '=', 'A');
        }

        $estoque = $estoque->get();
        $tela = 'pesquisa';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'estoque',
				'estoque'=> $estoque,
				'request' => $request,
				'rotaIncluir' => 'incluir-estoque',
				'rotaAlterar' => 'alterar-estoque'
			);

        return view('estoque', $data);
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

    		$estoque_id = $this->salva($request);

	    	return redirect()->route('estoque', [ 'id' => $estoque_id ] );

    	}
        $tela = 'incluir';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'estoque',
				'request' => $request,
                'materiais' => (new OrcamentosController())->getAllMateriais(),
                'fornecedores' => $this->getFornecedores(),
				'rotaIncluir' => 'incluir-estoque',
				'rotaAlterar' => 'alterar-estoque'
			);

        return view('estoque', $data);
    }

     /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function alterar(Request $request)
    {

        $estoque = new Estoque();
        $historico = '';

        $estoque= $estoque->where('id', '=', $request->input('id'))->get();

		$metodo = $request->method();
		if ($metodo == 'POST') {

            $estoque_id = $this->salva($request, $historico);

	    	return redirect()->route('estoque', [ 'id' => $estoque_id ] );

    	}

        $tela = 'alterar';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'estoque',
				'estoque'=> $estoque,
                'materiais' => (new OrcamentosController())->getAllMateriais(),
                'fornecedores' => $this->getFornecedores(),
				'request' => $request,
				'rotaIncluir' => 'incluir-estoque',
				'rotaAlterar' => 'alterar-estoque'
			);

        return view('estoque', $data);
    }

    public function salva($request, $historico = null) {

        $id = DB::transaction(function () use ($request, $historico) {

            $estoque = new Estoque();

            if($request->input('id')) {
                $estoque = $estoque::find($request->input('id'));
            }
            $estoque->material_id = $request->input('material_id');
            $estoque->data = DateHelpers::formatDate_dmY($request->input('data'));
            $estoque->nota_fiscal = $request->input('nota_fiscal');
            $estoque->fornecedor_id = $request->input('fornecedor_id');
            $estoque->lote = $request->input('lote');
            $estoque->valor_unitario = DateHelpers::formatFloatValue($request->input('valor_unitario'));
            $estoque->valor = DateHelpers::formatFloatValue($request->input('valor'));
            $estoque->imposto = DateHelpers::formatFloatValue($request->input('imposto'));
            $estoque->total = DateHelpers::formatFloatValue($request->input('total'));
            $estoque->VD = $request->input('VD');
            $estoque->MO = $request->input('MO');
            $estoque->qtde_chapa_peca = $request->input('qtde_chapa_peca');
            $estoque->qtde_por_pacote = $request->input('qtde_por_pacote');
            $estoque->status = $request->input('status');
            $estoque->save();

            return $estoque->id;
        });

        return $id;

    }

    public function getFornecedores() : object{
        $fornecedores = new Pessoas();
        $fornecedores = $fornecedores->where('fornecedor','=', '1')->get();
        return $fornecedores;
    }

}
