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
        $status_ = !empty($request->input('status')) ? ($request->input('status')) : ( !empty($status) ? $status : false );

        $fichatecnicas = new Fichastecnicas();

        if ($id) {
        	$fichatecnicas = $fichatecnicas->where('id', '=', $id);
        }

        if ($ep) {
        	$fichatecnicas = $fichatecnicas->where('ep', '=', $ep);
        }

        if (!empty($request->input('status'))){
            $fichatecnicas = $fichatecnicas->where('status', '=', $request->input('status'));
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
        $fichatecnicasitens= $fichatecnicasitens::with('materiais')->where('fichatecnica_id', '=', $request->input('id'))->orderByRaw("CASE WHEN blank='' THEN 1 ELSE 0 END ASC")->orderBy('blank','ASC')->get();
        
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
            $fichatecnicas->tempo_inspecao =  $request->input('soma_tempo_inspecao');
            $fichatecnicas->tempo_montagem_torre =  $request->input('soma_tempo_montagem_torre');
            $fichatecnicas->alerta_usinagem1 =  $request->input('alerta_usinagem1');
            $fichatecnicas->alerta_usinagem2 =  $request->input('alerta_usinagem2');
            $fichatecnicas->alerta_usinagem3 =  $request->input('alerta_usinagem3');
            $fichatecnicas->alerta_usinagem4 =  $request->input('alerta_usinagem4');
            $fichatecnicas->alerta_usinagem5 =  $request->input('alerta_usinagem5');
            $fichatecnicas->alerta_acabamento1 =  $request->input('alerta_acabamento1');
            $fichatecnicas->alerta_acabamento2 =  $request->input('alerta_acabamento2');
            $fichatecnicas->alerta_acabamento3 =  $request->input('alerta_acabamento3');
            $fichatecnicas->alerta_acabamento4 =  $request->input('alerta_acabamento4');
            $fichatecnicas->alerta_acabamento5 =  $request->input('alerta_acabamento5');
            $fichatecnicas->alerta_montagem1 =  $request->input('alerta_montagem1');
            $fichatecnicas->alerta_montagem2 =  $request->input('alerta_montagem2');
            $fichatecnicas->alerta_montagem3 =  $request->input('alerta_montagem3');
            $fichatecnicas->alerta_montagem4 =  $request->input('alerta_montagem4');
            $fichatecnicas->alerta_montagem5 =  $request->input('alerta_montagem5');
            $fichatecnicas->alerta_inspecao1 =  $request->input('alerta_inspecao1');
            $fichatecnicas->alerta_inspecao2 =  $request->input('alerta_inspecao2');
            $fichatecnicas->alerta_inspecao3 =  $request->input('alerta_inspecao3');
            $fichatecnicas->alerta_inspecao4 =  $request->input('alerta_inspecao4');
            $fichatecnicas->alerta_inspecao5 =  $request->input('alerta_inspecao5');
            $fichatecnicas->alerta_expedicao1 =  $request->input('alerta_expedicao1');
            $fichatecnicas->alerta_expedicao2 =  $request->input('alerta_expedicao2');
            $fichatecnicas->alerta_expedicao3 =  $request->input('alerta_expedicao3');
            $fichatecnicas->alerta_expedicao4 =  $request->input('alerta_expedicao4');
            $fichatecnicas->alerta_expedicao5 =  $request->input('alerta_expedicao5');            
            $fichatecnicas->status = $request->input('status');
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
                    'status' => 'A',
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
        return $Materiais->where('status', '=', 'A')->get();

    }
}
