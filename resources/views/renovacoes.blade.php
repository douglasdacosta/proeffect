@extends('adminlte::page')

@section('title', 'Pro Effect')

@section('adminlte_css')
    <link rel="stylesheet" href="{{ asset('DataTables/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main_style.css') }}">
@endsection

<?php
    $palheta_cores = [1 => '#ff003d', 2 => '#ee7e4c', 3 => '#8f639f', 4 => '#94c5a5', 5 => '#ead56c', 6 => '#0fbab7', 7 => '#f7c41f', 8 => '#898b75', 9 =>
    '#c1d9d0', 10 => '#da8f72', 11 => '#00caf8', 12 => '#ffe792', 13 => '#9a5071', 14 => '#4a8583', 15 => '#f7c41f', 16 => '#898b75', 17 => '#c1d9d0'];
?>


@if(isset($tela) and $tela == 'pesquisa')
    @section('content_header')
    <div class="form-group row">
        <h1 class="m-0 text-dark col-sm-11 col-form-label">Pesquisa de {{ $nome_tela }}</h1>
        <div class="col-sm-1">
            @include('layouts.nav-open-incluir', ['rotaIncluir => $rotaIncluir'])
        </div>
    </div>
    @stop
    @section('content')
    <div class="right_col" role="main">

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

        <form id="filtro" action="renovacoes" method="get" data-parsley-validate="" class="form-horizontal form-label-left" novalidate="">
            <div class="form-group row">
                <label for="departamento" class="col-sm-2 col-form-label">Departamentos</label>
                <div class="col-sm-3">
                    <select class="form-control" id="departamento" name="departamento">
                        <option value="">Todos</option>
                        @if (isset($perfis))
                            @foreach ($perfis as $perfil)
                                <option value="{{ $perfil->id }}" @if (isset($request) && $request->input('departamento') == $perfil->id){{ ' selected '}}@else @endif>{{ $perfil->nome }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <label for="vencimento" class="col-sm-2 col-form-label">Vencimento</label>
                <div class="col-sm-2">
                    <input type="date" id="vencimento" name="vencimento" class="form-control" value="@if (isset($request) && $request->input('vencimento') != ''){{$request->input('vencimento')}}@else @endif">
                </div>
                <label for="status" class="col-sm-1 col-form-label"></label>
                <select class="form-control col-sm-2" id="status" name="status">
                    <option value="P" @if (isset($request) && $request->input('status') == 'P'){{ ' selected '}}@else @endif>Pendente</option>
                    <option value="F" @if (isset($request) && $request->input('status') == 'F'){{ ' selected '}}@else @endif>Finalizado</option>
                </select>
            </div>
            <div class="form-group row">
                <div class="col-sm-5">
                    <button type="submit" class="btn btn-primary">Pesquisar</button>
                </div>
                <div class="col-sm-5">
                </div>
            </div>
        </form>
        <div class="form-group">
          <label class="control-label col-md-3 col-sm-3 col-xs-12" for=""></label>
          <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
              <div class="x_title">
                <h4>Encontrados</h4>
                <div class="clearfix"></div>
              </div>
              <div class="x_content">
                @if(isset($renovacoes) && $renovacoes->count())
                    @php
                        $departamento_cores = [];
                        $cor_index = 0;
                    @endphp
                    @foreach ($renovacoes->groupBy('departamento_nome') as $departamento_nome => $lista)
                        @php
                            if (!isset($departamento_cores[$departamento_nome])) {
                                $departamento_id = optional($lista->first())->departamento_id;
                                if (!empty($palheta_cores[$departamento_id])) {
                                    $departamento_cores[$departamento_nome] = $palheta_cores[$departamento_id];
                                } else {
                                    $chave = ($cor_index % count($palheta_cores)) + 1;
                                    $departamento_cores[$departamento_nome] = $palheta_cores[$chave] ?? '#f7f7f7';
                                    $cor_index++;
                                }
                            }
                            $cor_header = $departamento_cores[$departamento_nome];
                        @endphp
                        <div class="mb-4">
                            <table class="table table-striped text-center table_renovacoes">
                              <thead>
                                <tr style="background-color: {{ $cor_header }}; ">
                                    <th colspan="13" class="text-left"><h5 class="mb-2 font-weight-bold">Departamento: {{ $departamento_nome }}</h5></th>
                                </tr>

                                <tr style="background-color: {{ $cor_header }}; ">
                                  <th>ID</th>
                                  <th>Data da abertura</th>
                                  <th>Departamento</th>
                                  <th>Descrição</th>
                                  <th>Responsável</th>
                                  <th>Número de documento</th>
                                  <th>Período de renovação</th>
                                  <th>Data do Vencimento</th>
                                  <th>Início da renovação</th>
                                  <th>Previsão</th>
                                  <th>Alerta</th>
                                  <th>Data finalizado</th>
                                  <th>Finalizar</th>
                                </tr>
                              </thead>
                              <tbody>
                                @foreach ($lista as $renovacao)
                                    <tr>
                                        <th scope="row"><a href="{{ route('alterar-renovacoes', ['id' => $renovacao->id]) }}">{{ $renovacao->id }}</a></th>
                                        <td>@if($renovacao->data_abertura) {{ \Carbon\Carbon::parse($renovacao->data_abertura)->format('d/m/Y') }} @else {{ '' }} @endif</td>
                                        <td>{{ $renovacao->departamento_nome }}</td>
                                        <td title="{{ $renovacao->descricao }}">{{ \Illuminate\Support\Str::limit($renovacao->descricao, 25, '...') }}</td>
                                        <td>{{ $renovacao->responsavel }}</td>
                                        <td>{{ $renovacao->numero_documento }}</td>
                                        <td>{{ $renovacao->periodo_renovacao }}</td>
                                        <td>@if($renovacao->data_vencimento) {{ \Carbon\Carbon::parse($renovacao->data_vencimento)->format('d/m/Y') }} @else {{ '' }} @endif</td>
                                        <td>{{ $renovacao->inicio_renovacao ? \Carbon\Carbon::parse($renovacao->inicio_renovacao)->format('d/m/Y') : '' }}</td>
                                        <td>
                                            @if($renovacao->previsao == 'mensal')
                                                Mensal
                                            @elseif($renovacao->previsao == 'anual')
                                                Anual
                                            @elseif($renovacao->previsao == 'outros')
                                                Outros
                                            @else
                                                {{ $renovacao->previsao }}
                                            @endif
                                        </td>
                                        <td class="@if($renovacao->em_alerta) alerta_limitador @endif" title="@if($renovacao->em_alerta) Início da renovação é hoje ou vencimento já passou @endif">
                                            @if($renovacao->data_vencimento || $renovacao->inicio_renovacao)
                                                @if($renovacao->alerta_direcao)
                                                    <i class="fas fa-arrow-{{ $renovacao->alerta_direcao }} text-{{ $renovacao->alerta_cor }}"></i>
                                                @endif
                                                {{ '' }}
                                            @endif
                                        </td>
                                        <td>@if($renovacao->data_finalizado) {{ \Carbon\Carbon::parse($renovacao->data_finalizado)->format('d/m/Y') }} @else {{ '' }} @endif</td>
                                        <td>
                                            @if($renovacao->status == 'F')
                                                <span class="badge badge-success">Finalizado</span>
                                            @else
                                                <button type="button" class="btn btn-sm btn-success btn-finalizar-renovacao" data-id="{{ $renovacao->id }}">Finalizar</button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                              </tbody>
                            </table>
                        </div>
                    @endforeach
                    <div class="clearfix"></div>
                @else
                    <div class="text-center text-muted">Nenhum registro encontrado</div>
                @endif
              </div>
            </div>
          </div>
        </div>
    </div>

    <div id="modal_finalizar_renovacao" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="width: 600px">
                <div class="modal-header">
                    <h5 class="modal-title">Finalizar renovação</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form_finalizar_renovacao" method="post" action="finalizar-renovacoes">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="id" id="finalizar_renovacao_id" value="">
                        <p>Deseja finalizar a renovação?</p>
                        <div class="form-group">
                            <label for="gerar_nova_renovacao">Deseja gerar uma nova renovação?</label>
                            <input type="checkbox" id="gerar_nova_renovacao" name="gerar_nova" value="1">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Finalizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @stop
@else
@section('content')
        @if($tela == 'alterar')
            @section('content_header')
                <h1 class="m-0 text-dark">Alteração de {{ $nome_tela }}</h1>
            @stop
            <form id="alterar" action="{{$rotaAlterar}}" data-parsley-validate="" class="form-horizontal form-label-left" method="post">
            <div class="form-group row">
                <label for="id" class="col-sm-2 col-form-label">Id</label>
                <div class="col-sm-2">
                <input type="text" id="id" name="id" class="form-control col-md-7 col-xs-12" readonly="true" value="@if (isset($renovacao[0]->id)){{$renovacao[0]->id}}@else{{''}}@endif">
                </div>
            </div>
        @else
            @section('content_header')
                <h1 class="m-0 text-dark">Inclusão de {{ $nome_tela }}</h1>
            @stop
            <form id="incluir" action="{{$rotaIncluir}}" data-parsley-validate="" class="form-horizontal form-label-left" method="post">
        @endif
            @csrf <!--{{ csrf_field() }}-->
            <div class="form-group row">
                <label for="data_abertura" class="col-sm-2 col-form-label">Data da abertura</label>
                <div class="col-sm-3">
                    <input type="text" class="form-control mask_date_time" id="data_abertura" name="data_abertura" value="@if (isset($renovacao[0]->data_abertura)){{ \Carbon\Carbon::parse($renovacao[0]->data_abertura)->format('d/m/Y H:i:s') }}@else{{ date('d/m/Y H:i:s') }}@endif">
                </div>
            </div>
            <div class="form-group row">
                <label for="departamento_id" class="col-sm-2 col-form-label">Departamento</label>
                <div class="col-sm-3">
                    <select class="form-control" id="departamento_id" name="departamento_id" required>
                        <option value=""></option>
                        @if (isset($perfis))
                            @foreach ($perfis as $perfil)
                                <option value="{{ $perfil->id }}" @if (isset($renovacao[0]->departamento_id) && $renovacao[0]->departamento_id == $perfil->id){{ ' selected '}}@else @endif>{{ $perfil->nome }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="descricao" class="col-sm-2 col-form-label">Descrição</label>
                <div class="col-sm-6">
                    <textarea class="form-control" id="descricao" name="descricao" rows="3">@if (isset($renovacao[0]->descricao)){{$renovacao[0]->descricao}}@else{{''}}@endif</textarea>
                </div>
            </div>
            <div class="form-group row">
                <label for="responsavel" class="col-sm-2 col-form-label">Responsável</label>
                <div class="col-sm-3">
                    <input type="text" class="form-control" id="responsavel" name="responsavel" value="@if (isset($renovacao[0]->responsavel)){{$renovacao[0]->responsavel}}@else{{''}}@endif">
                </div>
            </div>
            <div class="form-group row">
                <label for="numero_documento" class="col-sm-2 col-form-label">Número de documento</label>
                <div class="col-sm-3">
                    <input type="text" class="form-control" id="numero_documento" name="numero_documento" value="@if (isset($renovacao[0]->numero_documento)){{$renovacao[0]->numero_documento}}@else{{''}}@endif">
                </div>
            </div>
            <div class="form-group row">
                <label for="periodo_renovacao" class="col-sm-2 col-form-label">Período de renovação</label>
                <div class="col-sm-3">
                    <input type="text" class="form-control" id="periodo_renovacao" name="periodo_renovacao" value="@if (isset($renovacao[0]->periodo_renovacao)){{$renovacao[0]->periodo_renovacao}}@else{{''}}@endif">
                </div>
            </div>
            <div class="form-group row">
                <label for="data_vencimento" class="col-sm-2 col-form-label">Data do Vencimento</label>
                <div class="col-sm-3">
                    <input type="text" class="form-control mask_date_time" id="data_vencimento" name="data_vencimento" value="@if (isset($renovacao[0]->data_vencimento)){{ \Carbon\Carbon::parse($renovacao[0]->data_vencimento)->format('d/m/Y H:i:s') }}@else{{''}}@endif">
                </div>
            </div>
            <div class="form-group row">
                <label for="inicio_renovacao" class="col-sm-2 col-form-label">Início da renovação</label>
                <div class="col-sm-3">
                    <input type="text" class="form-control mask_date_time" id="inicio_renovacao" name="inicio_renovacao" value="@if (isset($renovacao[0]->inicio_renovacao)){{ \Carbon\Carbon::parse($renovacao[0]->inicio_renovacao)->format('d/m/Y H:i:s') }}@else{{''}}@endif">
                </div>
            </div>
            <div class="form-group row">
                <label for="previsao" class="col-sm-2 col-form-label">Previsão</label>
                <div class="col-sm-3">
                    <select class="form-control" id="previsao" name="previsao">
                        <option value="">Selecione</option>
                        <option value="mensal" @if (isset($renovacao[0]->previsao) && $renovacao[0]->previsao == 'mensal'){{ ' selected '}}@endif>Mensal</option>
                        <option value="anual" @if (isset($renovacao[0]->previsao) && $renovacao[0]->previsao == 'anual'){{ ' selected '}}@endif>Anual</option>
                        <option value="outros" @if (isset($renovacao[0]->previsao) && $renovacao[0]->previsao == 'outros'){{ ' selected '}}@endif>Outros</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="data_finalizado" class="col-sm-2 col-form-label">Data finalizado</label>
                <div class="col-sm-3">
                    <input type="text" class="form-control mask_date_time" id="data_finalizado" name="data_finalizado" value="@if (isset($renovacao[0]->data_finalizado)){{ \Carbon\Carbon::parse($renovacao[0]->data_finalizado)->format('d/m/Y H:i:s') }}@else{{''}}@endif">
                </div>
            </div>
            <div class="form-group row">
                <label for="status" class="col-sm-2 col-form-label"></label>
                <select class="form-control col-md-2" id="status" name="status">
                    <option value="P" @if (isset($renovacao[0]->status) && $renovacao[0]->status == 'P'){{ ' selected '}}@else @endif>Pendente</option>
                    <option value="F" @if (isset($renovacao[0]->status) && $renovacao[0]->status == 'F'){{ ' selected '}}@else @endif>Finalizado</option>
                </select>
            </div>
            @if (!empty($historicos))
                <div class="form-group row">
                    <label for="observacao" class="col-sm-2 col-form-label">Histórico</label>
                    <div class="col-sm-8">
                        <div class="d-flex p-2 bd-highlight overflow-auto">
                            @foreach ($historicos as $historico)
                                {{ '[' . \Carbon\Carbon::parse($historico->created_at)->format('d/m/Y H:i:s') . '] ' . $historico->historico }}</br>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
            <div class="form-group row">
                <div class="col-sm-5">
                    <button class="btn btn-danger" onclick="window.history.back();" type="button">Cancelar</button>
                </div>
                <div class="col-sm-5">
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </div>
        </form>

    @stop
@endif

@section('js')
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="{{ asset('DataTables/datatables.min.js') }}"></script>
    <script src="js/jquery.mask.js"></script>
    <script src="js/bootstrap.4.6.2.js"></script>
    <script src="js/main_custom.js"></script>
    <script src="{{ asset('js/renovacoes.js') }}"></script>
@endsection
