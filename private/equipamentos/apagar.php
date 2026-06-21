<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_admin();

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
        "SELECT codigo, codigoInterno, designacao, numeroSerie
         FROM Equipamento
         WHERE codigo = :id
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
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/nav.php'; ?>

    <div class="container-fluid">
        <div class="row">

            <?php include '../includes/sidebar.php'; ?>

            <main class="col-md-9 col-lg-10 px-md-4 pt-5">
                <div class="card shadow-sm border border-warning text-center mx-auto p-5 my-5" style="max-width: 550px; background-color: #fff;">
                    <h1 class="display-4 text-warning mb-3">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </h1>
                    <p class="fs-5 text-muted">Deseja desativar o seguinte equipamento?</p>

                    <h3 class="fw-bold mb-4" style="color: #1e1b4b;"><?= htmlspecialchars($equipamento->designacao) ?></h3>

                    <div class="bg-light rounded p-3 mb-4 border text-start small">
                        <div class="mb-2"><strong>Código de Inventário:</strong> <code class="text-dark fw-bold"><?= htmlspecialchars($equipamento->codigoInterno) ?></code></div>
                        <div><strong>Número de Série:</strong> <code class="text-secondary fw-bold"><?= htmlspecialchars($equipamento->numeroSerie) ?></code></div>
                    </div>

                    <p class="text-muted small">O equipamento ficará marcado como <strong>abatido</strong> e deixará de aparecer como ativo.</p>

                    <form action="confirmar_apagar.php" method="POST">
                        <input type="hidden" name="id_equipamento" value="<?= htmlspecialchars($id_cifrado) ?>">
                        <div class="d-flex justify-content-center gap-3">
                            <a href="lista.php" class="btn btn-outline-secondary px-4 py-2 fw-bold">
                                <i class="fa-solid fa-xmark me-2"></i> Não
                            </a>
                            <button type="submit" class="btn btn-danger px-4 py-2 fw-bold shadow-sm">
                                <i class="fa-solid fa-check me-2"></i> Sim, Desativar
                            </button>
                        </div>
                    </form>
                </div>
            </main>

        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
