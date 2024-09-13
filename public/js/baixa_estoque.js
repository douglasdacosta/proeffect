
$(function ($) {
    //var baseUrl = '/proeffect/public'
    var baseUrl = ''

    $(document).on('click', '.baixar_estoque', function (e) {
        id_estoque = $(this).data('id')
        $.ajax({
            type: "POST",
            url: baseUrl + '/baixar-estoque',
            data: {
                'id': id_estoque,
                '_token': $('meta[name="csrf-token"]').attr('content'),
            },
            success: function (data) {

            },
            error: function (data, textStatus, errorThrown) {

                $('.overlay').hide();
            },

        });
    })
});

