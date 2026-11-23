
<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';

exigirAdministrador();

$erro = '';
$usuario = null;

// Verifica se o ID foi informado na URL
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    $erro = 'ID de usuário inválido.';
} else {

    // Busca os dados do usuário, sem consultar a senha
    $sql = "SELECT id, nome, email, tipo, data_cadastro
            FROM usuarios
            WHERE id = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);

    $usuario = $stmt->fetch();

    if (!$usuario) {
        $erro = 'Usuário não encontrado.';
    }
}

$paginaTitulo = 'Visualizar Usuário';
?>

<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<div class="main-container">

    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

    <main class="content">

        <!-- Cabeçalho -->
        <div class="page-header mb-4">

            <div>
                <span class="page-eyebrow">GERENCIAMENTO</span>

                <h2>Visualizar Usuário</h2>

                <p>
                    Consulte os detalhes da conta cadastrada no PaperTech.
                </p>
            </div>

            <a href="index.php" class="btn btn-outline-secondary">
                ← Voltar
            </a>

        </div>

        <?php if ($erro): ?>

            <div class="alert alert-danger">
                <?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?>
            </div>

        <?php else: ?>

            <!-- Cartão de informações -->
            <div class="user-details-card">

                <div class="user-details-header">

                    <div class="user-details-icon">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             width="23" height="23"
                             viewBox="0 0 24 24"
                             fill="none"
                             stroke="currentColor"
                             stroke-width="1.8"
                             stroke-linecap="round"
                             stroke-linejoin="round">
                            <circle cx="12" cy="8" r="4"/>
                            <path d="M5 21v-2a7 7 0 0 1 14 0v2"/>
                        </svg>
                    </div>

                    <div>
                        <h5>Informações do usuário</h5>
                        <p>Dados registrados na conta selecionada.</p>
                    </div>

                </div>

                <div class="user-details-body">

                    <div class="user-detail-item">

                        <span class="user-detail-label">
                            ID do usuário
                        </span>

                        <span class="user-detail-value">
                            #<?= (int) $usuario['id'] ?>
                        </span>

                    </div>

                    <div class="user-detail-item">

                        <span class="user-detail-label">
                            Nome completo
                        </span>

                        <span class="user-detail-value">
                            <?= htmlspecialchars(
                                $usuario['nome'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </span>

                    </div>

                    <div class="user-detail-item">

                        <span class="user-detail-label">
                            E-mail
                        </span>

                        <span class="user-detail-value">
                            <?= htmlspecialchars(
                                $usuario['email'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </span>

                    </div>

                    <div class="user-detail-item">

                        <span class="user-detail-label">
                            Tipo de usuário
                        </span>

                        <span class="user-detail-value">

                            <?php if ($usuario['tipo'] === 'admin'): ?>

                                <span class="badge bg-primary">
                                    Administrador
                                </span>

                            <?php else: ?>

                                <span class="badge bg-secondary">
                                    Funcionário
                                </span>

                            <?php endif; ?>

                        </span>

                    </div>

                    <div class="user-detail-item">

                        <span class="user-detail-label">
                            Data de cadastro
                        </span>

                        <span class="user-detail-value">
                            <?= date(
                                'd/m/Y H:i',
                                strtotime($usuario['data_cadastro'])
                            ) ?>
                        </span>

                    </div>

                </div>

                <div class="user-details-footer">

                    <a
                        href="editar.php?id=<?= (int) $usuario['id'] ?>"
                        class="btn btn-primary"
                    >
                        Editar Usuário
                    </a>

                    <a href="index.php" class="btn btn-outline-secondary">
                        Voltar à lista
                    </a>

                </div>

            </div>

        <?php endif; ?>

    </main>

</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
