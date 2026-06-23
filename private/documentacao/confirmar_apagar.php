<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: lista.php');
    exit;
}

$id_cifrado = $_POST['id'] ?? '';
$id = aes_decrypt($id_cifrado);
if ($id === false || !ctype_digit((string) $id)) {
    header('Location: lista.php');
    exit;
}

$ligacao = ligar_bd();
if (!$ligacao) { header('Location: lista.php'); exit; }

try {
    $stmt = $ligacao->prepare("UPDATE Documentacao SET ativo = 0 WHERE codigo = :id");
    $stmt->execute([':id' => $id]);
    $_SESSION['toast'] = ['tipo' => 'warning', 'mensagem' => 'Documento arquivado com sucesso.'];
} catch (PDOException $err) {
    $_SESSION['toast'] = ['tipo' => 'danger', 'mensagem' => 'Erro ao arquivar o documento.'];
}
$ligacao = null;
header('Location: lista.php');
exit;
