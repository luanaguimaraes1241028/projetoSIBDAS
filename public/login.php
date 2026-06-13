<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedCore Inventory - Acesso Restrito</title>
    
    <link rel="shortcut icon" href="/projeto-sibdas/assets/img/logo medcore.png" type="image/jpeg">

    <link rel="stylesheet" href="/projeto-sibdas/assets/bootstrap/bootstrap.min.css">

    <link rel="stylesheet" href="/projeto-sibdas/assets/fontawesome/all.min.css">

    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="/projeto-sibdas/assets/css/1241028.css">
</head>
<body style="font-family: 'Nunito', sans-serif; background-color: #f8fafc;">

    <div class="container-fluid mt-5">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-8 col-md-6 col-lg-4">
                
                <div class="card p-4 shadow-sm border-0" style="border-radius: 12px;">
                    
                    <div class="text-center my-3">
                        <img src="/projeto-sibdas/assets/img/logo medcore.png" alt="Logo MedCore" style="height: 50px;" class="mb-2">
                        <h2 class="fw-bold h4 text-dark mb-1">Área Cliente</h2>
                        <p class="text-muted small">Faça login para aceder ao sistema.</p>
                    </div>
                    
                    <hr class="text-muted mb-4">

                    <form id="loginForm" action="/projeto-sibdas/private/dashboard/dashboard.php" method="POST">
                        
                        <div class="mb-3">
                            <label for="username" class="form-label fw-bold small text-secondary">Utilizador / Email:</label>
                            <input type="text" id="username" name="username" class="form-control" placeholder="Ex: engenheiro@medcore.pt" required>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label fw-bold small text-secondary">Palavra-passe:</label>
                            <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
                        </div>

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

    <script src="/projeto-sibdas/assets/bootstrap/bootstrap.bundle.min.js"></script>
</body>
</html>