<?php
require_once '../includes/funcoes.php';
redirect_if_not_logged();

$ligacao = ligar_bd();
if (!$ligacao) { header('Location: lista.php'); exit; }

try {
    $rows = $ligacao->query(
        "SELECT d.nome, d.tipo, d.dataDocumento,
                IFNULL(d.dataValidade, '') AS dataValidade,
                e.codigoInterno, e.designacao,
                IFNULL(f.nome, '') AS nomeFornecedor,
                IFNULL(d.ficheiro, '') AS ficheiro
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

registar_log('exportacao_csv', 'Exportação de documentação em CSV — ' . count($rows) . ' registos');

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="documentacao_' . date('Ymd_His') . '.csv"');
header('Cache-Control: no-cache, no-store, must-revalidate');

$out = fopen('php://output', 'w');
fwrite($out, "\xEF\xBB\xBF"); // BOM UTF-8: necessário para o Excel abrir o CSV com caracteres portugueses corretamente

fputcsv($out, [
    'Nome', 'Tipo', 'Data do Documento', 'Data de Validade',
    'Cód. Equipamento', 'Designação Equipamento', 'Fornecedor', 'Ficheiro'
], ';');

foreach ($rows as $r) {
    fputcsv($out, [
        $r['nome'], $r['tipo'], $r['dataDocumento'], $r['dataValidade'],
        $r['codigoInterno'], $r['designacao'], $r['nomeFornecedor'], $r['ficheiro']
    ], ';');
}

fclose($out);
exit;
