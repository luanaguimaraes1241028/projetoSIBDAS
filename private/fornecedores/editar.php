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

$tiposValidos = ['fabricante', 'distribuidor', 'assistencia tecnica', 'consumiveis'];
$f = null;
$equipamentos = [];
$associados = [];
$erro = '';

try {
    $stmt = $ligacao->prepare("SELECT * FROM Fornecedor WHERE codigo = :id");
    $stmt->execute([':id' => $id]);
    $f = $stmt->fetch(PDO::FETCH_OBJ);
    if (!$f) { header('Location: lista.php'); exit; }

    $equipamentos = $ligacao->query(
        "SELECT codigo, codigoInterno, designacao, marca FROM Equipamento WHERE estado != 'abatido' ORDER BY codigoInterno"
    )->fetchAll(PDO::FETCH_OBJ);

    $stmtAssoc = $ligacao->prepare(
        "SELECT codigoEquipamento FROM EquipamentoFornecedor WHERE codigoFornecedor = :id"
    );
    $stmtAssoc->execute([':id' => $id]);
    $associados = array_column($stmtAssoc->fetchAll(PDO::FETCH_OBJ), 'codigoEquipamento');
} catch (PDOException $err2) {
    header('Location: lista.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome           = trim($_POST['nome'] ?? '');
    $nif            = trim($_POST['nif'] ?? '') ?: null;
    $telefone       = trim($_POST['telefone'] ?? '') ?: null;
    $email          = trim($_POST['email'] ?? '') ?: null;
    $morada         = trim($_POST['morada'] ?? '') ?: null;
    $website        = trim($_POST['website'] ?? '') ?: null;
    $pessoaContacto = trim($_POST['pessoaContacto'] ?? '') ?: null;
    $telefonePessoa = trim($_POST['telefonePessoa'] ?? '') ?: null;
    $tipoFornecedor = $_POST['tipoFornecedor'] ?? '';
    $observacoes    = trim($_POST['observacoes'] ?? '') ?: null;
    $eqSelecionados = $_POST['equipamentos'] ?? [];

    if (!$nome) {
        $erro = "O nome da empresa é obrigatório.";
    } elseif (!in_array($tipoFornecedor, $tiposValidos)) {
        $erro = "Tipo de fornecedor inválido.";
    } else {
        try {
            $stmt = $ligacao->prepare(
                "UPDATE Fornecedor SET nome = :nome, nif = :nif, telefone = :telefone, email = :email,
                 morada = :morada, website = :website, pessoaContacto = :pessoaContacto,
                 telefonePessoa = :telefonePessoa, tipoFornecedor = :tipoFornecedor, observacoes = :observacoes
                 WHERE codigo = :id"
            );
            $stmt->execute([
                ':nome'           => $nome,
                ':nif'            => $nif,
                ':telefone'       => $telefone,
                ':email'          => $email,
                ':morada'         => $morada,
                ':website'        => $website,
                ':pessoaContacto' => $pessoaContacto,
                ':telefonePessoa' => $telefonePessoa,
                ':tipoFornecedor' => $tipoFornecedor,
                ':observacoes'    => $observacoes,
                ':id'             => $id,
            ]);

            $ligacao->prepare("DELETE FROM EquipamentoFornecedor WHERE codigoFornecedor = :id")
                    ->execute([':id' => $id]);

            if (!empty($eqSelecionados)) {
                $stmtAssoc = $ligacao->prepare(
                    "INSERT IGNORE INTO EquipamentoFornecedor (codigoEquipamento, codigoFornecedor) VALUES (:eq, :forn)"
                );
                foreach ($eqSelecionados as $eqId) {
                    if (ctype_digit((string) $eqId)) {
                        $stmtAssoc->execute([':eq' => $eqId, ':forn' => $id]);
                    }
                }
            }

            $ligacao = null;
            $_SESSION['toast'] = ['tipo' => 'success', 'mensagem' => 'Fornecedor atualizado com sucesso.'];
            header('Location: lista.php');
            exit;
        } catch (PDOException $err2) {
            $erro = "Erro ao atualizar o fornecedor.";
        }
    }
    $associados = array_map('strval', $eqSelecionados);
}
$ligacao = null;
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/nav.php'; ?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/sidebar.php'; ?>
        <main class="col-md-9 col-lg-10 px-md-4 pt-4">
            <div class="card shadow-sm border p-4 mx-auto my-4" style="max-width: 850px;">
                <h2 class="fw-bold mb-3" style="color: #1e1b4b;">
                    <i class="fa-solid fa-pen-to-square"></i> Editar Fornecedor
                </h2>
                <hr>

                <?php if ($erro): ?>
                    <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation me-2"></i><?= htmlspecialchars($erro) ?></div>
                <?php endif; ?>

                <form method="post" class="row g-3">
                    <h5 class="fw-bold text-primary mt-3"><i class="fa-solid fa-building"></i> Dados da Empresa</h5>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Nome da Empresa <span class="text-danger">*</span></label>
                        <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($_POST['nome'] ?? $f->nome) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">NIF</label>
                        <input type="text" name="nif" class="form-control" value="<?= htmlspecialchars($_POST['nif'] ?? $f->nif ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Tipo de Fornecedor <span class="text-danger">*</span></label>
                        <select name="tipoFornecedor" class="form-select" required>
                            <?php
                            $tipoAtual = $_POST['tipoFornecedor'] ?? $f->tipoFornecedor;
                            $tipos = ['fabricante' => 'Fabricante', 'distribuidor' => 'Distribuidor', 'assistencia tecnica' => 'Assistência Técnica', 'consumiveis' => 'Consumíveis'];
                            foreach ($tipos as $val => $label): ?>
                            <option value="<?= $val ?>" <?= $tipoAtual === $val ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Contacto Telefónico</label>
                        <input type="text" name="telefone" class="form-control" value="<?= htmlspecialchars($_POST['telefone'] ?? $f->telefone ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($_POST['email'] ?? $f->email ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Morada</label>
                        <input type="text" name="morada" class="form-control" value="<?= htmlspecialchars($_POST['morada'] ?? $f->morada ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Website</label>
                        <input type="url" name="website" class="form-control" value="<?= htmlspecialchars($_POST['website'] ?? $f->website ?? '') ?>">
                    </div>

                    <h5 class="fw-bold text-primary mt-4"><i class="fa-solid fa-user-tie"></i> Pessoa de Contacto</h5>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Nome do Contacto</label>
                        <input type="text" name="pessoaContacto" class="form-control" value="<?= htmlspecialchars($_POST['pessoaContacto'] ?? $f->pessoaContacto ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Telefone Direto</label>
                        <input type="text" name="telefonePessoa" class="form-control" value="<?= htmlspecialchars($_POST['telefonePessoa'] ?? $f->telefonePessoa ?? '') ?>">
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold">Observações</label>
                        <textarea name="observacoes" class="form-control" rows="2"><?= htmlspecialchars($_POST['observacoes'] ?? $f->observacoes ?? '') ?></textarea>
                    </div>

                    <h5 class="fw-bold text-primary mt-4"><i class="fa-solid fa-stethoscope"></i> Equipamentos Associados</h5>
                    <div class="col-12">
                        <p class="text-muted small mb-2">Marca os equipamentos que este fornecedor fornece ou presta assistência. (Opcional)</p>
                        <?php if (empty($equipamentos)): ?>
                            <p class="text-muted fst-italic">Nenhum equipamento disponível.</p>
                        <?php else: ?>
                        <div class="border rounded p-3" style="max-height: 220px; overflow-y: auto;">
                            <?php foreach ($equipamentos as $eq): ?>
                            <div class="form-check mb-1">
                                <input class="form-check-input" type="checkbox" name="equipamentos[]"
                                    id="eq<?= $eq->codigo ?>"
                                    value="<?= $eq->codigo ?>"
                                    <?= in_array((string) $eq->codigo, array_map('strval', $associados)) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="eq<?= $eq->codigo ?>">
                                    <span class="badge bg-dark font-monospace me-1"><?= htmlspecialchars($eq->codigoInterno) ?></span>
                                    <?= htmlspecialchars($eq->designacao) ?>
                                    <small class="text-muted">— <?= htmlspecialchars($eq->marca) ?></small>
                                </label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
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
