@extends('layouts.app')

<style type="text/css">
    .container_default {
        width: 100%;
        height: 100%;
        padding: 10px;
    }

    .container_default .table {
        font-size: 2em;
    }
</style>
<script type="text/javascript" > 

setTimeout(function () {
    location.reload();
    }, 3000);
</script>
@section('content')


    <div class="container_default" style="background-color: #afafaf;">   
        <div class="w-auto text-center">
            <h1>Painel de Montagem</h1>
        </div>
            
            <div class="w-auto"  style="min-height: 48%; background-color: #36a840;">
                <h1>Concluídos</h1> 
                <table class="table table-sm table-striped text-center" id="table_composicao">
                    <thead >
                        <tr>
                            <th scope="col">EP</th>
                            <th scope="col">OS</th>
                            <th scope="col">Qtde</th>
                            <th scope="col">Entrega</th>
                            <th scope="col">Alerta</th>
                            <th scope="col">Responsáveis</th>
                        </tr>
                    </thead>
                    <tbody>                        
                        <tr>
                            <td scope="col">234</td>
                            <td scope="col">23424</td>
                            <td scope="col">444</td>
                            <td scope="col">10/10/2024</td>
                            <td scope="col">55</td>
                            <td scope="col">João, Fernanda</td>
                        </tr>
                        <tr>
                            <td scope="col">4564</td>
                            <td scope="col">674</td>
                            <td scope="col">678</td>
                            <td scope="col">10/10/2024</td>
                            <td scope="col">7</td>
                            <td scope="col">Lucas, </td>
                        </tr>
                        <tr>
                            <td scope="col">234</td>
                            <td scope="col">23424</td>
                            <td scope="col">444</td>
                            <td scope="col">10/10/2024</td>
                            <td scope="col">55</td>
                            <td scope="col">João lucas, Maria </td>
                        </tr>
                        <tr>
                            <td scope="col">234</td>
                            <td scope="col">23424</td>
                            <td scope="col">444</td>
                            <td scope="col">10/10/2024</td>
                            <td scope="col">55</td>
                            <td scope="col">João ,  Fernanda</td>
                        </tr>
                        <tr>
                            <td scope="col">345345</td>
                            <td scope="col">456</td>
                            <td scope="col">456</td>
                            <td scope="col">10/10/2024</td>
                            <td scope="col">46</td>
                            <td scope="col"> lucas, Maria </td>
                        </tr>
                        <tr>
                            <td scope="col">56767</td>
                            <td scope="col">7575</td>
                            <td scope="col">675</td>
                            <td scope="col">10/10/2024</td>
                            <td scope="col">789</td>
                            <td scope="col">João lucas, Fernanda</td>
                        </tr>
                        <tr>
                            <td scope="col">56767</td>
                            <td scope="col">7575</td>
                            <td scope="col">675</td>
                            <td scope="col">10/10/2024</td>
                            <td scope="col">789</td>
                            <td scope="col">João lucas, Fernanda</td>
                        </tr>
                        <tr>
                            <td scope="col">56767</td>
                            <td scope="col">7575</td>
                            <td scope="col">675</td>
                            <td scope="col">10/10/2024</td>
                            <td scope="col">789</td>
                            <td scope="col">João lucas, Fernanda</td>
                        </tr>
                    </tbody>
                </table>


            </div>
            <div class="w-auto"  style="min-height: 47%; background-color: #ff7220;">
                <h1>Pendentes</h1> 
                <table class="table table-sm table-striped text-center" id="table_composicao">
                    <thead >
                        <tr>
                            <th scope="col">EP</th>
                            <th scope="col">OS</th>
                            <th scope="col">Qtde</th>
                            <th scope="col">Entrega</th>
                            <th scope="col">Alerta</th>
                            <th scope="col">Responsáveis</th>
                        </tr>
                    </thead>
                    <tbody>                        
                        <tr>
                            <td scope="col">234</td>
                            <td scope="col">23424</td>
                            <td scope="col">444</td>
                            <td scope="col">10/10/2024</td>
                            <td scope="col">55</td>
                            <td scope="col">João, Fernanda</td>
                        </tr>
                        <tr>
                            <td scope="col">4564</td>
                            <td scope="col">674</td>
                            <td scope="col">678</td>
                            <td scope="col">10/10/2024</td>
                            <td scope="col">7</td>
                            <td scope="col">Lucas, </td>
                        </tr>
                        <tr>
                            <td scope="col">234</td>
                            <td scope="col">23424</td>
                            <td scope="col">444</td>
                            <td scope="col">10/10/2024</td>
                            <td scope="col">55</td>
                            <td scope="col">João lucas, Maria </td>
                        </tr>
                        <tr>
                            <td scope="col">234</td>
                            <td scope="col">23424</td>
                            <td scope="col">444</td>
                            <td scope="col">10/10/2024</td>
                            <td scope="col">55</td>
                            <td scope="col">João ,  Fernanda</td>
                        </tr>
                        <tr>
                            <td scope="col">345345</td>
                            <td scope="col">456</td>
                            <td scope="col">456</td>
                            <td scope="col">10/10/2024</td>
                            <td scope="col">46</td>
                            <td scope="col"> lucas, Maria </td>
                        </tr>
                        <tr>
                            <td scope="col">56767</td>
                            <td scope="col">7575</td>
                            <td scope="col">675</td>
                            <td scope="col">10/10/2024</td>
                            <td scope="col">789</td>
                            <td scope="col">João lucas, Fernanda</td>
                        </tr>
                    </tbody>
                </table>
            </div>
    </div>

@stop
