<?php require_once __DIR__ . '/../includes/funcoes.php';
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
if (!$ligacao) {
    $_SESSION['toast'] = ['tipo' => 'danger', 'mensagem' => 'Erro na ligação à base de dados.'];
    header('Location: lista.php');
    exit;
}

try {
    // soft delete: ativo=0 arquiva a localização sem a eliminar — equipamentos já associados mantêm a referência
    $stmt = $ligacao->prepare("UPDATE Localizacao SET ativo = 0 WHERE codigo = :id");
    $stmt->execute([':id' => $id]);
    registar_log('localizacao_arquivada', 'Localização arquivada: #' . $id);
    $_SESSION['toast'] = ['tipo' => 'warning', 'mensagem' => 'Localização arquivada com sucesso.'];
} catch (PDOException $e) {
    $_SESSION['toast'] = ['tipo' => 'danger', 'mensagem' => 'Erro ao arquivar a localização.'];
}
$ligacao = null;
header('Location: lista.php');
exit;
