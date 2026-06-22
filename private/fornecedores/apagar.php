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

$f = null;
$emUso = false;
try {
    $stmt = $ligacao->prepare("SELECT * FROM Fornecedor WHERE codigo = :id");
    $stmt->execute([':id' => $id]);
    $f = $stmt->fetch(PDO::FETCH_OBJ);
    if (!$f) { header('Location: lista.php'); exit; }

    $stmt2 = $ligacao->prepare(
        "SELECT (SELECT COUNT(*) FROM EquipamentoFornecedor WHERE codigoFornecedor = :id) +
                (SELECT COUNT(*) FROM Garantia        WHERE codigoFornecedor = :id) +
                (SELECT COUNT(*) FROM Documentacao    WHERE codigoFornecedor = :id) AS total"
    );
    $stmt2->execute([':id' => $id]);
    $emUso = (int) $stmt2->fetchColumn() > 0;
} catch (PDOException $err) {
    header('Location: lista.php');
    exit;
}
$ligacao = null;
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/nav.php'; ?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/sidebar.php'; ?>
        <main class="col-md-9 col-lg-10 px-md-4 pt-5">
            <div class="card shadow-sm border text-center mx-auto p-5 my-5" style="max-width: 580px;">
                <?php if ($emUso): ?>
                    <h1 class="display-4 text-danger mb-3"><i class="fa-solid fa-circle-xmark"></i></h1>
                    <p class="fs-5 fw-bold text-danger">Não é possível eliminar este fornecedor</p>
                    <p class="text-muted"><strong><?= htmlspecialchars($f->nome) ?></strong> está associado a equipamentos, garantias ou documentação.<br>Remova essas associações antes de eliminar.</p>
                    <a href="lista.php" class="btn btn-secondary mt-3 px-4">
                        <i class="fa-solid fa-arrow-left"></i> Voltar à Lista
                    </a>
                <?php else: ?>
                    <h1 class="display-4 text-warning mb-3"><i class="fa-solid fa-triangle-exclamation"></i></h1>
                    <p class="fs-5 text-muted">Deseja eliminar permanentemente este fornecedor?</p>
                    <h3 class="fw-bold mb-2" style="color: #1e1b4b;"><?= htmlspecialchars($f->nome) ?></h3>
                    <p class="text-muted small"><?= htmlspecialchars($f->tipoFornecedor) ?><?= $f->nif ? ' · NIF: ' . htmlspecialchars($f->nif) : '' ?></p>
                    <form method="post" action="confirmar_apagar.php">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($id_cifrado) ?>">
                        <div class="d-flex justify-content-center gap-3 mt-3">
                            <a href="lista.php" class="btn btn-outline-secondary px-4 fw-bold">
                                <i class="fa-solid fa-xmark me-2"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-danger px-4 fw-bold shadow-sm">
                                <i class="fa-solid fa-trash me-2"></i> Sim, Eliminar
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
