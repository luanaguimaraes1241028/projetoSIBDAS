<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_readonly();
require_once __DIR__ . '/../includes/validacoes.php';

if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'])) {
    header('Location: ' . BASE_URL . '/public/login.php');
    exit;
}

$idEquipamentoEncrypted = $_GET['id_equipamento'] ?? null;
$idEquipamento = aes_decrypt($idEquipamentoEncrypted);

if (!$idEquipamento || !is_numeric($idEquipamento)) {
    header('Location: lista.php');
    exit;
}

$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $designacao          = trim($_POST['designacao']          ?? '');
    $codigoCategoria     = trim($_POST['codigoCategoria']     ?? '');
    $marca               = trim($_POST['marca']               ?? '');
    $modelo              = trim($_POST['modelo']              ?? '');
    $numeroSerie         = trim($_POST['numeroSerie']         ?? '');
    $fabricante          = trim($_POST['fabricante']          ?? '');
    $anoFabrico          = trim($_POST['anoFabrico']          ?? '');
    $dataAquisicao       = trim($_POST['dataAquisicao']       ?? '');
    $custoAquisicao      = trim($_POST['custoAquisicao']      ?? '');
    $tipoEntrada         = trim($_POST['tipoEntrada']         ?? '');
    $estado              = trim($_POST['estado']              ?? '');
    $criticidade         = trim($_POST['criticidade']         ?? '');
    $observacoes         = trim($_POST['observacoes']         ?? '');
    $codigoLocalizacao   = trim($_POST['codigoLocalizacao']   ?? '') ?: null;
    $garantiaInicio      = trim($_POST['garantiaInicio']      ?? '');
    $garantiaFim         = trim($_POST['garantiaFim']         ?? '');
    $contratoManutencao  = trim($_POST['contratoManutencao']  ?? '');
    $tipoContrato        = trim($_POST['tipoContrato']        ?? '');
    $entidadeResponsavel = trim($_POST['entidadeResponsavel'] ?? '');
    $periodicidade       = trim($_POST['periodicidade']       ?? '');
    $observacoesContrato = trim($_POST['observacoesContrato'] ?? '');
    $codigoGarantia      = trim($_POST['codigoGarantia']      ?? '');

    $erros = array_merge(
        validar_designacao($designacao),
        validar_categoria($codigoCategoria),
        validar_marca($marca),
        validar_modelo($modelo),
        validar_numero_serie($numeroSerie),
        validar_fabricante($fabricante),
        validar_ano($anoFabrico),
        validar_data($dataAquisicao,  'A data de aquisição é obrigatória.'),
        validar_custo($custoAquisicao),
        validar_data($garantiaInicio, 'A data de início da garantia é obrigatória.'),
        validar_data($garantiaFim,    'A data de fim da garantia é obrigatória.')
    );

    if (!empty($garantiaInicio) && !empty($garantiaFim) && $garantiaFim <= $garantiaInicio)
        $erros[] = 'A data de fim da garantia deve ser posterior à data de início.';

    if (empty($erros)) {
        $designacao  = ucwords(strtolower($designacao));
        $marca       = ucwords(strtolower($marca));
        $fabricante  = ucwords(strtolower($fabricante));
        $temContrato = ($contratoManutencao === 'sim') ? 1 : 0;

        try {
            $ligacao = new PDO(
                "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
                MYSQL_USERNAME, MYSQL_PASSWORD
            );
            $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $ligacao->beginTransaction();

            $stmt = $ligacao->prepare(
                "UPDATE Equipamento
                 SET designacao = :designacao, codigoCategoria = :codigoCategoria, marca = :marca,
                     modelo = :modelo, fabricante = :fabricante, numeroSerie = :numeroSerie,
                     anoFabrico = :anoFabrico, dataAquisicao = :dataAquisicao,
                     custoAquisicao = :custoAquisicao, tipoEntrada = :tipoEntrada,
                     estado = :estado, criticidade = :criticidade, observacoes = :observacoes,
                     codigoLocalizacao = :codigoLocalizacao
                 WHERE codigo = :id"
            );
            $stmt->execute([
                ':designacao'      => $designacao,
                ':codigoCategoria' => (int)$codigoCategoria,
                ':marca'           => $marca,
                ':modelo'          => $modelo,
                ':fabricante'      => $fabricante,
                ':numeroSerie'     => $numeroSerie,
                ':anoFabrico'      => (int)$anoFabrico,
                ':dataAquisicao'   => $dataAquisicao,
                ':custoAquisicao'  => (float)$custoAquisicao,
                ':tipoEntrada'     => $tipoEntrada,
                ':estado'          => $estado,
                ':criticidade'     => $criticidade,
                ':observacoes'       => $observacoes ?: null,
                ':codigoLocalizacao' => $codigoLocalizacao ? (int)$codigoLocalizacao : null,
                ':id'                => (int)$idEquipamento,
            ]);

            if ($codigoGarantia) {
                $stmt2 = $ligacao->prepare(
                    "UPDATE Garantia
                     SET dataInicio = :dataInicio, dataFim = :dataFim, temContrato = :temContrato,
                         tipoContrato = :tipoContrato, entidadeResponsavel = :entidadeResponsavel,
                         periodicidade = :periodicidade, observacoes = :observacoes
                     WHERE codigo = :codigoGarantia"
                );
                $stmt2->execute([
                    ':dataInicio'          => $garantiaInicio,
                    ':dataFim'             => $garantiaFim,
                    ':temContrato'         => $temContrato,
                    ':tipoContrato'        => $tipoContrato        ?: null,
                    ':entidadeResponsavel' => $entidadeResponsavel ?: null,
                    ':periodicidade'       => $periodicidade        ?: null,
                    ':observacoes'         => $observacoesContrato  ?: null,
                    ':codigoGarantia'      => (int)$codigoGarantia,
                ]);
            }

            $fornSelecionados = $_POST['fornecedores'] ?? [];
            $ligacao->prepare("DELETE FROM EquipamentoFornecedor WHERE codigoEquipamento = :id")
                    ->execute([':id' => (int)$idEquipamento]);
            if (!empty($fornSelecionados)) {
                $stmtForn = $ligacao->prepare(
                    "INSERT IGNORE INTO EquipamentoFornecedor (codigoEquipamento, codigoFornecedor) VALUES (:eq, :forn)"
                );
                foreach ($fornSelecionados as $fornId) {
                    if (ctype_digit((string) $fornId)) {
                        $stmtForn->execute([':eq' => (int)$idEquipamento, ':forn' => $fornId]);
                    }
                }
            }

            $ligacao->commit();
            registar_log('equipamento_editado', 'Equipamento editado: #' . $idEquipamento);
            $_SESSION['toast'] = ['tipo' => 'success', 'mensagem' => 'Equipamento atualizado com sucesso.'];
            header('Location: lista.php');
            exit;
        } catch (PDOException) {
            if (isset($ligacao) && $ligacao->inTransaction()) $ligacao->rollBack();
            $erros[] = 'Erro ao guardar as alterações. Por favor tente novamente.';
        }
    }
}

