# Diário do Trabalho – Projeto de Pentest

**Instituição:** Universidade Tiradentes (UNIT)  
**Disciplina:** Segurança da Informação – Prof. Msc. Felipe Lima  
**Tema 4:** Segurança em aplicações Web  
**Equipe:** Daniel de Oliveira Mendonça Mota · Wilia Francisco da Silva

---

## Situação atual

| Etapa | Situação |
|---|---|
| Etapa 1 – Formação da equipe e escolha do tema | Concluída |
| Etapa 2 – Pesquisa e planejamento | Concluída (13/11/2026) |
| Etapa 3 – Atividade prática (laboratório) | Documentação pronta; faltam só os prints (lista em `evidencias/README.md`) |
| Etapa 4 – Apresentação | Não iniciada |

---

## 10/11/2026

### Leitura do material da pasta

Arquivos analisados:

- `Roteiro/Trabalho em Equipe - Projeto de Pentest.pdf`: enunciado do trabalho, com 7 temas, 4 etapas, entregáveis e critérios de avaliação. A parte prática tem o maior peso (2,5 de 10).
- `apoio/Slides 7 - Pentest.pdf`: ética e legalidade, tipos de teste (caixa-preta, branca e cinza), metodologias (PTES, OWASP WSTG, NIST 800-115), estágios do pentest e OSINT.
- `apoio/Linux Server Hardening.pdf`: hardening, usuários e permissões no Linux.

Regra importante do roteiro: testes só em ambiente próprio ou autorizado (VMs, DVWA, Juice Shop), nunca em sistemas de terceiros.

### Etapa 1 – Formação da equipe e escolha do tema

- Tema escolhido: **Tema 4 – Segurança em aplicações Web**.
- Equipe definida com **dois integrantes**: Daniel e Wilia. O roteiro pede de 4 a 5, mas a equipe confirmou que ficará só com os dois.
- Foi redigido o texto da proposta: integrantes, tema e descrição do problema (confiança indevida nas entradas do usuário).
- Etapa 1 considerada concluída pela equipe.

### Etapa 2 – Pesquisa e planejamento

Criada a pasta `documentos/` com o arquivo `Etapa 2 - Pesquisa e Planejamento.md`, que contém:

1. Introdução e problema investigado
2. Fundamentação teórica: arquitetura cliente-servidor, papel de HTML/CSS/JS/PHP/SQL, HTTP, campos `hidden` e validação no cliente × no servidor
3. Vulnerabilidades e riscos:
   - V1: manipulação de campo oculto (CWE-472)
   - V2: validação apenas no cliente (CWE-602, CWE-20)
   - V3: IDOR, ou manipulação de parâmetros na URL (CWE-639)
   - V4: atribuição em massa, ou mass assignment (CWE-915)
   - Relação com o OWASP Top 10:2025 (A01, A05, A06) e tabela de risco
4. Impactos para organizações e usuários (incluindo LGPD)
5. Ferramentas: DevTools, Burp Suite, OWASP ZAP, curl, Docker, DVWA e Juice Shop
6. Medidas de prevenção e correção, com exemplos de código PHP vulnerável e corrigido
7. Planejamento da demonstração: aplicação própria "Loja Lab" (PHP + MySQL em Docker), com versão vulnerável e versão segura, 4 cenários de ataque (C1 a C4), procedimento, evidências a coletar e cuidados éticos
8. Divisão de tarefas (sugestão, ainda a confirmar pela equipe)
9. Cronograma
10. Referências

### Exportação para PDF e DOCX

- Gerados `Etapa 2 - Pesquisa e Planejamento.pdf` e `Etapa 2 - Pesquisa e Planejamento.docx`, em A4, com tabelas formatadas e rodapé com número de página no DOCX.
- Conversão feita por script em Node.js (bibliotecas `docx` e `marked`). O PDF foi impresso pelo Microsoft Edge em modo headless.
- Os dois arquivos foram conferidos visualmente, página por página. O DOCX foi checado exportando pelo próprio Word.
- Ajuste: as setas dos diagramas (▶ ◀ ▲ ▼) foram trocadas por `>` `<` `^` `v`, porque desalinhavam as caixas.

### Correção de erro ao abrir o PDF

- **Problema:** o visualizador de PDF do VS Code mostrava o erro `Requesting object that isn't resolved yet pattern_p2_14`.
- **Causa:** os emojis ❌ e ✅ do documento eram gravados no PDF num formato de fonte (Type3 com padrões de cor) que o visualizador antigo não consegue desenhar.
- **Solução:** os emojis foram trocados por texto (`ERRADO`, `CORRETO`, `concluída`) e os arquivos foram gerados de novo. O PDF novo não tem mais fontes Type3.

### Referências no padrão ABNT

