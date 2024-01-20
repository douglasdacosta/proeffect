<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fichastecnicas;
use App\Models\Fichastecnicasitens;
use App\Models\Materiais;

class FichatecnicaController extends Controller
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

        
        $fichatecnicas = new Fichastecnicas();

        if ($id) {
        	$fichatecnicas = $fichatecnicas->where('id', '=', $id);
        }
        if ($codigo) {
        	$fichatecnicas = $fichatecnicas->where('codigo', '=', $codigo);
        }

        if ($request->input('nome') != '') {
        	$fichatecnicas = $fichatecnicas->where('fichatecnica', 'like', '%'.$request->input('nome').'%');
        }

        $fichatecnicas = $fichatecnicas->get();
        $tela = 'pesquisa';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'ficha técnica',
				'fichatecnicas'=> $fichatecnicas,
				'request' => $request,
				'rotaIncluir' => 'incluir-fichatecnica',
				'rotaAlterar' => 'alterar-fichatecnica'
			);

        return view('fichatecnicas', $data);
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
    		$fichatecnica_id = $this->salva($request);

	    	return redirect()->route('fichatecnica', [ 'id' => $fichatecnica_id ] );

    	}
        $tela = 'incluir';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'ficha técnica',
				'request' => $request,
				'materiais' => $this->getAllMateriais(),
				'rotaIncluir' => 'incluir-fichatecnica',
				'rotaAlterar' => 'alterar-fichatecnica'
			);

        return view('fichatecnicas', $data);
    }

     /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function alterar(Request $request)
    {

        $fichatecnicas = new Fichastecnicas();
        

        $fichatecnica= $fichatecnicas->where('id', '=', $request->input('id'))->get();

		$metodo = $request->method();
		if ($metodo == 'POST') {

    		$fichatecnica_id = $this->salva($request);

	    	return redirect()->route('fichatecnica', [ 'id' => $fichatecnica_id ] );

    	}
        $tela = 'alterar';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'ficha técnica',
				'fichatecnicas'=> $fichatecnica,
				'request' => $request,
                'materiais' => $this->getAllMateriais(),
				'rotaIncluir' => 'incluir-fichatecnica',
				'rotaAlterar' => 'alterar-fichatecnica'
			);

        return view('fichatecnicas', $data);
    }

    public function salva($request) {
        $fichatecnicas = new Fichastecnicas();
        $Fichastecnicasitens = new Fichastecnicasitens();

        if($request->input('id')) {
            $fichatecnicas = $fichatecnicas::find($request->input('id'));
            $Fichastecnicasitens::where('fichatecnica_id', '=', $request->input('id'))->delete();
        }
        // dd($request->input());
        $fichatecnicas->ep = $request->input('ep');
        $fichatecnicas->tempo_usinagem =  $request->input('soma_tempo_usinagem');
        $fichatecnicas->tempo_acabamento =  $request->input('soma_tempo_acabamento');
        $fichatecnicas->tempo_montagem =  $request->input('soma_tempo_montagem');
        $fichatecnicas->tempo_montagem_torre =  $request->input('soma_tempo_montagem_torre');
        $fichatecnicas->tempo_inspecao =  $request->input('soma_tempo_inspecao');
        $fichatecnicas->status = $request->input('status') == 'on' ? 1 : 0;
        $fichatecnicas->save();

        $Fichastecnicasitens->fichatecnica_id = $fichatecnicas->id;
        
        $composicoes = json_decode($request->input('composicoes'));
        $composicaoeps = json_decode($composicoes->composicaoep);
        foreach ($composicaoeps as $key => $composicaoep) {
            foreach ($composicaoep as $key => $value) {
                // $value = json_decode($value);
                $i = $value;

                dd(($i));
                // $dados[] =[ $value[0] => $value[1]];
            }
        }

        // dd($dados);
        dd($composicoes);
        // array:5 [▼ // app/Http/Controllers/FichatecnicaController.php:72
//   0 => array:10 [▼
//     0 => "{qtde:34}"
//     1 => "{material_id:PR03 - PSAI Preto}"
//     2 => "{medidax:34}"
//     3 => "{mediday:34}"
//     4 => "{tempo_usinagem:4}"
//     5 => "{tempo_acabamento:3}"
//     6 => "{tempo_montagem:2}"
//     7 => "{tempo_montagem_torre:}"
//     8 => "{tempo_inspecao:2}"
//     9 => "{undefined:×}"
//   ]
        foreach ($composicaoeps as $key => $composicao) {
            $Fichastecnicasitens->materiais_id = $dados['material_id'];
            $Fichastecnicasitens->blank = $dados['blank'];
            $Fichastecnicasitens->qtde_blank = $dados['qtde'];
            $Fichastecnicasitens->medidax = $dados['medidax'];
            $Fichastecnicasitens->mediday = $dados['mediday'];
            $Fichastecnicasitens->tempo_usinagem = $dados['tempo_usinagem'];
            $Fichastecnicasitens->tempo_acabamento = $dados['tempo_acabamento'];
            $Fichastecnicasitens->tempo_montagem = $dados['tempo_montagem'];
            $Fichastecnicasitens->tempo_montagem_torre = $dados['tempo_montagem_torre'];
            $Fichastecnicasitens->tempo_inspecao = $dados['tempo_inspecao'];
        }
        

        $Fichastecnicasitens->save();

        return $fichatecnicas->id;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getAllMateriais() {
        $Materiais = new Materiais();
        return $Materiais->where('status', '=', 1)->get();

    }
}
