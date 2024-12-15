<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes(['register' => false, 'reset' => false]);

Route::get('admin/settings', [App\Http\Controllers\SettingsController::class, 'index'])->name('settings')->middleware('afterAuth:admin/settings');
Route::post('admin/alterar-senha', [App\Http\Controllers\SettingsController::class, 'edit'])->name('alterar-senha')->middleware('afterAuth:admin/settings');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::match(['get', 'post'],'/materiais', [App\Http\Controllers\MateriaisController::class, 'index'])->name('materiais')->middleware('afterAuth:materiais');
Route::match(['get', 'post'],'/alterar-materiais', [App\Http\Controllers\MateriaisController::class, 'alterar'])->name('alterar-materiais')->middleware('afterAuth:materiais');
Route::match(['get', 'post'],'/incluir-materiais', [App\Http\Controllers\MateriaisController::class, 'incluir'])->name('incluir-materiais')->middleware('afterAuth:materiais');

Route::match(['get', 'post'],'/clientes', [App\Http\Controllers\PessoasController::class, 'index'])->name('clientes')->middleware('afterAuth:clientes');
Route::match(['get', 'post'],'/alterar-clientes', [App\Http\Controllers\PessoasController::class, 'alterar'])->name('alterar-clientes')->middleware('afterAuth:clientes');
Route::match(['get', 'post'],'/incluir-clientes', [App\Http\Controllers\PessoasController::class, 'incluir'])->name('incluir-clientes')->middleware('afterAuth:clientes');

Route::match(['get', 'post'],'/status', [App\Http\Controllers\StatusController::class, 'index'])->name('status')->middleware('afterAuth:status');
Route::match(['get', 'post'],'/alterar-status', [App\Http\Controllers\StatusController::class, 'alterar'])->name('alterar-status')->middleware('afterAuth:status');
Route::match(['get', 'post'],'/incluir-status', [App\Http\Controllers\StatusController::class, 'incluir'])->name('incluir-status')->middleware('afterAuth:status');

Route::match(['get', 'post'],'/funcionarios', [App\Http\Controllers\FuncionariosController::class, 'index'])->name('funcionarios')->middleware('afterAuth:funcionarios');
Route::match(['get', 'post'],'/alterar-funcionarios', [App\Http\Controllers\FuncionariosController::class, 'alterar'])->name('alterar-funcionarios')->middleware('afterAuth:funcionarios');
Route::match(['get', 'post'],'/incluir-funcionarios', [App\Http\Controllers\FuncionariosController::class, 'incluir'])->name('incluir-funcionarios')->middleware('afterAuth:funcionarios');

Route::match(['get', 'post'],'/fichatecnica', [App\Http\Controllers\FichatecnicaController::class, 'index'])->name('fichatecnica')->middleware('afterAuth:fichatecnica');
Route::match(['get', 'post'],'/alterar-fichatecnica', [App\Http\Controllers\FichatecnicaController::class, 'alterar'])->name('alterar-fichatecnica')->middleware('afterAuth:fichatecnica');
Route::match(['get', 'post'],'/incluir-fichatecnica', [App\Http\Controllers\FichatecnicaController::class, 'incluir'])->name('incluir-fichatecnica')->middleware('afterAuth:fichatecnica');
Route::match(['get', 'post'],'/ajax-fichatecnica', [App\Http\Controllers\AjaxfichatecnicaController::class, 'buscarMateriais'])->name('ajax-fichatecnica')->middleware('afterAuth:fichatecnica');
Route::match(['post'],'/ajax-fichatecnica-check-ep', [App\Http\Controllers\AjaxfichatecnicaController::class, 'checkEpExistente'])->name('ajax-fichatecnica-check-ep')->middleware('afterAuth:fichatecnica');
Route::match(['get'],'/clona-fichatecnica', [App\Http\Controllers\FichatecnicaController::class, 'clonarFichatecnica'])->name('clona-fichatecnica')->middleware('afterAuth:fichatecnica');
Route::match(['post'],'/ajax-fichatecnica-calculo-usinagem', [App\Http\Controllers\AjaxfichatecnicaController::class, 'calculaUsinagem'])->name('ajax-fichatecnica-calculo-usinagem')->middleware('afterAuth:fichatecnica');
Route::match(['post'],'/buscar-caixas', [App\Http\Controllers\CaixasController::class, 'buscarCaixas'])->name('ajax-buscar-caixas');

