<?php include '../includes/header.php'; ?>
<?php include '../includes/nav.php'; ?>

    <div class="container-fluid">
        <div class="row">
            
            <?php include '../includes/sidebar.php'; ?>

            <main class="col-md-9 col-lg-10 px-md-4 pt-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <div>
                        <h1 class="h2 fw-bold" style="color: #1e1b4b;">Localização do Inventário Biomédico</h1>
                        <p class="text-muted">Consulta e controlo em tempo real de onde se encontra cada dispositivo médico.</p>
                    </div>
                    <div>
                        <a href="novo.php" class="btn text-white fw-bold shadow-sm" style="background-color: #1e1b4b;">
                            <i class="fa-solid fa-link"></i> &ensp;Associar Equipamento
                        </a>
                    </div>
                </div>

                <div class="p-3 bg-white border rounded shadow-sm mb-4">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover align-middle mb-0">
                            <thead class="table-dark small uppercase">
                                <tr>
                                    <th>Equipamento (Cód / Nome)</th>
                                    <th>Edifício</th>
                                    <th>Piso</th>
                                    <th>Serviço / Departamento</th>
                                    <th>Sala / Gabinete</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <span class="badge bg-dark mb-1">#EQ-0042</span>
                                        <div class="fw-bold text-dark">Ventilador Volumétrico Hospitalar</div>
                                    </td>
                                    <td>Edifício Central (A)</td>
                                    <td><span class="badge bg-secondary">Piso 2</span></td>
                                    <td>Cuidados Intensivos (UCIP)</td>
                                    <td><code class="text-primary small fw-bold">Sala UCIP-04</code></td>
                                    <td class="text-center">
                                        <div class="btn-group shadow-sm">
                                            <a href="detalhes.php" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center" title="Consultar Alocação">
                                                <i class="fa-solid fa-eye text-primary"></i>
                                            </a>
                                            <a href="editar.php" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center" title="Mover Equipamento">
                                                <i class="fa-solid fa-pen-to-square text-secondary"></i>
                                            </a>
                                            <a href="apagar.php" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center" title="Desassociar">
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