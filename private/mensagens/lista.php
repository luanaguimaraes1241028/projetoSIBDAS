<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_admin();

$ligacao = ligar_bd();
if (!$ligacao) { header('Location: ../dashboard/dashboard.php'); exit; }

$mensagens = $ligacao->query(
    "SELECT codigo, nome, email, mensagem, dataEnvio, lida FROM MensagemContacto ORDER BY lida ASC, dataEnvio DESC"
)->fetchAll(PDO::FETCH_OBJ);

$totalNaoLidas = count(array_filter($mensagens, fn($m) => !$m->lida));
$ligacao = null;
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/nav.php'; ?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/sidebar.php'; ?>

        <main class="col-md-9 col-lg-10 px-md-4 pt-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <div>
                    <h1 class="h2 fw-bold" style="color: #1e1b4b;">
                        <i class="fa-solid fa-envelope"></i> Mensagens de Contacto
                    </h1>
                    <p class="text-muted mb-0">Mensagens recebidas através da área pública.</p>
                </div>
                <?php if ($totalNaoLidas > 0): ?>
                <span class="badge bg-danger fs-6"><?= $totalNaoLidas ?> não lida<?= $totalNaoLidas > 1 ? 's' : '' ?></span>
                <?php endif; ?>
            </div>

            <?php if (isset($_GET['lida'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fa-solid fa-circle-check me-2"></i> Mensagem marcada como lida.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <?php if (empty($mensagens)): ?>
            <div class="card shadow-sm border p-5 text-center text-muted">
                <i class="fa-solid fa-inbox fa-3x mb-3"></i>
                <p class="mb-0">Ainda não foram recebidas mensagens.</p>
            </div>
            <?php else: ?>
            <div class="card shadow-sm border">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th></th>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>Data</th>
                                <th>Mensagem</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($mensagens as $msg): ?>
                            <tr class="<?= !$msg->lida ? 'table-warning' : '' ?>">
                                <td>
                                    <?php if (!$msg->lida): ?>
                                    <span class="badge bg-danger">Nova</span>
                                    <?php else: ?>
                                    <span class="badge bg-secondary">Lida</span>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-bold"><?= htmlspecialchars($msg->nome) ?></td>
                                <td class="text-muted"><?= htmlspecialchars($msg->email) ?></td>
                                <td class="text-muted small"><?= date('d/m/Y H:i', strtotime($msg->dataEnvio)) ?></td>
                                <td class="text-muted small"><?= htmlspecialchars(mb_strimwidth($msg->mensagem, 0, 80, '...')) ?></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal" data-bs-target="#modalMensagem"
                                        data-id="<?= $msg->codigo ?>"
                                        data-nome="<?= htmlspecialchars($msg->nome) ?>"
                                        data-email="<?= htmlspecialchars($msg->email) ?>"
                                        data-data="<?= date('d/m/Y \à\s H:i', strtotime($msg->dataEnvio)) ?>"
                                        data-mensagem="<?= htmlspecialchars($msg->mensagem) ?>"
                                        data-lida="<?= $msg->lida ?>">
                                        <i class="fa-solid fa-eye"></i> Ver
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalMensagem" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-envelope-open me-2"></i><span id="modalNome"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-1"><i class="fa-solid fa-at me-1"></i> <span id="modalEmail"></span></p>
                <p class="text-muted mb-3"><i class="fa-regular fa-clock me-1"></i> <span id="modalData"></span></p>
                <hr>
                <p id="modalTextoMensagem" class="mt-3" style="white-space: pre-wrap;"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <form method="post" action="marcar_lida.php" id="formMarcarLida" style="display:none;">
                    <input type="hidden" name="id" id="modalId">
                    <button type="submit" class="btn btn-success">
                        <i class="fa-solid fa-check"></i> Marcar como lida
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// 'show.bs.modal': evento Bootstrap disparado antes de o modal ficar visível — ideal para preencher conteúdo dinâmico
document.getElementById('modalMensagem').addEventListener('show.bs.modal', function (e) {
    // e.relatedTarget: o elemento que acionou o modal (o botão "Ver") — contém os dados nos atributos data-*
    const btn = e.relatedTarget;
    document.getElementById('modalNome').textContent        = btn.dataset.nome;
    document.getElementById('modalEmail').textContent       = btn.dataset.email;
    document.getElementById('modalData').textContent        = btn.dataset.data;
    document.getElementById('modalTextoMensagem').textContent = btn.dataset.mensagem;
    document.getElementById('modalId').value                = btn.dataset.id;
    // dataset.lida vem como string '0' ou '1' — atributos HTML são sempre texto, nunca booleano nativo
    document.getElementById('formMarcarLida').style.display = btn.dataset.lida === '0' ? 'block' : 'none';
});
</script>

<?php include '../includes/footer.php'; ?>
