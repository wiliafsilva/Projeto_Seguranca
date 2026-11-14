# 1 Introdução

As aplicações Web estão presentes em quase todos os serviços digitais atuais: lojas virtuais, bancos, sistemas acadêmicos e redes sociais. Por ficarem expostas à internet, qualquer pessoa pode interagir com elas, inclusive quem tem intenção de atacá-las.

Este trabalho investiga um problema que está na raiz de muitas falhas de segurança em aplicações Web: a confiança indevida nas entradas do usuário. O navegador é um ambiente controlado por quem o utiliza; tudo o que sai dele (campos de formulário, campos ocultos, parâmetros da URL e cookies) pode ser lido e alterado antes de chegar ao servidor. Quando o servidor aceita esses dados sem verificá-los, um atacante consegue mudar preços, acessar dados de outras pessoas ou executar ações que não deveria.

Este trabalho tem como objetivo geral compreender e demonstrar como a confiança indevida nas entradas do usuário compromete a segurança de uma aplicação Web, e apresentar as medidas de proteção correspondentes. Como objetivos específicos, busca-se (i) explicar a arquitetura de uma aplicação Web e o conceito de fronteira de confiança; (ii) reproduzir, em ambiente controlado, quatro falhas comuns; (iii) avaliar o impacto de cada falha; e (iv) implementar e verificar as correções.

Todas as atividades práticas foram realizadas exclusivamente no laboratório próprio da equipe, acessível apenas pela própria máquina (localhost) e com dados fictícios. Nenhum sistema de terceiros foi testado. Testar sistemas sem autorização é crime, previsto no Art. 154-A do Código Penal [1], com penas ampliadas pela Lei nº 14.155/2021 [2]; dados pessoais permanecem protegidos pela Lei Geral de Proteção de Dados [3].

O restante deste trabalho está organizado da seguinte forma: a Seção 2 apresenta a fundamentação teórica; a Seção 3 descreve a metodologia e o ambiente; a Seção 4 apresenta o desenvolvimento e os resultados dos quatro cenários; a Seção 5 analisa os resultados; a Seção 6 apresenta as medidas de proteção; e a Seção 7 traz as considerações finais.

# 2 Fundamentação teórica

Uma aplicação Web segue o modelo cliente-servidor, com comunicação pelo protocolo HTTP ou sua versão criptografada, o HTTPS. O cliente (navegador) exibe as páginas e envia as requisições, executando HTML, CSS e JavaScript; é controlado pelo usuário. O servidor recebe as requisições, aplica as regras de negócio e responde, executando, neste trabalho, código PHP e consultas a um banco de dados MySQL; é controlado pela organização.

O ponto central é a fronteira de confiança: tudo o que ocorre no cliente pode ser manipulado pelo usuário, enquanto apenas o que ocorre no servidor está sob controle da organização. A consequência prática é direta: esconder um valor com CSS ou validá-lo apenas com JavaScript melhora a experiência de uso, mas não constitui mecanismo de segurança.

Toda requisição HTTP pode carregar dados controlados pelo usuário: parâmetros na URL, corpo de requisições POST (incluindo campos ocultos), cookies e cabeçalhos. Com um proxy de interceptação, esses valores podem ser alterados antes de a requisição chegar ao servidor. O campo HTML do tipo hidden, por exemplo, não aparece na tela, mas está no código-fonte e é enviado normalmente com o formulário; qualquer usuário o visualiza com as ferramentas do navegador. Do mesmo modo, atributos como min, max e required, bem como validações em JavaScript, são ignorados por quem envia a requisição diretamente ao servidor.

As vulnerabilidades deste trabalho são classificadas pelo CWE (Common Weakness Enumeration) e relacionadas ao OWASP Top 10:2025 [12], referência das falhas mais críticas em aplicações Web. A Tabela 1 resume as quatro falhas estudadas.

Tabela: Falhas estudadas e sua classificação.

| # | Vulnerabilidade | CWE | OWASP Top 10:2025 |
|---|---|---|---|
| C1 | Manipulação de campo oculto | CWE-472 | A06 a Insecure Design |
| C2 | Validação apenas no cliente | CWE-602, CWE-20 | A06 a Insecure Design |
| C3 | Referência direta insegura a objeto (IDOR) | CWE-639 | A01 a Broken Access Control |
| C4 | Atribuição em massa | CWE-915 | A01 a Broken Access Control |

