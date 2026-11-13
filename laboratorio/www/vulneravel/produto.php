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
      <input type="hidden" name="produto_id" value="<?= (int) $p['id'] ?>">
      <!-- FALHA C1: o preço vai para o navegador num campo oculto e volta para o servidor -->
      <input type="hidden" name="preco" value="<?= h($p['preco']) ?>">

      <div class="qtd">
        <label for="quantidade">Quantidade</label>
        <!-- FALHA C2: o limite de 1 a 5 só existe aqui no HTML e no JavaScript abaixo -->
        <input id="quantidade" type="number" name="quantidade" value="1" min="1" max="5" required>
        <small><?= (int) $p['estoque'] ?> disponíveis · máx. 5 por pedido</small>
      </div>
      <button class="btn btn-comprar"><?= icone('carrinho') ?> Comprar agora</button>
    </form>
    <script>
      // Validação só no cliente: melhora a experiência, mas não protege nada.
      function validarQuantidade() {
        const q = Number(document.getElementById('quantidade').value);
        if (!Number.isInteger(q) || q < 1 || q > 5) {
          alert('Escolha entre 1 e 5 unidades.');
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
