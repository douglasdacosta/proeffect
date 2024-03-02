<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fichastecnicas;
use App\Models\Fichastecnicasitens;
use App\Models\Materiais;
use Illuminate\Support\Facades\DB;

class OrcamentosController extends Controller
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
                'nome_tela' => 'orçamentos',
				'fichatecnicas'=> $fichatecnicas,
				'request' => $request,
				'rotaIncluir' => 'incluir-fichatecnica',
				'rotaAlterar' => 'alterar-orcamentos'
			);

        return view('orcamentos', $data);
    }

     /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function incluir(Request $request)
    {

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
        $pedidos = new PedidosController();
        $consumoMateriais = new ConsumoMateriaisController();

        $fichatecnica= $fichatecnicas->where('id', '=', $request->input('id'))->get();

        $fichatecnicasitens= $fichatecnicasitens::with('tabelaMateriais')->where('fichatecnica_id', '=', $request->input('id'))->orderByRaw("CASE WHEN blank='' THEN 1 ELSE 0 END ASC")->orderBy('blank','ASC')->get();

        $tempo_fresa_total = '00:00:00';

        foreach ($fichatecnicasitens as $key => $fichatecnicasitem) {
            $tempo_usinagem = $fichatecnicasitem->tempo_usinagem;
            $tempo_usinagem = $pedidos->multiplyTimeByInteger($tempo_usinagem,$fichatecnicasitem->qtde_blank);
            $tempo_fresa_total = $pedidos::somarHoras($tempo_fresa_total, $tempo_usinagem);
        }

        foreach ($fichatecnicasitens as $key => $fichatecnicasitem) {
           
            $tempo_usinagem = $fichatecnicasitem->tempo_usinagem;
            $tempo_usinagem = $pedidos->multiplyTimeByInteger($tempo_usinagem,$fichatecnicasitem->qtde_blank);

            $percentuais[$key]['percentual']=round($pedidos::calcularPorcentagemEntreMinutos($tempo_usinagem, $tempo_fresa_total));


            $pecas = [
                'width' => $fichatecnicasitem->medidax + 2,
                'height'=> $fichatecnicasitem->mediday + 10,
            ];

            $chapa = [
                'sheetWidth' => $fichatecnicasitem->tabelaMateriais->unidadex - 20,
                'sheetHeight'=> $fichatecnicasitem->tabelaMateriais->unidadey - 20 
            ];

            if($fichatecnicasitem->tabelaMateriais->peca_padrao == 2){

                $blank_por_chapa = $consumoMateriais->calculaPecas($pecas, $chapa);
            } else {
                
                $blank_por_chapa = $fichatecnicasitem->qtde_blank;
            }

            $percentuais[$key]['blank_por_chapa'] = $blank_por_chapa;
        }

        $tela = 'alterar';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'ficha técnica',
				'fichatecnicas'=> $fichatecnica,
                'fichatecnicasitens' => $fichatecnicasitens,
				'request' => $request,
                'materiais' => $this->getAllMateriais(),
				'rotaIncluir' => '',
                'tempo_fresa_total' => $tempo_fresa_total,
				'rotaAlterar' => 'alterar-orcamentos',
                'percentuais' => $percentuais,
			);

        return view('orcamentos', $data);
    }

    public function salva(Request  $request) {

        DB::transaction(function () use ($request) {


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
