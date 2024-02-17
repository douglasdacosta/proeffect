
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



