<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Progresso do Pedido</title>
<style>
%
    @media only screen and (min-width: 2024px) {
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
        }
        .containerA {
            display: block;
            justify-content: space-between;
            align-items: center;
            margin: 50px auto;
            width: 90%;
            position: relative;
            padding-left: 9%;
        }

        .pendente {
            font-size: 13px;
            color: #ccc;
            background-color: #ccc;
        }

        .concluido {
            font-size: 13px;
            color: #007bff;
            background-color: #007bff;
            margin-right: 15px;
        }

        .emandamento {
            font-size: 13px;
            color: #f5e400;
            background-color: #f5e400;
            margin-right: 15px;
        }

        .entenda{
            margin-right: 120px;
        }

        .container {
            margin: 50px auto;
            width: 70%;
            position: relative;
        }

        .container p{
            font-size: 14px;
        }

        .container img{
            width: 99%;
        }

        .circle {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: #ccc;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            margin-left: 34%;
            margin-bottom: 10px;
        }

        .line {

            width: calc(100% - 10px);
            height: 2px;
            background-color: #ccc;
            position: absolute;
            top: 24px;
            left: 88%;
            transform: translateX(-50%);
            z-index: -1;
        }

        .line:first-child {
            display: none;
        }

        .circle.active {
            background-color: #007bff;
            color: #fff;
        }

        .line.active {
            background-color: #007bff;
            color: #fff;
        }

        .circle.activeProcessando {
            background-color: #f5e400;
            color: #272424;
        }
        .line.activeProcessando {
            background-color: #f5e400;
            color: #272424;
        }

        .footer {
            background-color: #f9f9f9;
            padding: 20px;
            text-align: center;
        }
        .footer p {
            margin: 5px 0;
        }
        .footer a {
            color: #007bff;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }
        .footer .address {
            font-weight: bold;
        }
        .footer .contact-info {
            margin-top: 10px;
        }
    }

    @media only screen and (min-width: 768px) and (max-width: 2023px) {

        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
        }
        .containerA {
            display: block;
            justify-content: space-between;
            align-items: center;
            margin: 50px auto;
            width: 90%;
            position: relative;
            padding-left: 9%;
        }

        .pendente {
            font-size: 13px;
            color: #ccc;
            background-color: #ccc;
        }
        .concluido {
            font-size: 13px;
            color: #007bff;
            background-color: #007bff;
            margin-right: 15px;
        }
        .emandamento {
            font-size: 13px;
            color: #f5e400;
            background-color: #f5e400;
            margin-right: 15px;
        }
        .entenda{
            margin-right: 120px;
        }

        .container {
            margin: 50px auto;
            width: 70%;
            position: relative;
        }
        .container p{
            font-size: 14px;
        }

        .container img{
            width: 99%;
        }

        .circle {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: #ccc;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            margin-left: 34%;
            margin-bottom: 10px;
        }


        .line {

            width: calc(100% - 10px);
            height: 2px;
            background-color: #ccc;
            position: absolute;
            top: 24px;
            left: 88%;
            transform: translateX(-50%);
            z-index: -1;
        }
        .line:first-child {
            display: none;
        }
        .circle.active {
            background-color: #007bff;
            color: #fff;
        }
        .line.active {
            background-color: #007bff;
            color: #fff;
        }

        .circle.activeProcessando {
            background-color: #f5e400;
            color: #272424;
        }
        .line.activeProcessando {
            background-color: #f5e400;
            color: #272424;
        }

        .footer {
            background-color: #f9f9f9;
            padding: 20px;
            text-align: center;
        }
        .footer p {
            margin: 5px 0;
        }
        .footer a {
            color: #007bff;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }
        .footer .address {
            font-weight: bold;
        }
        .footer .contact-info {
            margin-top: 10px;
        }
}

    @media only screen and (max-width: 767px) {
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .containerA {
            display: block;
            justify-content: space-between;
            align-items: center;
            margin: 50px auto;
            width: 90%;
            position: relative;
            padding-left: 9%;
        }

        .pendente {
            font-size: 13px;
            color: #ccc;
            background-color: #ccc;
        }
        .concluido {
            font-size: 13px;
            color: #007bff;
            background-color: #007bff;
            margin-right: 15px;
        }
        .emandamento {
            font-size: 13px;
            color: #f5e400;
            background-color: #f5e400;
            margin-right: 15px;
        }
        .entenda{
            margin-right: 120px;
        }
        .container {
            margin: 50px auto;
            width: 70%;
            position: relative;
        }

        .container p{
            font-size: 7px;
        }

        .container img {
            width: 99%;
        }

        .circle {
            width: 25px;
            height: 25px;
            border-radius: 50%;
            background-color: #ccc;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            margin-left: 22%;
            margin-bottom: 10px;
        }

        .line {
            width: calc(100% - 2px);
            height: 2px;
            background-color: #ccc;
            position: absolute;
            top: 12px;
            left: 88%;
            transform: translateX(-50%);
            z-index: -1;
        }

        .line:first-child {
            display: none;
        }
        .circle.active {
            background-color: #007bff;
            color: #fff;
        }
        .line.active {
            background-color: #007bff;
            color: #fff;
        }

        .circle.activeProcessando {
            background-color: #f5e400;
            color: #272424;
        }
        .line.activeProcessando {
            background-color: #f5e400;
            color: #272424;
        }

        .footer {
            background-color: #f9f9f9;
            padding: 20px;
            text-align: center;
        }
        .footer p {
            margin: 5px 0;
        }
        .footer a {
            color: #007bff;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }
        .footer .address {
            font-weight: bold;
        }
        .footer .contact-info {
            margin-top: 10px;
        }
}


</style>
</head>
<body>
    <div class="containerA">
        <h3>Prezado cliente, {{$pedidos[0]->nome_contato}}</h3>
        <p>Para sua comodidade, acompanhe em qual etapa encontra-se;</p>
        <p></p>
        <p><b>Previsão de liberação em {{\Carbon\Carbon::createFromDate($pedidos[0]->data_entrega)->format('d/m/Y')}}</b></p>
        <div>
                <div><span class="entenda">Entenda o status do seu pedido:</span> Concluído <span class="concluido">&#x2689;</span> Em andamento <span class="emandamento">&#x2689;</span>  Pendente <span class="pendente">&#x2689;</span></div>
        </div>
    </div>
<div class="container">
    <?php $setado_andamento = false ?>
    @foreach ($statusEnvio as $key => $status)

        @if(in_array($pedidos[0]->status_id, $status['status_contenedor']))
            <img src="{{"data:image/png;base64,".$status['imagem']}}">
        @endif
    @endforeach
</div>
<div class="footer">
    <p>Atenciosamente.</p>
    <p class="address">Rua Antonio Ricardo, 55, Vila Lourdes, Carapicuíba - SP, CEP: 06397-145</p>
    <p><a href="mailto:Comercial3@eplax.com.br">Comercial3@eplax.com.br</a></p>
    <p>(11) 4181-8330</p>
    <p><a href="http://www.eplax.com.br">eplax.com.br</a></p>
</div>
</body>
</html>
