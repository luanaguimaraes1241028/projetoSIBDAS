<?php
require_once '../includes/funcoes.php';
redirect_if_not_logged();

$cores = ['#4f46e5', '#06b6d4', '#f59e0b', '#10b981', '#ef4444', '#8b5cf6', '#f97316', '#14b8a6'];
$critMap = [
    'suporte de vida' => ['label' => 'Suporte de Vida', 'color' => '#ef4444'],
    'alta'            => ['label' => 'Alta',             'color' => '#f97316'],
    'media'           => ['label' => 'Média',            'color' => '#f59e0b'],
    'baixa'           => ['label' => 'Baixa',            'color' => '#10b981'],
];

try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
        MYSQL_USERNAME, MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $counts = $ligacao->query(
        "SELECT COUNT(*) AS total, SUM(estado='ativo') AS ativos,
                SUM(estado='em manutencao') AS manutencao, SUM(estado='inativo') AS inativos
         FROM Equipamento"
    )->fetch(PDO::FETCH_OBJ);

    $garantiasExpiradas = (int) $ligacao->query(
        "SELECT COUNT(*) FROM Garantia WHERE dataFim < CURDATE()"
    )->fetchColumn();

    $semDocumentacao = (int) $ligacao->query(
        "SELECT COUNT(*) FROM Equipamento e
         WHERE NOT EXISTS (
             SELECT 1 FROM Documentacao d
             WHERE d.codigoEquipamento = e.codigo AND d.ativo = 1
         )"
    )->fetchColumn();

    $garantiasAExpirar = (int) $ligacao->query(
        "SELECT COUNT(*) FROM Garantia
         WHERE dataFim >= CURDATE() AND dataFim <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)"
    )->fetchColumn();

    $rowsServico = $ligacao->query(
        "SELECT IFNULL(l.servico, 'Sem Localização') AS servico, COUNT(*) AS total
         FROM Equipamento e
         LEFT JOIN Localizacao l ON l.codigo = e.codigoLocalizacao
         GROUP BY servico
         ORDER BY total DESC
         LIMIT 8"
    )->fetchAll(PDO::FETCH_OBJ);

    $rowsCrit = $ligacao->query(
        "SELECT criticidade, COUNT(*) AS total FROM Equipamento
         GROUP BY criticidade
         ORDER BY FIELD(criticidade, 'suporte de vida', 'alta', 'media', 'baixa')"
    )->fetchAll(PDO::FETCH_OBJ);

} catch (PDOException) {
    $counts             = (object)['total' => 0, 'ativos' => 0, 'manutencao' => 0, 'inativos' => 0];
    $garantiasExpiradas = 0;
    $semDocumentacao    = 0;
    $garantiasAExpirar  = 0;
    $rowsServico        = [];
    $rowsCrit           = [];
}
$ligacao = null;

$labelsServicos = array_map(fn($r) => $r->servico, $rowsServico);
$dataServicos   = array_map(fn($r) => (int)$r->total, $rowsServico);
$colorsServicos = array_slice($cores, 0, count($labelsServicos));

$labelsCrit = array_map(fn($r) => $critMap[$r->criticidade]['label'] ?? ucfirst($r->criticidade), $rowsCrit);
$dataCrit   = array_map(fn($r) => (int)$r->total, $rowsCrit);
$colorsCrit = array_map(fn($r) => $critMap[$r->criticidade]['color'] ?? '#6b7280', $rowsCrit);

$temAlertas = $garantiasExpiradas > 0 || $semDocumentacao > 0 || $garantiasAExpirar > 0;
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
                    <?php if (!$temAlertas): ?>
                    <div class="alert alert-success shadow-sm py-2 px-3" role="alert">
                        <i class="fa-solid fa-circle-check me-1"></i> <strong>Sem alertas.</strong> O sistema está operacional.
                    </div>
                    <?php endif; ?>
                    <?php if ($garantiasExpiradas > 0): ?>
                    <div class="alert alert-danger shadow-sm py-2 px-3 mb-2" role="alert">
                        <strong>Garantia Expirada:</strong> <?= $garantiasExpiradas ?> equipamento<?= $garantiasExpiradas > 1 ? 's' : '' ?> necessita<?= $garantiasExpiradas > 1 ? 'm' : '' ?> de revisão de contrato.
                    </div>
                    <?php endif; ?>
                    <?php if ($semDocumentacao > 0): ?>
                    <div class="alert alert-danger shadow-sm py-2 px-3 mb-2" role="alert">
                        <strong>Falta Documentação:</strong> <?= $semDocumentacao ?> equipamento<?= $semDocumentacao > 1 ? 's' : '' ?> sem documentação associada.
                    </div>
                    <?php endif; ?>
                    <?php if ($garantiasAExpirar > 0): ?>
                    <div class="alert alert-warning shadow-sm py-2 px-3" role="alert">
                        <strong>Garantias a expirar (30 dias):</strong> <?= $garantiasAExpirar ?> dispositivo<?= $garantiasAExpirar > 1 ? 's' : '' ?> médico<?= $garantiasAExpirar > 1 ? 's' : '' ?> próximo<?= $garantiasAExpirar > 1 ? 's' : '' ?> do fim.
                    </div>
                    <?php endif; ?>
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

<script>
window.dashboardData = {
    servicos: {
        labels: <?= json_encode($labelsServicos) ?>,
        data:   <?= json_encode($dataServicos) ?>,
        colors: <?= json_encode($colorsServicos) ?>
    },
    criticidade: {
        labels: <?= json_encode($labelsCrit) ?>,
        data:   <?= json_encode($dataCrit) ?>,
        colors: <?= json_encode($colorsCrit) ?>
    }
};
</script>

<?php include '../includes/footer.php'; ?>
