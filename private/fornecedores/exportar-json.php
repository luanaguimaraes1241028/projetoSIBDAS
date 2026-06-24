<?php
require_once '../includes/funcoes.php';
redirect_if_not_logged();

$ligacao = ligar_bd();
if (!$ligacao) { header('Location: lista.php'); exit; }

try {
    $rows = $ligacao->query(
        "SELECT nome, nif, tipoFornecedor AS tipo,
                pessoaContacto AS pessoa_contacto,
                telefone, telefonePessoa AS telefone_pessoa,
                email, morada, website, observacoes
         FROM Fornecedor
         WHERE ativo = 1
         ORDER BY nome"
    )->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    header('Location: lista.php');
    exit;
}
$ligacao = null;

registar_log('exportacao_json', 'Exportação de fornecedores em JSON — ' . count($rows) . ' registos');

$payload = [
    'sistema'      => 'MedCore — Inventário Hospitalar',
    'exportado_em' => date('Y-m-d H:i:s'),
    'total'        => count($rows),
    'fornecedores' => $rows,
];

header('Content-Type: application/json; charset=utf-8');
header('Content-Disposition: attachment; filename="fornecedores_' . date('Ymd_His') . '.json"');
header('Cache-Control: no-cache, no-store, must-revalidate');

// JSON_PRETTY_PRINT: indenta o output; JSON_UNESCAPED_UNICODE: mantém "ã/ç/é" legíveis; JSON_UNESCAPED_SLASHES: não escapa "/"
echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
exit;
