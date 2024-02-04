<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Status;
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
        $status = new Status();

        $id = !empty($request->input('id')) ? ($request->input('id')) : ( !empty($id) ? $id : false );

        if ($id) {
            $status = $status->where('id', '=', $id);
        }

        if (!empty($request->input('status'))){
            $status = $status->where('status', '=', $request->input('status'));
        }
        if ($request->input('nome') != '') {
        	$status = $status->where('nome', 'like', '%'.$request->input('nome').'%');
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
        } else {
            $status->nome = $request->input('nome');
        }

        $status->alertacliente = $request->input('alertacliente') == 'on' ? 1 : 0;
        $status->status = $request->input('status');
        $status->save();

        return $status->id;

}
}
