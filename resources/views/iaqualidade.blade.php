@extends('adminlte::page')

@section('title', 'Pro Effect')
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="js/jquery.mask.js"></script>
<script src="js/bootstrap.4.6.2.js"></script>
<script src="js/main_custom.js"></script>

@section('content_header')
<div class="form-group row">
    <h1 class="m-0 text-dark col-sm-11 col-form-label">Pesquisa de {{ $nome_tela }}</h1>
</div>
@stop

@section('content')
<div class="right_col" role="main">

    {{-- Mostrar mensagens de sucesso/erro --}}
    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Sucesso!</strong> {{ $message }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if ($message = Session::get('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Erro!</strong> {{ $message }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Formulário de Pesquisa -->
    <form id="filtro" action="iaqualidade" method="get" data-parsley-validate="" class="form-horizontal form-label-left" novalidate="">
        <div class="form-group row">
            <label for="data_entrega_de" class="col-sm-2 col-form-label">Data de Entrega</label>
            <div class="col-sm-3">
                <input type="date" id="data_entrega_de" name="data_entrega_de" class="form-control col-md-7 col-xs-12" value="@if (isset($request) && $request->input('data_entrega_de') != ''){{$request->input('data_entrega_de')}}@else @endif">
            </div>
            <label for="data_entrega_ate" class="col-sm-1 col-form-label">até</label>
            <div class="col-sm-3">
                <input type="date" id="data_entrega_ate" name="data_entrega_ate" class="form-control col-md-7 col-xs-12" value="@if (isset($request) && $request->input('data_entrega_ate') != ''){{$request->input('data_entrega_ate')}}@else @endif">
            </div>
        </div>

        <div class="form-group row">
            <label for="os" class="col-sm-1 col-form-label">OS</label>
            <div class="col-sm-1">
                <input type="text" id="os" name="os" class="form-control col-md-7 col-xs-12" value="@if (isset($request) && $request->input('os') != ''){{$request->input('os')}}@else @endif">
            </div>
            <label for="ep" class="col-sm-1 col-form-label">EP</label>
            <div class="col-sm-1">
                <input type="text" id="ep" name="ep" class="form-control col-md-7 col-xs-12" value="@if (isset($request) && $request->input('ep') != ''){{$request->input('ep')}}@else @endif">
            </div>
            <label for="quantidade" class="col-sm-1 col-form-label">Quantidade</label>
            <div class="col-sm-1">
                <input type="text" id="quantidade" name="quantidade" class="form-control col-md-7 col-xs-12" value="@if (isset($request) && $request->input('quantidade') != ''){{$request->input('quantidade')}}@else @endif">
            </div>
            <label for="responsavel_qualidade" class="col-sm-2 col-form-label">Resp Qualidade</label>
            <div class="col-sm-2">
                <input type="text" id="responsavel_qualidade" name="responsavel_qualidade" class="form-control col-md-7 col-xs-12" value="@if (isset($request) && $request->input('responsavel_qualidade') != ''){{$request->input('responsavel_qualidade')}}@else @endif">
            </div>
        </div>


        <div class="form-group row">

            <label for="status_lead" class="col-sm-2 col-form-label">Status do Lead</label>
            <div class="col-sm-2">
                <select class="form-control col-md-7 col-xs-12" id="status_lead" name="status_lead">
                    <option value="pendente" @if (!empty($request) && $request->input('status_lead') == 'pendente' || empty($request->input('status_lead'))){{ ' selected '}}@else @endif>Pendente</option>
                    <option value="removido" @if (!empty($request) && $request->input('status_lead') == 'removido'){{ ' selected '}}@else @endif>Removido</option>
                    <option value="finalizado" @if (!empty($request) && $request->input('status_lead') == 'finalizado'){{ ' selected '}}@else @endif>Finalizado</option>
                </select>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-sm-5">
                <button type="submit" class="btn btn-primary">Pesquisar</button>
            </div>
        </div>
    </form>

    <!-- Tabela de Resultados -->
    <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for=""></label>
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h4>Encontrados</h4>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <form id="formAcoes" method="POST" class="form-horizontal">
                        @csrf
                        <table class="table table-striped text-center">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">ID</th>
                                    <th>Data de Entrega</th>
                                    <th>Id CLiente</th>
                                    <th>OS</th>
                                    <th>EP</th>
                                    <th>Quantidade</th>
                                    <th>Responsável Qualidade</th>
                                    <th>Whats do Cliente</th>
                                    <th>Data envio</th>
                                    <th style="width: 80px;">Selecionar<br><input type="checkbox" id="selectAllEnviar"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($pedidos) && count($pedidos) > 0)
                                    @foreach ($pedidos as $pedido)
                                        <tr>
                                            <td>{{ $pedido->id }}</td>
                                            <td>{{ \Carbon\Carbon::parse($pedido->data_entrega)->format('d/m/Y') }}</td>
                                            <td><a href="/alterar-clientes?id={{ $pedido->pessoas_id }}" target="_blank">{{ $pedido->pessoas_id }}</a></td>
                                            <td>{{ $pedido->os }}</td>
                                            <td>{{ $pedido->ep }}</td>
                                            <td>{{ $pedido->qtde }}</td>
                                            <td>{{ $pedido->contato_pos_venda }}</td>
                                            @if($pedido->numero_whatsapp_pos_venda)
                                                <td class="mask_phone">
                                                    {{ $pedido->numero_whatsapp_pos_venda }}
                                                </td>
                                                @else
                                                <td>
                                                    {{""}}
                                                </td>
                                            @endif
                                            <td>
                                                @if($pedido->numero_whatsapp_pos_venda)
                                                    {{ !empty($pedido->datahora_envio_ultimo_lead) ? \Carbon\Carbon::parse($pedido->datahora_envio_ultimo_lead)->format('d/m/Y H:i:s') : '' }}</td>
                                                @else
                                                    {{ '' }}
                                                @endif
                                            <td>
                                                @if($pedido->numero_whatsapp_pos_venda)
                                                    <input type="checkbox" name="ids[]" value="{{ $pedido->id }}" class="checkbox-item checkbox-enviar">
                                                @else
                                                    {{ '' }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="10" class="text-center text-muted">Nenhum registro encontrado</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                        <div class="form-group row">
                            <div class="col-sm-3">
                               <button type="button" class="btn btn-success" id="btnEnviarLead" style="display:none;">
                                    <i class="fas fa-paper-plane"></i> Enviar Lead
                                </button>
                            </div>
                            <div class="col-sm-3">
                                <button type="button" class="btn btn-warning" id="btnRemoverLead" style="display:none;">
                                    <i class="fas fa-trash"></i> Remover Lead
                                </button>
                            </div>
                            <div class="col-sm-3">
                               <button type="button" class="btn btn-danger" id="btnFinalizarLead" style="display:none;">
                                    <i class="fas fa-check"></i> Finalizar Lead
                                </button>
                            </div>
                        </div>
                    </form>
                    <hr>
                </div>
            </div>
        </div>
    </div>

</div>

@stop

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.checkbox-item');
    const enviarCheckboxes = document.querySelectorAll('.checkbox-enviar');
    const selectAllEnviar = document.getElementById('selectAllEnviar');
    const btnEnviar = document.getElementById('btnEnviarLead');
    const btnRemover = document.getElementById('btnRemoverLead');
    const btnFinalizar = document.getElementById('btnFinalizarLead');
    const form = document.getElementById('formAcoes');

    // Selecionar todos para Enviar
    if (selectAllEnviar) {
        selectAllEnviar.addEventListener('change', function() {
            enviarCheckboxes.forEach(cb => { cb.checked = selectAllEnviar.checked; });
            toggleButtons();
        });
    }

    // Mostrar/ocultar botões quando checkboxes são selecionadas
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            toggleButtons();
            // Atualiza o select-all do Enviar para refletir estado
            if (selectAllEnviar) {
                const totalEnviar = enviarCheckboxes.length;
                const marcados = Array.from(enviarCheckboxes).filter(cb => cb.checked).length;
                selectAllEnviar.checked = totalEnviar > 0 && marcados === totalEnviar;
                selectAllEnviar.indeterminate = marcados > 0 && marcados < totalEnviar;
            }
        });
    });

    // Ações dos botões
    btnEnviar.addEventListener('click', function() {
        const ids = getSelectedIds();
        if (ids.length > 0) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids';
            input.value = ids.join(',');
            form.appendChild(input);

            form.action = '{{ route("iaqualidade.enviar") }}';
            form.submit();
        }
    });

    btnRemover.addEventListener('click', function() {
        const ids = getSelectedIds();
        if (ids.length > 0 && confirm('Tem certeza que deseja remover este(s) lead(s)?')) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids';
            input.value = ids.join(',');
            form.appendChild(input);

            form.action = '{{ route("iaqualidade.remover") }}';
            form.submit();
        }
    });

    btnFinalizar.addEventListener('click', function() {
        const ids = getSelectedIds();
        if (ids.length > 0 && confirm('Tem certeza que deseja finalizar este(s) lead(s)?')) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids';
            input.value = ids.join(',');
            form.appendChild(input);

            form.action = '{{ route("iaqualidade.finalizar") }}';
            form.submit();
        }
    });

    function getSelectedIds() {
        const ids = [];
        checkboxes.forEach(checkbox => {
            if (checkbox.checked) {
                ids.push(checkbox.value);
            }
        });
        // Remover duplicatas
        return [...new Set(ids)];
    }

    function toggleButtons() {
        const temSelecionado = Array.from(checkboxes).some(cb => cb.checked);
        btnEnviar.style.display = temSelecionado ? 'inline-block' : 'none';
        btnRemover.style.display = temSelecionado ? 'inline-block' : 'none';
        btnFinalizar.style.display = temSelecionado ? 'inline-block' : 'none';
    }
});
</script>
@endsection
