# Etapa 2 – Pesquisa e Planejamento

**Instituição:** Universidade Tiradentes (UNIT)  
**Disciplina:** Segurança da Informação  
**Professor:** Prof. Msc. Felipe Lima  
**Trabalho:** Projeto de Teste de Invasão (Pentest)  
**Tema 4:** Segurança em aplicações Web  
**Equipe:** Daniel de Oliveira Mendonça Mota · Wilia Francisco da Silva

---

## 1. Introdução

Aplicações Web estão presentes em praticamente todos os serviços digitais: lojas, bancos, sistemas acadêmicos e redes sociais. Por estarem expostas à internet, qualquer pessoa pode interagir com elas, inclusive atacantes.

Este trabalho investiga um problema que está na raiz de muitas falhas Web: **a aplicação confiar em dados que vêm do usuário**. O navegador é um ambiente controlado pelo usuário. Tudo o que é enviado de lá (campos de formulário, campos ocultos, parâmetros da URL, cookies, cabeçalhos) pode ser lido e alterado antes de chegar ao servidor. Quando o servidor aceita esses valores sem verificá-los, o atacante consegue mudar preços, acessar dados de outras pessoas ou executar ações que não deveria.

**Problema investigado:** confiança indevida nas entradas do usuário, com foco em manipulação de parâmetros, campos ocultos (`hidden`) e validação feita somente no lado do cliente.

---

## 2. Fundamentação teórica

### 2.1 Arquitetura básica de uma aplicação Web

Uma aplicação Web segue o modelo **cliente-servidor**, com comunicação pelo protocolo **HTTP/HTTPS**:

```
 ┌──────────────────────┐      requisição HTTP       ┌──────────────────────┐       SQL       ┌──────────────┐
 │  CLIENTE (navegador) │ ─────────────────────────> │  SERVIDOR (PHP)      │ ──────────────> │ BANCO DE     │
 │  HTML · CSS · JS     │                            │  regras de negócio   │                 │ DADOS        │
 │  (controlado pelo    │ <───────────────────────── │  (controlado pela    │ <────────────── │ (MySQL)      │
 │   usuário)           │      resposta HTML/JSON    │   organização)       │                 │              │
 └──────────────────────┘                            └──────────────────────┘                 └──────────────┘
          ^                                                    ^
          └──────────── FRONTEIRA DE CONFIANÇA ────────────────┘
```

O ponto central é a **fronteira de confiança**: tudo à esquerda dela pode ser manipulado pelo usuário; só o que está à direita está sob controle da organização.

### 2.2 Papel de cada tecnologia

| Tecnologia | Onde executa | Papel | Pode ser manipulada pelo usuário? |
|---|---|---|---|
| **HTML** | Navegador | Estrutura da página e formulários (inclusive campos `hidden`) | Sim, pelo DevTools |
| **CSS** | Navegador | Aparência visual (esconder/mostrar elementos) | Sim |
| **JavaScript** | Navegador | Interatividade e validações no cliente | Sim, pode ser desativado ou alterado |
| **PHP** | Servidor | Lógica de negócio, autenticação, autorização | Não diretamente |
| **SQL** | Banco de dados | Armazenamento e consulta dos dados | Só se houver falha de injeção |

Consequência prática: **esconder algo com CSS ou validar com JavaScript melhora a experiência do usuário, mas não é mecanismo de segurança.**

### 2.3 O protocolo HTTP e as entradas do usuário

Toda requisição HTTP pode carregar dados controlados pelo usuário:

- **Parâmetros GET** na URL: `pedido.php?id=15`
- **Corpo de requisições POST**: dados de formulários, incluindo campos ocultos
- **Cookies**: identificadores de sessão, preferências
- **Cabeçalhos**: `User-Agent`, `Referer`, `X-Forwarded-For`

Exemplo de requisição gerada por um formulário de compra:

```http
POST /comprar.php HTTP/1.1
Host: loja.lab
Cookie: PHPSESSID=a1b2c3d4
Content-Type: application/x-www-form-urlencoded

produto_id=3&quantidade=1&preco=2499.90
```

