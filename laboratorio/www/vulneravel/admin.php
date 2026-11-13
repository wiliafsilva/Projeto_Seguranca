<?php
require __DIR__ . '/inc.php';
$usuario = exigir_login($pdo);

// O painel em si checa o perfil no servidor (corretamente).
// O problema da versão vulnerável está em perfil.php, que deixa o usuário MUDAR o próprio perfil.
if ($usuario['perfil'] !== 'admin') {
    http_response_code(403);
    cabecalho('Acesso negado', $usuario);
    echo '<div class="login"><h1>Acesso negado</h1><p class="sub">Esta área é restrita a administradores da loja.</p>'
       . '<a class="btn btn-primario" href="index.php">Voltar à loja</a></div>';
    rodape();
    exit;
}

$usuarios = $pdo->query('SELECT id, usuario, nome, email, cpf, perfil FROM usuarios ORDER BY id')->fetchAll();
$pedidos  = $pdo->query(
    'SELECT pe.id, u.nome AS cliente, pr.nome AS produto, pe.quantidade, pe.total, pe.criado_em
       FROM pedidos pe JOIN usuarios u ON u.id = pe.usuario_id JOIN produtos pr ON pr.id = pe.produto_id
      ORDER BY pe.id DESC'
)->fetchAll();
$faturamento = array_sum(array_column($pedidos, 'total'));
$estoque     = (int) $pdo->query('SELECT SUM(estoque) FROM produtos')->fetchColumn();

cabecalho('Painel admin', $usuario);
?>
<div class="area">
  <?php menu_conta($usuario, 'admin'); ?>
  <div>
    <section class="kpis">
      <div class="kpi"><small>Faturamento</small><b class="<?= $faturamento < 0 ? 'negativo' : '' ?>"><?= moeda($faturamento) ?></b></div>
      <div class="kpi"><small>Pedidos</small><b><?= count($pedidos) ?></b></div>
      <div class="kpi"><small>Clientes</small><b><?= count($usuarios) ?></b></div>
      <div class="kpi"><small>Itens em estoque</small><b><?= $estoque ?></b></div>
    </section>

    <section class="painel" style="margin-bottom:20px">
      <h1>Últimos pedidos</h1>
      <div class="rolagem">
        <table class="tabela">
          <tr><th>Nº</th><th>Cliente</th><th>Produto</th><th>Qtd.</th><th>Total</th><th>Data</th></tr>
          <?php foreach ($pedidos as $p): ?>
            <tr><td><a href="pedido.php?id=<?= (int) $p['id'] ?>">#<?= (int) $p['id'] ?></a></td><td><?= h($p['cliente']) ?></td>
                <td><?= h($p['produto']) ?></td><td><?= (int) $p['quantidade'] ?></td>
                <td class="<?= $p['total'] < 0 ? 'negativo' : '' ?>"><?= moeda($p['total']) ?></td>
                <td><?= date('d/m/Y H:i', strtotime($p['criado_em'])) ?></td></tr>
          <?php endforeach; ?>
        </table>
      </div>
    </section>

    <section class="painel">
      <h1>Clientes cadastrados</h1>
      <div class="rolagem">
        <table class="tabela">
          <tr><th>ID</th><th>Usuário</th><th>Nome</th><th>E-mail</th><th>CPF</th><th>Perfil</th></tr>
          <?php foreach ($usuarios as $u): ?>
            <tr><td><?= (int) $u['id'] ?></td><td><?= h($u['usuario']) ?></td><td><?= h($u['nome']) ?></td>
                <td><?= h($u['email']) ?></td><td><?= h($u['cpf']) ?></td>
                <td><span class="status chip-<?= h($u['perfil']) ?>"><?= h($u['perfil']) ?></span></td></tr>
          <?php endforeach; ?>
        </table>
      </div>
    </section>
  </div>
</div>
<?php rodape();
