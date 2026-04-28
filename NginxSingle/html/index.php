<?php
require_once 'crud.php';

$action = $_GET['action'] ?? 'list';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create'])) {
        createProduto($pdo, $_POST['nome'], $_POST['preco'], $_POST['quantidade']);
        $message = "Produto criado com sucesso!";
    } elseif (isset($_POST['update'])) {
        updateProduto($pdo, $_POST['id'], $_POST['nome'], $_POST['preco'], $_POST['quantidade']);
        $message = "Produto atualizado com sucesso!";
    } elseif (isset($_POST['delete'])) {
        deleteProduto($pdo, $_POST['id']);
        $message = "Produto deletado com sucesso!";
    }
}

$produtos = getProdutos($pdo);
$produtoEdit = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $produtoEdit = getProduto($pdo, $_GET['id']);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>CRUD Produtos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .container { margin-top: 50px; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mb-4">Gerenciamento de Produtos</h2>
        
        <?php if ($message): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-4">
                <h4><?= $produtoEdit ? 'Editar Produto' : 'Novo Produto' ?></h4>
                <form method="POST" action="index.php">
                    <?php if ($produtoEdit): ?>
                        <input type="hidden" name="id" value="<?= $produtoEdit['id'] ?>">
                    <?php endif; ?>
                    <div class="mb-3">
                        <label class="form-label">Nome</label>
                        <input type="text" name="nome" class="form-control" required value="<?= $produtoEdit ? htmlspecialchars($produtoEdit['nome']) : '' ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Preço</label>
                        <input type="number" step="0.01" name="preco" class="form-control" required value="<?= $produtoEdit ? htmlspecialchars($produtoEdit['preco']) : '' ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quantidade</label>
                        <input type="number" name="quantidade" class="form-control" required value="<?= $produtoEdit ? htmlspecialchars($produtoEdit['quantidade']) : '' ?>">
                    </div>
                    <?php if ($produtoEdit): ?>
                        <button type="submit" name="update" class="btn btn-warning">Atualizar</button>
                        <a href="index.php" class="btn btn-secondary">Cancelar</a>
                    <?php else: ?>
                        <button type="submit" name="create" class="btn btn-primary">Salvar</button>
                    <?php endif; ?>
                </form>
            </div>
            
            <div class="col-md-8">
                <h4>Lista de Produtos</h4>
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Preço</th>
                            <th>Qtd</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($produtos as $p): ?>
                        <tr>
                            <td><?= $p['id'] ?></td>
                            <td><?= htmlspecialchars($p['nome']) ?></td>
                            <td>R$ <?= number_format($p['preco'], 2, ',', '.') ?></td>
                            <td><?= $p['quantidade'] ?></td>
                            <td>
                                <a href="index.php?action=edit&id=<?= $p['id'] ?>" class="btn btn-sm btn-info text-white">Editar</a>
                                <form method="POST" action="index.php" class="d-inline">
                                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                    <button type="submit" name="delete" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja deletar este produto?')">Deletar</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (count($produtos) === 0): ?>
                        <tr>
                            <td colspan="5" class="text-center">Nenhum produto encontrado.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