Quanto ao impacto, para as organizações as falhas podem causar prejuízo financeiro direto, vazamento de dados de clientes, com sanções da LGPD [3], dano à reputação e fraudes realizadas por usuários sem permissão. Para os usuários, há exposição de dados pessoais (endereço, CPF, histórico de compras), que podem ser usados em golpes e engenharia social, além da alteração ou perda de informações da própria conta.

# 3 Metodologia

Para demonstrar o problema de forma controlada, a equipe desenvolveu a Loja Lab, uma loja virtual fictícia em duas versões: uma com as quatro falhas inseridas de propósito e outra com as mesmas falhas corrigidas. O mesmo ataque é aplicado às duas versões, o que torna visível tanto o impacto de cada falha quanto a eficácia da correção. A Figura 1 apresenta a arquitetura do laboratório.

![Arquitetura do laboratório Loja Lab.](../evidencias/diagrama-laboratorio.png)

Todo o laboratório é executado no computador da equipe. O servidor Web utiliza PHP 8.3 com Apache, em contêiner Docker, e disponibiliza as duas versões da loja na porta 8000. O banco de dados utiliza MySQL 8.0, também em contêiner, com dois bancos separados (loja_vulneravel e loja_segura). A orquestração é feita com Docker Compose, que inicia todo o ambiente com um único comando.

Como medidas de isolamento, a porta está vinculada apenas ao endereço 127.0.0.1, de modo que nenhum outro computador da rede acessa a loja; cada versão possui banco e sessão próprios; os dados são fictícios; e o laboratório pode ser reiniciado ao estado inicial a qualquer momento. Para que as únicas falhas da versão vulnerável fossem as quatro estudadas, as duas versões adotam boas práticas no restante do código: senhas armazenadas com a função password_hash, consultas parametrizadas (prepared statements) e escape de saída com htmlspecialchars.

Foram utilizadas as seguintes ferramentas: Docker e Docker Compose, para montar o ambiente isolado e reproduzível; o navegador e suas ferramentas de desenvolvedor, para inspecionar o código-fonte, os campos ocultos e as requisições; o Burp Suite Community [13], para interceptar e alterar as requisições; o comando curl, para enviar requisições diretamente ao servidor sem passar pelo navegador; e os registros (logs) do servidor, para documentar as tentativas bloqueadas pela versão segura.

Para cada cenário, adotou-se o seguinte procedimento, a partir do estado inicial do laboratório: (i) evidenciar o ponto fraco; (ii) executar o ataque na versão vulnerável e registrar o resultado; (iii) executar o mesmo ataque na versão segura e registrar o bloqueio; e, ao final, (iv) consultar os registros da versão segura.

# 4 Desenvolvimento e resultados

## 4.1 C1 a Alteração do preço em campo oculto

Na página do produto Notebook Pro 14, de preço R$ 2.499,90, o valor estava armazenado em um campo oculto do formulário, visível no código-fonte, conforme a Figura 2.

![Preço do produto enviado ao navegador em um campo oculto.](../evidencias/C1/C1-01-codigo-fonte-hidden.png)

Ao acionar a compra, a requisição foi interceptada no Burp Suite. O corpo original continha o parâmetro preco=2499.90 (Figura 3), que foi alterado para preco=0.01 (Figura 4).

![Requisição de compra interceptada no Burp Suite.](../evidencias/C1/C1-02-burp-original.png)

![Requisição alterada: preço modificado para 0.01.](../evidencias/C1/C1-03-burp-alterada.png)

Na versão vulnerável, o servidor aceitou o valor recebido e registrou o pedido por R$ 0,01 (Figura 5).

![Versão vulnerável: pedido registrado por R$ 0,01.](../evidencias/C1/C1-04-vulneravel-pedido.png)

Na versão segura, o formulário não envia mais o preço: o corpo da requisição contém apenas o identificador do produto e a quantidade (Figura 6). Ainda que o parâmetro preco=0.01 seja acrescentado manualmente (Figura 7), o servidor o ignora e utiliza o preço obtido do banco de dados. O pedido foi registrado por R$ 2.499,90 (Figura 8).

![Versão segura: o formulário não envia mais o preço.](../evidencias/C1/C1-05-segura-burp-sem-preco.png)

![Versão segura: tentativa de forçar o preço na requisição.](../evidencias/C1/C1-06-segura-burp-com-preco.png)

