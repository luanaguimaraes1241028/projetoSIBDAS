<?php
// Inclui o ficheiro core com as funções globais do ecossistema MedCore
require_once '../includes/funcoes.php';

// Medida de segurança: barra o acesso direto à página se o utilizador não tiver uma sessão ativa
redirect_if_not_logged();

// Inicia o canal de ligação à base de dados. Se falhar, aborta e regressa à lista principal
$ligacao = ligar_bd();
if (!$ligacao) { header('Location: lista.php'); exit; }

try {
    // Query 1: Extrai todos os documentos ativos ordenados de forma cronológica decrescente.
    // Une a tabela de Equipamentos (INNER JOIN) e Fornecedores (LEFT JOIN para manter registos sem fornecedor).
    $rows = $ligacao->query(
        "SELECT d.nome, d.tipo, d.dataDocumento, d.dataValidade,
                e.codigoInterno, e.designacao,
                IFNULL(f.nome, '—') AS nomeFornecedor
         FROM Documentacao d
         JOIN Equipamento e ON d.codigoEquipamento = e.codigo
         LEFT JOIN Fornecedor f ON d.codigoFornecedor = f.codigo
         WHERE d.ativo = 1
         ORDER BY d.dataDocumento DESC"
    )->fetchAll(PDO::FETCH_OBJ); // FETCH_OBJ converte as linhas em objetos PHP (acedidos com "->")

    // Query 2: Recolhe a contagem total de registos ativos.
    // O fetchColumn() extrai o escalar numérico direto, poupando a memória de criar um array associativo completo.
    $total = $ligacao->query("SELECT COUNT(*) FROM Documentacao WHERE ativo = 1")->fetchColumn();
    
    // Query 3: Filtra e conta quantos documentos ativos possuem data de validade preenchida e já expiraram.
    // A comparação temporal (< CURDATE()) é efetuada no motor MySQL, sendo muito mais eficiente do que carregar e avaliar os dados no PHP.
    $expirados = $ligacao->query("SELECT COUNT(*) FROM Documentacao WHERE ativo = 1 AND dataValidade IS NOT NULL AND dataValidade < CURDATE()")->fetchColumn();
} catch (PDOException $e) {
    // Interceção de falhas críticas na BD: redireciona o utilizador de forma segura para não expor erros do MySQL
    header('Location: lista.php');
    exit;
}
// Corta a ligação com a base de dados
$ligacao = null;

// Insere uma linha no histórico de auditoria do hospital registando o volume de dados exportado
registar_log('exportacao_pdf', 'Exportação de documentação em PDF — ' . count($rows) . ' registos');
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Documentação — MedCore</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 11px; color: #1a1a2e; background: #fff; }
        .page-header { background: #1e1b4b; color: #fff; padding: 20px 30px; display: flex; justify-content: space-between; align-items: center; }
        .page-header h1 { font-size: 18px; font-weight: 700; }
        .page-header .meta { font-size: 10px; text-align: right; opacity: 0.85; }
        .summary { display: flex; gap: 16px; padding: 16px 30px; background: #f8f9ff; border-bottom: 1px solid #e0e0f0; }
        .summary-box { background: #fff; border: 1px solid #e0e0f0; border-radius: 6px; padding: 10px 18px; text-align: center; flex: 1; }
        .summary-box .num { font-size: 22px; font-weight: 700; color: #1e1b4b; }
        .summary-box .lbl { font-size: 9px; text-transform: uppercase; color: #666; letter-spacing: 0.5px; }
        .table-wrap { padding: 16px 30px 30px; }
        .section-title { font-size: 13px; font-weight: 700; color: #1e1b4b; margin-bottom: 10px; padding-bottom: 6px; border-bottom: 2px solid #1e1b4b; }
        table { width: 100%; border-collapse: collapse; font-size: 10px; }
        thead tr { background: #1e1b4b; color: #fff; }
        thead th { padding: 8px 6px; text-align: left; font-weight: 600; font-size: 9px; text-transform: uppercase; letter-spacing: 0.4px; }
        tbody tr:nth-child(even) { background: #f8f8ff; }
        tbody td { padding: 6px; border-bottom: 1px solid #ececf5; vertical-align: middle; }
        .badge { display: inline-block; padding: 2px 7px; border-radius: 20px; font-size: 9px; font-weight: 600; color: #fff; background: #6c757d; }
        .expirado { color: #dc3545; font-weight: 700; }
        .print-btn { position: fixed; bottom: 24px; right: 24px; background: #1e1b4b; color: #fff; border: none; padding: 12px 22px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; box-shadow: 0 4px 12px rgba(0,0,0,0.2); }
        .print-btn:hover { background: #2d2a6e; }
        .footer-info { padding: 10px 30px; border-top: 1px solid #e0e0f0; font-size: 9px; color: #999; text-align: center; }
        @media print {
            .print-btn { display: none !important; }
            thead tr { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            tbody tr:nth-child(even) { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
        @page { margin: 1cm; }
    </style>
</head>
<body>
<div class="page-header">
    <div>
        <h1><img src="/sibdas/1241028/medcore/assets/img/logo medcore.png" alt="MedCore" style="height:36px;vertical-align:middle;margin-right:10px;">MedCore — Relatório de Documentação</h1>
        <div style="font-size:11px;opacity:.8;margin-top:4px;">Manuais, certificados e contratos associados a equipamentos</div>
    </div>
    <div class="meta">
        <div>Gerado em: <?= date('d/m/Y \à\s H:i') ?></div>
        <div>Total de registos: <?= count($rows) ?></div>
    </div>
</div>
<div class="summary">
    <div class="summary-box"><div class="num"><?= $total ?></div><div class="lbl">Documentos Ativos</div></div>
    
    <div class="summary-box"><div class="num" style="color:<?= $expirados > 0 ? '#dc3545' : '#198754' ?>;"><?= $expirados ?></div><div class="lbl">Expirados</div></div>
</div>
<div class="table-wrap">
    <div class="section-title">Lista de Documentação</div>
    <table>
        <thead>
            <tr>
                <th>Nome / Tipo</th>
                <th>Equipamento</th>
                <th>Data Documento</th>
                <th>Validade</th>
                <th>Fornecedor</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $doc):
                // Lógica de Validação: Compara em tempo real se a validade do documento é inferior ao dia de hoje
                $expirado = $doc->dataValidade && $doc->dataValidade < date('Y-m-d');
            ?>
            <tr>
                <td>
                    <strong><?= htmlspecialchars($doc->nome) ?></strong><br>
                    <span class="badge"><?= htmlspecialchars($doc->tipo) ?></span>
                </td>
                <td>
                    <span style="font-family:monospace;font-size:9px;background:#1e1b4b;color:#fff;padding:1px 5px;border-radius:3px;"><?= htmlspecialchars($doc->codigoInterno) ?></span>
                    <?= htmlspecialchars($doc->designacao) ?>
                </td>
                <td><?= date('d/m/Y', strtotime($doc->dataDocumento)) ?></td>
                
                <td class="<?= $expirado ? 'expirado' : '' ?>">
                    <?= $doc->dataValidade ? date('d/m/Y', strtotime($doc->dataValidade)) . ($expirado ? ' ⚠' : '') : '—' ?>
                </td>
                <td><?= htmlspecialchars($doc->nomeFornecedor) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<div class="footer-info">MedCore — Sistema de Gestão de Inventário Hospitalar &nbsp;|&nbsp; Relatório gerado em <?= date('d/m/Y H:i:s') ?></div>

<button class="print-btn" onclick="window.print()">Imprimir / Guardar como PDF</button>
</body>
</html>