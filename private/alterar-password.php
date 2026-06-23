<?php
require_once __DIR__ . '/includes/funcoes.php';
redirect_if_not_logged();

$erros = [];
$sucesso = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $passwordAtual  = $_POST['passwordAtual']  ?? '';
    $passwordNova   = trim($_POST['passwordNova']   ?? '');
    $passwordConfirm = trim($_POST['passwordConfirm'] ?? '');

    if (empty($passwordAtual))
        $erros[] = 'A password atual é obrigatória.';
    if (empty($passwordNova))
        $erros[] = 'A nova password é obrigatória.';
    if (strlen($passwordNova) < 6 || strlen($passwordNova) > 72)
        $erros[] = 'A nova password deve ter entre 6 e 72 caracteres.';
    if ($passwordNova !== $passwordConfirm)
        $erros[] = 'A confirmação da password não coincide.';
    if ($passwordAtual === $passwordNova)
        $erros[] = 'A nova password deve ser diferente da atual.';

    if (empty($erros)) {
        $ligacao = ligar_bd();
        if (!$ligacao) {
            $erros[] = 'Erro ao ligar à base de dados.';
        } else {
            $stmt = $ligacao->prepare("SELECT password FROM Utilizador WHERE codigo = :id LIMIT 1");
            $stmt->execute([':id' => $_SESSION['codigo_utilizador']]);
            $utilizador = $stmt->fetch(PDO::FETCH_OBJ);

            if (!$utilizador || !password_verify($passwordAtual, $utilizador->password)) {
                $erros[] = 'A password atual está incorreta.';
            } else {
                $novoHash = password_hash($passwordNova, PASSWORD_DEFAULT);
                $stmt2 = $ligacao->prepare("UPDATE Utilizador SET password = :hash WHERE codigo = :id");
                $stmt2->execute([':hash' => $novoHash, ':id' => $_SESSION['codigo_utilizador']]);
                registar_log('password_alterada', 'Password alterada pelo utilizador: ' . $_SESSION['utilizador']);
                $sucesso = true;
            }
            $ligacao = null;
        }
    }
}
?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/nav.php'; ?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>

        <main class="col-md-9 col-lg-10 px-md-4 pt-4">
            <div class="d-flex align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2 fw-bold" style="color: #1e1b4b;">Alterar Password</h1>
            </div>

            <div class="card shadow-sm border p-4 mx-auto" style="max-width: 480px; background-color: #fff;">

                <?php if ($sucesso): ?>
                <div class="alert alert-success">
                    <i class="fa-solid fa-circle-check me-2"></i> Password alterada com sucesso.
                </div>
                <?php endif; ?>

                <?php if (!empty($erros)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($erros as $erro): ?>
                        <li><?= htmlspecialchars($erro) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <form method="POST" action="#" class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-bold">Password Atual</label>
                        <input type="password" name="passwordAtual" class="form-control" placeholder="••••••••" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Nova Password</label>
                        <input type="password" name="passwordNova" class="form-control" placeholder="••••••••" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Confirmar Nova Password</label>
                        <input type="password" name="passwordConfirm" class="form-control" placeholder="••••••••" required>
                    </div>
                    <div class="col-12 d-flex gap-2 mt-2">
                        <a href="dashboard/dashboard.php" class="btn btn-secondary px-4"><i class="fa-solid fa-xmark"></i> Cancelar</a>
                        <button type="submit" class="btn text-white px-4" style="background-color: #1e1b4b;"><i class="fa-solid fa-key"></i> Alterar Password</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
