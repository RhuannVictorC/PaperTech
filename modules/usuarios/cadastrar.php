
<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/csrf.php';

// Somente administradores podem cadastrar usuários
exigirAdministrador();

$erro = '';
$nome = '';
$email = '';
$tipo = 'funcionario';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!validarCSRF()) {

        http_response_code(403);
        $erro = 'Token de segurança inválido. Atualize a página e tente novamente.';

    } else {

        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';
        $confirmarSenha = $_POST['confirmar_senha'] ?? '';
        $tipo = $_POST['tipo'] ?? '';

        if (
            empty($nome) ||
            empty($email) ||
            empty($senha) ||
            empty($confirmarSenha) ||
            empty($tipo)
        ) {

            $erro = 'Preencha todos os campos.';

        } elseif (strlen($nome) > 150) {

            $erro = 'O nome deve ter no máximo 150 caracteres.';

        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

            $erro = 'Informe um endereço de e-mail válido.';

        } elseif (strlen($email) > 150) {

            $erro = 'O e-mail deve ter no máximo 150 caracteres.';

        } elseif (!in_array($tipo, ['admin', 'funcionario'], true)) {

            $erro = 'Selecione um tipo de usuário válido.';

        } elseif (strlen($senha) < 8) {

            $erro = 'A senha deve possuir pelo menos 8 caracteres.';

        } elseif ($senha !== $confirmarSenha) {

            $erro = 'As senhas informadas não coincidem.';

        } else {

            try {

                $sqlVerificar = "SELECT id
                                 FROM usuarios
                                 WHERE email = :email";

                $verificar = $pdo->prepare($sqlVerificar);

                $verificar->execute([
                    ':email' => $email
                ]);

                if ($verificar->fetch()) {

                    $erro = 'Este e-mail já está cadastrado.';

                } else {

                    $senhaHash = password_hash(
                        $senha,
                        PASSWORD_DEFAULT
                    );

                    $sql = "INSERT INTO usuarios
                            (nome, email, senha, tipo)
                            VALUES
                            (:nome, :email, :senha, :tipo)";

                    $stmt = $pdo->prepare($sql);

                    $stmt->execute([
                        ':nome' => $nome,
                        ':email' => $email,
                        ':senha' => $senhaHash,
                        ':tipo' => $tipo
                    ]);

                    header('Location: cadastrar.php?sucesso=1');
                    exit;

                }

            } catch (PDOException $e) {

                $erro = 'Não foi possível cadastrar o usuário. Tente novamente.';

            }

        }

    }

}

$sucesso = isset($_GET['sucesso']);

$paginaTitulo = 'Cadastrar Usuário';

?>

<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<div class="main-container">

    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

    <main class="content">

        <div class="page-header user-page-header">

            <div>
                <span class="page-eyebrow">GERENCIAMENTO</span>

                <h2>Cadastrar Usuário</h2>

                <p>
                    Preencha os dados abaixo para adicionar um novo usuário ao PaperTech.
                </p>
            </div>

            <a href="index.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i>
                Voltar
            </a>

        </div>

        <?php if ($sucesso): ?>

            <div class="alert alert-success">
                <i class="bi bi-check-circle-fill me-2"></i>
                Usuário cadastrado com sucesso!
            </div>

        <?php endif; ?>

        <?php if ($erro !== ''): ?>

            <div class="alert alert-danger">
                <i class="bi bi-exclamation-circle-fill me-2"></i>
                <?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?>
            </div>

        <?php endif; ?>

        <div class="user-form-card">

            <div class="user-form-card-header">

                <div class="d-flex align-items-center gap-3">

                    <div class="user-form-icon">
                        <i class="bi bi-person-plus"></i>
                    </div>

                    <div>
                        <h5>Informações do usuário</h5>

                        <p>
                            Os campos marcados com * são obrigatórios.
                        </p>
                    </div>

                </div>

            </div>

            <form method="POST" action="" class="user-form">

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
                        value="<?= htmlspecialchars($nome, ENT_QUOTES, 'UTF-8') ?>"
                        placeholder="Ex.: João da Silva"
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
                        value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"
                        placeholder="exemplo@email.com"
                        autocomplete="email"
                        required
                    >

                </div>

                <div class="row">

                    <div class="col-md-6 mb-4">

                        <label for="senha" class="form-label">
                            Senha *
                        </label>

                        <input
                            type="password"
                            id="senha"
                            name="senha"
                            class="form-control"
                            minlength="8"
                            placeholder="Mínimo de 8 caracteres"
                            autocomplete="new-password"
                            required
                        >

                        <small class="form-text">
                            A senha deve possuir pelo menos 8 caracteres.
                        </small>

                    </div>

                    <div class="col-md-6 mb-4">

                        <label for="confirmar_senha" class="form-label">
                            Confirmar senha *
                        </label>

                        <input
                            type="password"
                            id="confirmar_senha"
                            name="confirmar_senha"
                            class="form-control"
                            minlength="8"
                            placeholder="Digite a senha novamente"
                            autocomplete="new-password"
                            required
                        >

                    </div>

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
                            <?= $tipo === 'funcionario' ? 'selected' : '' ?>>
                            Funcionário
                        </option>

                        <option value="admin"
                            <?= $tipo === 'admin' ? 'selected' : '' ?>>
                            Administrador
                        </option>

                    </select>

                    <small class="form-text">
                        O tipo de conta determina as permissões de acesso ao sistema.
                    </small>

                </div>

                <div class="user-form-footer">

                    <a href="index.php" class="btn btn-outline-secondary">
                        Cancelar
                    </a>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-person-plus me-1"></i>
                        Cadastrar Usuário
                    </button>

                </div>

            </form>

        </div>

    </main>

</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
