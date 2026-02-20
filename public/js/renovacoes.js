$(document).ready(function() {

    if ($('.table_renovacoes').length) {
        $('.table_renovacoes').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "language": {
                "sEmptyTable": "Nenhum registro encontrado",
                "sInfo": "Mostrando _START_ até _END_ de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
                "sInfoFiltered": "(filtrado de _MAX_ registros no total)",
                "sInfoPostFix": "",
                "sInfoThousands": ".",
                "sLengthMenu": "Mostrar _MENU_ registros por página",
                "sLoadingRecords": "Carregando...",
                "sProcessing": "Processando...",
                "sSearch": "Pesquisar:",
                "sUrl": "",
                "sZeroRecords": "Nenhum registro encontrado",
                "oPaginate": {
                    "sFirst": "Primeiro",
                    "sLast": "Último",
                    "sNext": "Próximo",
                    "sPrevious": "Anterior"
                },
                "oAria": {
                    "sSortAscending": ": ativar para ordenar coluna ascendente",
                    "sSortDescending": ": ativar para ordenar coluna descendente"
                }
            },
            "columnDefs": [
                {
                    "targets": [10, 11, 12],
                    "orderable": false,
                    "searchable": false
                }
            ]
        });
    }

    $(document).on('click', '.btn-finalizar-renovacao', function () {
        var id = $(this).data('id');
        $('#finalizar_renovacao_id').val(id);
        $('#gerar_nova_renovacao').prop('checked', false);
        $('#modal_finalizar_renovacao').modal('show');
    });

    $(document).on('submit', '#incluir, #alterar, #form_finalizar_renovacao', function (e) {
        var $form = $(this);
        if ($form.data('submitting')) {
            e.preventDefault();
            return false;
        }

        $form.data('submitting', true);
        $form.find('button[type="submit"]').prop('disabled', true);
    });
});
