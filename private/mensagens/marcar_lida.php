<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: lista.php');
    exit;
}

$id = (int)($_POST['id'] ?? 0);
if (!$id) {
    header('Location: lista.php');
    exit;
}

$ligacao = ligar_bd();
if ($ligacao) {
    $stmt = $ligacao->prepare("UPDATE MensagemContacto SET lida = 1 WHERE codigo = :id");
    $stmt->execute([':id' => $id]);
    $ligacao = null;
}

header('Location: lista.php?lida=1');
exit;
