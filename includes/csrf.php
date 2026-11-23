
<?php

// Gera ou recupera o token CSRF da sessão
function gerarTokenCSRF() {

    if (empty($_SESSION['csrf_token'])) {

        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    }

    return $_SESSION['csrf_token'];

}

// Gera o campo oculto para formulários
function campoCSRF() {

    $token = htmlspecialchars(
        gerarTokenCSRF(),
        ENT_QUOTES,
        'UTF-8'
    );

    return '<input type="hidden" name="csrf_token" value="' . $token . '">';

}

// Valida o token enviado pelo formulário
function validarCSRF() {

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

        return false;

    }

    $tokenRecebido = $_POST['csrf_token'] ?? '';

    if (
        !is_string($tokenRecebido) ||
        empty($_SESSION['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $tokenRecebido)
    ) {

        return false;

    }

    return true;

}

?>
