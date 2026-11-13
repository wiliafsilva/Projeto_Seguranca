# Projeto de Pentest – Segurança em aplicações Web

Trabalho em equipe da disciplina **Segurança da Informação** (Prof. Msc. Felipe Lima), **Universidade Tiradentes (UNIT)** – Aracaju/SE, 2026.

**Tema 4 – Segurança em aplicações Web**  
**Equipe:** Daniel de Oliveira Mendonça Mota · Wilia Francisco da Silva

---

## Sobre o trabalho

O projeto investiga a **confiança indevida nas entradas do usuário** em aplicações Web: tudo o que sai do navegador (campos ocultos, parâmetros da URL, formulários) pode ser alterado antes de chegar ao servidor. Quando o servidor aceita esses dados sem verificar, um atacante consegue mudar preços, ver dados de outras pessoas ou virar administrador.

Para demonstrar isso, a equipe criou a **Loja Lab**: uma loja virtual fictícia, em duas versões (vulnerável e segura), com 4 cenários de ataque:

| Cenário | Falha | Referência |
|---|---|---|
| **C1** | Preço do produto num campo oculto (`hidden`) | CWE-472 |
| **C2** | Validação da quantidade só no navegador | CWE-602, CWE-20 |
| **C3** | Ver o pedido de outro cliente trocando o número na URL (IDOR) | CWE-639 |
| **C4** | Cliente comum vira administrador (atribuição em massa) | CWE-915 |

---

## Como usar

👉 **O passo a passo completo está em [`laboratorio/README.md`](laboratorio/README.md)**: instalação do Docker, como ligar a loja, contas de teste, uso do Burp Suite e do curl, roteiro de cada ataque e solução de problemas.

Resumo para quem já tem o Docker Desktop:

```bash
git clone https://github.com/wiliafsilva/Projeto_Seguranca.git
cd Projeto_Seguranca/laboratorio
docker compose up -d --build
```

Depois abra http://localhost:8000. Contas: `ana / ana123`, `bruno / bruno123`, `admin / admin123`.

---

## Conteúdo do repositório

```
├── README.md                 ← esta página
├── laboratorio/              ← Loja Lab (Docker, PHP, MySQL) e guia passo a passo
└── documentos/
    ├── Etapa 2 - Pesquisa e Planejamento.pdf / .docx / .md
    └── diario.md             ← diário com tudo o que foi feito e o que falta
```

## Etapas

| Etapa | Situação |
|---|---|
| 1 – Formação da equipe e escolha do tema | Concluída |
| 2 – Pesquisa e planejamento | Concluída |
| 3 – Atividade prática (laboratório) | Em andamento |
| 4 – Documento técnico final e apresentação | Entrega em 29/11/2026 |

---

> ⚠️ **Aviso:** projeto acadêmico. A versão vulnerável tem falhas **de propósito** e só deve ser usada no próprio computador. Testar sistemas de terceiros sem autorização é crime (Art. 154-A do Código Penal).
