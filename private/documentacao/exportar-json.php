<?php
require_once '../includes/funcoes.php';
redirect_if_not_logged();

$ligacao = ligar_bd();
if (!$ligacao) { header('Location: lista.php'); exit; }

try {
    $rows = $ligacao->query(
        "SELECT d.nome, d.tipo, d.dataDocumento AS data_documento,
                d.dataValidade AS data_validade,
                e.codigoInterno AS codigo_interno_equipamento,
                e.designacao AS designacao_equipamento,
                f.nome AS fornecedor
         FROM Documentacao d
         JOIN Equipamento e ON d.codigoEquipamento = e.codigo
         LEFT JOIN Fornecedor f ON d.codigoFornecedor = f.codigo
         WHERE d.ativo = 1
         ORDER BY d.dataDocumento DESC"
    )->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    header('Location: lista.php');
    exit;
}
$ligacao = null;

registar_log('exportacao_json', 'Exportação de documentação em JSON — ' . count($rows) . ' registos');

$payload = [
    'sistema'       => 'MedCore — Inventário Hospitalar',
    'exportado_em'  => date('Y-m-d H:i:s'),
    'total'         => count($rows),
    'documentacao'  => $rows,
];

header('Content-Type: application/json; charset=utf-8');
header('Content-Disposition: attachment; filename="documentacao_' . date('Ymd_His') . '.json"');
header('Cache-Control: no-cache, no-store, must-revalidate');

echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
exit;
