# Evidências da Etapa 3

Prints da atividade prática. O passo a passo para tirar cada um está na seção 9 do [`laboratorio/README.md`](../laboratorio/README.md).

**Antes de começar:** zere o laboratório (`docker compose down -v` e `docker compose up -d`), para os números dos pedidos baterem com as legendas.

**Em todos os prints:** deixe visível a **faixa vermelha (vulnerável) ou verde (segura)** do topo e, quando der, a **barra de endereço**.

Salve cada print com o nome exato da tabela, dentro da subpasta do cenário (`C1/`, `C2/` …). As legendas já estão prontas para o documento final.

---

## Já pronto

| Arquivo | Legenda |
|---|---|
| `diagrama-laboratorio.png` / `.svg` | Figura – Arquitetura do laboratório Loja Lab |

---

## C1 – Preço em campo oculto

| ✔ | Arquivo | O que mostrar | Legenda |
|---|---|---|---|
| ☐ | `C1/C1-01-codigo-fonte-hidden.png` | Ctrl+U na página do Notebook, com a linha `name="preco" value="2499.90"` | Preço do produto enviado ao navegador em um campo oculto |
| ☐ | `C1/C1-02-burp-original.png` | Burp com a requisição de compra interceptada: `preco=2499.90` | Requisição de compra interceptada no Burp Suite |
| ☐ | `C1/C1-03-burp-alterada.png` | A mesma requisição com `preco=0.01` | Requisição alterada: preço trocado para 0,01 |
| ☐ | `C1/C1-04-vulneravel-pedido.png` | Página do pedido com Total R$ 0,01 | Versão vulnerável: pedido registrado por R$ 0,01 |
| ☐ | `C1/C1-05-segura-pedido.png` | Versão segura, mesmo ataque, Total R$ 2.499,90 | Versão segura: o preço enviado é ignorado |

## C2 – Quantidade negativa

| ✔ | Arquivo | O que mostrar | Legenda |
|---|---|---|---|
| ☐ | `C2/C2-01-navegador-bloqueia.png` | Aviso do navegador ao tentar comprar `-10` | O navegador bloqueia a quantidade inválida (validação no cliente) |
| ☐ | `C2/C2-02-requisicao-negativa.png` | Burp ou terminal com `quantidade=-10` | Requisição enviada direto ao servidor com quantidade -10 |
| ☐ | `C2/C2-03-vulneravel-pedido.png` | Pedido com Total R$ -899,00 | Versão vulnerável: pedido com total negativo |
| ☐ | `C2/C2-04-vulneravel-estoque.png` | Página do Mouse com 60 disponíveis | Versão vulnerável: o estoque aumentou de 50 para 60 |
| ☐ | `C2/C2-05-segura-recusa.png` | "Compra recusada – Quantidade inválida" | Versão segura: compra recusada pelo servidor (HTTP 400) |

## C3 – Pedido de outro cliente (IDOR)

| ✔ | Arquivo | O que mostrar | Legenda |
|---|---|---|---|
| ☐ | `C3/C3-01-pedido-proprio.png` | Pedido nº 1 da Ana, com `pedido.php?id=1` na barra | Pedido da própria cliente, identificado pelo número na URL |
| ☐ | `C3/C3-02-vulneravel-outro-cliente.png` | `pedido.php?id=2` com nome, CPF e endereço do Bruno | Versão vulnerável: dados pessoais de outro cliente expostos |
| ☐ | `C3/C3-03-segura-nao-encontrado.png` | `pedido.php?id=2` com "Pedido não encontrado" | Versão segura: acesso negado ao pedido de outro cliente |

## C4 – Elevação para administrador

| ✔ | Arquivo | O que mostrar | Legenda |
|---|---|---|---|
| ☐ | `C4/C4-01-acesso-negado.png` | `admin.php` com "Acesso negado" (Ana) | Cliente comum sem acesso ao painel administrativo |
| ☐ | `C4/C4-02-burp-perfil-admin.png` | Burp com `&perfil=admin` no fim do corpo | Requisição de "Meus dados" com o campo extra perfil=admin |
| ☐ | `C4/C4-03-vulneravel-painel.png` | Painel admin aberto pela Ana | Versão vulnerável: cliente promovida a administradora |
| ☐ | `C4/C4-04-segura-negado.png` | Versão segura, mesmo ataque, "Acesso negado" | Versão segura: o campo extra é ignorado |

## Logs

| ✔ | Arquivo | O que mostrar | Legenda |
|---|---|---|---|
| ☐ | `logs/logs-01-segura.png` | Terminal com `docker compose logs web \| grep loja-segura` | Tentativas de ataque registradas pela versão segura |

---

**Total:** 18 prints + 1 diagrama.

Depois de tirar os prints, me avise: eu encaixo as figuras no documento técnico final e nos slides.
