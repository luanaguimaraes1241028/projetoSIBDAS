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
    $stmt = $ligacao->prepare("DELETE FROM Documentacao WHERE codigo = :id");
    $stmt->execute([':id' => $id]);
} catch (PDOException $err) {
    header('Location: lista.php');
    exit;
}

$ligacao = null;
$_SESSION['toast'] = ['tipo' => 'warning', 'mensagem' => 'Documento eliminado com sucesso.'];
header('Location: lista.php');
exit;
