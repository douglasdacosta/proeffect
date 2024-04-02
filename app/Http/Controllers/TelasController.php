<?php

namespace App\Http\Controllers;

use App\Models\CategoriaTela;
use App\Models\Pedidos;
use App\Models\Telas;
use Illuminate\Http\Response;

class TelasController extends Controller
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

    public function getTelas()
    {
        $telas = new Telas();
        $telas = $telas->all();

        $data  = [
            'telas' => $telas
        ];
        return response($data);
    }

    public function getCategoriaTelas()
    {
        $categoriaTelas = new CategoriaTela();
        $categoriaTelas = $categoriaTelas->all();

        $data  = [
            'categoriaTelas' => $categoriaTelas
        ];
        return response($data);
    }

    public function getTelasPorId($id)
    {
        $Telas = new Telas();
        $Telas = $Telas->where('id','=',$id)->get();

        $data  = [
            'Telas' => $Telas
        ];
        return response($data);
    }


}
