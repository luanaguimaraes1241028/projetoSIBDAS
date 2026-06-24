<?php require_once __DIR__ . '/../includes/funcoes.php';

// Segurança: Garante que utilizadores com permissões apenas de leitura não acedem a este script
redirect_if_readonly();

// Captura e valida o identificador cifrado passado via URL (GET)
$id_cifrado = $_GET['id'] ?? '';
$id = aes_decrypt($id_cifrado);

// Se a desencriptação falhar ou o ID não for puramente numérico, interrompe e redireciona
if ($id === false || !ctype_digit((string) $id)) {
    header('Location: lista.php');
    exit;
}

// Estabelece a ligação à base de dados através do driver PDO
$ligacao = ligar_bd();
if (!$ligacao) { header('Location: lista.php'); exit; }

// Listas brancas (Whitelists) para validação de integridade dos dados no servidor
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

$doc = null;
$erros = [];

try {
    // Procura o documento original ativo para preencher os valores iniciais do formulário
    $stmt = $ligacao->prepare("SELECT * FROM Documentacao WHERE codigo = :id AND ativo = 1");
    $stmt->execute([':id' => $id]);
    $doc = $stmt->fetch(PDO::FETCH_OBJ);
    
    // Se o documento não existir ou já estiver arquivado, barra o acesso
    if (!$doc) { header('Location: lista.php'); exit; }

    // Carrega as listas que vão alimentar os blocos de seleção (<select>) no HTML
    $equipamentos = $ligacao->query("SELECT codigo, codigoInterno, designacao FROM Equipamento ORDER BY codigoInterno")->fetchAll(PDO::FETCH_OBJ);
    $fornecedores = $ligacao->query("SELECT codigo, nome FROM Fornecedor WHERE ativo = 1 ORDER BY nome")->fetchAll(PDO::FETCH_OBJ);
} catch (PDOException $err2) {
    header('Location: lista.php');
    exit;
}

// --- PROCESSAMENTO DO FORMULÁRIO (MÉTODO POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Captura e limpa os espaços em branco indesejados dos inputs de texto
    $nome              = trim($_POST['nome'] ?? '');
    $tipo              = $_POST['tipo'] ?? '';
    $dataDocumento     = $_POST['dataDocumento'] ?? '';
    $dataValidade      = trim($_POST['dataValidade'] ?? '') ?: null; // Converte string vazia em NULL para a BD
    $ficheiro          = trim($_POST['ficheiro'] ?? '') ?: null;
    $codigoEquipamento = (int)($_POST['codigoEquipamento'] ?? 0);
    $codigoFornecedor  = (int)($_POST['codigoFornecedor'] ?? 0) ?: null;

    // --- VALIDAÇÕES DE REGRAS DE NEGÓCIO NO SERVIDOR ---
    if (strlen($nome) < 2)
        $erros[] = 'O nome do documento é obrigatório e deve ter pelo menos 2 caracteres.';
    
    // Impede a inserção de valores adulterados fora do Enum definido
    if (!in_array($tipo, $tiposValidos))
        $erros[] = 'Selecione um tipo de documento válido.';
        
    if (empty($dataDocumento))
        $erros[] = 'A data do documento é obrigatória.';
        
    if (!$codigoEquipamento)
        $erros[] = 'Selecione o equipamento associado.';
        
    // Validação lógica de consistência temporal das datas
    if ($dataValidade !== null && !empty($dataDocumento) && $dataValidade < $dataDocumento)
        $erros[] = 'A data de validade não pode ser anterior à data do documento.';

    // Se passou todas as validações, avança para a atualização na base de dados
    if (empty($erros)) {
        try {
            $stmt = $ligacao->prepare(
                "UPDATE Documentacao SET nome = :nome, tipo = :tipo, dataDocumento = :dataDocumento,
                 dataValidade = :dataValidade, ficheiro = :ficheiro,
                 codigoEquipamento = :codigoEquipamento, codigoFornecedor = :codigoFornecedor
                 WHERE codigo = :id"
            );
            $stmt->execute([
                ':nome'              => $nome,
                ':tipo'              => $tipo,
                ':dataDocumento'     => $dataDocumento,
                ':dataValidade'      => $dataValidade,
                ':ficheiro'          => $ficheiro,
                ':codigoEquipamento' => $codigoEquipamento,
                ':codigoFornecedor'  => $codigoFornecedor,
                ':id'                => $id,
            ]);
            
            // Termina a ligação antes de efetuar o redirecionamento
            $ligacao = null;
            
            // Regista a atividade técnica na tabela de auditoria
            registar_log('documento_editado', 'Documento editado: #' . $id);
            
            // Define a mensagem flash de sucesso que será lida na página seguinte
            $_SESSION['toast'] = ['tipo' => 'success', 'mensagem' => 'Documento atualizado com sucesso.'];
            header('Location: lista.php');
            exit;
        } catch (PDOException $err2) {
            $erros[] = 'Erro ao atualizar o documento. Por favor tente novamente.';
        }
    }
}
// Garante o fecho da ligação no cenário de erro ou quando o método é GET
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
                    <i class="fa-solid fa-pen-to-square"></i> Editar Registo de Documento
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
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Nome do Documento <span class="text-danger">*</span></label>
                        <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($_POST['nome'] ?? $doc->nome) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tipo de Documento <span class="text-danger">*</span></label>
                        <select name="tipo" class="form-select" required>
                            <?php $tipoAtual = $_POST['tipo'] ?? $doc->tipo;
                            foreach ($tiposLabel as $val => $label): ?>
                            <option value="<?= $val ?>" <?= $tipoAtual === $val ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Data do Documento <span class="text-danger">*</span></label>
                        <input type="date" name="dataDocumento" class="form-control" value="<?= htmlspecialchars($_POST['dataDocumento'] ?? $doc->dataDocumento) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Data de Validade <span class="text-muted small">(opcional)</span></label>
                        <input type="date" name="dataValidade" class="form-control" value="<?= htmlspecialchars($_POST['dataValidade'] ?? $doc->dataValidade ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Equipamento Associado <span class="text-danger">*</span></label>
                        <select name="codigoEquipamento" class="form-select" required>
                            <?php $eqAtual = $_POST['codigoEquipamento'] ?? $doc->codigoEquipamento;
                            foreach ($equipamentos as $eq): ?>
                            <option value="<?= $eq->codigo ?>" <?= $eqAtual == $eq->codigo ? 'selected' : '' ?>>
                                <?= htmlspecialchars($eq->codigoInterno . ' — ' . $eq->designacao) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Fornecedor Associado <span class="text-muted small">(opcional)</span></label>
                        <select name="codigoFornecedor" class="form-select">
                            <option value="" <?= empty($_POST['codigoFornecedor'] ?? $doc->codigoFornecedor) ? 'selected' : '' ?>>— Nenhum —</option>
                            <?php $fornAtual = $_POST['codigoFornecedor'] ?? $doc->codigoFornecedor;
                            foreach ($fornecedores as $forn): ?>
                            <option value="<?= $forn->codigo ?>" <?= $fornAtual == $forn->codigo ? 'selected' : '' ?>>
                                <?= htmlspecialchars($forn->nome) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Nome / Caminho do Ficheiro <span class="text-muted small">(opcional)</span></label>
                        <input type="text" name="ficheiro" class="form-control" value="<?= htmlspecialchars($_POST['ficheiro'] ?? $doc->ficheiro ?? '') ?>">
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