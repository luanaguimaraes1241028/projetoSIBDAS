<?php
require_once '../includes/funcoes.php';
redirect_if_not_logged();

$ligacao = ligar_bd();
if (!$ligacao) { header('Location: lista.php'); exit; }

try {
    $rows = $ligacao->query(
        "SELECT l.edificio, IFNULL(l.piso, '') AS piso, l.servico,
                IFNULL(l.sala, '') AS sala,
                COUNT(e.codigo) AS totalEquipamentos
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

registar_log('exportacao_csv', 'Exportação de localizações em CSV — ' . count($rows) . ' registos');

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="localizacoes_' . date('Ymd_His') . '.csv"');
header('Cache-Control: no-cache, no-store, must-revalidate');

$out = fopen('php://output', 'w');
fwrite($out, "\xEF\xBB\xBF");

fputcsv($out, ['Edifício', 'Piso', 'Serviço / Departamento', 'Sala / Gabinete', 'Nº Equipamentos'], ';');

foreach ($rows as $r) {
    fputcsv($out, [
        $r['edificio'], $r['piso'], $r['servico'], $r['sala'], $r['totalEquipamentos']
    ], ';');
}

fclose($out);
exit;
