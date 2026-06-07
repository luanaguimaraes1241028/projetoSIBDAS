

function inicializarGraficos() {
    // --- 1. GRÁFICO DE BARRAS: EQUIPAMENTOS POR SERVIÇO ---
    const ctxServicos = document.getElementById('chartServicos');
    if (ctxServicos) {
        new Chart(ctxServicos, {
            type: 'bar',
            data: {
                labels: ['Bloco Operatório', 'Cuidados Intensivos (UCIP)', 'Radiologia / Imagiologia'],
                datasets: [{
                    label: 'Número de Equipamentos',
                    data: [65, 48, 29], // Dados do teu parque
                    backgroundColor: [
                        '#4f46e5', // Indigo
                        '#06b6d4', // Cyan
                        '#f59e0b'  // Amber
                    ],
                    borderWidth: 0,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false } // Esconde a legenda individual porque já tem o título
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    // --- 2. GRÁFICO DE PIZZA: PROPORÇÃO DE CRITICIDADE ---
    const ctxCriticidade = document.getElementById('chartCriticidade');
    if (ctxCriticidade) {
        new Chart(ctxCriticidade, {
            type: 'doughnut', 
            data: {
                labels: ['Elevada (Suporte de Vida)', 'Média (Diagnóstico)', 'Baixa (Geral)'],
                datasets: [{
                    data: [30, 84, 28], // Valores proporcionais
                    backgroundColor: [
                        '#ef4444', // Vermelho (Elevada)
                        '#f97316', // Laranja (Média)
                        '#10b981'  // Verde (Baixa)
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right', // Coloca as legendas ao lado do gráfico
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