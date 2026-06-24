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
    // soft delete: não elimina o registo — ativo=0 arquiva o fornecedor, preservando o histórico e FKs
    $stmt = $ligacao->prepare("UPDATE Fornecedor SET ativo = 0 WHERE codigo = :id");
    $stmt->execute([':id' => $id]);
    registar_log('fornecedor_arquivado', 'Fornecedor arquivado: #' . $id);
    $_SESSION['toast'] = ['tipo' => 'warning', 'mensagem' => 'Fornecedor arquivado com sucesso.'];
} catch (PDOException $err) {
    $_SESSION['toast'] = ['tipo' => 'danger', 'mensagem' => 'Erro ao arquivar o fornecedor.'];
}
$ligacao = null;
header('Location: lista.php');
exit;
