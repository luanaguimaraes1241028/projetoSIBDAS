<?php require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_readonly();

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $edificio = trim($_POST['edificio'] ?? '');
    $piso     = trim($_POST['piso'] ?? '') ?: null;
    $servico  = trim($_POST['servico'] ?? '');
    $sala     = trim($_POST['sala'] ?? '') ?: null;

    if (!$edificio || !$servico) {
        $erro = "Edifício e Serviço são obrigatórios.";
    } else {
        $ligacao = ligar_bd();
        if (!$ligacao) {
            $erro = "Erro na ligação à base de dados.";
        } else {
            try {
                $stmt = $ligacao->prepare(
                    "INSERT INTO Localizacao (edificio, piso, servico, sala) VALUES (:edificio, :piso, :servico, :sala)"
                );
                $stmt->execute([':edificio' => $edificio, ':piso' => $piso, ':servico' => $servico, ':sala' => $sala]);
                $ligacao = null;
                $_SESSION['toast'] = ['tipo' => 'success', 'mensagem' => 'Localização registada com sucesso.'];
                header('Location: lista.php');
                exit;
            } catch (PDOException $e) {
                $erro = "Erro ao guardar a localização.";
                $ligacao = null;
            }
        }
    }
}
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/nav.php'; ?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/sidebar.php'; ?>
        <main class="col-md-9 col-lg-10 px-md-4 pt-4">
            <div class="card shadow-sm border p-4 mx-auto my-4" style="max-width: 800px;">
                <h2 class="fw-bold mb-3" style="color: #1e1b4b;">
                    <i class="fa-solid fa-square-plus"></i> Nova Localização
                </h2>
                <hr>

                <?php if ($erro): ?>
                    <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation me-2"></i><?= htmlspecialchars($erro) ?></div>
                <?php endif; ?>

                <form method="post" class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Edifício <span class="text-danger">*</span></label>
                        <input type="text" name="edificio" class="form-control" placeholder="Ex: Bloco A" value="<?= htmlspecialchars($_POST['edificio'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Piso</label>
                        <input type="text" name="piso" class="form-control" placeholder="Ex: Piso 2" value="<?= htmlspecialchars($_POST['piso'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Serviço / Departamento <span class="text-danger">*</span></label>
                        <input type="text" name="servico" class="form-control" placeholder="Ex: UCI - Cuidados Intensivos" value="<?= htmlspecialchars($_POST['servico'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Sala / Gabinete</label>
                        <input type="text" name="sala" class="form-control" placeholder="Ex: Sala UCIP-04" value="<?= htmlspecialchars($_POST['sala'] ?? '') ?>">
                    </div>

                    <div class="col-12 d-flex gap-2 mt-4">
                        <a href="lista.php" class="btn btn-secondary px-4">
                            <i class="fa-solid fa-xmark"></i> Cancelar
                        </a>
                        <button type="submit" class="btn text-white px-4" style="background-color: #1e1b4b;">
                            <i class="fa-regular fa-floppy-disk"></i> Guardar Localização
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