- Seção de referências reescrita conforme a ABNT NBR 6023:2018:
  - título **REFERÊNCIAS**, sem número;
  - ordem alfabética, título em negrito e subtítulo sem destaque;
  - "Disponível em: … Acesso em: 10 nov. 2026." nos documentos online;
  - `[S. l.]` quando não há local e `[20--]` quando não há data;
  - uma referência separada para cada CWE;
  - leis com data completa, ementa e link do Planalto.
- Acrescentada a instituição: **Universidade Tiradentes (UNIT)**, no cabeçalho do documento e na referência dos slides do professor (local: Aracaju).

## 13/11/2026

- A equipe confirmou a leitura do documento da Etapa 2.
- O campus foi confirmado: **Aracaju**.
- Os links das referências foram conferidos pela equipe, e a data de acesso passou para **13 nov. 2026** em todas as referências.
- **Prazo da entrega final: 29/11/2026.** O cronograma (seção 9) foi preenchido com as datas.
- O PDF e o DOCX da Etapa 2 foram gerados de novo.
- A equipe acredita que o professor vai passar orientações para a Etapa 3 e está aguardando a resposta dele.
- **Etapa 2 marcada como concluída.** A divisão de tarefas da seção 8 foi aceita como está.

### Início da Etapa 3 – Laboratório "Loja Lab"

