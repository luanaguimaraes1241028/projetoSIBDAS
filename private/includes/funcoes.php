<?php

function start_session() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function check_session() {
    start_session();
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

function redirect_if_not_logged() {
    if (!check_session()) {
        header('Location: /projeto-sibdas/public/login.php');
        exit;
    }
}

function get_username() {
    start_session();
    return isset($_SESSION['utilizador']) ? htmlspecialchars($_SESSION['utilizador']) : 'Utilizador';
}

function logout_and_redirect() {
    start_session();
    session_destroy();
    header('Location: /projeto-sibdas/public/login.php');
    exit;
}
