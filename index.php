<?php
session_start();
$nome = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : null;
$foto_de_perfil = isset($_SESSION['foto_de_perfil']) ? $_SESSION['foto_de_perfil'] : null;

//produtos
include 'php/conexao.php';
$stmt = $conn->prepare("
    SELECT p.*, i.imagem
    FROM produto p
    LEFT JOIN imagens i 
        ON i.idproduto = p.idproduto
    WHERE i.idimagens = (
        SELECT idimagens
        FROM imagens
        WHERE idproduto = p.idproduto
        ORDER BY idimagens ASC
        LIMIT 1
    )
");
$stmt->execute();
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Entre Linhas - Livraria Moderna</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  <style>
    :root {
      --marrom: #5a4224;
      --verde: #5a6b50;
      --background: #F4F1EE;
    }

    * {
      margin: 0; 
      padding: 0; 
      box-sizing: border-box;
      font-family: 'Playfair Display', serif;
    }

    body {
      background-color: var(--background);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    .sidebar {
      position: fixed;
      top: 0; left: 0;
      width: 250px;
      height: 100vh;
      background-color: var(--verde);
      color: #fff;
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      padding-top: 20px;
      overflow-y: auto; /* SCROLL HABILITADO */
      scrollbar-width: thin;
      scrollbar-color: #ccc transparent;
    }

    .sidebar::-webkit-scrollbar {
      width: 6px;
    }

    .sidebar::-webkit-scrollbar-thumb {
      background-color: #ccc;
      border-radius: 4px;
    }

    .sidebar::-webkit-scrollbar-track {
      background: transparent;
    }

    .sidebar .logo {
      display: flex;
      align-items: center;
      justify-content: flex-start;
      width: 100%;
      padding: 0 20px;
      margin-bottom: 20px;
    }

    .sidebar .logo img {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      object-fit: cover;
      margin-right: 15px;
    }

    .sidebar .user-info {
      display: flex;
      flex-direction: column;
      line-height: 1.2;
    }

    .sidebar .user-info .nome-usuario {
      font-weight: bold;
      font-size: 0.95rem; 
      color: #fff;
    }

    .sidebar .user-info .tipo-usuario {
      font-size: 0.8rem;
      color: #ddd;
    }

    .sidebar .logo p {
      font-weight: bold;
    }

    .sidebar nav {
      width: 100%;
      padding: 0 20px;
    }

    .sidebar nav h3 {
      margin-top: 20px;
      margin-bottom: 10px;
      font-size: 1rem;
      color: #ddd;
    }

    .sidebar nav ul {
      list-style: none;
      padding: 0;
      margin: 0 0 10px 0;
      width: 100%;
    }

    .sidebar nav ul li {
      width: 100%;
      margin-bottom: 10px;
    }

    .sidebar nav ul li a {
      color: #fff;
      text-decoration: none;
      display: flex;
      align-items: center;
      padding: 10px;
      border-radius: 8px;
      transition: background 0.3s;
    }

    .sidebar nav ul li a i {
      margin-right: 10px;
    }

    .sidebar nav ul li a:hover {
      background-color: #6f8562;
    }

    .topbar {
      position: fixed;
      top: 0; left: 250px; right: 0;
      height: 70px;
      background-color: var(--marrom);
      color: #fff;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 30px;
      z-index: 1001;
    }

    .topbar h1 {
      font-size: 1.5rem;
    }

    .search-form {
      display: flex;
      align-items: center;
    }
    
    .topbar input[type="text"] {
      padding: 10px 15px;
      border: none;
      border-radius: 20px 0 0 20px;
      width: 250px;
      font-size: 0.9rem;
    }
    
    .topbar input[type="submit"] {
      padding: 10px 15px;
      background: var(--verde);
      color: white;
      border: none;
      border-radius: 0 20px 20px 0;
      cursor: pointer;
    }

    .banner {
      position: relative;
      margin-left: 250px;
      margin-top: 70px;
      overflow: hidden;
    }

    .banner img {
      width: 100%;
      height: 300px;
      object-fit: cover;
      display: block;
      margin-bottom: 20px;
    }

    .main {
      flex: 1;
      margin-left: 250px;
      padding: 30px;
      margin-top: 20px;
    }

    .section-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }

    .section-header h2 {
      color: var(--verde);
    }

    .section-header .ver-mais {
      color: var(--marrom);
      text-decoration: none;
      font-weight: 600;
      transition: color 0.3s;
    }

    .section-header .ver-mais:hover {
      color: #000;
      text-decoration: underline;
    }

    .cards {
      display: grid;
      gap: 20px;
    }

    .cards-novidades {
      grid-template-columns: repeat(6, 1fr);
    }

    .cards-recomendacoes {
      grid-template-columns: repeat(6, 1fr);
    }

    .card {
      background-color: #fff;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      transition: transform 0.3s;
    }

    .card:hover {
      transform: translateY(-5px);
    }

    .card img {
      width: 100%;
      height: 300px;
      object-fit: cover;
    }

    .card .info {
      padding: 15px;
      text-align: center;
    }

    .card .info h3 {
      margin-bottom: 10px;
      font-size: 1rem;
      color: var(--verde);
    }

    .card-link {
      text-decoration: none;
      color: inherit;
      display: block; /* faz o link ocupar todo o card */
    }
    .card-link .card {
      cursor: pointer; /* indica que é clicável */
      transition: transform 0.2s;
    }
    .card-link .card:hover {
      transform: scale(1.03); /* efeito visual ao passar o mouse */
    }


    .card .info .stars {
      color: #f5c518;
    }

    .footer {
      margin-left: 250px;
      background-color: var(--marrom);
      color: #fff;
      text-align: center;
      padding: 15px;
    }

    /* Estilos para a seção Mais Destaques */
    .mais-destaque-container {
      margin: 20px 0 40px 0;
    }

    .mais-destaque-card {
      display: flex;
      background-color: #fff;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .mais-destaque-imagem {
      flex: 1;
      max-width: 40%;
    }

    .mais-destaque-imagem img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .mais-destaque-conteudo {
      flex: 2;
      padding: 30px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .mais-destaque-conteudo h3 {
      font-size: 1.8rem;
      color: var(--marrom);
      margin-bottom: 15px;
    }

    .mais-destaque-conteudo p {
      color: #555;
      line-height: 1.6;
      margin-bottom: 20px;
    }

    .mais-destaque-preco {
      margin-bottom: 20px;
    }

    .preco-atual {
      font-size: 1.4rem;
      color: var(--verde);
      font-weight: bold;
      margin-right: 15px;
    }

    .preco-antigo {
      font-size: 1.1rem;
      color: #999;
      text-decoration: line-through;
    }

    .btn-destaque {
      display: inline-block;
      background-color: var(--verde);
      color: white;
      padding: 12px 25px;
      border-radius: 6px;
      text-decoration: none;
      font-weight: bold;
      text-align: center;
      transition: background-color 0.3s;
      max-width: 250px;
    }

    .btn-destaque:hover {
      background-color: #4a5a40;
    }

    @media (max-width: 1200px) {
      .cards-novidades, .cards-recomendacoes {
        grid-template-columns: repeat(4, 1fr);
      }
    }

    @media (max-width: 768px) {
      .sidebar {
        width: 200px;
      }
      .topbar, .banner, .main, .footer {
        margin-left: 200px;
      }
      .cards-novidades, .cards-recomendacoes {
        grid-template-columns: repeat(2, 1fr);
      }
      
      /* Responsividade para Mais Destaques */
      .mais-destaque-card {
        flex-direction: column;
      }
      
      .mais-destaque-imagem {
        max-width: 100%;
        height: 200px;
      }
      
      .mais-destaque-conteudo {
        padding: 20px;
      }
      
      .mais-destaque-conteudo h3 {
        font-size: 1.5rem;
      }
    }

    @media (max-width: 576px) {
      .sidebar {
        display: none;
      }
      .topbar, .banner, .main, .footer {
        margin-left: 0;
      }
    }
  </style>
</head>
<body>
<div class="sidebar">
  <div class="logo">
      <?php if ($foto_de_perfil): ?>
      <img src="data:image/jpeg;base64,<?= base64_encode($foto_de_perfil) ?>">
      <?php else: ?>
        <img src="imgs/usuario.jpg" alt="Foto de Perfil">
      <?php endif; ?>
      <div class="user-info">
        <?php if (!$nome): ?>
          <a href="php/cadastro/cadastroUsuario.php" style="text-decoration: none; color: white;">
            <p class="nome-usuario">Entre ou crie sua conta</p>
          </a>
        <?php else: ?>
          <p class="nome-usuario"><?= htmlspecialchars($nome) ?></p>
        <?php endif; ?>
      </div>
  </div>

  <nav>
    <ul class="menu">
      <li><a href="index.php"><img src="imgs/inicio.png" alt="Início" style="width:20px; margin-right:10px;"> Início</a></li>
      <li><a href="php/comunidades/comunidade.php"><img src="imgs/comunidades.png" alt="Comunidades" style="width:20px; margin-right:10px;"> Comunidades</a></li>
      <li><a href="php/destaques/destaques.php"><img src="imgs/destaque.png" alt="Destaques" style="width:20px; margin-right:10px;"> Destaques</a></li>
      <li><a href="php/favoritos/favoritos.php"><img src="imgs/favoritos.png" alt="Favoritos" style="width:20px; margin-right:10px;"> Favoritos</a></li>
      <li><a href="php/carrinho/carrinho.php"><img src="imgs/carrinho.png" alt="Carrinho" style="width:20px; margin-right:10px;"> Carrinho</a></li>
    </ul>

    <h3>Conta</h3>
    <ul class="account">
      <?php if (!$nome): ?>
        <li><a href="php/login/login.php"><img src="imgs/entrarconta.png" alt="Entrar" style="width:20px; margin-right:10px;"> Entrar na conta</a></li>
        <li><a href="php/cadastro/cadastroUsuario.php"><img src="imgs/criarconta.png" alt="Criar Conta" style="width:20px; margin-right:10px;"> Criar conta</a></li>
        <li><a href="php/cadastro/cadastroVendedor.php"><img src="imgs/querovende.png" alt="Quero Vender" style="width:20px; margin-right:10px;"> Quero vender</a></li>
        <li><a href="php/login/loginVendedor.php"><img src="imgs/entrarconta.png" alt="Entrar" style="width:20px; margin-right:10px;"> Painel do Livreiro</a></li>
      <?php else: ?>
        <li><a href="php/perfil-usuario/ver_perfil.php"><img src="imgs/criarconta.png" alt="Perfil" style="width:20px; margin-right:10px;"> Ver perfil</a></li>
      <?php endif; ?>

      <?php if ($nome === 'adm'): ?>
        <li><a href="php/consulta/consulta.php"><img src="imgs/explorar.png" alt="Consulta" style="width:20px; margin-right:10px;"> Consulta</a></li>
        <li><a href="php/consultaFiltro/busca.php"><img src="imgs/explorar.png" alt="Consulta Nome" style="width:20px; margin-right:10px;"> Consulta por Nome</a></li>
        <li><a href="php/cadastro/cadastroProduto.php"><img src="imgs/explorar.png" alt="Cadastrar Produto" style="width:20px; margin-right:10px;"> Cadastrar Produto</a></li>
      <?php endif; ?>

      <?php if ($nome): ?>
        <li><a href="php/login/logout.php"><img src="imgs/sair.png" alt="Sair" style="width:20px; margin-right:10px;"> Sair</a></li>
      <?php endif; ?>
    </ul>
  </nav>

</div>

<div class="topbar">
  <h1>Entre Linhas - Livraria Moderna</h1>
  <form class="search-form" action="php/consultaFiltro/consultaFiltro.php" method="POST">
    <input type="text" name="nome" placeholder="Pesquisar livros, autores...">
    <input type="submit" value="Buscar">
  </form>
</div>

<div style="margin-left: 250px; margin-top: 70px; padding: 30px; background-color: #F4F1EE;">
  <div style="display: flex; gap: 30px; flex-wrap: wrap;">
    <div style="flex: 2; background: #ffffffff; padding: 20px; border-radius: 12px; display: flex; align-items: center; justify-content: space-between;">
      <div>
        <p style="color: #5a6b50; font-weight: bold; font-size: 1rem;">Livro de destaque</p>
        <h2 style="font-size: 2rem; font-weight: bold; margin: 10px 0; color: #5a4224;">Amor & azeitonas</h2>
        <p style="margin-bottom: 20px; color: #555;">Jenna Evans Welch</p>
        <a href="php/produto/pagiproduto.php" style="background-color: #5a4224; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: bold; display: inline-block;">Compre agora</a>
      </div>
      <img src="imgs/Amor e Azeitonas.jpg" alt="Livro Amor & Azeitonas" style="max-height: 180px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.2);">
    </div>
    <div style="flex: 1; display: flex; flex-direction: column; gap: 10px;">
      <div style="display: flex; background: #ffffffff; padding: 10px; border-radius: 8px; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <img src="imgs/divinosrivais.jpg" alt="Produto" style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px; margin-right: 10px;">
        <div>
          <p style="font-weight: bold; font-size: 0.9rem;">Divinos Rivais</p>
          <p style="color: #5a6b50;">R$ 86,00 <span style="color: #999; text-decoration: line-through; font-size: 0.8rem;">R$ 95,00</span></p>
        </div>
      </div>
      <div style="display: flex; background: #ffffffff; padding: 10px; border-radius: 8px; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <img src="imgs/emrotadecolisao.jpg" alt="Produto" style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px; margin-right: 10px;">
        <div>
          <p style="font-weight: bold; font-size: 0.9rem;">Em Rota De Colisão</p>
          <p style="color: #5a6b50;">R$ 80,00 <span style="color: #999; text-decoration: line-through; font-size: 0.8rem;">R$ 86,00</span></p>
        </div>
      </div>
      <div style="display: flex; background: #ffffffff; padding: 10px; border-radius: 8px; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <img src="imgs/bibliotecadameianoite.jpg" alt="Produto" style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px; margin-right: 10px;">
        <div>
          <p style="font-weight: bold; font-size: 0.9rem;">A Biblioteca da Meia-Noite</p>
          <p style="color: #5a6b50;">R$ 98,00 <span style="color: #999; text-decoration: line-through; font-size: 0.8rem;">R$ 120,00</span></p>
        </div>
      </div>
    </div>
  </div>

  <div style="display: flex; justify-content: space-around; align-items: center; margin: 30px 0; flex-wrap: wrap;">
    <div style="text-align: center;">
      <img src="imgs/entrega-rapida.png" alt="Frete" style="height: 40px;">
      <p>Frete grátis para pedidos <br><small>Acima de R$ 50</small></p>
    </div>
    <div style="text-align: center;">
      <img src="imgs/garantia-de-devolucao-de-dinheiro.png" alt="Garantia" style="height: 40px;">
      <p>Garantia de devolução do dinheiro<br><small>100% do seu dinheiro de volta</small></p>
    </div>
    <div style="text-align: center;">
      <img src="imgs/pagamento-com-cartao-de-credito.png" alt="Pagamento" style="height: 40px;">
      <p>Pagamento<br><small>Pix, Crédito e Débito</small></p>
    </div>
    <div style="text-align: center;">
      <img src="imgs/suporte-online.png" alt="Suporte" style="height: 40px;">
      <p>Ajuda e Suporte<br><small>(11) 4002-8922</small></p>
    </div>
  </div>

  <div style="display: flex; gap: 30px; flex-wrap: wrap;">
    <div style="flex: 1; background-color: #5A6B50; border-radius: 15px; padding: 20px; display: flex; align-items: center; justify-content: space-between;">
      <div>
        <h3 style="color: #ffffffff; font-size: 1.4rem;">Comunidades</h3>
        <p style="color: #ffffffff; font-size: 0.9rem;"> Visite nossas comunidades e participe dos chats online!</p>
        <a href="#" style="margin-top: 10px; display: inline-block; background-color: #5a4224; color: white; padding: 8px 16px; border-radius: 6px; text-decoration: none;">Clique aqui</a>
      </div>
      <img src="imgs/comuni.jpg" alt="Card" style="height: 150px; border-radius: 8px;">
    </div>
    <div style="flex: 1; background-color: #5a4224; color: white; border-radius: 12px; padding: 20px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
      <h2 style="font-size: 2rem;">PROMOÇÃO</h2>
      <p style="font-size: 1.2rem;">ATÉ <strong>40% OFF</strong></p>
    </div>
  </div>
</div>

<div class="main">

<!-- Novidades -->
<div class="section-header">
  <h2>Novidades</h2>
  <a href="#" class="ver-mais">Ver mais</a>
</div>

<div class="cards cards-novidades">
  <div class="card">
    <img src="imgs/Daisy Jone & The Six.jpg" alt="Livro 1">
    <div class="info">
      <h3>Daisy Jone & The Six</h3>
      <p class="price">R$ 44,90</p>
      <div class="stars">★★★★☆</div>
    </div>
  </div>

  <div class="card">
    <img src="imgs/A Menina que Roubava Livros.jpg" alt="Livro 2">
    <div class="info">
      <h3>A Menina que Roubava Livros</h3>
      <p class="price">R$ 39,50</p>
      <div class="stars">★★★★★</div>
    </div>
  </div>

  <div class="card">
    <img src="imgs/Extraordinário.jpg" alt="Livro 3">
    <div class="info">
      <h3>Extraordinário</h3>
      <p class="price">R$ 28,00</p>
      <div class="stars">★★★★★</div>
    </div>
  </div>

  <div class="card">
    <img src="imgs/Relatos de um Gato Viajante.jpg" alt="Livro 4">
    <div class="info">
      <h3>Relatos de um Gato Viajante</h3>
      <p class="price">R$ 33,90</p>
      <div class="stars">★★★☆☆</div>
    </div>
  </div>

  <div class="card">
    <img src="imgs/Prisioneiro de Azkaban.jpg" alt="Livro 5">
    <div class="info">
      <h3>Prisioneiro de Azkaban</h3>
      <p class="price">R$ 55,00</p>
      <div class="stars">★★★★★</div>
    </div>
  </div>

  <div class="card">
    <img src="imgs/Dom Casmurro.jpg" alt="Livro 6">
    <div class="info">
      <h3>Dom Casmurro</h3>
      <p class="price">R$ 22,00</p>
      <div class="stars">★★★★☆</div>
    </div>
  </div>
</div>

<!-- Recomendações -->
<div class="section-header" style="margin-top: 50px;">
  <h2>Recomendações</h2>
  <a href="#" class="ver-mais">Ver mais</a>
</div>

<div class="cards cards-recomendacoes">
  <div class="card">
    <img src="imgs/Orgulho e Preconceito.jpg" alt="Livro 7">
    <div class="info">
      <h3>Orgulho e Preconceito</h3>
      <p class="price">R$ 34,90</p>
      <div class="stars">★★★★★</div>
    </div>
  </div>

  <div class="card">
    <img src="imgs/1984.jpg" alt="Livro 8">
    <div class="info">
      <h3>1984</h3>
      <p class="price">R$ 29,99</p>
      <div class="stars">★★★★★</div>
    </div>
  </div>

  <div class="card">
    <img src="imgs/O Pequeno Príncipe.jpg" alt="Livro 9">
    <div class="info">
      <h3>O Pequeno Príncipe</h3>
      <p class="price">R$ 19,90</p>
      <div class="stars">★★★★★</div>
    </div>
  </div>

  <div class="card">
    <img src="imgs/Romeu e Julieta.jpg" alt="Livro 10">
    <div class="info">
      <h3>Romeu e Julieta</h3>
      <p class="price">R$ 24,00</p>
      <div class="stars">★★★☆☆</div>
    </div>
  </div>

  <div class="card">
    <img src="imgs/Senhor dos Anéis.jpg" alt="Livro 11">
    <div class="info">
      <h3>Senhor dos Anéis</h3>
      <p class="price">R$ 59,90</p>
      <div class="stars">★★★★★</div>
    </div>
  </div>

  <div class="card">
    <img src="imgs/A Culpa é das Estrelas.jpg" alt="Livro 12">
    <div class="info">
      <h3>A Culpa é das Estrelas</h3>
      <p class="price">R$ 35,00</p>
      <div class="stars">★★★★☆</div>
    </div>
  </div>

  <div class="card">
    <img src="imgs/O Hobbit.jpg" alt="Livro 13">
    <div class="info">
      <h3>O Hobbit</h3>
      <p class="price">R$ 39,90</p>
      <div class="stars">★★★★★</div>
    </div>
  </div>

  <div class="card">
    <img src="imgs/Cem Anos de Solidão.jpg" alt="Livro 14">
    <div class="info">
      <h3>Cem Anos de Solidão</h3>
      <p class="price">R$ 42,00</p>
      <div class="stars">★★★★★</div>
    </div>
  </div>

  <div class="card">
    <img src="imgs/A Revolução dos Bichos.jpg" alt="Livro 15">
    <div class="info">
      <h3>A Revolução dos Bichos</h3>
      <p class="price">R$ 27,50</p>
      <div class="stars">★★★★☆</div>
    </div>
  </div>

  <div class="card">
    <img src="imgs/O Conto da Aia.jpg" alt="Livro 16">
    <div class="info">
      <h3>O Conto da Aia</h3>
      <p class="price">R$ 36,00</p>
      <div class="stars">★★★★☆</div>
    </div>
  </div>

  <div class="card">
    <img src="imgs/Amor e Azeitonas.jpg" alt="Livro 17">
    <div class="info">
      <h3>Amor e Azeitonas</h3>
      <p class="price">R$ 22,90</p>
      <div class="stars">★★★☆☆</div>
    </div>
  </div>

  <div class="card">
    <img src="imgs/As vantagens de ser invisivel.jpg" alt="Livro 18">
    <div class="info">
      <h3>As Vantagens de Ser Invisível</h3>
      <p class="price">R$ 38,00</p>
      <div class="stars">★★★★★</div>
    </div>
  </div>
</div>
  
<br><br>

<!-- produtos do banco -->
<div class="section-header">
    <h2>Ofertas Recem Adicionadas</h2>
    <a href="#" class="ver-mais">Ver mais</a>
</div>

<div class="cards cards-novidades">
  <?php foreach ($produtos as $produto): ?>
    <a href="php/produto/pagiproduto.php?id=<?= $produto['idproduto'] ?>" class="card-link">
      <div class="card">
        <?php if (!empty($produto['imagem'])): ?>
          <img src="data:image/jpeg;base64,<?= base64_encode($produto['imagem']) ?>" alt="<?= htmlspecialchars($produto['nome']) ?>">
        <?php else: ?>
          <img src="imgs/usuario.jpg" alt="Foto de Perfil">
        <?php endif; ?>
        <div class="info">
          <h3><?= htmlspecialchars($produto['nome']) ?></h3>
          <p class="price">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></p>
          <div class="stars">★★★★★</div>
        </div>
      </div>
    </a>
  <?php endforeach; ?>
</div>

<!-- + Mais Destaques -->
<div class="section-header" style="margin-top: 50px;">
  <h2>+ Mais Destaques</h2>
  <a href="#" class="ver-mais">Ver mais</a>
</div>

<div class="mais-destaque-container">
  <div class="mais-destaque-card">
    <div class="mais-destaque-imagem">
      <img src="imgs/colecao-especial.jpg" alt="Coleção Especial de Verão">
    </div>
    <div class="mais-destaque-conteudo">
      <h3>Coleção Especial de Verão</h3>
      <p>Descubra nossa seleção exclusiva de livros para aproveitar a estação mais quente do ano. 
        Romances leves, aventuras emocionantes e histórias que vão te transportar para praias paradisíacas.</p>
      <div class="mais-destaque-preco">
        <span class="preco-atual">A partir de R$ 29,90</span>
        <span class="preco-antigo">R$ 49,90</span>
      </div>
      <a href="#" class="btn-destaque">Ver Coleção Completa</a>
    </div>
  </div>
</div>

</div>  

  <div class="footer">
    &copy; 2025 Entre Linhas - Todos os direitos reservados.
  </div>
</body>
</html>