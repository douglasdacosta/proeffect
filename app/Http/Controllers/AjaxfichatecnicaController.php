<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Materiais;
use PhpParser\Node\Expr\Cast\Array_;
use PhpParser\Node\Expr\Cast\Object_;

class AjaxfichatecnicaController extends Controller
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
     * Create a new controller instance.
     *
     *
     */
    public function buscarMateriais(Request $request) {

        $materiais = new Materiais();

        if($request->input('id')) {
            $material= $materiais->where('id', '=', $request->input('id'))->get();
            return response($material);
        }

        return response(null);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getAllMateriais() {
        $Materiais = new Materiais();
        return $Materiais->where('status', '=', 'A')->get();

    }
}
