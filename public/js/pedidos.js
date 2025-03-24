

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

    $(document).on('click', '.whatsapp_status', function (elemento) {
        var id_pessoa = $(this).data('id_pessoa');
        var whatsapp_status = $(this).data('whatsapp_status');
        var link = $(this).data('link');
        var el = $(this);
        
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
                       
                whatsapp_status = el.attr('data-whatsapp_status');
                if(whatsapp_status == 1) {
                    el.attr('style', 'color: red !important;');
                    el.attr('data-whatsapp_status',0);
                } else {
                    el.attr('style', 'color: green !important;');
                    el.attr('data-whatsapp_status',1);
                    //open a new table os browser with the whatsapp link
                    window.open(link, '_blank');
                 }
                

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
