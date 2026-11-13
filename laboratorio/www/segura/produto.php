<?php
require __DIR__ . '/inc.php';
$usuario = usuario_atual($pdo);

$st = $pdo->prepare('SELECT id, nome, preco, estoque FROM produtos WHERE id = ?');
$st->execute([(int) ($_GET['id'] ?? 0)]);
$p = $st->fetch();
if (!$p) {
    http_response_code(404);
    exit('Produto não encontrado.');
}
$info = info_produto((int) $p['id']);

cabecalho($p['nome'], $usuario);
?>
<div class="trilha"><a href="index.php">Início</a> › <a href="index.php"><?= h($info['categoria']) ?></a> › <?= h($p['nome']) ?></div>

<section class="produto">
  <div class="galeria">
    <div class="principal"><img src="<?= img_produto((int) $p['id']) ?>" alt="<?= h($p['nome']) ?>"></div>
    <div class="miniaturas">
      <div><img src="<?= img_produto((int) $p['id']) ?>" alt=""></div><div></div><div></div>
    </div>
  </div>

  <div class="info">
    <div class="sku"><?= h($info['marca']) ?> · Cód. LAB-<?= str_pad((string) $p['id'], 4, '0', STR_PAD_LEFT) ?></div>
    <h1><?= h($p['nome']) ?></h1>
    <?= estrelas($info['nota'], $info['avaliacoes']) ?>
    <p><?= h($info['resumo']) ?></p>

    <div class="caixa-preco">
      <?php if ($info['desconto']): ?><span class="de"><?= moeda(preco_de($p['preco'], (int) $p['id'])) ?></span> <span class="etiqueta inline">-<?= (int) $info['desconto'] ?>%</span><?php endif; ?>
      <div class="por"><?= moeda($p['preco']) ?></div>
      <div class="parcelas"><?= parcelas($p['preco']) ?></div>
    </div>

    <?php if ($usuario): ?>
    <form class="caixa-compra" method="post" action="comprar.php" onsubmit="return validarQuantidade()">
      <!-- CORREÇÃO C1: o preço NÃO vai mais no formulário. Só o id do produto,
           e o servidor busca o preço no banco. -->
      <input type="hidden" name="produto_id" value="<?= (int) $p['id'] ?>">

      <div class="qtd">
        <label for="quantidade">Quantidade</label>
        <!-- Os limites continuam aqui só para ajudar o usuário. Quem garante é o servidor (comprar.php). -->
        <input id="quantidade" type="number" name="quantidade" value="1" min="<?= QTD_MIN ?>" max="<?= QTD_MAX ?>" required>
        <small><?= (int) $p['estoque'] ?> disponíveis · máx. <?= QTD_MAX ?> por pedido</small>
      </div>
      <button class="btn btn-comprar"><?= icone('carrinho') ?> Comprar agora</button>
    </form>
    <script>
      // Validação no cliente só para a experiência do usuário; o servidor valida de novo.
      function validarQuantidade() {
        const q = Number(document.getElementById('quantidade').value);
        if (!Number.isInteger(q) || q < <?= QTD_MIN ?> || q > <?= QTD_MAX ?>) {
          alert('Escolha entre <?= QTD_MIN ?> e <?= QTD_MAX ?> unidades.');
          return false;
        }
        return true;
      }
    </script>
    <?php else: ?>
    <div class="entrar-para-comprar"><a class="btn btn-comprar" href="login.php">Entre para comprar</a></div>
    <?php endif; ?>

    <ul class="garantias">
      <li><?= icone('caminhao') ?> Frete grátis · entrega em até 5 dias úteis</li>
      <li><?= icone('troca') ?> Troca ou devolução grátis em até 7 dias</li>
      <li><?= icone('escudo') ?> Garantia de 12 meses do fabricante</li>
    </ul>
  </div>
</section>

<section class="descricao">
  <h2>Características</h2>
  <ul>
    <?php foreach ($info['itens'] as $item): ?><li><?= h($item) ?></li><?php endforeach; ?>
  </ul>
</section>
<?php rodape();
