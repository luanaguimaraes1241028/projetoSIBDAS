<?php require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged(); ?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/nav.php'; ?>


<?php
try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $resultados = $ligacao->query(
        "SELECT e.*, l.servico AS nomeLocalizacao, c.nome AS nomeCategoria
         FROM Equipamento e
         LEFT JOIN Localizacao l ON e.codigoLocalizacao = l.codigo
         LEFT JOIN Categoria c ON e.codigoCategoria = c.codigo"
    )->fetchAll(PDO::FETCH_OBJ);
    $categorias = $ligacao->query("SELECT nome FROM Categoria ORDER BY nome")->fetchAll(PDO::FETCH_OBJ);
    $erro = '';
} catch (PDOException $err) {
    $erro = "Aconteceu um erro na ligação.";
    $resultados = [];
    $categorias = [];
}
$ligacao = null;

$total      = count($resultados);
$ativos     = count(array_filter($resultados, fn($eq) => $eq->estado === 'ativo'));
$manutencao = count(array_filter($resultados, fn($eq) => $eq->estado === 'em manutencao'));
$inativos   = count(array_filter($resultados, fn($eq) => $eq->estado === 'inativo'));
?>


    <div class="container-fluid">
        <div class="row">
            
            <?php include '../includes/sidebar.php'; ?>

            <main class="col-md-9 col-lg-10 px-md-4 pt-4">
                
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <div>
                        <h1 class="h2 fw-bold" style="color: #1e1b4b;">Inventário de Dispositivos Médicos</h1>
                        <p class="text-muted">Inserção, listagem, consulta e atualização do inventário tecnológico hospitalar.</p>
                    </div>
                    <div>
                        <a href="novo.php" class="btn text-white fw-bold shadow-sm d-inline-flex align-items-center" style="background-color: #1e1b4b;">
                            <i class="fa-solid fa-plus"></i> &ensp;Inserir Equipamento
                        </a>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card p-3 shadow-sm h-100 border-start border-0 border-4 border-secondary">
                            <div class="card-body p-0">
                                <h6 class="card-subtitle mb-2 text-muted text-uppercase fw-bold small tracking-wider">Total Equipamentos</h6>
                                <h2 class="card-title mb-0 fw-bold"><?= $total ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card p-3 shadow-sm h-100 border-start border-0 border-4 border-success">
                            <div class="card-body p-0">
                                <h6 class="card-subtitle mb-2 text-muted text-uppercase fw-bold small tracking-wider">Equipamentos Ativos</h6>
                                <h2 class="card-title mb-0 fw-bold text-success"><?= $ativos ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card p-3 shadow-sm h-100 border-start border-0 border-4 border-warning">
                            <div class="card-body p-0">
                                <h6 class="card-subtitle mb-2 text-muted text-uppercase fw-bold small tracking-wider">Em Manutenção</h6>
                                <h2 class="card-title mb-0 fw-bold text-warning"><?= $manutencao ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card p-3 shadow-sm h-100 border-start border-0 border-4 border-danger">
                            <div class="card-body p-0">
                                <h6 class="card-subtitle mb-2 text-muted text-uppercase fw-bold small tracking-wider">Equipamentos Inativos</h6>
                                <h2 class="card-title mb-0 fw-bold text-danger"><?= $inativos ?></h2>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card p-3 border rounded shadow-sm mb-4">
                    <div class="row g-2">
                        <div class="col-12 col-md-6">
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                                <input type="text" id="pesquisa-texto" class="form-control" placeholder="Pesquisar por designação, marca ou número de série...">
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <select id="filtro-categoria" class="form-select text-muted">
                                <option value="">Filtrar por Categoria</option>
                                <?php foreach ($categorias as $cat): ?>
                                <option value="<?= $cat->nome ?>"><?= $cat->nome ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <select id="filtro-estado" class="form-select text-muted">
                                <option value="">Filtrar por Estado</option>
                                <option value="ativo">Ativo</option>
                                <option value="em manutencao">Em Manutenção</option>
                                <option value="inativo">Inativo</option>
                                <option value="em calibracao">Em Calibração</option>
                                <option value="em quarentena">Em Quarentena</option>
                                <option value="abatido">Abatido</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="p-3 bg-white border rounded shadow-sm mb-4">
                    <div class="table-responsive">
                        <table id="tabela-equipamentos" class="table table-bordered table-striped table-hover align-middle mb-0">
                            <thead class="table-dark small uppercase">
                                <tr>
                                    <th>Cód. Inventário / Designação</th>
                                    <th>Marca / Modelo</th>
                                    <th>Número de Série</th>
                                    <th>Serviço Clínico</th>
                                    <th>Estado Atual</th>
                                    <th>Criticidade</th>
                                    <th class="text-center">Ações</th>
                                    <th class="d-none">Categoria</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($erro)): ?>
                                    <tr><td colspan="7" class="text-center text-danger py-3"><?= $erro ?></td></tr>
                                <?php elseif (count($resultados) === 0): ?>
                                    <tr><td colspan="7" class="text-center text-muted py-3">Não existem equipamentos registados.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($resultados as $eq): ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-dark text-white font-monospace mb-1"><?= $eq->codigoInterno ?></span>
                                            <div class="fw-bold text-dark"><?= $eq->designacao ?></div>
                                        </td>
                                        <td>
                                            <div class="small fw-semibold"><?= $eq->marca ?></div>
                                            <div class="text-muted small"><?= $eq->modelo ?></div>
                                        </td>
                                        <td><code class="text-secondary small fw-bold"><?= $eq->numeroSerie ?></code></td>
                                        <td><?= $eq->nomeLocalizacao ?></td>
                                        <?php $corEstado = match($eq->estado) {
                                            'ativo'         => 'success',
                                            'em manutencao' => 'warning',
                                            'inativo'       => 'danger',
                                            'em calibracao' => 'info',
                                            'em quarentena' => 'secondary',
                                            'abatido'       => 'dark',
                                            default         => 'secondary'
                                        }; ?>
                                        <td><span class="badge bg-<?= $corEstado ?> px-2"><?= $eq->estado ?></span></td>
                                        <?php $corCriticidade = match($eq->criticidade) {
                                            'baixa'           => 'success',
                                            'media'           => 'warning',
                                            'alta'            => 'danger',
                                            'suporte de vida' => 'dark',
                                            default           => 'secondary'
                                        }; ?>
                                        <td><span class="badge bg-<?= $corCriticidade ?> rounded-pill"><?= $eq->criticidade ?></span></td>
                                        <td class="text-center">
                                            <div class="btn-group shadow-sm">
                                                <a href="detalhes.php" class="btn btn-sm btn-outline-secondary" title="Consultar Ficha Detalhada"><i class="fa-solid fa-eye text-primary"></i></a>
                                                <a href="editar.php" class="btn btn-sm btn-outline-secondary" title="Editar dados do equipamento"><i class="fa-solid fa-pen-to-square text-secondary"></i></a>
                                                <a href="apagar.php" class="btn btn-sm btn-outline-secondary" title="Remover ou arquivar"><i class="fa-solid fa-box-archive text-danger"></i></a>
                                            </div>
                                        </td>
                                        <td class="d-none"><?= $eq->nomeCategoria ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="col">
                        <p class="mb-5">Total: <strong> <?= count($resultados) ?> </strong></p>
                    </div>
                </div>
            </main>
        </div>
    </div>
