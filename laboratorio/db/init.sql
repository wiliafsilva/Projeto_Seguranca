-- Cria dois bancos idênticos: um para cada versão da Loja Lab.
-- Assim, ataques feitos na versão vulnerável não alteram os dados da versão segura.
-- Os usuários, produtos e pedidos de exemplo são inseridos pelo PHP (www/comum/db.php)
-- na primeira execução, para que as senhas fiquem com hash (password_hash).

CREATE DATABASE IF NOT EXISTS loja_vulneravel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE IF NOT EXISTS loja_segura     CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

GRANT ALL PRIVILEGES ON loja_vulneravel.* TO 'loja'@'%';
GRANT ALL PRIVILEGES ON loja_segura.*     TO 'loja'@'%';
FLUSH PRIVILEGES;

-- ---------- Estrutura (repetida nos dois bancos) ----------

USE loja_vulneravel;

CREATE TABLE usuarios (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  usuario    VARCHAR(30)  NOT NULL UNIQUE,
  senha_hash VARCHAR(255) NOT NULL,
  nome       VARCHAR(100) NOT NULL,
  email      VARCHAR(100) NOT NULL,
  cpf        VARCHAR(14)  NOT NULL,
  endereco   VARCHAR(200) NOT NULL,
  perfil     ENUM('cliente','admin') NOT NULL DEFAULT 'cliente'
);

CREATE TABLE produtos (
  id      INT AUTO_INCREMENT PRIMARY KEY,
  nome    VARCHAR(100)  NOT NULL,
  preco   DECIMAL(10,2) NOT NULL,
  estoque INT           NOT NULL
);

CREATE TABLE pedidos (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id     INT           NOT NULL,
  produto_id     INT           NOT NULL,
  quantidade     INT           NOT NULL,
  preco_unitario DECIMAL(10,2) NOT NULL,
  total          DECIMAL(10,2) NOT NULL,
  criado_em      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
  FOREIGN KEY (produto_id) REFERENCES produtos(id)
);

USE loja_segura;

CREATE TABLE usuarios  LIKE loja_vulneravel.usuarios;
CREATE TABLE produtos  LIKE loja_vulneravel.produtos;
CREATE TABLE pedidos (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id     INT           NOT NULL,
  produto_id     INT           NOT NULL,
  quantidade     INT           NOT NULL,
  preco_unitario DECIMAL(10,2) NOT NULL,
  total          DECIMAL(10,2) NOT NULL,
  criado_em      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
  FOREIGN KEY (produto_id) REFERENCES produtos(id)
);
