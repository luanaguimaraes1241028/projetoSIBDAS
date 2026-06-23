<?php require_once __DIR__ . '/funcoes.php'; redirect_if_not_logged(); ?>

<header class="container-fluid text-white" style="background-color: #1e1b4b; height: 60px;">
        <div class="row align-items-center h-100">
            
            <div class="col-6 d-flex align-items-center px-4">
                <a href="../dashboard/dashboard.php">
                    <img src="/sibdas/1241028/medcore/assets/img/logo medcore.png" alt="Logo MedCore" style="height: 40px;" class="me-3">
                </a>
                <span class="mb-0 h1 fw-bold h5"><?php echo APP_NAME; ?></span>
            </div>

            <div class="col-6 text-end px-4">
                <div class="dropdown d-inline-block">
                    <button class="btn btn-secondary dropdown-toggle px-3" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="background-color: rgba(255, 255, 255, 0.15); border: none; padding: 6px 12px;">
                        <i class="fa-regular fa-user me-2"></i> <?= htmlspecialchars($_SESSION['utilizador']) ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        <li>
                            <a class="dropdown-item" href="/sibdas/1241028/medcore/private/alterar-password.php">
                                <i class="fa-solid fa-key me-2"></i> Alterar password
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item text-danger" href="/sibdas/1241028/medcore/public/logout.php">
                                <i class="fa-solid fa-right-from-bracket me-2"></i> Sair (Logout)
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </header>