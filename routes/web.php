<?php

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

Route::get('admin/settings', [App\Http\Controllers\SettingsController::class, 'index'])->name('settings');
Route::post('admin/alterar-senha', [App\Http\Controllers\SettingsController::class, 'edit'])->name('alterar-senha');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::match(['get', 'post'],'/materiais', [App\Http\Controllers\MateriaisController::class, 'index'])->name('materiais');
Route::match(['get', 'post'],'/alterar-materiais', [App\Http\Controllers\MateriaisController::class, 'alterar'])->name('alterar-materiais');
Route::match(['get', 'post'],'/incluir-materiais', [App\Http\Controllers\MateriaisController::class, 'incluir'])->name('incluir-materiais');

Route::match(['get', 'post'],'/clientes', [App\Http\Controllers\PessoasController::class, 'index'])->name('clientes');
Route::match(['get', 'post'],'/alterar-clientes', [App\Http\Controllers\PessoasController::class, 'alterar'])->name('alterar-clientes');
Route::match(['get', 'post'],'/incluir-clientes', [App\Http\Controllers\PessoasController::class, 'incluir'])->name('incluir-clientes');

Route::match(['get', 'post'],'/status', [App\Http\Controllers\StatusController::class, 'index'])->name('status');
Route::match(['get', 'post'],'/alterar-status', [App\Http\Controllers\StatusController::class, 'alterar'])->name('alterar-status');
Route::match(['get', 'post'],'/incluir-status', [App\Http\Controllers\StatusController::class, 'incluir'])->name('incluir-status');

Route::match(['get', 'post'],'/fichatecnica', [App\Http\Controllers\FichatecnicaController::class, 'index'])->name('fichatecnica');
Route::match(['get', 'post'],'/alterar-fichatecnica', [App\Http\Controllers\FichatecnicaController::class, 'alterar'])->name('alterar-fichatecnica');
Route::match(['get', 'post'],'/incluir-fichatecnica', [App\Http\Controllers\FichatecnicaController::class, 'incluir'])->name('incluir-fichatecnica');
Route::match(['get', 'post'],'/ajax-fichatecnica', [App\Http\Controllers\AjaxfichatecnicaController::class, 'buscarMateriais'])->name('ajax-fichatecnica');

Route::match(['get', 'post'],'/ajax-getProducao', [App\Http\Controllers\HomeController::class, 'getProducao'])->name('ajax-getProducao');
Route::match(['get', 'post'],'/followup', [App\Http\Controllers\PedidosController::class, 'followup'])->name('followup');
Route::match(['get', 'post'],'/followup-geral', [App\Http\Controllers\PedidosController::class, 'followup'])->name('followup-geral');
Route::match(['get', 'post'],'/imprimir-os', [App\Http\Controllers\PedidosController::class, 'imprimirOS'])->name('imprimirOS');
Route::match(['get', 'post'],'/imprimir-mp', [App\Http\Controllers\PedidosController::class, 'imprimirMP'])->name('imprimirMP');
Route::match(['get', 'post'],'/followup-detalhes', [App\Http\Controllers\PedidosController::class, 'followupDetalhes'])->name('followup-detalhes');
Route::match(['get', 'post'],'/pedidos', [App\Http\Controllers\PedidosController::class, 'index'])->name('pedidos');
Route::match(['get', 'post'],'/alterar-pedidos', [App\Http\Controllers\PedidosController::class, 'alterar'])->name('alterar-pedidos');
Route::match(['get', 'post'],'/incluir-pedidos', [App\Http\Controllers\PedidosController::class, 'incluir'])->name('incluir-pedidos');
Route::match(['post'],'/alterar-pedidos-ajax', [App\Http\Controllers\PedidosController::class, 'ajaxAlterar'])->name('alterar-pedidos-ajax');
Route::match(['post'],'/calcular-orcamento-ajax', [App\Http\Controllers\AjaxOrcamentosController::class, 'ajaxCalculaOrcamentos'])->name('calcular-orcamento-ajax');

Route::match(['get',],'/contatos', [App\Http\Controllers\ContatosController::class, 'index'])->name('contatos');
Route::match(['post',],'/enviar-contatos', [App\Http\Controllers\ContatosController::class, 'store'])->name('envia-contatos');
Route::match(['get', 'post'],'/alertas-pedidos', [App\Http\Controllers\PedidosController::class, 'alertasPedidos'])->name('alertas-pedidos');


Route::match(['get', 'post'],'/consumo-materiais', [App\Http\Controllers\ConsumoMateriaisController::class, 'index'])->name('consumo-materiais');
Route::match(['get', 'post'],'/consumo-materiais-detalhes', [App\Http\Controllers\ConsumoMateriaisController::class, 'detalhes'])->name('consumo-materiais-detalhes');
Route::match(['get', 'post'],'/maquinas', [App\Http\Controllers\MaquinasController::class, 'index'])->name('maquinas');

Route::match(['get', 'post'],'/orcamentos', [App\Http\Controllers\OrcamentosController::class, 'index'])->name('orcamentos');
Route::match(['get', 'post'],'/alterar-orcamentos', [App\Http\Controllers\OrcamentosController::class, 'alterar'])->name('alterar-orcamentos');

Route::match(['get', 'post'],'/teste', [App\Http\Controllers\TestesController::class, 'index'])->name('teste');


