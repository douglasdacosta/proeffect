<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fichastecnicas;
use App\Models\Fichastecnicasitens;
use App\Models\Materiais;
use App\Providers\DateHelpers;
use Exception;
use Illuminate\Support\Facades\DB;

class AtualizaFichatecnicaController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    public function apiAtualizaBlank(Request $request) {

        try{
            $ep = $request->input('ep');
            $blank = $request->input('blank');
            $tempo = $request->input('tempo');

            $fichatecnica = new Fichastecnicas();
            $fichatecnica = $fichatecnica->where('ep', '=', $ep)
                                            ->where('status', '=', 'A')
                                            ->get();


            $Fichastecnicasitens = new Fichastecnicasitens();
            $Fichastecnicasitens = $Fichastecnicasitens->where('fichatecnica_id', '=', $fichatecnica[0]->id)
            ->where('blank', '=', $blank);
            $Fichastecnicasitens = $Fichastecnicasitens->update(['tempo_usinagem' => $tempo]);


            return response('ok', 200);
        }catch(Exception $e) {

            return response($e->getMessage(), 500);
        }



    }

}
