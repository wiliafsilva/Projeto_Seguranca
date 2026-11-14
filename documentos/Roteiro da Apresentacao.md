# Roteiro da Apresentação

**Tema 4 – Segurança em aplicações Web · Loja Lab**
Daniel de Oliveira Mendonça Mota · Wilia Francisco da Silva

Tempo sugerido: **12 a 15 minutos** + perguntas. Os dois apresentam. A demonstração é feita ao vivo no laboratório; se algo falhar, use os prints da pasta `evidencias` como plano B.

## Antes de começar

- [ ] Docker Desktop aberto e laboratório no ar (`docker compose up -d` na pasta `laboratorio`).
- [ ] Laboratório zerado (`docker compose down -v` e `up -d`), para os números baterem.
- [ ] Burp Suite aberto, com o navegador do Burp já em `localhost:8000`.
- [ ] Abas prontas: a loja, o slide, e um terminal na pasta `laboratorio`.
- [ ] Testar uma vez no computador e na rede que serão usados no dia.

## Divisão das falas

| Slides | Quem | Conteúdo |
|---|---|---|
| 1–2 Capa e roteiro | Wilia | Abertura, equipe, tema e visão geral |
| 3–4 Problema e ética | Daniel | Fronteira de confiança; ética e legalidade |
| 5–6 Loja Lab e as 4 falhas | Wilia | As duas versões; tabela das falhas (CWE/OWASP) |
| 7 C1 (demo) | Daniel | Preço em campo oculto |
| 8 C2 (demo) | Wilia | Quantidade negativa |
| 9 C3 (demo) | Daniel | Pedido de outro cliente (IDOR) |
| 10 C4 (demo) | Wilia | Cliente vira administrador |
| 11–12 Resultados e proteção | Daniel | Síntese e medidas de correção |
| 13–14 Conclusão e perguntas | Wilia + Daniel | Princípio central; abrir para perguntas |

## Roteiro de cada demonstração

Em todas: mostrar o ataque na versão **vulnerável** (faixa vermelha) e depois o bloqueio na **segura** (faixa verde). Logar como `ana` / `ana123`.

- **C1 – preço:** abrir o Notebook, mostrar o campo oculto com Ctrl+U, interceptar a compra no Burp, trocar `preco=2499.90` por `preco=0.01`, mostrar o pedido de R$ 0,01. Na segura, o mesmo ataque cobra R$ 2.499,90.
- **C2 – quantidade:** no Mouse, tentar `-10` (o navegador bloqueia), depois enviar `quantidade=-10` pelo Burp; mostrar o total R$ -899,00 e o estoque indo de 50 para 60. Na segura, HTTP 400.
- **C3 – IDOR:** abrir o próprio pedido (`id=1`), trocar na URL para `id=2`, mostrar o CPF do Bruno. Na segura, "Pedido não encontrado".
- **C4 – admin:** mostrar "Acesso negado" ao painel; em "Meus dados", interceptar e acrescentar `&perfil=admin` (sem deixar linha em branco no Burp); mostrar a Ana no painel com os CPFs. Na segura, o campo é ignorado.
- **Fechar:** mostrar os logs da versão segura (`docker compose logs web | Select-String loja-segura`).

## Perguntas prováveis e respostas

- **Por que validar no cliente, se não é seguro?** Pela usabilidade: dá retorno imediato ao usuário. A segurança é responsabilidade do servidor; as duas coisas coexistem.
- **O que é IDOR?** Insecure Direct Object Reference: acessar um objeto de outra pessoa trocando o identificador (o número do pedido na URL), quando o servidor não confere o dono.
- **HTTPS resolveria?** Não. O HTTPS protege os dados no caminho, mas quem altera a requisição é o próprio usuário, antes da criptografia.
- **Prepared statements não bastam?** Eles protegem contra injeção de SQL, que é outra falha. Não protegem contra erros de lógica de negócio ou de autorização, como os quatro cenários.
- **Isso acontece em sites reais?** Sim; são falhas do OWASP Top 10. Por isso testamos só em ambiente próprio e autorizado.
- **Como vocês garantiram que a loja não tinha outras falhas?** As duas versões usam senha com hash, consultas parametrizadas e escape de saída; as únicas diferenças são as quatro falhas estudadas.

## Encerramento

Frase final: **"O servidor nunca deve confiar no cliente."** Valores sensíveis vêm do servidor, toda entrada é validada no servidor e todo acesso é autorizado no servidor. O código e o laboratório estão no GitHub do projeto.
