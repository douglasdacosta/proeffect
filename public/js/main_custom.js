
// $(document).ready(function(){
//     $('.date').mask('00/00/0000');
//     $('.time').mask('00:00:00');
//     $('.date_time').mask('00/00/0000 00:00:00');
//     $('.cep').mask('00000-000');
//     $('.phone').mask('0000-0000');
//     $('.phone_with_ddd').mask('(00) 0000-0000');
//     $('.phone_us').mask('(000) 000-0000');
//     $('.mixed').mask('AAA 000-S0S');
//     $('.cpf').mask('000.000.000-00', {reverse: true});
//     $('.cnpj').mask('00.000.000/0000-00', {reverse: true});
//     $('.money').mask('000.000.000.000.000,00', {reverse: true});
//     $('.money2').mask("#.##0,00", {reverse: true});
//     $('.ip_address').mask('0ZZ.0ZZ.0ZZ.0ZZ', {
//       translation: {
//         'Z': {
//           pattern: /[0-9]/, optional: true
//         }
//       }
//     });
//     $('.ip_address').mask('099.099.099.099');
//     $('.percent').mask('##0,00%', {reverse: true});
//     $('.clear-if-not-match').mask("00/00/0000", {clearIfNotMatch: true});
//     $('.placeholder').mask("00/00/0000", {placeholder: "__/__/____"});
//     $('.fallback').mask("00r00r0000", {
//         translation: {
//           'r': {
//             pattern: /[\/]/,
//             fallback: '/'
//           },
//           placeholder: "__/__/____"
//         }
//       });
//     $('.selectonfocus').mask("00/00/0000", {selectOnFocus: true});

