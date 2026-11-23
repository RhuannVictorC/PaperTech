
<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/csrf.php';

exigirLogin();

// Busca todos os produtos cadastrados
$sql = "SELECT id, nome, descricao, categoria, marca,
               preco, quantidade_estoque, data_cadastro
        FROM produtos
        ORDER BY id DESC";

$stmt = $pdo->query($sql);
$produtos = $stmt->fetchAll();

// Mensagens do sistema
$mensagem = '';
$tipoMensagem = 'info';

if (isset($_GET['cadastrado'])) {
    $mensagem = 'Produto cadastrado com sucesso!';
    $tipoMensagem = 'success';

} elseif (isset($_GET['editado'])) {
    $mensagem = 'Produto atualizado com sucesso!';
    $tipoMensagem = 'success';

} elseif (isset($_GET['excluido'])) {
    $mensagem = 'Produto excluído com sucesso!';
    $tipoMensagem = 'success';

} elseif (isset($_GET['erro'])) {

    if ($_GET['erro'] === 'id_invalido') {
        $mensagem = 'ID do produto inválido.';

    } elseif ($_GET['erro'] === 'nao_encontrado') {
        $mensagem = 'Produto não encontrado.';
    }

    $tipoMensagem = 'danger';
}

$paginaTitulo = 'Gerenciar Produtos';

?>

<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<div class="main-container">

    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

    <main class="content">

        <!-- Cabeçalho -->
        <div class="products-header mb-4">

            <div>
                <span class="dashboard-eyebrow">
                    GERENCIAMENTO
                </span>

                <h2 class="dashboard-title">
                    Produtos
                </h2>

                <p class="dashboard-subtitle">
                    Consulte, cadastre e gerencie os produtos do PaperTech.
                </p>
            </div>

            <a href="cadastrar.php" class="btn products-new-button">

                <svg xmlns="http://www.w3.org/2000/svg"
                    width="19" height="19"
                    viewBox="0 0 24 24"
                    fill="none" stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round">

                    <path d="M12 5v14"/>
                    <path d="M5 12h14"/>

                </svg>

                Novo Produto

            </a>

        </div>

        <!-- Mensagem do sistema -->
        <?php if ($mensagem !== ''): ?>

            <div class="alert alert-<?= $tipoMensagem ?> alert-dismissible fade show products-alert">

                <?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?>

                <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Fechar">
                </button>

            </div>

        <?php endif; ?>

        <!-- Resumo da listagem -->
        <div class="products-summary mb-3">

            <div class="products-summary-icon">

                <svg xmlns="http://www.w3.org/2000/svg"
                    width="23" height="23"
                    viewBox="0 0 24 24"
                    fill="none" stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round">

                    <path d="m21 8-9-5-9 5v8l9 5 9-5z"/>
                    <path d="m3.3 7.5 8.7 5 8.7-5"/>
                    <path d="M12 22V12.5"/>

                </svg>

            </div>

            <div>
                <span class="products-summary-label">
                    Produtos cadastrados
                </span>

                <strong class="products-summary-number">
                    <?= count($produtos) ?>
                </strong>
            </div>

        </div>

        <!-- Tabela de produtos -->
        <div class="card products-table-card">

            <div class="card-body p-0">

                <div class="products-table-heading">
                    <div>
                        <h5>Lista de produtos</h5>
                        <p>Visualize e gerencie os registros cadastrados.</p>
                    </div>
                </div>

                <div class="table-responsive">

                    <table class="table products-table align-middle mb-0">

                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Produto</th>
                                <th>Categoria</th>
                                <th>Marca</th>
                                <th>Preço</th>
                                <th>Estoque</th>
                                <th>Cadastro</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php if (count($produtos) > 0): ?>

                                <?php foreach ($produtos as $produto): ?>

                                    <tr>

                                        <td class="products-id">
                                            #<?= (int) $produto['id'] ?>
                                        </td>

                                        <td>
                                            <span class="products-name">
                                                <?= htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                        </td>

                                        <td>
                                            <span class="products-category">
                                                <?= htmlspecialchars($produto['categoria'], ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($produto['marca'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                        </td>

                                        <td class="products-price">
                                            R$ <?= number_format((float) $produto['preco'], 2, ',', '.') ?>
                                        </td>

                                        <td>
                                            <span class="products-stock">
                                                <?= (int) $produto['quantidade_estoque'] ?>
                                            </span>
                                        </td>

                                        <td class="products-date">
                                            <?= date('d/m/Y H:i', strtotime($produto['data_cadastro'])) ?>
                                        </td>

                                        <td>

                                            <div class="products-actions">

                                                <a href="visualizar.php?id=<?= (int) $produto['id'] ?>"
                                                    class="products-action action-view"
                                                    title="Visualizar produto"
                                                    aria-label="Visualizar produto">

                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        width="17" height="17"
                                                        viewBox="0 0 24 24"
                                                        fill="none" stroke="currentColor"
                                                        stroke-width="2"
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round">

                                                        <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"/>
                                                        <circle cx="12" cy="12" r="3"/>

                                                    </svg>

                                                </a>

                                                <a href="editar.php?id=<?= (int) $produto['id'] ?>"
                                                    class="products-action action-edit"
                                                    title="Editar produto"
                                                    aria-label="Editar produto">

                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        width="17" height="17"
                                                        viewBox="0 0 24 24"
                                                        fill="none" stroke="currentColor"
                                                        stroke-width="2"
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round">

                                                        <path d="M12 20h9"/>
                                                        <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4z"/>

                                                    </svg>

                                                </a>

                                                <form method="POST"
                                                    action="excluir.php"
                                                    onsubmit="return confirm('Tem certeza de que deseja excluir este produto?');">

                                                    <?= campoCSRF() ?>

                                                    <input type="hidden"
                                                        name="id"
                                                        value="<?= (int) $produto['id'] ?>">

                                                    <button type="submit"
                                                        class="products-action action-delete"
                                                        title="Excluir produto"
                                                        aria-label="Excluir produto">

                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            width="17" height="17"
                                                            viewBox="0 0 24 24"
                                                            fill="none" stroke="currentColor"
                                                            stroke-width="2"
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round">

                                                            <path d="M3 6h18"/>
                                                            <path d="M8 6V4h8v2"/>
                                                            <path d="m19 6-1 14H6L5 6"/>
                                                            <path d="M10 11v5"/>
                                                            <path d="M14 11v5"/>

                                                        </svg>

                                                    </button>

                                                </form>

                                            </div>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            <?php else: ?>

                                <tr>
                                    <td colspan="8">

                                        <div class="products-empty">

                                            <div class="products-empty-icon">

                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    width="34" height="34"
                                                    viewBox="0 0 24 24"
                                                    fill="none" stroke="currentColor"
                                                    stroke-width="1.5"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round">

                                                    <path d="m21 8-9-5-9 5v8l9 5 9-5z"/>
                                                    <path d="M12 22V12"/>

                                                </svg>

                                            </div>

                                            <h5>Nenhum produto cadastrado</h5>

                                            <p>
                                                Cadastre seu primeiro produto para começar.
                                            </p>

                                            <a href="cadastrar.php"
                                                class="btn btn-primary">
                                                Cadastrar produto
                                            </a>

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
