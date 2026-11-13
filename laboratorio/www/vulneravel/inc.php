<?php
// Configuração da versão VULNERÁVEL (banco e sessão próprios).
const VERSAO = 'vulneravel';

require __DIR__ . '/../comum/db.php';
require __DIR__ . '/../comum/layout.php';

session_name('LOJA_VULNERAVEL');
session_start();

$pdo = conectar('loja_vulneravel');
