@extends('layouts.app')

<style type="text/css">
    .container_default {
        width: 100%;
        height: 100%;
        padding: 10px;
    }

    .container_default .table {
        font-size: 1.2em;
    }
</style>

@section('content')

    <div class="container_default" style="background-color:   #f8fafc; padding: 50px">
        <div class="w-auto text-center">
            <h2>Manutenção de produção</h2>
        </div>
        <hr class="my-5">
        <div class="right_col" role="main">

            <form id="filtro" action="manutencao-status" method="post" data-parsley-validate="" class="form-horizontal form-label-left" novalidate="">
                <div class="form-group row">
                    @if ($mensagem!='')
                        <span class="text-danger">{{$mensagem}}</span>
                    @endif
                </div>
                <div class="form-group row">
                    @csrf <!--{{ csrf_field() }}-->
                    <label for="senha" class="col-sm-1 col-form-label">Senha</label>
                    <div class="col-sm-2">
                        <input type="password" id="senha" name="senha" class="form-control col-md-7 col-xs-12" value="">
                    </div>
                    <label for="os" class="col-sm-1 col-form-label">OS</label>
                    <div class="col-sm-2">
                        <input type="text" id="os" name="os" class="form-control col-md-7 col-xs-12" value="">
                    </div>
                    <div class="col-sm-5">
                        <button type="submit" class="btn btn-primary">Pesquisar</button>
                    </div>
                </div>
            </form>
            <input type="hidden" id="senha_funcionario" name="senha_funcionario" value="{{isset($senha) ? $senha : ''}}">
            <hr class="my-5">
            <h4><b>Encontrados</b></h4>
            <table class="table table-striped text-center" id="table_composicao">
                <thead >
                    <tr>
                        <th scope="col">EP</th>
                        <th scope="col">OS</th>
                        <th scope="col">Status</th>
                        <th scope="col">Etapa</th>
                        <th scope="col">Nº máquina</th>
                        <th scope="col">Motivo pausa</th>
                        <th scope="col">Qtde</th>
                        <th scope="col">Responsável</th>
                        <th scope="col">Colaborador</th>
                        <th scope="col">Ação</th>
                    </tr>
                </thead>
                <tbody>
                    @if(!empty($pedidos))
                        @foreach ($pedidos as $pedido)
                            @if(isset($pedido->colaboradores[$pedido->id]))
                                <?php $contador =0 ?>
                                @foreach ($pedido->colaboradores[$pedido->id] as $colaborador)
                                    <tr style="padding-top: 10px">
                                        <td style="margin-top: 10px" scope="col">{{$pedido->ep}}</td>
                                        <td scope="col">{{$pedido->os}}</td>
                                        <td scope="col">{{$pedido->nomeStatus}}</td>
                                        <td scope="col">{{$colaborador['nome_etapa'] }}</td>
                                        <td scope="col">{{$colaborador['numero_maquina'] }}</td>
                                        <td scope="col">{{$colaborador['select_motivo_pausas']}}</td>
                                        <td scope="col">{{$colaborador['texto_quantidade']}}</td>
                                        <td scope="col">@if($pedido->id_status == 6){{$pedido->funcionario}}@else @endif</td>
                                        <td scope="col">{{$colaborador['nome']}}</td>
                                        <td scope="col">
                                            <?php $st = ($pedido->id_status == 11) ? $pedido->id_status : $pedido->id_status + 1 ?>

                                            <button data-pedidoid={{$pedido->id}} data-etapasalteracao='{{$colaborador['etapas_alteracao_id']}}' data-statusatual='{{$pedido->id_status}}' data-descricaoproximostatus='{{$status[$st]['nome']}}' data-proximostatus='{{$status[$st]['id']}}' type="button" class="btn btn-primary alteracao_status_pedido">
                                                <span  style="font-size: 25px">&#9998;</button>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php $contador++ ?>
                                @endforeach
                            @else
                                <tr>
                                    <td scope="col">{{$pedido->ep}}</td>
                                    <td scope="col">{{$pedido->os}}</td>
                                    <td scope="col">{{$pedido->nomeStatus}}</td>
                                    <td scope="col"></td>
                                    <td scope="col"></td>
                                    <td scope="col"></td>
                                    <td scope="col"></td>
                                    <td scope="col"></td>
                                    <td scope="col"></td>
                                    <td scope="col">
                                        <?php $st = ($pedido->id_status == 11) ? $pedido->id_status : $pedido->id_status + 1 ?>
                                        <button data-pedidoid={{$pedido->id}} data-statusatual='{{$pedido->id_status}}' data-descricaoproximostatus='{{$status[$st]['nome']}}' data-proximostatus='{{$status[$st]['id']}}' type="button" class="btn btn-primary alteracao_status_pedido">
                                            <span  style="font-size: 25px">&#9998;</button>
                                        </span>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    @endif
                </tbody>
            </table>

            <div id='modal_acao_manutencao'  class="modal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content" style="width: 700px">
                        <div class="modal-header">
                        <h5 class="modal-title" id='texto_status'></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        </div>
                        <div class="modal-body" >
                            <div class="form-group row" id='tipo_manutencao'>
                                <label for="" class="col-sm-3 col-form-label text-right">Tipo</label>
                                <select class="form-control col-md-5" name="select_tipo_manutencao" id="select_tipo_manutencao" name="select_tipo_manutencao">
                                    <option value=""></option>
                                    <option value="T">Montagem Torre</option>
                                    <option value="A">Montagem Agulha</option>
                                </select>
                            </div>
                            <div class="form-group row" id='etapa_manutencao'>
                                <label for="" class="col-sm-4 col-form-label text-right">Etapa</label>
                                <select class="form-control col-md-5" name="select_etapa_manutencao" id="select_etapa_manutencao" name="select_etapa_manutencao">
                                    <option value=""></option>
                                    <option value="1">Início</option>
                                    <option value="2">Pausa</option>
                                    <option value="3">Continuar</option>
                                    <option value="4">Finalizado</option>
                                </select>
                            </div>
                            <div class="form-group row" id='motivo_pausas'>
                                <label for="" class="col-sm-4 col-form-label text-right">Motivos pausa</label>
                                <select class="form-control col-md-5" name="select_motivo_pausas" id="select_motivo_pausas" name="select_motivo_pausas">
                                    <option value=""></option>
                                    @foreach ($motivosPausa as $key => $motivoPausa)
                                        <option value="{{$key}}">{{$motivoPausa}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group row" id='necessita_montagem'>
                                <label for="" class="col-sm-4 col-form-label text-right">Necessita montagem?</label>
                                <select class="form-control col-md-5" name="select_necessita_montagem" id="select_necessita_montagem" name="select_motivo_pausas">
                                    <option value="0">Não necessita</option>
                                    <option value="1">Montagem Agulha</option>
                                    <option value="2">Montagem Torre</option>
                                </select>
                            </div>
                            <div class="form-group row" id='quantidade'>
                                <label for="" class="col-sm-4 col-form-label text-right">Quantidade</label>
                                <input class="form-control col-md-5" name="texto_quantidade" id="texto_quantidade"/>
                            </div>
                            <div class="form-group row" id='div_numero_maquina'>
                                <label for="" class="col-sm-4 col-form-label text-right">Nº máquina</label>
                                <input type="text" class="form-control col-md-2 sonumeros" name="numero_maquina" id="numero_maquina"/>
                            </div>
                        </div>
                        <input type="hidden" name="atualStatus" id="atualStatus" value=""/>
                        <input type="hidden" name="novoStatus" id="novoStatus" value=""/>
                        <input type="hidden" name="novoPedido" id="novoPedido" value=""/>
                        <input type="hidden" name="etapasalteracao" id="etapasalteracao" value=""/>
                        <input type="hidden" name="necessiaMontagemExtra" id="necessiaMontagemExtra" value="0"/>
                        <input type="hidden" name="numero_maquina_iniciando" id="numero_maquina_iniciando" value=""/>

                        <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" id="salvar_alteracao_status_pedido" data-dismiss="modal" >Salvar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div id='modal_caixas'  class="modal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content" style="width: 1050px">
                        <div class="modal-header">
                        <h5 class="modal-title" id='texto_status_caixas'></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        </div>
                        <div class="modal-body" >
                            @if (isset($materiais))
                                <label for="" class="col-sm-3 col-form-label text-right">Caixas</label>
                                <span id='nova_caixa' title="Adicionar nova caixa" style="font-size: 20px;  cursor: pointer;  color:darkgreen; font-weight: bolder">+</i></span>
                                <div class="form-group row div_caixas" id='div_caixas'>
                                    <label for="" class="col-sm-1 col-form-label text-right">&nbsp;</label>
                                    <select class="form-control col-md-3 material" name="cx_material[]" id="material" >
                                            <option value=""></option>
                                            @foreach ($materiais as $material)
                                                <option value="{{$material->id}}">{{ $material->material }}</option>
                                            @endforeach
                                        </select>&nbsp;
                                        <input class="form-control col-md-2 cx_quantidade" name="cx_quantidade[]" id="cx_quantidade"  placeholder="Qtde"/>&nbsp;
                                        <input class="form-control col-md-1 cx_a" name="cx_a[]" id="cx_a"  placeholder="A"/>&nbsp;
                                        <input class="form-control col-md-1 cx_b" name="cx_b[]" id="cx_b"  placeholder="B"/>&nbsp;
                                        <input class="form-control col-md-1 cx_c" name="cx_c[]" id="cx_c"  placeholder="C"/>&nbsp;
                                        <input class="form-control col-md-2 cx_peso" name="cx_peso[]" id="cx_peso"  placeholder="Peso"/>
                                </div>
                            @endif
                        </div>
                        <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="salvar_caixas" data-dismiss="modal" >Salvar</button>
                        </div>
                    </div>
                </div>
            </div>

    </div>

@stop
