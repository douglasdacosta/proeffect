<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\status;
class StatusController extends Controller
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

        $id = !empty($request->input('id')) ? ($request->input('id')) : ( !empty($id) ? $id : false );
        
        $status = new Status();

        if ($id) {
        	$status = $status->where('id', '=', $id);
        }

        if ($request->input('nome') != '') {
        	$status = $status->where('status', 'like', '%'.$request->input('nome').'%');
        }

        $status = $status->get();
        $tela = 'pesquisa';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'status',
				'status'=> $status,
				'request' => $request,
				'rotaIncluir' => 'incluir-status',
				'rotaAlterar' => 'alterar-status'
			);

        return view('status', $data);
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

    		$status_id = $this->salva($request);

	    	return redirect()->route('status', [ 'id' => $status_id ] );

    	}
        $tela = 'incluir';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'status',
				'request' => $request,
				'rotaIncluir' => 'incluir-status',
				'rotaAlterar' => 'alterar-status'
			);

        return view('status', $data);
    }

     /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function alterar(Request $request)
    {

        $status = new Status();
        

        $status= $status->where('id', '=', $request->input('id'))->get();

		$metodo = $request->method();
		if ($metodo == 'POST') {

    		$status_id = $this->salva($request);

	    	return redirect()->route('status', [ 'id' => $status_id ] );

    	}
        $tela = 'alterar';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'status',
				'status'=> $status,
				'request' => $request,
				'rotaIncluir' => 'incluir-status',
				'rotaAlterar' => 'alterar-status'
			);

        return view('status', $data);
    }

    public function salva($request) {
        $status = new Status();

        if($request->input('id')) {
            $status = $status::find($request->input('id'));
        }

        $status->nome = $request->input('nome');
        $status->alertacliente = $request->input('alertacliente') == 'on' ? 1 : 0;
        $status->status = $request->input('status') == 'on' ? 1 : 0;
        $status->save();

        return $status->id;

}
}