Com um proxy de interceptação, o atacante pode trocar `preco=2499.90` por `preco=0.01` antes da requisição chegar ao servidor.

### 2.4 Campos ocultos (`hidden`)

O campo `<input type="hidden">` não aparece na tela, mas **está no código-fonte da página e é enviado normalmente** junto com o formulário:

```html
<form action="comprar.php" method="POST">
  <input type="hidden" name="produto_id" value="3">
  <input type="hidden" name="preco" value="2499.90">   <!-- ERRADO: valor sensível no cliente -->
  <input type="number" name="quantidade" value="1" min="1" max="5">
  <button>Comprar</button>
</form>
```

"Oculto" significa apenas "não exibido". Qualquer usuário pode vê-lo com **Ctrl+U** (código-fonte) ou **F12** (DevTools) e alterá-lo.

### 2.5 Validação no cliente × validação no servidor

| | Validação no cliente (HTML/JS) | Validação no servidor (PHP) |
|---|---|---|
| Objetivo | Usabilidade: feedback rápido | **Segurança**: garantir regras |
| Pode ser contornada? | Sim (desativar JS, editar HTML, usar proxy, `curl`) | Não, pelo usuário |
| Obrigatória? | Opcional | **Obrigatória** |

Atributos como `min`, `max`, `required`, `maxlength` e `pattern`, além de validações em JavaScript, são ignorados por quem envia a requisição direto ao servidor.

---

## 3. Vulnerabilidades e riscos

### 3.1 Vulnerabilidades estudadas

| # | Vulnerabilidade | Descrição | Referência |
|---|---|---|---|
| V1 | **Manipulação de campo oculto** | Valor sensível (preço, desconto, ID do usuário) enviado num campo `hidden` e aceito pelo servidor | CWE-472 |
| V2 | **Validação apenas no cliente** | Regras (quantidade mínima, limites) existem só no HTML/JS; o servidor aceita qualquer valor, inclusive negativo | CWE-602, CWE-20 |
| V3 | **Manipulação de parâmetros na URL (IDOR)** | Trocar o `id` na URL permite ver dados de outros usuários, pois o servidor não confere se o recurso pertence a quem pede | CWE-639 |
| V4 | **Atribuição em massa (mass assignment)** | O servidor grava todos os campos recebidos; o atacante adiciona um campo como `perfil=admin` | CWE-915 |

### 3.2 Relação com o OWASP Top 10:2025

- **A01 – Broken Access Control**: V3 e V4 são falhas de controle de acesso, a categoria mais crítica da lista.
- **A06 – Insecure Design**: V1 e V2 vêm de um projeto que coloca regras de negócio no cliente.
- **A05 – Injection**: entradas não validadas também abrem caminho para injeções (SQL, XSS). Esse assunto é aprofundado pelo Tema 3, por isso aqui só é citado.

### 3.3 Avaliação de risco

| Vulnerabilidade | Probabilidade | Impacto | Risco |
|---|---|---|---|
| V1 – Campo oculto com preço | Alta (basta o DevTools) | Alto (prejuízo financeiro) | **Alto** |
| V2 – Validação só no cliente | Alta | Médio/Alto (estoque, saldo, regras de negócio) | **Alto** |
| V3 – IDOR | Alta (basta trocar um número) | Alto (vazamento de dados pessoais) | **Alto** |
| V4 – Mass assignment | Média (exige descobrir o nome do campo) | Crítico (elevação para administrador) | **Alto** |

---

## 4. Impactos para usuários e organizações

**Para as organizações**
- Prejuízo financeiro direto: produtos vendidos por valores alterados, descontos indevidos.
- Vazamento de dados de clientes, com sanções da **LGPD** (multas de até 2% do faturamento, limitadas a R$ 50 milhões por infração).
- Dano à reputação e perda de confiança dos clientes.
- Fraudes internas e ações administrativas feitas por usuários sem permissão.

**Para os usuários**
- Exposição de dados pessoais (endereço, CPF, histórico de compras).
- Uso desses dados em golpes e engenharia social.
- Alteração ou perda de informações da própria conta.

