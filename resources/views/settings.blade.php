@extends('adminlte::page')

@section('title', '4Jur')

@section('content_header')
    <h1 class="m-0 text-dark">Configuração da conta</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-4">
                            <form action="alterar-senha" method="post">
                                @csrf <!--{{ csrf_field() }}-->
                                <div class="form-group">
                                    <label for="nome">Nome</label>
                                    <input type="text" class="form-control" id="nome" name="nome" value='{{ $user->name }}' placeholder="nome">
                                </div>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="text" class="form-control" id="email" name="email" value='{{ $user->email }}' placeholder="Email">
                                </div>
                                <div class="form-group">
                                    <label for="senha">Senha</label>
                                    <input type="password" class="form-control" id="senha" name="password" value='' placeholder="Senha">
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Salvar</button>
                                </div>
                            </form>              
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
