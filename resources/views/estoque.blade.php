@extends('adminlte::page')

@section('title', 'Pro Effect')
<script src="../vendor/jquery/jquery.min.js?cache={{time()}}"></script>
<script src="js/bootstrap.4.6.2.js?cache={{time()}}"></script>
<script src="js/jquery.mask.js"></script>
<script src="js/main_custom.js"></script>
<script src="DataTables/datatables.min.js"></script>
<link  rel="stylesheet" src="DataTables/datatables.min.css"></link>
<link rel="stylesheet" href="{{asset('css/main_style.css')}}" />
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

        <div id='modal_imprime_etiqueta'  class="modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content" style="width: 99%">
                    <div class="modal-header">
                    <h5 class="modal-title" id='texto_status_caixas'>Impressão de etiqueta</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>
                    <div class="modal-body" >
                        <label for="id" class="col-sm-9 col-form-label">Confirma impressão de etiqueta?</label>
                        <div class="form-group row">
                            <label for="id" class="col-sm-4 col-form-label">Qtde de etiqueta</label>
                            <input type="text" class='form-control col-sm-2 sonumeros' name="qtde_etiqueta" id="qtde_etiqueta" value=""/>
                        </div>
                        <input type="hidden" name="estoque_id" id="estoque_id" value=""/>
                    </div>
                    <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="salva_fila_impressao" data-dismiss="modal" >Salvar</button>
                    </div>
                </div>
            </div>
        </div>
        <form id="filtro" action="estoque" method="get" data-parsley-validate="" class="form-horizontal form-label-left" novalidate="">
            <div class="form-group row">
                <label for="lote" class="col-sm-2 col-form-label text-right">Lote</label>
                <div class="col-sm-2">
                    <input type="text" id="lote" name="lote" class="form-control col-md-7 col-xs-12" value="@if (isset($request) && $request->input('lote') != ''){{$request->input('lote')}}@else @endif">
                </div>
                <label for="data" class="col-sm-2 col-form-label text-right">Data: de</label>
                <div class="col-sm-2">
                    <input type="text" class="form-control mask_date" id="data" name="data"
                        placeholder="DD/MM/AAAA">
                </div>
                <label for="data_fim" class="col-form-label text-right">até</label>
                <div class="col-sm-2">
                    <input type="text" class="form-control mask_date" id="data_fim" name="data_fim"
                        placeholder="DD/MM/AAAA">
                </div>
            </div>
            <div class="form-group row">
                <label for="status_estoque" class="col-sm-2 col-form-label text-right">Status do estoque</label>
                <select class="form-control col-md-2" id="status_estoque" name="status_estoque">
                    <option value="A" @if (isset($request) && $request->input('status_estoque') == 'A'){{ ' selected '}}@else @endif>Em estoque</option>
                    <option value="F" @if (isset($request) && $request->input('status_estoque')  == 'F'){{ ' selected '}}@else @endif>Finalizado</option>
                </select>

                <label for="Material" class="col-sm-2 col-form-label text-right">Matéria prima</label>
                <div class="col-sm-2">
                    <select class="form-control" id="material_id" name="material_id">
                        <option value=""></option>
                        @if (isset($materiais))
                            @foreach ($materiais as $material)
                                <option
                                @if(isset($estoque[0]->material_id) && $material->id == $estoque[0]->material_id) selected="selected" @else {{''}}@endif
                                value="{{ $material->id }}">{{ $material->codigo . ' - ' . $material->material }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <label for="status" class="col-sm-1 col-form-label">&nbsp;</label>
                <div class="col-sm-2">
                    <select class="form-control " id="status" name="status">
                        <option value="A" @if (isset($request) && $request->input('status') == 'A') {{ ' selected ' }}@else @endif>Ativo
                        </option>
                        <option value="I" @if (isset($request) && $request->input('status') == 'I') {{ ' selected ' }}@else @endif>Inativo
                        </option>
                    </select>
                </div>
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
                <table id="table_estoque" class="table table-striped  text-center ">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Fornecedor</th>
                      <th>Lote</th>
                      <th>Data</th>
                      <th>Material</th>
                      <th>Estoque comprado</th>
                      <th>Estoque atual</th>
                      <th>Pacote</th>
                      <th>Estoque mínimo</th>
                      <th>Alerta</th>
                      <th>Previsão</th>
                      <th>Etiqueta</th>
                    </tr>
                  </thead>
                  <tbody>
                  @if(isset($array_estoque))
                        @foreach ($array_estoque as $item_estoque)

                            <tr style="@if (isset($item_estoque['alerta_baixa_errada']) && $item_estoque['alerta_baixa_errada'] =='1'){{ ' background-color: rgb(233, 76, 76) '}}@else @endif">
                                <th data-sortable='true' data-field="id" scope="row"><a href={{ URL::route($rotaAlterar, array('id' => $item_estoque['id'] )) }}>{{$item_estoque['id']}}</a></th>
                                <td data-sortable='true' data-field="fornecedor" nowrap >{{$item_estoque['fornecedor']}}</td>
                                <td data-sortable='true' data-field="lote">{{$item_estoque['lote']}}</td>
                                <td data-sortable='true' data-field="data" >{{Carbon\Carbon::parse($item_estoque['data'])->format('d/m/Y')}}</td>
                                <td data-sortable='true' data-field="material" nowrap>{{$item_estoque['material']}}</td>
                                <td data-sortable='true' data-field="estoque_comprado" >{{$item_estoque['estoque_comprado']}}</td>
                                <td data-sortable='true' data-field="estoque_atual" >{{$item_estoque['estoque_atual']}}</td>
                                <td data-sortable='true' data-field="pacote" >{{$item_estoque['pacote']}}</td>
                                <td data-sortable='true' data-field="estoqu_minimo" >{{$item_estoque['estoque_minimo']}}</td>
                                <td data-sortable='true' data-field="alerta" >@if($item_estoque['alerta'] == 0) <i class="text-danger fas fa-arrow-down"></i> @else <i class="text-success fas fa-arrow-up"></i> @endif</td>
                                <td data-sortable='true' data-field="previsao" title="{{$item_estoque['previsao_meses']}} meses">{{$item_estoque['previsao_meses'] }}</td>
                                <th  scope="row">
                                    <a href="#">
                                        <span data-id="{{$item_estoque['id']}}" style="cursor:pointer;" class="fa fa-print adiciona_fila_impressao"></span>
                                    </a>
                            </th>
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

            <div id='modal_estoque'  class="modal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content" style="width: 99%">
                        <div class="modal-header">
                        <h5 class="modal-title" id='texto_status_caixas'>Alteração de estoque</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        </div>
                        <div class="modal-body" >
                            <label for="id" class="col-sm-9 col-form-label">Confirma alterar a quantidade de estoque?</label>
                            <input type="hidden" name="acao_estoque" id="acao_estoque" value=""/>
                        </div>
                        <div class="modal-footer">
                        <button type="button" class="btn btn-success" id="salva_estoque" data-dismiss="modal" >Salvar</button>
                        </div>
                    </div>
                </div>
            </div>
            <div id='modal_imprime_etiqueta'  class="modal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content" style="width: 99%">
                        <div class="modal-header">
                        <h5 class="modal-title" id='texto_status_caixas'>Alteração de estoque</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        </div>
                        <div class="modal-body" >
                            <label for="id" class="col-sm-9 col-form-label">Confirma alterar a quantidade de estoque?</label>
                            <input type="hidden" name="acao_estoque" id="acao_estoque" value=""/>
                        </div>
                        <div class="modal-footer">
                        <button type="button" class="btn btn-success" id="salva_estoque" data-dismiss="modal" >Salvar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <label for="Material" class="col-sm-2 col-form-label">Matéria prima</label>
                <div class="col-sm-6">
                    <select class="form-control material_id_estoque" id="material_id" name="material_id">
                        <option value=""></option>
                        @if (isset($materiais))
                            @foreach ($materiais as $material)
                                @if(isset($estoque[0]->material_id) && $material->id == $estoque[0]->material_id) selected="selected" @else {{''}}@endif
                                value="{{ $material->id }}">{{ $material->codigo . ' - ' . $material->material }}
                                <option
                                @if(isset($estoque[0]->material_id) && $material->id == $estoque[0]->material_id) selected="selected" @else {{''}}@endif
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
                                @if(isset($estoque[0]->fornecedor_id) &&  $fornecedor->id == $estoque[0]->fornecedor_id) selected="selected" @else {{''}}@endif
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
                    <input type="text" readonly class="form-control" id="lote" name="lote" value="@if (isset($estoque[0]->lote)){{$estoque[0]->lote}}@else{{''}}@endif">
                </div>
                <label for="MO" class="col-sm-2 col-form-label  text-center">MO</label>
                <div class="col-sm-2">
                    <input type="checkbox" class="form-control form-check-input ckeckbox_mo" id="MO" name="MO"
                    @if (isset($estoque[0]->MO) && $estoque[0]->MO == 1) {{'checked'}} @else{{''}}@endif
                    value="1">
                </div>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col col-lg-6">
                        <div class="form-group row">
                            <label for="valor_unitario" class="col-sm-4 col-form-label">Valor unitário </label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control mask_valor" id="valor_unitario" name="valor_unitario"  value="@if (isset($estoque[0]->valor_unitario)){{ number_format($estoque[0]->valor_unitario,2, ',','.')}}@else{{''}}@endif">
                            </div>
                            <div class="col-sm-4">
                                <input type="text" class="form-control mask_valor identificador_mo" id="valor_mo" placeholder="valor de MO" name="valor_mo"  value="@if (isset($estoque[0]->valor_mo)){{ number_format($estoque[0]->valor_mo,2, ',','.')}}@else{{''}}@endif">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="valor" class="col-sm-4 col-form-label">Valor kg</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control mask_valor" id="valor" name="valor"  value="@if (isset($estoque[0]->valor)){{ number_format($estoque[0]->valor,2, ',','.')}}@else{{''}}@endif">
                            </div>
                            <div class="col-sm-4">
                                <input type="text" class="form-control mask_valor identificador_mo" id="valor_kg_mo" name="valor_kg_mo"  value="@if (isset($estoque[0]->valor_kg_mo)){{ number_format($estoque[0]->valor_kg_mo,2, ',','.')}}@else{{''}}@endif">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="imposto" class="col-sm-4 col-form-label">Imposto</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control mask_valor" id="imposto" name="imposto"  value="@if (isset($estoque[0]->imposto)){{ number_format($estoque[0]->imposto,2, ',','.')}}@else{{''}}@endif">
                            </div>
                            <div class="col-sm-4">
                                <input type="text" class="form-control mask_valor identificador_mo" id="imposto_mo" name="imposto_mo"  value="@if (isset($estoque[0]->imposto_mo)){{ number_format($estoque[0]->imposto_mo,2, ',','.')}}@else{{''}}@endif">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="peso" class="col-sm-4 col-form-label">Peso(Kg)</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control kg calcula_peso_chapa" id="peso_material" name="peso_material" value="@if (isset($estoque[0]->peso_material)){{number_format($estoque[0]->peso_material, 3, ',', '.');}}@else{{''}}@endif">
                            </div>
                            <div class="col-sm-4">
                                <input type="text" class="form-control kg calcula_peso_chapa identificador_mo" id="peso_material_mo" name="peso_material_mo" value="@if (isset($estoque[0]->peso_material_mo)){{number_format($estoque[0]->peso_material_mo, 3, ',', '.');}}@else{{''}}@endif">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="total" class="col-sm-4 col-form-label">Total</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control mask_valor" id="total" name="total"  value="@if (isset($estoque[0]->total)){{ number_format($estoque[0]->total,2, ',','.')}}@else{{''}}@endif">
                            </div>
                            <div class="col-sm-4">
                                <input type="text" class="form-control mask_valor identificador_mo" id="total_mo" name="total_mo"  value="@if (isset($estoque[0]->total_mo)){{ number_format($estoque[0]->total_mo,2, ',','.')}}@else{{''}}@endif">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="qtde_chapa_peca" class="col-sm-4 col-form-label">Qtde chapa/peça</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control kg calcula_peso_chapa" id="qtde_chapa_peca" name="qtde_chapa_peca" value="@if (isset($estoque[0]->qtde_chapa_peca)){{$estoque[0]->qtde_chapa_peca}}@else{{''}}@endif">
                            </div>
                            <div class="col-sm-4">
                                <input type="text" class="form-control kg calcula_peso_chapa identificador_mo" id="qtde_chapa_peca_mo" name="qtde_chapa_peca_mo" value="@if (isset($estoque[0]->qtde_chapa_peca_mo)){{$estoque[0]->qtde_chapa_peca_mo}}@else{{''}}@endif">
                            </div>

                        </div>
                        <div class="form-group row">
                            <label for="qtde_por_pacote" class="col-sm-4 col-form-label">Qtde de pacote</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control kg calcula_peso_chapa" id="qtde_por_pacote" name="qtde_por_pacote" value="@if (isset($estoque[0]->qtde_por_pacote)){{$estoque[0]->qtde_por_pacote}}@else{{''}}@endif">
                            </div>
                            <div class="col-sm-4">
                                <input type="text" class="form-control kg calcula_peso_chapa identificador_mo" id="qtde_por_pacote_mo" name="qtde_por_pacote_mo" value="@if (isset($estoque[0]->qtde_por_pacote_mo)){{$estoque[0]->qtde_por_pacote_mo}}@else{{''}}@endif">
                            </div>
                        </div>
                    </div>
                    <div class="col col-sm-5">
                        <label for="qtde_por_pacote" class="col-sm-4 col-form-label">Observacões</label>
                        <div class="">
                            <textarea class="form-control" id="observacaoes" name="observacaoes" rows="11">@if (isset($estoque[0]->observacaoes)){{$estoque[0]->observacaoes}}@else{{''}}@endif</textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label for="total_chapa_peca" class="col-sm-2 col-form-label">Total chapa/peças</label>
                <div class="col-sm-3">
                    <input type="text" readonly class="form-control kg" id="total_chapa_peca" name="total_chapa_peca" value="@if (isset($estoque[0]->qtde_por_pacote) && isset($estoque[0]->qtde_por_pacote)){{$estoque[0]->qtde_chapa_peca * $estoque[0]->qtde_por_pacote}}@else{{''}}@endif">
                </div>
            </div>

            <div class="form-group row">
                <label for="peso" class="col-sm-2 col-form-label">Peso total(Kg)</label>
                <div class="col-sm-2">
                <input type="text" class="form-control kg" readonly id="peso" name="peso" value="@if (isset($materiais[0]->peso)){{number_format($materiais[0]->peso * ($estoque[0]->qtde_chapa_peca * $estoque[0]->qtde_por_pacote), 3, '.', '.');}}@else{{''}}@endif">
                </div>
            </div>
            @if($tela == 'alterar')
                <div class="form-group row">
                    <label for="status" class="col-sm-2 col-form-label">Baixa/Devolução </label>
                    <div class="col-sm-2">
                        <input type="text" pattern="[0-9]+$" class="form-control sonumeros col-sm-6" id="qtde_alteracao_estoque" name="qtde_alteracao_estoque" value="">
                    </div>
                    <div class="col-sm-2">
                        <button id='adicionar_ao_estoque' type="button" data-acao='adicionar' class="btn btn-success altera_estoque">Devolver estoque</button>
                    </div>
                    <div class="col-sm-2">
                        <button id='remover_do_estoque' type="button" data-acao='remover' class="btn btn-warning altera_estoque">Baixar estoque</button>
                    </div>
                </div>
            @endif
            <div class="form-group row">
                <label for="status_estoque" class="col-sm-2 col-form-label">Status do estoque</label>
                <select class="form-control custom-select col-md-2 " id="status_estoque" name="status_estoque">
                    <option value="A" @if (isset($estoque[0]->status_estoque) && $estoque[0]->status_estoque == 'A'){{ ' selected '}}@else @endif>Em andamento</option>
                    <option value="F" @if (isset($estoque[0]->status_estoque) && $estoque[0]->status_estoque =='F'){{ ' selected '}}@else @endif>Finalizado</option>
                </select>
            </div>
            <div class="form-group row " >
                <label for="alerta_baixa_errada" class="col-sm-2 col-form-label">Alerta baixa indevida</label>
                <select class="form-control custom-select col-md-2 " id="alerta_baixa_errada" name="alerta_baixa_errada">
                    <option value="0" @if (isset($estoque[0]->alerta_baixa_errada) && $estoque[0]->alerta_baixa_errada == '0'){{ ' selected '}}@else @endif>Sem alerta</option>
                    <option value="1" @if (isset($estoque[0]->alerta_baixa_errada) && $estoque[0]->alerta_baixa_errada =='1'){{ ' selected '}}@else @endif>Em alerta</option>
                </select>
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
                    <label for="observacao" class="col-sm-2 col-form-label">Histórico do Estoque</label>
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
            @if (!empty($historicosMateriais))
                <div class="form-group row">
                    <label for="observacao" class="col-sm-2 col-form-label">Histórico dos Materiais</label>
                    <div class="col-sm-8">
                        <div class="d-flex p-2 bd-highlight overflow-auto">
                            @foreach ($historicosMateriais as $historicoMateriais)
                                {{ '[' . \Carbon\Carbon::parse($historicoMateriais->created_at)->format('d/m/Y H:i:s') . '] ' . $historicoMateriais->historico }}</br>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </form>

    @stop
@endif
