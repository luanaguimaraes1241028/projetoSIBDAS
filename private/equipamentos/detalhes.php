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
                l.edificio, l.piso, l.servico AS nomeLocalizacao, l.sala,
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

    $stmtForn = $ligacao->prepare(
        "SELECT f.codigo, f.nome, f.tipoFornecedor, f.telefone, f.email, f.pessoaContacto
         FROM EquipamentoFornecedor ef
         JOIN Fornecedor f ON ef.codigoFornecedor = f.codigo
         WHERE ef.codigoEquipamento = :id"
    );
    $stmtForn->execute([':id' => $id]);
    $fornecedores = $stmtForn->fetchAll(PDO::FETCH_OBJ);

    $stmtDoc = $ligacao->prepare(
        "SELECT d.codigo, d.nome, d.tipo, d.dataDocumento, d.dataValidade, d.ficheiro
         FROM Documentacao d
         WHERE d.codigoEquipamento = :id
         ORDER BY d.dataDocumento DESC"
    );
    $stmtDoc->execute([':id' => $id]);
    $documentos = $stmtDoc->fetchAll(PDO::FETCH_OBJ);
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
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold py-2 px-3" id="fornecedor-tab" data-bs-toggle="tab" data-bs-target="#fornecedor" type="button" role="tab">
                                <i class="fa-solid fa-truck-field"></i> Fornecedores
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold py-2 px-3" id="documentacao-tab" data-bs-toggle="tab" data-bs-target="#documentacao" type="button" role="tab">
                                <i class="fa-solid fa-folder-open"></i> Documentação
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
                            <?php if ($equipamento->nomeLocalizacao): ?>
                            <div class="row g-4 text-dark">
                                <div class="col-md-6 border-bottom pb-3">
                                    <small class="text-muted small text-uppercase fw-bold d-block">Edifício</small>
                                    <div class="fs-6 text-dark fw-semibold"><?= htmlspecialchars($equipamento->edificio ?? '—') ?></div>
                                </div>
                                <div class="col-md-6 border-bottom pb-3">
                                    <small class="text-muted small text-uppercase fw-bold d-block">Piso</small>
                                    <div class="fs-6 text-dark fw-semibold"><?= htmlspecialchars($equipamento->piso ?? '—') ?></div>
                                </div>
                                <div class="col-md-6 border-bottom pb-3">
                                    <small class="text-muted small text-uppercase fw-bold d-block">Serviço / Departamento</small>
                                    <div class="fs-6 text-dark fw-semibold"><?= htmlspecialchars($equipamento->nomeLocalizacao) ?></div>
                                </div>
                                <div class="col-md-6 border-bottom pb-3">
                                    <small class="text-muted small text-uppercase fw-bold d-block">Sala / Gabinete</small>
                                    <div class="fs-6 text-dark fw-semibold"><?= htmlspecialchars($equipamento->sala ?? '—') ?></div>
                                </div>
                            </div>
                            <?php else: ?>
                            <p class="text-muted">Sem localização atribuída a este equipamento.</p>
                            <?php endif; ?>
                        </div>

                        <div class="tab-pane" id="fornecedor" role="tabpanel">
                            <?php if (count($fornecedores) === 0): ?>
                                <p class="text-muted">Nenhum fornecedor associado a este equipamento.</p>
                            <?php else: ?>
                                <ul class="list-group">
                                <?php foreach ($fornecedores as $f): ?>
                                <?php $corTipo = match($f->tipoFornecedor) {
                                    'fabricante'          => 'primary',
                                    'distribuidor'        => 'info',
                                    'assistencia tecnica' => 'warning',
                                    'consumiveis'         => 'secondary',
                                    default               => 'secondary'
                                }; ?>
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <div class="fw-bold"><?= htmlspecialchars($f->nome) ?></div>
                                            <?php if ($f->pessoaContacto): ?>
                                            <small class="text-muted"><i class="fa-solid fa-user me-1"></i><?= htmlspecialchars($f->pessoaContacto) ?></small>
                                            <?php endif; ?>
                                            <?php if ($f->telefone): ?>
                                            <small class="text-muted ms-3"><i class="fa-solid fa-phone me-1"></i><?= htmlspecialchars($f->telefone) ?></small>
                                            <?php endif; ?>
                                            <?php if ($f->email): ?>
                                            <small class="text-muted ms-3"><i class="fa-solid fa-envelope me-1"></i><?= htmlspecialchars($f->email) ?></small>
                                            <?php endif; ?>
                                        </div>
                                        <span class="badge bg-<?= $corTipo ?>"><?= htmlspecialchars($f->tipoFornecedor) ?></span>
                                    </div>
                                </li>
                                <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>

                        <div class="tab-pane" id="documentacao" role="tabpanel">
                            <?php if (count($documentos) === 0): ?>
                                <p class="text-muted">Nenhum documento associado a este equipamento.</p>
                            <?php else: ?>
                                <ul class="list-group">
                                <?php foreach ($documentos as $doc): ?>
                                <?php $expirado = $doc->dataValidade && $doc->dataValidade < date('Y-m-d'); ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold"><?= htmlspecialchars($doc->nome) ?></div>
                                        <small class="text-muted"><?= htmlspecialchars($doc->tipo) ?></small>
                                        <small class="text-muted ms-3"><i class="fa-regular fa-calendar me-1"></i><?= date('d/m/Y', strtotime($doc->dataDocumento)) ?></small>
                                        <?php if ($doc->ficheiro): ?>
                                        <small class="text-muted ms-3"><i class="fa-regular fa-file-pdf text-danger me-1"></i><?= htmlspecialchars($doc->ficheiro) ?></small>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($doc->dataValidade): ?>
                                    <span class="badge bg-<?= $expirado ? 'danger' : 'success' ?>">
                                        <?= $expirado ? 'Expirado' : 'Válido até ' . date('d/m/Y', strtotime($doc->dataValidade)) ?>
                                    </span>
                                    <?php endif; ?>
                                </li>
                                <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
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
