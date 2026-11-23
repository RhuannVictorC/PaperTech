
<?php

// Identifica a página atual
$caminhoAtual = $_SERVER['PHP_SELF'] ?? '';

// Verifica a seção atual
$dashboardAtivo = strpos($caminhoAtual, '/public/dashboard.php') !== false;

$produtosAtivo = strpos($caminhoAtual, '/modules/produtos/') !== false;

$paperbotAtivo = strpos($caminhoAtual, '/modules/paperbot/') !== false;

$usuariosAtivo = strpos($caminhoAtual, '/modules/usuarios/') !== false;

?>

<aside class="sidebar p-3">

    <h6 class="text-uppercase text-secondary mb-3">
        Menu principal
    </h6>

    <ul class="nav nav-pills flex-column gap-2">

        <!-- Dashboard -->
        <li class="nav-item">

            <a href="/PaperTech/public/dashboard.php"
               class="nav-link <?= $dashboardAtivo ? 'active' : '' ?>">

                Dashboard

            </a>

        </li>

        <!-- Produtos -->
        <li class="nav-item">

            <a href="/PaperTech/modules/produtos/index.php"
               class="nav-link <?= $produtosAtivo ? 'active' : '' ?>">

                Produtos

            </a>

        </li>

        <!-- Usuários: somente administradores -->
        <?php if (($_SESSION['usuario_tipo'] ?? '') === 'admin'): ?>

            <li class="nav-item">

                <a href="/PaperTech/modules/usuarios/index.php"
                   class="nav-link <?= $usuariosAtivo ? 'active' : '' ?>">

                    Usuários

                </a>

            </li>

        <?php endif; ?>

        <!-- PaperBot -->
        <li class="nav-item">

            <a href="/PaperTech/modules/paperbot/index.php"
               class="nav-link <?= $paperbotAtivo ? 'active' : '' ?>">

                <span style="
                    width: 36px;
                    height: 36px;
                    background: #563d7c;
                    border-radius: 50%;
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    flex-shrink: 0;
                ">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="21"
                        height="21"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="white"
                        stroke-width="2"
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

                </span>

                <span>
                    PaperBot

                    <small class="d-block text-muted">
                        Assistente virtual
                    </small>
                </span>

            </a>

        </li>

    </ul>

</aside>
