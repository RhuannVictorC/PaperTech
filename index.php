<?php

require_once __DIR__ . '/includes/auth.php';

if (isset($_SESSION['usuario_id'])) {
    header('Location: /PaperTech/public/dashboard.php');
    exit;
}

header('Location: /PaperTech/public/login.php');
exit;
