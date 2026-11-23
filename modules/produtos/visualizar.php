
<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';

exigirLogin();

$erro = '';
$produto = null;

// Valida o ID recebido pela URL
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {

    http_response_code(400);
    $erro = 'ID de produto inválido.';

} else {

    $sql = "SELECT id, nome, descricao, categoria, marca,
                   preco, quantidade_estoque, data_cadastro
            FROM produtos
            WHERE id = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);

    $produto = $stmt->fetch();

    if (!$produto) {
        http_response_code(404);
        $erro = 'Produto não encontrado.';
    }
}

$paginaTitulo = 'Visualizar Produto';
?>

<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<div class="main-container">

    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

    <main class="content">

        <div class="products-page-header">

            <div>
                <span class="products-eyebrow">GERENCIAMENTO</span>
                <h2>Detalhes do Produto</h2>
                <p>Consulte todas as informações do produto cadastrado.</p>
            </div>

            <a href="index.php" class="btn btn-outline-secondary products-back-button">
                <i class="bi bi-arrow-left me-1"></i>
                Voltar
            </a>

        </div>

        <?php if ($erro): ?>

            <div class="alert alert-danger products-error">
                <?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?>
            </div>

        <?php else: ?>

            <div class="card product-details-card">

                <div class="card-header product-details-header">

                    <div>
                        <span class="product-details-label">PRODUTO CADASTRADO</span>

                        <h5>
                            <?= htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8') ?>
                        </h5>

                        <small>
                            Código do produto: #<?= (int) $produto['id'] ?>
                        </small>
                    </div>

                    <div class="product-details-icon">
                        <i class="bi bi-box-seam"></i>
                    </div>

                </div>

                <div class="card-body">

                    <div class="row g-4">

                        <div class="col-md-6">
                            <div class="product-detail-item">
                                <span>Nome do produto</span>
                                <strong>
                                    <?= htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8') ?>
                                </strong>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="product-detail-item">
                                <span>Categoria</span>
                                <strong>
                                    <?= htmlspecialchars($produto['categoria'], ENT_QUOTES, 'UTF-8') ?>
                                </strong>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="product-detail-item">
                                <span>Marca</span>
                                <strong>
                                    <?php if (!empty($produto['marca'])): ?>
                                        <?= htmlspecialchars($produto['marca'], ENT_QUOTES, 'UTF-8') ?>
                                    <?php else: ?>
                                        <span class="text-muted">Não informada</span>
                                    <?php endif; ?>
                                </strong>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="product-detail-item">
                                <span>Preço</span>
                                <strong class="product-detail-price">
                                    R$ <?= number_format(
                                        (float) $produto['preco'],
                                        2,
                                        ',',
                                        '.'
                                    ) ?>
                                </strong>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="product-detail-item">
                                <span>Quantidade em estoque</span>
                                <strong>
                                    <?= (int) $produto['quantidade_estoque'] ?> unidades
                                </strong>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="product-detail-item">
                                <span>Data de cadastro</span>
                                <strong>
                                    <?= date(
                                        'd/m/Y H:i',
                                        strtotime($produto['data_cadastro'])
                                    ) ?>
                                </strong>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="product-detail-item product-detail-description">
                                <span>Descrição</span>

                                <div>
                                    <?php if (!empty($produto['descricao'])): ?>
                                        <?= nl2br(htmlspecialchars(
                                            $produto['descricao'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        )) ?>
                                    <?php else: ?>
                                        <span class="text-muted">Não informada</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="product-details-footer">

                        <a href="index.php" class="btn btn-light product-cancel-button">
                            Voltar para produtos
                        </a>

                        <a
                            href="editar.php?id=<?= (int) $produto['id'] ?>"
                            class="btn btn-primary product-submit-button"
                        >
                            <i class="bi bi-pencil me-1"></i>
                            Editar Produto
                        </a>

                    </div>

                </div>

            </div>

        <?php endif; ?>

    </main>

</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
