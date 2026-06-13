<?php
session_start();
$validation_errors = [];
if (!empty($_SESSION['validation_errors'])) {
    $validation_errors = $_SESSION['validation_errors'];
    unset($_SESSION['validation_errors']);
}
$server_error = [];
if (!empty($_SESSION['server_error'])) {
    $server_error = $_SESSION['server_error'];
    unset($_SESSION['server_error']);
}
?>

<?php include '../private/includes/header.php'; ?>

    <div class="container-fluid mt-5">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-8 col-md-6 col-lg-4">
                
                <div class="card p-4 shadow-sm border-0" style="border-radius: 12px;">
                    
                    <div class="text-center my-3">
                        <img src="/projeto-sibdas/assets/img/logo medcore.png" alt="Logo MedCore" style="height: 50px;" class="mb-2">
                        <h2 class="fw-bold h4 text-dark mb-1"><?php echo APP_NAME; ?></h2>
                        <p class="text-muted small">Faça login para aceder ao sistema.</p>
                    </div>
                    
                    <hr class="text-muted mb-4">

                    <form id="loginForm" action="../private/processa_login.php" method="POST">
                        
                        <div class="mb-3">
                            <label for="username" class="form-label fw-bold small text-secondary">Utilizador / Email:</label>
                            <input type="text" id="username" name="text_username" class="form-control" placeholder="Ex: engenheiro@medcore.pt" required>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label fw-bold small text-secondary">Palavra-passe:</label>
                            <input type="password" id="password" name="text_password" class="form-control" placeholder="••••••••" required>
                        </div>

                        <?php if (!empty($validation_errors)) : ?>
                            <div class="alert alert-danger p-2 text-center">
                                <?php foreach ($validation_errors as $error) : ?>
                                    <div><?= htmlspecialchars($error) ?></div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($server_error)) : ?>
                            <div class="alert alert-danger p-2 text-center">
                                <div><?= htmlspecialchars($server_error) ?></div>
                            </div>
                        <?php endif; ?>

                        <div class="mb-3 text-center">
                            <button type="submit" class="btn btn-primary px-4 w-100 fw-bold" style="background-color: #1e1b4b; border: none; padding: 10px;">
                                Aceder à Plataforma &ensp;<i class="fa-solid fa-right-to-bracket"></i>
                            </button>
                        </div>
                    </form>

                    <p class="text-center mb-0 mt-2 small">
                        <a href="/projeto-sibdas/public/index.php" class="text-decoration-none text-muted">
                            <i class="fa-solid fa-arrow-left small"></i> Voltar ao site principal
                        </a>
                    </p>
                    
                </div> </div>
        </div>
    </div>

<?php include '../private/includes/footer.php'; ?>
