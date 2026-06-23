<?php
require_once '../includes/funcoes.php';
redirect_if_not_logged();

$ligacao = ligar_bd();
if (!$ligacao) { header('Location: lista.php'); exit; }

try {
    $rows = $ligacao->query(
        "SELECT l.edificio, IFNULL(l.piso, '—') AS piso, l.servico,
                IFNULL(l.sala, '—') AS sala,
                COUNT(e.codigo) AS totalEquipamentos
         FROM Localizacao l
         LEFT JOIN Equipamento e ON e.codigoLocalizacao = l.codigo
         WHERE l.ativo = 1
         GROUP BY l.codigo
         ORDER BY l.edificio, l.piso, l.servico"
    )->fetchAll(PDO::FETCH_OBJ);

    $total = $ligacao->query("SELECT COUNT(*) FROM Localizacao WHERE ativo = 1")->fetchColumn();
} catch (PDOException $e) {
    header('Location: lista.php');
    exit;
}
$ligacao = null;

registar_log('exportacao_pdf', 'Exportação de localizações em PDF — ' . count($rows) . ' registos');
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Localizações — MedCore</title>
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
        .badge { display: inline-block; padding: 2px 8px; border-radius: 20px; font-size: 9px; font-weight: 600; color: #fff; background: #1e1b4b; }
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
        <h1><img src="/sibdas/1241028/medcore/assets/img/logo medcore.png" alt="MedCore" style="height:36px;vertical-align:middle;margin-right:10px;">MedCore — Relatório de Localizações</h1>
        <div style="font-size:11px;opacity:.8;margin-top:4px;">Locais físicos do Hospital</div>
    </div>
    <div class="meta">
        <div>Gerado em: <?= date('d/m/Y \à\s H:i') ?></div>
        <div>Total de registos: <?= count($rows) ?></div>
    </div>
</div>
<div class="summary">
    <div class="summary-box"><div class="num"><?= $total ?></div><div class="lbl">Localizações Ativas</div></div>
    <div class="summary-box"><div class="num"><?= array_sum(array_column((array)$rows, 'totalEquipamentos')) ?></div><div class="lbl">Equipamentos Alocados</div></div>
</div>
<div class="table-wrap">
    <div class="section-title">Lista de Localizações</div>
    <table>
        <thead>
            <tr>
                <th>Edifício</th>
                <th>Piso</th>
                <th>Serviço / Departamento</th>
                <th>Sala / Gabinete</th>
                <th style="text-align:center;">Nº Equipamentos</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $loc): ?>
            <tr>
                <td><strong><?= htmlspecialchars($loc->edificio) ?></strong></td>
                <td><?= htmlspecialchars($loc->piso) ?></td>
                <td><?= htmlspecialchars($loc->servico) ?></td>
                <td style="font-family:monospace;"><?= htmlspecialchars($loc->sala) ?></td>
                <td style="text-align:center;">
                    <span class="badge" style="background:<?= $loc->totalEquipamentos > 0 ? '#1e1b4b' : '#adb5bd' ?>;">
                        <?= $loc->totalEquipamentos ?>
                    </span>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<div class="footer-info">MedCore — Sistema de Gestão de Inventário Hospitalar &nbsp;|&nbsp; Relatório gerado em <?= date('d/m/Y H:i:s') ?></div>
<button class="print-btn" onclick="window.print()">Imprimir / Guardar como PDF</button>
</body>
</html>
