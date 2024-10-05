<?php

namespace App\Http\Controllers;

use App\Models\HistoricosEstoque;
use App\Models\HistoricosMateriais;
use App\Models\Materiais;
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

        $lote = !empty($request->input('lote')) ? ($request->input('lote')) : ( !empty($lote) ? $lote : false );

        $where = [];
        $condicao='';
        if ($lote) {
        	$where[] = " A.lote = '$lote' ";
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

        $status_estoque = "A.status_estoque = 'A'";

        if ($request->input('status_estoque') == 'F'){
            $status_estoque  = "A.status_estoque = 'F'";
        }

        $where[] = $status_estoque;

        if(count($where)) {
            $condicao = ' WHERE '.implode(' AND ', $where);
        }

        $estoque = DB::select(DB::raw("SELECT
                                            A.data,
                                            A.id,
                                            A.qtde_chapa_peca,
                                            A.qtde_por_pacote,
                                            B.estoque_minimo,
                                            A.lote,
                                            C.nome_cliente as fornecedor,
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
                                                                C.status = 'A' AND
                                                                C.material_id = A.material_id)  as qtde_total_estoque_material
                                        FROM
                                            estoque A
                                        INNER JOIN
                                            materiais B
                                            ON B.id = A.material_id
                                        INNER JOIN
                                            pessoas C
                                        ON
                                            C.id = A.fornecedor_id
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
                                            and C.status = 'A'
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
                                                                and C.status = 'A'
                                                        "));

            if(isset($dados_estoque[$value->material_id]['gasto_total'])) {
                $dados_estoque[$value->material_id]['gasto_total'] += ($qtde_baixa[0]->qtde_baixa * $value->qtde_chapa_peca);
            } else {
                $dados_estoque[$value->material_id]['gasto_total'] = ($qtde_baixa[0]->qtde_baixa * $value->qtde_chapa_peca);
            }

            $dados_estoque[$value->material_id]['estoque'] = $qtde_total_estoque_material[0]->qtde_total - $dados_estoque[$value->material_id]['gasto_total'];


            $value->estoque_atual = ($value->qtde_chapa_peca * $value->qtde_por_pacote) - $qtde_baixa[0]->qtde_baixa;

            $dados_estoque[$value->material_id]['alerta']=1; //1 = estoque alto
            if($dados_estoque[$value->material_id]['estoque'] <= $value->estoque_minimo) {
                $dados_estoque[$value->material_id]['alerta'] = 0;
            }

            $value->consumo_medio_mensal = $value->consumo_medio_mensal == 0 ? 1 : $value->consumo_medio_mensal;

            $previsao_meses = ($dados_estoque[$value->material_id]['estoque']  - $value->estoque_minimo) / $value->consumo_medio_mensal;
            if($previsao_meses <= 0) {
                $dados_estoque[$value->material_id]['previsao_meses'] = 0;
            } else {

                $previsao_meses = round($previsao_meses, 1);
                $dados_estoque[$value->material_id]['previsao_meses'] = $previsao_meses;
            }

        }
        $array_estoque = [];
        foreach($estoque as $key => $value) {

            $array_estoque[$value->id]['id'] = $value->id;
            $array_estoque[$value->id]['fornecedor'] = implode(' ', array_slice(explode(' ', $value->fornecedor), 0, 1));
            $array_estoque[$value->id]['lote'] = $value->lote;
            $array_estoque[$value->id]['data'] = $value->data;
            $array_estoque[$value->id]['pacote'] = number_format($value->qtde_por_pacote,0, '','.');
            $array_estoque[$value->id]['material'] = $value->material;
            $array_estoque[$value->id]['alerta'] = $dados_estoque[$value->material_id]['alerta'];
            $array_estoque[$value->id]['previsao_meses'] = $dados_estoque[$value->material_id]['previsao_meses'];
            $array_estoque[$value->id]['estoque_comprado'] = number_format($value->qtde_chapa_peca * $value->qtde_por_pacote,0, '','.');
            $array_estoque[$value->id]['estoque_minimo'] = number_format($value->estoque_minimo,0, '','.');
            $array_estoque[$value->id]['estoque_atual'] = number_format($value->estoque_atual,0, '','.');

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

        $historicosMateriais = HistoricosMateriais::where('materiais_id','=', $estoque[0]->material_id)->orderBy('created_at', 'desc')->get();
        $historicos = HistoricosEstoque::where('estoque_id', '=', $request->input('id'))->orderBy('created_at', 'desc')->get();

        $tela = 'alterar';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'estoque',
				'estoque'=> $estoque,
                'historicos' => $historicos,
                'historicosMateriais' =>$historicosMateriais,
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
            $estoque->qtde_chapa_peca = str_replace('.', '',$request->input('qtde_chapa_peca'));
            $estoque->qtde_por_pacote = str_replace('.', '',$request->input('qtde_por_pacote'));
            $estoque->status_estoque = $request->input('status_estoque');
            $estoque->status = $request->input('status');
            $estoque->valor_mo = trim($request->input('valor_mo')) != '' ? DateHelpers::formatFloatValue($request->input('valor_mo')): null;
            $estoque->valor_kg_mo = trim($request->input('valor_kg_mo')) != '' ? DateHelpers::formatFloatValue($request->input('valor_kg_mo')): null;
            $estoque->imposto_mo = trim($request->input('imposto_mo')) != '' ? DateHelpers::formatFloatValue($request->input('imposto_mo')): null;
            $estoque->total_mo = trim($request->input('total_mo')) != '' ? DateHelpers::formatFloatValue($request->input('total_mo')): null;
            $estoque->peso_material = $request->input('peso_material');
            $estoque->peso_material_mo = $request->input('peso_material_mo');
            $estoque->qtde_chapa_peca_mo = str_replace('.', '',$request->input('qtde_chapa_peca_mo'));
            $estoque->qtde_por_pacote_mo = str_replace('.', '',$request->input('qtde_por_pacote_mo'));
            $estoque->observacaoes = $request->input('observacaoes');

            $estoque->save();

            $material = new Materiais();
            $material = $material->find($request->input('material_id'));
            if($material->valor != DateHelpers::formatFloatValue($request->input('valor_unitario'))) {

                $historico = "Valor do material alterado  de ". number_format($material->valor, 2, ',', '') . " para " . $request->input('valor_unitario');
                $material->valor = trim($request->input('valor_unitario')) != '' ? DateHelpers::formatFloatValue($request->input('valor_unitario')): null;
                $material->save();

                if(!empty($historico)) {
                    $historicos = new HistoricosMateriais();
                    $historicos->materiais_id = $material->id;
                    $historicos->historico = $historico;
                    $historicos->status = 'A';
                    $historicos->save();
                }

            }






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
