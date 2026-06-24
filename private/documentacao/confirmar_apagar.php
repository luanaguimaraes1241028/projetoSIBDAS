<?php
// Inclui o ficheiro global com as funções core de segurança e base de dados
require_once __DIR__ . '/../includes/funcoes.php';

// Guardrail de segurança: barra o acesso imediato caso o perfil não seja 'admin'
redirect_if_not_admin();

// Validação do método HTTP: garante que esta página só aceita requisições via POST (submissões de formulários).
// Se alguém tentar aceder ao ficheiro diretamente pelo link (GET), é imediatamente corrido o redirecionamento.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: lista.php');
    exit;
}

// Captura o ID cifrado enviado de forma oculta pelo formulário POST
$id_cifrado = $_POST['id'] ?? '';

// Desencripta o ID utilizando o método AES-256-CBC do sistema
$id = aes_decrypt($id_cifrado);

// Validação estrita do ID: se a chave foi adulterada (false) ou não for um número inteiro positivo,
// aborta o processo e reencaminha o utilizador para a lista de segurança
if ($id === false || !ctype_digit((string) $id)) {
    header('Location: lista.php');
    exit;
}

// Abre a ligação à base de dados. Se falhar, interrompe a execução e sai de forma limpa
$ligacao = ligar_bd();
if (!$ligacao) { header('Location: lista.php'); exit; }

try {
    // Execução do "Soft Delete": Em vez de usar um comando DELETE real (que apagaria o registo para sempre),
    // alteramos o campo "ativo" para 0. Isto esconde o manual/certificado do site, mantendo o histórico na BD.
    $stmt = $ligacao->prepare("UPDATE Documentacao SET ativo = 0 WHERE codigo = :id");
    $stmt->execute([':id' => $id]);
    
    // Escreve uma linha na tabela de auditoria (Log) para registar que o admin arquivou este ID específico
    registar_log('documento_arquivado', 'Documento arquivado: #' . $id);
    
    // Aloca uma mensagem temporária na sessão (Toast) para que a página seguinte mostre um aviso de sucesso amarelo (warning)
    $_SESSION['toast'] = ['tipo' => 'warning', 'mensagem' => 'Documento arquivado com sucesso.'];

} catch (PDOException $err) {
    // Tratamento de exceções: se a query falhar (ex: quebra de ligação), cria um alerta vermelho (danger) na sessão
    $_SESSION['toast'] = ['tipo' => 'danger', 'mensagem' => 'Erro ao arquivar o documento.'];
}

// Corta a ligação ao servidor MySQL para libertar memória
$ligacao = null;

// Redireciona o utilizador de volta para a tabela principal, onde o Toast configurado acima será exibido
header('Location: lista.php');
exit;