
$(function () {
    $('.toast').hide();
    function bloqueiaEP() {
        if ($('#table_composicao tbody tr').length > 0) {
            $('#ep').attr('readonly', true);
        } else {
            $('#ep').attr('readonly', false);
        }
    }

    $("#material_id").change(function () {
        $('.overlay').show();
        $('#blank, #medidax, #mediday, #qtde ,#tempo_usinagem #tempo_acabamento #tempo_montagem #tempo_montagem_torre #tempo_inspecao').val('');
        $.ajax({
            type: "POST",
            url: '/ajax-fichatecnica',
            data: {
                id: this.value,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (data) {
                $('#tempo_montagem_torre').val(data[0].tempo_montagem_torre);
                $('.overlay').hide();
                $('#blank').val('');
            },
            error: function (data, textStatus, errorThrown) {
                $('.overlay').hide();
                console.log('Erro na alteração');

            },

        });

    })

    $("#addComposicao").click(function () {
        texto = ' não pode ser vazio!';
        if ($('#ep').val() === '') {
            abreAlert('O campo EP' + texto);
            $('#ep').focus();
            return false;
        }
        if ($('#material_id').val() === '') {
            abreAlert('O campo  Material não selecionado');
            $('#material_id').focus();
            return false;
        }
        if ($('#qtde').val() === '') {
            abreAlert('O campo  Qtde' + texto);
            $('#qtde').focus();
            return false;
        }
        $('#table_composicao tbody').append(
            '<tr class="blank_' + $('#blank').val() + '">' +
            '<th data-name="blank" class="blank" scope="row">' + $('#blank').val() + '</th>' +
            '<td data-name="qtde" class="qtde">' + $('#qtde').val() + '</td>' +
            '<td data-name="material_id" class="material_id" data-materialid="' + $('#material_id option:selected').val() + '" >' + $('#material_id option:selected').text() + '</td>' +
            '<td data-name="medidax" class="medidax">' + $('#medidax').val() + '</td>' +
            '<td data-name="mediday" class="mediday">' + $('#mediday').val() + '</td>' +
            '<td data-name="tempo_usinagem" class="tempo_usinagem">' + $('#tempo_usinagem').val() + '</td>' +
            '<td data-name="tempo_acabamento" class="tempo_acabamento">' + $('#tempo_acabamento').val() + '</td>' +
            '<td data-name="tempo_montagem" class="tempo_montagem">' + $('#tempo_montagem').val() + '</td>' +
            '<td data-name="tempo_montagem_torre" class="tempo_montagem_torre">' + $('#tempo_montagem_torre').val() + '</td>' +
            '<td data-name="tempo_inspecao" class="tempo_inspecao">' + $('#tempo_inspecao').val() + '</td>' +
            '<td><button type="button" class="close" aria-label="Close" data-blank="' + $('#blank').val() + '">' +
            '<span aria-hidden="true">&times;</span>' +
            '</button>' +
            '</td>' +
            '</tr>');

        calculaTempos();
    });

    $(document).on('click', '#table_composicao .close', function () {
        id = $(this).data('blank');
        $('.blank_' + id).remove();
        calculaTempos();
    });

    $(document).on('click', 'button.close', function () {
        $('.toast').hide('slow');
    });

    function abreAlert(texto) {
        $('.textoAlerta').text(texto);
        $('.toast').show();
        setTimeout(function () {
            $('.toast').hide('slow');
        }, 7000);
    };


    function calculaTempos() {
        somatempo_usinagem = somatempo_acabamento = somatempo_montagem = somamontagem_torre = somatempo_inspecao = 0;
        $('.tempo_usinagem').each(function (i, e) {
            if (e.textContent != '') {
                valor = parseFloat(e.textContent.replace(',', '.'));
                somatempo_usinagem = somatempo_usinagem + valor;
                somatempo_usinagem = calculaQuantidade(i, somatempo_usinagem);
            }
        });

        $('.tempo_acabamento').each(function (i, e) {
            if (e.textContent != '') {
                valor = parseFloat(e.textContent.replace(',', '.'));
                somatempo_acabamento = somatempo_acabamento + valor;
                somatempo_acabamento = calculaQuantidade(i, somatempo_acabamento);
            }
        });

        $('.tempo_montagem').each(function (i, e) {
            if (e.textContent != '') {
                valor = parseFloat(e.textContent.replace(',', '.'));
                somatempo_montagem = somatempo_montagem + valor;
                somatempo_montagem = calculaQuantidade(i, somatempo_montagem);
            }
        });

        $('.tempo_montagem_torre').each(function (i, e) {
            if (e.textContent != '') {
                valor = parseFloat(e.textContent.replace(',', '.'));
                somamontagem_torre = somamontagem_torre + valor;
                somamontagem_torre = calculaQuantidade(i, somamontagem_torre);
            }
        });

        $('.tempo_inspecao').each(function (i, e) {
            if (e.textContent != '') {
                valor = parseFloat(e.textContent.replace(',', '.'));
                somatempo_inspecao = somatempo_inspecao + valor;
                somatempo_inspecao = calculaQuantidade(i, somatempo_inspecao);
            }
        });


        $('#soma_tempo_acabamento').val(somatempo_acabamento.toString().replace('.', ','));
        $('#soma_tempo_montagem_torre').val(somamontagem_torre.toString().replace('.', ','));
        $('#soma_tempo_montagem').val(somatempo_montagem.toString().replace('.', ','));
        $('#soma_tempo_usinagem').val(somatempo_usinagem.toString().replace('.', ','));
        $('#soma_tempo_inspecao').val(somatempo_inspecao.toString().replace('.', ','));

        bloqueiaEP();
    }

    function calculaQuantidade(index, valor) {

        qtde = $('.qtde').eq(index).text();

        var hms = valor;
        var a = hms.split(':'); // split it at the colons

        // minutes are worth 60 seconds. Hours are worth 60 minutes.
        var seconds = (+a[0]) * 60 * 60 + (+a[1]) * 60 + (+a[2]);

        var newSeconds = seconds * qtde;

        var t = new Date();
        t.setSeconds(newSeconds);
       
        return t;
    }

    

    $("#salvar_ficha").click(function () {

        var composicaoep = new Array();

        $('#table_composicao tbody tr').each(function (i, e) {
            json = new Array();
            $(e).find('td').each(function (c, j) {

                if ($(j).data('name') == 'material_id') {
                    json.push('{"' + $(j).data('name') + '":"' + $(j).data('materialid') + '"}');
                } else {
                    json.push('{"' + $(j).data('name') + '":"' + $(j).text() + '"}');
                }

            });
            composicaoep.push(json);
        })





        composicaoep = JSON.stringify(composicaoep);
        json_valores = {
            "composicaoep": composicaoep,
            "soma_tempo_acabamento": $('#soma_tempo_acabamento').val(),
            "soma_tempo_montagem_torre": $('#soma_tempo_montagem_torre').val(),
            "soma_tempo_montagem": $('#soma_tempo_montagem').val(),
            "soma_tempo_usinagem": $('#soma_tempo_usinagem').val(),
            "soma_tempo_inspecao": $('#soma_tempo_inspecao').val(),
        }

        $('#composicoes').val(JSON.stringify(json_valores));
        $('.form_ficha').submit();
    });


}); //fim ready

