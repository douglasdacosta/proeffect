<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use DateTime;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Cache;

class ApiERPController extends Controller
{

    public $token;
    /*
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }


    public function Login(){
        if (Cache::has('TOKEN')) {
            $this->token = Cache::get('TOKEN');
            return $this->token;
        } else {
            $url = env('URL_ERP').'api/Auth/login';

            $client = new Client();
            $headers = [
            'accept' => 'text/plain',
            'Authorization' => '35197047-818b-4a01-b421-693ac7d13d8d',
            'Content-Type' => 'application/json-patch+json'
            ];

            $API_USER = env('API_USER');
            $API_PASSWORD = env('API_PASSWORD');
            $API_TOKEN = env('API_TOKEN');

            $body = [
                'usuario' => $API_USER,
                'idVendedor' => 0,
                'senha' => $API_PASSWORD,
                'email' => 'user@example.com',
                'applicationId' => 0,
                'applicationToken' => $API_TOKEN,
            ];

            $body = json_encode($body);

            $request = new Request('POST', $url, $headers, $body);

            $response = $client->sendAsync($request)->wait();

            $body = $response->getBody();
            $resposta = json_decode($body, true);

            $agora = new DateTime($resposta['created']);
            $dataHoraDesejada = new DateTime($resposta['expiration']);
            $diferenca = $agora->diff($dataHoraDesejada);
            $totalMinutos = $diferenca->days * 24 * 60 + $diferenca->h * 60 + $diferenca->i;
            $this->token=$resposta['token'];
            Cache::put('TOKEN', $this->token, $totalMinutos);

            return $this->token;
        }

    }

    public function getToken(){
        return $this->Login();
    }

    public function getVendasByStatus(){
        $client = new Client();

        $this->token = Cache::get('TOKEN');

        $headers = [
        'accept' => 'text/plain',
        'Authorization' => "Bearer $this->token"
        ];

        $NumRecords = 1000;
        $page = 1;
        $url = env('URL_ERP')."api/VendaV2?Status=7968&NumRecords=$NumRecords&page=$page";

        $request = new Request('GET', $url, $headers);

        $response = $client->sendAsync($request)->wait();
        $body = $response->getBody();
        $resposta = json_decode($body, true);
        return $resposta;
    }



    public function getVendasByOS($os){
        try {
            $client = new Client();
            $headers = [
                'accept' => 'text/plain',
                'Authorization' => "Bearer $this->token"
            ];

            $url = env('URL_ERP')."api/VendaV2/completa/$os";
            $request = new Request('GET', $url, $headers);
            $response = $client->sendAsync($request)->wait();
            $body = $response->getBody();
            $resposta = json_decode($body, true);

            return $resposta;

        } catch (\Throwable $th) {
            info($th->getMessage());
            return false;
        }

    }
    public function getVendedorById($vendedor_id){
        try {
            $client = new Client();
            $headers = [
                'accept' => 'text/plain',
                'Authorization' => "Bearer $this->token"
            ];

            $url = env('URL_ERP')."api/Vendedor/id/$vendedor_id";
            $request = new Request('GET', $url, $headers);
            $response = $client->sendAsync($request)->wait();
            $body = $response->getBody();
            $resposta = json_decode($body, true);
            return $resposta;
        } catch (\Throwable $th) {
            info($th->getMessage());
            return false;
        }

    }




}
