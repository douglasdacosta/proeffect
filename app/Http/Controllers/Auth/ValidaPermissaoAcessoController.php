<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Funcionarios;
use App\Models\PerfilSubmenus;
use App\Models\Perfis;
use App\Models\SubMenus;

class ValidaPermissaoAcessoController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    private function fixPath($path = '') {
        return ltrim($path, '/');
    }


    public function GetSubMenuLiberado($path = '') {

        $user = \Auth::user();
        $users = new Funcionarios();
        $users = $users->where('id', '=', $user->id)->first();
        $perfil = $users->perfil;

        $perfis = new Perfis();
        $perfis = $perfis->where('id', '=', $perfil)->first();

        $subMenus = new SubMenus();



        $perfis_menu = new PerfilSubmenus();
        $perfis_menu = $perfis_menu
                       ->select('submenu_id')
                       ->where('perfil_id', '=', $perfil);

        if($path != '') {
            $subMenus = $subMenus->where('rota', '=', $this->fixPath($path))->first();
            $perfis_menu = $perfis_menu->where('submenu_id', '=', $subMenus->id);
        }

        $perfis_menu = $perfis_menu->get()
                       ->pluck('submenu_id')
                       ->toArray();

        return $perfis_menu;

    }

    Public function validaPathLiberado($path) {


        $path = $this->GetSubMenuLiberado($path);
        //dd($path);
        if(count($path) > 0) {
            return true;
        }

        return false;
    }


}
