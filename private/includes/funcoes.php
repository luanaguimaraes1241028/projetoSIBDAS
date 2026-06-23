<?php
require_once __DIR__ . '/../../config/config.php';

function ligar_bd()
{
    try {
        $ligacao = new PDO(
            "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
            MYSQL_USERNAME,
            MYSQL_PASSWORD
        );
        $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $ligacao;
    } catch (PDOException) {
        return null;
    }
}

function start_session()
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
}

function check_session()
{
    return isset($_SESSION['utilizador']);
}

function redirect_if_not_logged()
{
    start_session();
    if (!check_session()) {
        header("Location: " . BASE_URL . "/public/login.php");
        exit;
    }
}

function redirect_if_not_admin()
{
    redirect_if_not_logged();
    if (($_SESSION['perfil'] ?? '') !== 'admin') {
        header("Location: " . BASE_URL . "/private/dashboard/dashboard.php");
        exit;
    }
}

function redirect_if_readonly()
{
    redirect_if_not_logged();
    if (($_SESSION['perfil'] ?? '') === 'profissional de saude') {
        header("Location: " . BASE_URL . "/private/dashboard/dashboard.php");
        exit;
    }
}

function aes_encrypt($value) {
    return bin2hex(openssl_encrypt(
        $value,
        OPENSSL_METHOD,
        OPENSSL_KEY,
        OPENSSL_RAW_DATA,
        OPENSSL_IV
    ));
}

function aes_decrypt($value) {
    if (!is_string($value) || strlen($value) % 2 !== 0) return false;
    return openssl_decrypt(
        hex2bin($value),
        OPENSSL_METHOD,
        OPENSSL_KEY,
        OPENSSL_RAW_DATA,
        OPENSSL_IV
    );
}

function registar_log(string $tipo, string $descricao): void {
    start_session();
    $codigoUtilizador = $_SESSION['codigo_utilizador'] ?? null;
    try {
        $lig = ligar_bd();
        if ($lig) {
            $stmt = $lig->prepare(
                "INSERT INTO Log (tipo, descricao, codigoUtilizador) VALUES (:tipo, :descricao, :user)"
            );
            $stmt->execute([':tipo' => $tipo, ':descricao' => $descricao, ':user' => $codigoUtilizador]);
        }
    } catch (Exception $e) {}
}

function logout_and_redirect()
{
    start_session();
    session_unset();
    session_destroy();
    header("Location: " . BASE_URL . "/public/login.php");
    exit;
}
