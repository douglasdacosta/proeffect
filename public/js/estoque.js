

$(function ($) {

    $(document).on('click', '.inventarios', function () {

        var estoque = $(this).data('estoque');
        var id = $(this).attr('id');
        var checked = $(this).prop('checked');


        $.ajax({
            type: "POST",
            url: '/ajax-inventario',
            data: {
                'id': estoque,
                'inventario': checked,
                '_token': $('meta[name="csrf-token"]').attr('content'),
            },
            success: function (data) {
                abreAlertSuccess('Inventário atualizado', true);
            },
            error: function (data, textStatus, errorThrown) {

                $('.overlay').hide();
            },

        });
    });

    $(document).on('click', '#btn_limpar_inventario', function () {

        if(!confirm('Tem certeza que deseja limpar o inventário?')) {
            return false;
        }

        $.ajax({
            type: "POST",
            url: '/ajax-limpar-inventario',
            data: {
                '_token': $('meta[name="csrf-token"]').attr('content'),
            },
            success: function (data) {
                abreAlertSuccess('Inventário atualizado', true);
                $('.inventarios').prop('checked', false);
            },
            error: function (data, textStatus, errorThrown) {

                $('.overlay').hide();
            },

        });

    });


});
