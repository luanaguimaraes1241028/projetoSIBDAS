<?php
require_once '../includes/funcoes.php';
redirect_if_not_logged();

try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
        MYSQL_USERNAME, MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $counts = $ligacao->query(
        "SELECT
            COUNT(*)                              AS total,
            SUM(estado = 'ativo')                 AS ativos,
            SUM(estado = 'em manutencao')         AS manutencao,
            SUM(estado = 'inativo')               AS inativos
         FROM Equipamento"
    )->fetch(PDO::FETCH_OBJ);
} catch (PDOException) {
    $counts = (object)['total' => 0, 'ativos' => 0, 'manutencao' => 0, 'inativos' => 0];
}
$ligacao = null;
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/nav.php'; ?>

    <div class="container-fluid">
        <div class="row">
            
            <?php include '../includes/sidebar.php'; ?>

            <main class="col-md-9 col-lg-10 px-md-4 pt-4">
                
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <div>
                        <h1 class="h2 fw-bold" style="color: #1e1b4b;">Estado Global do Parque Tecnológico</h1>
                        <p class="text-muted">Visão rápida e sintética para apoio à tomada de decisão técnica.</p>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card p-3 shadow-sm h-100 border-start border-0 border-4 border-secondary">
                            <div class="card-body p-0">
                                <h6 class="card-subtitle mb-2 text-muted text-uppercase fw-bold small">Total Equipamentos</h6>
                                <h2 class="card-title mb-0 fw-bold"><?= $counts->total ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card p-3 shadow-sm h-100 border-start border-0 border-4 border-success">
                            <div class="card-body p-0">
                                <h6 class="card-subtitle mb-2 text-muted text-uppercase fw-bold small">Equipamentos Ativos</h6>
                                <h2 class="card-title mb-0 fw-bold text-success"><?= $counts->ativos ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card p-3 shadow-sm h-100 border-start border-0 border-4 border-warning">
                            <div class="card-body p-0">
                                <h6 class="card-subtitle mb-2 text-muted text-uppercase fw-bold small">Em Manutenção</h6>
                                <h2 class="card-title mb-0 fw-bold text-warning"><?= $counts->manutencao ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card p-3 shadow-sm h-100 border-start border-0 border-4 border-danger">
                            <div class="card-body p-0">
                                <h6 class="card-subtitle mb-2 text-muted text-uppercase fw-bold small">Equipamentos Inativos</h6>
                                <h2 class="card-title mb-0 fw-bold text-danger"><?= $counts->inativos ?></h2>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <h5 class="fw-bold mb-3"><i class="fa-solid fa-triangle-exclamation text-warning"></i> Alertas do Sistema</h5>
                    <div class="alert alert-danger shadow-sm py-2 px-3 mb-2" role="alert">
                        <strong>Garantia Expirada:</strong> 4 equipamentos necessitam de revisão de contrato.
                    </div>
                    <div class="alert alert-danger shadow-sm py-2 px-3 mb-2" role="alert">
                        <strong>Falta Documentação:</strong> 6 equipamentos sem manual técnico associado.
                    </div>
                    <div class="alert alert-warning shadow-sm py-2 px-3" role="alert">
                        <strong>Garantias a expirar (30 dias):</strong> 2 dispositivos médicos próximos do fim.
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-12 col-lg-6">
                        <div class="p-4 bg-white border rounded shadow-sm">
                            <h5 class="fw-bold mb-3 text-dark"><i class="fa-solid fa-chart-bar"></i> Equipamentos por Serviço Clínico</h5>
                            <p class="text-muted small">Distribuição volumétrica por blocos hospitalares.</p>
                            <hr>
                            <div style="position: relative; height:250px;">
                                <canvas id="chartServicos"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6">
                        <div class="p-4 bg-white border rounded shadow-sm">
                            <h5 class="fw-bold mb-3 text-dark"><i class="fa-solid fa-chart-pie"></i> Proporção de Criticidade</h5>
                            <p class="text-muted small">Análise de risco clínico e suporte de vida.</p>
                            <hr>
                            <div style="position: relative; height:250px;">
                                <canvas id="chartCriticidade"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>

<?php include '../includes/footer.php'; ?>