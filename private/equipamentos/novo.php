<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_readonly();

try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
        MYSQL_USERNAME, MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $categorias    = $ligacao->query("SELECT codigo, nome FROM Categoria ORDER BY nome")->fetchAll(PDO::FETCH_OBJ);
    $localizacoes  = $ligacao->query("SELECT codigo, edificio, piso, servico, sala FROM Localizacao WHERE ativo = 1 ORDER BY edificio, piso, servico")->fetchAll(PDO::FETCH_OBJ);
} catch (PDOException) {
    $categorias   = [];
    $localizacoes = [];
}
$ligacao = null;

$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigoInterno       = trim($_POST['codigoInterno']       ?? '');
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

    if (empty($codigoInterno))
        $erros[] = 'O código interno é obrigatório.';
    if (empty($designacao))
        $erros[] = 'A designação é obrigatória.';
    if (empty($codigoCategoria) || !ctype_digit($codigoCategoria))
        $erros[] = 'Selecione uma categoria válida.';
    if (empty($marca))
        $erros[] = 'A marca é obrigatória.';
    if (empty($modelo))
        $erros[] = 'O modelo é obrigatório.';
    if (empty($numeroSerie))
        $erros[] = 'O número de série é obrigatório.';
    if (empty($fabricante))
        $erros[] = 'O fabricante é obrigatório.';
    if (empty($anoFabrico) || !preg_match('/^\d{4}$/', $anoFabrico) || (int)$anoFabrico < 1900 || (int)$anoFabrico > (int)date('Y'))
        $erros[] = 'O ano de fabrico deve ser um valor entre 1900 e ' . date('Y') . '.';
    if (empty($dataAquisicao))
        $erros[] = 'A data de aquisição é obrigatória.';
    if (empty($custoAquisicao) || !is_numeric($custoAquisicao) || (float)$custoAquisicao < 0)
        $erros[] = 'O custo de aquisição deve ser um valor numérico positivo.';
    if (empty($tipoEntrada))
        $erros[] = 'O tipo de entrada é obrigatório.';
    if (empty($estado))
        $erros[] = 'O estado atual é obrigatório.';
    if (empty($criticidade))
        $erros[] = 'A criticidade é obrigatória.';
    if (empty($garantiaInicio))
        $erros[] = 'A data de início da garantia é obrigatória.';
    if (empty($garantiaFim))
        $erros[] = 'A data de fim da garantia é obrigatória.';
    if (!empty($garantiaInicio) && !empty($garantiaFim) && $garantiaFim <= $garantiaInicio)
        $erros[] = 'A data de fim da garantia deve ser posterior à data de início.';

    if (empty($erros)) {
        $codigoInterno = strtoupper($codigoInterno);
        $designacao    = ucwords(strtolower($designacao));
        $marca         = ucwords(strtolower($marca));
        $fabricante    = ucwords(strtolower($fabricante));
        $temContrato   = ($contratoManutencao === 'sim') ? 1 : 0;

        try {
            $ligacao = new PDO(
                "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
                MYSQL_USERNAME, MYSQL_PASSWORD
            );
            $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $ligacao->beginTransaction();

            $stmt = $ligacao->prepare(
                "INSERT INTO Equipamento
                 (codigoInterno, designacao, marca, modelo, fabricante, numeroSerie, anoFabrico,
                  dataAquisicao, custoAquisicao, tipoEntrada, estado, criticidade, observacoes, codigoCategoria, codigoLocalizacao)
                 VALUES
                 (:codigoInterno, :designacao, :marca, :modelo, :fabricante, :numeroSerie, :anoFabrico,
                  :dataAquisicao, :custoAquisicao, :tipoEntrada, :estado, :criticidade, :observacoes, :codigoCategoria, :codigoLocalizacao)"
            );
            $stmt->execute([
                ':codigoInterno'   => $codigoInterno,
                ':designacao'      => $designacao,
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
                ':observacoes'     => $observacoes ?: null,
                ':codigoCategoria'   => (int)$codigoCategoria,
                ':codigoLocalizacao' => $codigoLocalizacao ? (int)$codigoLocalizacao : null,
            ]);

            $idEquipamento = $ligacao->lastInsertId();

            $stmt2 = $ligacao->prepare(
                "INSERT INTO Garantia
                 (dataInicio, dataFim, temContrato, tipoContrato, entidadeResponsavel, periodicidade, observacoes, codigoEquipamento)
                 VALUES
                 (:dataInicio, :dataFim, :temContrato, :tipoContrato, :entidadeResponsavel, :periodicidade, :observacoes, :codigoEquipamento)"
            );
            $stmt2->execute([
                ':dataInicio'          => $garantiaInicio,
                ':dataFim'             => $garantiaFim,
                ':temContrato'         => $temContrato,
                ':tipoContrato'        => $tipoContrato        ?: null,
                ':entidadeResponsavel' => $entidadeResponsavel ?: null,
                ':periodicidade'       => $periodicidade        ?: null,
                ':observacoes'         => $observacoesContrato  ?: null,
                ':codigoEquipamento'   => $idEquipamento,
            ]);

            $ligacao->commit();
            $_SESSION['toast'] = ['tipo' => 'success', 'mensagem' => 'Equipamento inserido com sucesso.'];
            header('Location: lista.php');
            exit;
        } catch (PDOException) {
            if (isset($ligacao) && $ligacao->inTransaction()) $ligacao->rollBack();
            $erros[] = 'Erro ao guardar o equipamento. Por favor tente novamente.';
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
                <div class="card shadow-sm border p-4 mx-auto my-4" style="max-width: 850px; background-color: #fff;">
                    <h2 class="fw-bold mb-3" style="color: #1e1b4b;"><i class="fa-solid fa-square-plus"></i> Inserir Novo Equipamento</h2>
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

                    <form action="#" method="post" class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Código Interno de Inventário</label>
                            <input type="text" name="codigoInterno" class="form-control" placeholder="Ex: EQ-0043"
                                   value="<?= htmlspecialchars($_POST['codigoInterno'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Designação do Equipamento</label>
                            <input type="text" name="designacao" class="form-control" placeholder="Ex: Ventilador Volumétrico"
                                   value="<?= htmlspecialchars($_POST['designacao'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Categoria / Grupo</label>
                            <select name="codigoCategoria" class="form-select">
                                <option disabled value="" <?= empty($_POST['codigoCategoria']) ? 'selected' : '' ?>>Escolha uma opção...</option>
                                <?php foreach ($categorias as $cat): ?>
                                <option value="<?= $cat->codigo ?>" <?= ($_POST['codigoCategoria'] ?? '') == $cat->codigo ? 'selected' : '' ?>><?= htmlspecialchars($cat->nome) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Marca</label>
                            <input type="text" name="marca" class="form-control" placeholder="Ex: Dräger"
                                   value="<?= htmlspecialchars($_POST['marca'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Modelo</label>
                            <input type="text" name="modelo" class="form-control" placeholder="Ex: Evita V500"
                                   value="<?= htmlspecialchars($_POST['modelo'] ?? '') ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Número de Série</label>
                            <input type="text" name="numeroSerie" class="form-control" placeholder="Ex: SN-DRG-88321-X"
                                   value="<?= htmlspecialchars($_POST['numeroSerie'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Fabricante</label>
                            <input type="text" name="fabricante" class="form-control" placeholder="Ex: Dräger Medical GmbH"
                                   value="<?= htmlspecialchars($_POST['fabricante'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Ano de Fabrico</label>
                            <input type="number" name="anoFabrico" class="form-control" placeholder="Ex: <?= date('Y') ?>" min="1900" max="<?= date('Y') ?>"
                                   value="<?= htmlspecialchars($_POST['anoFabrico'] ?? '') ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Data de Aquisição</label>
                            <input type="text" name="dataAquisicao" id="dataAquisicao" class="form-control" placeholder="AAAA-MM-DD"
                                   value="<?= htmlspecialchars($_POST['dataAquisicao'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Custo de Aquisição (€)</label>
                            <input type="number" name="custoAquisicao" step="0.01" class="form-control" placeholder="Ex: 24500.00"
                                   value="<?= htmlspecialchars($_POST['custoAquisicao'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Tipo de Entrada</label>
                            <select name="tipoEntrada" class="form-select">
                                <option disabled value="" <?= empty($_POST['tipoEntrada']) ? 'selected' : '' ?>>Selecione...</option>
                                <option value="compra"    <?= ($_POST['tipoEntrada'] ?? '') === 'compra'    ? 'selected' : '' ?>>Compra</option>
                                <option value="doacao"    <?= ($_POST['tipoEntrada'] ?? '') === 'doacao'    ? 'selected' : '' ?>>Doação</option>
                                <option value="aluguer"   <?= ($_POST['tipoEntrada'] ?? '') === 'aluguer'   ? 'selected' : '' ?>>Aluguer</option>
                                <option value="emprestimo" <?= ($_POST['tipoEntrada'] ?? '') === 'emprestimo' ? 'selected' : '' ?>>Empréstimo</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Estado Atual</label>
                            <select name="estado" class="form-select">
                                <option disabled value="" <?= empty($_POST['estado']) ? 'selected' : '' ?>>Selecione...</option>
                                <option value="ativo"          <?= ($_POST['estado'] ?? '') === 'ativo'          ? 'selected' : '' ?>>Ativo / Operacional</option>
                                <option value="em manutencao"  <?= ($_POST['estado'] ?? '') === 'em manutencao'  ? 'selected' : '' ?>>Em Manutenção</option>
                                <option value="em calibracao"  <?= ($_POST['estado'] ?? '') === 'em calibracao'  ? 'selected' : '' ?>>Em Calibração</option>
                                <option value="em quarentena"  <?= ($_POST['estado'] ?? '') === 'em quarentena'  ? 'selected' : '' ?>>Em Quarentena</option>
                                <option value="inativo"        <?= ($_POST['estado'] ?? '') === 'inativo'        ? 'selected' : '' ?>>Inativo</option>
                                <option value="abatido"        <?= ($_POST['estado'] ?? '') === 'abatido'        ? 'selected' : '' ?>>Abatido</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Criticidade</label>
                            <select name="criticidade" class="form-select">
                                <option disabled value="" <?= empty($_POST['criticidade']) ? 'selected' : '' ?>>Selecione...</option>
                                <option value="baixa"          <?= ($_POST['criticidade'] ?? '') === 'baixa'          ? 'selected' : '' ?>>Baixa</option>
                                <option value="media"          <?= ($_POST['criticidade'] ?? '') === 'media'          ? 'selected' : '' ?>>Média</option>
                                <option value="alta"           <?= ($_POST['criticidade'] ?? '') === 'alta'           ? 'selected' : '' ?>>Alta</option>
                                <option value="suporte de vida" <?= ($_POST['criticidade'] ?? '') === 'suporte de vida' ? 'selected' : '' ?>>Suporte de Vida</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold">Observações Gerais</label>
                            <textarea name="observacoes" class="form-control" rows="2" placeholder="Notas adicionais sobre o equipamento..."><?= htmlspecialchars($_POST['observacoes'] ?? '') ?></textarea>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold">Localização</label>
                            <select name="codigoLocalizacao" class="form-select">
                                <option value="">— Sem localização atribuída —</option>
                                <?php foreach ($localizacoes as $loc): ?>
                                <option value="<?= $loc->codigo ?>" <?= ($_POST['codigoLocalizacao'] ?? '') == $loc->codigo ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($loc->edificio) ?><?= $loc->piso ? ' · ' . htmlspecialchars($loc->piso) : '' ?> — <?= htmlspecialchars($loc->servico) ?><?= $loc->sala ? ' · ' . htmlspecialchars($loc->sala) : '' ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <h5 class="fw-bold mt-4 pt-2 mb-3" style="color: #1e1b4b;"><i class="fa-solid fa-file-signature"></i> Garantia e Contrato de Manutenção</h5>
                        <div class="row g-3 p-3 bg-light rounded border mx-0">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Data de Início da Garantia</label>
                                <input type="text" name="garantiaInicio" id="garantiaInicio" class="form-control" placeholder="AAAA-MM-DD"
                                       value="<?= htmlspecialchars($_POST['garantiaInicio'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Data de Fim da Garantia</label>
                                <input type="text" name="garantiaFim" id="garantiaFim" class="form-control" placeholder="AAAA-MM-DD"
                                       value="<?= htmlspecialchars($_POST['garantiaFim'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Contrato de Manutenção?</label>
                                <select name="contratoManutencao" class="form-select">
                                    <option value="nao" <?= ($_POST['contratoManutencao'] ?? 'nao') === 'nao' ? 'selected' : '' ?>>Não, apenas garantia base</option>
                                    <option value="sim" <?= ($_POST['contratoManutencao'] ?? '') === 'sim' ? 'selected' : '' ?>>Sim, contrato ativo</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Tipo de Contrato</label>
                                <select name="tipoContrato" class="form-select">
                                    <option value="" <?= empty($_POST['tipoContrato']) ? 'selected' : '' ?>>Escolha uma opção...</option>
                                    <option value="Preventiva e Corretiva" <?= ($_POST['tipoContrato'] ?? '') === 'Preventiva e Corretiva' ? 'selected' : '' ?>>Manutenção Preventiva e Corretiva (Full Risk)</option>
                                    <option value="Apenas Preventiva"      <?= ($_POST['tipoContrato'] ?? '') === 'Apenas Preventiva'      ? 'selected' : '' ?>>Apenas Manutenção Preventiva</option>
                                    <option value="Suporte Técnico"        <?= ($_POST['tipoContrato'] ?? '') === 'Suporte Técnico'        ? 'selected' : '' ?>>Suporte Técnico Remoto / Calibração</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Entidade Responsável (Fornecedor)</label>
                                <input type="text" name="entidadeResponsavel" class="form-control" placeholder="Ex: Dräger Portugal Lda."
                                       value="<?= htmlspecialchars($_POST['entidadeResponsavel'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Periodicidade das Visitas</label>
                                <select name="periodicidade" class="form-select">
                                    <option value="" <?= empty($_POST['periodicidade']) ? 'selected' : '' ?>>Selecione o intervalo...</option>
                                    <option value="trimestral" <?= ($_POST['periodicidade'] ?? '') === 'trimestral' ? 'selected' : '' ?>>Trimestral</option>
                                    <option value="semestral"  <?= ($_POST['periodicidade'] ?? '') === 'semestral'  ? 'selected' : '' ?>>Semestral</option>
                                    <option value="anual"      <?= ($_POST['periodicidade'] ?? '') === 'anual'      ? 'selected' : '' ?>>Anual</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Observações do Contrato</label>
                                <textarea name="observacoesContrato" class="form-control" rows="2" placeholder="Ex: Inclui substituição de kits de juntas e calibração anual pneumática."><?= htmlspecialchars($_POST['observacoesContrato'] ?? '') ?></textarea>
                            </div>
                        </div>

                        <div class="col-12 d-flex gap-2 mt-4">
                            <a href="lista.php" class="btn btn-secondary px-4"><i class="fa-solid fa-xmark"></i> Cancelar</a>
                            <button type="submit" class="btn text-white px-4" style="background-color: #1e1b4b;"><i class="fa-regular fa-floppy-disk"></i> Guardar</button>
                        </div>
                    </form>
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
