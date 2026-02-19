<?php

namespace App\Http\Controllers;

use App\Models\HistoricosRenovacoes;
use App\Models\Perfis;
use App\Models\Renovacoes;
use App\Providers\DateHelpers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RenovacoesController extends Controller
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
        $departamento = $request->input('departamento');
        $status = $request->input('status', 'P');
        $vencimento = $request->input('vencimento');

        $renovacoes = Renovacoes::query()
            ->select('renovacoes.*', 'perfis.nome as departamento_nome')
            ->leftJoin('perfis', 'perfis.id', '=', 'renovacoes.departamento_id');

        if (!empty($departamento)) {
            $renovacoes->where('renovacoes.departamento_id', '=', $departamento);
        }

        if (!empty($status)) {
            $renovacoes->where('renovacoes.status', '=', $status);
        }

        if (!empty($vencimento)) {
            $data = DateHelpers::formatDate_dmY($vencimento);
            $renovacoes->whereDate('renovacoes.data_vencimento', '=', $data);
        }

        $renovacoes = $renovacoes
            ->orderBy('perfis.nome', 'asc')
            ->orderBy('renovacoes.data_vencimento', 'asc')
            ->get()
            ->map(function ($item) {
                $item->em_alerta = false;
                $item->alerta_direcao = null;
                $item->alerta_cor = null;

                if ($item->status == 'P') {
                    if (!empty($item->inicio_renovacao)) {
                        $inicio = Carbon::parse($item->inicio_renovacao)->startOfDay();
                        $hoje = Carbon::today();

                        if ($inicio->greaterThan($hoje)) {
                            $item->em_alerta = false;
                            $item->alerta_direcao = 'up';
                            $item->alerta_cor = 'success';
                        } else {
                            $item->em_alerta = true;
                            $item->alerta_direcao = 'down';
                            $item->alerta_cor = 'danger';

                        }
                    } elseif (!empty($item->data_vencimento)) {
                        $alerta_vencimento = Carbon::parse($item->data_vencimento)->isPast();
                        $item->em_alerta = $alerta_vencimento;
                        $item->alerta_direcao = $alerta_vencimento ? 'down' : 'up';
                        $item->alerta_cor = $alerta_vencimento ? 'danger' : 'success';
                    }
                }
                return $item;
            });

        $tela = 'pesquisa';
        $data = array(
            'tela' => $tela,
            'nome_tela' => 'renovações',
            'renovacoes' => $renovacoes,
            'perfis' => (new Perfis())->where('status', '=', 'A')->orderBy('nome', 'asc')->get(),
            'request' => $request,
            'rotaIncluir' => 'incluir-renovacoes',
            'rotaAlterar' => 'alterar-renovacoes'
        );

        return view('renovacoes', $data);
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
            $renovacao_id = $this->salva($request);
            return redirect()->route('renovacoes', ['id' => $renovacao_id]);
        }

        $tela = 'incluir';
        $data = array(
            'tela' => $tela,
            'nome_tela' => 'renovações',
            'perfis' => (new Perfis())->where('status', '=', 'A')->orderBy('nome', 'asc')->get(),
            'request' => $request,
            'rotaIncluir' => 'incluir-renovacoes',
            'rotaAlterar' => 'alterar-renovacoes'
        );

        return view('renovacoes', $data);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function alterar(Request $request)
    {
        $renovacao = Renovacoes::where('id', '=', $request->input('id'))->get();

        $metodo = $request->method();
        if ($metodo == 'POST') {
            $renovacao_id = $this->salva($request);
            return redirect()->route('renovacoes', ['id' => $renovacao_id]);
        }

        $historicos = HistoricosRenovacoes::where('renovacoes_id', '=', $renovacao[0]->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $tela = 'alterar';
        $data = array(
            'tela' => $tela,
            'nome_tela' => 'renovações',
            'renovacao' => $renovacao,
            'historicos' => $historicos,
            'perfis' => (new Perfis())->where('status', '=', 'A')->orderBy('nome', 'asc')->get(),
            'request' => $request,
            'rotaIncluir' => 'incluir-renovacoes',
            'rotaAlterar' => 'alterar-renovacoes'
        );

        return view('renovacoes', $data);
    }

    public function finalizar(Request $request)
    {
        $resultado = DB::transaction(function () use ($request) {
            $renovacao = Renovacoes::find($request->input('id'));

            if (empty($renovacao)) {
                return [
                    'error' => true,
                    'mensagem' => 'Renovação não encontrada.'
                ];
            }

            $renovacao->status = 'F';
            $renovacao->data_finalizado = now();
            $renovacao->save();

            $historicos = new HistoricosRenovacoes();
            $historicos->renovacoes_id = $renovacao->id;
            $historicos->historico = 'Renovação finalizada';
            $historicos->status = 'A';
            $historicos->save();

            $novo_id = null;
            if ($request->input('gerar_nova') == '1') {
                $nova = $renovacao->replicate();
                $nova->data_abertura = now();
                $nova->data_vencimento = null;
                $nova->inicio_renovacao = null;
                $nova->data_finalizado = null;
                $nova->status = 'P';
                $nova->save();

                $novo_id = $nova->id;

                $historico_novo = new HistoricosRenovacoes();
                $historico_novo->renovacoes_id = $novo_id;
                $historico_novo->historico = 'Renovação criada a partir da finalização da #' . $renovacao->id;
                $historico_novo->status = 'A';
                $historico_novo->save();
            }

            return [
                'error' => false,
                'novo_id' => $novo_id
            ];
        });

        if (!empty($resultado['error']) && $resultado['error']) {
            return redirect()->route('renovacoes')->with('error', $resultado['mensagem']);
        }

        if (!empty($resultado['novo_id'])) {
            return redirect()->route('alterar-renovacoes', ['id' => $resultado['novo_id']])
                ->with('success', 'Renovação finalizada e nova renovação criada.');
        }

        return redirect()->route('renovacoes')->with('success', 'Renovação finalizada com sucesso.');
    }

    public function salva(Request $request)
    {
        $id = DB::transaction(function () use ($request) {
            $renovacao = new Renovacoes();
            $is_nova = true;

            if ($request->input('id')) {
                $renovacao = $renovacao::find($request->input('id'));
                $is_nova = false;
            }

            $renovacao->data_abertura = $this->parseDateTime($request->input('data_abertura'))
                ?? ($is_nova ? now() : $renovacao->data_abertura);
            $renovacao->departamento_id = $request->input('departamento_id');
            $renovacao->descricao = $request->input('descricao');
            $renovacao->responsavel = $request->input('responsavel');
            $renovacao->numero_documento = $request->input('numero_documento');
            $renovacao->periodo_renovacao = $request->input('periodo_renovacao');
            $renovacao->data_vencimento = $this->parseDateTime($request->input('data_vencimento'));
            $renovacao->inicio_renovacao = $this->parseDateTime($request->input('inicio_renovacao'));
            $renovacao->previsao = $request->input('previsao');
            $renovacao->data_finalizado = $this->parseDateTime($request->input('data_finalizado'));
            $renovacao->status = $request->input('status', 'P');

            if ($renovacao->status == 'P') {
                $renovacao->data_finalizado = null;
            }

            if ($renovacao->status == 'F' && empty($renovacao->data_finalizado)) {
                $renovacao->data_finalizado = now();
            }

            $renovacao->save();

            $historicos = new HistoricosRenovacoes();
            $historicos->renovacoes_id = $renovacao->id;
            $historicos->historico = $is_nova ? 'Inclusão da renovação' : 'Alteração da renovação';
            $historicos->status = 'A';
            $historicos->save();

            return $renovacao->id;
        });

        return $id;
    }

    private function parseDateTime($value)
    {
        if (empty($value)) {
            return null;
        }

        try {
            return Carbon::createFromFormat('d/m/Y H:i:s', $value)->format('Y-m-d H:i:s');
        } catch (\Throwable $th) {
            return Carbon::parse(str_replace('/', '-', $value))->format('Y-m-d H:i:s');
        }
    }
}
