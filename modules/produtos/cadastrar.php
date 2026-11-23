
<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/csrf.php';

exigirLogin();

$erro = '';

$nome = '';
$descricao = '';
$categoria = '';
$marca = '';
$preco = '';
$quantidade = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!validarCSRF()) {
        http_response_code(403);
        $erro = 'Token de segurança inválido. Atualize a página e tente novamente.';

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

        } elseif (strlen($nome) > 150) {
            $erro = 'O nome deve ter no máximo 150 caracteres.';

        } elseif (strlen($categoria) > 100) {
            $erro = 'A categoria deve ter no máximo 100 caracteres.';

        } elseif (strlen($marca) > 100) {
            $erro = 'A marca deve ter no máximo 100 caracteres.';

        } elseif (!is_numeric($preco) || (float) $preco < 0) {
            $erro = 'Informe um preço válido e não negativo.';

        } elseif (
            !preg_match('/^\d+$/', $quantidade) ||
            (float) $quantidade > 2147483647
        ) {
            $erro = 'Informe uma quantidade inteira válida e não negativa.';

        } else {

            $precoFormatado = number_format((float) $preco, 2, '.', '');
            $quantidadeFormatada = (int) $quantidade;

            try {

                $sql = "INSERT INTO produtos
                        (nome, descricao, categoria, marca, preco, quantidade_estoque)
                        VALUES
                        (:nome, :descricao, :categoria, :marca, :preco, :quantidade)";

                $stmt = $pdo->prepare($sql);

                $stmt->execute([
                    ':nome' => $nome,
                    ':descricao' => $descricao !== '' ? $descricao : null,
                    ':categoria' => $categoria,
                    ':marca' => $marca !== '' ? $marca : null,
                    ':preco' => $precoFormatado,
                    ':quantidade' => $quantidadeFormatada
                ]);

                header('Location: index.php?cadastrado=1');
                exit;

            } catch (PDOException $e) {

                $erro = 'Não foi possível cadastrar o produto. Tente novamente.';

            }
        }
    }
}

$paginaTitulo = 'Cadastrar Produto';
?>

<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<div class="main-container">

    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

    <main class="content">

        <div class="products-page-header">

            <div>
                <span class="products-eyebrow">GERENCIAMENTO</span>
                <h2>Cadastrar Produto</h2>
                <p>Preencha os dados abaixo para adicionar um novo produto ao estoque.</p>
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

        <div class="card product-form-card">

            <div class="card-header product-form-header">
                <div>
                    <h5>Informações do produto</h5>
                    <small>Os campos marcados com * são obrigatórios.</small>
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
                                placeholder="Ex.: Notebook Dell"
                                value="<?= htmlspecialchars($nome, ENT_QUOTES, 'UTF-8') ?>"
                                required
                            >
                        </div>

                        <div class="col-md-12 mb-4">
                            <label class="form-label">Descrição</label>

                            <textarea
                                name="descricao"
                                class="form-control"
                                rows="4"
                                placeholder="Descreva as características do produto..."
                            ><?= htmlspecialchars($descricao, ENT_QUOTES, 'UTF-8') ?></textarea>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label">Categoria *</label>

                            <input
                                type="text"
                                name="categoria"
                                class="form-control"
                                maxlength="100"
                                placeholder="Ex.: Informática"
                                value="<?= htmlspecialchars($categoria, ENT_QUOTES, 'UTF-8') ?>"
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
                                placeholder="Ex.: Dell"
                                value="<?= htmlspecialchars($marca, ENT_QUOTES, 'UTF-8') ?>"
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
                                placeholder="0,00"
                                value="<?= htmlspecialchars($preco, ENT_QUOTES, 'UTF-8') ?>"
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
                                placeholder="Ex.: 10"
                                value="<?= htmlspecialchars($quantidade, ENT_QUOTES, 'UTF-8') ?>"
                                required
                            >
                        </div>

                    </div>

                    <div class="product-form-footer">

                        <a href="index.php" class="btn btn-light product-cancel-button">
                            Cancelar
                        </a>

                        <button type="submit" class="btn btn-primary product-submit-button">
                            <i class="bi bi-plus-circle me-1"></i>
                            Cadastrar Produto
                        </button>

                    </div>

                </form>

            </div>
        </div>

    </main>

</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
