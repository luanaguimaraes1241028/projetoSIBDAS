<?php require_once __DIR__ . '/../includes/funcoes.php';

// Segurança: Garante que apenas utilizadores autenticados conseguem aceder à listagem
redirect_if_not_logged(); ?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/nav.php'; ?>

<?php
// Abre o canal de comunicação com o MySQL
$ligacao = ligar_bd();
$erro = '';
$ativos = [];
$arquivados = [];

if (!$ligacao) {
    // Tratamento preventivo caso o servidor de base de dados do ISEP falhe ou esteja em baixo
    $erro = "Erro na ligação à base de dados.";
} else {
    try {
        // Executa a consulta trazendo todos os documentos e as suas chaves estrangeiras resolvidas.
        // Ordena por ordem cronológica decrescente para colocar os registos mais recentes no topo.
        $stmt = $ligacao->query(
            "SELECT d.*, e.codigoInterno, e.designacao FROM Documentacao d
             JOIN Equipamento e ON d.codigoEquipamento = e.codigo
             LEFT JOIN Fornecedor f ON d.codigoFornecedor = f.codigo
             ORDER BY d.dataDocumento DESC"
        );
        $resultados = $stmt->fetchAll(PDO::FETCH_OBJ);

        // Lógica de Separação de Subconjuntos: Varre a coleção inteira devolvida pela BD
        // e organiza os registos em dois blocos separados com base no estado lógico do soft-delete
        foreach ($resultados as $r) {
            if ((int)$r->ativo === 1) {
                $ativos[] = $r;       // Vai preencher a tabela principal de trabalho
            } else {
                $arquivados[] = $r;   // Vai preencher a secção secundária de histórico
            }
        }
    } catch (PDOException $err) {
        // Captura falhas de execução do driver SQL impedindo erros fatais no ecrã do utilizador
        $erro = "Erro ao carregar documentação.";
    }
    // Liberta o ponteiro e fecha a sessão com a base de dados
    $ligacao = null;
}
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/sidebar.php'; ?>

        <main class="col-md-9 col-lg-10 px-md-4 pt-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items