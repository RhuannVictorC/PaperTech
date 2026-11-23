<?php

require_once __DIR__ . '/../includes/auth.php';

// Limpa todas as variáveis da sessão
$_SESSION = [];

// Obtém os parâmetros do cookie da sessão
$parametros = session_get_cookie_params();

// Invalida o cookie no navegador
if (ini_get('session.use_cookies')) {

    setcookie(
        session_name(),
        '',
        [
            'expires' => time() - 42000,
            'path' => $parametros['path'],
            'domain' => $parametros['domain'],
            'secure' => $parametros['secure'],
            'httponly' => $parametros['httponly'],
            'samesite' => $parametros['samesite'] ?? 'Lax'
        ]
    );

}

// Destrói a sessão
session_destroy();

// Redireciona para o login
header('Location: login.php');
exit;

?>