![Versão segura: preço correto, obtido do banco de dados.](../evidencias/C1/C1-07-segura-pedido.png)

## 4.2 C2 a Quantidade negativa

O limite de 1 a 5 unidades existia apenas no HTML e em JavaScript. Pelo navegador, o valor -10 é bloqueado (Figura 9). A requisição foi então enviada diretamente ao servidor, com o parâmetro quantidade=-10 (Figura 10).

![O navegador bloqueia a quantidade inválida (validação no cliente).](../evidencias/C2/C2-01-navegador-bloqueia.png)

![Requisição enviada diretamente ao servidor com quantidade -10.](../evidencias/C2/C2-02-requisicao-negativa.png)

Na versão vulnerável, o pedido foi registrado com total negativo, de R$ -899,00 (Figura 11). Além disso, a quantidade negativa aumentou o estoque, de 50 para 60 unidades (Figura 12).

![Versão vulnerável: pedido com total negativo.](../evidencias/C2/C2-03-vulneravel-pedido.png)

![Versão vulnerável: o estoque aumentou de 50 para 60 unidades.](../evidencias/C2/C2-04-vulneravel-estoque.png)

Na versão segura, o estoque inicial é o mesmo, de 50 unidades (Figura 13). A mesma requisição com quantidade=-10 (Figura 14) é recusada pelo servidor, com resposta HTTP 400 (Figura 15).

![Versão segura: estoque antes da tentativa.](../evidencias/C2/C2-05-segura-estoque.png)

![Versão segura: requisição com quantidade -10.](../evidencias/C2/C2-06-segura-burp-negativa.png)

![Versão segura: compra recusada pelo servidor.](../evidencias/C2/C2-07-segura-recusa.png)

## 4.3 C3 a Acesso ao pedido de outro cliente (IDOR)

A lista "Meus pedidos" apresenta apenas os pedidos da própria cliente, Ana; o pedido nº 2, do cliente Bruno, não é exibido (Figura 16). Ao abrir os detalhes do próprio pedido, a URL revela o identificador pedido.php?id=1 (Figura 17).

![A lista apresenta apenas os pedidos da própria cliente.](../evidencias/C3/C3-01-meus-pedidos.png)

![Pedido da própria cliente, identificado pelo número na URL.](../evidencias/C3/C3-02-pedido-proprio.png)

Na versão vulnerável, ao substituir id=1 por id=2 na URL, a cliente acessa o pedido do Bruno, com nome, e-mail, CPF e endereço (Figura 18). Na versão segura, a mesma URL retorna "Pedido não encontrado", com resposta HTTP 404, idêntica à de um pedido inexistente, para não revelar quais identificadores existem (Figura 19).

![Versão vulnerável: dados pessoais de outro cliente expostos ao alterar o número na URL.](../evidencias/C3/C3-03-vulneravel-outro-cliente.png)

![Versão segura: acesso negado ao pedido de outro cliente.](../evidencias/C3/C3-04-segura-nao-encontrado.png)

## 4.4 C4 a Elevação para administrador (atribuição em massa)

Como cliente, Ana não acessa o painel administrativo (Figura 20). O formulário "Meus dados" apresenta apenas os campos nome, e-mail e endereço; ao salvar, a requisição foi interceptada e o parâmetro perfil=admin foi acrescentado ao corpo (Figura 21).

![Cliente comum sem acesso ao painel administrativo.](../evidencias/C4/C4-01-acesso-negado.png)

![Requisição de "Meus dados" com o campo adicional perfil=admin.](../evidencias/C4/C4-02-burp-perfil-admin.png)

Na versão vulnerável, a cliente passou a ter perfil de administradora e acessou o painel, com o faturamento e os CPFs de todos os clientes (Figura 22). Na versão segura, o mesmo ataque não produz efeito: a página "Meus dados" retorna ao estado normal, sem o acesso ao painel (Figura 23), que permanece inacessível (Figura 24).

![Versão vulnerável: cliente promovida a administradora, com acesso aos dados de todos.](../evidencias/C4/C4-03-vulneravel-painel.png)

![Versão segura: o campo perfil é ignorado e a cliente não é promovida.](../evidencias/C4/C4-04-segura-sem-painel.png)

![Versão segura: cliente permanece sem acesso ao painel.](../evidencias/C4/C4-05-segura-negado.png)

## 4.5 Registros da versão segura

