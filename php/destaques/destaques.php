<?php
session_start();
$nome = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : null;
$foto_de_perfil = isset($_SESSION['foto_de_perfil']) ? $_SESSION['foto_de_perfil'] : null;

include '../conexao.php';
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
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Destaques - Entre Linhas</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="icon" type="image/png" href="../../imgs/logotipo.png"/>
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
      z-index: 1000;
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

    .search-form {
      display: flex;
      align-items: center;
    }
    
    .topbar input[type="text"] {
      padding: 8px 12px;
      border: none;
      border-radius: 20px 0 0 20px;
      width: 250px;
      font-size: 1rem;
      background-color: var(--background);
    }
    
    .topbar input[type="submit"] {
      padding: 8px 12px;
      background: var(--verde);
      color: white;
      border: none;
      border-radius: 0 20px 20px 0;
      cursor: pointer;
      font-size: 1rem;
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

    /* Ajuste para o título quando tiver logo */
    .topbar h1 {
      display: flex;
      align-items: center;
      gap: 15px;
      font-size: 1.6rem;
    }

    .categorias-barra {
      position: fixed;
      top: 70px;
      left: 250px;
      right: 0;
      background-color: #9a8c7c;
      padding: 10px 40px;
      display: flex;
      justify-content: space-between;
      flex-wrap: wrap;
      z-index: 999;
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

    /* Estilos do carrossel - POSICIONAMENTO CORRIGIDO */
    .carousel-container {
      position: relative;
      width: 100%;
      max-width: 1200px;
      margin: 0 auto 40px;
      overflow: hidden;
      border-radius: 8px;
      box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }

    .carousel {
      display: flex;
      transition: transform 0.5s ease;
    }

    .carousel-item {
      min-width: 100%;
      position: relative;
    }

    .carousel-item img {
      width: 100%;
      height: 350px;
      object-fit: cover;
      display: block;
    }

    .carousel-btn {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      background-color: rgba(0,0,0,0.4);
      color: white;
      border: none;
      padding: 12px;
      cursor: pointer;
      font-size: 1.2rem;
      z-index: 10;
      transition: background-color 0.3s;
    }

    .carousel-btn:hover {
      background-color: rgba(0,0,0,0.7);
    }

    .carousel-btn.prev {
      left: 10px;
      border-radius: 0 4px 4px 0;
    }

    .carousel-btn.next {
      right: 10px;
      border-radius: 4px 0 0 4px;
    }

    .carousel-indicators {
      position: absolute;
      bottom: 10px;
      left: 50%;
      transform: translateX(-50%);
      display: flex;
      gap: 8px;
      z-index: 10;
    }

    .carousel-indicator {
      width: 10px;
      height: 10px;
      border-radius: 50%;
      background-color: rgba(255,255,255,0.5);
      cursor: pointer;
      transition: background-color 0.3s;
    }

    .carousel-indicator.active {
      background-color: white;
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
      
      .topbar {
        padding: 0 15px;
        flex-direction: column;
        height: auto;
        padding: 8px;
      }
      
      .topbar h1 {
        font-size: 1.3rem;
        margin-bottom: 8px;
        gap: 10px;
      }

      .logo-img {
        width: 35px;
        height: 35px;
      }

      .categorias-barra {
        position: static;
        margin-top: 60px;
        padding: 8px 15px;
      }

      .content {
        margin-top: 0;
        padding: 15px;
      }
      
      .topbar input[type="text"] {
        width: calc(100% - 90px);
      }
      
      .carousel-item img {
        height: 200px;
      }
      
      .carousel-btn {
        padding: 8px;
        font-size: 1rem;
      }
    }

    @media (max-width: 576px) {
      .logo-img {
        width: 30px;
        height: 30px;
      }
      
      .topbar h1 {
        font-size: 1.1rem;
        gap: 8px;
      }
    }
  </style>
</head>
<body>
  <div class="sidebar">
    <div class="logo">
      <?php if ($foto_de_perfil): ?>
        <img src="data:image/jpeg;base64,<?= base64_encode($foto_de_perfil) ?>" alt="Foto de perfil">
      <?php else: ?>
        <img src="../../imgs/usuario.jpg" alt="Foto de Perfil">
      <?php endif; ?>
      <div class="user-info">
        <?php if (!$nome): ?>
          <a href="../cadastro/cadastroUsuario.php" style="text-decoration: none; color: white;">
            <p class="nome-usuario">Entre ou crie sua conta</p>
          </a>
        <?php else: ?>
          <p class="nome-usuario"><?= htmlspecialchars($nome) ?></p>
        <?php endif; ?>
      </div>
    </div>
    
    <nav>
      <ul class="menu">
        <li><a href="../../index.php"><img src="../../imgs/inicio.png" alt="Início" style="width:20px; margin-right:10px;"> Início</a></li>
        <li><a href="../comunidades/comunidade.php"><img src="../../imgs/comunidades.png" alt="Comunidades" style="width:20px; margin-right:10px;"> Comunidades</a></li>
        <li><a href="destaques.php"><img src="../../imgs/destaque.png" alt="Destaques" style="width:20px; margin-right:10px;"> Destaques</a></li>
        <li><a href="../favoritos/favoritos.php"><img src="../../imgs/favoritos.png" alt="Favoritos" style="width:20px; margin-right:10px;"> Favoritos</a></li>
        <li><a href="../carrinho/carrinho.php"><img src="../../imgs/carrinho.png" alt="Carrinho" style="width:20px; margin-right:10px;"> Carrinho</a></li>
      </ul>

      <h3>Conta</h3>
      <ul class="account">
        <?php if (!$nome): ?>
          <li><a href="../login/login.php"><img src="../../imgs/entrarconta.png" alt="Entrar" style="width:20px; margin-right:10px;"> Entrar na conta</a></li>
          <li><a href="../cadastro/cadastroUsuario.php"><img src="../../imgs/criarconta.png" alt="Criar Conta" style="width:20px; margin-right:10px;"> Criar conta</a></li>
          <li><a href="../cadastro/cadastroVendedor.php"><img src="../../imgs/querovende.png" alt="Quero Vender" style="width:20px; margin-right:10px;"> Quero vender</a></li>
          <li><a href="../login/loginVendedor.php"><img src="../../imgs/entrarconta.png" alt="Entrar" style="width:20px; margin-right:10px;"> Painel do Livreiro</a></li>
        <?php else: ?>
          <li><a href="../perfil/ver_perfil.php"><img src="../../imgs/criarconta.png" alt="Perfil" style="width:20px; margin-right:10px;"> Ver perfil</a></li>
          <li><a href="../login/logout.php"><img src="../../imgs/sair.png" alt="Sair" style="width:20px; margin-right:10px;"> Sair</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </div>

  <!-- Topo -->
  <div class="topbar">
    <h1>
      <div class="logo-container">
        <img src="../../imgs/logotipo.png" alt="Logo Entre Linhas" class="logo-img">
        Destaques - Entre Linhas
      </div>
    </h1>
    <form class="search-form" action="../consultaFiltro/consultaFiltro.php" method="POST">
      <input type="text" name="nome" placeholder="Pesquisar livros, autores...">
      <input type="submit" value="Buscar">
    </form>
  </div>

  <!-- Barra de categorias -->
  <div class="categorias-barra">
    <a href="#">Terror</a>
    <a href="#">Suspense</a>
    <a href="#">Romance</a>
    <a href="#">Fantasia</a>
    <a href="#">Biográfico</a>
    <a href="#">Ficção Científica</a>
    <a href="#">Infantil</a>
    <a href="#">Ficção literária</a>
  </div>

  <!-- Conteúdo -->
  <div class="content">
    <!-- Carrossel de banners - AGORA COM 5 BANNERS -->
    <div class="carousel-container">
      <div class="carousel">
        <div class="carousel-item">
          <img src="../../imgs/Banner1.png" alt="Promoção de Livros de Fantasia">
        </div>
        <div class="carousel-item">
          <img src="../../imgs/Banner2.png" alt="Novos Lançamentos">
        </div>
        <div class="carousel-item">
          <img src="../../imgs/Banner3.png" alt="Ofertas Especiais">
        </div>
        <div class="carousel-item">
          <img src="https://placehold.co/1200x350/5a6b50/FFFFFF/png?text=Autores+Em+Destaque" alt="Autores em Destaque">
        </div>
        <div class="carousel-item">
          <img src="https://placehold.co/1200x350/8B4513/FFFFFF/png?text=Clássicos+da+Literatura" alt="Clássicos da Literatura">
        </div>
      </div>
      <button class="carousel-btn prev">&#10094;</button>
      <button class="carousel-btn next">&#10095;</button>
      <div class="carousel-indicators">
        <span class="carousel-indicator active"></span>
        <span class="carousel-indicator"></span>
        <span class="carousel-indicator"></span>
        <span class="carousel-indicator"></span>
        <span class="carousel-indicator"></span>
      </div>
    </div>
    
    <h2>Bem-vindo aos Destaques</h2>
    <p>Descubra as obras em destaque, autores renomados e as novidades que separamos especialmente para você.</p>

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
      <?php if (empty($produtos)): ?>
        <p style="text-align: center;">Nenhum produto disponível no momento.</p>
      <?php endif; ?>
    </div>

  </div>

  <div class="footer">
    &copy; 2025 Entre Linhas - Todos os direitos reservados.
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const carousel = document.querySelector('.carousel');
      const items = document.querySelectorAll('.carousel-item');
      const prevBtn = document.querySelector('.carousel-btn.prev');
      const nextBtn = document.querySelector('.carousel-btn.next');
      const indicators = document.querySelectorAll('.carousel-indicator');
      
      let currentIndex = 0;
      const totalItems = items.length;
   

      // Função para atualizar o carrossel
      function updateCarousel() {
        carousel.style.transform = `translateX(-${currentIndex * 100}%)`;
        
        // Atualizar indicadores
        indicators.forEach((indicator, index) => {
          if (index === currentIndex) {
            indicator.classList.add('active');
          } else {
            indicator.classList.remove('active');
          }
        });
      }
      
      // Avançar para o próximo slide
      function nextSlide() {
        currentIndex = (currentIndex + 1) % totalItems;
        updateCarousel();
      }
      
      // Voltar para o slide anterior
      function prevSlide() {
        currentIndex = (currentIndex - 1 + totalItems) % totalItems;
        updateCarousel();
      }
      
      // Event listeners para os botões
      nextBtn.addEventListener('click', nextSlide);
      prevBtn.addEventListener('click', prevSlide);
      
      // Event listeners para os indicadores
      indicators.forEach((indicator, index) => {
        indicator.addEventListener('click', () => {
          currentIndex = index;
          updateCarousel();
        });
      });
      
      // Auto-avanço do carrossel
      let interval = setInterval(nextSlide, 5000);
      
      // Pausar auto-avanço quando o mouse estiver sobre o carrossel
      const carouselContainer = document.querySelector('.carousel-container');
      carouselContainer.addEventListener('mouseenter', () => {
        clearInterval(interval);
      });
      
      carouselContainer.addEventListener('mouseleave', () => {
        interval = setInterval(nextSlide, 5000);
      });
    });
  </script>
</body>
</html>