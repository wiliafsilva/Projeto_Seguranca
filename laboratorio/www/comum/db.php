<?php
// Funções comuns às duas versões: conexão, dados de exemplo e sessão.
// Este arquivo NÃO contém as falhas estudadas; elas ficam nas páginas de cada versão.

function conectar(string $banco): PDO
{
    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', getenv('DB_HOST') ?: 'db', $banco);
    $pdo = new PDO($dsn, getenv('DB_USER') ?: 'loja', getenv('DB_PASS') ?: 'loja_lab', [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
    popular_se_vazio($pdo);
    return $pdo;
}

// Insere usuários, produtos e pedidos de exemplo na primeira execução.
// Todos os dados são fictícios.
function popular_se_vazio(PDO $pdo): void
{
    if ((int) $pdo->query('SELECT COUNT(*) FROM usuarios')->fetchColumn() > 0) {
        return;
    }

    $usuarios = [
        ['ana',   'ana123',   'Ana Souza',     'ana@exemplo.com',   '111.222.333-44', 'Rua das Flores, 100 – Aracaju/SE',       'cliente'],
        ['bruno', 'bruno123', 'Bruno Santos',  'bruno@exemplo.com', '555.666.777-88', 'Av. Beira Mar, 2000, ap. 31 – Aracaju/SE', 'cliente'],
        ['admin', 'admin123', 'Administrador', 'admin@exemplo.com', '000.000.000-00', 'Sede da Loja Lab',                         'admin'],
    ];
    $st = $pdo->prepare('INSERT INTO usuarios (usuario, senha_hash, nome, email, cpf, endereco, perfil) VALUES (?, ?, ?, ?, ?, ?, ?)');
    foreach ($usuarios as $u) {
        $u[1] = password_hash($u[1], PASSWORD_DEFAULT);
        $st->execute($u);
    }

    $produtos = [
        ['Notebook Pro 14',     2499.90, 10],
        ['Smartphone X',        1899.00, 15],
        ['Fone Bluetooth',       199.90, 30],
        ['Mouse sem fio',         89.90, 50],
    ];
    $st = $pdo->prepare('INSERT INTO produtos (nome, preco, estoque) VALUES (?, ?, ?)');
    foreach ($produtos as $p) {
        $st->execute($p);
    }

    // pedido 1 = Ana, pedido 2 = Bruno, pedido 3 = Ana
    $pedidos = [
        [1, 1, 1, 2499.90],
        [2, 2, 1, 1899.00],
        [1, 4, 2,   89.90],
    ];
    $st = $pdo->prepare('INSERT INTO pedidos (usuario_id, produto_id, quantidade, preco_unitario, total) VALUES (?, ?, ?, ?, ?)');
    foreach ($pedidos as [$uid, $pid, $qtd, $preco]) {
        $st->execute([$uid, $pid, $qtd, $preco, $qtd * $preco]);
    }
}

// Usuário logado, sempre lido do banco (assim o perfil está sempre atualizado).
function usuario_atual(PDO $pdo): ?array
{
    if (empty($_SESSION['usuario_id'])) {
        return null;
    }
    $st = $pdo->prepare('SELECT id, usuario, nome, email, cpf, endereco, perfil FROM usuarios WHERE id = ?');
    $st->execute([$_SESSION['usuario_id']]);
    return $st->fetch() ?: null;
}

function exigir_login(PDO $pdo): array
{
    $u = usuario_atual($pdo);
    if (!$u) {
        header('Location: login.php');
        exit;
    }
    return $u;
}

function h(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

function moeda($v): string
{
    return 'R$ ' . number_format((float) $v, 2, ',', '.');
}

function mensagem(string $texto, string $tipo = 'ok'): void
{
    $_SESSION['flash'] = [$tipo, $texto];
}
