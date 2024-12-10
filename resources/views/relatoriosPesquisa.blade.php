
        <form id="filtro" action="relatorio-previsao-material" method="get" data-parsley-validate="" class="form-horizontal form-label-left" novalidate="">
            <div class="row" role="main">
                <div class="form-group row col-sm-12">
                    <label for="data" class="col-sm-2 col-form-label text-right">Data: de</label>
                    <div class="col-sm-2">
                        <input type="text" class="form-control mask_date" id="data" name="data" value="{{$request->input('data', '')}}"
                            placeholder="DD/MM/AAAA">
                    </div>
                    <label for="data_fim" class="col-form-label text-right regra_data_fim">até</label>
                    <div class="col-sm-2">
                        <input type="text" class="form-control mask_date regra_data_fim" id="data_fim" name="data_fim" value="{{$request->input('data_fim', '')}}"
                            placeholder="DD/MM/AAAA">
                    </div>
                </div>
                <div class="form-group row col-sm-12">
                    <label for="lote" class="col-sm-2 col-form-label text-right tipo_consulta ">Tipo de consulta</label>
                    <div class="col-sm-4">
                        <select class="form-control col-sm-12 tipo_consulta" id="tipo_consulta" name="tipo_consulta">
                            <option value="P" @if($request->input('tipo_consulta') == 'P'){{ ' selected '}}@else @endif>Previsto</option>
                            <option value="E" @if($request->input('tipo_consulta') == 'E'){{ ' selected '}}@else @endif>Realizado</option>
                            <option value="ED" @if($request->input('tipo_consulta') == 'ED'){{ ' selected '}}@else @endif>Estoque MP por Data</option>
                            <option value="V" @if($request->input('tipo_consulta') == 'V'){{ ' selected '}}@else @endif>Entrada de MP por período</option>
                            <option value="C" @if($request->input('tipo_consulta') == 'C'){{ ' selected '}}@else @endif>Consumo de MP por período</option>
                            <option value="EEC" @if($request->input('tipo_consulta') == 'EEC'){{ ' selected '}}@else @endif>Estoque x Entradas x Consumo por período</option>
                            <option value="CRF" @if($request->input('tipo_consulta') == 'CRF'){{ ' selected '}}@else @endif>Consumo x Realizado Ficha Técnica</option>
                            </option>
                        </select>
                    </div>
                    <label for="categorias" class="col-sm-1 col-form-label text-right campo_categorias">Categorias</label>
                    <div class="col-sm-2">
                        <select class="form-control col-sm-10 campo_categorias" id="campo_categorias" name="categorias">
                            @if(!empty($CategoriasMateriais))
                                <option value="">Todos</option>
                                @foreach ($CategoriasMateriais as $categoria)
                                    <option value="{{$categoria->id}}" @if($request->input('categorias') == $categoria->id){{ ' selected '}}@else @endif>{{$categoria->nome}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="form-group row col-sm-12">
                    <div class="col-md-5 themed-grid-col">
                        <div class="row">
                            <label for="ep" class="col-sm-5 col-form-label text-right status_pedido">Status do pedido</label>
                            <div class="col-sm-6 status_pedido" style="overflow-y: auto; height: 175px; border:1px solid #ced4da; border-radius: .25rem;">
                                <div class="right_col col-sm-6" role="main">
                                    @foreach ($status as $status)
                                    <div class="col-sm-6 form-check">
                                        <input class="form-check-input col-sm-4"  name="status_id[]" id="{{$status->id}}" type="checkbox"
                                        @if($status->id > 4) {{''}} @else {{ 'checked'}}@endif value="{{$status->id}}">
                                        <label class="form-check-label col-sm-6" style="white-space:nowrap" for="{{$status->id}}">{{$status->nome}}</label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
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
