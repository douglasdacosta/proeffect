<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Auth\ValidaPermissaoAcessoController;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AfterAuthMiddleware
{
    public function handle($request, Closure $next, $path = null)
    {

        // Deixe o próximo middleware ou controlador ser executado primeiro
        $response = $next($request);

        // Após o middleware de autenticação, capture o usuário autenticado
        if (Auth::check()) {
            $user = Auth::user();

            if(empty($path)) {
                $path = $request->getPathInfo();
            }

            $valida = new ValidaPermissaoAcessoController();
            $liberado = $valida->validaPathLiberado($path);

            if($liberado == false) {
                abort(403);
            }
            // Coloque aqui a lógica para o usuário autenticado
        }

        return $response;
    }
}