---

## 5. Ferramentas

| Ferramenta | Tipo | Finalidade no trabalho |
|---|---|---|
| **DevTools do navegador** (F12) | Nativa do navegador | Inspecionar e editar HTML, ver campos ocultos, acompanhar requisições na aba *Network* |
| **Burp Suite Community** | Proxy de interceptação | Interceptar, alterar e reenviar requisições HTTP (*Proxy* e *Repeater*) |
| **OWASP ZAP** | Proxy / scanner gratuito | Alternativa ao Burp; também faz varredura automática |
| **curl** | Linha de comando | Enviar requisições forjadas sem passar pelo navegador |
| **Docker** | Contêineres | Subir o laboratório isolado e reproduzível |
| **DVWA / OWASP Juice Shop** | Aplicações vulneráveis de propósito | Ambientes de apoio para praticar e comparar |

---

## 6. Medidas de prevenção e correção

1. **Nunca confiar no cliente.** Toda regra de negócio e de segurança deve ser verificada no servidor.
2. **Buscar valores sensíveis no servidor.** O preço vem do banco de dados a partir do `produto_id`, nunca do formulário.
   ```php
   // ERRADO (vulnerável)
   $preco = $_POST['preco'];

   // CORRETO (seguro)
   $stmt = $pdo->prepare('SELECT preco FROM produtos WHERE id = ?');
   $stmt->execute([(int) $_POST['produto_id']]);
   $preco = $stmt->fetchColumn();
   ```
3. **Validar no servidor com lista de permitidos (*allowlist*):** tipo, formato, intervalo e tamanho.
   ```php
   $qtd = filter_input(INPUT_POST, 'quantidade', FILTER_VALIDATE_INT,
       ['options' => ['min_range' => 1, 'max_range' => 5]]);
   if ($qtd === false || $qtd === null) {
       http_response_code(400);
       exit('Quantidade inválida');
   }
   ```
4. **Verificar autorização em todo acesso a recurso** (contra IDOR): o recurso pedido pertence ao usuário da sessão?
   ```php
   $stmt = $pdo->prepare('SELECT * FROM pedidos WHERE id = ? AND usuario_id = ?');
   $stmt->execute([(int) $_GET['id'], $_SESSION['usuario_id']]);
   ```
5. **Aceitar só os campos esperados** (contra mass assignment): definir explicitamente quais campos o usuário pode alterar.
6. **Usar consultas parametrizadas** (*prepared statements*) em todo acesso ao banco.
7. **Proteger a integridade** de valores que precisam passar pelo cliente: guardar na sessão do servidor ou assinar (HMAC).
8. **Registrar e monitorar** tentativas suspeitas, como valores fora do intervalo e IDs de outros usuários.
9. **Testar a segurança** periodicamente, seguindo o OWASP WSTG.

---

## 7. Planejamento da demonstração prática

### 7.1 Ambiente

- **Aplicação:** "Loja Lab", uma loja virtual simples em **PHP + MySQL** desenvolvida pela equipe, com duas versões:
  - `vulneravel/`: contém as falhas V1 a V4;
  - `segura/`: mesma aplicação, com as correções da seção 6.
- **Execução:** em contêineres **Docker** na máquina da própria equipe, acessível só em `localhost`.
- **Ferramentas de ataque:** DevTools, Burp Suite Community (ou OWASP ZAP) e `curl`.
- **Apoio:** DVWA ou OWASP Juice Shop, para comparar com cenários conhecidos.

```
┌─────────────────── Máquina da equipe (localhost) ───────────────────┐
│                                                                     │
│  Navegador ──> Burp Suite (proxy :8080) ──> Loja Lab (Docker :8000) │
│                                                  │                  │
│                                                  v                  │
│                                            MySQL (Docker)           │
└─────────────────────────────────────────────────────────────────────┘
```

### 7.2 Cenários

