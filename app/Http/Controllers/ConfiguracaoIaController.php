<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConfiguracaoIaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        // Sempre busca ou cria o registro único de configuração
        $config = DB::table('configuracoes_ia')->first();

        if (!$config) {
            // Cria registro padrão se não existir
            $id = DB::table('configuracoes_ia')->insertGetId([
                'tempo_entrega_dias' => 30,
                'tempo_cliente_sem_compra_dias' => 30,
                'status' => 'A',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $config = DB::table('configuracoes_ia')->find($id);
        }

        if ($request->method() === 'POST') {
            $this->salva($request, $config->id);
            return redirect()->route('configuracao-ia')->with('success', 'Configuração atualizada com sucesso');
        }

        $data = [
            'nome_tela' => 'Configuração IA',
            'config' => $config,
            'request' => $request,
        ];

        return view('configuracao_ia', $data);
    }

    private function salva(Request $request, $configId)
    {
        $data = [
            'tempo_entrega_dias' => (int) $request->input('tempo_entrega_dias'),
            'tempo_cliente_sem_compra_dias' => (int) $request->input('tempo_cliente_sem_compra_dias'),
            'updated_at' => now(),
        ];

        DB::table('configuracoes_ia')->where('id', $configId)->update($data);
        return $configId;
    }
}
