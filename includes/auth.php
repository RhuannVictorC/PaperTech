
<?php

// Configurações de segurança da sessão

if (session_status() === PHP_SESSION_NONE) {

    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_strict_mode', '1');

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => false, // true quando utilizar HTTPS
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

    session_start();
}

// Verifica se o usuário está autenticado
function usuarioAutenticado() {

    return isset($_SESSION['usuario_id']);

}

// Impede acesso de usuários não autenticados
function exigirLogin() {
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: /PaperTech/public/login.php');
        exit;
    }
}

// Verifica se o usuário é administrador

function exigirAdministrador() {

    exigirLogin();

    if (($_SESSION['usuario_tipo'] ?? '') !== 'admin') {

        http_response_code(403);
        exit('Acesso negado. Você não possui permissão.');

    }

}


?>
