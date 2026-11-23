
<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/csrf.php';

exigirAdministrador();

// Busca todos os usuários
$sql = "SELECT id, nome, email, tipo, data_cadastro
        FROM usuarios
        ORDER BY id DESC";

$stmt = $pdo->query($sql);
$usuarios = $stmt->fetchAll();

$paginaTitulo = 'Gerenciar Usuários';
?>

<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<div class="main-container">

    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

    <main class="content">

        <div class="users-page-header">

            <div>
                <span class="users-eyebrow">GERENCIAMENTO</span>
                <h2>Gerenciar Usuários</h2>
                <p>Consulte e administre os usuários cadastrados no PaperTech.</p>
            </div>

            <a href="cadastrar.php" class="btn btn-primary users-new-button">
                <i class="bi bi-person-plus me-1"></i>
                Novo Usuário
            </a>

        </div>

        <!-- Mensagens de retorno -->

        <?php if (isset($_GET['editado'])): ?>
            <div class="alert alert-success users-alert">
                <i class="bi bi-check-circle me-2"></i>
                Usuário atualizado com sucesso!
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['excluido'])): ?>
            <div class="alert alert-success users-alert">
                <i class="bi bi-check-circle me-2"></i>
                Usuário excluído com sucesso!
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['erro'])): ?>

            <?php
            $mensagens = [
                'id_invalido' => 'ID de usuário inválido.',
                'propria_conta' => 'Você não pode excluir sua própria conta.',
                'nao_encontrado' => 'Usuário não encontrado.',
                'ultimo_admin' => 'Não é possível excluir o último administrador.'
            ];

            $mensagemErro = $mensagens[$_GET['erro']] ?? 'Não foi possível realizar a operação.';
            ?>

            <div class="alert alert-danger users-alert">
                <i class="bi bi-exclamation-circle me-2"></i>
                <?= htmlspecialchars($mensagemErro, ENT_QUOTES, 'UTF-8') ?>
            </div>

        <?php endif; ?>

        <!-- Resumo de usuários -->

        <div class="users-summary-card">

            <div class="users-summary-icon">
                <i class="bi bi-people"></i>
            </div>

            <div>
                <span>Total de usuários</span>
                <strong><?= count($usuarios) ?></strong>
            </div>

        </div>

        <!-- Tabela de usuários -->

        <div class="card users-table-card">

            <div class="card-header users-table-header">
                <div>
                    <h5>Usuários cadastrados</h5>
                    <small>Lista de contas registradas no sistema.</small>
                </div>
            </div>

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table users-table align-middle">

                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>E-mail</th>
                                <th>Tipo de conta</th>
                                <th>Data de cadastro</th>
                                <th>Ações</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php if (count($usuarios) > 0): ?>

                                <?php foreach ($usuarios as $usuario): ?>

                                    <tr>

                                        <td>
                                            <span class="users-id">
                                                #<?= (int) $usuario['id'] ?>
                                            </span>
                                        </td>

                                        <td>
                                            <strong class="users-name">
                                                <?= htmlspecialchars($usuario['nome'], ENT_QUOTES, 'UTF-8') ?>
                                            </strong>
                                        </td>

                                        <td>
                                            <span class="users-email">
                                                <?= htmlspecialchars($usuario['email'], ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                        </td>

                                        <td>
                                            <?php if ($usuario['tipo'] === 'admin'): ?>

                                                <span class="users-role users-role-admin">
                                                    <i class="bi bi-shield-check me-1"></i>
                                                    Administrador
                                                </span>

                                            <?php else: ?>

                                                <span class="users-role users-role-employee">
                                                    <i class="bi bi-person me-1"></i>
                                                    Funcionário
                                                </span>

                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <span class="users-date">
                                                <?= date('d/m/Y H:i', strtotime($usuario['data_cadastro'])) ?>
                                            </span>
                                        </td>

                                        <td>

                                            <div class="users-actions">

                                                <a
                                                    href="visualizar.php?id=<?= (int) $usuario['id'] ?>"
                                                    class="users-action-button users-action-view"
                                                    title="Visualizar usuário"
                                                    aria-label="Visualizar usuário"
                                                >
                                                    <i class="bi bi-eye"></i>
                                                </a>

                                                <a
                                                    href="editar.php?id=<?= (int) $usuario['id'] ?>"
                                                    class="users-action-button users-action-edit"
                                                    title="Editar usuário"
                                                    aria-label="Editar usuário"
                                                >
                                                    <i class="bi bi-pencil"></i>
                                                </a>

                                                <form
                                                    method="POST"
                                                    action="excluir.php"
                                                    onsubmit="return confirm('Tem certeza de que deseja excluir este usuário?');"
                                                >

                                                    <?= campoCSRF() ?>

                                                    <input
                                                        type="hidden"
                                                        name="id"
                                                        value="<?= (int) $usuario['id'] ?>"
                                                    >

                                                    <button
                                                        type="submit"
                                                        class="users-action-button users-action-delete"
                                                        title="Excluir usuário"
                                                        aria-label="Excluir usuário"
                                                    >
                                                        <i class="bi bi-trash"></i>
                                                    </button>

                                                </form>

                                            </div>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            <?php else: ?>

                                <tr>
                                    <td colspan="6">

                                        <div class="users-empty-state">
                                            <i class="bi bi-people"></i>
                                            <strong>Nenhum usuário cadastrado</strong>
                                            <span>Os usuários cadastrados aparecerão aqui.</span>
                                        </div>

                                    </td>
                                </tr>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>
        </div>

    </main>

</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
