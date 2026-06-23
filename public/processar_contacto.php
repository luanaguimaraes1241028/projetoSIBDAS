<?php
require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$nome     = trim($_POST['nome']     ?? '');
$email    = trim($_POST['email']    ?? '');
$mensagem = trim($_POST['mensagem'] ?? '');

if (!$nome || !$email || !$mensagem || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: index.php?erro=1#contacto');
    exit;
}

try {
    $pdo = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
        MYSQL_USERNAME, MYSQL_PASSWORD,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    $stmt = $pdo->prepare(
        "INSERT INTO MensagemContacto (nome, email, mensagem) VALUES (:nome, :email, :mensagem)"
    );
    $stmt->execute([':nome' => $nome, ':email' => $email, ':mensagem' => $mensagem]);
    $pdo = null;
    header('Location: index.php?enviado=1#contacto');
} catch (Exception $e) {
    header('Location: index.php?erro=1#contacto');
}
exit;
