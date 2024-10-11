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
            <table class="table table-striped text-center" id="table_composicao">
                <thead >
                    <tr>
                        <th scope="col">Lote</th>
                        <th scope="col">Material</th>
                        {{-- <th scope="col">Fornecedor</th> --}}
                        <th scope="col">Ação</th>
                    </tr>
                </thead>
                <tbody>
                    @if(!empty($estoque))
                        @foreach ($estoque as $estoque)
                                    <tr style="padding-top: 10px">
                                        <td style="margin-top: 10px" scope="col">{{$estoque->lote}}</td>
                                        <td scope="col">{{$materiais[$estoque->material_id]['material']}}</td>
                                        {{-- <td scope="col">{{\Illuminate\Support\Str::words($fornecedores[$estoque->fornecedor_id]['nome_cliente'], 2, '') }}</td> --}}
                                        <td scope="col">
                                            <button class="btn btn-primary baixar_estoque" data-id="{{$estoque->id}}">Baixar</button>
                                        </td>
                                    </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
    </div>
@stop
