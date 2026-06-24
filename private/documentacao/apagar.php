<?php
// Inclui o ficheiro global com as funções de segurança, criptografia e base de dados
require_once __DIR__ . '/../includes/funcoes.php';

// Guardrail de segurança: garante que apenas utilizadores com perfil 'admin' acedem a esta operação
redirect_if_not_admin();

// Captura o ID cifrado enviado pelo método GET (através do link da página anterior)
$id_cifrado = $_GET['id'] ?? '';

// Desencripta o ID usando a função AES-256-CBC configurada globalmente no sistema
$id = aes_decrypt($id_cifrado);

// Validação estrita de segurança: se a desencriptação falhar ou o ID não for um número inteiro puro,
// redireciona imediatamente para a listagem para evitar ataques de injeção ou manipulação de URLs
if ($id === false || !ctype_digit((string) $id)) {
    header('Location: lista.php');
    exit;
}

// Estabelece a ligação à base de dados. Se falhar, aborta e redireciona de forma segura
$ligacao = ligar_bd();
if (!$ligacao) { header('Location: lista.php'); exit; }

$doc = null;
try {
    // Prepara a instrução SQL com um "Prepared Statement" (:id) para mitigar SQL Injections.
    // Faz um INNER JOIN para trazer também os dados do equipamento ao qual este documento pertence.
    $stmt = $ligacao->prepare(
        "SELECT d.*, e.codigoInterno, e.designacao FROM Documentacao d
         JOIN Equipamento e ON d.codigoEquipamento = e.codigo
         WHERE d.codigo = :id"
    );
    // Executa a query injetando com total segurança o ID numérico já validado
    $stmt->execute([':id' => $id]);
    
    // Recolhe o resultado como um objeto anónimo
    $doc = $stmt->fetch(PDO::FETCH_OBJ);
    
    // Se o documento não existir na BD (ex: ID inventado), força a saída da página
    if (!$doc) { header('Location: lista.php'); exit; }
    
} catch (PDOException $err) {
    // Em caso de erro técnico na base de dados, falha de forma silenciosa para o utilizador e redireciona
    header('Location: lista.php');
    exit;
}
// Fecha a ligação ao servidor MySQL para poupar recursos do sistema
$ligacao = null;

// Array de tradução para converter as chaves guardadas na BD em texto limpo e legível na interface
$tiposLabel = [
    'manual de utilizador'        => 'Manual de Utilizador',
    'manual de servico'           => 'Manual de Serviço',
    'certificado de calibracao'   => 'Certificado de Calibração',
    'contrato de manutencao'      => 'Contrato de Manutenção',
    'fatura ou guia de aquisicao' => 'Fatura ou Guia de Aquisição',
    'declaracao de conformidade'  => 'Declaração de Conformidade',
    'relatorio tecnico'           => 'Relatório Técnico',
];
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/nav.php'; ?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/sidebar.php'; ?>
        <main class="col-md-9 col-lg-10 px-md-4 pt-5">
            <div class="card shadow-sm border border-warning text-center mx-auto p-5 my-5" style="max-width: 580px;">
                <h1 class="display-4 text-warning mb-3"><i class="fa-solid fa-triangle-exclamation"></i></h1>
                <p class="fs-5 text-muted">Deseja arquivar este registo de documentação?</p>
                
                <h3 class="fw-bold mb-2" style="color: #1e1b4b;"><?= htmlspecialchars($doc->nome) ?></h3>
                
                <div class="bg-light rounded p-3 mb-4 border text-start small">
                    <div class="mb-2">
                        <strong>Tipo:</strong> <?= htmlspecialchars($tiposLabel[$doc->tipo] ?? $doc->tipo) ?>
                    </div>
                    <div class="mb-2">
                        <strong>Equipamento:</strong>
                        <span class="badge bg-dark font-monospace me-1"><?= htmlspecialchars($doc->codigoInterno) ?></span>
                        <?= htmlspecialchars($doc->designacao) ?>
                    </div>
                    
                    <?php if ($doc->ficheiro): ?>
                    <div><strong>Ficheiro:</strong> <code><?= htmlspecialchars($doc->ficheiro) ?></code></div>
                    <?php endif; ?>
                </div>
                
                <form method="post" action="confirmar_apagar.php">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($id_cifrado) ?>">
                    
                    <div class="d-flex justify-content-center gap-3">
                        <a href="lista.php" class="btn btn-outline-secondary px-4 fw-bold">
                            <i class="fa-solid fa-xmark me-2"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-danger px-4 fw-bold shadow-sm">
                            <i class="fa-solid fa-box-archive me-2"></i> Sim, Arquivar
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>
<?php include '../includes/footer.php'; ?>