<?php

namespace App\Http\Controllers;
use PDF;


class PDFController extends Controller
{
    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

     public function generatePDF($data, $view, $type ='view')
     {
        if($type == 'view') {
            $pdf = PDF::loadView($view, $data)->setPaper('a4', 'landscape');
            return $pdf->stream('ordendeservico.pdf');

        } else{
             return $data;
        }

     }
}
