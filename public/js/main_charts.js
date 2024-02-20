
$(function ($) {
    const ctx = $('#grafico');
    const valores = new Array();
    const colunas = new Array();
    var dados_valores ='';
    var dados_colunas = '';

    $.ajax({
        type: "POST",
        url: '/ajax-getProducao',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {


            colunas.push('Total de pedidos');
            valores.push(data.total_pedidos);

            Object.keys(data.totais).forEach(function(key, el) {
                colunas.push(key);
                valores.push(data.totais[key].qtde);
              });


            new Chart(ctx, {
                type: 'bar',
                data: {
                labels: colunas,
                datasets: [{
                        label: 'Produção',
                        data: valores,
                        borderWidth: 2
                    },
                ]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

        },
        error: function (data, textStatus, errorThrown) {
            alert('Erro na consulta de produção')
        },

    });




});


