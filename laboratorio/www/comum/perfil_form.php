<?php
// Visual da página "Meus dados" (igual nas duas versões).
// A diferença de segurança está em COMO cada perfil.php trata o POST.
if (!isset($usuario)) {
    http_response_code(404);
    exit;
}
?>
<div class="area">
  <?php menu_conta($usuario, 'perfil'); ?>
  <section class="painel">
    <h1>Meus dados</h1>
    <p class="sub">Mantenha seus dados de contato e entrega atualizados.</p>
    <?php foreach ($erros as $e): ?><div class="aviso aviso-erro"><?= h($e) ?></div><?php endforeach; ?>
    <form class="form-estreito" method="post">
      <label for="nome">Nome completo</label>
      <input id="nome" name="nome" value="<?= h($usuario['nome']) ?>" required>
      <label for="email">E-mail</label>
      <input id="email" name="email" type="email" value="<?= h($usuario['email']) ?>" required>
      <label for="endereco">Endereço de entrega</label>
      <input id="endereco" name="endereco" value="<?= h($usuario['endereco']) ?>" required>
      <label>CPF</label>
      <input value="<?= h($usuario['cpf']) ?>" disabled>
      <button class="btn btn-primario" style="margin-top:20px">Salvar alterações</button>
    </form>
  </section>
</div>
