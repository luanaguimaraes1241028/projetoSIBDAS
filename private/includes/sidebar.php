<?php $uri = $_SERVER['REQUEST_URI']; ?>

<aside class="col-md-3 col-lg-2 bg-white sidebar vh-100 border-end p-0">
                <div class="position-sticky pt-3">
                    <nav>
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link py-3 px-4 fw-bold <?php echo strpos($uri, '/dashboard/') !== false ? 'active text-primary bg-light border-start border-4 border-primary' : 'text-secondary'; ?>" href="/sibdas/1241028/medcore/private/dashboard/dashboard.php">
                                    <i class="fa-solid fa-chart-pie"></i> &ensp; Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-3 px-4 fw-bold <?php echo strpos($uri, '/equipamentos/') !== false ? 'active text-primary bg-light border-start border-4 border-primary' : 'text-secondary'; ?>" href="/sibdas/1241028/medcore/private/equipamentos/lista.php">
                                    <i class="fa-solid fa-stethoscope"></i> &ensp; Equipamentos
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-3 px-4 fw-bold <?php echo strpos($uri, '/localizacoes/') !== false ? 'active text-primary bg-light border-start border-4 border-primary' : 'text-secondary'; ?>" href="/sibdas/1241028/medcore/private/localizacoes/lista.php">
                                    <i class="fa-solid fa-hospital"></i> &ensp; Localizações
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-3 px-4 fw-bold <?php echo strpos($uri, '/fornecedores/') !== false ? 'active text-primary bg-light border-start border-4 border-primary' : 'text-secondary'; ?>" href="/sibdas/1241028/medcore/private/fornecedores/lista.php">
                                    <i class="fa-solid fa-truck-field"></i> &ensp; Fornecedores
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-3 px-4 fw-bold <?php echo strpos($uri, '/documentacao/') !== false ? 'active text-primary bg-light border-start border-4 border-primary' : 'text-secondary'; ?>" href="/sibdas/1241028/medcore/private/documentacao/lista.php">
                                    <i class="fa-solid fa-folder-open"></i> &ensp; Documentação
                                </a>
                            </li>
                            <?php if (($_SESSION['perfil'] ?? '') === 'admin'): ?>
                            <?php
                            $naoLidas = 0;
                            try {
                                $ligSidebar = ligar_bd();
                                if ($ligSidebar) {
                                    $naoLidas = (int) $ligSidebar->query("SELECT COUNT(*) FROM MensagemContacto WHERE lida = 0")->fetchColumn();
                                    $ligSidebar = null;
                                }
                            } catch (Exception $e) {}
                            ?>
                            <li class="nav-item">
                                <a class="nav-link py-3 px-4 fw-bold <?php echo strpos($uri, '/mensagens/') !== false ? 'active text-primary bg-light border-start border-4 border-primary' : 'text-secondary'; ?>" href="/sibdas/1241028/medcore/private/mensagens/lista.php">
                                    <i class="fa-solid fa-envelope"></i> &ensp; Mensagens
                                    <?php if ($naoLidas > 0): ?>
                                    <span class="badge bg-danger ms-1"><?= $naoLidas ?></span>
                                    <?php endif; ?>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-3 px-4 fw-bold <?php echo strpos($uri, '/gestao-public') !== false ? 'active text-primary bg-light border-start border-4 border-primary' : 'text-secondary'; ?>" href="/sibdas/1241028/medcore/private/gestao-public.php">
                                    <i class="fa-solid fa-globe"></i> &ensp; Gestão de Conteúdos
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            </aside>
