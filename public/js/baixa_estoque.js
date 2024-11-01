
$(function ($) {
    //var baseUrl = '/proeffect/public'
    var baseUrl = ''

    $(document).on('click', '.baixar_estoque', function (e) {
        id_estoque = $(this).data('id')
        if (!confirm("Confirma baixar estoque?")) {
            return false;
        }
        $.ajax({
            type: "POST",
            url: baseUrl + '/baixar-estoque',
            data: {
                'id': id_estoque,
                '_token': $('meta[name="csrf-token"]').attr('content'),
            },
            success: function (data) {
                history.replaceState(null, null, window.location.pathname);
                window.location.href = baseUrl + '/tela-baixa-estoque?salvo=true';
            },
            error: function (data, textStatus, errorThrown) {

                $('.overlay').hide();
            },

        });
    })
});

