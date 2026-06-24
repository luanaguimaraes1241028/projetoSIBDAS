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
$erros = [];

try {
    $stmt = $ligacao->prepare("SELECT * FROM Fornecedor WHERE codigo = :id AND ativo = 1");
    $stmt->execute([':id' => $id]);
    $f = $stmt->fetch(PDO::FETCH_OBJ);
    if (!$f) { header('Location: lista.php'); exit; }
} catch (PDOException $err2) {
    header('Location: lista.php');
    exit;
}

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
    if ($telefonePessoa !== null && !preg_match('/^[0-9]{9}$/', $telefonePessoa))
        $erros[] = 'O telefone direto do contacto deve ter exatamente 9 dígitos.';

    if (empty($erros)) {
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

            $ligacao = null;
            registar_log('fornecedor_editado', 'Fornecedor editado: #' . $id);
            $_SESSION['toast'] = ['tipo' => 'success', 'mensagem' => 'Fornecedor atualizado com sucesso.'];
            header('Location: lista.php');
            exit;
        } catch (PDOException $err2) {
            $erros[] = 'Erro ao atualizar o fornecedor. Por favor tente novamente.';
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
            <div class="card shadow-sm border p-4 mx-auto my-4" style="max-width: 850px;">
                <h2 class="fw-bold mb-3" style="color: #1e1b4b;">
                    <i class="fa-solid fa-pen-to-square"></i> Editar Fornecedor
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
                        <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($_POST['nome'] ?? $f->nome) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">NIF <span class="text-danger">*</span></label>
                        <input type="text" name="nif" class="form-control" value="<?= htmlspecialchars($_POST['nif'] ?? $f->nif ?? '') ?>" required>
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
