<?php

namespace App\Http\Controllers;

use App\Models\Dashboards;
use App\Models\Funcionarios;
use App\Models\Pedidos;
use App\Models\Perfis;
use App\Models\PerfisDashboards;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
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
    public function index()
    {

        $data_atraso=$data_30=$data_60=date('d/m/Y');
        $qtde_vendas_dia=$total_soma_dia=$total_soma_entrega_dia=0;
        $total_soma_entrega_dia=$qtde_vendas_mes=$total_soma_mes=$qtde_vendas_entrega_mes=$total_soma_entrega_mes=0;
        $qtqe_os_atrasada=$total_soma_entrega_mes_anterior=$percentual_comparativo=0;
        $array_material_alerta_30=$array_material_alerta_60=[];

        $user = \Auth::user();

        $users = new Funcionarios();
        $users = $users->where('id', '=', $user->id)->first();
        $perfil = $users->perfil;

        $perfis = new Perfis();
        $perfis = $perfis->where('id', '=', $perfil)->first();

        $dashboards = new Dashboards();
        $dashboards = $dashboards->where('status', '=', 'A')->get();

        $perfis_dashboards = new PerfisDashboards();
        $perfis_dashboards = $perfis_dashboards->where('perfis_id', '=', $perfis->id)->get()->pluck('dashboard_id')->toArray();


        //se tiver permissão de vendas ou comparativo (o comparativo presisa do valor de vendas)
        if(in_array('1', $perfis_dashboards) || in_array('3', $perfis_dashboards) ){


            //Busca dados diario

            $data1 = $this->ultimoDiaUtil(date('d/m/Y')); //"01/08/2024";
            $data2 = $this->ultimoDiaUtil(date('d/m/Y'));//"05/08/2024";
            $tipo_consulta = 'G';
            $status_ids =["1","2","3","4","5","6","7","8","9","10"];
            $request = $this->GeraRequestBuscaOs($data1, $data2, $tipo_consulta, $status_ids);

            $pedidos = new PedidosController();
            $array_pedidos = $pedidos->followupgerencial($request);

            $request = Request::create('/followup-gerencial-dados', 'GET', [
                'pedidos_encontrados' => json_encode($array_pedidos),
                'nome_tela' => 'gerencial',
            ]);
            $dados = $pedidos->followupgerencialDados ($request);
            $qtde_vendas_dia=$total_soma_dia=0;
            $dados = $dados->getData();
            // dd($dados);
            foreach ($dados['dados_pedido_status'] as $status) {
                foreach ($status['classe'] as $pedido) {
                    $total = $pedido->valor_unitario_adv * $pedido->qtde;
                    $total_soma_dia = isset($total_soma_dia) ? $total_soma_dia + $total : $total;
                    $qtde_vendas_dia++;
                }
            }

            //Busca dados mensais

            $data = date('d/m/Y');
            $data1 = Carbon::createFromFormat('d/m/Y', $data)->startOfMonth()->format('d/m/Y'); //"01/08/2024";
            $data2 = $this->ultimoDiaUtil(date('d/m/Y')); //"05/08/2024";
            $tipo_consulta = 'G';
            $status_ids =["1","2","3","4","5","6","7","8","9","10"];
            $request = $this->GeraRequestBuscaOs($data1, $data2, $tipo_consulta, $status_ids);

            $pedidos = new PedidosController();
            $array_pedidos = $pedidos->followupgerencial($request);

            $request = Request::create('/followup-gerencial-dados', 'GET', [
                'pedidos_encontrados' => json_encode($array_pedidos),
                'nome_tela' => 'gerencial',
            ]);
            $dados = $pedidos->followupgerencialDados ($request);
            $qtde_vendas_mes=$total_soma_mes=0;
            $dados = $dados->getData();
            // dd($dados);
            foreach ($dados['dados_pedido_status'] as $status) {
                foreach ($status['classe'] as $pedido) {
                    $total = $pedido->valor_unitario_adv * $pedido->qtde;
                    $total_soma_mes = isset($total_soma_mes) ? $total_soma_mes + $total : $total;
                    $qtde_vendas_mes++;
                }
            }

            //Busca dados realizados dia
            $data = date('d/m/Y');
            $data1 = $this->ultimoDiaUtil(date('d/m/Y')); //"01/08/2024";
            $data2 = $this->ultimoDiaUtil(date('d/m/Y'));//"05/08/2024";
            $tipo_consulta = 'R';
            $status_ids =['11'];
            $request = $this->GeraRequestBuscaOsRealizado($data1, $data2, $tipo_consulta, $status_ids);

            $pedidos = new PedidosController();
            $array_pedidos = $pedidos->followupgerencial($request);

            $request = Request::create('/followup-gerencial-dados', 'GET', [
                'pedidos_encontrados' => json_encode($array_pedidos),
                'nome_tela' => 'gerencial',
            ]);
            $dados = $pedidos->followupgerencialDados ($request);
            $qtde_vendas_entrega_dia=$total_soma_entrega_dia=0;
            $dados = $dados->getData();
            // dd($dados);
            foreach ($dados['dados_pedido_status'] as $status) {
                foreach ($status['classe'] as $pedido) {
                    $total = $pedido->valor_unitario_adv * $pedido->qtde;
                    $total_soma_entrega_dia = isset($total_soma_entrega_dia) ? $total_soma_entrega_dia + $total : $total;
                    $qtde_vendas_entrega_dia++;
                }
            }

            //Busca dados realisados mes
            $data = date('d/m/Y');
            $data1 = Carbon::createFromFormat('d/m/Y', $data)->startOfMonth()->format('d/m/Y'); //"01/08/2024";
            $data2 = $this->ultimoDiaUtil(date('d/m/Y')); //"05/08/2024";
            $tipo_consulta = 'R';
            $status_ids =['11'];
            $request = $this->GeraRequestBuscaOsRealizado($data1, $data2, $tipo_consulta, $status_ids);

            $pedidos = new PedidosController();
            $array_pedidos = $pedidos->followupgerencial($request);

            $request = Request::create('/followup-gerencial-dados', 'GET', [
                'pedidos_encontrados' => json_encode($array_pedidos),
                'nome_tela' => 'gerencial',
            ]);
            $dados = $pedidos->followupgerencialDados ($request);
            $qtde_vendas_entrega_mes=$total_soma_entrega_mes=0;
            $dados = $dados->getData();
            // dd($dados);
            foreach ($dados['dados_pedido_status'] as $status) {
                foreach ($status['classe'] as $pedido) {
                    $total = $pedido->valor_unitario_adv * $pedido->qtde;
                    $total_soma_entrega_mes = isset($total_soma_entrega_mes) ? $total_soma_entrega_mes + $total : $total;
                    $qtde_vendas_entrega_mes++;
                }
            }

        }

        if(in_array('2', $perfis_dashboards)){
            //Busca Atrasos
            $data_atraso = date('d/m/Y'); //"05/08/2024";
            $tipo_consulta = 'F';
            $status_ids =["1","2","3","4","5","6","7","8","9"];
            $request = $this->GeraRequestBuscaOsFollowup($data_atraso, $tipo_consulta, $status_ids);

            $pedidos = new PedidosController();
            $array_pedidos = $pedidos->followup($request);
            $array_pedidos = $array_pedidos->getData();

            $request = Request::create('/followup-detalhes', 'GET', [
                'pedidos_encontrados' => json_encode($array_pedidos['pedidos_encontrados']),
                'nome_tela' => 'gerencial',
            ]);
            $dados = $pedidos->followupDetalhes ($request);
            $dados = $dados->getData();
            $qtqe_os_atrasada =0;
            foreach ($dados['dados_pedido_status'] as $pedidos) {
                foreach ($pedidos['classe'] as $pedido) {

                    $entrega = Carbon::createFromDate($pedido->data_entrega)->format('Y-m-d');
                    $hoje = date('Y-m-d');
                    $dias_alerta = Carbon::createFromDate($hoje)->diffInDays($entrega, false);


                    if($dias_alerta < 0) {
                        $qtqe_os_atrasada++;
                    }
                }
            }

        }

        if(in_array('3', $perfis_dashboards)){
            //Busca dados comparativos mes anterior

            // Data de hoje
            $data_hoje = Carbon::now();

            // 1º dia do mês atual
            $data_1_mes = $data_hoje->copy()->startOfMonth()->format('d/m/Y');

            // 1º dia do mês anterior
            $data_1_mes_anterior = $data_hoje->copy()->subMonth()->startOfMonth()->format('d/m/Y');

            // Mesmo dia do mês anterior
            $data_mes_anterior_no_dia_atual = $data_hoje->copy()->subMonth()->format('d/m/Y');
            $data = date('d/m/Y');
            $data1 = $data_1_mes_anterior;
            $data2 = $data_mes_anterior_no_dia_atual;
            $tipo_consulta = 'R';
            $status_ids =['11'];
            $request = $this->GeraRequestBuscaOsRealizado($data1, $data2, $tipo_consulta, $status_ids);

            $pedidos = new PedidosController();
            $array_pedidos = $pedidos->followupgerencial($request);

            $request = Request::create('/followup-gerencial-dados', 'GET', [
                'pedidos_encontrados' => json_encode($array_pedidos),
                'nome_tela' => 'gerencial',
            ]);
            $dados = $pedidos->followupgerencialDados ($request);
            $total_soma_entrega_mes_anterior=0;
            $dados = $dados->getData();
            // dd($dados);
            foreach ($dados['dados_pedido_status'] as $status) {
                foreach ($status['classe'] as $pedido) {
                    $total = $pedido->valor_unitario_adv * $pedido->qtde;
                    $total_soma_entrega_mes_anterior = isset($total_soma) ? $total_soma + $total : $total;
                }
            }

            $percentual_comparativo = $this->calcularPercentual($total_soma_entrega_mes_anterior, $total_soma_entrega_mes);
        }

        if(in_array('4', $perfis_dashboards)){
            //busca previsto 30 dias
            $data_hoje = Carbon::now();
            $data_30 = $data_hoje->copy()->addDays(30)->format('d/m/Y');
            $tipo_consulta = 'P';
            $status_ids =['1','2','3','4','5','6','7','8','9','10'];
            $request = $this->GeraRequestBuscaPrevisto($data_30, $status_ids);

            $relatorios = new RelatoriosController();
            $dados = $relatorios->relatorioPrevisaoMaterial($request);
            $dados = $dados->getData();
            $array_material_alerta_30 = [];
            foreach ($dados['materiais'] as $material) {

                if($material['estoque_atual'] < $material['consumo_previsto'] || ($material['estoque_atual']==0 && $material['consumo_previsto']==0)) {
                    $array_material_alerta_30[]=[
                        'material' => $material['material'],
                        'diferenca' => $material['diferenca']
                    ];
                }

            }

            //busca previsto 60 dias
            $data_60 = $data_hoje->copy()->addDays(60)->format('d/m/Y');
            $tipo_consulta = 'P';
            $status_ids =['1','2','3','4','5','6','7','8','9','10'];
            $request = $this->GeraRequestBuscaPrevisto($data_60, $status_ids);
            $relatorios = new RelatoriosController();
            $dados = $relatorios->relatorioPrevisaoMaterial($request);
            $dados = $dados->getData();
            $array_material_alerta_60 = [];
            foreach ($dados['materiais'] as $material) {

                if($material['estoque_atual'] < $material['consumo_previsto'] || ($material['estoque_atual']==0 && $material['consumo_previsto']==0)) {
                    $array_material_alerta_60[]=[
                        'material' => $material['material'],
                        'diferenca' => $material['diferenca']
                    ];
                }

            }

        }



        // dd($percentual_comparativo);
        $dados = [
            'perfis_dashboards' => $perfis_dashboards,
            'vendas' => [
                'qtde_vendas_dia' => $qtde_vendas_dia,
                'vendas_dia' => "R$ ". number_format($total_soma_dia, 2, ',', '.'),
                'qtde_entregas_dia' => $total_soma_entrega_dia,
                'entregas_dia' => "R$ ". number_format($total_soma_entrega_dia, 2, ',', '.'),
                'qtde_vendas_mes' => $qtde_vendas_mes,
                'vendas_mensal' => "R$ ". number_format($total_soma_mes, 2, ',', '.'),
                'qtde_entregas_mensal' => $qtde_vendas_entrega_mes,
                'entregas_mensal' => "R$ ". number_format($total_soma_entrega_mes, 2, ',', '.'),
            ],
            'os_atraso' => $qtqe_os_atrasada,
            'data_atraso' => $data_atraso,
            'comparativo_valor' => 'R$ '. number_format($total_soma_entrega_mes_anterior, 2, ',', '.'),
            'comparativo_percentual' => number_format($percentual_comparativo, 0, ',', '.'),
            'array_material_alerta_30' => $array_material_alerta_30,
            'array_material_alerta_60' => $array_material_alerta_60,
            'data_30' => $data_30,
            'data_60' => $data_60
            ];

        return view('home', $dados);
    }

    public function calcularPercentual($valorAnterior, $valorAtual) {
        if ($valorAnterior == 0) {
            return 0;
        }
        $percentual = (($valorAtual - $valorAnterior) / $valorAnterior) * 100;
        return $percentual;
    }

    public function GeraRequestBuscaOsFollowup($data2, $tipo_consulta, $status_id) {
        return $request = Request::create('/followup-gerencial', 'GET', [
            'somente_dados' => true,
            "tipo_consulta" => $tipo_consulta,
            "data_entrega_fim" => $data2,
            "status_id" =>  $status_id
            ]);
    }
    public function GeraRequestBuscaOs($data1, $data2, $tipo_consulta, $status_id) {
        return $request = Request::create('/followup-gerencial', 'GET', [
            'somente_dados' => true,
            "tipo_consulta" => $tipo_consulta,
            "data_gerado" => $data1,
            "data_gerado_fim" => $data2,
            "status_id" =>  $status_id
            ]);
    }

    public function GeraRequestBuscaOsRealizado($data1, $data2, $tipo_consulta, $status_id) {
        return $request = Request::create('/followup-gerencial', 'GET', [
            'somente_dados' => true,
            "tipo_consulta" => $tipo_consulta,
            "data_apontamento" => $data1,
            "data_apontamento_fim" => $data2,
            "status_id" =>  $status_id
            ]);
    }

    public function GeraRequestBuscaPrevisto($data2, $status_id) {
        return $request = Request::create('/followup-gerencial', 'GET', [
            "tipo_consulta" => 'P',
            "data_fim" => $data2,
            "status_id" =>  $status_id
            ]);
    }

    public function ultimoDiaUtil($data)
    {
        $data = Carbon::createFromFormat('d/m/Y', $data); // Converte a data para objeto Carbon

        // Se for segunda-feira, volta para sexta-feira anterior
        if ($data->isMonday()) {
            return $data->subDays(3)->format('d/m/Y');
        }

        // Se for sábado, volta para sexta-feira
        if ($data->isSaturday()) {
            return $data->subDay()->format('d/m/Y');
        }

        // Se for domingo, volta para sexta-feira
        if ($data->isSunday()) {
            return $data->subDays(2)->format('d/m/Y');
        }

        return $data->subDay()->format('d/m/Y');
    }


}
