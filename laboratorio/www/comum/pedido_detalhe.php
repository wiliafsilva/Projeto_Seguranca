<?php
// Visual da página de detalhes do pedido (igual nas duas versões).
// A diferença de segurança está em COMO o pedido $p é buscado em cada pedido.php.
if (!isset($p)) {
    http_response_code(404);
    exit;
}
?>
<div class="trilha"><a href="index.php">Início</a> › <a href="pedidos.php">Meus pedidos</a> › Pedido nº <?= (int) $p['id'] ?></div>

<div class="confirmacao">
  <section class="painel">
    <div class="sucesso">
      <div class="check"><?= icone('check') ?></div>
      <div>
        <h1 style="margin:0">Pedido nº <?= (int) $p['id'] ?></h1>
        <span class="status">Pagamento aprovado</span>
        <span class="meta" style="color:var(--texto-2);font-size:13px"> · <?= date('d/m/Y \à\s H:i', strtotime($p['criado_em'])) ?></span>
      </div>
    </div>

    <div class="produto-pedido">
      <div class="mini"><img src="<?= img_produto((int) $p['produto_id']) ?>" alt=""></div>
      <div>
        <b><?= h($p['produto']) ?></b>
        <div class="parcelas"><?= (int) $p['quantidade'] ?> unidade(s) × <?= moeda($p['preco_unitario']) ?></div>
      </div>
    </div>

    <div class="resumo-linha"><span>Subtotal</span><span class="<?= $p['total'] < 0 ? 'negativo' : '' ?>"><?= moeda($p['total']) ?></span></div>
    <div class="resumo-linha"><span>Frete</span><span style="color:var(--verde);font-weight:700">Grátis</span></div>
    <div class="resumo-linha total"><span>Total</span><span class="<?= $p['total'] < 0 ? 'negativo' : '' ?>"><?= moeda($p['total']) ?></span></div>
  </section>

  <aside class="painel">
    <h2 style="font-size:17px">Dados da entrega</h2>
    <dl class="dados">
      <dt>Cliente</dt>  <dd><?= h($p['cliente']) ?></dd>
      <dt>E-mail</dt>   <dd><?= h($p['email']) ?></dd>
      <dt>CPF</dt>      <dd><?= h($p['cpf']) ?></dd>
      <dt>Endereço</dt> <dd><?= h($p['endereco']) ?></dd>
      <dt>Previsão</dt> <dd>Até 5 dias úteis</dd>
    </dl>
  </aside>
</div>
