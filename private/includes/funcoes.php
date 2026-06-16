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

function logout_and_redirect()
{
    start_session();
    session_unset();
    session_destroy();
    header("Location: " . BASE_URL . "/public/login.php");
    exit;
}
