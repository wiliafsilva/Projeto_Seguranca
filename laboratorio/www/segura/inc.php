<?php
// Configuração da versão SEGURA (banco e sessão próprios).
const VERSAO = 'segura';

require __DIR__ . '/../comum/db.php';
require __DIR__ . '/../comum/layout.php';

session_name('LOJA_SEGURA');
session_start();

$pdo = conectar('loja_segura');

const QTD_MIN = 1;
const QTD_MAX = 5;