<script>
    // tradução para português
    $(document).ready(function() {
        // datatable
        var tabela = $('#tabela-equipamentos').DataTable({
            dom: 'lrtip',
            pageLength: 5,
            pagingType: "full_numbers",
            columnDefs: [{ visible: false, targets: 7 }],
            language: {
                decimal: "",
                emptyTable: "Sem dados disponíveis na tabela.",
                info: "Mostrando _START_ até _END_ de _TOTAL_ registos",
                infoEmpty: "Mostrando 0 até 0 de 0 registos",
                infoFiltered: "(Filtrando _MAX_ total de registos)",
                infoPostFix: "",
                thousands: ",",
                lengthMenu: "Mostrando _MENU_ registos por página.",
                loadingRecords: "Carregando...",
                processing: "Processando...",
                search: "Filtrar:",
                zeroRecords: "Nenhum registro encontrado.",
                paginate: {
                    first: "Primeira",
                    last: "Última",
                    next: "Seguinte",
                    previous: "Anterior"
                },
                aria: {
                    sortAscending: ": ative para classificar a coluna em ordem crescente.",
                    sortDescending: ": ative para classificar a coluna em ordem decrescente."
                }
            }
        });

        // pesquisa de texto ligada ao input personalizado
        $('#pesquisa-texto').on('keyup', function() {
            tabela.search(this.value).draw();
        });

        // filtro por categoria (coluna escondida index 7)
        $('#filtro-categoria').on('change', function() {
            var val = this.value;
            tabela.column(7).search(val ? '^' + val + '$' : '', true, false).draw();
        });

        // filtro por estado (coluna index 4)
        $('#filtro-estado').on('change', function() {
            var val = this.value;
            tabela.column(4).search(val ? '^' + val + '$' : '', true, false).draw();
        });
    })
</script>
<?php include '../includes/footer.php'; ?>
