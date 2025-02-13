<?php
use App\Http\Controllers\PedidosController;
$palheta_cores = [1 => '#ff003d', 2 => '#ee7e4c', 3 => '#8f639f', 4 => '#94c5a5', 5 => '#ead56c', 6 => '#0fbab7', 7 => '#f7c41f', 8 => '#898b75', 9 => '#c1d9d0', 10 => '#da8f72', 11 => '#00caf8', 12 => '#ffe792', 13 => '#9a5071'];
?>
@extends('adminlte::page')

@section('title', 'Pro Effect')
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="js/jquery.mask.js"></script>
<script src="js/main_custom.js"></script>
<link rel="stylesheet" href="{{ asset('css/main_style.css') }}" />
<link rel="stylesheet" href="{{ asset('css/main_style.css') }}" />
@section('content_header')
    <div class="form-group row">
        <div class="col-sm-2">
            <a target="_blank">
                <button type="button" data-nometela='Painel de usinagem' data-url="{{URL::route('paineis-usinagem')}}" class="btn painel" style="background-color: {{$palheta_cores[4]}}">Tela de usinagem</button>
            </a>
        </div>
        <div class="col-sm-2">
            <a target="_blank">
                <button type="button" data-nometela='Painel de acabamento' data-url="{{URL::route('paineis-acabamento')}}" class="btn painel" style="background-color: {{$palheta_cores[5]}}">Tela de Acabamento</button>
            </a>
        </div>
        <div class="col-sm-2">
            <a target="_blank">
                <button type="button"  data-nometela='Painel de montagem' data-url="{{URL::route('paineis-montagem')}}" class="btn painel" style="background-color: {{$palheta_cores[6]}}">Tela de Montagem</button>
            </a>
        </div>
        <div class="col-sm-2">
            <a target="_blank">
                <button type="button"  data-nometela='Painel de inspeção' data-url="{{URL::route('paineis-inspecao')}}" class="btn painel" style="background-color: {{$palheta_cores[7]}}">Tela de Inspeção</button>
            </a>
        </div>
        <div class="col-sm-2">
            <a target="_blank">
                <button type="button"  data-nometela='Painel de Embalagem' data-url="{{URL::route('paineis-embalar')}}" class="btn painel" style="background-color: {{$palheta_cores[8]}}">Tela de Embalagem</button>
            </a>
        </div>

    </div>
    <div class="form-group row">
        <div class="col-sm-2">
            <a target="_blank">
                <button type="button"  data-nometela='Apontamentos' data-url="{{URL::route('manutencao-status')}}" class="btn painel" style="background-color: {{$palheta_cores[9]}}">Apontamentos</button>
            </a>
        </div>
        <div class="col-sm-2">
            <a target="_blank">
                <button type="button"  data-nometela='Baixa de estoque' data-url="{{URL::route('tela-baixa-estoque')}}" class="btn painel" style="background-color: {{$palheta_cores[9]}}">Baixa de estoque</button>
            </a>
        </div>
    </div>
@stop
