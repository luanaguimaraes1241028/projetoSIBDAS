<?php include '../includes/header.php'; ?>
<?php include '../includes/nav.php'; ?>

    <div class="container-fluid">
        <div class="row">
            
            <?php include '../includes/sidebar.php'; ?>

            <main class="col-md-9 col-lg-10 px-md-4 pt-4">
                <div class="card shadow-sm border p-4 mx-auto my-4" style="max-width: 800px;">
                    <h2 class="fw-bold mb-3" style="color: #1e1b4b;">
                        <i class="fa-solid fa-circle-info"></i> Informação de Localização do Dispositivo
                    </h2>
                    <hr>
                    
                    <h4 class="fw-bold text-muted small uppercase mb-3">Equipamento Alocado</h4>
                    <div class="p-3 bg-light rounded border border-start border-4 border-dark mb-4">
                        <span class="badge bg-dark mb-1">#EQ-0042</span>
                        <h5 class="fw-bold text-dark mb-1">Ventilador Volumétrico Hospitalar</h5>
                        <small class="text-muted">N.º Série: SN-DRG-88321-X | Marca: Dräger</small>
                    </div>

                    <h4 class="fw-bold text-muted small uppercase mb-3">Coordenadas Físicas Hospitalares</h4>
                    <div class="row g-4 mb-4">
                        <div class="col-md-6 border-bottom pb-2">
                            <label class="text-muted small d-block">Edifício</label>
                            <div class="fs-6 text-dark fw-bold">Edifício Central (A)</div>
                        </div>
                        <div class="col-md-6 border-bottom pb-2">
                            <label class="text-muted small d-block">Piso</label>
                            <div class="fs-6 text-dark fw-bold">Piso 2</div>
                        </div>
                        <div class="col-md-6 border-bottom pb-2">
                            <label class="text-muted small d-block">Serviço / Departamento</label>
                            <div class="fs-6 text-dark fw-bold">Cuidados Intensivos (UCIP)</div>
                        </div>
                        <div class="col-md-6 border-bottom pb-2">
                            <label class="text-muted small d-block">Sala / Gabinete</label>
                            <div class="fs-6 text-dark fw-bold">Sala UCIP-04</div>
                        </div>
                    </div>

                    <div>
                        <a href="lista.php" class="btn btn-secondary px-4">
                            <i class="fa-solid fa-arrow-left"></i> &ensp;Voltar à Lista
                        </a>
                    </div>
                </div>
            </main>

        </div>
    </div>
    <?php include '../includes/footer.php'; ?>