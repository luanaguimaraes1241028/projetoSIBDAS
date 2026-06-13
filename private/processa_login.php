<?php
session_start();

$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

$erros = [];

if (empty($username)) {
    $erros[] = 'O campo utilizador é obrigatório.';
} elseif (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
    $erros[] = 'Introduza um endereço de email válido.';
}

if (empty($password)) {
    $erros[] = 'O campo palavra-passe é obrigatório.';
} elseif (strlen($password) < 6) {
    $erros[] = 'A palavra-passe deve ter pelo menos 6 caracteres.';
}

if (!empty($erros)) {
    $_SESSION['form_errors'] = $erros;
    header('Location: /projeto-sibdas/public/login.php');
    exit;
}

// Credenciais simuladas (substituir por BD no futuro)
$utilizadores = [
    'admin@medcore.pt'       => 'admin123',
    'engenheiro@medcore.pt'  => 'medcore2025',
];

if (isset($utilizadores[$username]) && $utilizadores[$username] === $password) {
    $_SESSION['utilizador'] = $username;
    $_SESSION['logged_in'] = true;
    header('Location: /projeto-sibdas/private/dashboard/dashboard.php');
    exit;
} else {
    $_SESSION['server_error'] = 'Email ou palavra-passe incorretos.';
    header('Location: /projeto-sibdas/public/login.php');
    exit;
}
