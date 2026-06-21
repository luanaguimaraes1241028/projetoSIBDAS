<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();

$id_cifrado = $_GET['id_equipamento'] ?? '';
$id = aes_decrypt($id_cifrado);
if ($id === false || !ctype_digit((string) $id)) {
    header('Location: lista.php');
    exit;
}

try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $ligacao->prepare(
        "SELECT e.*,
                c.nome AS nomeCategoria,
                l.servico AS nomeLocalizacao,
                g.codigo AS codigoGarantia, g.dataInicio, g.dataFim,
                g.tipoContrato, g.entidadeResponsavel, g.periodicidade,
                g.observacoes AS observacoesGarantia
         FROM Equipamento e
         LEFT JOIN Categoria c ON e.codigoCategoria = c.codigo
         LEFT JOIN Localizacao l ON e.codigoLocalizacao = l.codigo
         LEFT JOIN Garantia g ON g.codigoEquipamento = e.codigo
         WHERE e.codigo = :id
         LIMIT 1"
    );
    $stmt->execute([':id' => $id]);
    $equipamento = $stmt->fetch(PDO::FETCH_OBJ);
} catch (PDOException $err) {
    header('Location: lista.php');
    exit;
}

if (!$equipamento) {
    header('Location: lista.php');
    exit;
}
$ligacao = null;

$corEstado = match($equipamento->estado) {
    'ativo'         => 'success',
    'em manutencao' => 'warning',
    'inativo'       => 'danger',
    'em calibracao' => 'info',
    'em quarentena' => 'secondary',
    'abatido'       => 'dark',
    default         => 'secondary'
};

