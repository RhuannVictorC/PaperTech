
<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/csrf.php';

exigirLogin();

// Somente POST pode executar exclusões
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Validação CSRF
if (!validarCSRF()) {
    http_response_code(403);
    exit('Token CSRF inválido. Operação não autorizada.');
}

// Validação do ID
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    header('Location: index.php?erro=id_invalido');
    exit;
}

try {

    // Exclui o produto
    $stmt = $pdo->prepare(
        "DELETE FROM produtos WHERE id = :id"
    );

    $stmt->execute([':id' => $id]);

    // Verifica se algum produto foi excluído
    if ($stmt->rowCount() === 0) {
        header('Location: index.php?erro=nao_encontrado');
        exit;
    }

    header('Location: index.php?excluido=1');
    exit;

} catch (PDOException $e) {

    http_response_code(500);
    exit('Não foi possível excluir o produto.');

}
