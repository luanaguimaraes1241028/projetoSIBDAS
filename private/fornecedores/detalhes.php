<?php require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();

$id_cifrado = $_GET['id'] ?? '';
$id = aes_decrypt($id_cifrado);
if ($id === false || !ctype_digit((string) $id)) {
    header('Location: lista.php');
    exit;
}

$ligacao = ligar_bd();
$f = null;
$equipamentos = [];
$erro = '';

if (!$ligacao) {
    $erro = "Erro na ligação à base de dados.";
} else {
    try {
        $stmt = $ligacao->prepare("SELECT * FROM Fornecedor WHERE codigo = :id AND ativo = 1");
        $stmt->execute([':id' => $id]);
        $f = $stmt->fetch(PDO::FETCH_OBJ);
        if (!$f) { header('Location: lista.php'); exit; }

        $stmt2 = $ligacao->prepare(
            "SELECT e.codigoInterno, e.designacao, e.marca, e.estado
             FROM EquipamentoFornecedor ef
             JOIN Equipamento e ON ef.codigoEquipamento = e.codigo
             WHERE ef.codigoFornecedor = :id
             ORDER BY e.codigoInterno"
        );
        $stmt2->execute([':id' => $id]);
        $equipamentos = $stmt2->fetchAll(PDO::FETCH_OBJ);
    } catch (PDOException $err) {
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
            <?php elseif ($f): ?>
            <?php $corTipo = match($f->tipoFornecedor) {
                'fabricante'          => 'primary',
                'distribuidor'        => 'info',
                'assistencia tecnica' => 'warning',
                'consumiveis'         => 'secondary',
                default               => 'secondary'
            }; ?>
            <div class="card shadow-sm border p-4 mx-auto my-4" style="max-width: 850px;">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                    <h2 class="fw-bold mb-0" style="color: #1e1b4b;">
                        <i class="fa-solid fa-building me-2"></i><?= htmlspecialchars($f->nome) ?>
                    </h2>
                    <span class="badge bg-<?= $corTipo ?> fs-6 px-3 py-2"><?= htmlspecialchars($f->tipoFornecedor) ?></span>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-4 border-bottom pb-2">
                        <label class="text-muted small d-block">NIF</label>
                        <span class="fw-bold"><?= $f->nif ? htmlspecialchars($f->nif) : '—' ?></span>
                    </div>
                    <div class="col-md-4 border-bottom pb-2">
                        <label class="text-muted small d-block">Telefone</label>
                        <span><?= $f->telefone ? htmlspecialchars($f->telefone) : '—' ?></span>
                    </div>
                    <div class="col-md-4 border-bottom pb-2">
                        <label class="text-muted small d-block">Email</label>
                        <span><?= $f->email ? htmlspecialchars($f->email) : '—' ?></span>
                    </div>
                    <div class="col-md-6 border-bottom pb-2">
                        <label class="text-muted small d-block">Morada</label>
                        <span><?= $f->morada ? htmlspecialchars($f->morada) : '—' ?></span>
                    </div>
                    <div class="col-md-6 border-bottom pb-2">
                        <label class="text-muted small d-block">Website</label>
                        <?php if ($f->website): ?>
                            <a href="<?= htmlspecialchars($f->website) ?>" target="_blank" rel="noopener" class="text-primary"><?= htmlspecialchars($f->website) ?></a>
                        <?php else: ?>
                            <span>—</span>
                        <?php endif; ?>
                    </div>
                    <?php if ($f->observacoes): ?>
                    <div class="col-12 border-bottom pb-2">
                        <label class="text-muted small d-block">Observações</label>
                        <span><?= htmlspecialchars($f->observacoes) ?></span>
                    </div>
                    <?php endif; ?>
                </div>

                <?php if ($f->pessoaContacto || $f->telefonePessoa): ?>
                <h6 class="text-muted fw-bold text-uppercase small mb-3">Pessoa de Contacto</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6 border-bottom pb-2">
                        <label class="text-muted small d-block">Nome</label>
                        <span class="fw-bold"><?= $f->pessoaContacto ? htmlspecialchars($f->pessoaContacto) : '—' ?></span>
                    </div>
                    <div class="col-md-6 border-bottom pb-2">
                        <label class="text-muted small d-block">Telefone Direto</label>
                        <span><?= $f->telefonePessoa ? htmlspecialchars($f->telefonePessoa) : '—' ?></span>
                    </div>
                </div>
                <?php endif; ?>

                <h6 class="text-muted fw-bold text-uppercase small mb-3">Equipamentos Associados (<?= count($equipamentos) ?>)</h6>
                <?php if (count($equipamentos) === 0): ?>
                    <p class="text-muted fst-italic mb-4">Nenhum equipamento associado a este fornecedor.</p>
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
