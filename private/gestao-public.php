<?php
require_once __DIR__ . '/includes/funcoes.php';
redirect_if_not_admin();

$ligacao = ligar_bd();
if (!$ligacao) { header('Location: dashboard/dashboard.php'); exit; }

$chaves = [
    'titulo_principal', 'subtitulo', 'botao_acao', 'imagem_destaque',
    'sobre_titulo1', 'sobre_conteudo1',
    'sobre_titulo2', 'sobre_conteudo2',
    'sobre_titulo3', 'sobre_conteudo3',
    'contacto_morada', 'contacto_horarios', 'contacto_email', 'contacto_telefone',
];

$stmtUser = $ligacao->prepare("SELECT codigo FROM Utilizador WHERE email = :email");
$stmtUser->execute([':email' => $_SESSION['utilizador']]);
$codigoUser = (int) $stmtUser->fetchColumn();

$sucesso = '';
$erro    = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $ligacao->prepare(
            "INSERT INTO ConteudoPublico (chave, valor, dataAtualizacao, codigoUtilizador)
             VALUES (:chave, :valor, CURDATE(), :user)
             ON DUPLICATE KEY UPDATE valor = VALUES(valor), dataAtualizacao = VALUES(dataAtualizacao), codigoUtilizador = VALUES(codigoUtilizador)"
        );
        foreach ($chaves as $chave) {
            $stmt->execute([
                ':chave' => $chave,
                ':valor' => trim($_POST[$chave] ?? ''),
                ':user'  => $codigoUser,
            ]);
        }
        $sucesso = 'Conteúdo publicado com sucesso.';
    } catch (PDOException) {
        $erro = 'Erro ao guardar as alterações.';
    }
}

$rows = $ligacao->query("SELECT chave, valor FROM ConteudoPublico")->fetchAll(PDO::FETCH_KEY_PAIR);
$c    = array_merge(array_fill_keys($chaves, ''), $rows);
$ligacao = null;
?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/nav.php'; ?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>

        <main class="col-md-9 col-lg-10 px-md-4 pt-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <div>
                    <h1 class="h2 fw-bold" style="color: #1e1b4b;">Gestão da Área Pública</h1>
                    <p class="text-muted">Painel de Administração evitando a edição direta do HTML.</p>
                </div>
            </div>

            <?php if ($sucesso): ?>
            <div class="alert alert-success"><i class="fa-solid fa-circle-check me-2"></i><?= htmlspecialchars($sucesso) ?></div>
            <?php endif; ?>
            <?php if ($erro): ?>
            <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation me-2"></i><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>

            <div class="card shadow-sm border p-4 mb-4" style="background-color: #fff;">
                <form method="post" class="row g-4">

                    <div class="col-12">
                        <h5 class="fw-bold text-primary border-bottom pb-2"><i class="fa-solid fa-heading"></i> Ecrã Principal</h5>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Título Principal do Site</label>
                        <input type="text" name="titulo_principal" class="form-control" value="<?= htmlspecialchars($c['titulo_principal']) ?>" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Subtítulo de Introdução</label>
                        <textarea name="subtitulo" class="form-control" rows="2" required><?= htmlspecialchars($c['subtitulo']) ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Imagem de Destaque (Caminho)</label>
                        <input type="text" name="imagem_destaque" class="form-control" value="<?= htmlspecialchars($c['imagem_destaque']) ?>">
                        <div class="form-text text-muted">Caminho para a imagem, ex: /sibdas/1241028/medcore/assets/img/foto.jpg</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Texto do Botão de Ação</label>
                        <input type="text" name="botao_acao" class="form-control" value="<?= htmlspecialchars($c['botao_acao']) ?>" required>
                    </div>

                    <div class="col-12 mt-3">
                        <h5 class="fw-bold text-primary border-bottom pb-2"><i class="fa-solid fa-file-lines"></i> Sobre Nós</h5>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Título 1</label>
                        <input type="text" name="sobre_titulo1" class="form-control" value="<?= htmlspecialchars($c['sobre_titulo1']) ?>" required>
                        <label class="form-label fw-bold mt-2 text-muted small">Conteúdo</label>
                        <textarea name="sobre_conteudo1" class="form-control" rows="4" required><?= htmlspecialchars($c['sobre_conteudo1']) ?></textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Título 2</label>
                        <input type="text" name="sobre_titulo2" class="form-control" value="<?= htmlspecialchars($c['sobre_titulo2']) ?>" required>
                        <label class="form-label fw-bold mt-2 text-muted small">Conteúdo</label>
                        <textarea name="sobre_conteudo2" class="form-control" rows="4" required><?= htmlspecialchars($c['sobre_conteudo2']) ?></textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Título 3</label>
                        <input type="text" name="sobre_titulo3" class="form-control" value="<?= htmlspecialchars($c['sobre_titulo3']) ?>" required>
                        <label class="form-label fw-bold mt-2 text-muted small">Conteúdo</label>
                        <textarea name="sobre_conteudo3" class="form-control" rows="4" required><?= htmlspecialchars($c['sobre_conteudo3']) ?></textarea>
                    </div>

                    <div class="col-12 mt-3">
                        <h5 class="fw-bold text-primary border-bottom pb-2"><i class="fa-solid fa-address-book"></i> Informações de Contacto e Suporte</h5>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Endereço Hospitalar / Sede</label>
                        <textarea name="contacto_morada" class="form-control" rows="3" required><?= htmlspecialchars($c['contacto_morada']) ?></textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Horários do Suporte Técnico</label>
                        <textarea name="contacto_horarios" class="form-control" rows="3" required><?= htmlspecialchars($c['contacto_horarios']) ?></textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Canais Diretos de Contacto</label>
                        <label class="text-muted small d-block">Email:</label>
                        <input type="email" name="contacto_email" class="form-control form-control-sm mb-2" value="<?= htmlspecialchars($c['contacto_email']) ?>" required>
                        <label class="text-muted small d-block">Telefone:</label>
                        <input type="text" name="contacto_telefone" class="form-control form-control-sm" value="<?= htmlspecialchars($c['contacto_telefone']) ?>" required>
                    </div>

                    <div class="col-12 d-flex gap-2 mt-4 border-top pt-3">
                        <a href="dashboard/dashboard.php" class="btn btn-secondary px-4">
                            <i class="fa-solid fa-xmark"></i> Cancelar
                        </a>
                        <button type="submit" class="btn text-white px-4" style="background-color: #1e1b4b;">
                            <i class="fa-regular fa-floppy-disk"></i> Publicar Alterações no Website
                        </button>
                    </div>

                </form>
            </div>
        </main>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
