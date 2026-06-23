
function inicializarGraficos() {
    const dados = window.dashboardData;
    if (!dados) return;

    const ctxServicos = document.getElementById('chartServicos');
    if (ctxServicos) {
        new Chart(ctxServicos, {
            type: 'bar',
            data: {
                labels: dados.servicos.labels,
                datasets: [{
                    label: 'Número de Equipamentos',
                    data: dados.servicos.data,
                    backgroundColor: dados.servicos.colors,
                    borderWidth: 0,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    }

    const ctxCriticidade = document.getElementById('chartCriticidade');
    if (ctxCriticidade) {
        new Chart(ctxCriticidade, {
            type: 'doughnut',
            data: {
                labels: dados.criticidade.labels,
                datasets: [{
                    data: dados.criticidade.data,
                    backgroundColor: dados.criticidade.colors,
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            font: { family: 'Nunito', size: 12 },
                            boxWidth: 15
                        }
                    }
                }
            }
        });
    }
}
