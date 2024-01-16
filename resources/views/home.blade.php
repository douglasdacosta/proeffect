@extends('adminlte::page')

@section('title', '4Jur')

@section('content_header')
    <h1 class="m-0 text-dark">Início</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
            <div class="col-4">
                    <form>
                        <div class="form-group">
                            <label for="name">Nome</label>
                            <input type="text" class="form-control" id="name" placeholder="nome">
                        </div>                                
                        <div class="form-group">
                            <label for="name">Nome</label>
                            <input type="text" class="form-control" id="name" placeholder="nome">
                        </div>                                
                        <div class="form-group">
                            <label for="name">Nome</label>
                            <input type="text" class="form-control" id="name" placeholder="nome">
                        </div>                                
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </form>      
                </div>
            </div>
        </div>
    </div>
@stop
