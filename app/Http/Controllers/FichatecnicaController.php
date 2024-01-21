<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fichastecnicas;
use App\Models\Fichastecnicasitens;
use App\Models\Materiais;
use Illuminate\Support\Facades\DB;

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
        $ep = !empty($request->input('ep')) ? ($request->input('ep')) : ( !empty($ep) ? $ep : false );

        
        $fichatecnicas = new Fichastecnicas();

        if ($id) {
        	$fichatecnicas = $fichatecnicas->where('id', '=', $id);
        }
        if ($ep) {
        	$fichatecnicas = $fichatecnicas->where('ep', '=', $ep);
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
		$metodo = $request->method();

		if ($metodo == 'POST') {

    		$fichatecnica_id = $this->salva($request);

	    	return redirect()->route('fichatecnica', [ 'id' => $fichatecnica_id ] );
    	}

        $fichatecnicas = new Fichastecnicas();
        $fichatecnicasitens = new Fichastecnicasitens();

        $fichatecnica= $fichatecnicas->where('id', '=', $request->input('id'))->get();
        $fichatecnicasitens= $fichatecnicasitens::with('materiais')->where('fichatecnica_id', '=', $request->input('id'))->get();
        $tela = 'alterar';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'ficha técnica',
				'fichatecnicas'=> $fichatecnica,
                'fichatecnicasitens' => $fichatecnicasitens,
				'request' => $request,
                'materiais' => $this->getAllMateriais(),
				'rotaIncluir' => 'incluir-fichatecnica',
				'rotaAlterar' => 'alterar-fichatecnica'
			);

        return view('fichatecnicas', $data);
    }

    public function salva(Request  $request) {

        DB::transaction(function () use ($request) {

            $fichatecnicas = new Fichastecnicas();
            $Fichastecnicasitens = new Fichastecnicasitens();
            if($request->input('id')) {
                $fichatecnicas = $fichatecnicas::find($request->input('id'));
                $Fichastecnicasitens::where('fichatecnica_id', '=', $request->input('id'))->delete();
            }

            $fichatecnicas->ep = $request->input('ep');
            $fichatecnicas->tempo_usinagem =  $request->input('soma_tempo_usinagem');
            $fichatecnicas->tempo_acabamento =  $request->input('soma_tempo_acabamento');
            $fichatecnicas->tempo_montagem =  $request->input('soma_tempo_montagem');
            $fichatecnicas->tempo_montagem_torre =  $request->input('soma_tempo_montagem_torre');
            $fichatecnicas->tempo_inspecao =  $request->input('soma_tempo_inspecao');
            $fichatecnicas->status = $request->input('status') == 'on' ? 1 : 0;
            $fichatecnicas->save();
            
            $composicoes = json_decode($request->input('composicoes'));
            $composicaoeps = json_decode($composicoes->composicaoep);            
            foreach ($composicaoeps as $key1 => $composicaoep) {
                foreach ($composicaoep as $key => $value) {
                    $value_array = json_decode($value, true);
                    $key = array_keys($value_array)[0];                    
                    $dados[$key1][$key] =$value_array[$key];
                }
            }        

            foreach ($dados as $key => $dado) {
                
                $inserts[] =[ 
                    'fichatecnica_id' => $fichatecnicas->id,
                    'materiais_id'=> $dado['material_id'],
                    'blank'=> isset($dado['blank']) ? $dado['blank'] : null ,
                    'qtde_blank'=> $dado['qtde'],
                    'medidax'=> !empty($dado['medidax']) ? $dado['medidax'] : null ,
                    'mediday'=> !empty($dado['mediday']) ? $dado['mediday'] : null ,
                    'tempo_usinagem'=> !empty($dado['tempo_usinagem']) ? $this->trataStringHora($dado['tempo_usinagem']) : null ,
                    'tempo_acabamento'=> !empty($dado['tempo_acabamento']) ? $this->trataStringHora($dado['tempo_acabamento']) : null ,
                    'tempo_montagem'=> !empty($dado['tempo_montagem']) ? $this->trataStringHora($dado['tempo_montagem']) : null ,
                    'tempo_montagem_torre'=> isset($dado['tempo_montagem_torre']) ? $this->trataStringHora($dado['tempo_montagem_torre']) : null ,
                    'tempo_inspecao'=> !empty($dado['tempo_inspecao']) ? $this->trataStringHora($dado['tempo_inspecao']) : null ,
                    'status' => 1,
                ];
            }
            $Fichastecnicasitens->insert($inserts);
            return $fichatecnicas->id;
        });
    }

/**
 * Transforma um numero inteiro em formato de 00:00:00
 */
    function trataStringHora($numeroString) {

        preg_match_all('/[0-9]/', $numeroString, $numerosEncontrados);

        $numerosString = $numerosEncontrados ? implode('', $numerosEncontrados[0]) : '';

        while (strlen($numerosString) < 6) {
            $numerosString = '0' . $numerosString;
        }
        $horaFormatada = substr($numerosString, 0, 2) . ':' . substr($numerosString, 2, 2) . ':' . substr($numerosString, 4, 2);
    
        return $horaFormatada;
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
