<?php include '../includes/header.php'; ?>
<?php include '../includes/nav.php'; ?>

    <div class="container-fluid">
        <div class="row">
            
            <?php include '../includes/sidebar.php'; ?>

            <main class="col-md-9 col-lg-10 px-md-4 pt-4">
                <div class="card shadow-sm border p-4 mx-auto my-4" style="max-width: 800px;">
                    <h2 class="fw-bold mb-3" style="color: #1e1b4b;">
                        <i class="fa-solid fa-file-circle-plus"></i> Associar Novo Documento
                    </h2>
                    <hr>
                    
                    <form action="lista.php" method="post" enctype="multipart/form-data" class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nome do Documento</label>
                            <input type="text" class="form-control" placeholder="Ex: Manual de Utilizador V1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tipo de Documento</label>
                            <select class="form-select" required>
                                <option selected disabled value="">Escolha um tipo...</option>
                                <option>Manual Técnico / Operação</option>
                                <option>Contrato de Garantia</option>
                                <option>Certificado de Calibração / Conformidade</option>
                                <option>Folha de Assistência Preventiva</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Data do Documento</label>
                            <input type="date" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Data de Validade <span class="text-muted small">(Quando aplicável)</span></label>
                            <input type="date" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Equipamento Associado</label>
                            <select class="form-select" required>
                                <option selected disabled value="">Selecione o equipamento...</option>
                                <option>#EQ-0042 - Ventilador Volumétrico Hospitalar</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Fornecedor Associado <span class="text-muted small">(Se necessário)</span></label>
                            <select class="form-select">
                                <option selected disabled value="">Selecione o fornecedor...</option>
                                <option>Dräger Portugal Lda.</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold">Ficheiro Digital (Upload Real / Caminho)</label>
                            <input type="file" class="form-control" required>
                        </div>

                        <div class="col-12 d-flex gap-2 mt-4">
                            <a href="lista.php" class="btn btn-secondary px-4">
                                <i class="fa-solid fa-xmark"></i> Cancelar
                            </a>
                            <button type="submit" class="btn text-white px-4" style="background-color: #1e1b4b;">
                                <i class="fa-regular fa-floppy-disk"></i> Guardar Documento
                            </button>
                        </div>
                    </form>
                </div>
            </main>

        </div>
    </div>
    <?php include '../includes/footer.php'; ?>