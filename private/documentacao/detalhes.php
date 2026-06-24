<?php 
// Inclui as funções globais e utilitários (criptografia, funções de BD, etc.)
require_once __DIR__ . '/../includes/funcoes.php';

// Segurança: impede o acesso se o utilizador não tiver uma sessão ativa no browser
redirect_if_not_logged();

// Captura o ID encriptado recebido via parâmetro GET na URL (?id=...)
$id_cifrado = $_GET['id'] ?? '';

// Desencripta o ID através do algoritmo AES-256-CBC do sistema
$id = aes_decrypt($id_cifrado);

// Validação rigorosa: se a chave falhar ou o ID resultante não contiver apenas dígitos numéricos,
// aborta imediatamente a execução e redireciona para a lista geral
if ($id === false || !ctype_digit((string) $id)) {
    header('Location: lista.php');
    exit;
}

// Inicializa as variáveis de controle do estado da página
$ligacao = ligar_bd();
$doc = null;
$erro = '';

if (!$ligacao) {
    // Caso a função de conexão falhe, armazena a mensagem de erro para o utilizador
    $erro = "Erro na ligação à base de dados.";
} else {
    try {
        // Prepara a instrução SQL unindo três tabelas (Documentação, Equipamento e Fornecedor).
        // O LEFT JOIN no Fornecedor assegura que o documento é exibido mesmo que não tenha fornecedor associado.
        $stmt = $ligacao->prepare(
            "SELECT d.*, e.codigoInterno, e.designacao, e.marca, f.nome AS nomeFornecedor
             FROM Documentacao d
             JOIN Equipamento e ON d.codigoEquipamento = e.codigo
             LEFT JOIN Fornecedor f ON d.codigoFornecedor = f.codigo
             WHERE d.codigo = :id AND d.ativo = 1"
        );
        
        // Executa a consulta associando de forma segura o ID numérico decifrado
        $stmt->execute([':id' => $id]);
        
        // Recolhe o resultado como um objeto de propriedades dinâmicas (PDO::FETCH_OBJ)
        $doc = $stmt->fetch(PDO::FETCH_OBJ);
        
        // Se a consulta não retornar linhas (ex: documento inativo ou ID inexistente), redireciona por segurança
        if (!$doc) { header('Location: lista.php'); exit; }
        
    } catch (PDOException $err) {
        // Captura exceções do PDO e define uma mensagem amigável para mitigar quebras de ecrã
        $erro = "Erro ao carregar dados.";
    }
    // Fecha o canal de comunicação com o MySQL
    $ligacao = null;
}

// Dicionário local para fazer o mapeamento do termo técnico da BD para o texto limpo da interface
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
        <main class="col-md-9 col-lg-10 px-md-4 pt-4">
            
            <?php if ($erro): ?>
                <div class="alert alert-danger m-4"><?= htmlspecialchars($erro) ?></div>
            <?php elseif ($doc): ?>
            <?php
                // Lógica de Negócio: Compara a data de validade com o dia de hoje (gerado dinamicamente pelo servidor)
                $expirado  = $doc->dataValidade && $doc->dataValidade < date('Y-m-d');
                
                // Fallback: se o tipo da BD não existir no dicionário, imprime o texto cru da BD
                $labelTipo = $tiposLabel[$doc->tipo] ?? $doc->tipo;
            ?>
            <div class="card shadow-sm border p-4 mx-auto my-4" style="max-width: 800px;">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                    <h2 class="fw-bold mb-0" style="color: #1e1b4b;">
                        <i class="fa-solid fa-file-lines me-2"></i><?= htmlspecialchars($doc->nome) ?>
                    </h2>
                    <span class="badge bg-primary px-3 py-2"><?= htmlspecialchars($labelTipo) ?></span>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-6 border-bottom pb-2">
                        <label class="text-muted small d-block">Data do Documento</label>
                        <span class="fw-bold"><?= date('d/m/Y', strtotime($doc->dataDocumento)) ?></span>
                    </div>
                    <div class="col-md-6 border-bottom pb-2">
                        <label class="text-muted small d-block">Data de Validade</label>
                        <?php if ($doc->dataValidade): ?>
                            <span class="fw-bold <?= $expirado ? 'text-danger' : '' ?>">
                                <?= date('d/m/Y', strtotime($doc->dataValidade)) ?>
                                <?php if ($expirado): ?><span class="badge bg-danger ms-2">Expirado</span><?php endif; ?>
                            </span>
                        <?php else: ?>
                            <span class="text-muted">Não aplicável</span>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6 border-bottom pb-2">
                        <label class="text-muted small d-block">Equipamento Associado</label>
                        <span class="badge bg-dark font-monospace me-1"><?= htmlspecialchars($doc->codigoInterno) ?></span>
                        <span class="fw-bold"><?= htmlspecialchars($doc->designacao) ?></span>
                        <small class="text-muted ms-1"><?= htmlspecialchars($doc->marca) ?></small>
                    </div>
                    <div class="col-md-6 border-bottom pb-2">
                        <label class="text-muted small d-block">Fornecedor Associado</label>
                        <span><?= $doc->nomeFornecedor ? htmlspecialchars($doc->nomeFornecedor) : '—' ?></span>
                    </div>
                    
                    <?php if ($doc->ficheiro): ?>
                    <div class="col-12 border-bottom pb-2">
                        <label class="text-muted small d-block">Ficheiro</label>
                        <div class="p-2 bg-light rounded border text-primary small">
                            <i class="fa-regular fa-file-pdf text-danger me-1"></i>
                            <code><?= htmlspecialchars($doc->ficheiro) ?></code>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="d-flex gap-2">
                    <a href="lista.php" class="btn btn-secondary px-4">
                        <i class="fa-solid fa-arrow-left"></i> Voltar
                    </a>
                    
                    <?php if (($_SESSION['perfil'] ?? '') !== 'profissional de saude'): ?>
                    <a href="editar.php?id=<?= urlencode($id_cifrado) ?>" class="btn text-white px-4" style="background-color: #1e1b4b;">
                        <i class="fa-solid fa-pen-to-square"></i> Editar
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </main>
    </div>
</div>
<?php include '../includes/footer.php'; ?>