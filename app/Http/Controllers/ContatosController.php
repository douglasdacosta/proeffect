<?php

namespace App\Http\Controllers;

use App\Mail\Contatos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContatosController extends Controller
{
    public function index()
    {
        return view('contatos');
    }

    public function store($dados)
    {
        try {
            $email = $dados['email_cliente'];

            Mail::to(users: $email)->send(mailable: new Contatos(
                data: [
                    'fromName' => $dados['fromName'],
                    'fromEmail' => $dados['fromEmail'],
                    'subject' => $dados['assunto'],
                    'message' => $dados['texto'],
                    'email_cliente' => $email,
                    'nome_cliente' => $dados['nome_cliente'],
                ]));

                return response('Sucesso', 200);

            } catch (\Throwable $th) {
                return response($th, 501);
            }
    }
}
