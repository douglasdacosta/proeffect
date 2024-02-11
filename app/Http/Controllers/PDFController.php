<?php

namespace App\Http\Controllers;
use PDF;

use Illuminate\Http\Request;

class PDFController extends Controller
{
    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

     public function generatePDF($data, $view)
     {

         $pdf = PDF::loadView($view, $data)->setPaper('a4', 'landscape');

         return $pdf->stream('ordendeservico.pdf');
     }
}
