<?php
require __DIR__ . '/inc.php';
$usuario = usuario_atual($pdo);

$q = trim((string) ($_GET['q'] ?? ''));
if ($q !== '') {
    $st = $pdo->prepare('SELECT id, nome, preco, estoque FROM produtos WHERE nome LIKE ? ORDER BY id');
    $st->execute(['%' . $q . '%']);
} else {
    $st = $pdo->query('SELECT id, nome, preco, estoque FROM produtos ORDER BY id');
}
$produtos = $st->fetchAll();

cabecalho($q !== '' ? "Busca: $q" : 'Ofertas', $usuario);
?>
<?php if ($q === ''): ?>
<section class="hero">
  <div>
    <span class="selo">SEMANA DA TECNOLOGIA</span>
    <h1>Até 20% de desconto em eletrônicos</h1>
    <p>Notebooks, celulares, fones e acessórios com frete grátis e parcelamento em até 10x sem juros.</p>
    <a class="btn btn-claro" href="produto.php?id=1">Ver a oferta do notebook</a>
  </div>
  <img src="../comum/img/notebook.svg" alt="">
</section>

<section class="vantagens">
  <div class="vantagem"><?= icone('caminhao') ?><div><b>Frete grátis</b>para todo o Brasil</div></div>
  <div class="vantagem"><?= icone('cartao') ?><div><b>Até 10x sem juros</b>no cartão de crédito</div></div>
  <div class="vantagem"><?= icone('troca') ?><div><b>Troca fácil</b>em até 7 dias</div></div>
  <div class="vantagem"><?= icone('cadeado') ?><div><b>Compra segura</b>seus dados protegidos</div></div>
</section>
<?php endif; ?>

<div class="titulo-secao">
  <h2><?= $q !== '' ? 'Resultados para “' . h($q) . '”' : 'Ofertas do dia' ?></h2>
  <span><?= count($produtos) ?> produto(s)</span>
</div>

<?php if (!$produtos): ?>
  <div class="vazio">Nenhum produto encontrado. <a href="index.php">Ver todas as ofertas</a></div>
<?php else: ?>
<div class="grade">
  <?php foreach ($produtos as $p): $info = info_produto((int) $p['id']); ?>
    <article class="card">
      <?php if ($info['desconto']): ?><span class="etiqueta">-<?= (int) $info['desconto'] ?>%</span><?php endif; ?>
      <div class="foto"><img src="<?= img_produto((int) $p['id']) ?>" alt="<?= h($p['nome']) ?>"></div>
      <div class="corpo">
        <h3><?= h($p['nome']) ?></h3>
        <?= estrelas($info['nota'], $info['avaliacoes']) ?>
        <span class="de"><?= moeda(preco_de($p['preco'], (int) $p['id'])) ?></span>
        <span class="por"><?= moeda($p['preco']) ?></span>
        <span class="parcelas"><?= parcelas($p['preco']) ?></span>
        <?php if ($p['estoque'] <= 10): ?><span class="esgotando">Restam só <?= (int) $p['estoque'] ?> unidades!</span><?php endif; ?>
        <span class="frete">Frete grátis</span>
      </div>
      <a class="cobre" href="produto.php?id=<?= (int) $p['id'] ?>" aria-label="Ver <?= h($p['nome']) ?>"></a>
    </article>
  <?php endforeach; ?>
</div>
<?php endif; ?>
<?php rodape();
