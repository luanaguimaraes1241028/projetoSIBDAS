<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedCore Inventory - Sistemas de Informação Hospitalar</title> 

    <link rel="shortcut icon" href="../assets/img/logo medcore.png" type="image/png">

    <link rel="stylesheet" href="../assets/css/1241028.css">

    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../assets/fontawesome/all.min.css">

</head>
<body>

    <nav class="bng-navbar">
        <div>
            <img src="../assets/img/logo medcore.png" alt="Logo MedCore Inventory">
            <h3>MedCore Inventory</h3>
        </div> 
        <div class="container-navegacao">
            <a href="#quem-somos">Sobre Nós</a>
            <a href="#solucoes">Soluções</a>
            <a href="#contacto">Suporte e Contactos</a>
        </div>
        <div class="nav-cliente">
            <a href="../login/login.html" target="_blank">Área Cliente</a>
        </div>
    </nav>

    <section class="container-texto-generico" id="quem-somos">

        <div class="quem-somos-content" style="width: 100%; text-align: center; margin-bottom: 50px;">
            <h1 style="font-size: 3em; color: #1e1b4b; margin-bottom: 20px;">Gestão Tecnológica Hospitalar Integrada</h1>
            <p style="font-size: 1.2em; color: #64748b; max-width: 800px; margin: 0 auto 30px auto;">A plataforma definitiva para Engenharia Clínica e controlo absoluto de ativos médicos.</p>
            
            <img src="../assets/img/foto front office.jpg" alt="Monitorização Hospitalar" style="width: 100%; max-width: 800px; height: 250px; object-fit: cover; border-radius: 8px; margin: 20px auto; display: block;">
            
            <a href="#contacto" class="button" style="display: inline-block; background-color: #4f46e5; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: 600; margin-top: 10px;">Solicitar Demonstração</a>
        </div>

        <article>
            <h2>Ecossistema Unificado</h2>
            <p>O MedCore Inventory centraliza a gestão de equipamentos médicos. Substituímos folhas de Excel dispersas e pastas físicas por uma <strong>plataforma única e digital</strong>, garantindo dados atualizados em tempo real.</p>
        </article>

        <article>
            <h2>Ciclo de Vida Clínico</h2>
            <p>Alinhado com as diretrizes da OMS, o sistema controla os dispositivos desde a aquisição até ao seu abate técnico, servindo de base para o <strong>planeamento, segurança e análise de risco</strong> hospitalar.</p>
        </article>

        <article>
            <h2>Rastreabilidade e Futuro</h2>
            <p>Garanta total controlo sobre a localização de ativos, fornecedores e contratos. Tudo assente numa estrutura de dados robusta, pronta para <strong>evoluuir para um sistema CMMS/GMAO</strong> completo.</p>
        </article>

    </section>

    <section class="container-texto-generico" id="solucoes">
        <article>
            <i class="fa-solid fa-heart-pulse fa-3x" style="color: #4f46e5; margin-bottom: 15px;"></i>
            <h2>Controlo de Ativos</h2>
            <p>Centralize o registo completo do parque tecnológico...</p>
        </article>

        <article>
            <i class="fa-solid fa-folder-open fa-3x" style="color: #4f46e5; margin-bottom: 15px;"></i>
            <h2>Arquivo Técnico e Contratos</h2>
            <p>Aloque manuais, certificados e apólices diretamente...</p>
        </article>

        <article>
            <i class="fa-solid fa-hospital fa-3x" style="color: #4f46e5; margin-bottom: 15px;"></i>
            <h2>Rastreabilidade Posicional</h2>
            <p>Mapeie a infraestrutura hospitalar com precisão...</p>
        </article>
    </section>

    <section id="contacto">
        <h2>Contacto</h2>
        <p>Entre em contacto connosco para tirar as suas dúvidas ou solicitar uma demonstração da plataforma.</p> 
        
        <form id="contactForm">
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
            <p>Rua Dr. António Bernardino de Almeida<br> 4200-072, Porto <br> Portugal</p>
        </div>
        <div class="footer-section">
            <strong>SUPORTE TÉCNICO</strong>
            <p>2ª a 6ª Feira: 8h — 18h</p>
            <p>Sábados: 9h — 13h</p>
            <p>Piquete de Prevenção: 24h / 7 dias</p>
        </div>
        <div class="footer-section">
            <strong>CONTACTOS</strong>
            <p>Email: suporte@medcore.pt</p>
            <p>Telefone: +351 226 811 298</p>
        </div>
    </footer>

</body>
</html>