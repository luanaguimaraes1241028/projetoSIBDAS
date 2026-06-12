<?php include '../includes/header.php'; ?>
<?php include '../includes/nav.php'; ?>

    <div class="container-fluid">
        <div class="row">
            
            <?php include '../includes/sidebar.php'; ?>

            <main class="col-md-9 col-lg-10 px-md-4 pt-4">
                <div class="card shadow-sm border p-4 mx-auto my-4" style="max-width: 850px;">
                    <h2 class="fw-bold mb-3" style="color: #1e1b4b;">
                        <i class="fa-solid fa-square-plus"></i> Novo Fornecedor / Entidade
                    </h2>
                    <hr>
                    
                    <form action="lista.php" method="post" class="row g-3">
                        <h5 class="fw-bold text-primary mt-3"><i class="fa-solid fa-building"></i> Dados da Empresa</h5>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nome da Empresa</label>
                            <input type="text" class="form-control" placeholder="Ex: Dräger Portugal Lda." required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">NIF (Número de Identificação Fiscal)</label>
                            <input type="text" class="form-control" placeholder="Ex: 500123456" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Tipo de Fornecedor</label>
                            <select class="form-select" required>
                                <option selected disabled value="">Escolha uma opção...</option>
                                <option>Fabricante</option>
                                <option>Distribuidor / Fornecedor Comercial</option>
                                <option>Empresa de Assistência Técnica</option>
                                <option>Fornecedor de Consumíveis ou Acessórios</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Contacto Telefónico</label>
                            <input type="tel" class="form-control" placeholder="Ex: 210000000" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" class="form-control" placeholder="Ex: info@empresa.com" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Morada</label>
                            <input type="text" class="form-control" placeholder="Ex: Rua Principal, Nº 1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Website</label>
                            <input type="url" class="form-control" placeholder="https://...">
                        </div>

                        <h5 class="fw-bold text-primary mt-4"><i class="fa-solid fa-user-tie"></i> Pessoa de Contacto</h5>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nome do Contacto</label>
                            <input type="text" class="form-control" placeholder="Ex: Eng. Carlos Mendes">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Telefone Direto</label>
                            <input type="tel" class="form-control" placeholder="Ex: 912345678">
                        </div>

                        <h5 class="fw-bold text-primary mt-4"><i class="fa-solid fa-link"></i> Associação ao Inventário</h5>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Vincular a Equipamento Médico</label>
                            <select class="form-select">
                                <option selected disabled value="">Escolha um equipamento...</option>
                                <option>#EQ-0042 - Ventilador Volumétrico Hospitalar</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold">Observações</label>
                            <textarea class="form-control" rows="2" placeholder="Notas adicionais sobre a parceria técnica..."></textarea>
                        </div>

                        <div class="col-12 d-flex gap-2 mt-4">
                            <a href="lista.php" class="btn btn-secondary px-4">
                                <i class="fa-solid fa-xmark"></i> Cancelar
                            </a>
                            <button type="submit" class="btn text-white px-4" style="background-color: #1e1b4b;">
                                <i class="fa-regular fa-floppy-disk"></i> Registar Entidade
                            </button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>