| Cenário | Ataque na versão vulnerável | Resultado esperado | Comportamento da versão segura |
|---|---|---|---|
| **C1 – Preço no campo oculto** (V1) | Alterar `preco` de 2499.90 para 0.01 via DevTools ou Burp | Pedido registrado por R$ 0,01 | Ignora o campo; usa o preço do banco |
| **C2 – Quantidade negativa** (V2) | Enviar `quantidade=-10` por `curl`, contornando o `min="1"` do HTML | Total negativo / estoque incorreto | Rejeita com HTTP 400 |
| **C3 – Ver pedido de outro cliente** (V3) | Trocar `pedido.php?id=1` por `?id=2` | Exibe dados pessoais de outro cliente | Retorna "acesso negado" |
| **C4 – Virar administrador** (V4) | Adicionar `perfil=admin` ao formulário de edição de perfil | Usuário comum ganha acesso ao painel admin | Campo ignorado; perfil não muda |

### 7.3 Procedimento

1. Subir o laboratório com Docker e criar dois usuários de teste (ex.: `ana` e `bruno`).
2. Configurar o navegador para usar o Burp Suite como proxy.
3. Para cada cenário: executar o ataque na versão vulnerável e **registrar evidências** (prints da requisição original, da requisição alterada e do resultado).
4. Repetir o mesmo ataque na versão segura e registrar o bloqueio.
5. Comparar o código vulnerável com o corrigido, trecho a trecho.
6. Consolidar as evidências no documento técnico e nos slides.

### 7.4 Evidências a coletar

- Prints do código-fonte mostrando os campos ocultos.
- Prints do Burp com a requisição interceptada antes e depois da alteração.
- Prints do resultado (pedido por R$ 0,01, dados de outro cliente, painel admin).
- Prints das mesmas tentativas bloqueadas na versão segura.
- Diagrama da arquitetura do laboratório.
- Trechos de código comparando as duas versões.

### 7.5 Cuidados éticos e legais

- Todos os testes acontecem **apenas** no laboratório próprio da equipe (`localhost`).
- Nenhum sistema, rede ou conta de terceiros será testado, incluindo os sistemas da universidade.
- Os dados usados são fictícios.
- A atividade respeita o Art. 154-A do Código Penal e a LGPD, conforme orientação da disciplina.

---

## 8. Divisão de tarefas

| Atividade | Responsável |
|---|---|
| Fundamentação teórica (arquitetura, HTTP, validação) | Daniel |
| Vulnerabilidades, riscos e OWASP Top 10 | Wilia |
| Desenvolvimento da Loja Lab (versão vulnerável) | Daniel e Wilia |
| Correções (versão segura) | Daniel e Wilia |
| Execução dos cenários e coleta de evidências | Daniel e Wilia |
| Documento técnico final | Wilia |
| Slides da apresentação | Daniel |
| Ensaio da apresentação | Daniel e Wilia |

*Divisão sugerida; pode ser ajustada pela equipe.*

---

## 9. Cronograma

| Fase | Atividades | Data |
|---|---|---|
| Etapa 1 (concluída) | Formação da equipe e escolha do tema | 09/11/2026 |
| Etapa 2 (este documento) | Pesquisa e planejamento | 13/11/2026 |
| Etapa 3 | Montar o laboratório, executar os cenários, coletar evidências | até 29/11/2026 |
| Etapa 4 | Documento técnico final, slides e apresentação | **29/11/2026 (entrega final)** |

---

## REFERÊNCIAS

BRASIL. **Decreto-Lei nº 2.848, de 7 de dezembro de 1940**. Código Penal. Brasília, DF: Presidência da República, 1940. Disponível em: https://www.planalto.gov.br/ccivil_03/decreto-lei/del2848compilado.htm. Acesso em: 13 nov. 2026.

BRASIL. **Lei nº 12.737, de 30 de novembro de 2012**. Dispõe sobre a tipificação criminal de delitos informáticos; altera o Decreto-Lei nº 2.848, de 7 de dezembro de 1940 - Código Penal; e dá outras providências. Brasília, DF: Presidência da República, 2012. Disponível em: https://www.planalto.gov.br/ccivil_03/_ato2011-2014/2012/lei/l12737.htm. Acesso em: 13 nov. 2026.