Route::match(['get', 'post'],'/ajax-getProducao', [App\Http\Controllers\HomeController::class, 'getProducao'])->name('ajax-getProducao');
Route::match(['get', 'post'],'/followup', [App\Http\Controllers\PedidosController::class, 'followup'])->name('followup')->middleware('afterAuth:followup');
Route::match(['get', 'post'],'/followup-geral', [App\Http\Controllers\PedidosController::class, 'followup'])->name('followup-geral')->middleware('afterAuth:followup');
Route::match(['get', 'post'],'/followup-realizado', [App\Http\Controllers\PedidosController::class, 'followupRealizado'])->name('followup-realizado')->middleware('afterAuth:followup');
Route::match(['get', 'post'],'/imprimir-os', [App\Http\Controllers\PedidosController::class, 'imprimirOS'])->name('imprimirOS')->middleware('afterAuth:followup');
Route::match(['get', 'post'],'/imprimir-mp', [App\Http\Controllers\PedidosController::class, 'imprimirMP'])->name('imprimirMP')->middleware('afterAuth:followup');
Route::match(['get', 'post'],'/followup-detalhes', [App\Http\Controllers\PedidosController::class, 'followupDetalhes'])->name('followup-detalhes')->middleware('afterAuth:followup');

Route::match(['get', 'post'],'/pedidos', [App\Http\Controllers\PedidosController::class, 'index'])->name('pedidos')->middleware('afterAuth:pedidos');
Route::match(['get', 'post'],'/alterar-pedidos', [App\Http\Controllers\PedidosController::class, 'alterar'])->name('alterar-pedidos')->middleware('afterAuth:pedidos');
Route::match(['get', 'post'],'/incluir-pedidos', [App\Http\Controllers\PedidosController::class, 'incluir'])->name('incluir-pedidos')->middleware('afterAuth:pedidos');

Route::match(['post'],'/alterar-pedidos-ajax', [App\Http\Controllers\PedidosController::class, 'ajaxAlterar'])->name('alterar-pedidos-ajax')->middleware('afterAuth:pedidos');
Route::match(['post'],'/incluir-pedidos-funcionario-montagem', [App\Http\Controllers\PedidosController::class, 'ajaxIncluirFuncionariosMontagens'])->name('incluir-pedidos-funcionario-montagem');
Route::match(['post'],'/calcular-orcamento-ajax', [App\Http\Controllers\AjaxOrcamentosController::class, 'ajaxCalculaOrcamentos'])->name('calcular-orcamento-ajax');

Route::match(['get',],'/contatos', [App\Http\Controllers\ContatosController::class, 'index'])->name('contatos')->middleware('afterAuth:pedidos');;
Route::match(['post',],'/enviar-contatos', [App\Http\Controllers\ContatosController::class, 'store'])->name('envia-contatos')->middleware('afterAuth:pedidos');;
Route::match(['get', 'post'],'/alertas-pedidos', [App\Http\Controllers\PedidosController::class, 'alertasPedidos'])->name('alertas-pedidos')->middleware('afterAuth:pedidos');;


Route::match(['get', 'post'],'/consumo-materiais', [App\Http\Controllers\ConsumoMateriaisController::class, 'index'])->name('consumo-materiais')->middleware('afterAuth:consumo-materiais');
Route::match(['get', 'post'],'/consumo-materiais-detalhes', [App\Http\Controllers\ConsumoMateriaisController::class, 'detalhes'])->name('consumo-materiais-detalhes')->middleware('afterAuth:consumo-materiais');

Route::match(['get', 'post'],'/maquinas', [App\Http\Controllers\MaquinasController::class, 'index'])->name('maquinas')->middleware('afterAuth:maquinas');

Route::match(['get', 'post'],'/producao-maquinas', [App\Http\Controllers\MaquinasController::class, 'producaoMaquinas'])->name('producao_maquinas')->middleware('afterAuth:producao-maquinas');

Route::match(['get', 'post'],'/orcamentos', [App\Http\Controllers\OrcamentosController::class, 'index'])->name('orcamentos')->middleware('afterAuth:orcamentos');
Route::match(['get', 'post'],'/alterar-orcamentos', [App\Http\Controllers\OrcamentosController::class, 'alterar'])->name('alterar-orcamentos')->middleware('afterAuth:orcamentos');