A versão segura registra, no log do servidor, cada tentativa bloqueada, atendendo à medida de monitoramento (Figura 25). A Tabela 2 sintetiza os resultados dos quatro cenários.

![Tentativas de ataque registradas pela versão segura.](../evidencias/logs/logs-01-segura.png)

Tabela: Síntese dos resultados nas duas versões.

| Cenário | Versão vulnerável | Versão segura |
|---|---|---|
| C1 a preço alterado para R$ 0,01 | Pedido por R$ 0,01 | Pedido por R$ 2.499,90 (campo ignorado) |
| C2 a quantidade -10 | Total R$ -899,00; estoque 50 → 60 | Recusa (HTTP 400); estoque mantido |
| C3 a acesso ao pedido nº 2 | Expõe CPF e endereço do Bruno | "Pedido não encontrado" (HTTP 404) |
| C4 a perfil=admin | Acesso ao painel administrativo | Campo ignorado; acesso negado (HTTP 403) |

# 5 Análise dos resultados

Os quatro cenários confirmam a ideia central do trabalho: o que vem do navegador está sob controle do usuário. Em todos eles, a página parecia correta para um usuário comum, e a falha só se manifestou para quem alterou a requisição.

Observa-se que esconder um dado não o protege: o campo oculto (C1) e o limite máximo (C2) estavam no código-fonte, acessíveis pelas ferramentas do navegador. Validar no cliente também não protege: o bloqueio do navegador no C2 foi contornado sem esforço por uma requisição direta. Da mesma forma, converter o tipo de um dado não equivale a validá-lo: no C2, a conversão de "-10" para inteiro resulta em -10 sem erro, e apenas uma validação de intervalo no servidor impede o problema. No C3, a lista "Meus pedidos" exibia somente os pedidos da cliente, mas a página de detalhe não conferia o dono do pedido, evidenciando que uma interface correta não garante um controle de acesso correto.

Cabe destacar que o uso de HTTPS não evitaria nenhum dos casos, pois a criptografia protege os dados em trânsito, enquanto a alteração é feita pelo próprio usuário, antes do envio. Do mesmo modo, o uso de consultas parametrizadas, já presente nas duas versões, protege contra injeção de SQL, mas não contra falhas de lógica de negócio ou de autorização como as estudadas.

# 6 Medidas de proteção

As correções aplicadas à versão segura seguem um único princípio: o servidor não deve confiar no cliente. Para o C1, o preço é obtido do banco de dados a partir do identificador do produto, e o formulário não envia mais esse valor. Para o C2, a quantidade é validada no servidor com a função filter_input, restrita ao intervalo de 1 a 5, com conferência de estoque e uso de transação. Para o C3, a consulta exige que o pedido pertença ao usuário autenticado, exceto para o administrador, com resposta idêntica para "não existe" e "não pertence". Para o C4, adota-se uma lista de campos permitidos, de modo que apenas nome, e-mail e endereço possam ser alterados, cada um validado. Em todos os casos, as tentativas suspeitas são registradas em log, para fins de monitoramento.

De forma geral, recomenda-se que toda aplicação Web valide as entradas com lista de permitidos, obtenha valores sensíveis no servidor, verifique a autorização em cada acesso a recurso, aceite apenas os campos esperados e registre as tentativas suspeitas.

# 7 Conclusão

Este trabalho propôs e demonstrou, em ambiente controlado, quatro falhas decorrentes da confiança indevida nas entradas do usuário em uma aplicação Web, além de suas respectivas correções. A atividade evidenciou que as falhas são fáceis de explorar, pois bastam o navegador, o Burp Suite ou o curl, sem conhecimento avançado, e, ao mesmo tempo, graves, pois causam prejuízo financeiro, vazamento de dados pessoais e elevação de privilégio.

As correções, por outro lado, mostraram-se simples, exigindo poucas linhas de código e seguindo todas o mesmo princípio de não confiar no cliente. Conclui-se que a segurança de uma aplicação Web não pode depender da interface: campos ocultos, limites no HTML e telas que escondem opções apenas organizam a experiência de uso, enquanto a proteção efetiva está em validar e autorizar cada requisição no servidor. Como trabalhos futuros, sugere-se ampliar o laboratório com outras classes de vulnerabilidade, como injeção de SQL e Cross-Site Scripting, e aplicar ferramentas de análise automatizada seguindo o OWASP Web Security Testing Guide [14].

%%DECLARACAO_IA%%

%%REFERENCIAS%%
