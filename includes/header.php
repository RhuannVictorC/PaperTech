
<?php

$paginaTitulo = $paginaTitulo ?? 'PaperTech';

$nomeUsuario = $_SESSION['usuario_nome'] ?? 'Usuário';

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?= htmlspecialchars($paginaTitulo, ENT_QUOTES, 'UTF-8') ?> | PaperTech
    </title>

    <!-- Bootstrap 5 -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

    <!-- CSS personalizado -->
    <link
        href="/PaperTech/public/assets/css/style.css?v=1"
        rel="stylesheet"
    >

</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">

        <div class="container-fluid px-4">

            <a class="navbar-brand fw-bold" href="/PaperTech/public/dashboard.php">
                PaperTech
            </a>

            <div class="d-flex align-items-center gap-3">

                <span class="text-white">
                    <?= htmlspecialchars($nomeUsuario, ENT_QUOTES, 'UTF-8') ?>
                </span>

                <a href="/PaperTech/public/logout.php" class="btn btn-light btn-sm">
                    Sair
                </a>

            </div>

        </div>

    </nav>
