
<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/csrf.php';

exigirAdministrador();

$erro = '';
$usuario = null;

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    http_response_code(400);
    $erro = 'ID de usuário inválido.';
} else {
    $stmt = $pdo->prepare(
        "SELECT id, nome, email, tipo FROM usuarios WHERE id = :id"
    );

    $stmt->execute([':id' => $id]);
    $usuario = $stmt->fetch();

    if (!$usuario) {
        http_response_code(404);
        $erro = 'Usuário não encontrado.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $usuario) {

    if (!validarCSRF()) {
        http_response_code(403);
        $erro = 'Token de segurança inválido. Atualize a página.';
    } else {
        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $tipo = $_POST['tipo'] ?? '';
        $senha = $_POST['senha'] ?? '';
        $confirmarSenha = $_POST['confirmar_senha'] ?? '';

        if (
            empty($nome) ||
            empty($email) ||
            empty($tipo)
        ) {
            $erro = 'Preencha todos os campos obrigatórios.';

        } elseif (strlen($nome) > 150) {
            $erro = 'O nome deve ter no máximo 150 caracteres.';

        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erro = 'Informe um e-mail válido.';

        } elseif (strlen($email) > 150) {
            $erro = 'O e-mail deve ter no máximo 150 caracteres.';

        } elseif (!in_array($tipo, ['admin', 'funcionario'], true)) {
            $erro = 'Tipo de usuário inválido.';

        } elseif (
            $senha !== '' &&
            strlen($senha) < 8
        ) {
            $erro = 'A nova senha deve possuir pelo menos 8 caracteres.';

        } elseif ($senha !== $confirmarSenha) {
            $erro = 'As senhas não coincidem.';

        } elseif (
            $usuario['id'] == $_SESSION['usuario_id'] &&
            $tipo !== 'admin'
        ) {
            $erro = 'Você não pode remover seu próprio acesso de administrador.';

        } else {
            try {
                // Verifica se o e-mail já pertence a outro usuário
                $stmt = $pdo->prepare(
                    "SELECT id FROM usuarios
                     WHERE email = :email AND id != :id"
                );

                $stmt->execute([
                    ':email' => $email,
                    ':id' => $id
                ]);

                if ($stmt->fetch()) {
                    $erro = 'Este e-mail já está sendo utilizado.';
                } else {

                    // Impede remover o último administrador
                    if ($usuario['tipo'] === 'admin' && $tipo !== 'admin') {
                        $stmt = $pdo->prepare(
                            "SELECT COUNT(*) FROM usuarios
                             WHERE tipo = 'admin' AND id != :id"
                        );

                        $stmt->execute([':id' => $id]);

                        if ((int) $stmt->fetchColumn() === 0) {
                            $erro = 'Não é possível remover o último administrador.';
                        }
                    }

                    if ($erro === '') {
                        if ($senha !== '') {
                            $senhaHash = password_hash(
                                $senha,
                                PASSWORD_DEFAULT
                            );

                            $sql = "UPDATE usuarios
                                    SET nome = :nome,
                                        email = :email,
                                        tipo = :tipo,
                                        senha = :senha
                                    WHERE id = :id";

                            $stmt = $pdo->prepare($sql);

                            $stmt->execute([
                                ':nome' => $nome,
                                ':email' => $email,
                                ':tipo' => $tipo,
                                ':senha' => $senhaHash,
                                ':id' => $id
                            ]);

                        } else {
                            $sql = "UPDATE usuarios
                                    SET nome = :nome,
                                        email = :email,
                                        tipo = :tipo
                                    WHERE id = :id";

                            $stmt = $pdo->prepare($sql);

                            $stmt->execute([
                                ':nome' => $nome,
                                ':email' => $email,
                                ':tipo' => $tipo,
                                ':id' => $id
                            ]);
                        }

                        // Atualiza os dados da sessão se for a própria conta
                        if ($usuario['id'] == $_SESSION['usuario_id']) {
                            $_SESSION['usuario_nome'] = $nome;
                            $_SESSION['usuario_email'] = $email;
                        }

                        header('Location: index.php?editado=1');
                        exit;
                    }
                }

            } catch (PDOException $e) {
                $erro = 'Não foi possível atualizar o usuário.';
            }
        }

        // Mantém os dados digitados em caso de erro
        $usuario['nome'] = $nome;
        $usuario['email'] = $email;
        $usuario['tipo'] = $tipo;
    }
}

$paginaTitulo = 'Editar Usuário';
?>

<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<div class="main-container">

    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

    <main class="content">

        <div class="page-header user-page-header">

            <div>
                <span class="page-eyebrow">GERENCIAMENTO</span>

                <h2>Editar Usuário</h2>

                <p>
                    Atualize as informações da conta selecionada.
                </p>
            </div>

            <a href="index.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i>
                Voltar
            </a>

        </div>

        <?php if ($erro !== ''): ?>

            <div class="alert alert-danger">
                <i class="bi bi-exclamation-circle-fill me-2"></i>
                <?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?>
            </div>

        <?php endif; ?>

        <?php if ($usuario): ?>

            <div class="user-form-card">

                <div class="user-form-card-header">

                    <div class="d-flex align-items-center gap-3">

                        <div class="user-form-icon">
                            <i class="bi bi-person-gear"></i>
                        </div>

                        <div>
                            <h5>Informações do usuário</h5>

                            <p>
                                Altere os dados da conta conforme necessário.
                            </p>
                        </div>

                    </div>

                </div>

                <form method="POST" class="user-form">

                    <?= campoCSRF() ?>

                    <div class="mb-4">

                        <label for="nome" class="form-label">
                            Nome completo *
                        </label>

                        <input
                            type="text"
                            id="nome"
                            name="nome"
                            class="form-control"
                            maxlength="150"
                            value="<?= htmlspecialchars($usuario['nome'], ENT_QUOTES, 'UTF-8') ?>"
                            autocomplete="name"
                            required
                        >

                    </div>

                    <div class="mb-4">

                        <label for="email" class="form-label">
                            E-mail *
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control"
                            maxlength="150"
                            value="<?= htmlspecialchars($usuario['email'], ENT_QUOTES, 'UTF-8') ?>"
                            autocomplete="email"
                            required
                        >

                    </div>

                    <div class="mb-4">

                        <label for="tipo" class="form-label">
                            Tipo de usuário *
                        </label>

                        <select
                            id="tipo"
                            name="tipo"
                            class="form-select"
                            required
                        >

                            <option value="funcionario"
                                <?= $usuario['tipo'] === 'funcionario' ? 'selected' : '' ?>>
                                Funcionário
                            </option>

                            <option value="admin"
                                <?= $usuario['tipo'] === 'admin' ? 'selected' : '' ?>>
                                Administrador
                            </option>

                        </select>

                        <small class="form-text">
                            O tipo de conta determina as permissões de acesso ao sistema.
                        </small>

                    </div>

                    <div class="user-password-section">

                        <div class="user-password-heading">

                            <div class="user-form-icon">
                                <i class="bi bi-shield-lock"></i>
                            </div>

                            <div>
                                <h5>Alterar senha</h5>

                                <p>
                                    Deixe os campos vazios para manter a senha atual.
                                </p>
                            </div>

                        </div>

                        <div class="row">

                            <div class="col-md-6 mb-4">

                                <label for="senha" class="form-label">
                                    Nova senha
                                </label>

                                <input
                                    type="password"
                                    id="senha"
                                    name="senha"
                                    class="form-control"
                                    minlength="8"
                                    placeholder="Mínimo de 8 caracteres"
                                    autocomplete="new-password"
                                >

                                <small class="form-text">
                                    Mínimo de 8 caracteres.
                                </small>

                            </div>

                            <div class="col-md-6 mb-4">

                                <label for="confirmar_senha" class="form-label">
                                    Confirmar nova senha
                                </label>

                                <input
                                    type="password"
                                    id="confirmar_senha"
                                    name="confirmar_senha"
                                    class="form-control"
                                    minlength="8"
                                    placeholder="Digite a senha novamente"
                                    autocomplete="new-password"
                                >

                            </div>

                        </div>

                    </div>

                    <div class="user-form-footer">

                        <a href="index.php" class="btn btn-outline-secondary">
                            Cancelar
                        </a>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>
                            Salvar alterações
                        </button>

                    </div>

                </form>

            </div>

        <?php endif; ?>

    </main>

</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
