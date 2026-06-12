<?php include '../includes/header.php'; ?>
<?php include '../includes/nav.php'; ?>

    <div class="container-fluid">
        <div class="row">
            
            <?php include '../includes/sidebar.php'; ?>

            <main class="col-md-9 col-lg-10 px-md-4 pt-5">
                <div class="card shadow-sm border border-warning text-center mx-auto p-5 my-5" style="max-width: 580px; background-color: #fff;">
                    <h1 class="display-4 text-warning mb-3">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </h1>
                    <p class="fs-5 text-muted">Deseja remover este fornecedor do ecossistema?</p>
                    
                    <h3 class="fw-bold mb-4" style="color: #1e1b4b;">Dräger Portugal Lda.</h3>
                    
                    <div class="bg-light rounded p-3 mb-4 border text-start small">
                        <div class="mb-2"><strong>Papel no Sistema:</strong> Fabricante Oficial</div>
                        <div><strong>Equipamento Vinculado:</strong> #EQ-0042 - Ventilador Volumétrico</div>
                    </div>
                    
                    <div class="d-flex justify-content-center gap-3">
                        <a href="lista.html" class="btn btn-outline-secondary px-4 py-2 fw-bold">
                            <i class="fa-solid fa-xmark me-2"></i> Cancelar
                        </a>
                        <a href="lista.php" class="btn btn-danger px-4 py-2 fw-bold shadow-sm">
                            <i class="fa-solid fa-trash-can me-2"></i> Eliminar Registo
                        </a>
                    </div>
                </div>
            </main>

        </div>
    </div>
    <?php include '../includes/footer.php'; ?>