try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
        MYSQL_USERNAME, MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $ligacao->prepare(
        "SELECT e.*,
                g.codigo        AS codigoGarantia,
                g.dataInicio    AS garantiaInicio,
                g.dataFim       AS garantiaFim,
                g.temContrato,
                g.tipoContrato,
                g.entidadeResponsavel,
                g.periodicidade,
                g.observacoes   AS observacoesContrato
         FROM Equipamento e
         LEFT JOIN Garantia g ON g.codigoEquipamento = e.codigo
         WHERE e.codigo = :id
         LIMIT 1"
    );
    $stmt->bindParam(':id', $idEquipamento, PDO::PARAM_INT);
    $stmt->execute();
    $equipamento = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$equipamento) {
        header('Location: lista.php');
        exit;
    }

    $categorias   = $ligacao->query("SELECT codigo, nome FROM Categoria ORDER BY nome")->fetchAll(PDO::FETCH_OBJ);
    $localizacoes = $ligacao->query("SELECT codigo, edificio, piso, servico, sala FROM Localizacao WHERE ativo = 1 ORDER BY edificio, piso, servico")->fetchAll(PDO::FETCH_OBJ);
    $fornecedores = $ligacao->query("SELECT codigo, nome, tipoFornecedor FROM Fornecedor WHERE ativo = 1 ORDER BY nome")->fetchAll(PDO::FETCH_OBJ);

    $stmtFornAssoc = $ligacao->prepare("SELECT codigoFornecedor FROM EquipamentoFornecedor WHERE codigoEquipamento = :id");
    $stmtFornAssoc->execute([':id' => $idEquipamento]);
    $fornAssociados = array_column($stmtFornAssoc->fetchAll(PDO::FETCH_OBJ), 'codigoFornecedor');
} catch (PDOException) {
    $erros[] = 'Erro na ligação à base de dados.';
    $equipamento  = null;
    $categorias   = [];
    $localizacoes = [];
    $fornecedores = [];
    $fornAssociados = [];
}
$ligacao = null;
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/nav.php'; ?>

    <div class="container-fluid">
        <div class="row">

            <?php include '../includes/sidebar.php'; ?>

            <main class="col-md-9 col-lg-10 px-md-4 pt-4">
                <div class="card shadow-sm border p-4 mx-auto my-4" style="max-width: 850px; background-color: #fff;">
                    <h2 class="fw-bold mb-3" style="color: #1e1b4b;"><i class="fa-solid fa-pen-to-square"></i> Atualização de Dados</h2>
                    <hr>

                    <?php if (!empty($erros)): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($erros as $erro): ?>
                        <div><?= htmlspecialchars($erro) ?></div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <?php if ($equipamento): ?>
                    <form action="editar.php?id_equipamento=<?= $idEquipamentoEncrypted ?>" method="post" class="row g-3">

                        <input type="hidden" name="codigoGarantia" value="<?= $equipamento->codigoGarantia ?>">

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Código Interno de Inventário</label>
                            <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($equipamento->codigoInterno) ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Designação do Equipamento</label>
                            <input type="text" name="designacao" class="form-control"
                                   value="<?= htmlspecialchars($equipamento->designacao) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Categoria / Grupo</label>
                            <select name="codigoCategoria" class="form-select">
                                <option disabled value="">Escolha uma opção...</option>
                                <?php foreach ($categorias as $cat): ?>
                                <option value="<?= $cat->codigo ?>" <?= $equipamento->codigoCategoria == $cat->codigo ? 'selected' : '' ?>><?= htmlspecialchars($cat->nome) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Marca</label>
                            <input type="text" name="marca" class="form-control"
                                   value="<?= htmlspecialchars($equipamento->marca) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Modelo</label>
                            <input type="text" name="modelo" class="form-control"
                                   value="<?= htmlspecialchars($equipamento->modelo) ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Número de Série</label>
                            <input type="text" name="numeroSerie" class="form-control"
                                   value="<?= htmlspecialchars($equipamento->numeroSerie) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Fabricante</label>
                            <input type="text" name="fabricante" class="form-control"
                                   value="<?= htmlspecialchars($equipamento->fabricante) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Ano de Fabrico</label>
                            <input type="number" name="anoFabrico" class="form-control" min="1900" max="<?= date('Y') ?>"
                                   value="<?= htmlspecialchars($equipamento->anoFabrico) ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Data de Aquisição</label>
                            <input type="text" name="dataAquisicao" id="dataAquisicao" class="form-control"
                                   value="<?= htmlspecialchars($equipamento->dataAquisicao) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Custo de Aquisição (€)</label>
                            <input type="number" name="custoAquisicao" step="0.01" class="form-control"
                                   value="<?= htmlspecialchars($equipamento->custoAquisicao) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Tipo de Entrada</label>
                            <select name="tipoEntrada" class="form-select">
                                <option value="compra"     <?= $equipamento->tipoEntrada === 'compra'     ? 'selected' : '' ?>>Compra</option>
                                <option value="doacao"     <?= $equipamento->tipoEntrada === 'doacao'     ? 'selected' : '' ?>>Doação</option>
                                <option value="aluguer"    <?= $equipamento->tipoEntrada === 'aluguer'    ? 'selected' : '' ?>>Aluguer</option>
                                <option value="emprestimo" <?= $equipamento->tipoEntrada === 'emprestimo' ? 'selected' : '' ?>>Empréstimo</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Estado Atual</label>
                            <select name="estado" class="form-select">
                                <option value="ativo"         <?= $equipamento->estado === 'ativo'         ? 'selected' : '' ?>>Ativo / Operacional</option>
                                <option value="em manutencao" <?= $equipamento->estado === 'em manutencao' ? 'selected' : '' ?>>Em Manutenção</option>
                                <option value="em calibracao" <?= $equipamento->estado === 'em calibracao' ? 'selected' : '' ?>>Em Calibração</option>
                                <option value="em quarentena" <?= $equipamento->estado === 'em quarentena' ? 'selected' : '' ?>>Em Quarentena</option>
                                <option value="inativo"       <?= $equipamento->estado === 'inativo'       ? 'selected' : '' ?>>Inativo</option>
                                <option value="abatido"       <?= $equipamento->estado === 'abatido'       ? 'selected' : '' ?>>Abatido</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Criticidade</label>
                            <select name="criticidade" class="form-select">
                                <option value="baixa"          <?= $equipamento->criticidade === 'baixa'          ? 'selected' : '' ?>>Baixa</option>
                                <option value="media"          <?= $equipamento->criticidade === 'media'          ? 'selected' : '' ?>>Média</option>
                                <option value="alta"           <?= $equipamento->criticidade === 'alta'           ? 'selected' : '' ?>>Alta</option>
                                <option value="suporte de vida" <?= $equipamento->criticidade === 'suporte de vida' ? 'selected' : '' ?>>Suporte de Vida</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold">Observações Gerais</label>
                            <textarea name="observacoes" class="form-control" rows="2"><?= htmlspecialchars($equipamento->observacoes ?? '') ?></textarea>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold">Localização</label>
                            <select name="codigoLocalizacao" class="form-select">
                                <option value="">— Sem localização atribuída —</option>
                                <?php
                                $locAtual = $_POST['codigoLocalizacao'] ?? $equipamento->codigoLocalizacao;
                                foreach ($localizacoes as $loc): ?>
                                <option value="<?= $loc->codigo ?>" <?= $locAtual == $loc->codigo ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($loc->edificio) ?><?= $loc->piso ? ' · ' . htmlspecialchars($loc->piso) : '' ?> — <?= htmlspecialchars($loc->servico) ?><?= $loc->sala ? ' · ' . htmlspecialchars($loc->sala) : '' ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">Fornecedores Associados</label>
                            <p class="text-muted small mb-2">Seleciona os fornecedores que fornecem ou prestam assistência a este equipamento.</p>
                            <?php if (empty($fornecedores)): ?>
                                <p class="text-muted fst-italic">Nenhum fornecedor disponível.</p>
                            <?php else: ?>
                            <div class="border rounded p-3" style="max-height: 220px; overflow-y: auto;">
                                <?php
                                $fornAtual = $_POST['fornecedores'] ?? $fornAssociados;
                                foreach ($fornecedores as $forn): ?>
                                <div class="form-check mb-1">
                                    <input class="form-check-input" type="checkbox" name="fornecedores[]"
                                        id="forn<?= $forn->codigo ?>"
                                        value="<?= $forn->codigo ?>"
                                        <?= in_array((string) $forn->codigo, array_map('strval', $fornAtual)) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="forn<?= $forn->codigo ?>">
                                        <span class="fw-semibold"><?= htmlspecialchars($forn->nome) ?></span>
                                        <small class="text-muted ms-1">— <?= htmlspecialchars($forn->tipoFornecedor) ?></small>
                                    </label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>

                        <h5 class="fw-bold mt-4 pt-2 mb-3" style="color: #1e1b4b;"><i class="fa-solid fa-file-signature"></i> Garantia e Contrato de Manutenção</h5>
                        <div class="row g-3 p-3 bg-light rounded border mx-0">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Data de Início da Garantia</label>
                                <input type="text" name="garantiaInicio" id="garantiaInicio" class="form-control"
                                       value="<?= htmlspecialchars($equipamento->garantiaInicio ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Data de Fim da Garantia</label>
                                <input type="text" name="garantiaFim" id="garantiaFim" class="form-control"
                                       value="<?= htmlspecialchars($equipamento->garantiaFim ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Contrato de Manutenção?</label>
                                <select name="contratoManutencao" class="form-select">
                                    <option value="nao" <?= !$equipamento->temContrato ? 'selected' : '' ?>>Não, apenas garantia base</option>
                                    <option value="sim" <?= $equipamento->temContrato  ? 'selected' : '' ?>>Sim, contrato ativo</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Tipo de Contrato</label>
                                <select name="tipoContrato" class="form-select">
                                    <option value="">Escolha uma opção...</option>
                                    <option value="Preventiva e Corretiva" <?= $equipamento->tipoContrato === 'Preventiva e Corretiva' ? 'selected' : '' ?>>Manutenção Preventiva e Corretiva (Full Risk)</option>
                                    <option value="Apenas Preventiva"      <?= $equipamento->tipoContrato === 'Apenas Preventiva'      ? 'selected' : '' ?>>Apenas Manutenção Preventiva</option>
                                    <option value="Suporte Técnico"        <?= $equipamento->tipoContrato === 'Suporte Técnico'        ? 'selected' : '' ?>>Suporte Técnico Remoto / Calibração</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Entidade Responsável (Fornecedor)</label>
                                <input type="text" name="entidadeResponsavel" class="form-control"
                                       value="<?= htmlspecialchars($equipamento->entidadeResponsavel ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Periodicidade das Visitas</label>
                                <select name="periodicidade" class="form-select">
                                    <option value="">Selecione o intervalo...</option>
                                    <option value="trimestral" <?= $equipamento->periodicidade === 'trimestral' ? 'selected' : '' ?>>Trimestral</option>
                                    <option value="semestral"  <?= $equipamento->periodicidade === 'semestral'  ? 'selected' : '' ?>>Semestral</option>
                                    <option value="anual"      <?= $equipamento->periodicidade === 'anual'      ? 'selected' : '' ?>>Anual</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Observações do Contrato</label>
                                <textarea name="observacoesContrato" class="form-control" rows="2"><?= htmlspecialchars($equipamento->observacoesContrato ?? '') ?></textarea>
                            </div>
                        </div>

                        <div class="col-12 d-flex gap-2 mt-4">
                            <a href="lista.php" class="btn btn-secondary px-4"><i class="fa-solid fa-xmark"></i> Cancelar</a>
                            <button type="submit" class="btn text-white px-4" style="background-color: #1e1b4b;"><i class="fa-regular fa-floppy-disk"></i> Guardar Alterações</button>
                        </div>
                    </form>
                    <?php endif; ?>
                </div>
            </main>

        </div>
    </div>

<script src="/sibdas/1241028/medcore/assets/flatpickr/flatpickr.js"></script>
<script>
    flatpickr("#dataAquisicao",  { dateFormat: "Y-m-d" });
    flatpickr("#garantiaInicio", { dateFormat: "Y-m-d" });
    flatpickr("#garantiaFim",    { dateFormat: "Y-m-d" });
</script>

    <?php include '../includes/footer.php'; ?>
