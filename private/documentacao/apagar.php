<?php require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_admin();

$id_cifrado = $_GET['id'] ?? '';
$id = aes_decrypt($id_cifrado);
if ($id === false || !ctype_digit((string) $id)) {
    header('Location: lista.php');
    exit;
}

$ligacao = ligar_bd();
if (!$ligacao) { header('Location: lista.php'); exit; }

$doc = null;
try {
    $stmt = $ligacao->prepare(
        "SELECT d.*, e.codigoInterno, e.designacao FROM Documentacao d
         JOIN Equipamento e ON d.codigoEquipamento = e.codigo
         WHERE d.codigo = :id"
    );
    $stmt->execute([':id' => $id]);
    $doc = $stmt->fetch(PDO::FETCH_OBJ);
    if (!$doc) { header('Location: lista.php'); exit; }
} catch (PDOException $err) {
    header('Location: lista.php');
    exit;
}
$ligacao = null;

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
        <main class="col-md-9 col-lg-10 px-md-4 pt-5">
            <div class="card shadow-sm border border-warning text-center mx-auto p-5 my-5" style="max-width: 580px;">
                <h1 class="display-4 text-warning mb-3"><i class="fa-solid fa-triangle-exclamation"></i></h1>
                <p class="fs-5 text-muted">Deseja arquivar este registo de documentação?</p>
                <h3 class="fw-bold mb-2" style="color: #1e1b4b;"><?= htmlspecialchars($doc->nome) ?></h3>
                <div class="bg-light rounded p-3 mb-4 border text-start small">
                    <div class="mb-2">
                        <strong>Tipo:</strong> <?= htmlspecialchars($tiposLabel[$doc->tipo] ?? $doc->tipo) ?>
                    </div>
                    <div class="mb-2">
                        <strong>Equipamento:</strong>
                        <span class="badge bg-dark font-monospace me-1"><?= htmlspecialchars($doc->codigoInterno) ?></span>
                        <?= htmlspecialchars($doc->designacao) ?>
                    </div>
                    <?php if ($doc->ficheiro): ?>
                    <div><strong>Ficheiro:</strong> <code><?= htmlspecialchars($doc->ficheiro) ?></code></div>
                    <?php endif; ?>
                </div>
                <form method="post" action="confirmar_apagar.php">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($id_cifrado) ?>">
                    <div class="d-flex justify-content-center gap-3">
                        <a href="lista.php" class="btn btn-outline-secondary px-4 fw-bold">
                            <i class="fa-solid fa-xmark me-2"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-danger px-4 fw-bold shadow-sm">
                            <i class="fa-solid fa-box-archive me-2"></i> Sim, Arquivar
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
