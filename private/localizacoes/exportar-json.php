<?php
require_once '../includes/funcoes.php';
redirect_if_not_logged();

$ligacao = ligar_bd();
if (!$ligacao) { header('Location: lista.php'); exit; }

try {
    $rows = $ligacao->query(
        "SELECT l.edificio, l.piso, l.servico, l.sala,
                COUNT(e.codigo) AS total_equipamentos
         FROM Localizacao l
         LEFT JOIN Equipamento e ON e.codigoLocalizacao = l.codigo
         WHERE l.ativo = 1
         GROUP BY l.codigo
         ORDER BY l.edificio, l.piso, l.servico"
    )->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    header('Location: lista.php');
    exit;
}
$ligacao = null;

registar_log('exportacao_json', 'Exportação de localizações em JSON — ' . count($rows) . ' registos');

$payload = [
    'sistema'       => 'MedCore — Inventário Hospitalar',
    'exportado_em'  => date('Y-m-d H:i:s'),
    'total'         => count($rows),
    'localizacoes'  => $rows,
];

header('Content-Type: application/json; charset=utf-8');
header('Content-Disposition: attachment; filename="localizacoes_' . date('Ymd_His') . '.json"');
header('Cache-Control: no-cache, no-store, must-revalidate');

echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
exit;
