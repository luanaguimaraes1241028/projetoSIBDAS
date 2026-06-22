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

$l = null;
$emUso = false;
try {
    $stmt = $ligacao->prepare("SELECT * FROM Localizacao WHERE codigo = :id AND ativo = 1");
    $stmt->execute([':id' => $id]);
    $l = $stmt->fetch(PDO::FETCH_OBJ);
    if (!$l) { header('Location: lista.php'); exit; }
} catch (PDOException $e) {
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
                <h1 class="display-4 text-warning mb-3"><i class="fa-solid fa-triangle-exclamation"></i></h1>
                <p class="fs-5 text-muted">Deseja arquivar esta localização?</p>
                <h3 class="fw-bold mb-1" style="color: #1e1b4b;"><?= htmlspecialchars($l->servico) ?></h3>
                <p class="text-muted small"><?= htmlspecialchars($l->edificio) ?><?= $l->piso ? ' · ' . htmlspecialchars($l->piso) : '' ?><?= $l->sala ? ' · ' . htmlspecialchars($l->sala) : '' ?></p>
                <form method="post" action="confirmar_apagar.php">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($id_cifrado) ?>">
                    <div class="d-flex justify-content-center gap-3 mt-3">
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
