<?php
require_once '../includes/funcoes.php';
redirect_if_not_logged();

$ligacao = ligar_bd();
if (!$ligacao) { header('Location: lista.php'); exit; }

try {
    $rows = $ligacao->query(
        "SELECT e.codigoInterno, e.designacao, e.marca, e.modelo, e.numeroSerie,
                IFNULL(e.fabricante, '') AS fabricante,
                IFNULL(c.nome, '') AS categoria,
                IFNULL(l.edificio, '') AS edificio,
                IFNULL(l.piso, '') AS piso,
                IFNULL(l.servico, '') AS servico,
                IFNULL(l.sala, '') AS sala,
                e.estado, e.criticidade,
                IFNULL(e.dataAquisicao, '') AS dataAquisicao,
                IFNULL(e.anoFabrico, '') AS anoFabrico,
                IFNULL(e.custoAquisicao, '') AS custoAquisicao,
                IFNULL(e.tipoEntrada, '') AS tipoEntrada,
                IFNULL(e.observacoes, '') AS observacoes
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

registar_log('exportacao_csv', 'Exportação de equipamentos em CSV — ' . count($rows) . ' registos');

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="equipamentos_' . date('Ymd_His') . '.csv"');
header('Cache-Control: no-cache, no-store, must-revalidate');

$out = fopen('php://output', 'w'); // php://output escreve diretamente para o buffer sem criar ficheiro temporário
fwrite($out, "\xEF\xBB\xBF"); // BOM UTF-8: necessário para o Excel abrir o CSV com caracteres portugueses corretamente

// ';' em vez de ',' porque o Excel europeu usa ponto e vírgula por padrão
fputcsv($out, [
    'Código Interno', 'Designação', 'Marca', 'Modelo', 'Nº de Série', 'Fabricante',
    'Categoria', 'Edifício', 'Piso', 'Serviço', 'Sala',
    'Estado', 'Criticidade', 'Data Aquisição', 'Ano Fabrico',
    'Custo Aquisição (€)', 'Tipo de Entrada', 'Observações'
], ';');

foreach ($rows as $r) {
    fputcsv($out, [
        $r['codigoInterno'], $r['designacao'], $r['marca'], $r['modelo'],
        $r['numeroSerie'], $r['fabricante'], $r['categoria'],
        $r['edificio'], $r['piso'], $r['servico'], $r['sala'],
        $r['estado'], $r['criticidade'], $r['dataAquisicao'],
        $r['anoFabrico'], $r['custoAquisicao'],
        $r['tipoEntrada'], $r['observacoes'],
    ], ';');
}

fclose($out);
exit;
