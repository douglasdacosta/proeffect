
$(function ($) {

  $(document).on('click', '.add_funcionarios_projetos', function (e) {

        $('.projeto_id').val($(this).data('projeto_id'));

        $('#modal_funcionarios_projetos').modal('show');
    });

    $(document).on('click', '.add_apontamentos_projetos', function (e) {

        $('.projeto_id').val($(this).data('projeto_id'));

        $('#modal_apontamento_projetos').modal('show');
    });

    $(document).on('click', '.add_tarefa_projetos', function (e) {
        projeto_id = $(this).data('projeto_id');
        $('.projeto_id').val(projeto_id);

        //busca as tarefas da tabela tarefas_projetos
        $.ajax({
            type: "GET",
            url: '/ajax-buscar-tarefas-projetos',
            data: {
                'projeto_id': projeto_id,
                '_token': $('meta[name="csrf-token"]').attr('content'),
            },
            success: function (data) {

                if (data.success) {
                    var tarefas = data.tarefas;
                    var html = '';


                    for (var i = 0; i < tarefas.length; i++) {

                        let datahora = new Date(tarefas[i].data_hora);
                        datahora = datahora.toLocaleDateString('pt-BR', { timeZone: 'America/Sao_Paulo' });

                        let created_at = new Date(tarefas[i].created_at);
                        created_at = created_at.toLocaleDateString('pt-BR', { timeZone: 'America/Sao_Paulo' }) + ' ' + created_at.toLocaleTimeString('pt-BR', { timeZone: 'America/Sao_Paulo' });

                        compromisso = '';

                        if(tarefas[i].funcionario_nome) {
                            compromisso = '<p class="card-text border border-danger font-weight-bold">COMPROMISSO: '+tarefas[i].funcionario_nome+'<br>DATA: '+datahora+'</p> ' +
                                           '<p class="card-text badge badge-secondary"></p>';
                        }


                        html +='<div class="card" >' +
                                    '<div class="card-body">' +
                                        '<h5 class="card-title col-sm-6">Data: '+ created_at +'</h5>' +
                                        '<h5 class="card-title col-sm-5">Criador: '+ tarefas[i].funcionario_criador_nome +'</h5><br>' +
                                        '<p class="card-text">'+tarefas[i].mensagem+'</p>' +
                                        compromisso +
                                    '</div>' +
                                '</div>';
                    }
                    $('#tarefas_list').html(html);
                } else {
                    $('#tarefas_list').html('Nenhuma tarefa encontrada');
                }
            },
            error: function (data, textStatus, errorThrown) {
                $('#tarefas_list').html('Erro ao buscar tarefas: ' + data.responseText);
            },

        });

        $('#modal_tarefa_projetos').modal('show');
    });



    // Adicionar tarefa
    $(document).on('click', '#adicionar_tarefa_projetos', function (e) {
        var projeto_id = $('#projeto_id').val();
        var tarefa_modal = $('#tarefa_modal').val();
        var data_tarefa = $('#data_tarefa_modal').val();
        var funcionario_id = $('#funcionarios_id_modal').val();

        alert(funcionario_id);
        if (tarefa_modal === '') {
            alert('Tarefa é obrigatória');
            return false;
        }

        $.ajax({
            type: "POST",
            url: '/ajax-adicionar-tarefa-projetos',
            data: {
                'projeto_id': projeto_id,
                'tarefa_modal': tarefa_modal,
                'data_tarefa': data_tarefa,
                'funcionario_id': funcionario_id,
                '_token': $('meta[name="csrf-token"]').attr('content'),
            },
            success: function (data) {

                if (data.success) {
                    alert('Tarefa adicionada com sucesso');
                } else {
                    alert(data.responseText);
                }
            },
            error: function (data, textStatus, errorThrown) {
                alert('Erro ao adicionar tarefa: ' + data.responseText);
            },

        });
    });

    // Adicionar apontamento
    $(document).on('click', '#adicionar_apontamento_projetos', function (e) {

        if(!confirm('Tem certeza que deseja adicionar este apontamento?')) {
            return false;
        }

        var projeto_id = $('#projeto_id').val();

        var apontamento_id = $('#apontamento_id').val();

        if (apontamento_id === '') {
            alert('Apontamento é obrigatório');
            return false;
        }

        $.ajax({
            type: "POST",
            url: '/ajax-adicionar-apontamento-projetos',
            data: {
                'projeto_id': projeto_id,
                'apontamento_id': apontamento_id,
                '_token': $('meta[name="csrf-token"]').attr('content'),
            },
            success: function (data) {

                if (data.success) {
                    alert('Apontamento adicionado com sucesso');

                } else {
                    alert(data.responseText);
                }
            },
            error: function (data, textStatus, errorThrown) {

                alert('Erro ao adicionar apontamento: ' + data.responseText);
                $('.overlay').hide();
            },

        });
    })

    //antes de submit validar o campo tempo_projetos e  tempo_programacao
    $(document).on('submit', '#form_projetos', function (e) {

        if($('#status_id').val() == 1) {

            var tempo_projetos = $('#tempo_projetos').val().trim();
            var tempo_programacao = $('#tempo_programacao').val().trim();

            if (tempo_projetos == '') {
                alert('Tempo de projetos não pode ser vazio');
                $('#tempo_projetos').focus();
                $('#tempo_projetos').css('border', '1px solid red');
                return false;
            }

            if (tempo_programacao === '') {
                alert('Tempo de programação não pode ser vazio');
                $('#tempo_programacao').focus();
                $('#tempo_programacao').css('border', '1px solid red');
                return false;
            }

        }
    });

    // Adicionar funcionário
    $(document).on('click', '#adicionar_funcionario_projetos', function (e) {

        var projeto_id = $('#projeto_id').val();
        var funcionario_id = $('#funcionarios_id').val();

        if (funcionario_id === '') {
            alert('Funcionário é obrigatório');
            return false;
        }

        $.ajax({
            type: "POST",
            url: '/ajax-adicionar-funcionario-projetos',
            data: {
                'projeto_id': projeto_id,
                'funcionario_id': funcionario_id,
                '_token': $('meta[name="csrf-token"]').attr('content'),
            },
            success: function (data) {

                if (data.success) {
                    alert('Funcionário adicionado com sucesso');
                } else {
                    alert(data.message);
                }
            },
            error: function (data, textStatus, errorThrown) {

                $('.overlay').hide();
            },

        });
    });

    $('.data_tarefa_modal').hide();
    $(document).on('change', '.funcionarios_id_modal', function (e) {
        var funcionario_id = $(this).val(); // já retorna o valor do option selecionado

        if (funcionario_id == '') {
            $('.data_tarefa_modal').hide();
        } else {
            $('.data_tarefa_modal').show();
        }
    });

    $(document).on('change', '.pesquisa_status_id', function (e) {

        var projeto = $(this).data('projeto');
        var status = $(this).val();

        //se status = 1 redirecionar para alterar-projetos?id=170"
        if (status == 1) {
            $.ajax({
            type: "GET",
            url: '/projetos-consulta-detalhes/' + projeto + '/',
            data: {
                'projeto_id': projeto,
                '_token': $('meta[name="csrf-token"]').attr('content'),
            },
            success: function (data) {

                if (data.projeto.tempo_projetos == null || data.projeto.tempo_programacao == null) {

                    alert('Obrigatório preencher o tempo de projetos e tempo de programação para LIBERADO PARA PROJETOS. Você será redirecionado para a página de alteração do projeto.');
                    window.location.href = '/alterar-projetos?id=' + projeto;
                } else {
                    alteraStatusProjeto(projeto, status);
                }

            },
            error: function (data, textStatus, errorThrown) {

                alert('Erro ao alterar status: ' + data.responseText);
                $('.overlay').hide();
            },

            });
        } else {
            $('.overlay').show();
            alteraStatusProjeto(projeto, status);
        }

    });

    function alteraStatusProjeto(projeto, status) {
        $('.overlay').show();
                    $.ajax({
                        type: "POST",
                        url: '/ajax-alterar-status-projetos',
                        data: {
                            'projeto_id': projeto,
                            'status': status,
                            '_token': $('meta[name="csrf-token"]').attr('content'),
                        },
                        success: function (data) {
                            $('.overlay').hide();
                            if (data.success) {
                                alert('Status alterado com sucesso');
                                //recarrega a tela sem histórico
                                location.reload();
                            } else {
                                alert(data.message);
                            }

                        },
                        error: function (data, textStatus, errorThrown) {

                            alert('Erro ao alterar status: ' + data.responseText);
                            $('.overlay').hide();
                        },

                    });
    }

    $(document).on('change', '.pesquisa_etapas_projetos', function (e) {
        var projeto = $(this).data('projeto');
        var sub_status_projetos_codigo = $(this).data('sub_status_projetos_codigo');
        var etapa = $(this).val();

        $('.overlay').show();
        $.ajax({
            type: "POST",
            url: '/ajax-alterar-etapas-projetos',
            data: {
                'projeto_id': projeto,
                'etapa': etapa,
                'sub_status_projetos_codigo': sub_status_projetos_codigo,
                '_token': $('meta[name="csrf-token"]').attr('content'),
            },
            success: function (data) {
                $('.overlay').hide();
                if (data.success) {
                    alert('Etapa alterada com sucesso');
                } else {
                    alert(data.message);
                }

            },
            error: function (data, textStatus, errorThrown) {

                alert('Erro ao alterar a etapa: ' + data.responseText);
                $('.overlay').hide();
            },

        });

    });

    $(document).on('click', '.toggle_alerta_projetos', function (e) {
        var projeto_id = $(this).data('projeto_id');
        //muda a cor a linha de acordo com o status atual



        $('.overlay').show();
        $.ajax({
            type: "GET",
            url: '/projetos-ativa-desativa-alerta/' + projeto_id + '/',
            data: {
                'projeto_id': projeto_id,
                '_token': $('meta[name="csrf-token"]').attr('content'),
            },
            success: function (data) {
                $('.overlay').hide();
                $('.linha_' + projeto_id).css('background-color', function() {
                    return $(this).css('background-color') == 'rgb(242, 200, 7)' ? '' : '#F2C807';
                });

                $('.toggle_alerta_projetos[data-projeto_id="' + projeto_id + '"]').css('color', function() {
                    return $(this).css('color') == 'rgb(217, 83, 79)' ? '#12ad04' : '#d9534f';
                });

            },
            error: function (data, textStatus, errorThrown) {

                alert('Erro ao alterar alerta: ' + data.responseText);
                $('.overlay').hide();
            },

        });

    });

});
