<?php require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();

$id_cifrado = $_GET['id'] ?? '';
$id = aes_decrypt($id_cifrado);
if ($id === false || !ctype_digit((string) $id)) {
    header('Location: lista.php');
    exit;
}

$ligacao = ligar_bd();
$doc = null;
$erro = '';

if (!$ligacao) {
    $erro = "Erro na ligação à base de dados.";
} else {
    try {
        $stmt = $ligacao->prepare(
            "SELECT d.*, e.codigoInterno, e.designacao, e.marca, f.nome AS nomeFornecedor
             FROM Documentacao d
             JOIN Equipamento e ON d.codigoEquipamento = e.codigo
             LEFT JOIN Fornecedor f ON d.codigoFornecedor = f.codigo
             WHERE d.codigo = :id"
        );
        $stmt->execute([':id' => $id]);
        $doc = $stmt->fetch(PDO::FETCH_OBJ);
        if (!$doc) { header('Location: lista.php'); exit; }
    } catch (PDOException $err) {
        $erro = "Erro ao carregar dados.";
    }
    $ligacao = null;
}

$tiposLabel = [
    'manual de utilizador'        => 'Manual de Utilizador',
    'manual de servico'           => 'Manual de Serviço',
    'certificado de calibracao'   => 'Certificado de Calibração',
    'contrato de manutencao'      => 'Contrato de Manutenção',
    'fatura ou guia de aquisicao' => 'Fatura ou Guia de Aquisição',
    'declaracao de conformidade'  => 'Declaração de Conformidade',
    'relatorio tecnico'           => 'Relatório Técnico',
];
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/nav.php'; ?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/sidebar.php'; ?>
        <main class="col-md-9 col-lg-10 px-md-4 pt-4">
            <?php if ($erro): ?>
                <div class="alert alert-danger m-4"><?= htmlspecialchars($erro) ?></div>
            <?php elseif ($doc): ?>
            <?php
                $expirado  = $doc->dataValidade && $doc->dataValidade < date('Y-m-d');
                $labelTipo = $tiposLabel[$doc->tipo] ?? $doc->tipo;
            ?>
            <div class="card shadow-sm border p-4 mx-auto my-4" style="max-width: 800px;">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                    <h2 class="fw-bold mb-0" style="color: #1e1b4b;">
                        <i class="fa-solid fa-file-lines me-2"></i><?= htmlspecialchars($doc->nome) ?>
                    </h2>
                    <span class="badge bg-primary px-3 py-2"><?= htmlspecialchars($labelTipo) ?></span>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-6 border-bottom pb-2">
                        <label class="text-muted small d-block">Data do Documento</label>
                        <span class="fw-bold"><?= date('d/m/Y', strtotime($doc->dataDocumento)) ?></span>
                    </div>
                    <div class="col-md-6 border-bottom pb-2">
                        <label class="text-muted small d-block">Data de Validade</label>
                        <?php if ($doc->dataValidade): ?>
                            <span class="fw-bold <?= $expirado ? 'text-danger' : '' ?>">
                                <?= date('d/m/Y', strtotime($doc->dataValidade)) ?>
                                <?php if ($expirado): ?><span class="badge bg-danger ms-2">Expirado</span><?php endif; ?>
                            </span>
                        <?php else: ?>
                            <span class="text-muted">Não aplicável</span>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6 border-bottom pb-2">
                        <label class="text-muted small d-block">Equipamento Associado</label>
                        <span class="badge bg-dark font-monospace me-1"><?= htmlspecialchars($doc->codigoInterno) ?></span>
                        <span class="fw-bold"><?= htmlspecialchars($doc->designacao) ?></span>
                        <small class="text-muted ms-1"><?= htmlspecialchars($doc->marca) ?></small>
                    </div>
                    <div class="col-md-6 border-bottom pb-2">
                        <label class="text-muted small d-block">Fornecedor Associado</label>
                        <span><?= $doc->nomeFornecedor ? htmlspecialchars($doc->nomeFornecedor) : '—' ?></span>
                    </div>
                    <?php if ($doc->ficheiro): ?>
                    <div class="col-12 border-bottom pb-2">
                        <label class="text-muted small d-block">Ficheiro</label>
                        <div class="p-2 bg-light rounded border text-primary small">
                            <i class="fa-regular fa-file-pdf text-danger me-1"></i>
                            <code><?= htmlspecialchars($doc->ficheiro) ?></code>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="d-flex gap-2">
                    <a href="lista.php" class="btn btn-secondary px-4">
                        <i class="fa-solid fa-arrow-left"></i> Voltar
                    </a>
                    <?php if (($_SESSION['perfil'] ?? '') !== 'profissional de saude'): ?>
                    <a href="editar.php?id=<?= urlencode($id_cifrado) ?>" class="btn text-white px-4" style="background-color: #1e1b4b;">
                        <i class="fa-solid fa-pen-to-square"></i> Editar
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </main>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
