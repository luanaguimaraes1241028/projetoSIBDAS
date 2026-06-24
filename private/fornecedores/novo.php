<?php require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_readonly();

$tiposValidos = ['fabricante', 'distribuidor', 'assistencia tecnica', 'consumiveis'];

$erros = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome           = trim($_POST['nome'] ?? '');
    $nif            = trim($_POST['nif'] ?? '');
    $telefone       = trim($_POST['telefone'] ?? '') ?: null;
    $email          = trim($_POST['email'] ?? '') ?: null;
    $morada         = trim($_POST['morada'] ?? '') ?: null;
    $website        = trim($_POST['website'] ?? '') ?: null;
    $pessoaContacto = trim($_POST['pessoaContacto'] ?? '') ?: null;
    $telefonePessoa = trim($_POST['telefonePessoa'] ?? '') ?: null;
    $tipoFornecedor = $_POST['tipoFornecedor'] ?? '';
    $observacoes    = trim($_POST['observacoes'] ?? '') ?: null;

    if (strlen($nome) < 2)
        $erros[] = 'O nome da empresa é obrigatório e deve ter pelo menos 2 caracteres.';
    if (!in_array($tipoFornecedor, $tiposValidos))
        $erros[] = 'Selecione um tipo de fornecedor válido.';
    if (empty($nif))
        $erros[] = 'O NIF é obrigatório.';
    elseif (!preg_match('/^[0-9]{9}$/', $nif))
        $erros[] = 'O NIF deve ter exatamente 9 dígitos numéricos.';
    if ($email !== null && !filter_var($email, FILTER_VALIDATE_EMAIL))
        $erros[] = 'O email introduzido não é válido.';
    if ($telefone !== null && !preg_match('/^[0-9]{9}$/', $telefone))
        $erros[] = 'O contacto telefónico deve ter exatamente 9 dígitos.';

    if (empty($erros)) {
        $ligacao = ligar_bd();
        if (!$ligacao) {
            $erros[] = "Erro na ligação à base de dados.";
        } else try {
            $stmt = $ligacao->prepare(
                "INSERT INTO Fornecedor (nome, nif, telefone, email, morada, website, pessoaContacto, telefonePessoa, tipoFornecedor, observacoes)
                 VALUES (:nome, :nif, :telefone, :email, :morada, :website, :pessoaContacto, :telefonePessoa, :tipoFornecedor, :observacoes)"
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
            ]);
            $ligacao = null;
            registar_log('fornecedor_criado', 'Fornecedor criado: ' . $nome);
            $_SESSION['toast'] = ['tipo' => 'success', 'mensagem' => 'Fornecedor registado com sucesso.'];
            header('Location: lista.php');
            exit;
        } catch (PDOException $err2) {
            $ligacao = null;
            $erros[] = "Erro ao guardar o fornecedor. Por favor tente novamente.";
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
            <div class="card shadow-sm border p-4 mx-auto my-4" style="max-width: 850px;">
                <h2 class="fw-bold mb-3" style="color: #1e1b4b;">
                    <i class="fa-solid fa-square-plus"></i> Novo Fornecedor / Entidade
                </h2>
                <hr>

                <?php if (!empty($erros)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($erros as $erro): ?>
                            <li><?= htmlspecialchars($erro) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="post" class="row g-3">
                    <h5 class="fw-bold text-primary mt-3"><i class="fa-solid fa-building"></i> Dados da Empresa</h5>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Nome da Empresa <span class="text-danger">*</span></label>
                        <input type="text" name="nome" class="form-control" placeholder="Ex: Dräger Portugal Lda." value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">NIF <span class="text-danger">*</span></label>
                        <input type="text" name="nif" class="form-control" placeholder="Ex: 500123456" value="<?= htmlspecialchars($_POST['nif'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Tipo de Fornecedor <span class="text-danger">*</span></label>
                        <select name="tipoFornecedor" class="form-select" required>
                            <option value="" disabled <?= empty($_POST['tipoFornecedor']) ? 'selected' : '' ?>>Escolha uma opção...</option>
                            <option value="fabricante"          <?= ($_POST['tipoFornecedor'] ?? '') === 'fabricante'          ? 'selected' : '' ?>>Fabricante</option>
                            <option value="distribuidor"        <?= ($_POST['tipoFornecedor'] ?? '') === 'distribuidor'        ? 'selected' : '' ?>>Distribuidor</option>
                            <option value="assistencia tecnica" <?= ($_POST['tipoFornecedor'] ?? '') === 'assistencia tecnica' ? 'selected' : '' ?>>Assistência Técnica</option>
                            <option value="consumiveis"         <?= ($_POST['tipoFornecedor'] ?? '') === 'consumiveis'         ? 'selected' : '' ?>>Consumíveis</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Contacto Telefónico</label>
                        <input type="text" name="telefone" class="form-control" placeholder="Ex: 210000000" value="<?= htmlspecialchars($_POST['telefone'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="Ex: info@empresa.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Morada</label>
                        <input type="text" name="morada" class="form-control" placeholder="Ex: Rua Principal, Nº 1" value="<?= htmlspecialchars($_POST['morada'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Website</label>
                        <input type="url" name="website" class="form-control" placeholder="https://..." value="<?= htmlspecialchars($_POST['website'] ?? '') ?>">
                    </div>

                    <h5 class="fw-bold text-primary mt-4"><i class="fa-solid fa-user-tie"></i> Pessoa de Contacto</h5>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Nome do Contacto</label>
                        <input type="text" name="pessoaContacto" class="form-control" placeholder="Ex: Eng. Carlos Mendes" value="<?= htmlspecialchars($_POST['pessoaContacto'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Telefone Direto</label>
                        <input type="text" name="telefonePessoa" class="form-control" placeholder="Ex: 912345678" value="<?= htmlspecialchars($_POST['telefonePessoa'] ?? '') ?>">
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold">Observações</label>
                        <textarea name="observacoes" class="form-control" rows="2" placeholder="Notas adicionais..."><?= htmlspecialchars($_POST['observacoes'] ?? '') ?></textarea>
                    </div>

                    <div class="col-12 d-flex gap-2 mt-4">
                        <a href="lista.php" class="btn btn-secondary px-4">
                            <i class="fa-solid fa-xmark"></i> Cancelar
                        </a>
                        <button type="submit" class="btn text-white px-4" style="background-color: #1e1b4b;">
                            <i class="fa-regular fa-floppy-disk"></i> Registar Entidade
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