- A equipe decidiu começar a Etapa 3 sem esperar as orientações do professor. Se elas chegarem, o laboratório será adaptado.
- Criada a pasta `laboratorio/` com a Loja Lab em **PHP 8.3 + MySQL 8.0**, rodando no **Docker** (só acessível pela própria máquina, em http://localhost:8000).
- Duas versões, cada uma com o próprio banco:
  - `vulneravel/` (faixa vermelha): falhas C1 a C4 marcadas no código com `FALHA C1`…`C4`;
  - `segura/` (faixa verde): correções marcadas com `CORREÇÃO C1`…`C4`.
- Só 5 arquivos mudam de uma versão para a outra: `inc.php`, `produto.php`, `comprar.php`, `pedido.php` e `perfil.php`.
- A versão segura também registra no log do servidor as tentativas bloqueadas (`[loja-segura]`).
- Dados fictícios: usuários `ana`, `bruno` e `admin`; 4 produtos; 3 pedidos.
- Escrito o `laboratorio/README.md`: como subir, onde estão as falhas, uso do Burp e do curl, roteiro de cada cenário e padrão de nomes das evidências.
- **Testes feitos com curl, nas duas versões:**

  | Cenário | Vulnerável | Segura |
  |---|---|---|
  | C1 – preço 0.01 no Notebook | Pedido por R$ 0,01 | Pedido por R$ 2.499,90 (campo ignorado) |
  | C2 – quantidade -10 no Mouse | Total R$ -899,00 e estoque 50 → 60 | HTTP 400, estoque continua 50 |
  | C3 – Ana abre o pedido 2 (Bruno) | Mostra CPF e endereço do Bruno | HTTP 404 "Pedido não encontrado" |
  | C4 – `perfil=admin` no perfil | Ana acessa o painel admin | Continua com acesso negado (403) |

  Outros casos conferidos na versão segura: quantidade 99 e "abc" recusadas, e-mail inválido recusado, compra normal funciona, Ana vê os próprios pedidos e o admin vê todos.
- Ajuste: a verificação de saúde do MySQL foi corrigida para o site só subir depois que os bancos estão criados.
- O laboratório foi zerado e está no estado inicial, pronto para os prints.

### Visual de loja real, passo a passo e GitHub

- **Novo visual da Loja Lab**, imitando uma loja virtual de verdade (marca fictícia, sem copiar nenhuma loja existente):
  - cabeçalho com logotipo, busca funcionando, "Minha conta" e carrinho, além de menu de categorias;
  - vitrine com banner de promoção, faixa de vantagens (frete grátis, 10x sem juros…) e cards com ilustração, avaliações, preço "de/por" e parcelamento;
  - página de produto com galeria, caixa de preço, botão "Comprar agora", garantias e características;
  - "Minha conta" com menu lateral, lista de pedidos e "Meus dados";
  - página do pedido com status, resumo e dados de entrega;
  - painel admin com indicadores (faturamento, pedidos, clientes, estoque) e tabelas;
  - rodapé com colunas e formas de pagamento.
- As ilustrações dos produtos são SVGs próprios, em `www/comum/img/`.
- As falhas C1 a C4 continuam idênticas. Os 4 ataques foram testados de novo nas duas versões, com o mesmo resultado.
- O `laboratorio/README.md` foi reescrito como **passo a passo para qualquer pessoa**: instalar o Docker, baixar o projeto, ligar, usar a loja, zerar, instalar e usar o Burp Suite, usar o curl (inclusive no PowerShell), roteiro detalhado de cada ataque com os prints a tirar, onde estão as falhas no código, evidências e problemas comuns. Todos os comandos do README foram testados.
- Criado o `README.md` da raiz, que apresenta o projeto no GitHub.
- Criado o `.gitignore`: os PDFs do professor (`apoio/` e `Roteiro/`) **não** vão para o GitHub.
- Projeto enviado para https://github.com/wiliafsilva/Projeto_Seguranca
- O primeiro envio saiu com a linha `Co-Authored-By: Claude` e o GitHub listou o Claude como contribuidor. O commit foi corrigido (sem coautoria) e reenviado pela equipe. **Regra:** commits só em nome da equipe.

### Documentação da Etapa 3

- A geração automática dos prints foi interrompida e **não** será feita pelo assistente. **Os prints ficam com a equipe**, seguindo o roteiro do `laboratorio/README.md`.
- Criado `documentos/Etapa 3 - Atividade Pratica.md`, com os 5 itens que o roteiro pede para a etapa: ambiente, ferramentas, procedimentos, resultados com evidências, análise e medidas de proteção, além de um apêndice com a comparação do código vulnerável × seguro.
- Criado o diagrama `evidencias/diagrama-laboratorio.png` (e `.svg`).
- Criado `evidencias/README.md` com a lista dos **18 prints** a tirar, com nome de arquivo, o que mostrar e a legenda pronta. Também foram criadas as subpastas `C1/`, `C2/`, `C3/`, `C4/` e `logs/`.
- Os resultados da seção 5 do relatório vêm dos testes feitos no laboratório em 13/11.

---

## Arquivos produzidos

```
documentos/
├── diario.md                                   ← este diário
├── Etapa 2 - Pesquisa e Planejamento.md        ← texto-fonte (editável)
├── Etapa 2 - Pesquisa e Planejamento.docx      ← versão Word
└── Etapa 2 - Pesquisa e Planejamento.pdf       ← versão para entrega

laboratorio/
├── README.md               ← guia: como subir, roteiro dos cenários, curl, evidências
├── docker-compose.yml      ← PHP + Apache e MySQL
├── Dockerfile
├── db/init.sql             ← estrutura dos dois bancos
└── www/
    ├── index.php           ← página inicial (escolha da versão)
    ├── comum/              ← conexão, dados de exemplo, layout e CSS
    ├── vulneravel/         ← versão com as falhas C1 a C4
    └── segura/             ← versão corrigida
```

---

## O que falta para concluir o trabalho

### Etapa 2 – Finalizar e entregar (concluída)

- [x] Os dois integrantes leem o documento inteiro, porque o roteiro exige que todos conheçam o conteúdo
- [x] Confirmar a divisão de tarefas (seção 8): aceita como está no documento
- [x] Preencher o cronograma (seção 9): entrega final em **29/11/2026**
- [x] Abrir os links das referências e atualizar a data de acesso (agora 13 nov. 2026)
- [x] Confirmar o campus: Aracaju
- [x] Gerar de novo o PDF e o DOCX
- [x] Etapa 2 concluída

### Etapa 3 – Atividade prática (laboratório)

> **Atenção (13/11/2026):** a equipe começou a Etapa 3 sem esperar o professor. Se ele passar orientações próprias, adaptar esta seção, o laboratório e, se for preciso, a seção 7 do documento da Etapa 2.
>
> - [ ] Confirmar com o professor se haverá orientações específicas para a Etapa 3 (ambiente, ferramentas, formato das evidências, prazo)

**3.1 Preparar o ambiente**
- [x] Instalar o Docker Desktop
- [ ] Instalar o Burp Suite Community (ou o OWASP ZAP)
- [ ] Usar o navegador embutido do Burp (*Proxy → Intercept → Open browser*), porque navegadores comuns não passam pelo proxy em `localhost`
- [x] Conferir se o `curl` funciona no terminal

**3.2 Construir a "Loja Lab"**
- [x] Criar o `docker-compose.yml` com PHP + Apache e MySQL
- [x] Criar o banco: tabelas `usuarios`, `produtos` e `pedidos`, com dados fictícios
- [x] Criar os usuários de teste `ana` (comum), `bruno` (comum) e `admin`
- [x] Versão `vulneravel/`:
  - [x] login e sessão
  - [x] página de produto com o preço em campo `hidden` (C1)
  - [x] quantidade validada só com `min`/`max` do HTML (C2)
  - [x] `pedido.php?id=` sem checar o dono do pedido (C3)
  - [x] edição de perfil que grava qualquer campo recebido (C4)
  - [x] painel admin acessível a quem tem `perfil=admin`
- [x] Versão `segura/`: mesma aplicação com as correções da seção 6 (preço vindo do banco, `filter_input`, checagem de dono do pedido, lista de campos permitidos e consultas parametrizadas)
- [x] Testar se as duas versões sobem com um único comando (`docker compose up`)

**3.3 Executar os cenários e coletar evidências** (lista completa e legendas em `evidencias/README.md`)

Para cada cenário (C1 a C4), na versão vulnerável e depois na segura:
- [ ] Print da página ou do código-fonte mostrando o ponto fraco
- [ ] Print da requisição original no Burp
- [ ] Print da requisição alterada
- [ ] Print do resultado do ataque (sucesso na vulnerável, bloqueio na segura)
- [x] Anotar os comandos `curl` usados (estão no `laboratorio/README.md`)
- [ ] Salvar tudo em `evidencias/C1/`, `evidencias/C2/`, etc., com os nomes do `evidencias/README.md` (pastas já criadas)

**3.4 Material de apoio**
- [x] Diagrama final da arquitetura do laboratório (`evidencias/diagrama-laboratorio.png`)
- [x] Comparação do código vulnerável e do corrigido, para cada cenário (Apêndice A do relatório da Etapa 3)
- [x] Relatório da Etapa 3 (`documentos/Etapa 3 - Atividade Pratica.md`)
- [ ] (Opcional) Repetir um cenário parecido no DVWA ou no Juice Shop, para comparar

### Etapa 4 – Documento técnico, slides e apresentação

**4.1 Documento técnico final** (amplia o da Etapa 2)
- [ ] Capa e folha de rosto no padrão ABNT (Universidade Tiradentes, curso, disciplina, integrantes, cidade e ano)
- [ ] Resumo
- [ ] Sumário
- [ ] Introdução e objetivos
- [ ] Fundamentação teórica (revisar a da Etapa 2)
- [ ] Metodologia: ambiente, ferramentas e procedimento
- [ ] Resultados: um subtítulo por cenário, com as evidências (figuras com legenda e fonte, no padrão ABNT)
- [ ] Análise dos resultados e contramedidas, relacionando cada falha ao CWE e ao OWASP Top 10
- [ ] Conclusão com recomendações de segurança
- [ ] Referências em ABNT, atualizadas
- [ ] Apêndice: trechos de código e instruções para subir o laboratório
- [ ] Gerar PDF e DOCX

**4.2 Slides** (seguindo a ordem que o roteiro pede)
- [ ] Capa: tema, integrantes, disciplina e professor
- [ ] Introdução e contextualização
- [ ] Fundamentação teórica: arquitetura, fronteira de confiança e cliente × servidor
- [ ] Ferramentas utilizadas
- [ ] Ambiente do laboratório (diagrama)
- [ ] Demonstração: um slide por cenário (ataque → resultado → correção)
- [ ] Resultados e análise das vulnerabilidades (tabela de risco)
- [ ] Recomendações de segurança
- [ ] Conclusão
- [ ] Referências
- [ ] Dividir os slides entre Daniel e Wilia, porque os dois precisam falar

**4.3 Demonstração ao vivo**
- [ ] Roteiro da demo, passo a passo, com o que cada um faz e fala
- [ ] Deixar o laboratório pronto antes de começar (contêineres no ar, Burp aberto, usuários logados)
- [ ] Plano B: vídeo gravado ou prints da demo, caso algo falhe na hora
- [ ] Testar no computador e na rede que serão usados na apresentação

**4.4 Ensaio**
- [ ] Pelo menos um ensaio completo, cronometrado
- [ ] Preparar respostas para perguntas prováveis (ex.: "por que validar no cliente se não é seguro?", "o que é IDOR?", "HTTPS resolve isso?")

### Entregáveis finais (seção 5 do roteiro)

- [ ] Documento técnico descrevendo o trabalho
- [ ] Apresentação de slides
- [ ] Evidências da atividade prática (prints, diagramas, registros)
- [ ] Demonstração prática
- [ ] Conclusão com recomendações de segurança

### Conferência com os critérios de avaliação

| Critério | Pontos | Onde é atendido |
|---|---|---|
| Fundamentação teórica | 2,0 | Seção 2 do documento e slides teóricos |
| Domínio do tema | 2,0 | Os dois integrantes dominam todo o conteúdo; respostas às perguntas |
| Desenvolvimento prático | 2,5 | Loja Lab, 4 cenários e evidências |
| Análise dos resultados e contramedidas | 1,5 | Comparação vulnerável × segura, CWE/OWASP e recomendações |
| Apresentação e participação da equipe | 2,0 | Falas divididas, demo ensaiada e plano B |

**Observação:** se o `.md` for alterado, o PDF e o DOCX precisam ser gerados de novo. Se a edição for feita direto no Word, basta salvar como PDF pelo próprio Word.
