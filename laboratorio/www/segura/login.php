<?php
require __DIR__ . '/inc.php';

$erro = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $st = $pdo->prepare('SELECT id, senha_hash FROM usuarios WHERE usuario = ?');
    $st->execute([(string) ($_POST['usuario'] ?? '')]);
    $u = $st->fetch();

    if ($u && password_verify((string) ($_POST['senha'] ?? ''), $u['senha_hash'])) {
        session_regenerate_id(true);
        $_SESSION['usuario_id'] = $u['id'];
        header('Location: index.php');
        exit;
    }
    $erro = 'Usuário ou senha inválidos.';
}

cabecalho('Entrar', null);
?>
<div class="login">
  <h1>Entrar na sua conta</h1>
  <p class="sub">Acompanhe pedidos e compre mais rápido.</p>
  <?php if ($erro): ?><div class="aviso aviso-erro"><?= h($erro) ?></div><?php endif; ?>
  <form method="post">
    <label for="usuario">Usuário</label>
    <input id="usuario" name="usuario" autocomplete="username" required>
    <label for="senha">Senha</label>
    <input id="senha" name="senha" type="password" autocomplete="current-password" required>
    <button class="btn btn-primario">Entrar</button>
  </form>
  <div class="contas-teste">
    <b>Contas de teste (laboratório):</b><br>
    <code>ana / ana123</code> · <code>bruno / bruno123</code> · <code>admin / admin123</code>
  </div>
</div>
<?php rodape();