BRASIL. **Lei nº 13.709, de 14 de agosto de 2018**. Lei Geral de Proteção de Dados Pessoais (LGPD). Brasília, DF: Presidência da República, 2018. Disponível em: https://www.planalto.gov.br/ccivil_03/_ato2015-2018/2018/lei/l13709.htm. Acesso em: 13 nov. 2026.

BRASIL. **Lei nº 14.155, de 27 de maio de 2021**. Altera o Decreto-Lei nº 2.848, de 7 de dezembro de 1940 (Código Penal), para tornar mais graves os crimes de violação de dispositivo informático, furto e estelionato cometidos de forma eletrônica ou pela internet. Brasília, DF: Presidência da República, 2021. Disponível em: https://www.planalto.gov.br/ccivil_03/_ato2019-2022/2021/lei/l14155.htm. Acesso em: 13 nov. 2026.

LIMA, Felipe. **Pentest**: realizando testes de segurança com Linux e Raspberry Pi. Aracaju: Universidade Tiradentes, 2026. Material de aula (slides) da disciplina Segurança da Informação.

MITRE CORPORATION. **CWE-20**: improper input validation. [*S. l.*]: MITRE, [20--]. Disponível em: https://cwe.mitre.org/data/definitions/20.html. Acesso em: 13 nov. 2026.

MITRE CORPORATION. **CWE-472**: external control of assumed-immutable web parameter. [*S. l.*]: MITRE, [20--]. Disponível em: https://cwe.mitre.org/data/definitions/472.html. Acesso em: 13 nov. 2026.

MITRE CORPORATION. **CWE-602**: client-side enforcement of server-side security. [*S. l.*]: MITRE, [20--]. Disponível em: https://cwe.mitre.org/data/definitions/602.html. Acesso em: 13 nov. 2026.

MITRE CORPORATION. **CWE-639**: authorization bypass through user-controlled key. [*S. l.*]: MITRE, [20--]. Disponível em: https://cwe.mitre.org/data/definitions/639.html. Acesso em: 13 nov. 2026.

MITRE CORPORATION. **CWE-915**: improperly controlled modification of dynamically-determined object attributes. [*S. l.*]: MITRE, [20--]. Disponível em: https://cwe.mitre.org/data/definitions/915.html. Acesso em: 13 nov. 2026.

OWASP FOUNDATION. **Input Validation Cheat Sheet**. [*S. l.*]: OWASP, [20--]. Disponível em: https://cheatsheetseries.owasp.org/cheatsheets/Input_Validation_Cheat_Sheet.html. Acesso em: 13 nov. 2026.

OWASP FOUNDATION. **Insecure Direct Object Reference Prevention Cheat Sheet**. [*S. l.*]: OWASP, [20--]. Disponível em: https://cheatsheetseries.owasp.org/cheatsheets/Insecure_Direct_Object_Reference_Prevention_Cheat_Sheet.html. Acesso em: 13 nov. 2026.

OWASP FOUNDATION. **Mass Assignment Cheat Sheet**. [*S. l.*]: OWASP, [20--]. Disponível em: https://cheatsheetseries.owasp.org/cheatsheets/Mass_Assignment_Cheat_Sheet.html. Acesso em: 13 nov. 2026.

OWASP FOUNDATION. **OWASP Top 10:2025**. [*S. l.*]: OWASP, 2025. Disponível em: https://owasp.org/Top10/. Acesso em: 13 nov. 2026.

OWASP FOUNDATION. **Web Security Testing Guide**: v4.2. [*S. l.*]: OWASP, 2020. Disponível em: https://owasp.org/www-project-web-security-testing-guide/. Acesso em: 13 nov. 2026.

PHP GROUP. **Manual do PHP**. [*S. l.*]: The PHP Group, [20--]. Disponível em: https://www.php.net/manual/pt_BR/. Acesso em: 13 nov. 2026.

PORTSWIGGER. **Web Security Academy**. [*S. l.*]: PortSwigger, [20--]. Disponível em: https://portswigger.net/web-security. Acesso em: 13 nov. 2026.
