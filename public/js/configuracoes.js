
$(function ($) {
    //var baseUrl = '/proeffect/public'
    var baseUrl = ''

    $(document).on('click', '#atualiza_dados', function (e) {
        id_estoque = $(this).data('id')
        usuario = $(this).data('usuario')
        
        $('#tipo_atualizacao').val(1)


        setTimeout(function () {
            $('#alterar_configuracoes').submit();
        }, 500);
        
    })
});

