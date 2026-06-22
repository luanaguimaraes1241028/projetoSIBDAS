<?php require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged(); ?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/nav.php'; ?>

<?php
$ligacao = ligar_bd();
$erro = '';
$resultados = [];

if (!$ligacao) {
    $erro = "Erro na ligação à base de dados.";
} else {
    try {
        $resultados = $ligacao->query(
            "SELECT * FROM Fornecedor ORDER BY nome"
        )->fetchAll(PDO::FETCH_OBJ);
    } catch (PDOException $err) {
        $erro = "Erro ao carregar fornecedores.";
    }
    $ligacao = null;
}
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/sidebar.php'; ?>

        <main class="col-md-9 col-lg-10 px-md-4 pt-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <div>
                    <h1 class="h2 fw-bold" style="color: #1e1b4b;">Gestão de Fornecedores</h1>
                    <p class="text-muted">Registo e associação de entidades, fabricantes e empresas de assistência técnica.</p>
                </div>
                <?php if (($_SESSION['perfil'] ?? '') !== 'profissional de saude'): ?>
                <div>
                    <a href="novo.php" class="btn text-white fw-bold shadow-sm d-inline-flex align-items-center" style="background-color: #1e1b4b;">
                        <i class="fa-solid fa-plus"></i> &ensp;Registar Fornecedor
                    </a>
                </div>
                <?php endif; ?>
            </div>

            <?php if (!empty($erro)): ?>
                <div class="alert alert-danger mb-3"><i class="fa-solid fa-circle-exclamation me-2"></i><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>

            <div class="p-3 bg-white border rounded shadow-sm mb-4">
                <div class="table-responsive">
                    <table id="tabela-fornecedores" class="table table-bordered table-striped table-hover align-middle mb-0">
                        <thead class="table-dark small">
                            <tr>
                                <th>Empresa / NIF</th>
                                <th>Tipo</th>
                                <th>Pessoa de Contacto</th>
                                <th>Telefone / Email</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($resultados as $f): ?>
                            <?php $corTipo = match($f->tipoFornecedor) {
                                'fabricante'          => 'primary',
                                'distribuidor'        => 'info',
                                'assistencia tecnica' => 'warning',
                                'consumiveis'         => 'secondary',
                                default               => 'secondary'
                            }; ?>
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($f->nome) ?></div>
                                    <?php if ($f->nif): ?><small class="text-muted">NIF: <?= htmlspecialchars($f->nif) ?></small><?php endif; ?>
                                </td>
                                <td><span class="badge bg-<?= $corTipo ?> px-2"><?= htmlspecialchars($f->tipoFornecedor) ?></span></td>
                                <td>
                                    <?php if ($f->pessoaContacto): ?>
                                        <div class="small fw-semibold"><?= htmlspecialchars($f->pessoaContacto) ?></div>
                                    <?php else: ?>
                                        <span class="text-muted small">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($f->telefone): ?><div class="small"><i class="fa-solid fa-phone text-muted me-1"></i><?= htmlspecialchars($f->telefone) ?></div><?php endif; ?>
                                    <?php if ($f->email): ?><div class="small text-muted"><i class="fa-solid fa-envelope me-1"></i><?= htmlspecialchars($f->email) ?></div><?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group shadow-sm">
                                        <a href="detalhes.php?id=<?= aes_encrypt($f->codigo) ?>" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" data-bs-placement="top" title="Ver detalhes"><i class="fa-solid fa-eye text-primary"></i></a>
                                        <?php if (($_SESSION['perfil'] ?? '') !== 'profissional de saude'): ?>
                                        <a href="editar.php?id=<?= aes_encrypt($f->codigo) ?>" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" data-bs-placement="top" title="Editar"><i class="fa-solid fa-pen-to-square text-secondary"></i></a>
                                        <?php endif; ?>
                                        <?php if (($_SESSION['perfil'] ?? '') === 'admin'): ?>
                                        <a href="apagar.php?id=<?= aes_encrypt($f->codigo) ?>" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" data-bs-placement="top" title="Eliminar"><i class="fa-solid fa-trash text-danger"></i></a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>

<?php if (!empty($_SESSION['toast'])): ?>
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100;">
    <div id="toastMensagem" class="toast align-items-center text-bg-<?= $_SESSION['toast']['tipo'] ?> border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body fw-semibold">
                <i class="fa-solid fa-circle-check me-2"></i> <?= htmlspecialchars($_SESSION['toast']['mensagem']) ?>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Fechar"></button>
        </div>
    </div>
</div>
<?php unset($_SESSION['toast']); ?>
<?php endif; ?>

<script>
$(document).ready(function() {
    $('#tabela-fornecedores').DataTable({
        dom: 'lrtip',
        pageLength: 10,
        pagingType: "full_numbers",
        language: {
            emptyTable: "Não existem fornecedores registados.",
            info: "Mostrando _START_ até _END_ de _TOTAL_ registos",
            infoEmpty: "Mostrando 0 até 0 de 0 registos",
            infoFiltered: "(Filtrando _MAX_ total de registos)",
            lengthMenu: "Mostrando _MENU_ registos por página.",
            zeroRecords: "Nenhum registo encontrado.",
            paginate: { first: "Primeira", last: "Última", next: "Seguinte", previous: "Anterior" }
        }
    });

    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(el) {
        new bootstrap.Tooltip(el);
    });

    var toastEl = document.getElementById('toastMensagem');
    if (toastEl) new bootstrap.Toast(toastEl, { delay: 4000 }).show();
});
</script>
<?php include '../includes/footer.php'; ?>
