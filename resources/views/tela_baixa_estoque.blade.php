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
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="js/baixa_estoque.js"></script>

@section('content')

    <div class="container_default" style="background-color:   #f8fafc; padding: 50px">
        <div class="w-auto text-center">
            <h2>Baixa de estoque</h2>
        </div>
        <hr class="my-5">
        <div class="right_col" role="main">

            <form id="filtro" action="tela-baixa-estoque" method="post" data-parsley-validate="" class="form-horizontal form-label-left" novalidate="">
                <div class="form-group row">
                    @if ($mensagem!='')
                        <span class="text-danger">{{$mensagem}}</span>
                    @endif
                </div>
                <div class="form-group row">
                    @csrf <!--{{ csrf_field() }}-->
                    <label for="senha" class="col-sm-1 col-form-label text-right">Senha</label>
                    <div class="col-sm-2">
                        <input type="password" id="senha" name="senha" class="form-control col-md-7 col-xs-12" value="">
                    </div>
                    <label for="id" class="col-sm-2 col-form-label text-right">Lote</label>
                    <div class="col-sm-2">
                        <input type="text" id="id" name="id" class="form-control col-md-7 col-xs-12" value="">
                    </div>
                    <div class="col-sm-5">
                        <button type="submit" class="btn btn-primary">Pesquisar</button>
                    </div>
                </div>
            </form>
            <input type="hidden" id="senha_funcionario" name="senha_funcionario" value="{{isset($senha) ? $senha : ''}}">
            <hr class="my-5">
            <h4><b>Encontrados</b></h4>

            @if($mensagem_alerta_estoque['alerta'] == true)
                <div id='modal_alerta' class="modal" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content" style="width: 100%">
                            <div class="modal-header">
                            <h5 class="modal-title" id='texto_status_caixas'>Atenção</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            </div>

                            <div class="modal-body text-danger" >
                                {{$mensagem_alerta_estoque['mensagem']}}
                            </div>
                            <div class="modal-footer">
                                <div class="col-sm-5 text-left text-bold">

                                    <a href="tela-baixa-estoque" class="btn btn-danger text-left" >Cancelar</a>
                                </div>
                                <div class="col-sm-6 text-right">
                                    <button type="button" class="btn btn-success" data-dismiss="modal" >Continuar mesmo assim</button>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <table class="table table-striped text-center" id="table_composicao">
                <thead >
                    <tr>
                        <th scope="col">Lote</th>
                        <th scope="col">Data</th>
                        <th scope="col">Material</th>
                        <th scope="col">Ação</th>
                    </tr>
                </thead>
                <tbody>
                    @if(!empty($estoque))
                        @foreach ($estoque as $estoque)
                                    <tr style="padding-top: 10px">
                                        <td style="margin-top: 10px" scope="col">{{$estoque->lote}}</td>
                                        <td style="margin-top: 10px" scope="col">{{\Carbon\Carbon::parse($estoque->data)->format('d/m/Y')}}</td>
                                        <td scope="col">{{$materiais[$estoque->material_id]['material']}}</td>
                                        <td scope="col">
                                            <button class="btn btn-primary baixar_estoque" data-id="{{$estoque->id}} " data-usuario="{{$usuario}}">Baixar</button>
                                        </td>
                                    </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
    </div>
@stop
