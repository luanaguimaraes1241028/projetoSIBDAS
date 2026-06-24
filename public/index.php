<?php
require_once __DIR__ . '/../config/config.php';
$cp = [];
try {
    $pdo = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
        MYSQL_USERNAME, MYSQL_PASSWORD,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    $cp  = $pdo->query("SELECT chave, valor FROM ConteudoPublico")->fetchAll(PDO::FETCH_KEY_PAIR);
    $pdo = null;
} catch (Exception $e) {
    $cp = [];
}
// cp(): lê conteúdo da BD com fallback e escapa HTML para output seguro
// cpnl(): igual mas converte \n em <br> — usado em campos de texto multilinha (morada, horários)
function cp(string $key, string $default = ''): string {
    global $cp;
    return htmlspecialchars($cp[$key] ?? $default);
}
function cpnl(string $key, string $default = ''): string {
    global $cp;
    return nl2br(htmlspecialchars($cp[$key] ?? $default));
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedCore Inventory - Sistemas de Informação Hospitalar</title>

    <link rel="shortcut icon" href="/sibdas/1241028/medcore/assets/img/logo medcore.png" type="image/png">

    <link rel="stylesheet" href="/sibdas/1241028/medcore/assets/css/1241028.css">

    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="/sibdas/1241028/medcore/assets/fontawesome/all.min.css">

</head>
<body>

    <nav class="bng-navbar">
        <div>
            <img src="/sibdas/1241028/medcore/assets/img/logo medcore.png" alt="Logo MedCore Inventory">
            <h3>MedCore Inventory</h3>
        </div>
        <div class="container-navegacao">
            <a href="#quem-somos">Sobre Nós</a>
            <a href="#solucoes">Soluções</a>
            <a href="#contacto">Suporte e Contactos</a>
        </div>
        <div class="nav-cliente">
            <a href="/sibdas/1241028/medcore/public/login.php" target="_blank">Área Cliente</a>
        </div>
    </nav>

    <section class="container-texto-generico" id="quem-somos">

        <div class="quem-somos-content" style="width: 100%; text-align: center; margin-bottom: 50px;">
            <h1 style="font-size: 3em; color: #1e1b4b; margin-bottom: 20px;"><?= cp('titulo_principal', 'Gestão Tecnológica Hospitalar Integrada') ?></h1>
            <p style="font-size: 1.2em; color: #64748b; max-width: 800px; margin: 0 auto 30px auto;"><?= cp('subtitulo', 'A plataforma definitiva para Engenharia Clínica e controlo absoluto de ativos médicos.') ?></p>

            <img src="<?= cp('imagem_destaque', '/sibdas/1241028/medcore/assets/img/foto front office.jpg') ?>" alt="Monitorização Hospitalar" style="width: 100%; max-width: 800px; height: 250px; object-fit: cover; border-radius: 8px; margin: 20px auto; display: block;">

            <a href="#contacto" class="button" style="display: inline-block; background-color: #4f46e5; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: 600; margin-top: 10px;"><?= cp('botao_acao', 'Solicitar Demonstração') ?></a>
        </div>

        <article>
            <h2><?= cp('sobre_titulo1', 'Ecossistema Unificado') ?></h2>
            <p><?= cpnl('sobre_conteudo1', 'O MedCore Inventory centraliza a gestão de equipamentos médicos.') ?></p>
        </article>

        <article>
            <h2><?= cp('sobre_titulo2', 'Ciclo de Vida Clínico') ?></h2>
            <p><?= cpnl('sobre_conteudo2', 'Alinhado com as diretrizes da OMS, o sistema controla os dispositivos desde a aquisição até ao seu abate técnico.') ?></p>
        </article>

        <article>
            <h2><?= cp('sobre_titulo3', 'Rastreabilidade e Futuro') ?></h2>
            <p><?= cpnl('sobre_conteudo3', 'Garanta total controlo sobre a localização de ativos, fornecedores e contratos.') ?></p>
        </article>

    </section>

    <section class="container-texto-generico" id="solucoes">
        <article>
            <i class="fa-solid fa-heart-pulse fa-3x" style="color: #4f46e5; margin-bottom: 15px;"></i>
            <h2><?= cp('solucao_titulo1', 'Controlo de Ativos') ?></h2>
            <p><?= cpnl('solucao_conteudo1', 'Centralize o registo completo do parque tecnológico hospitalar, com rastreio de estado, localização, garantias e contratos de manutenção.') ?></p>
        </article>

        <article>
            <i class="fa-solid fa-folder-open fa-3x" style="color: #4f46e5; margin-bottom: 15px;"></i>
            <h2><?= cp('solucao_titulo2', 'Arquivo Técnico e Contratos') ?></h2>
            <p><?= cpnl('solucao_conteudo2', 'Aloque manuais, certificados de calibração e apólices diretamente a cada equipamento, com alertas de validade e acesso imediato à documentação técnica.') ?></p>
        </article>

        <article>
            <i class="fa-solid fa-hospital fa-3x" style="color: #4f46e5; margin-bottom: 15px;"></i>
            <h2><?= cp('solucao_titulo3', 'Rastreabilidade Posicional') ?></h2>
            <p><?= cpnl('solucao_conteudo3', 'Mapeie a infraestrutura hospitalar com precisão, associando cada equipamento ao edifício, piso e serviço onde se encontra em tempo real.') ?></p>
        </article>
    </section>

    <section id="contacto">
        <h2>Contacto</h2>
        <p>Entre em contacto connosco para tirar as suas dúvidas ou solicitar uma demonstração da plataforma.</p>

        <?php if (($_GET['enviado'] ?? '') === '1'): ?>
        <p style="color: #16a34a; font-weight: 600; margin-bottom: 16px;">✓ Mensagem enviada com sucesso! Entraremos em contacto brevemente.</p>
        <?php elseif (($_GET['erro'] ?? '') === '1'): ?>
        <p style="color: #dc2626; font-weight: 600; margin-bottom: 16px;">✗ Erro ao enviar a mensagem. Verifique os campos e tente novamente.</p>
        <?php endif; ?>

        <form id="contactForm" method="post" action="/sibdas/1241028/medcore/public/processar_contacto.php">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" placeholder="Ex: Centro Hospitalar do Porto" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Ex: engenharia.biomedica@hospital.pt" required>

            <label for="mensagem">Mensagem:</label>
            <textarea id="mensagem" name="mensagem" rows="4" placeholder="Ex: Gostaria de solicitar uma demonstração das soluções de inventário..." required></textarea>

            <button type="submit">Enviar Mensagem</button>
        </form>
    </section>

    <footer class="footer-container">
        <div class="footer-section">
            <strong>LOCALIZAÇÃO</strong>
            <p><?= cpnl('contacto_morada', 'Rua Dr. António Bernardino de Almeida<br> 4200-072, Porto <br> Portugal') ?></p>
        </div>
        <div class="footer-section">
            <strong>SUPORTE TÉCNICO</strong>
            <p><?= cpnl('contacto_horarios', '2ª a 6ª Feira: 8h — 18h<br>Sábados: 9h — 13h<br>Piquete de Prevenção: 24h / 7 dias') ?></p>
        </div>
        <div class="footer-section">
            <strong>CONTACTOS</strong>
            <p>Email: <?= cp('contacto_email', 'suporte@medcore.pt') ?></p>
            <p>Telefone: <?= cp('contacto_telefone', '+351 226 811 298') ?></p>
        </div>
    </footer>

</body>
</html>
