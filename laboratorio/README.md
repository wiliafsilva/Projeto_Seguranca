# Loja Lab – Laboratório do Tema 4 (Segurança em aplicações Web)

A **Loja Lab** é uma loja virtual **fictícia**, feita em PHP e MySQL, que existe em duas versões:

| Versão | Endereço | Para que serve |
|---|---|---|
| **Vulnerável** (faixa vermelha no topo) | http://localhost:8000/vulneravel/ | Tem 4 falhas de segurança **de propósito**, para serem demonstradas |
| **Segura** (faixa verde no topo) | http://localhost:8000/segura/ | É a mesma loja, com as falhas corrigidas |

O objetivo é mostrar, na prática, o que acontece quando um site **confia em dados que vêm do navegador** do usuário, e como corrigir isso.

> ⚠️ **Uso exclusivo em laboratório.** A loja só abre no próprio computador (`localhost`), não aceita conexões de outras máquinas e todos os dados são inventados. Nunca use estas técnicas em sites de terceiros: sem autorização, isso é crime (Art. 154-A do Código Penal).

---

## Sumário

1. [O que você precisa](#1-o-que-você-precisa)
2. [Instalar o Docker Desktop](#2-instalar-o-docker-desktop)
3. [Baixar o projeto](#3-baixar-o-projeto)
4. [Ligar o laboratório](#4-ligar-o-laboratório)
5. [Usar a loja](#5-usar-a-loja)
6. [Desligar, religar e zerar](#6-desligar-religar-e-zerar)
7. [Instalar o Burp Suite](#7-instalar-o-burp-suite)
8. [Usar o curl](#8-usar-o-curl)
9. [Os 4 cenários de ataque, passo a passo](#9-os-4-cenários-de-ataque-passo-a-passo)
10. [Onde estão as falhas no código](#10-onde-estão-as-falhas-no-código)
11. [Como guardar as evidências](#11-como-guardar-as-evidências)
12. [Problemas comuns](#12-problemas-comuns)
13. [Estrutura dos arquivos](#13-estrutura-dos-arquivos)

---

## 1. O que você precisa

- Um computador com **Windows 10/11**, **macOS** ou **Linux**, com pelo menos **4 GB de RAM livres**.
- **Docker Desktop** (gratuito): roda a loja e o banco de dados sem precisar instalar PHP ou MySQL.
- **Um navegador** (Chrome, Edge ou Firefox).
- Para os ataques: **Burp Suite Community** (gratuito) e o **curl** (já vem no Windows 10/11, no macOS e no Linux).
- Opcional: **Git**, para baixar o projeto pelo terminal.

Não é preciso saber programar para ligar e usar a loja.

---

## 2. Instalar o Docker Desktop

1. Acesse https://www.docker.com/products/docker-desktop/ e baixe a versão do seu sistema.
2. Instale com as opções padrão.
   - **No Windows**, se o instalador pedir para ativar o **WSL 2**, aceite e reinicie o computador quando ele pedir.
3. Abra o **Docker Desktop** e espere aparecer **"Engine running"** (ou a baleia ficar parada, sem animação, na barra de tarefas).
4. Para conferir, abra um terminal e digite:

   ```bash
   docker --version
   docker compose version
   ```

   Se aparecerem os números das versões, está tudo certo.

> **Como abrir um terminal:** no Windows, abra o menu Iniciar e digite **PowerShell**. No macOS, abra o **Terminal**. No Linux, use o terminal do sistema.

---

## 3. Baixar o projeto

**Opção A – Sem Git (mais fácil)**

1. Abra https://github.com/wiliafsilva/Projeto_Seguranca
2. Clique no botão verde **Code** → **Download ZIP**.
3. Extraia o ZIP numa pasta, por exemplo na Área de Trabalho.

**Opção B – Com Git**

```bash
git clone https://github.com/wiliafsilva/Projeto_Seguranca.git
```

Nos dois casos, a loja fica na pasta **`laboratorio`** dentro do projeto.

---

## 4. Ligar o laboratório

1. Confira se o **Docker Desktop está aberto** e com "Engine running".
2. Abra um terminal **dentro da pasta `laboratorio`**:
   - **Windows:** abra a pasta `laboratorio` no Explorador de Arquivos, clique na barra de endereço, digite `powershell` e aperte Enter.
   - **macOS/Linux:** no terminal, use `cd` até a pasta, por exemplo `cd ~/Desktop/Projeto_Seguranca/laboratorio`.
3. Rode:

   ```bash
   docker compose up -d --build
   ```

   - Na **primeira vez**, o Docker baixa as imagens do PHP e do MySQL. Pode levar de 2 a 10 minutos, conforme a internet.
   - Quando terminar, devem aparecer as linhas `Container laboratorio-db-1 Healthy` e `Container laboratorio-web-1 Started`.
4. Abra no navegador: **http://localhost:8000**

Você verá a página inicial do laboratório, com os botões para a versão vulnerável e para a versão segura.

Para conferir se está tudo rodando:

```bash
docker compose ps
```

As duas linhas (`db` e `web`) devem estar com status **Up** (e o `db` com **healthy**).

---

## 5. Usar a loja

**Contas de teste**

| Usuário | Senha | Tipo | Pedidos que já tem |
|---|---|---|---|
| `ana` | `ana123` | cliente | nº 1 e nº 3 |
| `bruno` | `bruno123` | cliente | nº 2 |
| `admin` | `admin123` | administrador | — |

**O que dá para fazer** (igual numa loja real):

- Ver a vitrine, buscar produtos e filtrar pelas categorias do menu.
- Entrar com uma conta (**Entre ou cadastre-se**, no canto superior direito).
- Abrir um produto e comprar (**Comprar agora**).
- Ver **Minha conta → Meus pedidos** e os detalhes de cada pedido.
- Alterar o nome, o e-mail e o endereço em **Meus dados**.
- Com a conta `admin`: abrir o **Painel admin**, com faturamento, pedidos e clientes.

A faixa no topo mostra em qual versão você está, e tem um link para trocar de versão. **Cada versão tem o próprio banco de dados**: o que você faz em uma não aparece na outra.

---

## 6. Desligar, religar e zerar

Sempre dentro da pasta `laboratorio`:

| Quero… | Comando |
|---|---|
| Desligar (os dados ficam guardados) | `docker compose down` |
| Ligar de novo | `docker compose up -d` |
| **Zerar tudo** (volta aos 3 pedidos iniciais, desfaz perfis alterados) | `docker compose down -v` e depois `docker compose up -d` |
| Ver os registros do servidor | `docker compose logs web` |

> **Zere o laboratório antes de tirar os prints**, para os números dos pedidos baterem com este roteiro.

---

## 7. Instalar o Burp Suite

O **Burp Suite** fica entre o navegador e o site: ele **segura cada requisição** antes de ela sair, deixa você **editar** e só depois envia. É assim que se altera um campo oculto ou se acrescenta um campo que não existe no formulário.

1. Baixe o **Burp Suite Community Edition** em https://portswigger.net/burp/communitydownload e instale.
2. Abra o Burp → **Temporary project** → **Next** → **Use Burp defaults** → **Start Burp**.
3. Vá na aba **Proxy** → **Intercept** e clique em **Open browser**.
   - Isso abre um **navegador próprio do Burp**, já configurado. **Use sempre esse navegador** nos testes.
   - Chrome, Edge e Firefox comuns **não passam pelo Burp** quando o endereço é `localhost`, por isso o navegador embutido é o caminho mais simples.
4. No navegador do Burp, acesse http://localhost:8000.

**Como interceptar e alterar uma requisição**

1. Na aba **Proxy → Intercept**, deixe o botão em **Intercept on**.
2. No navegador do Burp, faça a ação (por exemplo, clique em **Comprar agora**). A página fica "carregando".
3. No Burp, aparece a requisição. Na parte de baixo, a última linha é o **corpo**, por exemplo:
   ```
   produto_id=1&preco=2499.90&quantidade=1
   ```
4. Edite o texto ali mesmo (por exemplo, troque `2499.90` por `0.01`).
5. Clique em **Forward** para enviar. Depois, deixe em **Intercept off** para navegar normalmente.

> Dica: a aba **Proxy → HTTP history** guarda todas as requisições. Clique com o botão direito numa delas → **Send to Repeater** para reenviar quantas vezes quiser, mudando os valores.

**Alternativa sem instalar nada:** a aba **Network** das ferramentas do navegador (tecla **F12**) mostra as requisições, e a aba **Elements** permite editar o HTML da página (por exemplo, mudar o `value` de um campo oculto antes de clicar em comprar).

---

## 8. Usar o curl

O **curl** envia requisições direto do terminal, **sem passar pelo navegador**. Isso prova que as regras feitas só no HTML ou no JavaScript não valem nada.

- **Windows (PowerShell):** escreva **`curl.exe`** em vez de `curl` (no PowerShell, `curl` é outro comando). Troque também `-o /dev/null` por `-o NUL`.
- **macOS, Linux ou Git Bash:** use `curl` normalmente.

O curl precisa "fazer login" e guardar o cookie da sessão num arquivo. Faça isso uma vez para cada versão:

```bash
# login da Ana na versão vulnerável (guarda o cookie em ana-vuln.txt)
curl -s -c ana-vuln.txt -d "usuario=ana&senha=ana123" http://localhost:8000/vulneravel/login.php -o /dev/null

# login da Ana na versão segura (guarda o cookie em ana-seg.txt)
curl -s -c ana-seg.txt -d "usuario=ana&senha=ana123" http://localhost:8000/segura/login.php -o /dev/null
```

Nos comandos seguintes, `-b ana-vuln.txt` (ou `-b ana-seg.txt`) envia esse cookie.

---

## 9. Os 4 cenários de ataque, passo a passo

Em cada cenário:
1. mostre o **ponto fraco**;
2. faça o **ataque na versão vulnerável** e veja funcionar;
3. repita o **mesmo ataque na versão segura** e veja ser bloqueado.

Antes de começar, **zere o laboratório** (seção 6).

### C1 – Mudar o preço de um produto (campo oculto)

**O problema:** o preço do produto vai escondido no formulário (`<input type="hidden">`) e o servidor usa o valor que volta do navegador.

**No navegador do Burp:**
1. Entre como `ana` na **versão vulnerável**.
2. Abra o **Notebook Pro 14** (R$ 2.499,90).
3. Clique com o botão direito na página → **Exibir código-fonte** (ou Ctrl+U). Procure por `name="preco"`: está lá o `<input type="hidden" name="preco" value="2499.90">`. 📸
4. No Burp, ligue **Intercept on** e clique em **Comprar agora**.
5. Na requisição interceptada, troque `preco=2499.90` por `preco=0.01`. 📸 (antes e depois)
6. Clique em **Forward** e depois em **Intercept off**.
7. **Resultado:** o pedido é registrado por **R$ 0,01**. 📸

**Na versão segura:** repita os passos. O formulário **nem tem** o campo `preco`. Mesmo acrescentando `&preco=0.01` no Burp, o pedido sai por **R$ 2.499,90**. 📸

**Pelo curl:**
```bash
curl -s -L -b ana-vuln.txt -d "produto_id=1&preco=0.01&quantidade=1" http://localhost:8000/vulneravel/comprar.php | grep -o "Total</span><span[^>]*>[^<]*"
curl -s -L -b ana-seg.txt  -d "produto_id=1&preco=0.01&quantidade=1" http://localhost:8000/segura/comprar.php     | grep -o "Total</span><span[^>]*>[^<]*"
```

### C2 – Comprar quantidade negativa (validação só no navegador)

**O problema:** o limite de 1 a 5 unidades só existe no HTML (`min="1" max="5"`) e num JavaScript. O servidor aceita qualquer número.

1. Na versão vulnerável, abra o **Mouse sem fio** e anote o estoque (50 disponíveis).
2. Tente digitar `-10` na quantidade e clicar em comprar: o **navegador bloqueia**. 📸
3. Agora envie direto, sem o navegador. **Pelo Burp:** intercepte uma compra normal e troque `quantidade=1` por `quantidade=-10`. **Ou pelo curl:**
   ```bash
   curl -s -L -b ana-vuln.txt -d "produto_id=4&preco=89.90&quantidade=-10" http://localhost:8000/vulneravel/comprar.php | grep -o "Total</span><span[^>]*>[^<]*"
   ```
4. **Resultado:** pedido com total **negativo (R$ -899,00)**, e o estoque do mouse **sobe de 50 para 60**. 📸
5. **Na versão segura**, a mesma requisição é **recusada (HTTP 400 – Quantidade inválida)** e o estoque não muda. 📸
   ```bash
   curl -s -o /dev/null -w "%{http_code}\n" -b ana-seg.txt -d "produto_id=4&quantidade=-10" http://localhost:8000/segura/comprar.php
   ```

### C3 – Ver o pedido de outro cliente (IDOR)

**O problema:** a página do pedido usa só o número da URL (`pedido.php?id=1`) e não confere se o pedido é de quem está logado.

1. Logada como `ana`, abra **Minha conta → Meus pedidos → Ver detalhes** do pedido nº 1. Repare na URL: `pedido.php?id=1`. 📸
2. Na barra de endereço, troque para `pedido.php?id=2` e aperte Enter.
3. **Resultado:** aparecem o **nome, e-mail, CPF e endereço do Bruno**. 📸
4. **Na versão segura**, a mesma URL mostra **"Pedido não encontrado"** (HTTP 404). A mensagem é igual à de um pedido que não existe, para não revelar quais números existem. 📸

```bash
curl -s -b ana-vuln.txt "http://localhost:8000/vulneravel/pedido.php?id=2" | grep -o "555.666.777-88"
curl -s -o /dev/null -w "%{http_code}\n" -b ana-seg.txt "http://localhost:8000/segura/pedido.php?id=2"
```

### C4 – Cliente comum vira administrador (atribuição em massa)

**O problema:** ao salvar "Meus dados", o servidor grava **qualquer campo enviado** que tenha o nome de uma coluna da tabela de usuários, inclusive `perfil`.

1. Logada como `ana`, tente abrir http://localhost:8000/vulneravel/admin.php: **Acesso negado**. 📸
2. Abra **Minha conta → Meus dados**. O formulário só tem nome, e-mail e endereço.
3. No Burp, ligue **Intercept on** e clique em **Salvar alterações**.
4. No fim do corpo da requisição, acrescente **`&perfil=admin`**. 📸 Clique em **Forward**.
   - Sem Burp: no F12, aba **Elements**, clique com o botão direito no formulário → **Edit as HTML** e acrescente `<input name="perfil" value="admin">` antes de salvar.
5. **Resultado:** aparece **Painel admin** no topo, e a Ana vê o faturamento, todos os pedidos e o **CPF de todos os clientes**. 📸
6. **Na versão segura**, o campo extra é ignorado e a Ana continua sem acesso ao painel. 📸

```bash
curl -s -o /dev/null -b ana-vuln.txt -d "nome=Ana Souza&email=ana@exemplo.com&endereco=Rua das Flores, 100&perfil=admin" http://localhost:8000/vulneravel/perfil.php
curl -s -o /dev/null -w "%{http_code}\n" -b ana-vuln.txt http://localhost:8000/vulneravel/admin.php   # 200 = entrou

curl -s -o /dev/null -b ana-seg.txt -d "nome=Ana Souza&email=ana@exemplo.com&endereco=Rua das Flores, 100&perfil=admin" http://localhost:8000/segura/perfil.php
curl -s -o /dev/null -w "%{http_code}\n" -b ana-seg.txt http://localhost:8000/segura/admin.php       # 403 = bloqueado
```

### Bônus – Registros das tentativas bloqueadas

A versão segura anota no log do servidor cada tentativa suspeita. Depois dos testes, rode:

```bash
docker compose logs web | grep loja-segura
```

Aparecem linhas como:
```
[loja-segura] usuario 1 enviou campo 'preco' na compra; ignorado
[loja-segura] usuario 1 tentou abrir o pedido 2; negado
[loja-segura] usuario 1 enviou campos extras no perfil (ignorados): perfil
```
📸 Esse print mostra a medida de **monitoramento** citada na Etapa 2.

---

## 10. Onde estão as falhas no código

As duas versões ficam em `www/vulneravel/` e `www/segura/`. As páginas são quase iguais; o que muda está marcado no código com **`FALHA C1`…`C4`** (vulnerável) e **`CORREÇÃO C1`…`C4`** (segura).

| Cenário | Arquivo | Versão vulnerável | Versão segura |
|---|---|---|---|
| **C1** | `produto.php`, `comprar.php` | Preço num `<input type="hidden">`; o servidor usa `$_POST['preco']` | Formulário sem preço; o servidor busca o preço no banco pelo `produto_id` |
| **C2** | `produto.php`, `comprar.php` | Limite só em `min`/`max` e JavaScript; `(int)` aceita `-10` | `filter_input(... FILTER_VALIDATE_INT, min_range 1, max_range 5)` e checagem de estoque |
| **C3** | `pedido.php` | `WHERE pe.id = ?` | `WHERE pe.id = ? AND pe.usuario_id = ?` (o admin vê todos) |
| **C4** | `perfil.php` | Grava todo campo cujo nome seja uma coluna da tabela | Só aceita `nome`, `email` e `endereco`, cada um validado |

Para ver todas as diferenças de uma vez (Git Bash, macOS ou Linux):

```bash
diff -r www/vulneravel www/segura
```

O que é igual nas duas versões (visual, conexão, dados de exemplo) fica em `www/comum/`.

---

## 11. Como guardar as evidências

Crie uma pasta `evidencias` na raiz do projeto, com uma subpasta por cenário, e numere os prints na ordem em que foram tirados:

```
evidencias/
├── C1/
│   ├── C1-01-codigo-fonte-campo-hidden.png
│   ├── C1-02-burp-requisicao-original.png
│   ├── C1-03-burp-requisicao-alterada.png
│   ├── C1-04-vulneravel-pedido-r$0,01.png
│   └── C1-05-segura-preco-correto.png
├── C2/
├── C3/
├── C4/
└── logs/
```

Nos prints, deixe sempre visível a **faixa vermelha ou verde** do topo, que mostra a versão.

---

## 12. Problemas comuns

| Problema | Solução |
|---|---|
| `error during connect` ou `Cannot connect to the Docker daemon` | O Docker Desktop não está aberto. Abra e espere "Engine running". |
| `port is already allocated` (porta 8000 em uso) | Outro programa usa a porta 8000. No `docker-compose.yml`, troque `"127.0.0.1:8000:80"` por `"127.0.0.1:8081:80"` e acesse http://localhost:8081. |
| A página dá erro de banco logo depois de ligar | O MySQL ainda está iniciando. Espere uns 30 segundos e recarregue. |
| No Windows, o Docker pede **WSL 2** | Aceite a instalação e reinicie o computador. |
| O Burp não intercepta nada | Use o **navegador do Burp** (*Proxy → Intercept → Open browser*) e confira se está em **Intercept on**. |
| `curl` no PowerShell dá um erro estranho | Use `curl.exe` em vez de `curl`, e `-o NUL` em vez de `-o /dev/null`. |
| Os números de pedido não batem com o roteiro | Zere o laboratório: `docker compose down -v` e `docker compose up -d`. |
| Quero só olhar a loja, sem ataques | Basta abrir http://localhost:8000 em qualquer navegador. |

---

## 13. Estrutura dos arquivos

```
laboratorio/
├── README.md               ← este guia
├── docker-compose.yml      ← liga o PHP + Apache (porta 8000) e o MySQL
├── Dockerfile              ← imagem do PHP 8.3 com a extensão do MySQL
├── db/
│   └── init.sql            ← cria os dois bancos (loja_vulneravel e loja_segura)
└── www/
    ├── index.php           ← página inicial: escolha da versão
    ├── comum/              ← partes iguais nas duas versões
    │   ├── db.php          ← conexão, dados de exemplo, sessão
    │   ├── layout.php      ← cabeçalho, menu e rodapé da loja
    │   ├── catalogo.php    ← imagens, descrições e avaliações dos produtos
    │   ├── pedido_detalhe.php, perfil_form.php  ← visual dessas páginas
    │   ├── style.css       ← visual da loja
    │   └── img/            ← ilustrações dos produtos (SVG)
    ├── vulneravel/         ← versão com as falhas C1 a C4
    └── segura/             ← versão corrigida
```

**Tecnologias:** PHP 8.3, Apache, MySQL 8.0 e Docker. As senhas das contas de teste são guardadas com `password_hash` e todas as consultas ao banco usam *prepared statements*, para que as únicas falhas da versão vulnerável sejam as 4 estudadas.
