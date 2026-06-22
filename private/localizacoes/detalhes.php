<?php require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();

$id_cifrado = $_GET['id'] ?? '';
$id = aes_decrypt($id_cifrado);
if ($id === false || !ctype_digit((string) $id)) {
    header('Location: lista.php');
    exit;
}

$ligacao = ligar_bd();
$l = null;
$equipamentos = [];
$erro = '';

if (!$ligacao) {
    $erro = "Erro na ligação à base de dados.";
} else {
    try {
        $stmt = $ligacao->prepare("SELECT * FROM Localizacao WHERE codigo = :id AND ativo = 1");
        $stmt->execute([':id' => $id]);
        $l = $stmt->fetch(PDO::FETCH_OBJ);
        if (!$l) { header('Location: lista.php'); exit; }

        $stmt2 = $ligacao->prepare(
            "SELECT codigo, codigoInterno, designacao, marca, estado
             FROM Equipamento WHERE codigoLocalizacao = :id ORDER BY codigoInterno"
        );
        $stmt2->execute([':id' => $id]);
        $equipamentos = $stmt2->fetchAll(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
        $erro = "Erro ao carregar dados.";
    }
    $ligacao = null;
}
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/nav.php'; ?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/sidebar.php'; ?>
        <main class="col-md-9 col-lg-10 px-md-4 pt-4">
            <?php if ($erro): ?>
                <div class="alert alert-danger m-4"><?= htmlspecialchars($erro) ?></div>
            <?php elseif ($l): ?>
            <div class="card shadow-sm border p-4 mx-auto my-4" style="max-width: 800px;">
                <h2 class="fw-bold mb-3" style="color: #1e1b4b;">
                    <i class="fa-solid fa-circle-info"></i> Detalhes da Localização
                </h2>
                <hr>

                <div class="row g-4 mb-4">
                    <div class="col-md-6 border-bottom pb-2">
                        <label class="text-muted small d-block">Edifício</label>
                        <div class="fs-6 text-dark fw-bold"><?= htmlspecialchars($l->edificio) ?></div>
                    </div>
                    <div class="col-md-6 border-bottom pb-2">
                        <label class="text-muted small d-block">Piso</label>
                        <div class="fs-6 text-dark fw-bold"><?= $l->piso ? htmlspecialchars($l->piso) : '—' ?></div>
                    </div>
                    <div class="col-md-6 border-bottom pb-2">
                        <label class="text-muted small d-block">Serviço / Departamento</label>
                        <div class="fs-6 text-dark fw-bold"><?= htmlspecialchars($l->servico) ?></div>
                    </div>
                    <div class="col-md-6 border-bottom pb-2">
                        <label class="text-muted small d-block">Sala / Gabinete</label>
                        <div class="fs-6 text-dark fw-bold"><?= $l->sala ? htmlspecialchars($l->sala) : '—' ?></div>
                    </div>
                </div>

                <h6 class="text-muted fw-bold text-uppercase small mb-3">Equipamentos nesta Localização (<?= count($equipamentos) ?>)</h6>
                <?php if (empty($equipamentos)): ?>
                    <p class="text-muted fst-italic mb-4">Nenhum equipamento alocado neste local.</p>
                <?php else: ?>
                <ul class="list-group mb-4">
                    <?php foreach ($equipamentos as $eq): ?>
                    <?php $corEstado = match($eq->estado) {
                        'ativo'         => 'success',
                        'em manutencao' => 'warning',
                        'inativo'       => 'danger',
                        'em calibracao' => 'info',
                        'em quarentena' => 'secondary',
                        'abatido'       => 'dark',
                        default         => 'secondary'
                    }; ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge bg-dark font-monospace me-2"><?= htmlspecialchars($eq->codigoInterno) ?></span>
                            <span class="fw-semibold"><?= htmlspecialchars($eq->designacao) ?></span>
                            <small class="text-muted ms-2"><?= htmlspecialchars($eq->marca) ?></small>
                        </div>
                        <span class="badge bg-<?= $corEstado ?>"><?= htmlspecialchars($eq->estado) ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>

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
