<?php
require __DIR__ . '/inc.php';
$usuario = exigir_login($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // FALHA C4 (atribuição em massa): o código grava TODO campo recebido
    // que tenha o mesmo nome de uma coluna da tabela usuarios.
    // O formulário só mostra nome, e-mail e endereço, mas quem envia a requisição
    // pode acrescentar perfil=admin.
    $colunas = $pdo->query('SHOW COLUMNS FROM usuarios')->fetchAll(PDO::FETCH_COLUMN);

    $sets = [];
    $valores = [];
    foreach ($_POST as $campo => $valor) {
        if ($campo !== 'id' && in_array($campo, $colunas, true)) {
            $sets[]    = "`$campo` = ?";
            $valores[] = (string) $valor;
        }
    }

    if ($sets) {
        $valores[] = $usuario['id'];
        $pdo->prepare('UPDATE usuarios SET ' . implode(', ', $sets) . ' WHERE id = ?')->execute($valores);
    }
    mensagem('Seus dados foram atualizados.');
    header('Location: perfil.php');
    exit;
}

$erros = [];
cabecalho('Meus dados', $usuario);
require __DIR__ . '/../comum/perfil_form.php';
rodape();
