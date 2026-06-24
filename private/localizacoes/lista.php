<?php require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged(); ?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/nav.php'; ?>

<?php
$ligacao = ligar_bd();
$erro = '';
$ativas = [];
$arquivadas = [];

if (!$ligacao) {
    $erro = "Erro na ligação à base de dados.";
} else {
    try {
        $todos = $ligacao->query(
            "SELECT l.*, COUNT(e.codigo) AS totalEquipamentos
             FROM Localizacao l
             LEFT JOIN Equipamento e ON e.codigoLocalizacao = l.codigo
             GROUP BY l.codigo
             ORDER BY l.edificio, l.piso, l.servico"
        )->fetchAll(PDO::FETCH_OBJ);

        foreach ($todos as $l) {
            if ($l->ativo) $ativas[] = $l;
            else           $arquivadas[] = $l;
        }
    } catch (PDOException $err) {
        $erro = "Erro ao carregar localizações.";
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
                    <h1 class="h2 fw-bold" style="color: #1e1b4b;">Localizações do Hospital</h1>
                    <p class="text-muted">Registo dos locais físicos onde os equipamentos médicos podem ser alocados.</p>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <?php if (($_SESSION['perfil'] ?? '') !== 'profissional de saude'): ?>
                    <a href="novo.php" class="btn text-white fw-bold shadow-sm d-inline-flex align-items-center" style="background-color: #1e1b4b;">
                        <i class="fa-solid fa-plus"></i> &ensp;Nova Localização
                    </a>
                    <?php endif; ?>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fa-solid fa-download"></i> Exportar
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="exportar-csv.php"><i class="fa-solid fa-file-csv text-success me-2"></i>CSV</a></li>
                            <li><a class="dropdown-item" href="exportar-json.php"><i class="fa-solid fa-file-code text-primary me-2"></i>JSON</a></li>
                            <li><a class="dropdown-item" href="exportar-pdf.php" target="_blank"><i class="fa-solid fa-file-pdf text-danger me-2"></i>PDF</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <?php if (!empty($erro)): ?>
                <div class="alert alert-danger mb-3"><i class="fa-solid fa-circle-exclamation me-2"></i><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>

            <div class="p-3 bg-white border rounded shadow-sm mb-4">
                <div class="table-responsive">
                    <table id="tabela-localizacoes" class="table table-bordered table-striped table-hover align-middle mb-0">
                        <thead class="table-dark small">
                            <tr>
                                <th>Edifício</th>
                                <th>Piso</th>
                                <th>Serviço / Departamento</th>
                                <th>Sala / Gabinete</th>
                                <th class="text-center">Equipamentos</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ativas as $l): ?>
                            <tr>
                                <td class="fw-semibold"><?= htmlspecialchars($l->edificio) ?></td>
                                <td><?= $l->piso ? '<span class="badge bg-secondary">' . htmlspecialchars($l->piso) . '</span>' : '—' ?></td>
                                <td><?= htmlspecialchars($l->servico) ?></td>
                                <td><?= $l->sala ? '<code class="text-primary small fw-bold">' . htmlspecialchars($l->sala) . '</code>' : '—' ?></td>
                                <td class="text-center">
                                    <span class="badge bg-<?= $l->totalEquipamentos > 0 ? 'primary' : 'light text-muted border' ?>">
                                        <?= $l->totalEquipamentos ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group shadow-sm">
                                        <a href="detalhes.php?id=<?= aes_encrypt($l->codigo) ?>" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" data-bs-placement="top" title="Ver detalhes"><i class="fa-solid fa-eye text-primary"></i></a>
                                        <?php if (($_SESSION['perfil'] ?? '') !== 'profissional de saude'): ?>
                                        <a href="editar.php?id=<?= aes_encrypt($l->codigo) ?>" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" data-bs-placement="top" title="Editar"><i class="fa-solid fa-pen-to-square text-secondary"></i></a>
                                        <?php endif; ?>
                                        <?php if (($_SESSION['perfil'] ?? '') === 'admin'): ?>
                                        <a href="apagar.php?id=<?= aes_encrypt($l->codigo) ?>" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" data-bs-placement="top" title="Arquivar"><i class="fa-solid fa-box-archive text-danger"></i></a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php if (!empty($arquivadas)): ?>
            <h5 class="fw-bold text-muted mt-2 mb-3"><i class="fa-solid fa-box-archive me-2"></i>Localizações Arquivadas</h5>
            <div class="p-3 border rounded mb-4" style="background-color: #f8f9fa; opacity: 0.8;">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0 small">
                        <thead class="table-secondary">
                            <tr>
                                <th>Edifício</th>
                                <th>Piso</th>
                                <th>Serviço / Departamento</th>
                                <th>Sala / Gabinete</th>
                                <th class="text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($arquivadas as $l): ?>
                            <tr class="text-muted">
                                <td class="fw-semibold text-decoration-line-through"><?= htmlspecialchars($l->edificio) ?></td>
                                <td><?= $l->piso ? htmlspecialchars($l->piso) : '—' ?></td>
                                <td><?= htmlspecialchars($l->servico) ?></td>
                                <td><?= $l->sala ? htmlspecialchars($l->sala) : '—' ?></td>
                                <td class="text-center"><span class="badge bg-secondary">Arquivada</span></td>
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
    $('#tabela-localizacoes').DataTable({
        // dom: 'lrtip' — layout da tabela sem a caixa de pesquisa nativa do DataTables
        dom: 'lrtip',
        pageLength: 10,
        pagingType: "full_numbers",
        language: {
            emptyTable: "Não existem localizações registadas.",
            info: "Mostrando _START_ até _END_ de _TOTAL_ registos",
            infoEmpty: "Mostrando 0 até 0 de 0 registos",
            infoFiltered: "(Filtrando _MAX_ total de registos)",
            lengthMenu: "Mostrando _MENU_ registos por página.",
            zeroRecords: "Nenhum registo encontrado.",
            paginate: { first: "Primeira", last: "Última", next: "Seguinte", previous: "Anterior" }
        }
    });

    // inicializa tooltips Bootstrap em todos os elementos com data-bs-toggle="tooltip"
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(el) {
        new bootstrap.Tooltip(el);
    });

    // toast de feedback após ação; delay: 4000ms antes do auto-hide
    var toastEl = document.getElementById('toastMensagem');
    if (toastEl) new bootstrap.Toast(toastEl, { delay: 4000 }).show();
});
</script>
<?php include '../includes/footer.php'; ?>
