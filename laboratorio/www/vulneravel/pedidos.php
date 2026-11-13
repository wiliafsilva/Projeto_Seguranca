<?php
require __DIR__ . '/inc.php';
$usuario = exigir_login($pdo);

// A LISTA mostra só os pedidos do usuário logado...
$st = $pdo->prepare(
    'SELECT pe.id, pe.produto_id, pr.nome AS produto, pe.quantidade, pe.total, pe.criado_em
       FROM pedidos pe JOIN produtos pr ON pr.id = pe.produto_id
      WHERE pe.usuario_id = ? ORDER BY pe.id DESC'
);
$st->execute([$usuario['id']]);
$pedidos = $st->fetchAll();

cabecalho('Meus pedidos', $usuario);
?>
<div class="area">
  <?php menu_conta($usuario, 'pedidos'); ?>
  <section class="painel">
    <h1>Meus pedidos</h1>
    <p class="sub"><?= count($pedidos) ?> pedido(s) nesta conta</p>
    <?php if (!$pedidos): ?>
      <div class="vazio">Você ainda não fez nenhum pedido. <a href="index.php">Ver ofertas</a></div>
    <?php endif; ?>
    <div class="lista-pedidos">
      <?php foreach ($pedidos as $p): ?>
        <div class="item-pedido">
          <div class="mini"><img src="<?= img_produto((int) $p['produto_id']) ?>" alt=""></div>
          <div>
            <b>Pedido nº <?= (int) $p['id'] ?></b> · <span class="status">Pagamento aprovado</span>
            <div class="meta"><?= h($p['produto']) ?> · <?= (int) $p['quantidade'] ?> un. · <?= date('d/m/Y H:i', strtotime($p['criado_em'])) ?></div>
          </div>
          <div class="valor">
            <div class="<?= $p['total'] < 0 ? 'negativo' : '' ?>"><?= moeda($p['total']) ?></div>
            <!-- ...mas cada link leva o número do pedido na URL (veja pedido.php) -->
            <a href="pedido.php?id=<?= (int) $p['id'] ?>">Ver detalhes</a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
</div>
<?php rodape();