//   });
$(function ($) {
    $('body').addClass('sidebar-collapse');

    //var baseUrl = '/proeffect/public'
    var baseUrl = ''

    $('#blocker').hide();
    $('.cep').mask('00000-000', {reverse: true});
    $('.sonumeros').mask('000000000000', {reverse: true});
    $('.mask_minutos').mask('00:00', {reverse: true});
    $('.mask_horas').mask('00:00:00', {reverse: true});
    $('.mask_valor').mask("###0,00", {reverse: true});
    $('.mask_date').mask('00/00/0000');
    $('.mask_date_time').mask('00/00/0000 00:00:00');
    $('.kg').mask("###.###.###.##0.000", {reverse: true});

    $(document).on('change', '#calc-type', function(){
        if($(this).val().trim() == '+') {
            $('#calc-val2').mask('00:00', {reverse: true});
        } else {
            $('#calc-val2').mask('000000000000', {reverse: true});
        }
    });

    if ($.fn.select2) {
        $('.default-select2').select2({
            placeholder: "",
            allowClear: false
        });

        // Foca automaticamente no campo de pesquisa ao clicar no select
        $('#default-select2').on('select2:open', function() {
            document.querySelector('.select2-search__field').focus();
        });

    }



    var behavior = function (val) {
        return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
    },
    options = {
        onKeyPress: function (val, e, field, options) {
            field.mask(behavior.apply({}, arguments), options);
        }
    };
    $('.mask_phone').mask(behavior, options);
    $('.toast').hide();


    $(document).on('click', '.pesquisar_materiais', function () {
        $('#blocker').show();
    });

    $(document).on('click', '.show_caixas', function () {

        var pedido = $(this).data('pedido_id');
        var html = '';
        $.ajax({
            type: "POST",
            url: baseUrl + '/buscar-caixas',
            data: {
                'id': pedido,
                '_token': $('meta[name="csrf-token"]').attr('content'),
            },
            success: function (data) {
                $('#tabela_caixas tbody').empty();
                $.each(data, function (k, value) {

                    html+="<tr>"+
                        "<td>"+value.material+"</td>"+
                        "<td>"+value.a+"</td>"+
                        "<td>"+value.l+"</td>"+
                        "<td>"+value.c+"</td>"+
                        "<td>"+value.quantidade+"</td>"+
                        "<td>"+value.peso+"</td>"+
                        "</tr>";
                });

                $('#tabela_caixas tbody').append(html);

                $('#modal_caixas').modal('show');

                $('.overlay').hide();
            },
            error: function (data, textStatus, errorThrown) {

                $('.overlay').hide();
            },

        });

    });

    $(document).on('blur', '.calcula_peso_chapa', function (e) {

        var qtde_chapa_peca = $('#qtde_chapa_peca').val().trim();
        var qtde_por_pacote = $('#qtde_por_pacote').val().trim();
        var peso_material = $('#peso_material').val().trim();
        var peso_material_mo = $('#peso_material_mo').val().trim();

        var qtde_chapa_peca_mo = $('#qtde_chapa_peca_mo').val().trim();
        var qtde_por_pacote_mo = $('#qtde_por_pacote_mo').val().trim();

        // Remover pontos (.) para tratar os números como inteiros
        qtde_chapa_peca = parseInt(qtde_chapa_peca.replace('.', ''), 10) || 0;
        qtde_por_pacote = parseInt(qtde_por_pacote.replace('.', ''), 10) || 0;

        qtde_chapa_peca_mo = parseInt(qtde_chapa_peca_mo.replace('.', ''), 10) || 0;
        qtde_por_pacote_mo = parseInt(qtde_por_pacote_mo.replace('.', ''), 10) || 0;

        peso_material = parseInt(peso_material.replace('.', ''), 10) || 0;
        peso_material_mo = parseInt(peso_material_mo.replace('.', ''), 10) || 0;

        if (qtde_chapa_peca == 0 || qtde_por_pacote == 0) {
            $('#total_chapa_peca').val(0);
            return false;
        }


        // Multiplicação das quantidades
        var total_chapa_peca = qtde_chapa_peca * qtde_por_pacote;
        var peso_mo = 0
        if(qtde_chapa_peca_mo> 0 && qtde_por_pacote_mo > 0) {
            var total_chapa_peca_mo = qtde_chapa_peca_mo * qtde_por_pacote_mo;
            peso_mo = total_chapa_peca_mo * peso_material_mo
        }

        //campo peso recebe o valor de total_chapa_peca multiplicado pelo peso_material
        peso = total_chapa_peca * peso_material
        if(total_chapa_peca_mo > 0) {
            total_chapa_peca = total_chapa_peca + total_chapa_peca_mo
        }

        if(peso_mo>0) {
            peso = peso + peso_mo
        }

        $('#peso').val(peso.toLocaleString('pt-BR'));

        // Definir o valor total no campo de resultado com separador de milhar
        $('#total_chapa_peca').val(total_chapa_peca.toLocaleString('pt-BR'));
    });


    $(document).on('change', '.material_id_estoque', function (e) {
        $('#peso_material').val(0);
        $('#total_chapa_peca').val(0);
        $('#peso').val(0);
        material_id = $(this).val();
        var xhr = new XMLHttpRequest();
        xhr.open("POST",  baseUrl + '/ajax-fichatecnica', true);
        xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                if (xhr.status >= 200 && xhr.status < 300) {
                    dados = JSON.parse(xhr.responseText)

                    if(dados[0].peso != null) {
                        peso = dados[0].peso.toFixed(3).toLocaleString('pt-BR')
                        $('#peso_material').val(peso);
                        $('#qtde_chapa_peca').blur();
                    }

                } else {

                    alert('Ocorreu um erro ao buscar o material!')
                }
            }
        };

        var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        var requestData = {
            'id': material_id,
            '_token': csrfToken
        };
        xhr.send(JSON.stringify(requestData));

    });

    if (typeof DataTable !== 'undefined') {
        let table_estoque = new DataTable('#table_estoque', {
            responsive: true,
            "paging": false,
            "info": false,
            "lengthChange": false,
            "pageLength": 15000,
            "language": {
                "search": "Pesquisar:",
                "emptyTable": "Nenhum dado encontrado"
            }
        });


        table_estoque.on('order.dt', function() {
            updateSortIcons();
        });

        let table_material = new DataTable('#table_material', {
            responsive: true,
            "paging": false,
            "info": false,
            "lengthChange": false,
            "pageLength": 15000,
            "language": {
                "search": "Pesquisar:",
                "emptyTable": "Nenhum dado encontrado"
            }
        });
        table_material.on('order.dt', function() {
            updateSortIcons();
        });


        function updateSortIcons() {

            $('th').removeClass('sorting_asc');
            $('th').removeClass('sorting_desc');

            $('th[aria-sort]').each(function() {
                var sortOrder = $(this).attr('aria-sort');
                if (sortOrder === 'ascending' ){
                    $(this).addClass('sorting_asc');
                }
                if (sortOrder === 'descending'){
                    $(this).addClass('sorting_desc');
                }
            });
        }



        updateSortIcons();

    }


    $(document).on('click', '#adicionar_montador', function (e) {
        salva_montadores = [];
        $('.montadores').each(function (c, j) {
            if($(this).prop('checked') == true ) {
                salva_montadores.push($(this).val());
            }
        })

        pedido_id = $('#pedido_montagem').val();
        var xhr = new XMLHttpRequest();
        xhr.open("POST",  baseUrl + '/incluir-pedidos-funcionario-montagem', true);
        xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                if (xhr.status >= 200 && xhr.status < 300) {
                    alert('Alterado com sucesso!')
                    history.replaceState(null, null, window.location.pathname);
                    location.reload();
                } else {
                    var data = JSON.parse(xhr.responseText);
                    alert('Ocorreu um erro al alterar o status!')
                }
            }
        };

        var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        var requestData = {
            'pedido_id': pedido_id,
            'montadores': JSON.stringify(salva_montadores),
            '_token': csrfToken
        };
        xhr.send(JSON.stringify(requestData));

    })

    $(document).on('click', '.add_funcionarios_montagens', function (e) {
        $('#funcionarios_selecionados').val($(this).data('funcionariomontagem'));

        montadores = $(this).data('funcionariomontagem').split(',');
        $('#pedido_montagem').val($(this).data('pedido_id'));

        $('.montadores').each(function (c, j) {
            if(montadores.includes($(this).val())) {
                $(this).prop('checked', true);
            } else {
                $(this).prop('checked', false);
            }
        });

        $('#modal_funcionarios').modal('show');
    });



    $(document).on('change', '.alteracao_status_pedido', function (e) {
        if (!confirm("Você deseja realmente alterar este pedido?")) {
            $(this).val($(this).attr('data-statusatual'));
            return false;
        }
        $('.overlay').show();
        var pedido = $(this).data("pedido");
        var status = $(this).val();
        $.ajax({
            type: "POST",
            url: baseUrl + '/alterar-pedidos-ajax',
            data: {
                'id': pedido,
                'status': status,
                _token: $('meta[name="csrf-token"]').attr('content'),
            },
            success: function (data) {
                abreAlertSuccess(data, false);
                $('.overlay').hide();
            },
            error: function (data, textStatus, errorThrown) {
                abreAlertSuccess(data, true);
                $('.overlay').hide();
            },

        });

    })
    if($('#imprimir').length){
        if($('#imprimir').val() ==1){

            setTimeout(() => window.print(), 1000)
        }
    }

    function Calcula(tipo, revisao, rev, data) {

        $('.overlay').show();
        var composicaoep = new Array();
        $('#table_composicao_orcamento tbody tr').each(function (i, e) {
            json = new Array();
            $(e).find('td').each(function (c, j) {

                if ($(j).data('name') == 'material_id') {
                    json.push('{"' + $(j).data('name') + '":"' + $(j).data('materialid') + '"}');
                } else {
                    json.push('{"' + $(j).data('name') + '":"' + $(j).text().trim() + '"}');
                }
            });
            composicaoep.push(json);
        })

        rv = $('#rv').val().trim();
        if(rev.toString().length > 0) {
            rv = rev;
            $('#rv').val(rev);
        }

        $.ajax({
            "type": "POST",
            "url": baseUrl + '/calcular-orcamento-ajax',
            "data": {
                "tipo": tipo,
                "dados": composicaoep,
                "revisao": revisao,
                "ep":$('#ep').val().trim(),
                "rv":rv,
                "data" : data,
                "calculo_hora_fresa": $('#calculo_hora_fresa').val(),
                "_token": $('meta[name="csrf-token"]').attr('content'),
            },
            success: function (data) {
                tabela = data[0];
                Totais = data[1];
                Orcamentos = data[2];

                $('.subTotalMO').text(Totais.subTotalMO);
                $('.subTotalMP').text(Totais.subTotalMP);
                $('.subTotalCI').text(Totais.subTotalCI);
                $('.desc_10_total').text(Totais.desc_10_total);
                $('.desc_20_total').text(Totais.desc_20_total);
                $('.desc_30_total').text(Totais.desc_30_total);
                $('.desc_40_total').text(Totais.desc_40_total);
                $('.desc_50_total').text(Totais.desc_50_total);



                $('#table_composicao_orcamento tbody').empty();
                contador = 0;
                html = '';
                $.each(tabela, function (k, data1) {
                    html = '<tr>';
                    $.each(data1, function (k, data2) {
                        objeto = JSON.parse(data2);
                        classe = Object.keys(objeto)[0];

                        switch(k) {
                            case 0:
                                html = html + "<td data-name='blank_"+contador+"' class='blank_"+contador+"' scope='row'>"+Object.values(objeto)[0]+"</td>";
                                break;
                            case 1:
                                html = html + "<td data-name='tmp_"+contador+"' class='tmp_"+contador+"'>"+Object.values(objeto)[0]+"</td>";
                                break;
                            case 2:
                                html = html + "<td data-name='uso_"+contador+"' class='uso_"+contador+"'>"+Object.values(objeto)[0]+"</td>";
                                break;
                            case 3:
                                html = html + "<td data-name='qtde_"+contador+"' class='qtde_"+contador+"'>"+Object.values(objeto)[0]+"</td>";
                                break;
                            case 4:
                                html = html +    "<td data-name='material_id_"+contador+"' class='material_id_"+contador+"'>"+Object.values(objeto)[0]+"</td>";
                                break;
                            case 5:
                                html = html + "<td data-name='qtdeCH_"+contador+"' class='qtdeCH_"+contador+"'>"+Object.values(objeto)[0]+"</td>";
                                break;
                            case 6:
                                html = html + "<td data-name='medidax_"+contador+"' class='medidax_"+contador+"'>"+Object.values(objeto)[0]+"</td>";
                                break;
                            case 7:
                                html = html + "<td data-name='mediday_"+contador+"' class='mediday_"+contador+"'>"+Object.values(objeto)[0]+"</td>";
                                break;
                            case 8:
                                html = html + "<td data-name='valor_chapa_"+contador+"' class='valor_chapa_"+contador+"'>"+Object.values(objeto)[0]+"</td>";
                                break;
                            case 9:
                                html = html + "<td style='border-left: solid; border-right: solid;' data-name='valorMO_"+contador+"' class='valorMO_"+contador+"'>"+Object.values(objeto)[0]+"</td>";
                                break;
                            case 10:
                                html = html + "<td style='border-left: solid; border-right: solid;'data-name='valorMP_"+contador+"' class='valorMP_"+contador+"'>"+Object.values(objeto)[0]+"</td>";
                                break;
                        }
                        if(Object.values(objeto)[0] != '') {
                            valor = Object.values(objeto)[0];
                            $('.' + classe).text(valor);
                        } else {
                            $('.' + classe).text('');
                        }

                    })
                    contador =contador+1;
                    html = html + "</tr>";
                    $('#table_composicao_orcamento tbody').append(html);
                })
                $('#tabela_rev tbody').empty();
                $.each(Orcamentos, function(i,j) {
                    $('#tabela_rev tbody').append(
                       '<tr>'+
                            '<th scope="col">'+j.rev+'</th>'+
                            '<th scope="col">'+formatarData(j.created_at)+'</th>'+
                            '<th scope="col" style="cursor:pointer"><span data-rv="'+j.rev+'" data-horafreza="'+j.hora_fresa+'" data-data="'+formatarData(j.created_at)+'" class="carrega_rev" style="color:blue">&#8599;</span></th>'+
                        '</tr>'
                    );
                });

                abreAlertSuccess('Orçamento calculado', false);
                $('.overlay').hide();
            },
            error: function (data, textStatus, errorThrown) {
                abreAlertSuccess('Erro ao Ao salvar/calcular', true);
                $('.overlay').hide();
            },

        });

    };

    $("#calculo_hora_fresa").change(function () {
        rv = $('#rv').val().trim();
        Calcula('calculo_hora_fresa', false, rv, false);
    })


    $("#salvar_orcamento").click(function () {
        $('.overlay').show();
        rv = $('#rv').val().trim();
        if($('#rv').val().trim() == '')
        {
            abreAlertSuccess('Campo RV. vazio não pode salvar o orçamento', true);
            $('.overlay').hide();
            return false;
        }

        Calcula('salvar_orcamento', false, rv, false);
    });

    $("#calculo_hora_fresa").change();

    $(document).on('click', '.carrega_rev', function(e){
        rev = $(this).data('rv');
        data = $(this).data('data');
        $('#calculo_hora_fresa').val($(this).data('horafreza'));

        Calcula('carrega_rev', true, rev, data);

    })

    $(document).on('click', '.adiciona_fila_impressao', function (e) {
        $('#modal_imprime_etiqueta').modal('show');
        $('#estoque_id').val($(this).data("id"));
    });
    $(document).on('click', '#salva_fila_impressao', function (e) {
        $('.overlay').show();

        var qtde_etiqueta = $('#qtde_etiqueta').val();

        if(qtde_etiqueta == '' || qtde_etiqueta == 0){
            $('.overlay').hide();
            alert('Qtde. etiqueta vazio');
            return false;
        }
        $.ajax({
            type: "POST",
            url: baseUrl + '/incluir-fila-impressao',
            data: {
                'id': $('#estoque_id').val(),
                'qtde_etiqueta': qtde_etiqueta,
                _token: $('meta[name="csrf-token"]').attr('content'),
            },
            success: function (data) {
                abreAlertSuccess(data, false);
                $('.overlay').hide();
            },
            error: function (data, textStatus, errorThrown) {
                abreAlertSuccess(data, true);
                $('.overlay').hide();
            },

        });

    })

    function formatarData(data) {
        // Cria um objeto de data com a data fornecida
        const dataObj = new Date(data);

        // Extrai os componentes da data
        const dia = String(dataObj.getDate()).padStart(2, '0');
        const mes = String(dataObj.getMonth() + 1).padStart(2, '0'); // Mês começa de zero, por isso adicionamos 1
        const ano = dataObj.getFullYear();
        const horas = String(dataObj.getHours()).padStart(2, '0');
        const minutos = String(dataObj.getMinutes()).padStart(2, '0');
        const segundos = String(dataObj.getSeconds()).padStart(2, '0');

        // Formata a data e hora conforme necessário
        const dataFormatada = `${dia}/${mes}/${ano} ${horas}:${minutos}:${segundos}`;

        return dataFormatada;
    }

    $(document).on('click', '.altera_estoque', function (e) {
        if ($('#qtde_alteracao_estoque').val()==0 || $('#qtde_alteracao_estoque').val().trim() =='') {
            alert('Quantidade Invalida');
            return false;
        }
        acao = $(this).data('acao');
        $('#acao_estoque').val(acao);
        $('#modal_estoque').modal('show');
    });

    $(document).on('click', '#salva_estoque', function (e) {
        qtde_alteracao_estoque = $('#qtde_alteracao_estoque').val();
        pacotes_restantes = $('#pacotes_restantes').val();


        acao_estoque = $('#acao_estoque').val();

        if(acao_estoque == 'remover') {
            if(parseInt(qtde_alteracao_estoque) > parseInt(pacotes_restantes))  {
                alert('Quantidade de pacotes disponíveis de ' + pacotes_restantes + ' pacotes. Baixa não liberada!');
                return false;
            }
        } else {
            qtde_por_pacote = $('#qtde_por_pacote').val();
            qtde_por_pacote_mo = $('#qtde_por_pacote_mo').val();

            qtde_liberado_incluir = parseInt(qtde_por_pacote) + parseInt(qtde_por_pacote_mo) - parseInt(pacotes_restantes);

            if(parseInt(qtde_alteracao_estoque) > parseInt(qtde_liberado_incluir))  {
                alert('Limite disponível para inclusão é de ' + qtde_liberado_incluir + ' pacotes. Inclusão não liberada!');
                return false;
            }

        }



        id = $('#id').val();

        $.ajax({
            type: "POST",
            url: baseUrl + '/altera-qtde-estoque',
            data: {
                'id': id,
                'qtde': qtde_alteracao_estoque,
                'acao_estoque': acao_estoque,
                _token: $('meta[name="csrf-token"]').attr('content'),
            },
            success: function (data) {

                console.log(data);
                dados = data;
                $('#pacotes_restantes').val(dados.pacotes_restantes);
                alert('Estoque salvo!');

            },
            error: function (data, textStatus, errorThrown) {

                dados = JSON.parse(data.responseText);
                alert('Erro ao salvar estoque! ' + dados.error);
            },

        });

    });


    $(document).on('click', '.ckeckbox_mo', function(){
        show_mo();
    });

    show_mo = function(){

        if($('.ckeckbox_mo').is(':checked')){
            $('.identificador_mo').show();
        }else{
            $('.identificador_mo').hide();
        }
    }

    show_mo();
    $('.calcula_peso_chapa').blur();

    $(document).on('click', '.ver_materiais', function () {
        pedidoid = $(this).data('pedidoid');
        $('#' + pedidoid).show();
    });
    $(document).on('click', '.close', function () {
        $('.modal').hide();
    })

    $(document).on('click', '.checkbox_emails_todos', function(){
        if($(this).is(':checked')) {
            $('.checkbox_emails').prop('checked', true);
        } else {
            $('.checkbox_emails').prop('checked', false);
        }
    })

    $('#modal_alerta').modal('show');

    $(document).on('change', '.tipo_consulta', function(){

        $('.campo_loja').show();
        $('.campo_categorias').show();
        $('.status_pedido').show();
        $('.regra_data_fim').show();

        if($('#tipo_consulta').val() == 'V' || $('#tipo_consulta').val() == 'C') {
            $('.status_pedido').hide();

        }

        if($('#tipo_consulta').val() == 'ED' || $('#tipo_consulta').val() == 'V') {
            $('.campo_loja').show();
        } else {
            $('.campo_loja').hide();
        }

        if($('#tipo_consulta').val() == 'ED') {

            $('.status_pedido').hide();
            $('.regra_data_fim').hide();
        }

        if($('#tipo_consulta').val() == 'EEC') {
            $('.status_pedido').hide();
        }



    })
    $('.tipo_consulta').change();

    $(document).on('click', '#lote_manual', function(){
        if($(this).is(':checked')) {

            // salva dos do lote anterior no storage
            lote_anterior = $('#lote').val();
            localStorage.setItem('lote_anterior', lote_anterior);

            // retira readonly do campo "lote"
            $('#lote').attr('readonly', false);
            $('#lote').val('');

        } else {
            $('#lote').attr('readonly', true);

            lote_anterior = localStorage.getItem('lote_anterior');

            $('#lote').val(lote_anterior);
        }
    })

    $(document).on('change', '.tipo_consulta_followup', function(){

        $('.status_pedido').show();

        if($('.tipo_consulta_followup').val() == 'G' )        {
            $('.campos_ciclo_producao').show();
            $('.campos_followup').show();
        }

        if($('.tipo_consulta_followup').val() == 'F' )        {

            $('.campos_ciclo_producao').hide();
            $('.campos_followup').show();
            $('.status_pedido').attr('checked', false);

            Status_pedido = $('.status_pedido');

            $.each(Status_pedido, function(i,j) {

                if(j.value >= 11) {
                    j.checked = false;
                } else {
                    j.checked = true;
                }

            });
        }

        if($('.tipo_consulta_followup').val() == 'R' )        {

            $('.campos_ciclo_producao').show();
            $('.campos_followup').hide();

            $('.status_pedido').attr('checked', false);

            Status_pedido = $('.status_pedido');
            $.each(Status_pedido, function(i,j) {

                if(j.value >= 11 ) {
                    j.checked = false;
                } else {
                    j.checked = true;
                }

            });
        }

        if($('.tipo_consulta_followup').val() == 'C' )        {

            $('.campos_ciclo_producao').show();
            $('.campos_followup').hide();

            $('.status_pedido').attr('checked', false);

            Status_pedido = $('.status_pedido');

            $.each(Status_pedido, function(i,j) {
                if(j.value == 11) {
                    j.checked = true;
                } else {
                    j.checked = false;
                }

            });
        }
    })

    $('.tipo_consulta_followup').change();

    function convertDataHora(dataOriginal){
        const data = new Date(dataOriginal);

        const dia = String(data.getDate()).padStart(2, '0');
        const mes = String(data.getMonth() + 1).padStart(2, '0'); // Janeiro é 0
        const ano = data.getFullYear();
        const hora = String(data.getHours()).padStart(2, '0');
        const minuto = String(data.getMinutes()).padStart(2, '0');
        const segundo = String(data.getSeconds()).padStart(2, '0');

        const dataFormatada = `${dia}/${mes}/${ano} ${hora}:${minuto}:${segundo}`;

        return dataFormatada;
    }

    $(document).on('click', '.ver-detalhes', function() {
        id = $(this).data('id');
        status_id = $(this).data('status_id');


        $.ajax({
            type: "POST",
            url: baseUrl + '/ajax-busca-responsveis',
            data: {
                'id': id,
                'status_id': status_id,
                _token: $('meta[name="csrf-token"]').attr('content'),
            },
            success: function (data) {

                $('#tabela_responsaveis tbody').empty();
                $.each(data, function(i, item) {
                    var tr = $('<tr>');
                    data_hora = convertDataHora(item.data)
                    tr.append($('<td>').text(item.responsavel));
                    tr.append($('<td>').text(item.etapa));
                    tr.append($('<td class="text-nowrap">').text(data_hora));
                    $('#tabela_responsaveis tbody').append(tr);
                });
                $('#modal_detalhes').modal('show');
            },
            error: function (data, textStatus, errorThrown) {

                dados = JSON.parse(data.responseText);
                alert('Erro ao buscar os responsáveis! ' + dados.error);
            },

        });
    });

    $(document).on('click', '.aplicar-valores', function() {
        id = $(this).data('id');
        tempo_aplicar = $(this).data('tempo_aplicar');
        ep = $(this).data('ep');
        status_id = $(this).data('status_id');

        if(!confirm('Você deseja realmente aplicar os valores?')) {
            return false;
        }

        $.ajax({
            type: "POST",
            url: baseUrl + '/ajax-aplica-valores-fichatecnica',
            data: {
                'id': id,
                'tempo_aplicar': tempo_aplicar,
                'ep': ep,
                'status_id': status_id,
                _token: $('meta[name="csrf-token"]').attr('content'),
            },
            success: function (data) {
                // recarrega a tela
                location.reload();
            },
            error: function (data, textStatus, errorThrown) {

                dados = JSON.parse(data.responseText);
                alert('Erro ao salvar os dados! ' + dados.error);
            },

        });



    });

}); //FIM DO BLOCO DE JQUERY READY

$(document).on('click', '.painel', function(){
    pageURL = $(this).data('url');
    pageTitle = $(this).data('nometela');
    popupWinWidth = 1200;
    popupWinHeight = 980;
    createPopupWin(pageURL, pageTitle,popupWinWidth, popupWinHeight);
});

function createPopupWin(pageURL, pageTitle,popupWinWidth, popupWinHeight) {
    let left = (screen.width);
    let top = (screen.height);
    let myWindow = window.open(pageURL, pageTitle,
        'resizable=yes, width=' + popupWinWidth
        + ', height=' + popupWinHeight + ', top='
        + top + ', left=' + left);
}



function abreAlertSuccess(texto, erro) {
    if(erro) {
        $('.toast').addClass('bg-danger')
    } else {
        $('.toast').addClass('bg-success')
    }
    $('.textoAlerta').text(texto);
    $('.toast').show();
    setTimeout(function () {
        $('.toast').hide('slow');
    }, 7000);
};


