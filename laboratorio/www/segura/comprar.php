<?php
require __DIR__ . '/inc.php';
$usuario = exigir_login($pdo);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Responde com HTTP 400 (requisição inválida) e não registra nada.
function recusar(string $motivo): never
{
    global $usuario;
    http_response_code(400);
    cabecalho('Compra recusada', $usuario);
    echo '<div class="login"><h1>Compra recusada</h1><div class="aviso aviso-erro">' . h($motivo) . '</div>'
       . '<a class="btn btn-primario" href="index.php">Voltar à loja</a></div>';
    rodape();
    exit;
}

// CORREÇÃO C2: validação no servidor, com lista de valores permitidos (inteiro de 1 a 5).
$produto_id = filter_input(INPUT_POST, 'produto_id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$quantidade = filter_input(INPUT_POST, 'quantidade', FILTER_VALIDATE_INT,
    ['options' => ['min_range' => QTD_MIN, 'max_range' => QTD_MAX]]);

if (!$produto_id) {
    recusar('Produto inválido.');
}
if ($quantidade === false || $quantidade === null) {
    recusar('Quantidade inválida: escolha entre ' . QTD_MIN . ' e ' . QTD_MAX . ' unidades.');
}

// Um campo "preco" enviado pelo navegador é simplesmente ignorado.
// (Registrar a tentativa ajuda a detectar ataques.)
if (isset($_POST['preco'])) {
    error_log("[loja-segura] usuario {$usuario['id']} enviou campo 'preco' na compra; ignorado");
}

$pdo->beginTransaction();

// CORREÇÃO C1: o preço vem do banco de dados, nunca do formulário.
$st = $pdo->prepare('SELECT preco, estoque FROM produtos WHERE id = ? FOR UPDATE');
$st->execute([$produto_id]);
$produto = $st->fetch();

if (!$produto) {
    $pdo->rollBack();
    recusar('Produto inválido.');
}
if ($produto['estoque'] < $quantidade) {
    $pdo->rollBack();
    recusar('Estoque insuficiente.');
}

$preco = $produto['preco'];
$total = $preco * $quantidade;

$pdo->prepare('INSERT INTO pedidos (usuario_id, produto_id, quantidade, preco_unitario, total) VALUES (?, ?, ?, ?, ?)')
    ->execute([$usuario['id'], $produto_id, $quantidade, $preco, $total]);
$pedido_id = $pdo->lastInsertId();

$pdo->prepare('UPDATE produtos SET estoque = estoque - ? WHERE id = ?')
    ->execute([$quantidade, $produto_id]);

$pdo->commit();

mensagem("Pedido nº $pedido_id registrado. Total: " . moeda($total));
header('Location: pedido.php?id=' . $pedido_id);
