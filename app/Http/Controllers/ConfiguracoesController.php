<?php

namespace App\Http\Controllers;

use App\Models\CategoriasMateriais;
use App\Models\Configuracoes;
use App\Models\Materiais;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Http\Controllers\RelatoriosController;
use App\Models\Fichastecnicas;

class ConfiguracoesController extends Controller
{
    private $configuracoes;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');

        $this->configuracoes = new Configuracoes();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $configuracoes = new Configuracoes();

        $id = !empty($request->input('id')) ? ($request->input('id')) : ( !empty($id) ? $id : false );

        if ($id) {
            $configuracoes = $configuracoes->where('id', '=', $id);
        }


        if ($request->input('nome') != '') {
        	$configuracoes = $configuracoes->where('nome', 'like', '%'.$request->input('nome').'%');
        }

        $categorias = New CategoriasMateriais();
        $categorias = $categorias->get();

        $configuracoes = $configuracoes->first();

        $configuracoes = json_decode($configuracoes->dados, true);


        $tela = 'configuracoes';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'configurações',
				'configuracoes'=> $configuracoes,
                'categorias'=> $categorias,
				'request' => $request,
				'rotaIncluir' => 'incluir-configuracoes',
				'rotaAlterar' => 'alterar-configuracoes'
			);

        return view('configuracoes', $data);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function alterar(Request $request)
    {


        $configuracoes = new Configuracoes();

        $configuracoes= $configuracoes->where('id', '=', 1)->get();

		$metodo = $request->method();


		if ($metodo == 'POST') {

    		$configuracoes_id = $this->salva($request);

            if($request->input('tipo_atualizacao') == '1') {

                $this->ProcessaAtualizacoesConfiguracoes();
                $this->ProcessaAtualizacoesTemposConfiguracoes();

            } else {
                $configuracoes = [];
            }

	    	return redirect()->route('configuracoes', [ 'id' => $configuracoes_id ] );

    	}

        $categorias = New CategoriasMateriais();
        $categorias = $categorias->get();

        $tela = 'configuracoes';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'configurações',
				'configuracoes'=> $configuracoes,
                'categorias'=> $categorias,
				'request' => $request,
				'rotaIncluir' => 'incluir-configuracoes',
				'rotaAlterar' => 'alterar-configuracoes'
			);

        return view('configuracoes', $data);
    }

    public function salva($request) {
        $configuracoes = new Configuracoes();

        $array_dados = Arr::except($request->input(), ['id', '_token', 'tipo_atualizacao']);

        $json = json_encode($array_dados);

        $configuracoes = $configuracoes::find(1);

        $configuracoes->dados = $json;

        $configuracoes->save();

        return $configuracoes->id;

    }

    /**
     * Processa as atualizações de configurações e atualiza os dados dos materiais
     *
     * @return void
     */
    public function ProcessaAtualizacoesConfiguracoes()
    {
        $configuracoes = new Configuracoes();
        $configuracoes= $configuracoes->first();
        $configuracoes = json_decode($configuracoes->dados, true);

        $relatorioController = new RelatoriosController();

        $array_materiais = [];

        foreach ($configuracoes as $key => $meses) {

            if($key == 'consumo_medio_mensal') {
                //calcula data de hoje menos a quantidade de meses
                $data_calculo = \Carbon\Carbon::now()->subMonths($meses)->format('d-m-Y');

                $data_hoje = \Carbon\Carbon::now()->format('d-m-Y');
                $retorno = $relatorioController->GeraRelatorioPrevisaoMaterial($data_calculo, $data_hoje);



                foreach ($retorno['materiais'] as $material) {

                    if(!isset($material['consumido']) || $material['consumido'] == 0 || $meses == 0) {
                        continue;
                    }

                    $media_atual = ceil($material['consumido'] / $meses);

                    info($material['material'] . ' - ' . $media_atual);

                    $controllerMateriais = new MateriaisController();
                    $rerorno = $controllerMateriais->atualizaConsumoMedioMensal($material['material_id'], $media_atual);

                    $array_materiais[$material['material_id']] = [
                        'material' => $material['material'],
                        'media_atual' => $media_atual
                    ];
                }
            }

        }

        foreach ($array_materiais as $key => $value) {

            $Materiais = new Materiais();
            $materiais = $Materiais->where('id', '=', $key)->first();

            $categoria_id = $materiais->categoria_id;

            $estoque_minimo_atualisar = $value['media_atual'] * $configuracoes['categoria_' . $categoria_id];

            $controllerMateriais = new MateriaisController();

            $rerorno = $controllerMateriais->atualizaEstoqueMinimo($key, $estoque_minimo_atualisar);

        }
    }

    public function ProcessaAtualizacoesTemposConfiguracoes()
    {
        $configuracoes = new Configuracoes();
        $configuracoes= $configuracoes->first();
        $configuracoes = json_decode($configuracoes->dados, true);

        foreach ($configuracoes as $key => $tempo_percentual) {

            if($key == 'percentual_usinagem_acabamento') {

                //buscar dados de usinagem
                $ficha_tecnica = new Fichastecnicas();
                $ficha_tecnica = $ficha_tecnica->where('status', '=', 'A')->get();

                foreach ($ficha_tecnica as $fichat) {

                    //$fichat->tempo_usinagem = 00:00:30; // Exemplo de 00:00:00 tempo de usinagem, converter para segundos
                    $tempo_usinagem = explode(':', $fichat->tempo_usinagem);
                    $tempo_usinagem = ($tempo_usinagem[0] * 60) + ($tempo_usinagem[1] * 60) + $tempo_usinagem[2];
                    //calculo de tempo, acabamento  70% do tempo de usinagem

                    $tempo_acabamento_em_segundos = ($tempo_usinagem * $tempo_percentual/100);

                    $fichat->tempo_acabamento = gmdate('H:i:s', $tempo_acabamento_em_segundos);
                    info('Antes ------');

                    info($fichat->tempo_usinagem);
                    info($fichat->tempo_acabamento);
                    info('após ------');

                    $fichat->save();
                }

            }

        }


    }
}
