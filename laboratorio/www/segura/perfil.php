<?php
require __DIR__ . '/inc.php';
$usuario = exigir_login($pdo);

$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CORREÇÃO C4: só estes campos podem ser alterados pelo próprio usuário.
    // Qualquer outro campo enviado (perfil, senha_hash, cpf...) é ignorado.
    $nome     = trim((string) ($_POST['nome'] ?? ''));
    $email    = trim((string) ($_POST['email'] ?? ''));
    $endereco = trim((string) ($_POST['endereco'] ?? ''));

    // Validação no servidor de cada campo permitido.
    if ($nome === '' || mb_strlen($nome) > 100) {
        $erros[] = 'Nome obrigatório (até 100 caracteres).';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 100) {
        $erros[] = 'E-mail inválido.';
    }
    if ($endereco === '' || mb_strlen($endereco) > 200) {
        $erros[] = 'Endereço obrigatório (até 200 caracteres).';
    }

    $extras = array_diff(array_keys($_POST), ['nome', 'email', 'endereco']);
    if ($extras) {
        error_log("[loja-segura] usuario {$usuario['id']} enviou campos extras no perfil (ignorados): " . implode(', ', $extras));
    }

    if (!$erros) {
        $pdo->prepare('UPDATE usuarios SET nome = ?, email = ?, endereco = ? WHERE id = ?')
            ->execute([$nome, $email, $endereco, $usuario['id']]);
        mensagem('Seus dados foram atualizados.');
        header('Location: perfil.php');
        exit;
    }
    http_response_code(400);
}

cabecalho('Meus dados', $usuario);
require __DIR__ . '/../comum/perfil_form.php';
rodape();
