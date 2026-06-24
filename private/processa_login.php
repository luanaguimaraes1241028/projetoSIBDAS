<?php
require_once 'includes/funcoes.php';
start_session();

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: ../public/login.php', true, 303);
    return;
}

$username = isset($_POST['text_username']) ? $_POST['text_username'] : '';
$password = isset($_POST['text_password']) ? $_POST['text_password'] : '';

$validation_errors = [];

if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
    $validation_errors[] = 'O username tem que ser um email válido.';
}

if (strlen($username) < 5 || strlen($username) > 50) {
    $validation_errors[] = 'O username deve ter entre 5 e 50 caracteres.';
}

if (strlen($password) < 6 || strlen($password) > 12) {
    $validation_errors[] = 'A password deve ter entre 6 e 12 caracteres.';
}

if (!empty($validation_errors)) {
    $_SESSION['validation_errors'] = $validation_errors;
    header('Location: ../public/login.php', true, 303);
    return;
}

$ligacao = ligar_bd();
if (!$ligacao) {
    $_SESSION['server_error'] = 'Erro ao ligar à base de dados.';
    header('Location: ../public/login.php', true, 303);
    exit;
}

$stmt = $ligacao->prepare("SELECT codigo, password, perfil FROM Utilizador WHERE email = :email LIMIT 1");
$stmt->execute([':email' => $username]);
$utilizador = $stmt->fetch(PDO::FETCH_OBJ);
$ligacao = null;

if (!$utilizador || !password_verify($password, $utilizador->password)) {
    registar_log('login_falha', 'Tentativa falhada: ' . $username);
    $_SESSION['server_error'] = 'Login inválido';
    header('Location: ../public/login.php', true, 303);
    exit;
}

$_SESSION['utilizador']        = $username;
$_SESSION['perfil']            = $utilizador->perfil;
$_SESSION['codigo_utilizador'] = $utilizador->codigo;
registar_log('login_sucesso', 'Sessão iniciada: ' . $username);
header('Location: dashboard/dashboard.php');
exit;
