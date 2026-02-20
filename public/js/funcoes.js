
$(function ($) {


    $(document).on('click', '.marcar_lido', function () {
        var tarefa = $(this).data('id');
        $.ajax({
            type: "POST",
            url: '/marcar-tarefa-lida',
            data: {
                'tarefa': tarefa,
                '_token': $('meta[name="csrf-token"]').attr('content'),
            },
            success: function (data) {
                alert('Tarefa marcada como lida');
                $('#tarefa_' + tarefa ).removeClass('marcar_lido');
                $('#tarefa_' + tarefa + ' span' ).text('Tarefa lida ');
                $('#tarefa_' + tarefa +' i').removeClass('text-danger').addClass('text-success');

            },
            error: function (data, textStatus, errorThrown) {
                alert('Erro ao marcar a tarefa como lida');
            },
        });

    });

    $(document).on('click', '.marcar_lido_renovacao', function () {
        var renovacao = $(this).data('id');
        $.ajax({
            type: "POST",
            url: '/marcar-renovacao-lida',
            data: {
                'renovacao': renovacao,
                '_token': $('meta[name="csrf-token"]').attr('content'),
            },
            success: function (data) {
                alert('Renovação marcada como lida');
                $('#renovacao_' + renovacao ).removeClass('marcar_lido_renovacao');
                $('#renovacao_' + renovacao + ' span' ).text('Renovação lida ');
                $('#renovacao_' + renovacao +' i').removeClass('text-danger').addClass('text-success');

            },
            error: function (data, textStatus, errorThrown) {
                alert('Erro ao marcar a renovação como lida');
            },
        });

    });


}); //FIM DO BLOCO DE JQUERY READY

