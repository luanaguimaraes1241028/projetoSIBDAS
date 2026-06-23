<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: lista.php');
    exit;
}

$id_cifrado = $_POST['id_equipamento'] ?? '';
$id = aes_decrypt($id_cifrado);
if ($id === false || !ctype_digit((string) $id)) {
    header('Location: lista.php');
    exit;
}

try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $ligacao->prepare("UPDATE Equipamento SET estado = 'abatido' WHERE codigo = :id");
    $stmt->execute([':id' => $id]);
} catch (PDOException $err) {
    header('Location: lista.php');
    exit;
}

$ligacao = null;
registar_log('equipamento_desativado', 'Equipamento desativado: #' . $id);
$_SESSION['toast'] = ['tipo' => 'warning', 'mensagem' => 'Equipamento desativado com sucesso.'];
header('Location: lista.php');
exit;
