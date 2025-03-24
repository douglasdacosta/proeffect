@section('content_header')
            <div class="form-group row">
                <h1 class="m-0 text-dark col-sm-6 col-form-label">Tela de envio de alertas ao cliente</h1>
            </div>
        @stop
        @section('content')
            @if (isset($pedidos))
                <form id="filtro" action="alertas-pedidos" method="post" data-parsley-validate=""
                    class="form-horizontal form-label-left">
                    @csrf <!--{{ csrf_field() }}-->
                    <table class="table table-sm table-striped  text-center">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col" title="Código do cliente">Cliente</th>
                                <th scope="col">Responsável</th>
                                <th scope="col">OS</th>
                                <th scope="col">Status do pedido</th>
                                <th scope="col" title="Data da entrega">Data Entrega</th>
                                <th scope="col" title="Alerta de dias">Alerta</th>
                                <th scope="col">Email</th>
                                <th scope="col">Enviar
                                    <input type="checkbox" class="checkbox_emails_todos" checked>
                                </th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($pedidos as $pedido)
                                <?php
                                $entrega = \Carbon\Carbon::createFromDate($pedido->data_entrega)->format('Y-m-d');
                                $hoje = date('Y-m-d');
                                $dias_alerta = \Carbon\Carbon::createFromDate($hoje)->diffInDays($entrega, false);
                                if ($dias_alerta < 6) {
                                    $class_dias_alerta = 'text-danger';
                                } else {
                                    $class_dias_alerta = 'text-primary';
                                }
                                ?>
                                <tr>
                                    <td>{{ $pedido->nome_cliente }}</td>
                                    <td>{{ $pedido->nome_contato }}</td>
                                    <td>{{ $pedido->os }}</td>
                                    <td>{{ $pedido->nome_status }}</td>
                                    <td>{{ \Carbon\Carbon::parse($pedido->data_entrega)->format('d/m/Y') }}</td>
                                    <td class="{{ $class_dias_alerta }}">{{ $dias_alerta }}</td>
                                    <td>{{ $pedido->email }}</td>
                                    <td>
                                        <input type="hidden" name="emails[]" value="{{ $pedido->id }}">
                                        <input type="checkbox" class="checkbox_emails" checked value="{{ $pedido->id }}"
                                            id="enviar" name="enviar[]">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-5">
                            <button class="btn btn-danger" onclick="window.history.back();" type="button">Cancelar</button>
                        </div>
                        <div class="col-sm-5">
                            <button type="submit" class="btn btn-primary">Enviar Email</button>
                        </div>
                    </div>
                </form>
            @else
                Nenhum alerta pendente de envio!
            @endif
        @stop