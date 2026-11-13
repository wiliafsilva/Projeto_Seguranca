<?php
require __DIR__ . '/inc.php';
$usuario = exigir_login($pdo);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$produto_id = (int) ($_POST['produto_id'] ?? 0);

// FALHA C1: o servidor confia no preço enviado pelo navegador (campo oculto).
$preco = (float) ($_POST['preco'] ?? 0);

// FALHA C2: a quantidade não é validada no servidor. Converter com (int) não é validar:
// "-10" vira -10 sem nenhum erro.
$quantidade = (int) ($_POST['quantidade'] ?? 0);

$total = $preco * $quantidade;

$pdo->prepare('INSERT INTO pedidos (usuario_id, produto_id, quantidade, preco_unitario, total) VALUES (?, ?, ?, ?, ?)')
    ->execute([$usuario['id'], $produto_id, $quantidade, $preco, $total]);
$pedido_id = $pdo->lastInsertId();

// Quantidade negativa AUMENTA o estoque.
$pdo->prepare('UPDATE produtos SET estoque = estoque - ? WHERE id = ?')
    ->execute([$quantidade, $produto_id]);

mensagem("Pedido nº $pedido_id registrado. Total: " . moeda($total));
header('Location: pedido.php?id=' . $pedido_id);
