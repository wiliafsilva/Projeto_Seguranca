<?php
require __DIR__ . '/inc.php';
$usuario = exigir_login($pdo);

// CORREÇÃO C3: além do número do pedido, a consulta exige que ele pertença
// ao usuário logado (o administrador pode ver todos).
$sql = 'SELECT pe.*, pr.nome AS produto, u.nome AS cliente, u.email, u.cpf, u.endereco
          FROM pedidos pe
          JOIN produtos pr ON pr.id = pe.produto_id
          JOIN usuarios u  ON u.id  = pe.usuario_id
         WHERE pe.id = ?';
$params = [(int) ($_GET['id'] ?? 0)];

if ($usuario['perfil'] !== 'admin') {
    $sql .= ' AND pe.usuario_id = ?';
    $params[] = $usuario['id'];
}

$st = $pdo->prepare($sql);
$st->execute($params);
$p = $st->fetch();

if (!$p) {
    // Mesma resposta para "não existe" e "não é seu": não revela quais números existem.
    error_log("[loja-segura] usuario {$usuario['id']} tentou abrir o pedido {$params[0]}; negado");
    http_response_code(404);
    cabecalho('Pedido não encontrado', $usuario);
    echo '<div class="login"><h1>Pedido não encontrado</h1><p class="sub">O pedido não existe ou não pertence à sua conta.</p>'
       . '<a class="btn btn-primario" href="pedidos.php">Meus pedidos</a></div>';
    rodape();
    exit;
}

cabecalho('Pedido nº ' . $p['id'], $usuario);
require __DIR__ . '/../comum/pedido_detalhe.php';
rodape();
