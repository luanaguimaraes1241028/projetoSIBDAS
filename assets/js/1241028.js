function inicializarGraficos() {
    // Pega os dados que vieram do servidor/banco de dados
    const dados = window.dashboardData;
    if (!dados) return; // Se não houver dados, para aqui para não dar erro

    // --- 1. Gráfico de Barras (Serviços) ---
    const ctxServicos = document.getElementById('chartServicos');
    if (ctxServicos) {
        new Chart(ctxServicos, {
            type: 'bar', 
            data: {
                labels: dados.servicos.labels, // Nomes nas colunas (Ex: Setor A, Setor B)
                datasets: [{
                    label: 'Número de Equipamentos',
                    data: dados.servicos.data,       // Os números/quantidades
                    backgroundColor: dados.servicos.colors, // Cores das barras
                    borderWidth: 0,
                    borderRadius: 6                  // Deixa as pontas das barras arredondadas
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, // Deixa o gráfico se ajustar ao tamanho da tela
                plugins: { legend: { display: false } }, // Esconde a legenda para poupar espaço
                scales: { y: { beginAtZero: true } }    // Garante que o gráfico comece do zero
            }
        });
    }

    // --- 2. Gráfico de Rosca (Criticidade) ---
    const ctxCriticidade = document.getElementById('chartCriticidade');
    if (ctxCriticidade) {
        new Chart(ctxCriticidade, {
            type: 'doughnut', 
            data: {
                labels: dados.criticidade.labels, // Categorias (Ex: Alto, Médio, Baixo)
                datasets: [{
                    data: dados.criticidade.data,       // Os valores de cada fatia
                    backgroundColor: dados.criticidade.colors, // Cores de cada nível
                    borderWidth: 2,
                    borderColor: '#ffffff'              // Linha branca separando as fatias
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right', // Coloca a legenda no lado direito
                        labels: {
                            font: { family: 'Nunito', size: 12 }, // Fonte do texto
                            boxWidth: 15
                        }
                    }
                }
            }
        });
    }
}