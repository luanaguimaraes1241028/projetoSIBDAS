<?php
require_once '../includes/funcoes.php';
redirect_if_not_logged();

$ligacao = ligar_bd();
if (!$ligacao) { header('Location: lista.php'); exit; }

try {
    $rows = $ligacao->query(
        "SELECT nome, IFNULL(nif, '') AS nif, tipoFornecedor,
                IFNULL(pessoaContacto, '') AS pessoaContacto,
                IFNULL(telefone, '') AS telefone,
                IFNULL(telefonePessoa, '') AS telefonePessoa,
                IFNULL(email, '') AS email,
                IFNULL(morada, '') AS morada,
                IFNULL(website, '') AS website,
                IFNULL(observacoes, '') AS observacoes
         FROM Fornecedor
         WHERE ativo = 1
         ORDER BY nome"
    )->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    header('Location: lista.php');
    exit;
}
$ligacao = null;

registar_log('exportacao_csv', 'Exportação de fornecedores em CSV — ' . count($rows) . ' registos');

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="fornecedores_' . date('Ymd_His') . '.csv"');
header('Cache-Control: no-cache, no-store, must-revalidate');

$out = fopen('php://output', 'w');
fwrite($out, "\xEF\xBB\xBF"); // BOM UTF-8: necessário para o Excel abrir o CSV com caracteres portugueses corretamente

fputcsv($out, [
    'Nome', 'NIF', 'Tipo', 'Pessoa de Contacto', 'Telefone', 'Tel. Contacto',
    'Email', 'Morada', 'Website', 'Observações'
], ';');

foreach ($rows as $r) {
    fputcsv($out, [
        $r['nome'], $r['nif'], $r['tipoFornecedor'], $r['pessoaContacto'],
        $r['telefone'], $r['telefonePessoa'], $r['email'],
        $r['morada'], $r['website'], $r['observacoes']
    ], ';');
}

fclose($out);
exit;
