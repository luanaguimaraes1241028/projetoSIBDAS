<?php require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_readonly();

$ligacao = ligar_bd();
if (!$ligacao) { header('Location: lista.php'); exit; }

$equipamentos = $ligacao->query("SELECT codigo, codigoInterno, designacao FROM Equipamento ORDER BY codigoInterno")->fetchAll(PDO::FETCH_OBJ);
$fornecedores = $ligacao->query("SELECT codigo, nome FROM Fornecedor WHERE ativo = 1 ORDER BY nome")->fetchAll(PDO::FETCH_OBJ);

$tiposValidos = ['manual de utilizador', 'manual de servico', 'certificado de calibracao', 'contrato de manutencao', 'fatura ou guia de aquisicao', 'declaracao de conformidade', 'relatorio tecnico'];
$tiposLabel   = [
    'manual de utilizador'        => 'Manual de Utilizador',
    'manual de servico'           => 'Manual de Serviço',
    'certificado de calibracao'   => 'Certificado de Calibração',
    'contrato de manutencao'      => 'Contrato de Manutenção',
    'fatura ou guia de aquisicao' => 'Fatura ou Guia de Aquisição',
    'declaracao de conformidade'  => 'Declaração de Conformidade',
    'relatorio tecnico'           => 'Relatório Técnico',
];

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome              = trim($_POST['nome'] ?? '');
    $tipo              = $_POST['tipo'] ?? '';
    $dataDocumento     = $_POST['dataDocumento'] ?? '';
    $dataValidade      = trim($_POST['dataValidade'] ?? '') ?: null;
    $ficheiro          = trim($_POST['ficheiro'] ?? '') ?: null;
    $codigoEquipamento = (int)($_POST['codigoEquipamento'] ?? 0);
    $codigoFornecedor  = (int)($_POST['codigoFornecedor'] ?? 0) ?: null;

    if (!$nome) {
        $erro = "O nome do documento é obrigatório.";
    } elseif (!in_array($tipo, $tiposValidos)) {
        $erro = "Tipo de documento inválido.";
    } elseif (!$dataDocumento) {
        $erro = "A data do documento é obrigatória.";
    } elseif (!$codigoEquipamento) {
        $erro = "Selecione o equipamento associado.";
    } else {
        try {
            $stmt = $ligacao->prepare(
                "INSERT INTO Documentacao (nome, tipo, dataDocumento, dataValidade, ficheiro, codigoEquipamento, codigoFornecedor)
                 VALUES (:nome, :tipo, :dataDocumento, :dataValidade, :ficheiro, :codigoEquipamento, :codigoFornecedor)"
            );
            $stmt->execute([
                ':nome'              => $nome,
                ':tipo'              => $tipo,
                ':dataDocumento'     => $dataDocumento,
                ':dataValidade'      => $dataValidade,
                ':ficheiro'          => $ficheiro,
                ':codigoEquipamento' => $codigoEquipamento,
                ':codigoFornecedor'  => $codigoFornecedor,
            ]);
            $ligacao = null;
            $_SESSION['toast'] = ['tipo' => 'success', 'mensagem' => 'Documento registado com sucesso.'];
            header('Location: lista.php');
            exit;
        } catch (PDOException $err2) {
            $erro = "Erro ao guardar o documento.";
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
            <div class="card shadow-sm border p-4 mx-auto my-4" style="max-width: 800px;">
                <h2 class="fw-bold mb-3" style="color: #1e1b4b;">
                    <i class="fa-solid fa-file-circle-plus"></i> Associar Novo Documento
                </h2>
                <hr>

                <?php if ($erro): ?>
                    <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation me-2"></i><?= htmlspecialchars($erro) ?></div>
                <?php endif; ?>

                <form method="post" class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Nome do Documento <span class="text-danger">*</span></label>
                        <input type="text" name="nome" class="form-control" placeholder="Ex: Manual de Utilizador V1" value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tipo de Documento <span class="text-danger">*</span></label>
                        <select name="tipo" class="form-select" required>
                            <option value="" disabled <?= empty($_POST['tipo']) ? 'selected' : '' ?>>Escolha um tipo...</option>
                            <?php foreach ($tiposLabel as $val => $label): ?>
                            <option value="<?= $val ?>" <?= ($_POST['tipo'] ?? '') === $val ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Data do Documento <span class="text-danger">*</span></label>
                        <input type="date" name="dataDocumento" class="form-control" value="<?= htmlspecialchars($_POST['dataDocumento'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Data de Validade <span class="text-muted small">(opcional)</span></label>
                        <input type="date" name="dataValidade" class="form-control" value="<?= htmlspecialchars($_POST['dataValidade'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Equipamento Associado <span class="text-danger">*</span></label>
                        <select name="codigoEquipamento" class="form-select" required>
                            <option value="" disabled <?= empty($_POST['codigoEquipamento']) ? 'selected' : '' ?>>Selecione o equipamento...</option>
                            <?php foreach ($equipamentos as $eq): ?>
                            <option value="<?= $eq->codigo ?>" <?= ($_POST['codigoEquipamento'] ?? '') == $eq->codigo ? 'selected' : '' ?>>
                                <?= htmlspecialchars($eq->codigoInterno . ' — ' . $eq->designacao) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Fornecedor Associado <span class="text-muted small">(opcional)</span></label>
                        <select name="codigoFornecedor" class="form-select">
                            <option value="">— Nenhum —</option>
                            <?php foreach ($fornecedores as $forn): ?>
                            <option value="<?= $forn->codigo ?>" <?= ($_POST['codigoFornecedor'] ?? '') == $forn->codigo ? 'selected' : '' ?>>
                                <?= htmlspecialchars($forn->nome) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Nome / Caminho do Ficheiro <span class="text-muted small">(opcional)</span></label>
                        <input type="text" name="ficheiro" class="form-control" placeholder="Ex: manual_ventilador_v1.pdf" value="<?= htmlspecialchars($_POST['ficheiro'] ?? '') ?>">
                    </div>

                    <div class="col-12 d-flex gap-2 mt-4">
                        <a href="lista.php" class="btn btn-secondary px-4">
                            <i class="fa-solid fa-xmark"></i> Cancelar
                        </a>
                        <button type="submit" class="btn text-white px-4" style="background-color: #1e1b4b;">
                            <i class="fa-regular fa-floppy-disk"></i> Guardar Documento
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
