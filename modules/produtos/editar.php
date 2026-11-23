
<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/csrf.php';

exigirLogin();

$erro = '';
$produto = null;

// Valida o ID recebido pela URL
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {

    http_response_code(400);
    $erro = 'ID de produto inválido.';

} else {

    $stmt = $pdo->prepare(
        "SELECT id, nome, descricao, categoria, marca,
                preco, quantidade_estoque
         FROM produtos
         WHERE id = :id"
    );

    $stmt->execute([':id' => $id]);
    $produto = $stmt->fetch();

    if (!$produto) {
        http_response_code(404);
        $erro = 'Produto não encontrado.';
    }
}

// Processa a edição
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $produto) {

    if (!validarCSRF()) {

        http_response_code(403);
        $erro = 'Token de segurança inválido. Atualize a página.';

    } else {

        $nome = trim($_POST['nome'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        $categoria = trim($_POST['categoria'] ?? '');
        $marca = trim($_POST['marca'] ?? '');
        $preco = trim($_POST['preco'] ?? '');
        $quantidade = trim($_POST['quantidade_estoque'] ?? '');

        if (
            $nome === '' ||
            $categoria === '' ||
            $preco === '' ||
            $quantidade === ''
        ) {

            $erro = 'Preencha todos os campos obrigatórios.';

        } elseif (mb_strlen($nome) > 150) {

            $erro = 'O nome deve ter no máximo 150 caracteres.';

        } elseif (mb_strlen($categoria) > 100) {

            $erro = 'A categoria deve ter no máximo 100 caracteres.';

        } elseif (mb_strlen($marca) > 100) {

            $erro = 'A marca deve ter no máximo 100 caracteres.';

        } elseif (!preg_match('/^\d+(\.\d{1,2})?$/', $preco)) {

            $erro = 'Informe um preço válido, com até duas casas decimais.';

        } elseif (
            !preg_match('/^\d+$/', $quantidade) ||
            (float) $quantidade > 2147483647
        ) {

            $erro = 'Informe uma quantidade inteira válida e não negativa.';

        } else {

            try {

                $sql = "UPDATE produtos
                        SET nome = :nome,
                            descricao = :descricao,
                            categoria = :categoria,
                            marca = :marca,
                            preco = :preco,
                            quantidade_estoque = :quantidade
                        WHERE id = :id";

                $stmt = $pdo->prepare($sql);

                $stmt->execute([
                    ':nome' => $nome,
                    ':descricao' => $descricao !== '' ? $descricao : null,
                    ':categoria' => $categoria,
                    ':marca' => $marca !== '' ? $marca : null,
                    ':preco' => number_format((float) $preco, 2, '.', ''),
                    ':quantidade' => (int) $quantidade,
                    ':id' => $id
                ]);

                header('Location: index.php?editado=1');
                exit;

            } catch (PDOException $e) {

                $erro = 'Não foi possível atualizar o produto.';

            }
        }

        // Preserva os dados digitados caso ocorra algum erro
        $produto['nome'] = $nome;
        $produto['descricao'] = $descricao;
        $produto['categoria'] = $categoria;
        $produto['marca'] = $marca;
        $produto['preco'] = $preco;
        $produto['quantidade_estoque'] = $quantidade;
    }
}

$paginaTitulo = 'Editar Produto';
?>

<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<div class="main-container">

    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

    <main class="content">

        <div class="products-page-header">

            <div>
                <span class="products-eyebrow">GERENCIAMENTO</span>
                <h2>Editar Produto</h2>
                <p>Atualize as informações do produto cadastrado no PaperTech.</p>
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

        <?php endif; ?>

        <?php if ($produto): ?>

            <div class="card product-form-card">

                <div class="card-header product-form-header">
                    <div>
                        <h5>Informações do produto</h5>
                        <small>Altere os campos necessários e salve as modificações.</small>
                    </div>
                </div>

                <div class="card-body">

                    <form method="POST">

                        <?= campoCSRF() ?>

                        <div class="row">

                            <div class="col-md-12 mb-4">
                                <label class="form-label">Nome do produto *</label>

                                <input
                                    type="text"
                                    name="nome"
                                    class="form-control"
                                    maxlength="150"
                                    value="<?= htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8') ?>"
                                    required
                                >
                            </div>

                            <div class="col-md-12 mb-4">
                                <label class="form-label">Descrição</label>

                                <textarea
                                    name="descricao"
                                    class="form-control"
                                    rows="4"
                                ><?= htmlspecialchars($produto['descricao'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label">Categoria *</label>

                                <input
                                    type="text"
                                    name="categoria"
                                    class="form-control"
                                    maxlength="100"
                                    value="<?= htmlspecialchars($produto['categoria'], ENT_QUOTES, 'UTF-8') ?>"
                                    required
                                >
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label">Marca</label>

                                <input
                                    type="text"
                                    name="marca"
                                    class="form-control"
                                    maxlength="100"
                                    value="<?= htmlspecialchars($produto['marca'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                >
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label">Preço (R$) *</label>

                                <input
                                    type="number"
                                    name="preco"
                                    class="form-control"
                                    min="0"
                                    step="0.01"
                                    value="<?= htmlspecialchars((string) $produto['preco'], ENT_QUOTES, 'UTF-8') ?>"
                                    required
                                >
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label">Quantidade em estoque *</label>

                                <input
                                    type="number"
                                    name="quantidade_estoque"
                                    class="form-control"
                                    min="0"
                                    step="1"
                                    value="<?= htmlspecialchars((string) $produto['quantidade_estoque'], ENT_QUOTES, 'UTF-8') ?>"
                                    required
                                >
                            </div>

                        </div>

                        <div class="product-form-footer">

                            <a href="index.php" class="btn btn-light product-cancel-button">
                                Cancelar
                            </a>

                            <button type="submit" class="btn btn-primary product-submit-button">
                                <i class="bi bi-check-circle me-1"></i>
                                Salvar alterações
                            </button>

                        </div>

                    </form>

                </div>
            </div>

        <?php endif; ?>

    </main>

</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
