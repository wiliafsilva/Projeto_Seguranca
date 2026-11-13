<?php
// Cabeçalho e rodapé das páginas. A faixa colorida no topo mostra qual versão está aberta,
// o que deixa os prints das evidências fáceis de identificar.

require_once __DIR__ . '/catalogo.php';

function cabecalho(string $titulo, ?array $usuario): void
{
    $vulneravel = VERSAO === 'vulneravel';
    $outra      = $vulneravel ? '../segura/' : '../vulneravel/';
    $busca      = (string) ($_GET['q'] ?? '');
    ?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= h($titulo) ?> | Loja Lab</title>
  <link rel="icon" href="../comum/img/logo.svg">
  <link rel="stylesheet" href="../comum/style.css">
</head>
<body>
  <div class="faixa <?= $vulneravel ? 'faixa-vuln' : 'faixa-seg' ?>">
    <div class="container">
      <span><strong><?= $vulneravel ? 'VERSÃO VULNERÁVEL' : 'VERSÃO SEGURA' ?></strong> · laboratório local para fins acadêmicos · dados fictícios</span>
      <a href="<?= $outra ?>">ir para a versão <?= $vulneravel ? 'segura' : 'vulnerável' ?> →</a>
    </div>
  </div>

  <header class="topo">
    <div class="container">
      <a class="logo" href="index.php">
        <svg viewBox="0 0 40 40" aria-hidden="true"><rect width="40" height="40" rx="10" fill="#ff7a1a"/><path d="M12 15h16l-1.5 15a2 2 0 0 1-2 1.8h-9a2 2 0 0 1-2-1.8z" fill="#fff"/><path d="M16 15v-2a4 4 0 0 1 8 0v2" stroke="#fff" stroke-width="2.4" fill="none"/></svg>
        <span>Loja<b>Lab</b></span>
      </a>

      <form class="busca" action="index.php" role="search">
        <input name="q" value="<?= h($busca) ?>" placeholder="O que você procura hoje?" aria-label="Buscar produtos">
        <button aria-label="Buscar"><?= icone('busca') ?></button>
      </form>

      <nav class="conta">
        <?php if ($usuario): ?>
          <a href="pedidos.php"><?= icone('usuario') ?>
            <span class="ola"><small>Olá, <?= h(strtok($usuario['nome'], ' ')) ?></small><b>Minha conta</b></span></a>
          <?php if ($usuario['perfil'] === 'admin'): ?>
            <a href="admin.php" title="Painel admin"><?= icone('painel') ?><span class="ola"><small>Gestão</small><b>Painel admin</b></span></a>
          <?php endif; ?>
          <a href="logout.php" title="Sair"><?= icone('sair') ?></a>
        <?php else: ?>
          <a href="login.php"><?= icone('usuario') ?><span class="ola"><small>Bem-vindo</small><b>Entre ou cadastre-se</b></span></a>
        <?php endif; ?>
        <a href="pedidos.php" title="Meus pedidos"><?= icone('carrinho') ?></a>
      </nav>
    </div>
    <div class="categorias">
      <div class="container">
        <a class="oferta" href="index.php">Ofertas do dia</a>
        <a href="index.php?q=Notebook">Notebooks</a>
        <a href="index.php?q=Smartphone">Celulares</a>
        <a href="index.php?q=Fone">Áudio</a>
        <a href="index.php?q=Mouse">Acessórios</a>
        <a href="index.php">Mais vendidos</a>
      </div>
    </div>
  </header>

  <main>
    <div class="container">
  <?php if (!empty($_SESSION['flash'])): [$tipo, $texto] = $_SESSION['flash']; unset($_SESSION['flash']); ?>
      <div class="aviso aviso-<?= h($tipo) ?>"><?= h($texto) ?></div>
  <?php endif; ?>
    <?php
}

function rodape(): void
{
    ?>
    </div>
  </main>

  <footer class="rodape">
    <div class="container colunas">
      <div>
        <h4>Loja Lab</h4>
        <p>Loja virtual <strong>fictícia</strong>, criada para o trabalho de Segurança da Informação (Tema 4 – Segurança em aplicações Web) da Universidade Tiradentes.</p>
        <div class="pagamentos"><span>VISA</span><span>MASTER</span><span>ELO</span><span>PIX</span><span>BOLETO</span></div>
      </div>
      <div>
        <h4>Institucional</h4>
        <a href="index.php">Sobre a loja</a><a href="index.php">Trabalhe conosco</a><a href="index.php">Política de privacidade</a>
      </div>
      <div>
        <h4>Ajuda</h4>
        <a href="pedidos.php">Meus pedidos</a><a href="index.php">Trocas e devoluções</a><a href="index.php">Prazos de entrega</a>
      </div>
      <div>
        <h4>Atendimento</h4>
        <p>Seg. a sex., 8h às 18h<br>atendimento@lojalab.exemplo</p>
      </div>
    </div>
    <div class="container legal">
      © 2026 Loja Lab · Ambiente de laboratório, sem vendas reais · Equipe: Daniel Mota e Wilia Silva
    </div>
  </footer>
</body>
</html>
    <?php
}

// Menu lateral da área do cliente.
function menu_conta(array $usuario, string $ativo): void
{
    $itens = [
        'pedidos' => ['pedidos.php', 'pacote', 'Meus pedidos'],
        'perfil'  => ['perfil.php', 'perfil', 'Meus dados'],
    ];
    if ($usuario['perfil'] === 'admin') {
        $itens['admin'] = ['admin.php', 'painel', 'Painel admin'];
    }
    echo '<aside class="menu-conta"><div class="quem"><b>' . h($usuario['nome']) . '</b><small>' . h($usuario['email']) . '</small></div>';
    foreach ($itens as $chave => [$link, $ico, $texto]) {
        echo '<a href="' . $link . '"' . ($chave === $ativo ? ' class="ativo"' : '') . '>' . icone($ico) . h($texto) . '</a>';
    }
    echo '<a href="logout.php">' . icone('sair') . 'Sair</a></aside>';
}