$corCriticidade = match($equipamento->criticidade) {
    'baixa'           => 'success',
    'media'           => 'warning',
    'alta'            => 'danger',
    'suporte de vida' => 'dark',
    default           => 'secondary'
};
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/nav.php'; ?>

    <div class="container-fluid">
        <div class="row">

            <?php include '../includes/sidebar.php'; ?>

            <main class="col-md-9 col-lg-10 px-md-4 pt-4">
                <div class="card shadow-sm border p-4 mx-auto my-4" style="max-width: 850px; background-color: #fff;">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="fw-bold mb-0" style="color: #1e1b4b;">
                            <i class="fa-solid fa-circle-info"></i> Ficha do Equipamento
                        </h2>
                        <span class="badge bg-dark fs-6"><?= htmlspecialchars($equipamento->codigoInterno) ?></span>
                    </div>
                    <hr class="mt-1 mb-4">

                    <ul class="nav nav-tabs mb-4" id="equipamentoTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold py-2 px-3" id="geral-tab" data-bs-toggle="tab" data-bs-target="#geral" type="button" role="tab">
                                <i class="fa-solid fa-sliders"></i> Dados Gerais
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold py-2 px-3" id="garantia-tab" data-bs-toggle="tab" data-bs-target="#garantia" type="button" role="tab">
                                <i class="fa-solid fa-shield-halved"></i> Garantia e Contrato
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold py-2 px-3" id="localizacao-tab" data-bs-toggle="tab" data-bs-target="#localizacao" type="button" role="tab">
                                <i class="fa-solid fa-location-dot"></i> Localização
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content p-2" id="equipamentoTabContent">

                        <div class="tab-pane show active" id="geral" role="tabpanel">
                            <div class="row g-4 text-dark">
                                <div class="col-md-6 border-bottom pb-3">
                                    <small class="text-muted small text-uppercase fw-bold d-block">Código Interno de Inventário</small>
                                    <div class="fs-5 fw-bold text-dark"><?= htmlspecialchars($equipamento->codigoInterno) ?></div>
                                </div>
                                <div class="col-md-6 border-bottom pb-3">
                                    <small class="text-muted small text-uppercase fw-bold d-block">Designação do Equipamento</small>
                                    <div class="fs-5 text-dark fw-semibold"><?= htmlspecialchars($equipamento->designacao) ?></div>
                                </div>
                                <div class="col-md-4 border-bottom pb-3">
                                    <small class="text-muted small text-uppercase fw-bold d-block">Categoria / Grupo</small>
                                    <div class="fs-6 text-dark fw-semibold"><?= htmlspecialchars($equipamento->nomeCategoria ?? '—') ?></div>
                                </div>
                                <div class="col-md-4 border-bottom pb-3">
                                    <small class="text-muted small text-uppercase fw-bold d-block">Marca</small>
                                    <div class="fs-6 text-dark fw-semibold"><?= htmlspecialchars($equipamento->marca) ?></div>
                                </div>
                                <div class="col-md-4 border-bottom pb-3">
                                    <small class="text-muted small text-uppercase fw-bold d-block">Modelo</small>
                                    <div class="fs-6 text-dark fw-semibold"><?= htmlspecialchars($equipamento->modelo) ?></div>
                                </div>
                                <div class="col-md-4 border-bottom pb-3">
                                    <small class="text-muted small text-uppercase fw-bold d-block">Número de Série</small>
                                    <div class="fs-6 font-monospace text-dark fw-semibold"><?= htmlspecialchars($equipamento->numeroSerie) ?></div>
                                </div>
                                <div class="col-md-4 border-bottom pb-3">
                                    <small class="text-muted small text-uppercase fw-bold d-block">Fabricante</small>
                                    <div class="fs-6 text-dark fw-semibold"><?= htmlspecialchars($equipamento->fabricante) ?></div>
                                </div>
                                <div class="col-md-4 border-bottom pb-3">
                                    <small class="text-muted small text-uppercase fw-bold d-block">Ano de Fabrico</small>
                                    <div class="fs-6 text-dark fw-semibold"><?= htmlspecialchars($equipamento->anoFabrico) ?></div>
                                </div>
                                <div class="col-md-4 border-bottom pb-3">
                                    <small class="text-muted small text-uppercase fw-bold d-block">Data de Aquisição</small>
                                    <div class="fs-6 text-dark fw-semibold"><?= htmlspecialchars($equipamento->dataAquisicao) ?></div>
                                </div>
                                <div class="col-md-4 border-bottom pb-3">
                                    <small class="text-muted small text-uppercase fw-bold d-block">Custo de Aquisição</small>
                                    <div class="fs-6 text-dark fw-semibold"><?= number_format((float)$equipamento->custoAquisicao, 2, ',', '.') ?> €</div>
                                </div>
                                <div class="col-md-4 border-bottom pb-3">
                                    <small class="text-muted small text-uppercase fw-bold d-block">Tipo de Entrada</small>
                                    <div class="mt-1">
                                        <span class="badge bg-dark px-2 py-1"><?= htmlspecialchars($equipamento->tipoEntrada) ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6 border-bottom pb-3">
                                    <small class="text-muted small text-uppercase fw-bold d-block">Estado Atual</small>
                                    <div class="mt-1">
                                        <span class="badge bg-<?= $corEstado ?> px-2"><?= htmlspecialchars($equipamento->estado) ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6 border-bottom pb-3">
                                    <small class="text-muted small text-uppercase fw-bold d-block">Criticidade</small>
                                    <div class="mt-1">
                                        <span class="badge bg-<?= $corCriticidade ?> rounded-pill"><?= htmlspecialchars($equipamento->criticidade) ?></span>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <small class="text-muted small text-uppercase fw-bold d-block">Observações Gerais</small>
                                    <p class="mb-0 small text-muted bg-light p-2 rounded border mt-1">
                                        <?= htmlspecialchars($equipamento->observacoes ?? '—') ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane" id="localizacao" role="tabpanel">
                            <div class="row g-4 text-dark">
                                <div class="col-md-12 border-bottom pb-3">
                                    <small class="text-muted small text-uppercase fw-bold d-block">Serviço / Departamento</small>
                                    <div class="fs-6 text-dark fw-semibold"><?= htmlspecialchars($equipamento->nomeLocalizacao ?? '—') ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane" id="garantia" role="tabpanel">
                            <?php if ($equipamento->codigoGarantia): ?>
                            <div class="card shadow-sm border border-success-subtle mb-2">
                                <div class="card-header bg-success-subtle text-success fw-bold">
                                    <i class="fa-solid fa-circle-check"></i> Informação de Garantia
                                </div>
                                <div class="card-body row g-3 text-dark">
                                    <div class="col-md-4 border-bottom pb-2">
                                        <small class="text-muted d-block text-uppercase small fw-bold">Início da Garantia</small>
                                        <span><?= htmlspecialchars($equipamento->dataInicio ?? '—') ?></span>
                                    </div>
                                    <div class="col-md-4 border-bottom pb-2">
                                        <small class="text-muted d-block text-uppercase small fw-bold">Fim da Garantia</small>
                                        <span><?= htmlspecialchars($equipamento->dataFim ?? '—') ?></span>
                                    </div>
                                    <div class="col-md-4 border-bottom pb-2">
                                        <small class="text-muted d-block text-uppercase small fw-bold">Tipo de Contrato</small>
                                        <span><?= htmlspecialchars($equipamento->tipoContrato ?? '—') ?></span>
                                    </div>
                                    <div class="col-md-4 border-bottom pb-2">
                                        <small class="text-muted d-block text-uppercase small fw-bold">Entidade Responsável</small>
                                        <span class="fw-semibold"><?= htmlspecialchars($equipamento->entidadeResponsavel ?? '—') ?></span>
                                    </div>
                                    <div class="col-md-4 border-bottom pb-2">
                                        <small class="text-muted d-block text-uppercase small fw-bold">Periodicidade de Revisão</small>
                                        <span><?= htmlspecialchars($equipamento->periodicidade ?? '—') ?></span>
                                    </div>
                                    <div class="col-md-12">
                                        <small class="text-muted d-block text-uppercase small fw-bold">Observações</small>
                                        <p class="mb-0 small text-muted bg-light p-2 rounded border mt-1"><?= htmlspecialchars($equipamento->observacoesGarantia ?? '—') ?></p>
                                    </div>
                                </div>
                            </div>
                            <?php else: ?>
                            <p class="text-muted">Sem informação de garantia registada.</p>
                            <?php endif; ?>
                        </div>

                    </div>

                    <div class="mt-4 pt-2 border-top d-flex gap-2">
                        <a href="lista.php" class="btn btn-secondary px-4">
                            <i class="fa-solid fa-arrow-left"></i> &ensp;Voltar à Lista
                        </a>
                        <a href="editar.php?id_equipamento=<?= urlencode($id_cifrado) ?>" class="btn btn-outline-primary px-4">
                            <i class="fa-solid fa-pen-to-square"></i> &ensp;Editar
                        </a>
                    </div>
                </div>
            </main>

        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
