<?php include '../includes/header.php'; ?>
<?php include '../includes/nav.php'; ?>


    <div class="container-fluid">
        <div class="row">
            
            <?php include '../includes/sidebar.php'; ?>


            <main class="col-md-9 col-lg-10 px-md-4 pt-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <div>
                        <h1 class="h2 fw-bold" style="color: #1e1b4b;">Gestão de Fornecedores</h1>
                        <p class="text-muted">Registo e associação de entidades, fabricantes e empresas de assistência técnica.</p>
                    </div>
                    <div>
                        <a href="novo.php" class="btn text-white fw-bold shadow-sm" style="background-color: #1e1b4b;">
                            <i class="fa-solid fa-plus"></i> &ensp;Registar Fornecedor
                        </a>
                    </div>
                </div>

                <div class="p-3 bg-white border rounded shadow-sm mb-4">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover align-middle mb-0">
                            <thead class="table-dark small uppercase">
                                <tr>
                                    <th>Empresa / NIF</th>
                                    <th>Tipo de Fornecedor</th>
                                    <th>Contacto Principal</th>
                                    <th>Equipamento Associado</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">Dräger Portugal Lda.</div>
                                        <small class="text-muted">NIF: 501234567</small>
                                    </td>
                                    <td><span class="badge bg-primary px-2 py-1">Fabricante</span></td>
                                    <td>
                                        <div class="small"><i class="fa-solid fa-phone text-muted"></i> 210 000 000</div>
                                        <div class="small text-muted"><i class="fa-solid fa-envelope"></i> info@draeger.com</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-dark">#EQ-0042</span>
                                        <div class="small fw-bold text-secondary">Ventilador Volumétrico</div>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group shadow-sm">
                                            <a href="detalhes.php" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center" title="Consultar">
                                                <i class="fa-solid fa-eye text-primary"></i>
                                            </a>
                                            <a href="editar.php" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center" title="Editar">
                                                <i class="fa-solid fa-pen-to-square text-secondary"></i>
                                            </a>
                                            <a href="apagar.php" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center" title="Remover">
                                                <i class="fa-solid fa-box-archive text-danger"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>

        </div>
    </div>
<?php include '../includes/footer.php'; ?>

    