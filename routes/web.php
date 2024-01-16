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
    return view('welcome');
});

Auth::routes();

Route::get('admin/settings', [App\Http\Controllers\SettingsController::class, 'index'])->name('settings');
Route::post('admin/alterar-senha', [App\Http\Controllers\SettingsController::class, 'edit'])->name('alterar-senha');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/andamentos', [App\Http\Controllers\AndamentosController::class, 'index'])->name('andamentos');
Route::get('/eventos', [App\Http\Controllers\EventosController::class, 'index'])->name('eventos');
Route::get('/publicacoes', [App\Http\Controllers\PublicacoesController::class, 'index'])->name('publicacoes');