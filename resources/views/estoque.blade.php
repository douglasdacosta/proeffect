@extends('adminlte::page')

@section('title', 'Pro Effect')
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="js/jquery.mask.js"></script>
<script src="js/main_custom.js"></script>

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

        <form id="filtro" action="estoque" method="get" data-parsley-validate="" class="form-horizontal form-label-left" novalidate="">
            <div class="form-group row">
                <label for="id" class="col-sm-2 col-form-label">Código</label>
                <div class="col-sm-2">
                    <input type="text" id="id" name="id" class="form-control col-md-7 col-xs-12" value="@if (isset($request) && $request->input('id') != ''){{$request->input('codigo')}}@else @endif">
                </div>
                <label for="status" class="col-sm-1 col-form-label"></label>
                <select class="form-control col-md-1" id="status" name="status">
                    <option value="A" @if (isset($request) && $request->input('status') == 'A'){{ ' selected '}}@else @endif>Ativo</option>
                    <option value="I" @if (isset($request) && $request->input('status')  == 'I'){{ ' selected '}}@else @endif>Inativo</option>
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
                <table class="table table-striped  text-center ">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Material</th>
                      <th>Data</th>
                      <th>Valor</th>
                    </tr>
                  </thead>
                  <tbody>
                  @if(isset($estoque))
                        @foreach ($estoque as $item_estoque)
                            <tr>
                            <th scope="row"><a href={{ URL::route($rotaAlterar, array('id' => $item_estoque->id )) }}>{{$item_estoque->id}}</a></th>
                              <td>{{$item_estoque->material}}</td>
                              <td>{{Carbon\Carbon::parse($item_estoque->data)->format('d/m/Y')}}</td>
                              <td>{{$item_estoque->qtde_chapa_peca}}</td>

                            </tr>
                        @endforeach
                    @endif
                  </tbody>
                </table>
              </div>
            </div>
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
                <input type="text" id="id" name="id" class="form-control col-md-7 col-xs-12" readonly="true" value="@if (isset($estoque[0]->id)){{$estoque[0]->id}}@else{{''}}@endif">
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
                <label for="Material" class="col-sm-2 col-form-label">Matéria prima</label>
                <div class="col-sm-6">
                    <select class="form-control" id="material_id" name="material_id">
                        <option value=""></option>
                        @if (isset($materiais))
                            @foreach ($materiais as $material)
                                <option
                                @if($material->id == $estoque[0]->material_id) selected="selected" @else {{''}}@endif
                                value="{{ $material->id }}">{{ $material->codigo . ' - ' . $material->material }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="data" class="col-sm-2 col-form-label">Data</label>
                <div class="col-sm-2">
                    <input type="text" class="form-control mask_date" id="data" name="data"
                        value="@if (isset($estoque[0]->data)) {{ Carbon\Carbon::parse($estoque[0]->data)->format('d/m/Y') }} @else {{ '' }} @endif">
                </div>
            </div>
            <div class="form-group row">
                <label for="nota_fiscal" class="col-sm-2 col-form-label">NF</label>
                <div class="col-sm-2">
                    <input type="text" class="form-control" id="nota_fiscal" name="nota_fiscal" value="@if (isset($estoque[0]->nota_fiscal)){{$estoque[0]->nota_fiscal}}@else{{''}}@endif">
                </div>
            </div>
            <div class="form-group row">
                <label for="fornecedor_id" class="col-sm-2 col-form-label">Fornecedor</label>
                <div class="col-sm-6">
                    <select class="form-control" id="fornecedor_id" name="fornecedor_id">
                        <option value=""></option>
                        @if (isset($fornecedores))
                            @foreach ($fornecedores as $fornecedor)
                                <option
                                @if($fornecedor->id == $estoque[0]->fornecedor_id) selected="selected" @else {{''}}@endif
                                value="{{ $fornecedor->id }}">{{ $fornecedor->id . ' - ' . $fornecedor->nome_cliente }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="lote" class="col-sm-2 col-form-label">Lote</label>
                <div class="col-sm-2">
                    <input type="text" class="form-control" id="lote" name="lote" value="@if (isset($estoque[0]->lote)){{$estoque[0]->lote}}@else{{''}}@endif">
                </div>
            </div>
            <div class="form-group row">
                <label for="valor_unitario" class="col-sm-2 col-form-label">Valor unitário </label>
                <div class="col-sm-2">
                    <input type="text" class="form-control mask_valor" id="valor_unitario" name="valor_unitario"  value="@if (isset($estoque[0]->valor_unitario)){{ number_format($estoque[0]->valor_unitario,2, ',','.')}}@else{{''}}@endif">
                </div>
            </div>
            <div class="form-group row">
                <label for="valor" class="col-sm-2 col-form-label">Valor</label>
                <div class="col-sm-2">
                    <input type="text" class="form-control mask_valor" id="valor" name="valor"  value="@if (isset($estoque[0]->valor)){{ number_format($estoque[0]->valor,2, ',','.')}}@else{{''}}@endif">
                </div>
            </div>
            <div class="form-group row">
                <label for="imposto" class="col-sm-2 col-form-label">Imposto</label>
                <div class="col-sm-2">
                    <input type="text" class="form-control mask_valor" id="imposto" name="imposto"  value="@if (isset($estoque[0]->imposto)){{ number_format($estoque[0]->imposto,2, ',','.')}}@else{{''}}@endif">
                </div>
            </div>
            <div class="form-group row">
                <label for="total" class="col-sm-2 col-form-label">Total</label>
                <div class="col-sm-2">
                    <input type="text" class="form-control mask_valor" id="total" name="total"  value="@if (isset($estoque[0]->total)){{ number_format($estoque[0]->total,2, ',','.')}}@else{{''}}@endif">
                </div>
            </div>
            <div class="form-group row">
                <label for="VD" class="col-sm-2 col-form-label">VD</label>
                <div class="col-sm-2">
                    <input type="checkbox" class="form-control form-check-input" id="VD" name="VD"
                    @if (isset($estoque[0]->VD) && $estoque[0]->VD == 1) {{'checked'}} @else{{''}}@endif
                    value="1">
                </div>
            </div>
            <div class="form-group row">
                <label for="MO" class="col-sm-2 col-form-label">MO</label>
                <div class="col-sm-2">
                    <input type="checkbox" class="form-control form-check-input" id="MO" name="MO"
                    @if (isset($estoque[0]->MO) && $estoque[0]->MO == 1) {{'checked'}} @else{{''}}@endif
                    value="1">
                </div>
            </div>

            <div class="form-group row">
                <label for="qtde_chapa_peca" class="col-sm-2 col-form-label">Qtde chapa/peça</label>
                <div class="col-sm-2">
                <input type="text" pattern="[0-9]+$" class="form-control sonumeros" id="qtde_chapa_peca" name="qtde_chapa_peca" value="@if (isset($estoque[0]->qtde_chapa_peca)){{$estoque[0]->qtde_chapa_peca}}@else{{''}}@endif">
                </div>
            </div>
            <div class="form-group row">
                <label for="qtde_por_pacote" class="col-sm-2 col-form-label">Qtde por pacote</label>
                <div class="col-sm-2">
                <input type="text" pattern="[0-9]+$" class="form-control sonumeros" id="qtde_por_pacote" name="qtde_por_pacote" value="@if (isset($estoque[0]->qtde_por_pacote)){{$estoque[0]->qtde_por_pacote}}@else{{''}}@endif">
                </div>
            </div>
            <div class="form-group row">
                <label for="status" class="col-sm-2 col-form-label">&nbsp;</label>
                <select class="form-control custom-select col-md-1 " id="status" name="status">
                    <option value="A" @if (isset($estoque[0]->status) && $estoque[0]->status == 'A'){{ ' selected '}}@else @endif>Ativo</option>
                    <option value="I" @if (isset($estoque[0]->status) && $estoque[0]->status =='I'){{ ' selected '}}@else @endif>Inativo</option>
                </select>
            </div>
            @if (!empty($historicos))
                <div class="form-group row">
                    <label for="observacao" class="col-sm-2 col-form-label">Histórico</label>
                    <div class="col-sm-8">
                        <div class="d-flex p-2 bd-highlight overflow-auto">
                            @foreach ($historicos as $historico)
                                {{ '[' . \Carbon\Carbon::parse($historico->created_at)->format('d/m/Y h:i:s') . '] ' . $historico->historico }}</br>
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
