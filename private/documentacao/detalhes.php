<?php include '../includes/header.php'; ?>
<?php include '../includes/nav.php'; ?>

    <div class="container-fluid">
        <div class="row">
            
            <?php include '../includes/sidebar.php'; ?>

            <main class="col-md-9 col-lg-10 px-md-4 pt-4">
                <div class="card shadow-sm border p-4 mx-auto my-4" style="max-width: 800px;">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                        <h2 class="fw-bold mb-0" style="color: #1e1b4b;">
                            <i class="fa-solid fa-file-lines"></i> Detalhes do Documento
                        </h2>
                        <span class="badge bg-primary px-3 py-2">Manual Técnico</span>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-12">
                            <label class="text-muted small text-uppercase d-block">Nome do Documento</label>
                            <span class="fs-5 fw-bold text-dark">Manual Técnico de Operação Dräger</span>
                        </div>
                        <div class="col-md-6 border-bottom pb-2">
                            <label class="text-muted small text-uppercase d-block">Data do Documento</label>
                            <span class="text-dark fw-bold">15/05/2026</span>
                        </div>
                        <div class="col-md-6 border-bottom pb-2">
                            <label class="text-muted small text-uppercase d-block">Data de Validade</label>
                            <span class="text-muted fw-bold">Vitalício / Não Aplicável</span>
                        </div>
                        <div class="col-md-6 border-bottom pb-2">
                            <label class="text-muted small text-uppercase d-block">Equipamento Associado</label>
                            <span class="text-dark fw-bold">#EQ-0042 - Ventilador Volumétrico Hospitalar</span>
                        </div>
                        <div class="col-md-6 border-bottom pb-2">
                            <label class="text-muted small text-uppercase d-block">Fornecedor Associado</label>
                            <span class="text-dark">Dräger Portugal Lda.</span>
                        </div>
                        <div class="col-md-12">
                            <label class="text-muted small text-uppercase d-block">Localização Física do Ficheiro / Link</label>
                            <div class="p-2 bg-light rounded border text-primary small">
                                <i class="fa-solid fa-file-pdf text-danger"></i> <code>/uploads/documentacao/manual_ventilador_v1.pdf</code>
                            </div>
                        </div>
                    </div>

                    <div>
                        <a href="lista.php" class="btn btn-secondary px-4">
                            <i class="fa-solid fa-arrow-left"></i> &ensp;Voltar
                        </a>
                    </div>
                </div>
            </main>

        </div>
    </div>
    <?php include '../includes/footer.php'; ?>