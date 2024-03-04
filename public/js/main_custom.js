
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
    $('#blocker').hide();
    $('.cep').mask('00000-000', {reverse: true});
    $('.sonumeros').mask('999999999999', {reverse: true});
    $('.mask_minutos').mask('00:00', {reverse: true});
    $('.mask_horas').mask('00:00:00', {reverse: true});
    $('.mask_valor').mask("###0,00", {reverse: true});
    $('.mask_date').mask('00/00/0000');

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


    $(".alteracao_status_pedido").change(function () {
        if (!confirm("Você deseja realmente alterar este pedido?")) {
            $(this).val($(this).data('statusatual'));
            return false;
        }

        $('.overlay').show();
        var pedido = $(this).data('pedido');
        var status = $(this).val();
        $.ajax({
            type: "POST",
            url: '/alterar-pedidos-ajax',
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
            "url": '/calcular-orcamento-ajax',
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
});


function createPopupWin(pageURL, pageTitle,
    popupWinWidth, popupWinHeight) {
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
