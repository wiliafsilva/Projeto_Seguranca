<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Loja Lab – Laboratório</title>
  <link rel="icon" href="comum/img/logo.svg">
  <link rel="stylesheet" href="comum/style.css">
</head>
<body>
  <header class="topo">
    <div class="container">
      <span class="logo">
        <img src="comum/img/logo.svg" alt="" width="34" height="34">
        <span>Loja<b>Lab</b></span>
      </span>
    </div>
  </header>
  <main>
    <div class="container">
      <section class="hero">
        <div>
          <span class="selo">TEMA 4 · SEGURANÇA EM APLICAÇÕES WEB</span>
          <h1>Laboratório Loja Lab</h1>
          <p>Loja virtual fictícia, em duas versões, para demonstrar a confiança indevida nas entradas do usuário. Uso exclusivo em ambiente local.</p>
        </div>
        <img src="comum/img/smartphone.svg" alt="">
      </section>

      <div class="grade" style="grid-template-columns:repeat(auto-fit,minmax(280px,1fr))">
        <article class="card">
          <div class="corpo" style="padding:22px">
            <span class="status" style="background:#fdecea;color:#8a1c14;align-self:flex-start">VERSÃO VULNERÁVEL</span>
            <h3 style="font-size:20px;margin-top:8px">Loja com as falhas C1 a C4</h3>
            <p class="parcelas">Preço em campo oculto, validação só no cliente, IDOR e atribuição em massa.</p>
            <a class="btn btn-primario" href="vulneravel/" style="margin-top:8px">Abrir versão vulnerável</a>
          </div>
        </article>
        <article class="card">
          <div class="corpo" style="padding:22px">
            <span class="status" style="align-self:flex-start">VERSÃO SEGURA</span>
            <h3 style="font-size:20px;margin-top:8px">Mesma loja, com as correções</h3>
            <p class="parcelas">Preço vindo do banco, validação no servidor, checagem de dono e lista de campos permitidos.</p>
            <a class="btn btn-primario" href="segura/" style="margin-top:8px">Abrir versão segura</a>
          </div>
        </article>
      </div>

      <div class="contas-teste" style="margin-top:20px">
        <b>Contas de teste:</b> <code>ana / ana123</code> · <code>bruno / bruno123</code> · <code>admin / admin123</code>
      </div>
    </div>
  </main>
  <footer class="rodape">
    <div class="container legal" style="padding-top:18px">Loja Lab · trabalho de Segurança da Informação · Universidade Tiradentes · Daniel Mota e Wilia Silva</div>
  </footer>
</body>
</html>
