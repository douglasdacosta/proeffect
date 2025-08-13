<?php

namespace App\Http\Controllers;

use App\Models\HistoricosEstoque;
use App\Models\HistoricosMateriais;
use App\Models\LoteEstoqueBaixados;
use App\Models\Materiais;
use App\Models\MateriaisHistoricosValores;
use Illuminate\Http\Request;
use App\Models\Estoque;
use App\Providers\DateHelpers;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\OrcamentosController;
use App\Models\CategoriasMateriais;
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
        	$where_sem_estoque[] = " A.lote = '$lote' ";
        }

        if (!empty($request->input('status'))){
            $status = $request->input('status');
            $where[] = "A.status='$status'";
            $where_sem_estoque[] = "A.status='$status'";
        } else{
            $where[] = "A.status='A'";
            $where_sem_estoque[] = "A.status='A'";
        }

        $data_inicio = !empty($request->input('data')) ? (DateHelpers::formatDate_dmY($request->input('data'))) : '';
        $data_fim = !empty($request->input('data_fim')) ? (DateHelpers::formatDate_dmY($request->input('data_fim'))) : '';

        if (!empty($data_inicio) && !empty($data_fim)){

            $where[] = "A.data between '".$data_inicio."' and '".$data_fim."'";
            $where_sem_estoque[] = "A.data between '".$data_inicio."' and '".$data_fim."'";
        }
        if (empty($data_inicio) && !empty($data_fim)){
            $where[] = "A.data <= '$data_fim'";
            $where_sem_estoque[] = "A.data <= '$data_fim'";

        }
        if (!empty($data_inicio) && empty($data_fim)){
            $where[] = "A.data >= '$data_inicio'" ;
            $where_sem_estoque[] = "A.data >= '$data_inicio'";
        }

        if (!empty($request->input('material_id'))){
            $where[] = "A.material_id = " . $request->input('material_id');
            $where_sem_estoque[] = "A.material_id = " . $request->input('material_id');
        }

        if (!empty($request->input('categoria_id'))){
            $categoria = $request->input('categoria_id');
            $where[] = "B.categoria_id = $categoria";
            $where_sem_estoque[] = "B.categoria_id = $categoria";
        }

        $where_sem_estoque[] = "A.status_estoque = 'F'";

        $status_estoque = "A.status_estoque = 'A'";

        if ($request->input('status_estoque') == 'F'){
            $status_estoque  = "A.status_estoque = 'F'";
        }

        $where[] = $status_estoque;

        if(count($where)) {
            $condicao = ' WHERE '.implode(' AND ', $where);
        }

        if(count($where_sem_estoque)) {
            $condicao_sem_estoque = ' WHERE '.implode(' AND ', $where_sem_estoque);
        }

        $estoque = DB::select(DB::raw("SELECT
                                            A.data,
                                            A.id,
                                            A.qtde_chapa_peca,
                                            A.qtde_chapa_peca_mo,
                                            A.qtde_por_pacote,
                                            A.qtde_por_pacote_mo,
                                            B.estoque_minimo,
                                            D.nome as categoria,
                                            A.lote,
                                            A.inventario,
                                            C.nome_cliente as fornecedor,
                                            E.nome_cliente as loja,
                                            ((A.qtde_chapa_peca_mo * A.qtde_por_pacote_mo) + (A.qtde_chapa_peca * A.qtde_por_pacote)) - ((select
                                                    count(1)
                                                from
                                                    lote_estoque_baixados X
                                                where
                                                    X.estoque_id = A.id) * (A.qtde_chapa_peca + A.qtde_chapa_peca_mo)) as estoque_atual,
                                            ((A.qtde_por_pacote_mo) + (A.qtde_por_pacote)) - ((select
                                                    count(1)
                                                from
                                                    lote_estoque_baixados X
                                                where
                                                    X.estoque_id = A.id)) as estoque_pacote_atual,
                                            B.material,
                                            A.material_id,
                                            B.consumo_medio_mensal,
                                            A.alerta_baixa_errada
                                        FROM
                                            estoque A
                                        INNER JOIN
                                            materiais B
                                            ON B.id = A.material_id
                                        INNER JOIN
                                            pessoas C
                                        ON
                                            C.id = A.fornecedor_id
                                        INNER JOIN
                                            categorias_materiais D
                                        ON
                                            D.id = B.categoria_id
                                        LEFT JOIN
                                            pessoas E
                                        ON
                                            E.id = A.loja_id
                                        $condicao
                                        ORDER BY
                                            A.data DESC
                                    "));

        $estoque_atual_somado=$dados_estoque = [];

        $material_id = [];
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
                                                                sum((qtde_chapa_peca * qtde_por_pacote)+(qtde_chapa_peca_mo * qtde_por_pacote_mo)) as qtde_total
                                                            FROM
                                                                estoque C
                                                            INNER JOIN
                                                                materiais B
                                                                ON B.id = C.material_id
                                                            WHERE
                                                                C.material_id = $value->material_id
                                                                and C.lote = '$value->lote'
                                                                and C.status = 'A'
                                                        "));
            if(isset($dados_estoque[$value->material_id]['gasto_total'][$value->lote])) {
                $dados_estoque[$value->material_id]['gasto_total'][$value->lote] += ($qtde_baixa[0]->qtde_baixa * ($value->qtde_chapa_peca));
            } else {
                $dados_estoque[$value->material_id]['gasto_total'][$value->lote] = ($qtde_baixa[0]->qtde_baixa * ($value->qtde_chapa_peca));
            }

            $dados_estoque[$value->material_id]['estoque'] = $qtde_total_estoque_material[0]->qtde_total - $dados_estoque[$value->material_id]['gasto_total'][$value->lote];

            $value->estoque_atual = ($value->qtde_chapa_peca * $value->qtde_por_pacote) + ($value->qtde_chapa_peca_mo * $value->qtde_por_pacote_mo) - $dados_estoque[$value->material_id]['gasto_total'][$value->lote];

            if(isset($estoque_atual_somado[$value->material_id]['somado'])){
                $estoque_atual_somado[$value->material_id]['somado'] +=$dados_estoque[$value->material_id]['estoque'];
            } else {
                $estoque_atual_somado[$value->material_id]['somado'] =$dados_estoque[$value->material_id]['estoque'];
            }

            $dados_estoque[$value->material_id]['alerta']=1; //1 = estoque alto
            if($estoque_atual_somado[$value->material_id]['somado'] <= $value->estoque_minimo) {
                $dados_estoque[$value->material_id]['alerta'] = 0;
            }

            $value->consumo_medio_mensal = $value->consumo_medio_mensal == 0 ? 1 : $value->consumo_medio_mensal;
            $dados_estoque[$value->material_id]['consumo_medio_mensal'] =  $value->consumo_medio_mensal;
            $previsao_meses = ($dados_estoque[$value->material_id]['estoque']) / $value->consumo_medio_mensal;
            if($previsao_meses <= 0) {
                $dados_estoque[$value->id]['previsao_meses'] = 0;
            } else {

                $previsao_meses = round($previsao_meses, 1);
                $dados_estoque[$value->id]['previsao_meses'] = $previsao_meses;
            }

            $material_id[] = $value->material_id;

        }

        $array_estoque = $array_soma_total_estoque =[];

        foreach($estoque as $key => $value) {

            $pacote = $value->qtde_por_pacote + $value->qtde_por_pacote_mo;
            $estoque_comprado = ($value->qtde_chapa_peca * $value->qtde_por_pacote) + ($value->qtde_chapa_peca_mo * $value->qtde_por_pacote_mo);

            $array_estoque[$value->id]['id'] = $value->id;
            $array_estoque[$value->id]['fornecedor'] = implode(' ', array_slice(explode(' ', $value->fornecedor), 0, 1));
            $array_estoque[$value->id]['loja'] = implode(' ', array_slice(explode(' ', $value->loja), 0, 1));
            $array_estoque[$value->id]['loja'] = implode(' ', array_slice(explode(' ', $value->loja), 0, 1));
            $array_estoque[$value->id]['lote'] = $value->lote;
            $array_estoque[$value->id]['data'] = $value->data;
            $array_estoque[$value->id]['pacote'] = number_format($value->estoque_pacote_atual,0, '','.');

            $array_estoque[$value->id]['inventario'] = $value->inventario;
            $array_estoque[$value->id]['categoria'] = $value->categoria;
            $array_estoque[$value->id]['material'] = $value->material;
            $array_estoque[$value->id]['alerta'] = $dados_estoque[$value->material_id]['alerta'];
            $array_estoque[$value->id]['previsao_meses'] = $dados_estoque[$value->id]['previsao_meses'];
            $array_estoque[$value->id]['consumo_medio_mensal'] = $dados_estoque[$value->material_id]['consumo_medio_mensal'];
            $array_estoque[$value->id]['estoque_comprado'] = number_format($estoque_comprado,0, '','.');
            $array_estoque[$value->id]['estoque_minimo'] = number_format($value->estoque_minimo,0, '','.');
            $array_estoque[$value->id]['estoque_atual'] = number_format($value->estoque_atual,0, '','.');
            $array_totais[$value->material][] = $value->estoque_atual;
            $array_estoque[$value->id]['alerta_baixa_errada'] = $value->alerta_baixa_errada;
            $array_estoque[$value->id]['alerta_estoque_zerado'] = ($value->estoque_atual == 0 && $value->estoque_minimo > 0) ? 1 : 0;


        }

        $estoque_material_somado = [];
        foreach($estoque as $key => $value) {

            $estoque_material_somado[$value->material]['estoque_total'] = array_sum($array_totais[$value->material]);

        }

        $condicao_material = "";
        if(!empty($material_id)) {
            $condicao_material = " and A.material_id not in (".implode(',',$material_id) . ")";
        }

        $estoque_finalizado = DB::select(DB::raw("SELECT
                                            A.data,
                                            A.id,
                                            A.qtde_chapa_peca,
                                            A.qtde_chapa_peca_mo,
                                            A.qtde_por_pacote,
                                            A.qtde_por_pacote_mo,
                                            B.estoque_minimo,
                                            D.nome as categoria,
                                            A.lote,
                                            A.inventario,
                                            C.nome_cliente as fornecedor,
                                            E.nome_cliente as loja,
                                            ((A.qtde_chapa_peca_mo * A.qtde_por_pacote_mo) + (A.qtde_chapa_peca * A.qtde_por_pacote)) - ((select
                                                    count(1)
                                                from
                                                    lote_estoque_baixados X
                                                where
                                                    X.estoque_id = A.id) * (A.qtde_chapa_peca + A.qtde_chapa_peca_mo)) as estoque_atual,
                                            ((A.qtde_por_pacote_mo) + (A.qtde_por_pacote)) - ((select
                                                    count(1)
                                                from
                                                    lote_estoque_baixados X
                                                where
                                                    X.estoque_id = A.id)) as estoque_pacote_atual,
                                            B.material,
                                            A.material_id,
                                            B.consumo_medio_mensal,
                                            A.alerta_baixa_errada
                                        FROM
                                            estoque A
                                        INNER JOIN
                                            materiais B
                                            ON B.id = A.material_id
                                        INNER JOIN
                                            pessoas C
                                        ON
                                            C.id = A.fornecedor_id
                                        INNER JOIN
                                            categorias_materiais D
                                        ON
                                            D.id = B.categoria_id
                                        LEFT JOIN
                                            pessoas E
                                        ON
                                            E.id = A.loja_id
                                        $condicao_sem_estoque
                                        $condicao_material
                                        ORDER BY
                                            A.data DESC
                                    "));


        $array_estoque_finalizado = $array_material_verificacao= [];
        foreach($estoque_finalizado as $key => $value) {

            $pacote = $value->qtde_por_pacote + $value->qtde_por_pacote_mo;
            $estoque_comprado = ($value->qtde_chapa_peca * $value->qtde_por_pacote) + ($value->qtde_chapa_peca_mo * $value->qtde_por_pacote_mo);

            //se não existir o material no estoque, ele não entra no array
            if(!in_array($value->material, $array_material_verificacao)) {
                $array_material_verificacao[] = $value->material;

                $array_estoque_finalizado[$value->id]['id'] = $value->id;
                $array_estoque_finalizado[$value->id]['categoria'] = $value->categoria;
                $array_estoque_finalizado[$value->id]['material'] = $value->material;
                $array_estoque_finalizado[$value->id]['estoque_minimo'] = number_format($value->estoque_minimo,0, '','.');
            }
        }

        $tela = 'pesquisa';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'estoque',
				'array_estoque'=> $array_estoque,
				'array_estoque_finalizado'=> $array_estoque_finalizado,
				'estoque_material_somado'=> $estoque_material_somado,
                'materiais' => (new OrcamentosController())->getAllMateriais(),
                'fornecedores' => $this->getFornecedores(),
                'lojas' => $this->getLojas(),
                'CategoriasMateriais' => (new CategoriasMateriais)->orderBy('nome')->get(),
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
                'lojas' => $this->getLojas(),
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

        $qtde_por_pacote = !empty($estoque[0]->qtde_por_pacote) ? $estoque[0]->qtde_por_pacote : 0;
        $qtde_por_pacote_mo = !empty($estoque[0]->qtde_por_pacote_mo) ? $estoque[0]->qtde_por_pacote_mo : 0;


        $total_pacote_no_lote = $qtde_por_pacote + $qtde_por_pacote_mo;

        $LoteEstoqueBaixados = new  LoteEstoqueBaixados();

        $pacotesbaixados = $LoteEstoqueBaixados->where('estoque_id', '=', $request->input('id'))->count();

        // dd($pacotesbaixados, $total_pacote_no_lote);
        $pacotes_restantes =  $total_pacote_no_lote-$pacotesbaixados;

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
                'lojas' => $this->getLojas(),
                'pacotes_restantes' => $pacotes_restantes,
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

            $valor_unitario = $request->input('valor_unitario');
            if(empty($request->input('valor_unitario'))) {
                $valor_unitario = 0;
            }

            $estoque->material_id = $request->input('material_id');
            $estoque->data = DateHelpers::formatDate_dmY($request->input('data'));
            $estoque->nota_fiscal = $request->input('nota_fiscal');
            $estoque->fornecedor_id = $request->input('fornecedor_id');
            $estoque->loja_id = $request->input('loja_id');
            $estoque->lote = $request->input('lote');
            $estoque->valor_unitario = trim($valor_unitario) != '' ? DateHelpers::formatFloatValue($valor_unitario): null;
            $estoque->valor = trim($request->input('valor')) != '' ? DateHelpers::formatFloatValue($request->input('valor')): null;
            $estoque->imposto = trim($request->input('imposto')) != '' ? DateHelpers::formatFloatValue($request->input('imposto')): null;
            $estoque->total = trim($request->input('total')) != '' ? DateHelpers::formatFloatValue($request->input('total')): null;
            $estoque->VD = (!empty($request->input('VD')) && $request->input('VD') ==1 ) ? 1 : 0;
            $estoque->MO = (!empty($request->input('MO')) && $request->input('MO') ==1 ) ? 1 : 0;
            $estoque->qtde_chapa_peca = str_replace('.', '',$request->input('qtde_chapa_peca', 0));
            $estoque->qtde_por_pacote = str_replace('.', '',$request->input('qtde_por_pacote', 0));
            $estoque->status_estoque = $request->input('status_estoque');
            $estoque->status = $request->input('status');
            $estoque->valor_mo = trim($request->input('valor_mo')) != '' ? DateHelpers::formatFloatValue($request->input('valor_mo')): 0;
            $estoque->valor_kg_mo = trim($request->input('valor_kg_mo')) != '' ? DateHelpers::formatFloatValue($request->input('valor_kg_mo')): 0;
            $estoque->imposto_mo = trim($request->input('imposto_mo')) != '' ? DateHelpers::formatFloatValue($request->input('imposto_mo')): 0;
            $estoque->total_mo = trim($request->input('total_mo')) != '' ? DateHelpers::formatFloatValue($request->input('total_mo')): 0;
            $estoque->peso_material = $request->input('peso_material', 0);
            $estoque->peso_material_mo = $request->input('peso_material_mo', '0');
            $estoque->qtde_chapa_peca_mo = trim($request->input('qtde_chapa_peca_mo')) != '' ? str_replace('.', '',$request->input('qtde_chapa_peca_mo', '0')) : 0;
            $estoque->qtde_por_pacote_mo = trim($request->input('qtde_por_pacote_mo')) != '' ? str_replace('.', '',$request->input('qtde_por_pacote_mo', '0')) : 0;
            $estoque->observacaoes = $request->input('observacaoes');
            $estoque->alerta_baixa_errada = $request->input('alerta_baixa_errada');

            $estoque->save();

            $material = new Materiais();
            $material = $material->find($request->input('material_id'));

            info('Salvando estoque: ' . $request->input('lote'));
            info('Material: ' . $material->id . ' - Valor: ' . $material->valor);
            info('Valor Unitário: ' . DateHelpers::formatFloatValue($valor_unitario));

            if(DateHelpers::formatFloatValue($valor_unitario) == 0) {
                //busca o valor do ultimo lote do mesmo material que tenha valor maior que 0
                $ultimoEstoque = Estoque::where('material_id', $material->id)
                                ->where('valor_unitario', '>', 0)
                                ->where('id', '<>', $estoque->id)
                                ->orderBy('data', 'desc')
                                ->orderBy('id', 'desc')
                                ->first();

                $valor_unitario = !empty($ultimoEstoque->valor_unitario) ? $ultimoEstoque->valor_unitario : 0;

            }
            if($material->valor != DateHelpers::formatFloatValue($valor_unitario) && DateHelpers::formatFloatValue($valor_unitario) > 0) {



                $historico = "Valor do material alterado pelo lote de ". number_format($material->valor, 2, ',', '') . " para " . DateHelpers::formatFloatValue($valor_unitario);
                $material->valor = DateHelpers::formatFloatValue($valor_unitario);
                $material->save();

                if(!empty($historico)) {

                    $MateriaisHistoricosValores = new MateriaisHistoricosValores();
                    $MateriaisHistoricosValores->materiais_id = $material->id;
                    $MateriaisHistoricosValores->valor = DateHelpers::formatFloatValue($valor_unitario);
                    $MateriaisHistoricosValores->save();

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
        $fornecedores = $fornecedores->where('fornecedor','=', '1')->orderBy('nome_cliente', 'asc')->get();
        if($array) {
            $fornecedores = $fornecedores->toArray();
        }
        return $fornecedores;
    }

    public function getLojas($array = false){
        $lojas = new Pessoas();
        $lojas = $lojas->where('loja','=', '1')->orderBy('nome_cliente', 'asc')->get();
        if($array) {
            $lojas = $lojas->toArray();
        }
        return $lojas;
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
