<?php require_once __DIR__ . '/includes/funcoes.php'; redirect_if_not_admin(); ?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/nav.php'; ?>


    <div class="container-fluid">
        <div class="row">
            
            <?php include 'includes/sidebar.php'; ?>

            <main class="col-md-9 col-lg-10 px-md-4 pt-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <div>
                        <h1 class="h2 fw-bold" style="color: #1e1b4b;">Gestão da Área Pública</h1>
                        <p class="text-muted">Painel de Administração evitando a edição direta do HTML.</p>
                    </div>
                </div>

                <div class="card shadow-sm border p-4 mb-4" style="background-color: #fff;">
                    <form action="../public/index.html" method="get" class="row g-4">
                        
                        <div class="col-12">
                            <h5 class="fw-bold text-primary border-bottom pb-2"><i class="fa-solid fa-heading"></i> Ecrã Principal</h5>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Título Principal do Site</label>
                            <input type="text" class="form-control" value="Gestão Tecnológica Hospitalar Integrada" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Subtítulo de Introdução</label>
                            <textarea class="form-control" rows="2" required>A plataforma definitiva para Engenharia Clínica e controlo absoluto de ativos médicos.</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Imagem de Destaque (Caminho / Upload)</label>
                            <input type="file" class="form-control">
                            <div class="form-text text-muted">Ficheiro atual: <code>/assets/img/foto front office.jpg</code></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Texto do Botão de Ação</label>
                            <input type="text" class="form-control" value="Solicitar Demonstração" required>
                        </div>

                        <div class="col-12 mt-5">
                            <h5 class="fw-bold text-primary border-bottom pb-2"><i class="fa-solid fa-file-lines"></i> Sobre Nós</h5>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Título 1</label>
                            <input type="text" class="form-control" value="Ecossistema Unificado" required>
                            <label class="form-label fw-bold mt-2 text-muted small">Conteúdo</label>
                            <textarea class="form-control" rows="4" required>O MedCore Inventory centraliza a gestão de equipamentos médicos. Substituímos folhas de Excel dispersas e pastas físicas por uma plataforma única e digital, garantindo dados atualizados em tempo real.</textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Título 2</label>
                            <input type="text" class="form-control" value="Ciclo de Vida Clínico" required>
                            <label class="form-label fw-bold mt-2 text-muted small">Conteúdo</label>
                            <textarea class="form-control" rows="4" required>Alinhado com as diretrizes da OMS, o system controla os dispositivos desde a aquisição até ao seu abate técnico, servindo de base para o planeamento, segurança e análise de risco hospitalar.</textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Título 3</label>
                            <input type="text" class="form-control" value="Rastreabilidade e Futuro" required>
                            <label class="form-label fw-bold mt-2 text-muted small">Conteúdo</label>
                            <textarea class="form-control" rows="4" required>Garanta total controlo sobre a localização de ativos, fornecedores e contratos. Tudo assente numa estrutura de dados robusta, pronta para evoluuir para um system CMMS/GMAO completo.</textarea>
                        </div>

                        <div class="col-12 mt-5">
                            <h5 class="fw-bold text-primary border-bottom pb-2"><i class="fa-solid fa-address-book"></i> Informações de Contacto e Suporte</h5>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Endereço Hospitalar / Sede</label>
                            <textarea class="form-control" rows="3" required>Rua Dr. António Bernardino de Almeida&#10;4200-072, Porto&#10;Portugal</textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Horários do Suporte Técnico</label>
                            <textarea class="form-control" rows="3" required>2ª a 6ª Feira: 8h — 18h&#10;Sábados: 9h — 13h&#10;Piquete de Prevenção: 24h / 7 dias</textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Canais Diretos de Contacto</label>
                            <label class="text-muted small d-block">Email:</label>
                            <input type="email" class="form-control form-control-sm mb-2" value="suporte@medcore.pt" required>
                            <label class="text-muted small d-block">Telefone:</label>
                            <input type="text" class="form-control form-control-sm" value="+351 226 811 298" required>
                        </div>

                        <div class="col-12 d-flex gap-2 mt-4 border-top pt-3">
                            <a href="dashboard/dashboard.html" class="btn btn-secondary px-4"><i class="fa-solid fa-xmark"></i> Cancelar</a>
                            <button type="submit" class="btn text-white px-4" style="background-color: #1e1b4b;"><i class="fa-regular fa-floppy-disk"></i> Publicar Alterações no Website</button>
                        </div>

                    </form>
                </div>
            </main>
            
        </div>
    </div>
<?php include 'includes/footer.php'; ?>

    