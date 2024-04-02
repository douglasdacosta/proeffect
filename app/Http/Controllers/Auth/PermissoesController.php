<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Permissoes;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class PermissoesController extends Controller
{

    /*
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }


    public function GetTodasPermissoes(){
        $permissoes = new Permissoes;
        $permissoes = $permissoes->get();
    }

    public function getPermissoesPorUsuario($user){

        $permissoes = new Permissoes;
        $permissoes = $permissoes->where('users_id', '=', $user)->get();
        return $permissoes;
    }

    public function getPermissoesUsuarioPorTela($tela, $user){
        $permissoes = new Permissoes;
        $permissoes = $permissoes->where('users_id', '=', $user)
        ->where('telas_id', '=', $tela)->get();
        return empty($permissoes[0]) ? false : true;
    }


}
