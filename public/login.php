<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

// Se já estiver conectado, direciona ao dashboard
if (usuarioAutenticado()) {

    header('Location: dashboard.php');
    exit;

}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (empty($email) || empty($senha)) {

        $erro = 'Preencha todos os campos.';

    } else {

        try {

            // Busca o usuário pelo e-mail
            $sql = "SELECT id, nome, email, senha, tipo
                    FROM usuarios
                    WHERE email = :email";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ':email' => $email
            ]);

            $usuario = $stmt->fetch();

            // Verifica a senha
            if ($usuario && password_verify($senha, $usuario['senha'])) {

                // Renova o identificador da sessão
                session_regenerate_id(true);

                // Armazena os dados necessários
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                $_SESSION['usuario_email'] = $usuario['email'];
                $_SESSION['usuario_tipo'] = $usuario['tipo'];

                // Redireciona ao dashboard
                header('Location: dashboard.php');
                exit;

            } else {

                $erro = 'E-mail ou senha incorretos.';

            }

        } catch (PDOException $e) {

            $erro = 'Não foi possível realizar o login. Tente novamente.';

        }

    }

}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | PaperTech</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

</head>

<body class="bg-light">

    <div class="container">

        <div class="row justify-content-center align-items-center min-vh-100">

            <div class="col-md-5 col-lg-4">

                <div class="card shadow-sm border-0">

                    <div class="card-body p-4">

                        <h2 class="text-center mb-2">
                            PaperTech
                        </h2>

                        <p class="text-center text-muted mb-4">
                            Acesse sua conta
                        </p>

                        <?php if (!empty($erro)): ?>

                            <div class="alert alert-danger">
                                <?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?>
                            </div>

                        <?php endif; ?>

                        <form method="POST" action="">

                            <div class="mb-3">

                                <label class="form-label">
                                    E-mail
                                </label>

                                <input
                                    type="email"
                                    name="email"
                                    class="form-control"
                                    placeholder="Digite seu e-mail"
                                    required
                                >

                            </div>

                            <div class="mb-3">

                                <label class="form-label">
                                    Senha
                                </label>

                                <input
                                    type="password"
                                    name="senha"
                                    class="form-control"
                                    placeholder="Digite sua senha"
                                    required
                                >

                            </div>

                            <button
                                type="submit"
                                class="btn btn-primary w-100"
                            >
                                Entrar
                            </button>

                        </form>

                    </div>

                </div>

                <p class="text-center text-muted mt-3">
                    Sistema de Gerenciamento de Papelaria
                </p>

            </div>

        </div>

    </div>

</body>

</html>
