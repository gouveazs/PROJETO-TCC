<?php
session_start();
$nome = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : null;
$tipo = isset($_SESSION['tipo']) ? $_SESSION['tipo'] : null;
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Comunidades - Entre Linhas</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --marrom: #5a4224;
      --verde: #5a6b50;
      --background: #f4f1ee;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Playfair Display', serif;
    }

    body {
      background-color: var(--background);
    }

    .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      width: 250px;
      height: 100vh;
      background-color: var(--verde);
      padding: 20px;
      color: white;
    }

    .sidebar .logo {
      text-align: center;
      margin-bottom: 30px;
    }

    .sidebar .logo img {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      object-fit: cover;
    }

    .sidebar .logo p {
      margin-top: 10px;
      font-weight: bold;
    }

    .sidebar ul {
      list-style: none;
      padding: 0;
    }

    .sidebar ul li {
      margin: 15px 0;
    }

    .sidebar ul li a {
      text-decoration: none;
      color: white;
    }

    .topbar {
      position: fixed;
      top: 0;
      left: 250px;
      right: 0;
      height: 70px;
      background-color: var(--marrom);
      color: white;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 40px;
      z-index: 1001;
    }

    .topbar h1 {
      font-size: 1.6rem;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .topbar h1 img {
      width: 30px;
      height: 30px;
    }

    .topbar .search-box {
      background-color: var(--background);
      border-radius: 30px;
      display: flex;
      align-items: center;
      padding: 5px 15px;
    }

    .topbar .search-box input {
      border: none;
      outline: none;
      background: transparent;
      font-size: 1rem;
      width: 250px;
      color: #3b3b3b;
    }

    .topbar .search-box img {
      width: 18px;
      height: 18px;
    }

    .categorias-barra {
      margin-left: 250px;
      margin-top: 70px;
      background-color: #9a8c7c;
      padding: 10px 40px;
      display: flex;
      justify-content: space-between;
      flex-wrap: wrap;
    }

    .categorias-barra a {
      color: white;
      text-decoration: none;
      font-size: 1rem;
      padding: 8px 12px;
      border-radius: 6px;
      transition: background 0.3s;
    }

    .categorias-barra a:hover {
      background-color: var(--marrom);
    }

    .content {
      margin-left: 250px;
      margin-top: 130px;
      padding: 30px;
    }

    .content h2 {
      font-size: 2rem;
      color: var(--marrom);
      margin-bottom: 10px;
    }

    .content p {
      font-size: 1.2rem;
      margin-bottom: 30px;
      color: #333;
    }

    .comunidade-box {
      display: flex;
      background-color: var(--marrom);
      border-radius: 10px;
      padding: 20px;
      color: white;
      margin-bottom: 30px;
      align-items: center;
    }

    .comunidade-box img {
      width: 180px;
      height: 130px;
      object-fit: cover;
      border-radius: 10px;
      margin-right: 20px;
    }

    .comunidade-box .descricao h3 {
      font-size: 1.2rem;
      margin-bottom: 10px;
    }

    .comunidade-box .descricao p {
      font-size: 1rem;
      line-height: 1.4;
    }

    .footer {
      margin-left: 250px;
      background-color: var(--marrom);
      color: white;
      text-align: center;
      padding: 15px;
    }

    @media (max-width: 768px) {
      .sidebar {
        display: none;
      }

      .topbar, .categorias-barra, .content, .footer {
        margin-left: 0;
      }
    }
  </style>
</head>
<body>
  <div class="sidebar">
    <div class="logo">
      <img src="imgs/usuario.jpg" alt="Foto de Perfil">
      <p><?= $nome ? htmlspecialchars($nome) : 'Entre ou crie sua conta'; ?></p>
    </div>
    <nav>
      <ul class="menu">
        <li><a href="#"><img src="imgs/inicio.png" alt="Início" style="width:20px; margin-right:10px;"> Início</a></li>
        <li><a href="explorar.php"><img src="imgs/explorar.png" alt="Explorar" style="width:20px; margin-right:10px;"> Explorar</a></li>
        <li><a href="#"><img src="imgs/comunidades.png" alt="Comunidades" style="width:20px; margin-right:10px;"> Comunidades</a></li>
        <li><a href="#"><img src="imgs/favoritos.png" alt="Favoritos" style="width:20px; margin-right:10px;"> Favoritos</a></li>
        <li><a href="#"><img src="imgs/carrinho.png" alt="Carrinho" style="width:20px; margin-right:10px;"> Carrinho</a></li>
      </ul>

      <h3>Conta</h3>
      <ul class="account">
        <?php if (!$nome): ?>
          <li><a href="login/login.php"><img src="imgs/entrarconta.png" alt="Entrar" style="width:20px; margin-right:10px;"> Entrar na conta</a></li>
          <li><a href="php/cadastro/cadastroUsuario.php"><img src="imgs/criarconta.png" alt="Criar Conta" style="width:20px; margin-right:10px;"> Criar conta</a></li>
          <li><a href="php/cadastro/cadastroVendedor.php"><img src="imgs/querovende.png" alt="Quero Vender" style="width:20px; margin-right:10px;"> Quero vender</a></li>
        <?php else: ?>
          <li><a href="php/perfil/ver_perfil.php"><img src="imgs/criarconta.png" alt="Perfil" style="width:20px; margin-right:10px;"> Ver perfil</a></li>
        <?php endif; ?>

        <?php if ($nome === 'adm'): ?>
          <li><a href="php/consulta/consulta.php"><img src="imgs/explorar.png" alt="Consulta" style="width:20px; margin-right:10px;"> Consulta</a></li>
          <li><a href="php/consultaFiltro/busca.php"><img src="imgs/explorar.png" alt="Consulta Nome" style="width:20px; margin-right:10px;"> Consulta por Nome</a></li>
          <li><a href="php/cadastro/cadastroProduto.php"><img src="imgs/querovender.png" alt="Cadastrar Produto" style="width:20px; margin-right:10px;"> Cadastrar Produto</a></li>
        <?php endif; ?>

        <?php if ($tipo === 'Vendedor'): ?>
          <li><a href="php/cadastro/cadastroProduto.php"><img src="imgs/querovender.png" alt="Cadastrar Produto" style="width:20px; margin-right:10px;"> Cadastrar Produto</a></li>
        <?php endif; ?>

        <?php if ($nome): ?>
          <li><a href="login/logout.php"><img src="imgs/logout.png" alt="Sair" style="width:20px; margin-right:10px;"> Sair</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </div>

  <!-- Topo -->
  <div class="topbar">
    <h1><img src="imgs/livros.png" alt="Logo"> Entre Linhas - Chats Online</h1>
    <div class="search-box">
      <input type="text" placeholder="Pesquisar tags, outros...">
      <img src="imgs/lupa.png" alt="Buscar">
    </div>
  </div>

  <!-- Barra de categorias -->
  <div class="categorias-barra">
    <a href="#">Ficção Científica</a>
    <a href="#">Romance</a>
    <a href="#">Aventura</a>
    <a href="#">Fantasia</a>
    <a href="#">Terror</a>
    <a href="#">Mistério</a>
    <a href="#">Drama</a>
    <a href="#">Não Ficção</a>
  </div>

  <!-- Conteúdo -->
  <div class="content">
    <h2>Bem vindo, conheça nossas comunidades!</h2>
    <p>Faça parte de bate-papos sobre seus livros preferidos</p>

    <div class="comunidade-box">
      <img src="imgs/livros-ciencia.jpg" alt="Ficção Científica">
      <div class="descricao">
        <h3>Descrição</h3>
        <p>
          Neste canto digital onde o tempo dobra e as realidades se entrelaçam, mentes inquietas se reúnem para tecer o futuro com palavras. <br><br>
          Entre mensagens que viajam mais rápido que a luz, exploramos juntos os enigmas dos multiversos, os segredos das civilizações estelares e os dilemas éticos das inteligências artificiais.
        </p>
      </div>
    </div>
  </div>

  <div class="footer">
    &copy; 2025 Entre Linhas - Todos os direitos reservados.
  </div>
</body>
</html>
