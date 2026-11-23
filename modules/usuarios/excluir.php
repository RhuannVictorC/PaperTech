
<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/csrf.php';

exigirAdministrador();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

if (!validarCSRF()) {
    http_response_code(403);
    exit('Token CSRF inválido. Operação não autorizada.');
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    header('Location: index.php?erro=id_invalido');
    exit;
}

// Impede excluir a própria conta
if ($id == $_SESSION['usuario_id']) {
    header('Location: index.php?erro=propria_conta');
    exit;
}

try {
    // Inicia transação para proteger as verificações
    $pdo->beginTransaction();

    // Busca e bloqueia o usuário durante a operação
    $stmt = $pdo->prepare(
        "SELECT id, tipo FROM usuarios
         WHERE id = :id
         FOR UPDATE"
    );

    $stmt->execute([':id' => $id]);
    $usuario = $stmt->fetch();

    if (!$usuario) {
        $pdo->rollBack();
        header('Location: index.php?erro=nao_encontrado');
        exit;
    }

    // Impede excluir o último administrador
    if ($usuario['tipo'] === 'admin') {
        $stmt = $pdo->query(
            "SELECT COUNT(*) FROM usuarios WHERE tipo = 'admin'"
        );

        $totalAdmins = (int) $stmt->fetchColumn();

        if ($totalAdmins <= 1) {
            $pdo->rollBack();
            header('Location: index.php?erro=ultimo_admin');
            exit;
        }
    }

    // Executa a exclusão
    $stmt = $pdo->prepare(
        "DELETE FROM usuarios WHERE id = :id"
    );

    $stmt->execute([':id' => $id]);

    $pdo->commit();

    header('Location: index.php?excluido=1');
    exit;

} catch (PDOException $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(500);
    exit('Não foi possível excluir o usuário.');
}
