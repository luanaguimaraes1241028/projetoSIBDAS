<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedCore Inventory - Registar Fornecedor</title>
    <link class="shortcut icon" href="../../assets/img/logo medcore.png" type="image/png">
    <link rel="stylesheet" href="../../assets/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/fontawesome/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/1241028.css">
</head>
<body style="font-family: 'Nunito', sans-serif; background-color: #f8fafc;">

    <header class="container-fluid text-white" style="background-color: #1e1b4b; height: 60px;">
        <div class="row align-items-center h-100">
            
            <div class="col-6 d-flex align-items-center px-4">
                <a href="../dashboard/dashboard.html">
                    <img src="../../assets/img/logo medcore.png" alt="Logo MedCore" style="height: 40px;" class="me-3">
                </a>
                <span class="mb-0 h1 fw-bold h5">MedCore Inventory</span>
            </div>

            <div class="col-6 text-end px-4">
                <div class="dropdown d-inline-block">
                    <button class="btn btn-secondary dropdown-toggle px-3" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="background-color: rgba(255, 255, 255, 0.15); border: none; padding: 6px 12px;">
                        <i class="fa-regular fa-user me-2"></i> Utilizador
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="fa-solid fa-key me-2"></i> Alterar password
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item text-danger" href="../public/index.html">
                                <i class="fa-solid fa-right-from-bracket me-2"></i> Sair (Logout)
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </header>

    <div class="container-fluid">
        <div class="row">
            
            <aside class="col-md-3 col-lg-2 bg-white sidebar vh-100 border-end p-0">
                <div class="position-sticky pt-3">
                    <nav>
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link py-3 px-4 fw-bold text-secondary" href="../dashboard/dashboard.html">
                                    <i class="fa-solid fa-chart-pie"></i> &ensp; Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-3 px-4 fw-bold text-secondary" href="../equipamentos/lista.html">
                                    <i class="fa-solid fa-stethoscope"></i> &ensp; Equipamentos
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-3 px-4 fw-bold text-secondary" href="../localizacoes/lista.html">
                                    <i class="fa-solid fa-hospital"></i> &ensp; Localizações
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active py-3 px-4 fw-bold text-primary bg-light border-start border-4 border-primary" href="lista.html">
                                    <i class="fa-solid fa-truck-field"></i> &ensp; Fornecedores
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-3 px-4 fw-bold text-secondary" href="../documentacao/lista.html">
                                    <i class="fa-solid fa-folder-open"></i> &ensp; Documentação
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-3 px-4 fw-bold text-secondary" href="../gestao-public.html">
                                    <i class="fa-solid fa-globe"></i> &ensp; Gestão de Conteúdos
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </aside>

            <main class="col-md-9 col-lg-10 px-md-4 pt-4">
                <div class="card shadow-sm border p-4 mx-auto my-4" style="max-width: 850px;">
                    <h2 class="fw-bold mb-3" style="color: #1e1b4b;">
                        <i class="fa-solid fa-square-plus"></i> Novo Fornecedor / Entidade
                    </h2>
                    <hr>
                    
                    <form action="lista.html" method="post" class="row g-3">
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
                            <a href="lista.html" class="btn btn-secondary px-4">
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
    <script src="../../assets/bootstrap/bootstrap.bundle.min.js"></script>
</body>
</html>