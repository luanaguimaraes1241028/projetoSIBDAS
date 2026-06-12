<?php include '../includes/header.php'; ?>
<?php include '../includes/nav.php'; ?>

    <div class="container-fluid">
        <div class="row">
            
            <?php include '../includes/sidebar.php'; ?>

            <main class="col-md-9 col-lg-10 px-md-4 pt-4">
                <div class="card shadow-sm border p-4 mx-auto my-4" style="max-width: 800px;">
                    <h2 class="fw-bold mb-3" style="color: #1e1b4b;">
                        <i class="fa-solid fa-link"></i> Associar Equipamento a Localização
                    </h2>
                    <hr>
                    
                    <form action="lista.php" method="post" class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Selecionar Equipamento Médico</label>
                            <select class="form-select" required>
                                <option selected disabled value="">Escolha um dispositivo disponível...</option>
                                <option>#EQ-0042 - Ventilador Volumétrico Hospitalar (Dräger)</option>
                                <option>#EQ-0089 - Desfibrilhador Bifásico Pro (Zoll Medical)</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Edifício</label>
                            <input type="text" class="form-control" placeholder="Ex: Edifício Central (A)" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Piso</label>
                            <input type="text" class="form-control" placeholder="Ex: Piso 2" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Serviço / Departamento</label>
                            <input type="text" class="form-control" placeholder="Ex: Cuidados Intensivos (UCIP)" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Sala / Gabinete</label>
                            <input type="text" class="form-control" placeholder="Ex: Sala UCIP-04" required>
                        </div>

                        <div class="col-12 d-flex gap-2 mt-4">
                            <a href="lista.php" class="btn btn-secondary px-4">
                                <i class="fa-solid fa-xmark"></i> Cancelar
                            </a>
                            <button type="submit" class="btn text-white px-4" style="background-color: #1e1b4b;">
                                <i class="fa-regular fa-floppy-disk"></i> Guardar Alocação
                            </button>
                        </div>
                    </form>
                </div>
            </main>

        </div>
    </div>
    <?php include '../includes/footer.php'; ?>