Route::match(['get', 'post'],'/relatorio-producao', [App\Http\Controllers\RelatoriosController::class, 'index'])->name('relatorio-producao')->middleware('afterAuth:relatorio-producao');

Route::match(['get', 'post'],'/paineis', [App\Http\Controllers\PaineisController::class, 'index'])->name('paineis')->middleware('afterAuth:paineis');

Route::match(['get', 'post'],'/manutencao-status', [App\Http\Controllers\ManutencaoProducaoController::class, 'pesquisar'])->name('manutencao-status');
Route::match(['post'],'/manutencao-producao-alterar-pedido', [App\Http\Controllers\ManutencaoProducaoController::class, 'ajaxAlterarPedido'])->name('manutencao-producao-alterar-pedido');
Route::match(['post'],'/manutencao-producao-salvar_caixas', [App\Http\Controllers\ManutencaoProducaoController::class, 'ajaxAlterarPedidoCaixa'])->name('manutencao-producao-salvar_caixas');
Route::match(['get', 'post'],'/atualiza-blank', [App\Http\Controllers\AtualizaFichatecnicaController::class, 'apiAtualizaBlank'])->name('atualiza-blank');
Route::match(['get', 'post'],'/imprimir-tag-estoque', [App\Http\Controllers\FilaImpressaoController::class, 'imprimirTagEstoque'])->name('imprimir-tag-estoque');
Route::match(['get', 'post'],'/incluir-fila-impressao', [App\Http\Controllers\FilaImpressaoController::class, 'incluirFilaImpressao'])->name('incluir-fila-impressao');

Route::match(['get', 'post'],'/estoque', [App\Http\Controllers\EstoqueController::class, 'index'])->name('estoque')->middleware('afterAuth:estoque');
Route::match(['get', 'post'],'/alterar-estoque', [App\Http\Controllers\EstoqueController::class, 'alterar'])->name('alterar-estoque')->middleware('afterAuth:estoque');
Route::match(['get', 'post'],'/incluir-estoque', [App\Http\Controllers\EstoqueController::class, 'incluir'])->name('incluir-estoque')->middleware('afterAuth:estoque');
Route::match(['get', 'post'],'/tela-baixa-estoque', [App\Http\Controllers\BaixaEstoqueController::class, 'telaBaixaEstoque'])->name('tela-baixa-estoque');
Route::match(['get', 'post'],'/baixar-estoque', [App\Http\Controllers\BaixaEstoqueController::class, 'baixarEstoque'])->name('baixar-estoque')->middleware('afterAuth:estoque');
Route::match(['get', 'post'],'/altera-qtde-estoque', [App\Http\Controllers\BaixaEstoqueController::class, 'alterarEstoque'])->name('altera-qtde-estoque')->middleware('afterAuth:estoque');
Route::match(['get', 'post'],'/relatorio-previsao-material', [App\Http\Controllers\RelatoriosController::class, 'relatorioPrevisaoMaterial'])->name('relatorio-previsao-material')->middleware('afterAuth:relatorio-previsao-material');

Route::match(['get', 'post'],'/perfis', [App\Http\Controllers\PerfisController::class, 'index'])->name('perfis')->middleware('afterAuth:perfis');
Route::match(['get', 'post'],'/alterar-perfis', [App\Http\Controllers\PerfisController::class, 'alterar'])->name('alterar-perfis')->middleware('afterAuth:perfis');
Route::match(['get', 'post'],'/incluir-perfis', [App\Http\Controllers\PerfisController::class, 'incluir'])->name('incluir-perfis')->middleware('afterAuth:perfis');

Route::match(['get', 'post'],'/categorias-materiais', [App\Http\Controllers\CategoriasMateriaisController::class, 'index'])->name('categorias-materiais')->middleware('afterAuth:categorias-materiais');
Route::match(['get', 'post'],'/alterar-categorias-materiais', [App\Http\Controllers\CategoriasMateriaisController::class, 'alterar'])->name('alterar-categorias-materiais')->middleware('afterAuth:categorias-materiais');
Route::match(['get', 'post'],'/incluir-categorias-materiais', [App\Http\Controllers\CategoriasMateriaisController::class, 'incluir'])->name('incluir-categorias-materiais')->middleware('afterAuth:categorias-materiais');
