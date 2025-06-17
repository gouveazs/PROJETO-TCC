<?php
session_start();
$nome = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : null;
$tipo = isset($_SESSION['tipo']) ? $_SESSION['tipo'] : null;
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Entre Linhas - Livraria Moderna</title>
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <!-- Font Awesome -->
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

  <style>
    :root {
      --marrom: #5a4224;
      --verde: #5a6b50;
      --background: #F4F1EE;
    }

    * {
      margin: 0; padding: 0; box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      background-color: var(--background);
    }

    .sidebar {
      position: fixed;
      top: 0; left: 0;
      width: 250px; height: 100vh;
      background-color: var(--verde);
      color: #fff;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding-top: 20px;
    }

    .sidebar .logo {
      margin-bottom: 20px;
      text-align: center;
    }

    .sidebar .logo img {
      width: 80px; height: 80px; border-radius: 50%;
      object-fit: cover;
      margin-bottom: 10px;
    }

    .sidebar .logo p {
      font-weight: bold;
    }

    .sidebar nav ul {
      list-style: none;
      padding: 0;
      width: 100%;
    }

    .sidebar nav ul li {
      width: 100%;
      margin-bottom: 10px;
    }

    .sidebar nav ul li a {
      color: #fff;
      text-decoration: none;
      display: block;
      padding: 10px 20px;
      transition: background 0.3s;
    }

    .sidebar nav ul li a i {
      margin-right: 10px;
    }

    .sidebar nav ul li a:hover {
      background-color: #6f8562;
      border-radius: 8px;
    }

    .sidebar h2 a {
      text-decoration: none;
      color: #ff7675;
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

    .topbar input[type="text"] {
      padding: 10px;
      border: none;
      border-radius: 20px;
      width: 250px;
    }

    .banner {
      position: fixed;
      top: 70px; left: 250px; right: 0;
      height: 200px;
      z-index: 1000;
    }

    .banner img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .main {
      margin-left: 250px;
      padding-top: 290px;
      padding-left: 30px;
      padding-right: 30px;
      min-height: 100vh;
    }

    h2 {
      margin-bottom: 20px;
      color: var(--verde);
    }

    .cards {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
      gap: 20px;
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
      height: 220px;
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

    @media (max-width: 768px) {
      .sidebar {
        width: 200px;
      }
      .topbar, .banner, .main, .footer {
        margin-left: 200px;
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
      <img src="imgs/perfil.png" alt="Foto de Perfil">
      <p><?= $nome ? htmlspecialchars($nome) : 'Entre ou crie sua conta'; ?></p>
    </div>
    <nav>
      <ul>
        <li><a href="#"><i class="fas fa-home"></i> Início</a></li>
        <li><a href="#"><i class="fas fa-compass"></i> Explorar</a></li>
        <li><a href="#"><i class="fas fa-book"></i> Minha Estante</a></li>
        <li><a href="#"><i class="fas fa-heart"></i> Favoritos</a></li>
        <li><a href="#"><i class="fas fa-history"></i> Histórico</a></li>

        <?php if (!$nome): ?>
          <li><a href="login/login.php"><i class="fas fa-sign-in-alt"></i> Entrar na conta</a></li>
          <li><a href="login/loginVendedor.php"><i class="fas fa-store"></i> Acessar painel do Livreiro</a></li>
          <li><a href="php/cadastro/cadastroUsuario.php"><i class="fas fa-user-plus"></i> Criar conta</a></li>
          <li><a href="php/cadastro/cadastroVendedor.php"><i class="fas fa-cogs"></i> Quero vender</a></li>
        <?php else: ?>
          <li><a href="php/perfil/ver_perfil.php"><i class="fas fa-user"></i> Ver perfil</a></li>
        <?php endif; ?>

        <?php if ($nome === 'adm'): ?>
          <li><a href="php/consulta/consulta.php"><i class="fas fa-search"></i> Consulta</a></li>
          <li><a href="php/consultaFiltro/busca.php"><i class="fas fa-filter"></i> Consulta por Nome</a></li>
          <li><a href="php/cadastro/cadastroProduto.php"><i class="fas fa-plus"></i> Cadastrar Produto</a></li>
        <?php endif; ?>

        <?php if ($tipo === 'Vendedor'): ?>
          <li><a href="php/cadastro/cadastroProduto.php"><i class="fas fa-plus"></i> Cadastrar Produto</a></li>
        <?php endif; ?>
      </ul>

      <?php if ($nome): ?>
        <h2><a href="login/logout.php">Sair</a></h2>
      <?php endif; ?>
    </nav>
  </div>

  <div class="topbar">
    <h1>Entre Linhas - Livraria Moderna</h1>
    <input type="text" placeholder="Pesquisar livros, autores...">
  </div>

  <div class="banner">
    <img src="img/Banner.png" alt="Banner da Livraria">
  </div>

  <div class="main">
    <h2>Novidades</h2>
    <div class="cards">
      <div class="card">
        <img src="https://images-na.ssl-images-amazon.com/images/I/51sNWdKoMbL.jpg" alt="Livro 1">
        <div class="info">
          <h3>A Menina que Roubava Livros</h3>
          <div class="stars">★★★★★</div>
        </div>
      </div>
      <div class="card">
        <img src="https://images-na.ssl-images-amazon.com/images/I/41aQPTCmeVL.jpg" alt="Livro 2">
        <div class="info">
          <h3>O Pequeno Príncipe</h3>
          <div class="stars">★★★★★</div>
        </div>
      </div>
      <div class="card">
        <img src="https://images-na.ssl-images-amazon.com/images/I/51hYlyByEGL.jpg" alt="Livro 3">
        <div class="info">
          <h3>Dom Casmurro</h3>
          <div class="stars">★★★★☆</div>
        </div>
      </div>
      <div class="card">
        <img src="https://images-na.ssl-images-amazon.com/images/I/51Mm1LJxeFL.jpg" alt="Livro 4">
        <div class="info">
          <h3>Harry Potter</h3>
          <div class="stars">★★★★★</div>
        </div>
      </div>
    </div>
  </div>

  <div class="footer">
    &copy; 2025 Entre Linhas - Todos os direitos reservados.
  </div>
</body>
</html>
