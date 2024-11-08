<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\ValidaPermissaoAcessoController;
use App\Models\PerfilSubmenus;
use App\Models\Perfis;
use App\Models\SubMenus;
use Illuminate\Http\Request;

class PerfisController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        // $this->middleware('afterAuth:perfis');
    }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $perfis = new Perfis();

        $id = !empty($request->input('id')) ? ($request->input('id')) : ( !empty($id) ? $id : false );

        if ($id) {
            $perfis = $perfis->where('id', '=', $id);
        }

        if (!empty($request->input('status'))){
            $perfis = $perfis->where('status', '=', $request->input('status'));
        } else {
            $perfis = $perfis->where('status', '=', 'A');
        }

        if ($request->input('nome') != '') {
        	$perfis = $perfis->where('nome', 'like', '%'.$request->input('nome').'%');
        }

        $perfis = $perfis->get();
        $tela = 'pesquisa';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'perfis',
				'perfis'=> $perfis,
				'request' => $request,
				'rotaIncluir' => 'incluir-perfis',
				'rotaAlterar' => 'alterar-perfis'
			);

        return view('perfis', $data);
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

    		$perfis_id = $this->salva($request);

	    	return redirect()->route('perfis', [ 'id' => $perfis_id ] );

    	}


        $telas = new SubMenus();
        $telas = $telas->where('status', '=', 'A')->get();

        $tela = 'incluir';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'perfis',
                'telas' => $telas,
				'request' => $request,
				'rotaIncluir' => 'incluir-perfis',
				'rotaAlterar' => 'alterar-perfis'
			);

        return view('perfis', $data);
    }

     /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function alterar(Request $request)
    {

        $perfis = new Perfis();


        $perfis= $perfis->where('id', '=', $request->input('id'))->get();

		$metodo = $request->method();
		if ($metodo == 'POST') {

    		$perfis_id = $this->salva($request);

	    	return redirect()->route('perfis', [ 'id' => $perfis_id ] );

    	}

        $telas = new SubMenus();
        $telas = $telas->where('status', '=', 'A')->get();

        $PerfilSubmenus = new PerfilSubmenus();
        $PerfilSubmenus = $PerfilSubmenus->where('perfil_id', '=', $request->input('id'))->get()->toArray();

        foreach ($telas as $key => $value) {

            $value->checked = false;
            foreach ($PerfilSubmenus as $key => $value2) {
                if ($value->id == $value2['submenu_id']) {
                    $value->checked = true;
                }
            }
        }

        $tela = 'alterar';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'perfis',
                'telas' => $telas,
				'perfis'=> $perfis,
				'request' => $request,
				'rotaIncluir' => 'incluir-perfis',
				'rotaAlterar' => 'alterar-perfis'
			);

        return view('perfis', $data);
    }

    public function salva($request) {
        $perfis = new Perfis();

        if($request->input('id')) {
            $perfis = $perfis::find($request->input('id'));
        }

        $perfis->nome = $request->input('nome');
        $perfis->status = $request->input('status');

        $perfis->save();


        // Obtém o array de IDs das telas a partir da requisição
        $array_telas = $request->input('telas');

        // Busca os submenus que correspondem aos IDs em $array_telas
        $subMenus = SubMenus::whereIn('id', $array_telas)->get();

        // Sincroniza os submenus encontrados com o perfil
        $perfis->subMenus()->sync($subMenus->pluck('id'));



        return $perfis->id;

}
}
