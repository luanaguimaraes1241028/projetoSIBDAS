<?php require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_readonly();

$id_cifrado = $_GET['id'] ?? '';
$id = aes_decrypt($id_cifrado);
if ($id === false || !ctype_digit((string) $id)) {
    header('Location: lista.php');
    exit;
}

$ligacao = ligar_bd();
if (!$ligacao) { header('Location: lista.php'); exit; }

$l = null;
$erro = '';

try {
    $stmt = $ligacao->prepare("SELECT * FROM Localizacao WHERE codigo = :id AND ativo = 1");
    $stmt->execute([':id' => $id]);
    $l = $stmt->fetch(PDO::FETCH_OBJ);
    if (!$l) { header('Location: lista.php'); exit; }
} catch (PDOException $e) {
    header('Location: lista.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $edificio = trim($_POST['edificio'] ?? '');
    $piso     = trim($_POST['piso'] ?? '') ?: null;
    $servico  = trim($_POST['servico'] ?? '');
    $sala     = trim($_POST['sala'] ?? '') ?: null;

    if (!$edificio || !$servico) {
        $erro = "Edifício e Serviço são obrigatórios.";
    } else {
        try {
            $stmtUpd = $ligacao->prepare(
                "UPDATE Localizacao SET edificio = :edificio, piso = :piso, servico = :servico, sala = :sala WHERE codigo = :id"
            );
            $stmtUpd->execute([':edificio' => $edificio, ':piso' => $piso, ':servico' => $servico, ':sala' => $sala, ':id' => $id]);
            $ligacao = null;
            registar_log('localizacao_editada', 'Localização editada: #' . $id);
            $_SESSION['toast'] = ['tipo' => 'success', 'mensagem' => 'Localização atualizada com sucesso.'];
            header('Location: lista.php');
            exit;
        } catch (PDOException $e) {
            $erro = "Erro ao atualizar a localização.";
        }
    }
}
$ligacao = null;
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/nav.php'; ?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/sidebar.php'; ?>
        <main class="col-md-9 col-lg-10 px-md-4 pt-4">
            <div class="card shadow-sm border p-4 mx-auto my-4" style="max-width: 800px;">
                <h2 class="fw-bold mb-3" style="color: #1e1b4b;">
                    <i class="fa-solid fa-pen-to-square"></i> Editar Localização
                </h2>
                <hr>

                <?php if ($erro): ?>
                    <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation me-2"></i><?= htmlspecialchars($erro) ?></div>
                <?php endif; ?>

                <form method="post" class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Edifício <span class="text-danger">*</span></label>
                        <input type="text" name="edificio" class="form-control" value="<?= htmlspecialchars($_POST['edificio'] ?? $l->edificio) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Piso</label>
                        <input type="text" name="piso" class="form-control" value="<?= htmlspecialchars($_POST['piso'] ?? $l->piso ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Serviço / Departamento <span class="text-danger">*</span></label>
                        <input type="text" name="servico" class="form-control" value="<?= htmlspecialchars($_POST['servico'] ?? $l->servico) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Sala / Gabinete</label>
                        <input type="text" name="sala" class="form-control" value="<?= htmlspecialchars($_POST['sala'] ?? $l->sala ?? '') ?>">
                    </div>

                    <div class="col-12 d-flex gap-2 mt-4">
                        <a href="lista.php" class="btn btn-secondary px-4">
                            <i class="fa-solid fa-xmark"></i> Cancelar
                        </a>
                        <button type="submit" class="btn text-white px-4" style="background-color: #1e1b4b;">
                            <i class="fa-regular fa-floppy-disk"></i> Guardar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
