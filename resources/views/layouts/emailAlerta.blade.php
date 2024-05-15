<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Progresso do Pedido</title>
<style>

    @media only screen and (min-width: 2024px) {
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
        }
        .container p{
            font-size: 14px;
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

    }

    @media only screen and (min-width: 768px) and (max-width: 2023px) {

        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
        }

        .container p{
            font-size: 14px;
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

}

    @media only screen and (max-width: 767px) {
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .container p{
            font-size: 7px;
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

}


</style>
</head>
<body>
    <div style="display: block;justify-content: space-between;align-items: center;margin: 50px auto;width: 90%;position: relative;padding-left: 9%;" class="containerA">
        <h3>Prezado cliente, {{$pedidos[0]->nome_contato}}</h3>
        <p>Para sua comodidade, acompanhe em qual etapa encontra-se;</p>
        <p></p>
        <p><b>Previsão de liberação em {{\Carbon\Carbon::createFromDate($pedidos[0]->data_entrega)->format('d/m/Y')}}</b></p>
        <div>
                <div><span style="margin-right: 120px;" class="entenda">Entenda o status do seu pedido:</span> Concluído <span style="font-size: 13px;color: #007bff;background-color: #007bff;margin-right: 15px;" class="concluido">&#x2689;</span> Em andamento <span style="font-size: 13px;color: #f5e400;background-color: #f5e400;margin-right: 15px;" class="emandamento">&#x2689;</span>  Pendente <span style="font-size: 13px; color: #ccc; background-color: #ccc;" class="pendente">&#x2689;</span></div>        </div>
    </div>
<div style="margin: 50px auto;width: 70%;position: relative;" class="container">
    <?php $setado_andamento = false ?>
    @foreach ($statusEnvio as $key => $status)

        @if(in_array($pedidos[0]->status_id, $status['status_contenedor']))
            <img style="width: 99%;" src="{{"data:image/png;base64,".$status['imagem']}}">
        @endif
    @endforeach
</div>
<div style="background-color: #f9f9f9;padding: 20px;text-align: center;" class="footer">
    <p>Atenciosamente.</p>
    <p style="font-weight: bold;" class="address">Rua Antonio Ricardo, 55, Vila Lourdes, Carapicuíba - SP, CEP: 06397-145</p>
    <p><a href="mailto:Comercial3@eplax.com.br">Comercial3@eplax.com.br</a></p>
    <p>(11) 4181-8330</p>
    <p><a href="http://www.eplax.com.br">eplax.com.br</a></p>
</div>
</body>
</html>
