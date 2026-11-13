<?php
require __DIR__ . '/inc.php';
$usuario = exigir_login($pdo);

// FALHA C3 (IDOR): o pedido é buscado só pelo número da URL.
// O servidor não confere se o pedido pertence ao usuário logado.
$st = $pdo->prepare(
    'SELECT pe.*, pr.nome AS produto, u.nome AS cliente, u.email, u.cpf, u.endereco
       FROM pedidos pe
       JOIN produtos pr ON pr.id = pe.produto_id
       JOIN usuarios u  ON u.id  = pe.usuario_id
      WHERE pe.id = ?'
);
$st->execute([(int) ($_GET['id'] ?? 0)]);
$p = $st->fetch();

if (!$p) {
    http_response_code(404);
    cabecalho('Pedido não encontrado', $usuario);
    echo '<div class="login"><h1>Pedido não encontrado</h1><p class="sub">Confira o número do pedido.</p>'
       . '<a class="btn btn-primario" href="pedidos.php">Meus pedidos</a></div>';
    rodape();
    exit;
}

cabecalho('Pedido nº ' . $p['id'], $usuario);
require __DIR__ . '/../comum/pedido_detalhe.php';
rodape();
