<?php
// Inclui o ficheiro core com as funções globais (ligação à BD, criptografia, etc.)
require_once '../includes/funcoes.php';

// Segurança: impede o acesso se o utilizador não tiver sessão iniciada
redirect_if_not_logged();

// Inicia a conexão à base de dados. Se falhar, aborta e volta à listagem
$ligacao = ligar_bd();
if (!$ligacao) { header('Location: lista.php'); exit; }

try {
    // Procura todos os documentos ativos. Faz um INNER JOIN com os Equipamentos (obrigatório) 
    // e um LEFT JOIN com os Fornecedores (opcional, traz vazio se não houver correspondência).
    // O IFNULL no SQL ajuda a limpar os valores nulos para strings vazias antes de irem para o CSV.
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
    )->fetchAll(PDO::FETCH_ASSOC); // FETCH_ASSOC devolve um array associativo limpo (útil para fputcsv)
} catch (PDOException $e) {
    header('Location: lista.php');
    exit;
}
// Fecha a conexão com a base de dados
$ligacao = null;

// Escreve uma entrada no histórico de logs de auditoria da instituição
registar_log('exportacao_csv', 'Exportação de documentação em CSV — ' . count($rows) . ' registos');

// --- CONFIGURAÇÃO DOS CABEÇALHOS HTTP PARA DOWNLOAD DIRETO ---

// Avisa o browser que o conteúdo gerado não é uma página HTML, mas sim um ficheiro de texto CSV em UTF-8
header('Content-Type: text/csv; charset=utf-8');

// Força o browser a abrir a janela de download com um nome de ficheiro dinâmico baseado na data/hora atual
header('Content-Disposition: attachment; filename="documentacao_' . date('Ymd_His') . '.csv"');

// Medida de segurança: limpa e proíbe o browser de guardar este ficheiro em cache (dados sempre frescos)
header('Cache-Control: no-cache, no-store, must-revalidate');

// --- CONSTRUÇÃO DO BUFFER DE SAÍDA DO FICHEIRO ---

// Abre um ponteiro de escrita apontado diretamente para a corrente de saída do sistema (php://output)
// Tudo o que for escrito aqui vai diretamente para o download do utilizador em vez de renderizar no ecrã
$out = fopen('php://output', 'w');

// Truque de compatibilidade (BOM UTF-8): Força o Microsoft Excel a reconhecer os caracteres portugueses 
// com acentos (como ç, á, õ) corretamente assim que o utilizador abre o ficheiro por clique duplo
fwrite($out, "\xEF\xBB\xBF");

// Escreve a primeira linha do CSV correspondente aos cabeçalhos das colunas
// O caractere ';' é usado explicitamente como delimitador por ser o padrão europeu esperado pelo Excel
fputcsv($out, [
    'Nome', 'Tipo', 'Data do Documento', 'Data de Validade',
    'Cód. Equipamento', 'Designação Equipamento', 'Fornecedor', 'Ficheiro'
], ';');

// Percorre a lista de registos recolhidos da BD e injeta cada linha sequencialmente no buffer
foreach ($rows as $r) {
    fputcsv($out, [
        $r['nome'], $r['tipo'], $r['dataDocumento'], $r['dataValidade'],
        $r['codigoInterno'], $r['designacao'], $r['nomeFornecedor'], $r['ficheiro']
    ], ';');
}

// Fecha o ponteiro do ficheiro e limpa o buffer do servidor
fclose($out);

// Interrompe imediatamente o script para garantir que o PHP não injeta nenhum espaço ou caractere extra no fim do CSV
exit;