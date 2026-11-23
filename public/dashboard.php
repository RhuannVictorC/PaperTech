
<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

exigirLogin();

// Consultar quantidade de usuários
$sqlUsuarios = "SELECT COUNT(*) FROM usuarios";
$totalUsuarios = $pdo->query($sqlUsuarios)->fetchColumn();

// Consultar quantidade de produtos
$sqlProdutos = "SELECT COUNT(*) FROM produtos";
$totalProdutos = $pdo->query($sqlProdutos)->fetchColumn();

// Título da página
$paginaTitulo = 'Dashboard';

?>

<?php require_once __DIR__ . '/../includes/header.php'; ?>

<div class="main-container">

    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

    <main class="content">

        <!-- Cabeçalho do dashboard -->
        <div class="dashboard-header mb-4">

            <div>
                <span class="dashboard-eyebrow">PAINEL DE CONTROLE</span>

                <h2 class="dashboard-title">
                    Dashboard
                </h2>

                <p class="dashboard-subtitle">
                    Bem-vindo ao painel administrativo do PaperTech.
                    Aqui você acompanha um resumo do sistema.
                </p>
            </div>

            <div class="dashboard-status">
                <span class="status-dot"></span>
                Sistema ativo
            </div>

        </div>

        <!-- Indicadores -->
        <div class="row g-4">

            <!-- Card de usuários -->
            <div class="col-md-6 col-xl-4">

                <div class="card dashboard-card h-100">

                    <div class="card-body p-4">

                        <div class="dashboard-card-top">

                            <div>
                                <span class="dashboard-card-label">
                                    Usuários cadastrados
                                </span>

                                <div class="dashboard-number">
                                    <?= (int) $totalUsuarios ?>
                                </div>
                            </div>

                            <div class="dashboard-icon icon-users">

                                <svg xmlns="http://www.w3.org/2000/svg"
                                    width="25" height="25"
                                    viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                    stroke-linejoin="round">

                                    <path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                    <circle cx="10" cy="7" r="4"/>
                                    <path d="M20 21v-2a4 4 0 0 0-3-3.87"/>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>

                                </svg>

                            </div>

                        </div>

                        <div class="dashboard-card-footer">
                            Total de contas registradas no sistema
                        </div>

                    </div>

                </div>

            </div>

            <!-- Card de produtos -->
            <div class="col-md-6 col-xl-4">

                <div class="card dashboard-card h-100">

                    <div class="card-body p-4">

                        <div class="dashboard-card-top">

                            <div>
                                <span class="dashboard-card-label">
                                    Produtos cadastrados
                                </span>

                                <div class="dashboard-number">
                                    <?= (int) $totalProdutos ?>
                                </div>
                            </div>

                            <div class="dashboard-icon icon-products">

                                <svg xmlns="http://www.w3.org/2000/svg"
                                    width="25" height="25"
                                    viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                    stroke-linejoin="round">

                                    <path d="m21 8-9-5-9 5v8l9 5 9-5z"/>
                                    <path d="m3.3 7.5 8.7 5 8.7-5"/>
                                    <path d="M12 22V12.5"/>
                                    <path d="m7.5 5.5 9 5"/>

                                </svg>

                            </div>

                        </div>

                        <div class="dashboard-card-footer">
                            Total de produtos registrados no sistema
                        </div>

                    </div>

                </div>

            </div>

        </div>

        <!-- Informações do sistema -->
        <div class="card dashboard-info-card mt-4">

            <div class="card-body p-4">

                <div class="dashboard-info-content">

                    <div class="dashboard-info-icon">

                        <svg xmlns="http://www.w3.org/2000/svg"
                            width="27" height="27"
                            viewBox="0 0 24 24"
                            fill="none" stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                            stroke-linejoin="round">

                            <circle cx="12" cy="12" r="10"/>
                            <path d="M12 16v-4"/>
                            <path d="M12 8h.01"/>

                        </svg>

                    </div>

                    <div>
                        <h5 class="dashboard-info-title">
                            Informações do sistema
                        </h5>

                        <p class="dashboard-info-text mb-0">
                            Utilize o menu lateral para acessar as
                            funcionalidades disponíveis no PaperTech.
                        </p>
                    </div>

                </div>

            </div>

        </div>

        <!-- Destaque do PaperBot -->
        <div class="paperbot-banner mt-4">

            <div class="paperbot-banner-icon">

                <svg xmlns="http://www.w3.org/2000/svg"
                    width="28" height="28"
                    viewBox="0 0 24 24"
                    fill="none" stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round">

                    <path d="M21 11.5a8.4 8.4 0 0 1-.9 3.8
                    8.5 8.5 0 0 1-7.6 4.7
                    8.4 8.4 0 0 1-3.8-.9L3 21l1.9-5.7
                    a8.4 8.4 0 0 1-.9-3.8
                    8.5 8.5 0 0 1 4.7-7.6
                    8.4 8.4 0 0 1 3.8-.9h.5
                    a8.5 8.5 0 0 1 8 8v.5z"/>

                </svg>

            </div>

            <div class="paperbot-banner-text">
                <h5>Conheça o PaperBot</h5>

                <p>
                    Precisa de ajuda para utilizar o PaperTech?
                    Converse com nosso assistente virtual.
                </p>
            </div>

            <a href="/PaperTech/modules/paperbot/index.php"
                class="btn paperbot-button">
                Acessar assistente
            </a>

        </div>

    </main>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
