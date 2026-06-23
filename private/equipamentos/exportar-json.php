<?php
require_once '../includes/funcoes.php';
redirect_if_not_logged();

$ligacao = ligar_bd();
if (!$ligacao) { header('Location: lista.php'); exit; }

try {
    $rows = $ligacao->query(
        "SELECT e.codigoInterno AS codigo_interno,
                e.designacao, e.marca, e.modelo,
                e.numeroSerie AS numero_serie,
                e.fabricante,
                c.nome AS categoria,
                l.edificio, l.piso, l.servico, l.sala,
                e.estado, e.criticidade,
                e.dataAquisicao AS data_aquisicao,
                e.anoFabrico AS ano_fabrico,
                e.custoAquisicao AS custo_aquisicao,
                e.tipoEntrada AS tipo_entrada,
                e.observacoes
         FROM Equipamento e
         LEFT JOIN Categoria c ON c.codigo = e.codigoCategoria
         LEFT JOIN Localizacao l ON l.codigo = e.codigoLocalizacao
         ORDER BY e.codigoInterno"
    )->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    header('Location: lista.php');
    exit;
}
$ligacao = null;

registar_log('exportacao_json', 'Exportação de equipamentos em JSON — ' . count($rows) . ' registos');

$payload = [
    'sistema'      => 'MedCore — Inventário Hospitalar',
    'exportado_em' => date('Y-m-d H:i:s'),
    'total'        => count($rows),
    'equipamentos' => $rows,
];

header('Content-Type: application/json; charset=utf-8');
header('Content-Disposition: attachment; filename="equipamentos_' . date('Ymd_His') . '.json"');
header('Cache-Control: no-cache, no-store, must-revalidate');

echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
exit;
