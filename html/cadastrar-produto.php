<?php
require_once 'crud.php';

$nome = $_POST['nome'] ?? '';
$preco = $_POST['preco'] ?? '';
$quantidade = $_POST['quantidade'] ?? '';

createProduto($pdo, $nome, $preco, $quantidade);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Produto Cadastrado</title>
</head>
<body>
    <h1>Produto cadastrado com sucesso!</h1>
    <p>Nome: <?= htmlspecialchars($nome) ?></p>
    <p>Preço: <?= htmlspecialchars($preco) ?></p>
    <p>Quantidade: <?= htmlspecialchars($quantidade) ?></p>
</body>
</html>
