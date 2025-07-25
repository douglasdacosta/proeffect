function copiarTexto() {
    const texto = document.getElementById("texto_link").innerText;

    if (!texto) {
        alert("⚠️ O texto está vazio!");
        return;
    }

    const textarea = document.createElement("textarea");
    textarea.value = texto;
    textarea.setAttribute('readonly', '');
    textarea.style.position = 'absolute';
    textarea.style.left = '-9999px';

    document.body.appendChild(textarea);

    textarea.select();


    try {
        const sucesso = document.execCommand('copy');
        abreAlertSuccess('Texto copiado para área de trênsferencia', false)
    } catch (err) {
        abreAlertSuccess('Falha ao copiar para área de trênsferencia', true)
    }

    document.body.removeChild(textarea);
}

$(function ($) {

    $(document).on('click', '.faturados', function (elemento) {
        var pedido = $(this).data('pedido');
        var status_faturado = $(this).data('status_faturado');
        var el = $(this);

        $.ajax({
            type: "POST",
            url: '/ajax-faturado',
            data: {
                'id': pedido,
                'status_faturado': status_faturado,
                '_token': $('meta[name="csrf-token"]').attr('content'),
            },
            success: function (data) {

                abreAlertSuccess('Pedido atualizado', false);

                status_faturado = el.attr('data-status_faturado');
                if(status_faturado == 1) {
                    el.attr('style', 'color: #red !important;');
                    el.attr('data-status_faturado',0);
                } else {
                    el.attr('style', 'color: green !important;');
                    el.attr('data-status_faturado',1);
                 }


            },
            error: function (data, textStatus, errorThrown) {

                $('.overlay').hide();
            },

        });

    });

    $(document).on('click', '#copiar_link', function () {

        const texto = document.getElementById("texto_link").innerText;

        const textarea = document.createElement("textarea");
        textarea.value = texto;
        textarea.style.position = 'fixed';
        textarea.style.top = 0;
        textarea.style.left = 0;
        textarea.style.opacity = 0;

        document.body.appendChild(textarea);

        textarea.focus();
        textarea.select();

        // Executa o copy depois de um pequeno delay
        setTimeout(() => {
            try {
                const sucesso = document.execCommand('copy');
                alert(sucesso ? '✅ Copiado!' : '❌ Falhou ao copiar.');
            } catch (err) {
                alert('❌ Erro ao copiar: ' + err);
            }


        }, 50);

        setTimeout(() => {
            document.body.removeChild(textarea);
        }, 150);
    })

    $(document).on('click', '.whatsapp_status', function (e) {

        let whatsapp_status =  Number($(this).attr('data-whatsapp_status'));
        $('#input_id_pessoa').val($(this).data('id_pessoa'));
        $('#input_whatsapp_status').val(whatsapp_status);

        $('#input_link_envio').val($(this).data('link'));
        $('#texto_link').text($(this).data('link_eplax'));

        if (whatsapp_status === 0 || whatsapp_status == '' ) {
            copiarTexto()
        }

        setTimeout(() => {

            if (whatsapp_status === 1) {
                var id_pessoa = $('#input_id_pessoa').val();
                var el = $('.icone_' + id_pessoa);

                $.ajax({
                    type: "POST",
                    url: '/ajax-whatsapp-status',
                    data: {
                        'id': id_pessoa,
                        'whatsapp_status': whatsapp_status,
                        '_token': $('meta[name="csrf-token"]').attr('content'),
                    },
                    success: function (data) {

                        abreAlertSuccess('WhatsApp atualizado', false);
                        el.attr('style', 'color: red !important; cursor: pointer;');
                        el.attr('data-whatsapp_status', 0).data('whatsapp_status', 0);
                    },
                    error: function (data, textStatus, errorThrown) {

                        $('.overlay').hide();
                    },

                });
            } else {
                $('#modal_whatsapp').modal('show');
            }
        }, 50);

    });

    // https://eplax.com.br/consultar-pedido/140f6969d5213fd0ece03148e62e461e/consulta/


    $(document).on('click', '#enviar_link', function (elemento) {
        var id_pessoa = $('#input_id_pessoa').val();
        var whatsapp_status = $('#input_whatsapp_status').val();
        var link = $('#input_link_envio').val();
        var el = $('.icone_' + id_pessoa);
        $.ajax({
            type: "POST",
            url: '/ajax-whatsapp-status',
            data: {
                'id': id_pessoa,
                'whatsapp_status': whatsapp_status,
                '_token': $('meta[name="csrf-token"]').attr('content'),
            },
            success: function (data) {

                abreAlertSuccess('WhatsApp atualizado', false);
                el.attr('style', 'color: green !important; cursor: pointer;');
                el.attr('data-whatsapp_status',1);
                window.open(link, '_blank');



            },
            error: function (data, textStatus, errorThrown) {

                $('.overlay').hide();
            },

        });

    });

    $(document).on('click', '#btn_limpar_faturado', function () {

        if(!confirm('Tem certeza que deseja limpar o faturado?')) {
            return false;
        }

        $.ajax({
            type: "POST",
            url: '/ajax-limpar-faturado',
            data: {
                '_token': $('meta[name="csrf-token"]').attr('content'),
            },
            success: function (data) {
                abreAlertSuccess('Pedidos atualizados', false);
                $('.faturados').prop('checked', false);
            },
            error: function (data, textStatus, errorThrown) {

                $('.overlay').hide();
            },

        });

    });


});
