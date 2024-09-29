<?php

namespace App\Http\Controllers;

use App\Models\HistoricosEstoque;
use Illuminate\Http\Request;
use App\Models\Estoque;
use App\Providers\DateHelpers;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\OrcamentosController;
use App\Models\Pessoas;
use PhpParser\Node\Expr\Cast\Object_;

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

        $where = [];
        $condicao='';
        if ($id) {
        	$where[] = " A.id = $id ";
        }

        if (!empty($request->input('status'))){
            $status = $request->input('status');
            $where[] = "A.status='$status'";
        } else{
            $where[] = "A.status='A'";
        }

        $data_inicio = !empty($request->input('data')) ? (DateHelpers::formatDate_dmY($request->input('data'))) : '';
        $data_fim = !empty($request->input('data_fim')) ? (DateHelpers::formatDate_dmY($request->input('data_fim'))) : '';

        if (!empty($data_inicio) && !empty($data_fim)){

            $where[] = "A.data between '".$data_inicio."' and '".$data_fim."'";
        }
        if (empty($data_inicio) && !empty($data_fim)){
            $where[] = "A.data <= '$data_fim'";

        }
        if (!empty($data_inicio) && empty($data_fim)){
            $where[] = "A.data >= '$data_inicio'" ;
        }

        if (!empty($request->input('material_id'))){
            $where[] = "A.material_id = " . $request->input('material_id');
        }

        if ($request->input('status_estoque') == '1'){
            $where[] = "((A.qtde_chapa_peca * A.qtde_por_pacote) - ((select
                                                    count(1)
                                                from
                                                    lote_estoque_baixados X
                                                where
                                                    X.estoque_id = A.id) * A.qtde_chapa_peca)) > 0";
        }
        if ($request->input('status_estoque') == '0'){
            $where[] = "((A.qtde_chapa_peca * A.qtde_por_pacote) - ((select
                                                    count(1)
                                                from
                                                    lote_estoque_baixados X
                                                where
                                                    X.estoque_id = A.id) * A.qtde_chapa_peca)) <= 0";
        }

        if(count($where)) {
            $condicao = ' WHERE '.implode(' AND ', $where);
        }

        $estoque = DB::select(DB::raw("SELECT
                                            A.data,
                                            A.id,
                                            A.qtde_chapa_peca,
                                            A.qtde_por_pacote,
                                            B.estoque_minimo,
                                            (A.qtde_chapa_peca * A.qtde_por_pacote) - ((select
                                                    count(1)
                                                from
                                                    lote_estoque_baixados X
                                                where
                                                    X.estoque_id = A.id) * A.qtde_chapa_peca) as estoque_atual,
                                            B.material,
                                            A.material_id,
                                            B.consumo_medio_mensal,
                                            (SELECT
                                                                sum((qtde_chapa_peca * qtde_por_pacote)) as qtde_total
                                                            FROM
                                                                estoque C
                                                            INNER JOIN
                                                                materiais B
                                                                ON B.id = C.material_id
                                                            WHERE
                                                                C.material_id = A.material_id)  as qtde_total_estoque_material
                                        FROM
                                            estoque A
                                        INNER JOIN
                                            materiais B
                                            ON B.id = A.material_id
                                        $condicao
                                        ORDER BY
                                            A.data DESC
                                    "));

        $dados_estoque = [];
        foreach ($estoque as $key => $value) {
            $qtde_baixa = DB::select(DB::raw("SELECT
                                            count(1) as qtde_baixa
                                        FROM
                                            lote_estoque_baixados A
                                        INNER JOIN
                                            estoque C
                                        on
                                            C.id = A.estoque_id
                                        INNER JOIN
                                            materiais B
                                            ON B.id = C.material_id
                                        WHERE
                                            A.estoque_id = $value->id
                                    "));

            $qtde_total_estoque_material = DB::select(DB::raw("SELECT
                                                                sum((qtde_chapa_peca * qtde_por_pacote)) as qtde_total
                                                            FROM
                                                                estoque C
                                                            INNER JOIN
                                                                materiais B
                                                                ON B.id = C.material_id
                                                            WHERE
                                                                C.material_id = $value->material_id
                                                        "));

            if(isset($dados_estoque[$value->material_id]['gasto_total'])) {
                $dados_estoque[$value->material_id]['gasto_total'] += ($qtde_baixa[0]->qtde_baixa * $value->qtde_chapa_peca);
            } else {
                $dados_estoque[$value->material_id]['gasto_total'] = ($qtde_baixa[0]->qtde_baixa * $value->qtde_chapa_peca);
            }

            $dados_estoque[$value->material_id]['estoque'] = $qtde_total_estoque_material[0]->qtde_total - $dados_estoque[$value->material_id]['gasto_total'];

            $dados_estoque[$value->material_id]['alerta']=1; //1 = estoque alto
            if($dados_estoque[$value->material_id]['estoque'] <= $value->estoque_minimo) {
                $dados_estoque[$value->material_id]['alerta'] = 0;
            }

            $value->consumo_medio_mensal = $value->consumo_medio_mensal == 0 ? 1 : $value->consumo_medio_mensal;
            $dados_estoque[$value->material_id]['previsao_meses'] = $dados_estoque[$value->material_id]['estoque'] / $value->consumo_medio_mensal;

        }
        $array_estoque = [];
        foreach($estoque as $key => $value) {

            $array_estoque[$value->id]['id'] = $value->id;
            $array_estoque[$value->id]['data'] = $value->data;
            $array_estoque[$value->id]['material'] = $value->material;
            $array_estoque[$value->id]['alerta'] = $dados_estoque[$value->material_id]['alerta'];
            $array_estoque[$value->id]['previsao_meses'] = $dados_estoque[$value->material_id]['previsao_meses'];
            $array_estoque[$value->id]['estoque_comprado'] = $value->qtde_chapa_peca * $value->qtde_por_pacote;
            $array_estoque[$value->id]['estoque_minimo'] = $value->estoque_minimo;
            $array_estoque[$value->id]['estoque_atual'] = $dados_estoque[$value->material_id]['estoque'];

        }

        $tela = 'pesquisa';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'estoque',
				'array_estoque'=> $array_estoque,
                'materiais' => (new OrcamentosController())->getAllMateriais(),
                'fornecedores' => $this->getFornecedores(),
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

        $lote = $this->geraRandomico();
        $existeLote = true;
        $contador = 0;
        $limit_tentativa = 20000;
        while($existeLote) {
            $existeLote = Estoque::where('lote', $lote)->exists();

            if(!$existeLote) {
                break;
            }

            info('Tentando encontrar um novo lote não usado');

            $lote = $this->geraRandomico();

            if($contador > $limit_tentativa) {
                info('Lote do estoque não suporta range na criação de lote randomico, verifique os parametros da .env');
                return redirect()->route('estoque');
            }

            $contador++;
        }

        $estoque = [];
        $estoque[0] = new Estoque();
        $estoque[0]->lote = $lote;
        $estoque[0]->data = DateHelpers::formatDate_dmY(date('Y-m-d'));

        $tela = 'incluir';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'estoque',
                'estoque' => $estoque,
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


        $estoque= $estoque->where('id', '=', $request->input('id'))->get();

		$metodo = $request->method();
		if ($metodo == 'POST') {

            $estoque_id = $this->salva($request);

	    	return redirect()->route('estoque', [ 'id' => $estoque_id ] );

    	}

        $historicos = HistoricosEstoque::where('estoque_id', '=', $request->input('id'))->get();

        $tela = 'alterar';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'estoque',
				'estoque'=> $estoque,
                'historicos' => $historicos,
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
            $estoque->valor_unitario = trim($request->input('valor_unitario')) != '' ? DateHelpers::formatFloatValue($request->input('valor_unitario')): null;
            $estoque->valor = trim($request->input('valor')) != '' ? DateHelpers::formatFloatValue($request->input('valor')): null;
            $estoque->imposto = trim($request->input('imposto')) != '' ? DateHelpers::formatFloatValue($request->input('imposto')): null;
            $estoque->total = trim($request->input('total')) != '' ? DateHelpers::formatFloatValue($request->input('total')): null;
            $estoque->VD = (!empty($request->input('VD')) && $request->input('VD') ==1 ) ? 1 : 0;
            $estoque->MO = (!empty($request->input('MO')) && $request->input('MO') ==1 ) ? 1 : 0;
            $estoque->qtde_chapa_peca = $request->input('qtde_chapa_peca');
            $estoque->qtde_por_pacote = $request->input('qtde_por_pacote');
            $estoque->status = $request->input('status');
            $estoque->valor_mo = trim($request->input('valor_mo')) != '' ? DateHelpers::formatFloatValue($request->input('valor_mo')): null;
            $estoque->save();

            return $estoque->id;
        });

        return $id;

    }

    public function getFornecedores($array = false){
        $fornecedores = new Pessoas();
        $fornecedores = $fornecedores->where('fornecedor','=', '1')->get();
        if($array) {
            $fornecedores = $fornecedores->toArray();
        }
        return $fornecedores;
    }

    function geraRandomico(){
        $letras = '';
        $numeros = '';

        $qtde_letras = env('QTDE_LETRAS', 2);
        $qtde_numeros = env('QTDE_NUMEROS', 2);
        // Gerar letras aleatórias
        for ($i = 0; $i < $qtde_letras; $i++) {
            $letras .= chr(rand(65, 90)); // Letras maiúsculas (A-Z)
        }

        // Gerar números aleatórios
        for ($i = 0; $i < $qtde_numeros; $i++) {
            $numeros .= rand(0, 9); // Números (0-9)
        }

        // Combinar letras e números
        return $letras . $numeros;
    }
}
