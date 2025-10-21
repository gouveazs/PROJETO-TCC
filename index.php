<?php
session_start();
$nome = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : null;
$foto_de_perfil = isset($_SESSION['foto_de_perfil']) ? $_SESSION['foto_de_perfil'] : null;

include 'php/conexao.php';
$stmt = $conn->prepare("
    SELECT p.*, i.imagem
    FROM produto p
    LEFT JOIN imagens i 
        ON i.idproduto = p.idproduto
    WHERE p.status = 'Disponivel'  -- filtra apenas produtos disponíveis
      AND i.idimagens = (
          SELECT idimagens
          FROM imagens
          WHERE idproduto = p.idproduto
          ORDER BY idimagens ASC
          LIMIT 1
      )
    LIMIT 6
");
$stmt->execute();
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Entre Linhas - Sebo Moderno</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="icon" type="image/png" href="imgs/logotipo.png"/>
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
      overflow-y: auto;
      scrollbar-width: thin;
      scrollbar-color: #ccc transparent;
      z-index: 1002;
      transition: transform 0.3s ease;
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
      display: flex;
      align-items: center;
      justify-content: space-between;
      background-color: #5a4226; /* marrom */
      padding: 10px 20px;
      position: fixed;
      top: 0;
      left: 250px; /* respeita a sidebar */
      right: 0;
      height: 70px;
      z-index: 1000;
    }
    .topbar h1 {
    color: #ffffff;
    }

    .topbar-left {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .topbar-left .logo {
        height: 50px;
    }

    .topbar-left h1 {
        font-size: 22px;
        color: #ffff;
        margin: 0;
        font-weight: bold;
    }

    .search-form {
      display: flex;
      align-items: center;
    }

    .search-form input[type="text"] {
      padding: 10px 15px;
      border: none;
      border-radius: 30px 0 0 30px; /* arredondado à esquerda */
      outline: none;
      width: 300px; /* campo maior */
      font-size: 0.9rem;
      margin: 0;
    }

    .search-form input[type="submit"] {
      padding: 10px 15px;
      border: none;
      background-color: #6f8562; /* verde escuro */
      color: #fff;
      font-weight: none;
      border-radius: 0 30px 30px 0; /* arredondado à direita */
      cursor: pointer;
      margin: 0;
      width: 90px; /* botão mais estreito */
    }

    .search-form input[type="submit"]:hover {
      background-color: #6f8562;
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

    .topbar h1 {
      font-size: 1.5rem;
      display: flex;
      align-items: center;
      gap: 15px;
    }


    .menu-toggle {
      display: none;
      background: none;
      border: none;
      color: white;
      font-size: 1.5rem;
      cursor: pointer;
      margin-right: 15px;
    }

    .banner {
      position: relative;
      margin-left: 250px;
      margin-top: 70px;
      overflow: hidden;
      transition: margin-left 0.3s ease;
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
      transition: margin-left 0.3s ease;
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
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      gap: 20px;
      align-items: stretch; /* força mesma altura */
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
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      height: 100%; /* obrigatório para stretch funcionar */
      box-sizing: border-box;
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
      display: -webkit-box;
      -webkit-line-clamp: 2; /* no máximo 2 linhas */
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    .card-link {
      text-decoration: none;
      color: inherit;
      display: block;
    }
    .card-link .card {
      cursor: pointer;
      transition: transform 0.2s;
    }
    .card-link .card:hover {
      transform: scale(1.03);
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
      transition: margin-left 0.3s ease;
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

    /* Estilos para a área de promoções */
    .promo-area {
      margin-left: 250px;
      margin-top: 70px;
      padding: 30px;
      background-color: #F4F1EE;
      transition: margin-left 0.3s ease;
    }

    .destaque-principal {
      display: flex;
      gap: 30px;
      flex-wrap: wrap;
      margin-bottom: 30px;
    }

    .destaque-livro {
      flex: 2;
      background: #ffffffff;
      padding: 20px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      min-width: 300px;
    }

    .produtos-laterais {
      flex: 1;
      display: flex;
      flex-direction: column;
      gap: 10px;
      min-width: 250px;
    }

    .produto-lateral {
      display: flex;
      background: #ffffffff;
      padding: 10px;
      border-radius: 8px;
      align-items: center;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .produto-lateral img {
      width: 50px;
      height: 50px;
      object-fit: cover;
      border-radius: 6px;
      margin-right: 10px;
    }

    .servicos {
      display: flex;
      justify-content: space-around;
      align-items: center;
      margin: 30px 0;
      flex-wrap: wrap;
      gap: 20px;
    }

    .servico {
      text-align: center;
      min-width: 180px;
    }

    .servico img {
      height: 40px;
    }

    .cards-promocionais {
      display: flex;
      gap: 30px;
      flex-wrap: wrap;
    }

    .card-promocional {
      flex: 1;
      border-radius: 15px;
      padding: 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      min-width: 300px;
    }

    .card-promocional.comunidades {
      background-color: #5A6B50;
    }

    .card-promocional.promocao {
      background-color: #5a4224;
      color: white;
      flex-direction: column;
      align-items: center;
      justify-content: center;
    }

    .card-promocional img {
      height: 150px;
      border-radius: 8px;
    }

    /* NOVOS ESTILOS PARA A SEÇÃO DE EDITORAS - FORMATO REDONDO */
    .editoras-container {
      margin: 40px 0 30px 0;
    }
    
    .editoras-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
      gap: 20px;
      margin-top: 20px;
    }
    
    .editora-item {
      background-color: #fff;
      border-radius: 50%;
      padding: 0;
      text-align: center;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      transition: transform 0.3s, box-shadow 0.3s;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      aspect-ratio: 1/1;
      min-height: 120px;
      overflow: hidden;
    }
    
    .editora-item:hover {
      transform: translateY(-5px);
      box-shadow: 0 6px 15px rgba(0,0,0,0.15);
      background-color: #f8f8f8;
    }
    
    .editora-logo {
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius: 50%;
    }
    
    .editora-nome {
      display: none;
    }

    /* Media Queries para responsividade */
    @media (max-width: 1400px) {
      .cards-novidades, .cards-recomendacoes {
        grid-template-columns: repeat(5, 1fr);
      }
    }

    @media (max-width: 1200px) {
      .cards-novidades, .cards-recomendacoes {
        grid-template-columns: repeat(4, 1fr);
      }
      
      .topbar h1 {
        font-size: 1.3rem;
      }
      
      .topbar input[type="text"] {
        width: 200px;
      }
    }

    @media (max-width: 1024px) {
      .cards-novidades, .cards-recomendacoes {
        grid-template-columns: repeat(3, 1fr);
      }
      
      .sidebar {
        width: 200px;
      }
      
      .topbar, .banner, .main, .footer, .promo-area {
        margin-left: 200px;
      }
      
      .topbar {
        left: 200px;
        padding: 0 20px;
      }
      
      .editoras-grid {
        grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
      }
      
      .editora-item {
        min-height: 110px;
      }
    }

    @media (max-width: 900px) {
      .cards-novidades, .cards-recomendacoes {
        grid-template-columns: repeat(2, 1fr);
      }
      
      .menu-toggle {
        display: block;
      }
      
      .sidebar {
        transform: translateX(-100%);
      }
      
      .sidebar.active {
        transform: translateX(0);
      }
      
      .topbar, .banner, .main, .footer, .promo-area {
        margin-left: 0;
      }
      
      .topbar {
        left: 0;
      }
      
      .destaque-principal, .cards-promocionais {
        flex-direction: column;
      }
      
      .destaque-livro, .card-promocional {
        max-width: 100%;
      }
      
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
      
      .editoras-grid {
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        gap: 15px;
      }
      
      .editora-item {
        min-height: 100px;
      }
    }

    @media (max-width: 768px) {
      .topbar {
        flex-wrap: wrap;
        height: auto;
        padding: 10px 15px;
      }
      
      .topbar h1 {
        font-size: 1.2rem;
        margin-right: 15px;
      }
      
      .search-form {
        order: 3;
        width: 100%;
        margin-top: 10px;
      }
      
      .topbar input[type="text"] {
        width: calc(100% - 100px);
      }
      
      .servicos {
        justify-content: center;
      }
      
      .servico {
        flex: 1;
        min-width: 140px;
      }
      
      .editoras-grid {
        grid-template-columns: repeat(4, 1fr);
      }
    }

    @media (max-width: 576px) {
      .cards-novidades, .cards-recomendacoes {
        grid-template-columns: 1fr;
      }
      
      .card img {
        height: 250px;
      }
      
      .banner img {
        height: 200px;
      }
      
      .section-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
      }
      
      .produto-lateral {
        flex-direction: column;
        text-align: center;
      }
      
      .produto-lateral img {
        margin-right: 0;
        margin-bottom: 10px;
      }
      
      .destaque-livro {
        flex-direction: column;
        text-align: center;
      }
      
      .destaque-livro img {
        margin-top: 15px;
      }
      
      .editoras-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
      }
      
      .editora-item {
        min-height: 90px;
      }
    }

    @media (max-width: 400px) {
      .topbar h1 {
        font-size: 1rem;
      }
      
      .menu-toggle {
        font-size: 1.2rem;
      }
      
      .editoras-grid {
        grid-template-columns: repeat(2, 1fr);
      }
      
      .editora-item {
        min-height: 80px;
      }
    }
    
    /* NOVOS ESTILOS PARA A LOGO */
    .logo-container {
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .logo-img {
      width: 40px;
      height: 40px;
      object-fit: contain;
    }

    .topbar h1 {
      display: flex;
      align-items: center;
      gap: 15px;
      font-size: 1.8rem;
    }

    @media (max-width: 1024px) {
      .logo-img {
        width: 50px;
        height: 50px;
      }
      
      .topbar h1 {
        font-size: 1.5rem;
      }
    }

    @media (max-width: 768px) {
      .logo-img {
        width: 45px;
        height: 45px;
      }
      
      .topbar h1 {
        font-size: 1.3rem;
        gap: 10px;
      }
      
      .logo-container {
        gap: 10px;
      }
    }

    @media (max-width: 576px) {
      .logo-img {
        width: 40px;
        height: 40px;
      }
      
      .topbar h1 {
        font-size: 1.1rem;
        gap: 8px;
      }
      
      .logo-container {
        gap: 8px;
      }
    }

    @media (max-width: 400px) {
      .logo-img {
        width: 35px;
        height: 35px;
      }
      
      .topbar h1 {
        font-size: 1rem;
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
        <li><a href="php/login/logout.php"><img src="imgs/sair.png" alt="Sair" style="width:20px; margin-right:10px;"> Sair</a></li>
      <?php endif; ?>
    </ul>
  </nav>
</div>

 <div class="topbar">
    <button class="menu-toggle">☰</button>
    <h1>
      <div class="logo-container">
        <img src="imgs/logotipo.png" alt="Logo Entre Linhas" class="logo-img">
        Entre Linhas
      </div>
    </h1>
    <form class="search-form" action="php/consultaFiltro/consultaFiltro.php" method="POST">
      <input type="text" name="nome" placeholder="Pesquisar livros, autores...">
      <input type="submit" value="Buscar">
    </form>
  </div>

<div class="promo-area">
  <div class="destaque-principal">
    <div class="destaque-livro">
      <div>
        <p style="color: #5a6b50; font-weight: bold; font-size: 1rem;">Livro de destaque</p>
        <h2 style="font-size: 2rem; font-weight: bold; margin: 10px 0; color: #5a4224;">Amor & azeitonas</h2>
        <p style="margin-bottom: 20px; color: #555;">Jenna Evans Welch</p>
        <a href="php/produto/pagiproduto.php" style="background-color: #5a4224; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: bold; display: inline-block;">Compre agora</a>
      </div>
      <img src="imgs/Amor e Azeitonas.jpg" alt="Livro Amor & Azeitonas" style="max-height: 180px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.2);">
    </div>
    <div class="produtos-laterais">
      <div class="produto-lateral">
        <img src="imgs/divinosrivais.jpg" alt="Produto">
        <div>
          <p style="font-weight: bold; font-size: 0.9rem;">Divinos Rivais</p>
          <p style="color: #5a6b50;">R$ 86,00 <span style="color: #999; text-decoration: line-through; font-size: 0.8rem;">R$ 95,00</span></p>
        </div>
      </div>
      <div class="produto-lateral">
        <img src="imgs/emrotadecolisao.jpg" alt="Produto">
        <div>
          <p style="font-weight: bold; font-size: 0.9rem;">Em Rota De Colisão</p>
          <p style="color: #5a6b50;">R$ 80,00 <span style="color: #999; text-decoration: line-through; font-size: 0.8rem;">R$ 86,00</span></p>
        </div>
      </div>
      <div class="produto-lateral">
        <img src="imgs/bibliotecadameianoite.jpg" alt="Produto">
        <div>
          <p style="font-weight: bold; font-size: 0.9rem;">A Biblioteca da Meia-Noite</p>
          <p style="color: #5a6b50;">R$ 98,00 <span style="color: #999; text-decoration: line-through; font-size: 0.8rem;">R$ 120,00</span></p>
        </div>
      </div>
    </div>
  </div>

  <div class="servicos">
    <div class="servico">
      <img src="imgs/entrega-rapida.png" alt="Frete">
      <p>Frete grátis para pedidos <br><small>Acima de R$ 50</small></p>
    </div>
    <div class="servico">
      <img src="imgs/garantia-de-devolucao-de-dinheiro.png" alt="Garantia">
      <p>Garantia de devolução do dinheiro<br><small>100% do seu dinheiro de volta</small></p>
    </div>
    <div class="servico">
      <img src="imgs/pagamento-com-cartao-de-credito.png" alt="Pagamento">
      <p>Pagamento<br><small>Pix, Crédito e Débito</small></p>
    </div>
    <div class="servico">
      <img src="imgs/suporte-online.png" alt="Suporte">
      <p>Ajuda e Suporte<br><small>(11) 4002-8922</small></p>
    </div>
  </div>

  <div class="cards-promocionais">
    <div class="card-promocional comunidades">
      <div>
        <h3 style="color: #ffffffff; font-size: 1.4rem;">Comunidades</h3>
        <p style="color: #ffffffff; font-size: 0.9rem;"> Visite nossas comunidades e participe dos chats online!</p>
        <a href="php/comunidades/comunidade.php" style="margin-top: 10px; display: inline-block; background-color: #5a4224; color: white; padding: 8px 16px; border-radius: 6px; text-decoration: none;">Clique aqui</a>
      </div>
      <img src="imgs/comuni.jpg" alt="Card">
    </div>
    <div class="card-promocional promocao">
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
     <div class="stars">★★★★★</div>
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
    <a href="php/produto/pagiproduto.php?id=<?=$produto['idproduto']?>&nome=<?=$produto['nome']?>&preco=<?=$produto['preco']?>" class="card-link">
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

<!-- NOVA SEÇÃO: Navegue por Editora - FORMATO REDONDO -->
<div class="editoras-container">
  <div class="section-header">
    <h2>Editoras</h2>
    <button id="toggle-editoras" style="background: #5a6b50; color: #fff; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-weight: bold;">
      Ver todas
    </button>
  </div>
  
  <div class="editoras-grid" id="editoras-grid">
    <div class="editora-item"><img src="imgs/edlogos/1.png" alt="Rocco" class="editora-logo"></div>
    <div class="editora-item"><img src="imgs/edlogos/2.png" alt="Intrínseca" class="editora-logo"></div>
    <div class="editora-item"><img src="imgs/edlogos/3.png" alt="Grupo Editorial Record" class="editora-logo"></div>
    <div class="editora-item"><img src="imgs/edlogos/4.png" alt="Verus Editora" class="editora-logo"></div>
    <div class="editora-item"><img src="imgs/edlogos/5.png" alt="Companhia das Letras" class="editora-logo"></div>
    <div class="editora-item"><img src="imgs/edlogos/6.png" alt="Arqueiro" class="editora-logo"></div>
    <div class="editora-item"><img src="imgs/edlogos/7.png" alt="Alt" class="editora-logo"></div>
    <div class="editora-item"><img src="imgs/edlogos/8.png" alt="Planeta Minotauro" class="editora-logo"></div>
    
    <!-- OS QUE VÃO FICAR ESCONDIDOS -->
    <div class="editora-item hidden"><img src="imgs/edlogos/9.png" alt="Boitempo Editorial" class="editora-logo"></div>
    <div class="editora-item hidden"><img src="imgs/edlogos/10.png" alt="Galera" class="editora-logo"></div>
    <div class="editora-item hidden"><img src="imgs/edlogos/11.png" alt="Gutenberg" class="editora-logo"></div>
    <div class="editora-item hidden"><img src="imgs/edlogos/12.png" alt="Darkside" class="editora-logo"></div>
    <div class="editora-item hidden"><img src="imgs/edlogos/13.png" alt="Panda Books" class="editora-logo"></div>
    <div class="editora-item hidden"><img src="imgs/edlogos/14.png" alt="L&PM" class="editora-logo"></div>
    <div class="editora-item hidden"><img src="imgs/edlogos/15.png" alt="Alfaguara" class="editora-logo"></div>
    <div class="editora-item hidden"><img src="imgs/edlogos/16.png" alt="Estação Liberdade" class="editora-logo"></div>
    <div class="editora-item hidden"><img src="imgs/edlogos/17.png" alt="Editora 34" class="editora-logo"></div>
    <div class="editora-item hidden"><img src="imgs/edlogos/18.png" alt="Principis" class="editora-logo"></div>
    <div class="editora-item hidden"><img src="imgs/edlogos/19.png" alt="Harper Collins" class="editora-logo"></div>
    <div class="editora-item hidden"><img src="imgs/edlogos/20.png" alt="Companhia das Letrinhas" class="editora-logo"></div>
    <div class="editora-item hidden"> <img src="imgs/edlogos/21.png" alt="Casa Lygia Bojunga" class="editora-logo"> </div> 
    <div class="editora-item hidden"> <img src="imgs/edlogos/22.png" alt="Brinquebook" class="editora-logo"> </div> 
    <div class="editora-item hidden"> <img src="imgs/edlogos/23.png" alt="Todolivro" class="editora-logo"> </div> 
    <div class="editora-item hidden"> <img src="imgs/edlogos/24.png" alt="Editora Melhoramentos" class="editora-logo"> </div> 
    <div class="editora-item hidden"> <img src="imgs/edlogos/25.png" alt="Leya" class="editora-logo"> </div> 
    <div class="editora-item hidden"> <img src="imgs/edlogos/26.png" alt="Aleph" class="editora-logo"> </div> 
    <div class="editora-item hidden"> <img src="imgs/edlogos/27.png" alt="Edipro Grupo Editorial" class="editora-logo"> </div> 
    <div class="editora-item hidden"> <img src="imgs/edlogos/28.png" alt="Bestseller" class="editora-logo"> </div> 
    <div class="editora-item hidden"> <img src="imgs/edlogos/29.png" alt="Globolivros" class="editora-logo"> </div>
  </div>
</div>

<style>
  /* Esconde os itens extras */
  .editora-item.hidden {
    display: none;
  }
</style>

<script>
  document.getElementById("toggle-editoras").addEventListener("click", function() {
    const hiddenItems = document.querySelectorAll(".editora-item.hidden");
    const isHidden = hiddenItems[0].style.display === "" || hiddenItems[0].style.display === "none";

    hiddenItems.forEach(item => {
      item.style.display = isHidden ? "flex" : "none";
    });

    this.textContent = isHidden ? "Ver menos" : "Ver todas";
  });
</script>


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

  <!-- VLibras - Widget de Libras -->
<div vw class="enabled">
    <div vw-access-button class="active"></div>
    <div vw-plugin-wrapper>
        <div class="vw-plugin-top-wrapper"></div>
    </div>
</div>
  <script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
  <script>
      new window.VLibras.Widget('https://vlibras.gov.br/app');
  </script>

  <script>
    // Menu toggle para dispositivos móveis
    document.querySelector('.menu-toggle').addEventListener('click', function() {
      document.querySelector('.sidebar').classList.toggle('active');
    });
    
    // Fechar menu ao clicar em um link (em dispositivos móveis)
    document.querySelectorAll('.sidebar a').forEach(link => {
      link.addEventListener('click', function() {
        if (window.innerWidth <= 900) {
          document.querySelector('.sidebar').classList.remove('active');
        }
      });
    });
  </script>
</body>
</html>