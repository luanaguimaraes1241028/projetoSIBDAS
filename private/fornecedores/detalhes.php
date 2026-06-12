<?php include '../includes/header.php'; ?>
<?php include '../includes/nav.php'; ?>

    <div class="container-fluid">
        <div class="row">
            
            <?php include '../includes/sidebar.php'; ?>

            <main class="col-md-9 col-lg-10 px-md-4 pt-4">
                <div class="card shadow-sm border p-4 mx-auto my-4" style="max-width: 850px;">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                        <h2 class="fw-bold mb-0" style="color: #1e1b4b;">
                            <i class="fa-solid fa-building"></i> Dräger Portugal Lda.
                        </h2>
                        <span class="badge bg-primary fs-6 px-3 py-2">Fabricante</span>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-4">
                            <label class="text-muted small text-uppercase d-block">NIF</label>
                            <span class="fw-bold text-dark">501234567</span>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small text-uppercase d-block">Telefone Geral</label>
                            <span class="text-dark">210 000 000</span>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small text-uppercase d-block">Email Institucional</label>
                            <span class="text-dark">info@draeger.com</span>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small text-uppercase d-block">Morada Fiscal</label>
                            <span class="text-dark">Rua da Tecnologia Biomédica, N.º 42, Lisboa</span>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small text-uppercase d-block">Website</label>
                            <a href="#" target="_blank" class="d-block text-primary">www.draeger.pt</a>
                        </div>
                    </div>

                    <h5 class="fw-bold border-bottom pb-2 mb-3" style="color: #1e1b4b;">
                        <i class="fa-solid fa-user-tie"></i> Contacto Direto Técnico
                    </h5>
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="text-muted small text-uppercase d-block">Pessoa de Contacto</label>
                            <span class="text-dark fw-bold">Eng. Carlos Mendes</span>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small text-uppercase d-block">Telefone Direto</label>
                            <span class="text-dark">912 345 678</span>
                        </div>
                    </div>

                    <h5 class="fw-bold border-bottom pb-2 mb-3" style="color: #1e1b4b;">
                        <i class="fa-solid fa-laptop-medical"></i> Equipamento Médico Associado
                    </h5>
                    <div class="p-3 bg-light rounded border mb-4">
                        <strong class="text-dark">#EQ-0042 - Ventilador Volumétrico Hospitalar</strong>
                        <p class="mb-0 text-muted small">Esta entidade assume o papel de Fabricante oficial deste ativo do inventário.</p>
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